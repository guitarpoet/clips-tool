<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\SimpleTreeNode;

class SimpleTreeNodeIteratorTest extends Clips\TestCase {

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testIterateWidth() {
		$node = new SimpleTreeNode($this->value);
		$iter = $node->iterator();
		$i = 0;
		foreach($iter as $k => $v) {
			echo $k." = [".$v->label."]\n";
		}
	}

	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testIterateDepth() {
		$node = new SimpleTreeNode($this->value);
		$iter = $node->iterator('depth');
		$i = 0;
		foreach($iter as $k => $v) {
			echo $k." = [".$v->label."]\n";
		}
	}
}
