<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\TestCase;

class ValidationTest extends TestCase {

	public function doSetUp() {
		$this->validator = new Clips\Validator();
	}

	public function testValidationIPv4() {
		$this->assertTrue($this->validator->valid_ip(array('ip', '1.2.3.40')));
		$this->assertFalse($this->validator->valid_ip(array('ip', 'a1.2.3.40')));
		$this->assertFalse($this->validator->valid_ip(array('ip', '1.2.3.400')));
		$this->assertFalse($this->validator->valid_ip(array('ip', 'This is not an address')));
	}

	public function testValidationIPv6() {
		$this->assertTrue($this->validator->valid_ip(array('ip', '1.2.3.40')));
		$this->assertFalse($this->validator->valid_ip(array('ip', 'a1.2.3.40')));
		$this->assertFalse($this->validator->valid_ip(array('ip', '1.2.3.400')));
		$this->assertFalse($this->validator->valid_ip(array('ip', 'This is not an address')));
	}

	public function doTearDown() {
		$this->clips->clear();
	}
}
