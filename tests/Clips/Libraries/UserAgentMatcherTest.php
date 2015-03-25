<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Libraries\UserAgentMatcher;

class UserAgentMatcherTest extends Clips\TestCase {

	public function testMatch() {
		$matcher = new UserAgentMatcher('IE');
		var_dump($matcher->match_Name());
		var_dump($matcher->match_Browser());
		var_dump($matcher->match_Expr());
		$matcher = new UserAgentMatcher('IE[9]');
		var_dump($matcher->match_Browser());
		var_dump($matcher->match_Expr());
		var_dump($matcher->match_Browser());
		$matcher = new UserAgentMatcher('IE[>=9]');
		var_dump($matcher->match_Expr());
	}
}
