<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * @Clips\Library("dataSource")
 */
class DataSourceTest extends Clips\TestCase {
	public function testFetch() {
		$mysql = $this->tool->datasource->get('mysql');
		$result = $mysql->fetch('user', 'root');
		$this->assertTrue(count($result) > 1);
	}

	public function testDataSourceIterate() {
		$mysql = $this->tool->datasource->get('mysql');
		$this->assertTrue(count($mysql->query('select * from user')) > 0);
		$mysql->iterate('select * from user where user = ?', function($obj, $context) {
			$this->assertEquals($obj->User, 'root');
			$this->assertEquals($context, array('hello' => 'world'));
		}, array('root'), array('hello' => 'world'));
	}

	public function testDataSource() {
		$this->assertnotnull($this->tool->datasource);
		$this->assertnotnull($this->tool->datasource->get('mysql'));
	}

	public function doTearDown() {
	}
}
