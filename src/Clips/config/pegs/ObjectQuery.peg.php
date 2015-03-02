<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");
use hafriedlander\Peg\Parser;

class ObjectQuery extends Parser\Basic {

/*!* ObjectQuery
 
DLR: '$'
LB: '['
RB: ']'
QUOTE: /['"]/
QM: '?'
ANY: /./
Word: /[a-zA-Z_]/
Number: /[0-9]/

Operator: '=' | '>' | '<' | '>=' | '<=' | '~=' | 'like' | 'not like' | '!='
Name: Word ( Word | Number ) *
ClassName: ( Name '\\' ) * Name
Alias: DLR Name
Value: Alias | QM
Type: ClassName | Alias
QuotedValue: Value | QUOTE > Value > QUOTE
	function Value(&$result, $sub) {
		$result['val'] = $sub['text'];
	}

Condition: (Name | Alias) > Operator > QuotedValue
	function Operator(&$result, $sub) {
		$result['op'] = $sub['text'];
	}

	function Name(&$result, $sub) {
		$result['var'] = $sub['text'];
	}

	function Alias(&$result, $sub) {
		$result['var'] = $sub['text'];
	}

	function QuotedValue(&$result, $sub) {
		$result['val'] = $sub['val'];
	}

Conditions: Condition ( > Condition )*
	function Condition(&$result, $sub) {
		if(!isset($result['conditions'])) {
			$result['conditions'] = array();
		}
		$result['conditions'] []= $sub;
	}

Selector: Type ( > LB > Conditions > RB )?
	function Type(&$result, $sub) {
		$result['type'] = $sub['text'];
	}

	function Conditions(&$result ,$sub) {
		if(isset($sub['conditions']))
			$result['conditions'] = $sub['conditions'];
	}

Selectors: ( Selector > ',' > )* Selector
	function Selector(&$result, $sub) {
		if(!isset($result['selectors'])) {
			$result['selectors'] = array();
		}
		$result['selectors'] []= $sub;
	}

TreeSelector: ( Selectors > '>' > )* Selectors
	function Selectors(&$result, $sub) {
		if(!isset($result['layers'])) {
			$result['layers'] = array();
		}
		$result['layers'] []= $sub;
	}

Expr: TreeSelector
	function TreeSelector(&$result, $sub) {
		$result['expr'] = $sub;
	}

*/
}
