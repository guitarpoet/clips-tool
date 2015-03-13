<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;

/**
 * The encryptor library
 *
 * @author Jack
 * @date Fri Mar 13 15:34:38 2015
 */
class Encryptor extends BaseService {
	public function doInit() {
		$this->keyPath = $this->config('encryptor_private_key', true);
		$this->publicKeyPath = $this->config('encryptor_public_key', true);
        $this->iv = mcrypt_create_iv(32);

		if($this->keyPath && file_exists($this->keyPath)) {
			$this->privateKey = file_get_contents($this->keyPath);
		}
		else {
			$this->logger->debug('No private key or public set for this encryptor.');
		}

		if($this->publicKeyPath && file_exists($this->publicKeyPath)) {
			$this->publicKey = file_get_contents($this->publicKeyPath);
		}
		else {
			$this->logger->debug('No public key or public set for this encryptor.');
		}
	}

	public function privateEncrypt($message) {
		$key = get_default($this, 'privateKey');
		if($key) {
			$ret = '';
			if(openssl_private_encrypt($message, $ret, $key)) {
				return base64_encode($ret);
			}
		}
		return false;
	}

	public function privateDecrypt($message) {
		$key = get_default($this, 'privateKey');
		if($key) {
			$ret = '';
			if(openssl_private_decrypt(base64_decode($message), $ret, $key)) {
				return $ret;
			}
		}
		return false;
	}

	public function publicEncrypt($message) {
		$key = get_default($this, 'publicKey');
		if($key) {
			$ret = '';
			if(openssl_public_encrypt($message, $ret, $key)) {
				return base64_encode($ret);
			}
		}
		return false;
	}

	public function publicDecrypt($message) {
		$key = get_default($this, 'publicKey');
		if($key) {
			$ret = '';
			if(openssl_public_decrypt(base64_decode($message), $ret, $key)) {
				return $ret;
			}
		}
		return false;
	}

	public function encrypt($message, $secret = null) {
		if(!$secret) {
			$secret = \Clips\random_string();
		}
		$secret_key = hash('sha256', $secret, true);
		return array(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secret_key, $message, MCRYPT_MODE_ECB, $this->iv)), $secret);
	}

	public function decrypt($message, $secret) {
		$secret_key = hash('sha256', $secret, true);
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secret_key, base64_decode($message), MCRYPT_MODE_ECB, $this->iv));
	}
}
