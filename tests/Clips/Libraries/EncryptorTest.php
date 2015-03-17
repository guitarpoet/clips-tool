<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * @Clips\Library("encryptor")
 */
class EncryptorTest extends Clips\TestCase {

	public function testEncrypt() {
		$text = 'test encrypt';

		$ret = $this->encryptor->encrypt($text);

		$this->assertEquals(count($ret), 2);

		$cipher = $ret[0];
		$secret = $ret[1];

		$this->assertNotNull($secret);

		$this->assertEquals($this->encryptor->decrypt($cipher, $secret), $text);
	}
}
