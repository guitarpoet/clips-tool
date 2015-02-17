<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class TestValueTest extends Clips\TestCase {

	public function __construct() {
		Clips\context("value", 123);
	}

	/**
	 * @Clips\TestValue(file="sample.json")
	 */
	public function testFileValue() {
		$this->assertNotNull($this->data);
	}

	/**
	 * @Clips\TestValue(123)
	 */
	public function testValue() {
		$this->assertEquals($this->data, 123);
	}

	/**
	 * @Clips\TestValue(context="value")
	 */
	public function testContextValue() {
		$this->assertEquals($this->data, 123);
	}
}
