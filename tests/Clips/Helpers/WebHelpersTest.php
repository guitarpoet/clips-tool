<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class WebHelpersTest extends Clips\TestCase {
	public function testSiteUri() {
		echo site_url('adsf');
	}
}
