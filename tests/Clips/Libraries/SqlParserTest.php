<?php  in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\TestCase;

/**
 * @Clips\Library("sqlParser")
 */
class SqlParserTest extends TestCase {
	public function testParseSql() {
		$sql = 'select count(*) % 10 + count(groups.name as n) as count from users';
		$this->assertNotNull($this->sqlparser->parse($sql));
	}
}
