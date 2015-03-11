<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class TestCase extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        $mute = (getenv('MUTE_PHPUNIT'));
        $ref = new \ReflectionClass($this);
        $func = $this->getName();
        if(!$mute && $func != 'testStub')
            echo "\n----------".$ref->name." | ".$func."----------\n";
		$this->tool = &get_clips_tool();
		$this->tool->helper('fake');
		$this->clips = new Engine();

        $re = new \Addendum\ReflectionAnnotatedClass($this);

		foreach(array($re,$re->getMethod($func)) as $m) {
			foreach($m->getAnnotations() as $a) {
				switch(get_class($a)) {
				case "Clips\\TestData":
					$this->data = $this->tool->enhance($a);
					break;
				case "Clips\\TestValue":
					if(isset($a->json))
						$a->file = $a->json;

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
							$this->value = \file_get_contents($p);
							if(isset($a->json)) {
								$this->value = parse_json($this->value);
							}
						}
					}
					else if(isset($a->context)) {
						$this->value = clips_context($a->context);
					}
					else if(isset($a->value)) {
						$this->value = $a->value;
					}
					break;
				default:
					$this->tool->annotationEnhance($a, $this);
				}
			}
		}

		$this->doSetUp();
    }

	protected function processObject($a) {
		if($a) {
			if(isset($a->value)) {
				if(!\is_array($a->value))
					$a->value = array($a->value);
				foreach($a->value as $c) {
					$h = strtolower($this->tool->getHandleName($c));
					$this->$h = $this->tool->load_class($c, true);
				}
			}
		}
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
