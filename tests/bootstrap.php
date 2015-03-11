<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

require_once(__DIR__.'/../vendor/autoload.php');

Clips\context('bundle_dir', 'messages', true);
