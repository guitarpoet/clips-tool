<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * @author Jack
 * @date Fri Jun 19 10:51:54 2015
 * @version 1.1
 *
 * @param sql
 * 		The sql query model to filter
 * @param config
 * 		The sql query filter configuration
 */
interface SqlFilter {
	public function filter($sql, $config);
}
