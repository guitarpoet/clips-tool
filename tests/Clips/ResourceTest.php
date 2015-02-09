<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ResourceTest extends Clips\TestCase {
	public function doSetUp() {
		$this->tool = &Clips\get_clips_tool();
	}

	public function testFileResource() {
		$this->assertTrue(class_exists('Clips\\Resource'));
		$r = new Clips\Resource('file://'.__FILE__);
		$this->assertNotNull($r);
		$this->assertNotNull($r->handler);
		$this->assertNotNull($r->contents());
	}

	public function testTplResource() {
		$r = new Clips\Resource('tpl://usage');
		$this->assertNotNull($r);
		$this->assertNotNull($r->handler);
		$this->assertNotNull($r->contents());
	}

	public function doTearDown() {
	}
}
