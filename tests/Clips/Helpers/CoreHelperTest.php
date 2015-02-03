<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class CoreHelperTest extends Clips\TestCase {

	public function testToCamel() {
		$this->assertEquals(to_camel('a_b_c'), 'ABC');
		$this->assertEquals(to_camel('aa_bb_cc'), 'AaBbCc');
		$this->assertEquals(to_camel('d/e/f/aa_bb_cc'), 'D/E/F/AaBbCc');
	}
}
