<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * @Clips\MessageBundle(name="test")
 */
class MessageBundleTest extends Clips\TestCase {

	public function testGetBundle() {
		$this->assertNotNull($this->bundle);
		$this->assertEquals($this->bundle->name, 'test');
	}

	public function testMessage() {
		$this->bundle->locale = 'en-US';
		$this->assertEquals($this->bundle->message('hello world', 'Jack'), 'Hello Jack!');
		$this->assertEquals($this->bundle->message('hello default', 'Jack'), 'Hello Jack!');
		$this->assertEquals($this->bundle->message('key not exists', 'Jack'), 'key not exists');
		$this->bundle->locale = 'false-locale';
		$this->assertEquals($this->bundle->message('hello world', 'Jack'), 'hello world');
		$this->assertEquals($this->bundle->message('hello %s!', 'Jack'), 'hello Jack!');
	}

	public function testTemplate() {
		$this->bundle->locale = 'en-US';
		$this->assertEquals($this->bundle->template('hello world template', array('name' => 'Jack')), 'Hello Jack!');
		$this->assertEquals($this->bundle->template('hello default template', array('name' => 'Jack')), 'Hello Jack!');
		$this->assertEquals($this->bundle->template('key not exists', array('name' => 'Jack')), 'key not exists');
		$this->bundle->locale = 'false-locale';
		$this->assertEquals($this->bundle->template('hello world template', array('name' => 'Jack')), 'hello world template');
		$this->assertEquals($this->bundle->template('hello {{name}}!', array('name' => 'Jack')), 'hello Jack!');
	}
}
