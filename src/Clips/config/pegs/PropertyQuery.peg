<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");
use hafriedlander\Peg\Parser;

class PropertyQuery extends Parser\Basic {
/*!* PropertyQuery
DLR: '$'
LB: '['
RB: ']'
QUOTE: /['"]/
QM: '?'
ANY: /./
Word: /[a-zA-Z_]/
Number: /[0-9]/
Wild: '*'

Name: Word ( Word | Number ) * | QM | Wild
Index: Number+ | '*'
ArrOper: LB > Index > RB
	function Index(&$result, $sub) {
		$result['index'] = $sub['text'];
	}
ObjOper: '.' > Name
	function Name(&$result, $sub) {
		$result['property'] = $sub['text'];
	}
Opers:  Name ( > ArrOper | ObjOper ) *
	function Name(&$result, $sub) {
		$result['property'] = $sub['text'];
		$result['opers'] = array();
	}
	function ArrOper(&$result, $sub) {
		$result['opers'] []= $sub;
	}
	function ObjOper(&$result, $sub) {
		$result['opers'] []= $sub;
	}

Expr: Opers
	function Opers(&$result, $sub) {
		$result['expr'] = $sub;
	}
*/
}
