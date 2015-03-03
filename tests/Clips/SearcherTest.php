<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * @Clips\Object("searcher")
 */
class SearcherTest extends Clips\TestCase {
	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimpleQuery() {
		$children = $this->value->children;
		$result = $this->searcher->search('* [id > ? id < ?] , * [id = ?]', $children, array(1, 3, 1));
		print_r($result);
		$this->assertEquals(count($result), 2);
		foreach($result as $r) {
			$this->assertTrue($r->id < 3);
		}
	}

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimpleQuery2() {
		$children = $this->value->children;
		$result = $this->searcher->search('* [id > ? id < ?] , * [id = ?]', $children, array(1, 3, 2));
		print_r($result);
		$this->assertEquals(count($result), 1);
		foreach($result as $r) {
			$this->assertEquals($r->id, 2);
		}
	}

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimpleQueryWithPattern() {
		$children = $this->value->children;
		$result = $this->searcher->search('* [label like ?]', $children, array('branch%'));
		print_r($result);
		$this->assertEquals(count($result), 3);
	}

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimpleQueryWithPattern2() {
		$children = $this->value->children;
		$result = $this->searcher->search('* [label not like ?]', $children, array('branch%'));
		print_r($result);
		$this->assertEquals(count($result), 0);
	}

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimpleQueryWithRegex() {
		$children = $this->value->children;
		$result = $this->searcher->search('* [label ~= ?]', $children, array('branch.*'));
		print_r($result);
		$this->assertEquals(count($result), 3);
	}

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimepleTreeSearch() {
		$node = new Clips\SimpleTreeNode($this->value);
		$result = $this->searcher->treeSearch('* > *', $node);
		var_dump($result);
	}

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimplePropertySearch() {
		$result = $this->searcher->propertySearch('label', $this->value);
		$this->assertEquals($result, 'root');

		$result = $this->searcher->propertySearch('children[0].label', $this->value);
		$this->assertEquals($result, 'branch 1');

		$result = $this->searcher->propertySearch('children[0].*', $this->value);
		$this->assertEquals($result, array(1, 'branch 1', array((object) array(
			'label' => 'Leaf 1.1'), (object) array('label' => 'Leaf 1.2'))));

		$result = $this->searcher->propertySearch('children[*]', $this->value);
		$this->assertEquals($result, $this->value->children);

		$result = $this->searcher->propertySearch('children[*].id', $this->value);
		$this->assertEquals($result, array(1,2,3));
	}
}
