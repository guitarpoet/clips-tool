<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class CoreHelperTest extends Clips\TestCase {
	public function testFindFile() {
		$this->assertEquals(count(find_file(getcwd(), 'HelperTest', 'php')), 1);
		$this->assertEquals(count(find_file(getcwd(), 'HelperTest', 'xml')), 0);
	}

	public function testToFlat() {
		$this->assertEquals(to_flat('ABC'), 'a_b_c');
		$this->assertEquals(to_flat( 'AaBbCc'), 'aa_bb_cc');
		$this->assertEquals(to_flat('D/E/F/AaBbCc'), 'd_e_f_aa_bb_cc');
	}

	public function testToCamel() {
		$this->assertEquals(to_camel('a_b_c'), 'ABC');
		$this->assertEquals(to_camel('aa_bb_cc'), 'AaBbCc');
		$this->assertEquals(to_camel('d/e/f/aa_bb_cc'), 'D/E/F/AaBbCc');
	}
}
