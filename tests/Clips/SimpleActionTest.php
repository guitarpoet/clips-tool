<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\SimpleAction as Action;

class SimpleActionTest extends Clips\TestCase {

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimpleAction() {
		$action = new Action($this->value);
		$this->assertEquals($action->label(), 'root');
	}
}
