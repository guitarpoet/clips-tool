<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class ToolTest extends Clips\TestCase {
	public function doSetUp() {
		$this->tool = &Clips\get_clips_tool();
	}

	public function testSetContext() {
		$this->tool->context("test", 1);
		$this->assertEquals($this->tool->context("test"), 1);
		$this->tool->context(array("test" => 2, "name" => "Jack"));
		$this->assertEquals($this->tool->context("test"), 2);
		$this->assertEquals($this->tool->context("name"), "Jack");
	}
	
	public function testAppendContext() {
		$this->tool->context("test", 1);
		$this->assertEquals($this->tool->context("test"), 1);
		$this->tool->context("test", 2, true);
		$this->assertEquals($this->tool->context("test"), array(1, 2));
	}

	public function testGetTool() {
		$this->assertNotNull($this->tool);
	}

	public function testLogger() {
		$logger = $this->tool->getLogger();
		$this->assertNotNull($logger);
		$logger->info("Hello {world}", array("world" => "Jack"));
	}

	public function doTearDown() {
	}
}
