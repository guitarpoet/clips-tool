<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * Dump the object using var_dump, this formatter mostly used for debug.
 *
 * @author Jack
 * @date Sat Feb 21 12:38:58 2015
 */
class DumpFormatter extends \Clips\Formatter {
	public function format($obj) {
		ob_start();
		var_dump($obj);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}
