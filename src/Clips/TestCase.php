<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class TestCase extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        $mute = (getenv('MUTE_PHPUNIT'));
        $ref = new \ReflectionClass($this);
        $func = $this->getName();
        if(!$mute && $func != 'testStub')
            echo "\n----------".$ref->name." | ".$func."----------\n";
		$this->tool = &Tool::get_instance();
		$this->clips = new Engine();

		// Check for test data
		$a = get_annotation($this, "Clips\\TestData", $func); // Check for method first
		if(!$a)
			$a = get_annotation($this, "Clips\\TestData"); // Check for class if there is no annotation on method

		if($a) {
			$this->data = $this->tool->enhance($a);
		}

		$this->doSetUp();
    }

	public function doSetUp() {
	}

	public function doTearDown() {
	}

	public function tearDown() {
		$this->doTearDown();
        $ref = new \ReflectionClass($this);
        $func = $this->getName();
        $mute = (getenv('MUTE_PHPUNIT'));
        if(!$mute && $func != 'testStub')
            echo "\n==========".$ref->name." | ".$func."==========\n";
        if (ob_get_length() == 0 ) {
            ob_start();
		}
    }
}
