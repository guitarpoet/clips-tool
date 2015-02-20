<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class FakeDataTest extends Clips\TestCase {

	/**
	 * @Clips\Object("FakeData")
	 */
	public function testFakeName() {
		$this->assertNotNull($this->fakedata);
		$name = $this->fakedata->fakeName();
		$this->assertTrue(isset($name->simple_name) && isset($name->first_name)
			&& isset($name->last_name) && isset($name->name));
	}

	/**
	 * @Clips\Object("FakeData")
	 */
	public function testFakeMac() {
		$mac = $this->fakedata->fakeMac();
		$this->assertNotNull($mac);
	}

	/**
	 * @Clips\Object({"FakeData", "Validator"})
	 */
	public function testFakeIP() {
		$this->assertNotNull($this->validator);
		// The fake ip must be a validate ip address
		$this->assertEquals($this->validator->valid_ip($this->fakedata->fakeIP()),
		   	array());
	}
}
