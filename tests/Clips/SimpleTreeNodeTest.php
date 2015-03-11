<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

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

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testTreeOperations() {
		$this->assertTrue(isset($this->value));
		$node = new Clips\SimpleTreeNode($this->value);

		$i = 1;
		$c = $node->childAt($i);
		print_r($c);
		$this->assertEquals($c->label, 'branch 2');
		$this->assertEquals($node->hasChild($c), $i);

		$c = $c->prevSibling();
		$this->assertNotNull($c);
		$this->assertEquals($c->label, 'branch 1');

		$c = $c->nextSibling();
		$this->assertNotNull($c);
		$this->assertEquals($c->label, 'branch 2');
	}
}
