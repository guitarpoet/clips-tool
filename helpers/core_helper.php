<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function console_meta() {
	return array('width' => `tput cols`);
}

function is_cli() {
	return (php_sapi_name() === 'cli' OR defined('STDIN'));
}
