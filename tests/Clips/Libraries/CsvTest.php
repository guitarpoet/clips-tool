<?php  in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * @Clips\Library('csv');
 */
class CsvTest extends Clips\TestCase {
	public function testRead() {
		$arr = $this->csv->read('file://not exists');
		$this->assertEquals($arr, array());
		$arr = $this->csv->read('src://data/sample.csv', array('password'=>'jack'));
		var_dump($arr);
	}
}
