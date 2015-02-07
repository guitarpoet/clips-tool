<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\TestCase;

class ValidationTest extends TestCase {

	public function doSetUp() {
		$this->validator = new Clips\Validator();
	}

	public function testValidationIPv4() {
		$this->assertEquals(count($this->validator->valid_ip(array('ip', '1.2.3.40'))), 0 );
		$this->assertEquals(count($this->validator->valid_ip(array('ip', 'a1.2.3.40'))), 1);
		$this->assertEquals(count($this->validator->valid_ip(array('ip', '1.2.3.400'))), 1);
		$this->assertEquals(count($this->validator->valid_ip(array('ip', 'This is not an address'))), 1);
	}

	public function testValidate() {
		$arr = array('password' => 'password', 'remember_me' => true, 'age' => -1);
		$json = <<<TEXT
[
        {
                "field": "username",
                "rules": [
					"required"
				],
				"messages": {
					"required": "This username field must be set!"
				}
        },
        {
                "field": "age",
                "rules": [
					{
						"type": "number",
						"min": 20,
						"max": 40
					},
					"required"
				],
				"messages": {
					"required": "This age field must be set!"
				}
        },
        {
                "field": "password",
                "rules": [
					"required"
				],
				"messages": {
					"required": "This password field must be set"
				}
        },
        {
                "field": "regex",
				"rules": {
					"regex": "^abc.*z$"
				}
        },
        {
                "field": "remember_me"
        }
]

TEXT;
		$errors = $this->validator->validate($arr, parse_json($json));
		var_dump($errors);
		$this->assertEquals(count($errors), 2);
		$arr['age'] = 100;
		$errors = $this->validator->validate($arr, parse_json($json));
		$this->assertEquals(count($errors), 2);
		$arr['age'] = 'test';
		$errors = $this->validator->validate($arr, parse_json($json));
		$this->assertEquals(count($errors), 2);
		$arr['regex'] = 'az';
		$errors = $this->validator->validate($arr, parse_json($json));
		$this->assertEquals(count($errors), 3);
		$arr['regex'] = 'abcdefgz';
		$errors = $this->validator->validate($arr, parse_json($json));
		$this->assertEquals(count($errors), 2);
	}

	public function doTearDown() {
		$this->clips->clear();
	}
}
