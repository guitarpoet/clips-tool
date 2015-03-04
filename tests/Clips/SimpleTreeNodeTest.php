<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class SimpleTreeNodeTest extends Clips\TestCase {

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimpleQuery() {
		$this->assertTrue(isset($this->value));
		$node = new Clips\SimpleTreeNode($this->value);
		$result = $node->query('* > *');
		$this->assertEquals(count($result), 6);
		$result = $node->query('* [id = ?] > *', array(1));
		$this->assertEquals(count($result), 2);
	}
}
