<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\TestCase;

/**
 * @author Jack
 * @version 1.1
 * @date Fri Jun 19 18:45:38 2015
 *
 * @Clips\Library("dbModelV2")
 */
class DBModelV2Test extends TestCase {

	public function testWhere() {
		$this->assertNotNull($this->dbmodelv2);
		$this->dbmodelv2->w(array(
			'name is not null' => '__yield__',
			'length(?)' => array('name', 3),
			'date between ? and ?' => array('today', 'tomorrow'),
			'name like ?' => '%jack%',
			'name in (?, ?, ?)' => array('jack', 'jake', 'rex'),
			'name' => 'jack',
			'(select count(*) from users) > ?' => 3,
			'age >= ?' => 30,
			'name' => null
		))->wor()->wor(array(
			'name like ?' => '%rex%',
			'name' => 'jake'
		))->wand()->wand(array('name' => 'hello'))->compile();
		$this->assertEquals($this->dbmodelv2->where, array('((name is not null) and (length(?) = ?) and (date between ? and ?) and (name like ?) and (name in (?, ?, ?)) and (name is null) and ((select count(*) from users) > ?) and (age >= ?)) or ((name like ?) or (name = ?)) and ((name = ?))'));
	}

	public function testSql() {
		$q = $this->dbmodelv2->from('users')->w(array(
			'name is not null' => '__yield__',
			'length(?)' => array('name', 3)))->compile()->limit()->orderBy('username')->groupBy('id')->sql();
		$this->assertEquals($q[0], 'select * from users where ((name is not null) and (length(?) = ?)) group by id order by username limit 0, 15');
		$q = $this->dbmodelv2->from('users as u')->join('groups as g', 'u.gid = g.id')->join('roles as r', 'u.rid = r.id', 'left')->w(array('u.id' => 1))->compile()->sql();
		$this->assertEquals($q[0], 'select * from users as u join groups as g on u.gid = g.id left join roles as r on u.rid = r.id where ((u.id = ?))');
	}
}
