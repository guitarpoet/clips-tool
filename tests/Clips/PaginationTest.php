<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class PaginationTest extends Clips\TestCase {
	/**
	 * @Clips\Object("Clips\Libraries\Sql")
	 * @Clips\TestValue(file="datatable.json")
	 */
	public function testPaginationFromJson() {
		$p = Clips\Pagination::fromJson($this->value);
   	}
}
