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
		$this->dbmodelv2->where(array(
			'name is not null' => '__yield__',
			'length(?)' => array('name', 3),
			'date between ? and ?' => array('today', 'tomorrow'),
			'name like ?' => '%jack%',
			'name in (?, ?, ?)' => array('jack', 'jake', 'rex'),
			'name' => 'jack',
			'(select count(*) from users) > ?' => 3,
			'age >= ?' => 30,
			'name' => null
		))->compile()->where()->wor(array(
			'name like ?' => '%rex%',
			'name like ?' => '%jake%'
		))->compile();
		print_r($this->dbmodelv2->where);
		print_r($this->dbmodelv2->args);
		exit;
	}
}
