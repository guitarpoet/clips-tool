<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ClipsDataSourceTest extends Clips_TestCase {
	public function doSetup() {
		$this->tool = get_clips_tool();
	}

	public function testDataSource() {
		$this->assertNotNull($this->tool->datasource);
	}

	public function doTearDown() {
	}
}
