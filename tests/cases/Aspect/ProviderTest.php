<?php
namespace Aspect;
use Aspect;

class FSProviderTest extends \Aspect\TestCase {
    /**
     * @var FSProvider
     */
    public $provider;

    public function setUp() {
        parent::setUp();
        $this->tpl("template1.tpl", 'Template 1 {$a}');
        $this->tpl("template2.tpl", 'Template 2 {$a}');
        $this->provider = new FSProvider(ASPECT_RESOURCES.'/template');
    }

    public function testIsTemplateExists() {
        $this->assertTrue($this->provider->isTemplateExists("template1.tpl"));
        $this->assertFalse($this->provider->isTemplateExists("unexists.tpl"));
    }

    public function testGetSource() {
        $src = $this->provider->getSource("template1.tpl", $time);
        clearstatcache();
        $this->assertEquals(file_get_contents(ASPECT_RESOURCES.'/template/template1.tpl'), $src);
        $this->assertEquals(filemtime(ASPECT_RESOURCES.'/template/template1.tpl'), $time);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetSourceInvalid() {
        $this->provider->getSource("unexists.tpl", $time);
    }

    public function testGetLastModified() {
        $time = $this->provider->getLastModified("template1.tpl");
        clearstatcache();
        $this->assertEquals(filemtime(ASPECT_RESOURCES.'/template/template1.tpl'), $time);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetLastModifiedInvalid() {
        $this->provider->getLastModified("unexists.tpl");
    }

    public function testGetLastModifiedBatch() {
        $times = $this->provider->getLastModifiedBatch($tpls = array("template1.tpl", "template2.tpl"));
        $this->assertSame($tpls, array_keys($times));
        clearstatcache();
        foreach($times as $template => $time) {
            $this->assertEquals(filemtime(ASPECT_RESOURCES."/template/$template"), $time);
        }
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetLastModifiedBatchInvalid() {
        $this->provider->getLastModifiedBatch(array("template1.tpl", "unexists.tpl", "parent.tpl"));

    }
}
