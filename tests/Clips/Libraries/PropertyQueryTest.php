<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Libraries\PropertyQuery;

class PropertyQueryTest extends Clips\TestCase {
	public function testParse() {
		$p = new PropertyQuery("*");
		print_r($p->match_Expr());
		$p = new PropertyQuery("*.*");
		print_r($p->match_Expr());
		$p = new PropertyQuery("*[*]");
		print_r($p->match_Expr());
		$p = new PropertyQuery("a.b[0].c[1].d");
		print_r($p->match_Expr());
	}
}
