<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ClipsToolTest extends Clips_TestCase {

    public function doSetUp() {
		$this->tool = get_clips_tool();
    }

	public function doTearDown() {
    }

	public function testTemplate() {
		$this->assertEquals("Hello Jack", $this->tool->template("string://Hello {{name}}", array('name' => 'Jack')));
	}

	public function testLoadPHP() {
		$command = $this->tool->command('version');
		$this->assertNotNull($command);
		$command->execute(array());
	}
}
