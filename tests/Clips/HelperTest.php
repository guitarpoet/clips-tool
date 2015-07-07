<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class HelperTest extends Clips\TestCase {
	public function doSetUp() {
	}

	public function testPathJoin() {
		$this->assertEquals(Clips\path_join(1,2,3,4), '1/2/3/4');
		$this->assertEquals(Clips\path_join("a/", "/b"), 'a/b');
		$this->assertEquals(Clips\path_join("/a/", "/b/"), '/a/b/');
		$this->assertEquals(Clips\path_join("a", "b"), 'a/b');
	}

	public function testHandleBar() {
		$i = Clips\str_template('{{php "rand" 1 10}}');
		$this->assertTrue($i <= 10 && $i >= 1);
	}
}
