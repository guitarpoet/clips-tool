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

	public function testExists() {
		$this->assertTrue($this->repo->exists('clips'));
		$this->assertTrue($this->repo->exists('src/Clips/Tool.php'));
		$this->assertFalse($this->repo->exists('This should not exists'));
	}

	public function testCreate() {
		$path = clips_path('/../../tests/_tmp/git_sample');
		mkdir($path, 0755, true);
		$r = $this->repo->get($path, false);
		$r->create();
		$r->save('a.txt', "This is only a test.");
		$r->commit('Hello world', 'Jack <guitarpoet@gmail.com>');
		$this->assertEquals($r->show('a.txt'), 'This is only a test.');
		$this->assertEquals(count($r->revisions()), 1);
		$r->remove();
	}
}
