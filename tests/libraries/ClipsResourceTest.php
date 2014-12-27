<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ClipsResourceTest extends Clips_TestCase {
	public function doSetUp() {
		$this->tool = get_clips_tool();
	}

	public function testFileResource() {
		$this->assertTrue(class_exists('Clips_Resource'));
		$r = new Clips_Resource('file://'.__FILE__);
		$this->assertNotNull($r);
		$this->assertNotNull($r->handler);
		$this->assertNotNull($r->contents());
	}

	public function testTplResource() {
		$r = new Clips_Resource('tpl://usage');
		$this->assertNotNull($r);
		$this->assertNotNull($r->handler);
		$this->assertNotNull($r->contents());
	}

	public function doTearDown() {
	}
}
