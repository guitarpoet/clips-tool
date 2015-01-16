<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class HelperTest extends Clips\TestCase {
	public function doSetUp() {
	}

	public function testPathJoin() {
		$this->assertEquals(path_join(1,2,3,4), '1/2/3/4');
		$this->assertEquals(path_join("a/", "/b"), 'a/b');
		$this->assertEquals(path_join("/a/", "/b/"), '/a/b/');
		$this->assertEquals(path_join("a", "b"), 'a/b');
	}
}
