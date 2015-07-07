<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\TestCase;

/**
 * @Clips\DataGenerator("sample")
 */
class DataGeneratorTest extends TestCase {
	public function testConfig() {
		$this->assertNotNull($this->data);
		$this->assertNotNull($this->data->sample);
		var_dump($this->data->sample);
		$this->assertNotNull($this->data->sample['user1']);
		$this->assertEquals($this->data->sample['user1']['id'], 1);
	}
}
