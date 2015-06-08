<?php namespace Clips\Controllers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Controller;

/**
 * @Clips\Widget('html')
 */
class ErrorController extends Controller {
	public function show($error) {
		return $this->not_found($error);
	}
}
