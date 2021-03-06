<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Libraries\UserAgentMatcher;

class UserAgentMatcherTest extends Clips\TestCase {
	public function testMatch() {
		$matcher = new UserAgentMatcher('IE');
		print_r($matcher->match_Expr());
		$matcher = new UserAgentMatcher('Safari{MacOSX}[9]');
		$this->assertTrue(!!$matcher->match_Expr());
		$matcher = new UserAgentMatcher('IE[>=9]');
		$this->assertTrue(!!$matcher->match_Expr());
		$matcher = new UserAgentMatcher('IE{Windows XP}(Desktop)[7.0~9.1]');
		print_r($matcher->match_Expr());
		$matcher = new UserAgentMatcher('IE{Windows XP}(Desktop)[!=9.1]');
		print_r($matcher->match_Expr());
	}
}
