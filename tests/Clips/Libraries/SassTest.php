<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * @Clips\Library('sass')
 */
class SassTest extends Clips\TestCase {
	public function testSassLoadPlugins() {
		$this->assertNotNull($this->sass);
	}

	public function testSassCompile() {
		$this->sass->output_style = SASS_FORMAT_COMPRESSED;
		$this->assertEquals(trim($this->sass->compile("string://foo { width: 2*2px;}")), "foo{width:4px}");
	}
}
