<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class PaginationTest extends Clips\TestCase {
	/**
	 * @Clips\Object("Clips\Libraries\Sql")
	 * @Clips\TestValue(file="datatable.json")
	 */
	public function testPaginationFromJson() {
		$p = Clips\Pagination::fromJson($this->value);
		$this->assertNotNull($this->sql);
		print_r($this->sql->count($p));
		print_r($this->sql->pagination($p));
   	}
}
