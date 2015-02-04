<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class WebHelpersTest extends Clips\TestCase {
	public function testSiteUri() {
		echo site_url('adsf');
	}

	public function testRequireWidgetSmartyPlugin() {
		require_widget_smarty_plugin('Html', 'h1');
		$this->assertTrue(function_exists('smarty_block_h1'));
	}

	public function testToHeader() {
		$this->assertEquals(to_header('content_type'), "Content-Type");
	}
}
