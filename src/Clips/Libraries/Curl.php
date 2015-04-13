<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The curl wrapper class
 *
 * @author Jack
 * @version 1.0
 * @date Mon Apr 13 14:53:47 2015
 */
class Curl extends \Curl\Curl {

    public function __construct() {
		parent::__construct();
	}

}
