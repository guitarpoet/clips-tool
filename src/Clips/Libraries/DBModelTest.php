<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class DBModelTest extends Clips\TestCase {
	public function doSetUp() {
		$this->tool = get_clips_tool();
		$this->demo = $this->tool->library('DBModel');
	}

	public function testDemoModel() {
		$this->assertNotNull($this->demo);
		$this->assertTrue(count($this->demo->select('user', 'host')->from('user')->result()) > 0);
	}
}
