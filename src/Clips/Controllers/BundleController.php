<?php namespace Clips\Controllers; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Controller;

class BundleController extends Controller {
	public function show($bundle = '') {
		$b = $this->tool->bundle($bundle);
		return $this->json($b->all());
	}
}
