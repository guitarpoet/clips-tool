<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class TagAttributeFormatterTest extends \Clips\TestCase {
	public function testTagAttributeFormatter() {
		$f = \Clips\Formatter::get("TagAttribute");
		$this->assertEquals($f->format(array('PlaceHolder' => 'Hello', 'data_handler' => 'demo', 'class' => array('a', 'b', 'c'))), 'place-holder="Hello" data-handler="demo" class="a b c"');
	}
}
