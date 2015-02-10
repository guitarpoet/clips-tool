<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class HtmlHelperTest extends Clips\TestCase {
	public function testCreateTag() {
		$this->assertEquals("<div class=\"a b\">\n\t\n</div>", Clips\create_tag('div', array('class' => array('a', 'b')), array(), ''));
	}
}
