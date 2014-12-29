<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ClipsDataSourceTest extends Clips_TestCase {
	public function doSetup() {
		$this->tool = get_clips_tool();
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
		$this->assertNotNull($this->tool->datasource);
		$this->assertNotNull($this->tool->datasource->get('mysql'));
	}

	public function doTearDown() {
	}
}
