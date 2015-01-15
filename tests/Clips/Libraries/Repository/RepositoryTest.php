<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class RepositoryTest extends Clips\TestCase {
	public function doSetUp() {
		$this->tool = &get_clips_tool();
		$this->repo = $this->tool->library("Repository");
	}

	public function testRepo() {
		$this->assertNotNull($this->repo);
		$this->assertNotNull($this->repo->git);
		$this->assertNotNull($this->repo->lastCommitterDate());
	}
}
