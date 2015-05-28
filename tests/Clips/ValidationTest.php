<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\TestCase;

/**
 * @Clips\Object("Validator")
 */
class ValidationTest extends TestCase {

	public function testValidationIPv4() {
		$this->assertEquals(count($this->validator->valid_ip(array('ip', 'test', '1.2.3.40'))), 0 );
		$this->assertEquals(count($this->validator->valid_ip(array('ip', 'test', 'a1.2.3.40'))), 1);
		$this->assertEquals(count($this->validator->valid_ip(array('ip', 'test', '1.2.3.400'))), 1);
		$this->assertEquals(count($this->validator->valid_ip(array('ip', 'test', 'This is not an address'))), 1);
	}

	/**
	 * @Clips\Object("FakeData")
	 */
	public function testValidateName() {
		$name = $this->fakedata->fakeName();
		$this->assertEquals($this->validator->valid_name(array('name', 'test', $name->simple_name)), array());
		$this->assertEquals(count($this->validator->valid_name(array('name', 'test',$name->name))), 1);
		$this->assertEquals(count($this->validator->valid_name(array('name', 'test', '~SHIT !@#$'))), 7);
	}

	public function testValidateDomainName() {
		$this->assertEquals($this->validator->valid_domain(array('domain', 'test', 'thinkingcloud.info')), array());
		$this->assertEquals($this->validator->valid_domain(array('domain', 'test', 'www.w3c-school.org')), array());
		$this->assertEquals(count($this->validator->valid_domain(array('domain', 'test', 'Oh No!'))), 3);
		$this->assertEquals(count($this->validator->valid_domain(array('domain', 'test', '$$$.micro$oft.com'))), 2);
	}

	public function testValidateEmail() {
		$this->assertEquals(count($this->validator->valid_email(array('email', 'test', 'adsf'))), 1);
		$this->assertEquals(count($this->validator->valid_email(array('email', 'test', 'adsf@a@b'))), 1);
		$this->assertEquals(count($this->validator->valid_email(array('email', 'test', 'guitarpoet@gmail.com'))), 0);
		$this->assertEquals(count($this->validator->valid_email(array('email', 'test', '~guitarpoet@gmail.com'))), 2);
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
		$errors = $this->validator->validate($arr, Clips\parse_json($json));
		var_dump($errors);
		$this->assertEquals(count($errors), 2);
		$arr['age'] = 100;
		$errors = $this->validator->validate($arr, Clips\parse_json($json));
		$this->assertEquals(count($errors), 2);
		$arr['age'] = 'test';
		$errors = $this->validator->validate($arr, Clips\parse_json($json));
		$this->assertEquals(count($errors), 2);
		$arr['regex'] = 'az';
		$errors = $this->validator->validate($arr, Clips\parse_json($json));
		$this->assertEquals(count($errors), 3);
		$arr['regex'] = 'abcdefgz';
		$errors = $this->validator->validate($arr, Clips\parse_json($json));
		$this->assertEquals(count($errors), 2);
	}

	public function doTearDown() {
		$this->clips->clear();
	}
}
