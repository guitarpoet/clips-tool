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
		else {
			// Honor TestData, if there is no test data, then try for test value
			$a = get_annotation($this, "Clips\\TestValue", $func); // Check for method first
			if(!$a)
				$a = get_annotation($this, "Clips\\TestValue"); // Check for class if there is no annotation on method

			if($a) {
				if(isset($a->file)) {
					$test_config_dir = clips_config('test_data_dir');
					if(!$test_config_dir) {
						$test_config_dir = clips_config('test_dir');
						$test_config_dir = path_join($test_config_dir[0], 'data');
					}
					else {
						$test_config_dir = $test_config_dir[0];
					}
					$p = path_join($test_config_dir, $a->file);
					if(\file_exists($p)) {
						$this->data = \file_get_contents($p);
					}
				}
				else if(isset($a->context)) {
					$this->data = clips_context($a->context);
				}
				else if(isset($a->value)) {
					$this->data = $a->value;
				}
			}
		}

		$this->doSetUp();
    }

	public function doSetUp() {
	}

	public function doTearDown() {
	}

	public function tearDown() {
		if(isset($this->data)) { // If we are using test data, auto clean it.
			if(method_exists($this->data, 'clean')) {
				$this->data->clean();
			}
		}
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
