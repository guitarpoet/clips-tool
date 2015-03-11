<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class TestValueTest extends Clips\TestCase {

	public function __construct() {
		Clips\context("value", 123);
	}

	/**
	 * @Clips\TestValue(file="sample.json")
	 */
	public function testFileValue() {
		$this->assertNotNull($this->value);
	}

	/**
	 * @Clips\TestValue(123)
	 */
	public function testValue() {
		$this->assertEquals($this->value, 123);
	}

	/**
	 * @Clips\TestValue(context="value")
	 */
	public function testContextValue() {
		$this->assertEquals($this->value, 123);
	}
}
