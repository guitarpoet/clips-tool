<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * @Clips\Object("searcher")
 */
class SearcherTest extends Clips\TestCase {
	/**
	 * @Clips\TestValue(json="tree.json")
	 */
	public function testSimpleQuery() {
	}
}
