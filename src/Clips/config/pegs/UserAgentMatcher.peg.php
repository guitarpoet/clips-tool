<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");
use hafriedlander\Peg\Parser;

class UserAgentMatcher extends Parser\Basic {
/*!* UserAgentMatcher
 
DLR: '$'
LB: '['
RB: ']'
QUOTE: /['"]/
QM: '?'
ANY: /./
Word: /[a-zA-Z_]+/
Number: /[0-9]/
VersionNumber: Number+ ('.' Number+ )?
Name:  Word ( > Word ) *

Operator: '>=' | '<=' | '>' | '<' | '~' 
Browser: Name
	function Name(&$result, $sub) {
		$result['browser'] = $sub['text'];
	}
Platform: '{' > Name > '}'
	function Name(&$result, $sub) {
		$result['platform'] = $sub['text'];
	}
Device: '(' > Name > ')'
	function Name(&$result, $sub) {
		$result['browser'] = $sub['text'];
	}
VersionMatcher: Operator > VersionNumber
	function Operator(&$result, $sub) {
		$result['operator'] = $sub['text'];
	}
	function VersionNumber(&$result, $sub) {
		$result['version'] = $sub['text'];
	}
VersionBetweenMatcher: VersionNumber > '~' > VersionNumber
	function VersionNumber(&$result, $sub) {
		if(!isset($result['version'])) {
			$result['version'] = array();
		}
		$result['version'] []= $sub['text'];
	}
VersionOp: VersionMatcher | VersionBetweenMatcher | VersionNumber
	function VersionNumber(&$result, $sub) {
		$result['type'] = 'version';
		$result['version'] = $sub['text'];
	}
	function VersionMatcher(&$result, $sub) {
		$result['type'] = 'matcher';
		$result['version'] = $sub['version'];
		$result['operator'] = $sub['operator'];
	}
	function VersionBetweenMatcher(&$result, $sub) {
		$result['type'] = 'between';
		$result['versions'] = $sub['version'];
	}
Version: '[' > VersionOp ( > ',' > VersionOp )* > ']'
	function VersionOp(&$result, $sub) {
		if(!isset($result['op'])) {
			$result['op'] = array();
		}
		$result['op'] []= $sub;
	}
Expr: Browser (> Platform)? (> Device)? (> Version)?
	function Browser(&$result, $sub) {
		$result['browser'] = $sub['browser'];
	}
	function Platform(&$result, $sub) {
		$result['platform'] = $sub['platform'];
	}
	function Device(&$result, $sub) {
		$result['device'] = $sub['device'];
	}
	function Version(&$result, $sub) {
		$result['version'] = $sub;
	}

*/
}
