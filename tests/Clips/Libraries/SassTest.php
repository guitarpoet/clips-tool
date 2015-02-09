<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class SassTest extends Clips\TestCase {
	public function doSetup() {
		$this->tool = &Clips\get_clips_tool();
		$this->sass = $this->tool->library('Sass');
	}

	public function testSassLoadPlugins() {
		$this->assertNotNull($this->sass);
	}

	public function testSassCompile() {
		$this->sass->output_style = SASS_FORMAT_COMPRESSED;
		$this->assertEquals($this->sass->compile("string://foo { width: 2*2px;}"), "foo{width:4px}");
	}
}
