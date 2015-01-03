<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ClipsSqlTest extends Clips_TestCase {

	public function doSetUp() {
		$tool = get_clips_tool();
		$this->sql = $tool->library('sql');
	}

	public function testGenerateSql() {
		$this->assertNotNull($this->sql);
		$this->sql->select('name as n', 'shit as t')
			->from('hello', 'users as u', 'class c')
			->join('groups as g', array('u.group' => 'g.id', 'shit' => '1'), 'left')
			->join('class as c', array('u.class' => 'c.id', 'shit' => '1'), 'left')
			->orderBy('u.name desc', 'g.name desc')
			->groupBy('u.name', 'g.name')
			->where(['name' => 'jack'])->limit();
		$result = $this->sql->sql();
		$this->assertNotNull($result[1]);
		$result = $this->sql->select('name as n', 'shit as t')
			->from('hello', 'users as u', 'class c')->sql();
		$this->assertEquals(count($result), 1);
	}

	public function doTearDown() {
	}
}
