<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\TestCase;

/**
 * @Clips\Model
 * @Clips\FakeModel("logFakeDataSourceHandler")
 */
class FakeModelTest extends TestCase {
	public function doSetUp() {
	}

	public function testPathJoin() {
		$this->assertEquals(Clips\path_join(1,2,3,4), '1/2/3/4');
		$this->assertEquals(Clips\path_join("a/", "/b"), 'a/b');
		$this->assertEquals(Clips\path_join("/a/", "/b/"), '/a/b/');
		$this->assertEquals(Clips\path_join("a", "b"), 'a/b');
	}
}
