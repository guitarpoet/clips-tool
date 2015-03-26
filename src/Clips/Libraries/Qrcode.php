<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The qrcode utitlities using endroid qrcode
 *
 * @author Jack
 * @version 1.0
 * @date Mon Mar 23 13:17:46 2015
 */
class Qrcode extends \Endroid\QrCode\QrCode {
	public function show($format = 'png') {
		return $this->get($format);
	}
}
