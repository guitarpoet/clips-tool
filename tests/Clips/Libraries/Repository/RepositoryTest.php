<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * @Clips\Library("repository")
 */
class RepositoryTest extends Clips\TestCase {
	public function doSetUp() {
		$this->repo = $this->repository;
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
		$path = Clips\clips_path('/../../tests/_tmp/git_sample');
		mkdir($path, 0755, true);
		$r = $this->repo->repo($path, false);
		$r->create();
		$r->save('a.txt', "This is only a test.");
		$this->assertFalse($r->has('a.txt'));
		$this->assertEquals($r->show('a.txt'), 'This is only a test.');
		$r->commit('Hello world', 'Jack <guitarpoet@gmail.com>');
		$this->assertTrue($r->has('a.txt'));
		$this->assertEquals($r->show('a.txt'), 'This is only a test.');
		$r->save('a.txt', "This is another test.");
		$r->reset();
		$this->assertEquals($r->show('a.txt'), 'This is only a test.');
		$this->assertEquals(count($r->revisions()), 1);
		$r->remove();
	}
}
