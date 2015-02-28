<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class SimpleTreeNodeTest extends Clips\TestCase {

	/**
	 * @Clips\TestValue(file="tree.json")
	 */
	public function testSimpleTreeNode() {
		$json = Clips\parse_json($this->value);
		var_dump(new Clips\SimpleTreeNode($json));
	}
}
