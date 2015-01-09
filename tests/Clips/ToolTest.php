<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ToolTest extends Clips\TestCase {
	public function doSetUp() {
		$this->tool = &get_clips_tool();
	}

	public function testGetTool() {
		$this->assertNotNull($this->tool);
	}

	public function doTearDown() {
	}
}
