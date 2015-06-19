<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\TestCase;

/**
 * @author Jack
 * @version 1.1
 * @date Fri Jun 19 13:16:16 2015
 *
 * @Clips\Library("dataSource")
 */
class ClipsDataSourceTest extends TestCase {
	public function doSetUp() {
		$this->ds = $this->datasource->get('clips');
	}

	public function testPrepare() {
		$this->assertNotNull($this->ds);
		$p = $this->ds->prepare('select (select count(*) from users where users.username is not null) as count, * from users as u join groups as g on u.gid = g.id where u.username = ?', 'jack');
		$this->assertEquals(count($p), 2);
		$this->assertEquals($p[0], 'SELECT (SELECT count(*) FROM test_users WHERE test_users.username is not null) AS count, * FROM test_users AS u INNER JOIN test_groups AS g ON u.gid = g.id WHERE u.username = ?');
	}
}
