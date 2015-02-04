<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class FormatterTest extends Clips\TestCase {
	public function doSetUp() {
		$this->tool = &get_clips_tool();
	}

	public function testDumpFormatter() {
		$f = Clips\Formatter::get("dump");
		$this->assertNotNull($f);
		$this->assertEquals($f->format(1), "int(1)");
	}

	public function doTearDown() {
	}
}
