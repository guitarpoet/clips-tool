<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");
use hafriedlander\Peg\Parser;

class FlowStep extends Parser\Basic {
/*!* ObjectQuery
DLR: '$'
LB: '['
RB: ']'
QUOTE: /['"]/
QM: '?'
ANY: /./
Word: /[a-zA-Z_]/
Number: /[0-9]/
Name: Word ( Word | Number ) *
Action: Name
	function Name(&$result, $sub) {
		$result['action'] = $sub['text'];
	}
From: Name
	function Name(&$result, $sub) {
		$result['from'] = $sub['text'];
	}
To: Name
	function Name(&$result, $sub) {
		$result['to'] = $sub['text'];
	}
Rule: > '[' > Action > ']' > ':' > From > '->' > To
	function Action(&$result, $sub) {
		$result['action'] = $sub['action'];
	}
	function From(&$result, $sub) {
		$result['from'] = $sub['from'];
	}
	function To(&$result, $sub) {
		$result['to'] = $sub['to'];
	}
*/
}
