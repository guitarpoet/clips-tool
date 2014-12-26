<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

require_once(dirname(__FILE__).'/../clips_tool.php');

class Clips_TestCase extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $mute = (getenv('MUTE_PHPUNIT'));
        $ref = new ReflectionClass($this);
        $func = $this->getName();
        if(!$mute && $func != 'testStub')
            echo "\n----------".$ref->name." | ".$func."----------\n";
		$this->doSetUp();
    }

	public function doSetUp() {
	}

	public function doTearDown() {
	}

	public function tearDown() {
		$this->doTearDown();
        $ref = new ReflectionClass($this);
        $func = $this->getName();
        $mute = (getenv('MUTE_PHPUNIT'));
        if(!$mute && $func != 'testStub')
            echo "\n==========".$ref->name." | ".$func."==========\n";
        if (ob_get_length() == 0 ) {
            ob_start();
		}
    }
}
