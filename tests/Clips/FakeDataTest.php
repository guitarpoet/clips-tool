<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * @Clips\Object({"FakeData", "Validator"})
 */
class FakeDataTest extends Clips\TestCase {

	public function testFakeName() {
		$this->assertNotNull($this->fakedata);
		$name = $this->fakedata->fakeName();
		$this->assertTrue(isset($name->simple_name) && isset($name->first_name)
			&& isset($name->last_name) && isset($name->name));
	}

	public function testFakeMac() {
		$mac = $this->fakedata->fakeMac();
		$this->assertNotNull($mac);
	}

	public function testFakeIP() {
		$this->assertNotNull($this->validator);
		// The fake ip must be a validate ip address
		$this->assertEquals($this->validator->valid_ip($this->fakedata->fakeIP()),
		   	array());
	}

	public function testFakeEmail() {
		$this->assertEquals($this->validator->valid_ip($this->fakedata->fakeEmail()),
		   	array());
	}
}
