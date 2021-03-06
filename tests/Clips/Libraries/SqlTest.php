<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class SqlTest extends Clips\TestCase {
	public function doSetUp() {
		$tool = Clips\get_clips_tool();
		$this->sql = new Clips\Libraries\Sql();
	}

	public function testGenerateSqlWithNullArg() {
		$sql = $this->sql->select('name as n', 'shit as t')
			->from('poi')->where(array('name' => ''))->sql();
		var_dump($sql);
		$sql = $this->sql->select('name as n', 'shit as t')
			->from('poi')->where(array('name' => null))->sql();
		var_dump($sql);
	}

	public function testGenerateSql() {
		$this->assertNotNull($this->sql);
		$this->sql->select('name as n', 'shit as t')
			->from('hello', 'users as u', 'class c')
			->join('groups as g', array('u.group' => 'g.id', 'shit' => '1'), 'left')
			->join('class as c', array('u.class' => 'c.id', 'shit' => '1'), 'left')
			->orderBy('u.name desc', 'g.name desc')
			->groupBy('u.name', 'g.name')
			->where(array('name' => 'jack'))->limit();
		$result = $this->sql->sql();
		$this->assertNotNull($result[1]);
		$result = $this->sql->select('name as n', 'shit as t')
			->from('hello', 'users as u', 'class c')->sql();
		var_dump($result);
		$this->assertEquals(count($result), 1);
	}

	public function doTearDown() {
	}
}
