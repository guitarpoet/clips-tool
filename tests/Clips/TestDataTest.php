<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class UserSample {
}

class GroupSample {
	public $name;
}

class TestDataTest extends Clips\TestCase {
	/**
	 * @Clips\TestData("sample")
	 */
	public function testGetConfig() {
		$this->assertNotNull($this->data);
		$this->assertNotNull($this->data->jack);
		$this->assertTrue(get_class($this->data->jack) == 'UserSample');
		$this->assertNotNull($this->data->jack->groups);
		$this->assertEquals(count($this->data->jack->groups), 1);
		$this->assertNotNull($this->data->user1);
		$this->assertEquals($this->data->user1->name, 'user1');
		print_r($this->data->all());
	}

	/**
	 * @Clips\Model("view")
	 */
	public function testModelTest() {
		$this->assertNotNull($this->view);
	}
}
