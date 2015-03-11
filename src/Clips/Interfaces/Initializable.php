<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * This interface indicates that this object should call init method when enhanced
 *
 * @author Jack
 * @date Sat Feb 21 11:58:07 2015
 */
interface Initializable {
	public function init();
}
