<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class GitTest extends Clips\TestCase {
	public function doSetUp() {
		$this->tool = &Clips\get_clips_tool();
		$this->git = $this->tool->library('git');
	}

	public function testGetInfo() {
		$this->assertNotNull($this->git);
		$repo = ($this->git->repo(Clips\clips_path('/../../')));
		$head = $repo->getHead();
		$rev = ($repo->getRevision($head->getFullname()));
		echo $rev->getCommit()->getCommitterDate()->getTimestamp();
	}

	public function doTearDown() {
	}
}
