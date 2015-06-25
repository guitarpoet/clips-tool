<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");
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
Type: ClassName | Alias | '*' | '**'
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

		if(!isset($result['args'])) {
			$result['args'] = 0;
		}

		$result['conditions'] []= $sub;

		if($sub['val'] == '?')
			$result['args'] = $result['args'] + 1;
	}

Selector: Type ( > LB > Conditions > RB )?
	function Type(&$result, $sub) {
		$result['type'] = $sub['text'];
	}

	function Conditions(&$result ,$sub) {
		if(isset($sub['conditions'])) {
			$result['conditions'] = $sub['conditions'];
			if(isset($sub['args']))
				$result['args'] = $sub['args'];
		}
	}


Selectors: ( Selector > ',' > )* Selector
	function Selector(&$result, $sub) {
		if(!isset($result['selectors'])) {
			$result['selectors'] = array();
		}

		if(!isset($result['args'])) {
			$result['args'] = 0;
		}

		$result['selectors'] []= $sub;
		if(isset($sub['args']))
			$result['args'] += $sub['args'];
	}


TreeSelector: ( Selectors > '>' > )* Selectors
	function Selectors(&$result, $sub) {
		if(!isset($result['layers'])) {
			$result['layers'] = array();
		}
		if(!isset($result['args'])) {
			$result['args'] = 0;
		}
		if(isset($sub['args']))
			$result['args'] += $sub['args'];
		$result['layers'] []= $sub;
	}

Expr: TreeSelector
	function TreeSelector(&$result, $sub) {
		if(isset($sub['args']))
			$result['args'] = $sub['args'];
		$result['expr'] = $sub;
	}

*/
}
