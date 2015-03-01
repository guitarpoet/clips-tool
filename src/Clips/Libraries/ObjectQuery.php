<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");
use hafriedlander\Peg\Parser;

class ObjectQuery extends Parser\Basic {

/* DLR: '$' */
protected $match_DLR_typestack = array('DLR');
function match_DLR ($stack = array()) {
	$matchrule = "DLR"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '$') {
		$this->pos += 1;
		$result["text"] .= '$';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* LB: '[' */
protected $match_LB_typestack = array('LB');
function match_LB ($stack = array()) {
	$matchrule = "LB"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '[') {
		$this->pos += 1;
		$result["text"] .= '[';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* RB: ']' */
protected $match_RB_typestack = array('RB');
function match_RB ($stack = array()) {
	$matchrule = "RB"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == ']') {
		$this->pos += 1;
		$result["text"] .= ']';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* QUOTE: /['"]/ */
protected $match_QUOTE_typestack = array('QUOTE');
function match_QUOTE ($stack = array()) {
	$matchrule = "QUOTE"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[\'"]/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* QM: '?' */
protected $match_QM_typestack = array('QM');
function match_QM ($stack = array()) {
	$matchrule = "QM"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '?') {
		$this->pos += 1;
		$result["text"] .= '?';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* ANY: /./ */
protected $match_ANY_typestack = array('ANY');
function match_ANY ($stack = array()) {
	$matchrule = "ANY"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/./' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Word: /[a-zA-Z_]/ */
protected $match_Word_typestack = array('Word');
function match_Word ($stack = array()) {
	$matchrule = "Word"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[a-zA-Z_]/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Number: /[0-9]/ */
protected $match_Number_typestack = array('Number');
function match_Number ($stack = array()) {
	$matchrule = "Number"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[0-9]/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Name: Word ( Word | Number ) * */
protected $match_Name_typestack = array('Name');
function match_Name ($stack = array()) {
	$matchrule = "Name"; $result = $this->construct($matchrule, $matchrule, null);
	$_16 = NULL;
	do {
		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_16 = FALSE; break; }
		while (true) {
			$res_15 = $result;
			$pos_15 = $this->pos;
			$_14 = NULL;
			do {
				$_12 = NULL;
				do {
					$res_9 = $result;
					$pos_9 = $this->pos;
					$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_12 = TRUE; break;
					}
					$result = $res_9;
					$this->pos = $pos_9;
					$matcher = 'match_'.'Number'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_12 = TRUE; break;
					}
					$result = $res_9;
					$this->pos = $pos_9;
					$_12 = FALSE; break;
				}
				while(0);
				if( $_12 === FALSE) { $_14 = FALSE; break; }
				$_14 = TRUE; break;
			}
			while(0);
			if( $_14 === FALSE) {
				$result = $res_15;
				$this->pos = $pos_15;
				unset( $res_15 );
				unset( $pos_15 );
				break;
			}
		}
		$_16 = TRUE; break;
	}
	while(0);
	if( $_16 === TRUE ) { return $this->finalise($result); }
	if( $_16 === FALSE) { return FALSE; }
}


/* ClassName: ( Name '\\' ) * Name */
protected $match_ClassName_typestack = array('ClassName');
function match_ClassName ($stack = array()) {
	$matchrule = "ClassName"; $result = $this->construct($matchrule, $matchrule, null);
	$_23 = NULL;
	do {
		while (true) {
			$res_21 = $result;
			$pos_21 = $this->pos;
			$_20 = NULL;
			do {
				$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_20 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == '\\') {
					$this->pos += 1;
					$result["text"] .= '\\';
				}
				else { $_20 = FALSE; break; }
				$_20 = TRUE; break;
			}
			while(0);
			if( $_20 === FALSE) {
				$result = $res_21;
				$this->pos = $pos_21;
				unset( $res_21 );
				unset( $pos_21 );
				break;
			}
		}
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_23 = FALSE; break; }
		$_23 = TRUE; break;
	}
	while(0);
	if( $_23 === TRUE ) { return $this->finalise($result); }
	if( $_23 === FALSE) { return FALSE; }
}


/* Alias: DLR Name */
protected $match_Alias_typestack = array('Alias');
function match_Alias ($stack = array()) {
	$matchrule = "Alias"; $result = $this->construct($matchrule, $matchrule, null);
	$_27 = NULL;
	do {
		$matcher = 'match_'.'DLR'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_27 = FALSE; break; }
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_27 = FALSE; break; }
		$_27 = TRUE; break;
	}
	while(0);
	if( $_27 === TRUE ) { return $this->finalise($result); }
	if( $_27 === FALSE) { return FALSE; }
}


/* Value: Alias | QM */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = array()) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, null);
	$_32 = NULL;
	do {
		$res_29 = $result;
		$pos_29 = $this->pos;
		$matcher = 'match_'.'Alias'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_32 = TRUE; break;
		}
		$result = $res_29;
		$this->pos = $pos_29;
		$matcher = 'match_'.'QM'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_32 = TRUE; break;
		}
		$result = $res_29;
		$this->pos = $pos_29;
		$_32 = FALSE; break;
	}
	while(0);
	if( $_32 === TRUE ) { return $this->finalise($result); }
	if( $_32 === FALSE) { return FALSE; }
}


/* Type: ClassName | Alias */
protected $match_Type_typestack = array('Type');
function match_Type ($stack = array()) {
	$matchrule = "Type"; $result = $this->construct($matchrule, $matchrule, null);
	$_37 = NULL;
	do {
		$res_34 = $result;
		$pos_34 = $this->pos;
		$matcher = 'match_'.'ClassName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_37 = TRUE; break;
		}
		$result = $res_34;
		$this->pos = $pos_34;
		$matcher = 'match_'.'Alias'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_37 = TRUE; break;
		}
		$result = $res_34;
		$this->pos = $pos_34;
		$_37 = FALSE; break;
	}
	while(0);
	if( $_37 === TRUE ) { return $this->finalise($result); }
	if( $_37 === FALSE) { return FALSE; }
}


/* QuotedValue: Value | QUOTE > Value > QUOTE */
protected $match_QuotedValue_typestack = array('QuotedValue');
function match_QuotedValue ($stack = array()) {
	$matchrule = "QuotedValue"; $result = $this->construct($matchrule, $matchrule, null);
	$_48 = NULL;
	do {
		$res_39 = $result;
		$pos_39 = $this->pos;
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_48 = TRUE; break;
		}
		$result = $res_39;
		$this->pos = $pos_39;
		$_46 = NULL;
		do {
			$matcher = 'match_'.'QUOTE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_46 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_46 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'QUOTE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_46 = FALSE; break; }
			$_46 = TRUE; break;
		}
		while(0);
		if( $_46 === TRUE ) { $_48 = TRUE; break; }
		$result = $res_39;
		$this->pos = $pos_39;
		$_48 = FALSE; break;
	}
	while(0);
	if( $_48 === TRUE ) { return $this->finalise($result); }
	if( $_48 === FALSE) { return FALSE; }
}

public function QuotedValue_Value (&$result, $sub) {
		$result['val'] = $sub['text'];
	}

/* Condition: (Name | Alias) > '=' > QuotedValue */
protected $match_Condition_typestack = array('Condition');
function match_Condition ($stack = array()) {
	$matchrule = "Condition"; $result = $this->construct($matchrule, $matchrule, null);
	$_61 = NULL;
	do {
		$_55 = NULL;
		do {
			$_53 = NULL;
			do {
				$res_50 = $result;
				$pos_50 = $this->pos;
				$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_53 = TRUE; break;
				}
				$result = $res_50;
				$this->pos = $pos_50;
				$matcher = 'match_'.'Alias'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_53 = TRUE; break;
				}
				$result = $res_50;
				$this->pos = $pos_50;
				$_53 = FALSE; break;
			}
			while(0);
			if( $_53 === FALSE) { $_55 = FALSE; break; }
			$_55 = TRUE; break;
		}
		while(0);
		if( $_55 === FALSE) { $_61 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
		}
		else { $_61 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'QuotedValue'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_61 = FALSE; break; }
		$_61 = TRUE; break;
	}
	while(0);
	if( $_61 === TRUE ) { return $this->finalise($result); }
	if( $_61 === FALSE) { return FALSE; }
}

public function Condition_Name (&$result, $sub) {
		$result['var'] = $sub['text'];
	}

public function Condition_Alias (&$result, $sub) {
		$result['var'] = $sub['text'];
	}

public function Condition_QuotedValue (&$result, $sub) {
		$result['val'] = $sub['val'];
	}

/* Conditions: Condition ( > Condition )* */
protected $match_Conditions_typestack = array('Conditions');
function match_Conditions ($stack = array()) {
	$matchrule = "Conditions"; $result = $this->construct($matchrule, $matchrule, null);
	$_68 = NULL;
	do {
		$matcher = 'match_'.'Condition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_68 = FALSE; break; }
		while (true) {
			$res_67 = $result;
			$pos_67 = $this->pos;
			$_66 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Condition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_66 = FALSE; break; }
				$_66 = TRUE; break;
			}
			while(0);
			if( $_66 === FALSE) {
				$result = $res_67;
				$this->pos = $pos_67;
				unset( $res_67 );
				unset( $pos_67 );
				break;
			}
		}
		$_68 = TRUE; break;
	}
	while(0);
	if( $_68 === TRUE ) { return $this->finalise($result); }
	if( $_68 === FALSE) { return FALSE; }
}

public function Conditions_Condition (&$result, $sub) {
		if(!isset($result['conditions'])) {
			$result['conditions'] = array();
		}
		$result['conditions'] []= $sub;
	}

/* Selector: Type ( > LB > Conditions > RB )? */
protected $match_Selector_typestack = array('Selector');
function match_Selector ($stack = array()) {
	$matchrule = "Selector"; $result = $this->construct($matchrule, $matchrule, null);
	$_79 = NULL;
	do {
		$matcher = 'match_'.'Type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_79 = FALSE; break; }
		$res_78 = $result;
		$pos_78 = $this->pos;
		$_77 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'LB'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_77 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Conditions'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_77 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'RB'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_77 = FALSE; break; }
			$_77 = TRUE; break;
		}
		while(0);
		if( $_77 === FALSE) {
			$result = $res_78;
			$this->pos = $pos_78;
			unset( $res_78 );
			unset( $pos_78 );
		}
		$_79 = TRUE; break;
	}
	while(0);
	if( $_79 === TRUE ) { return $this->finalise($result); }
	if( $_79 === FALSE) { return FALSE; }
}

public function Selector_Type (&$result, $sub) {
		$result['type'] = $sub['text'];
	}

public function Selector_Conditions (&$result ,$sub) {
		if(isset($sub['conditions']))
			$result['conditions'] = $sub['conditions'];
	}

/* Selectors: ( Selector > ',' > )* Selector */
protected $match_Selectors_typestack = array('Selectors');
function match_Selectors ($stack = array()) {
	$matchrule = "Selectors"; $result = $this->construct($matchrule, $matchrule, null);
	$_88 = NULL;
	do {
		while (true) {
			$res_86 = $result;
			$pos_86 = $this->pos;
			$_85 = NULL;
			do {
				$matcher = 'match_'.'Selector'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_85 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_85 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$_85 = TRUE; break;
			}
			while(0);
			if( $_85 === FALSE) {
				$result = $res_86;
				$this->pos = $pos_86;
				unset( $res_86 );
				unset( $pos_86 );
				break;
			}
		}
		$matcher = 'match_'.'Selector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_88 = FALSE; break; }
		$_88 = TRUE; break;
	}
	while(0);
	if( $_88 === TRUE ) { return $this->finalise($result); }
	if( $_88 === FALSE) { return FALSE; }
}

public function Selectors_Selector (&$result, $sub) {
		if(!isset($result['selectors'])) {
			$result['selectors'] = array();
		}
		$result['selectors'] []= $sub;
	}

/* TreeSelector: ( Selectors > '>' > )* Selectors */
protected $match_TreeSelector_typestack = array('TreeSelector');
function match_TreeSelector ($stack = array()) {
	$matchrule = "TreeSelector"; $result = $this->construct($matchrule, $matchrule, null);
	$_97 = NULL;
	do {
		while (true) {
			$res_95 = $result;
			$pos_95 = $this->pos;
			$_94 = NULL;
			do {
				$matcher = 'match_'.'Selectors'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_94 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == '>') {
					$this->pos += 1;
					$result["text"] .= '>';
				}
				else { $_94 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$_94 = TRUE; break;
			}
			while(0);
			if( $_94 === FALSE) {
				$result = $res_95;
				$this->pos = $pos_95;
				unset( $res_95 );
				unset( $pos_95 );
				break;
			}
		}
		$matcher = 'match_'.'Selectors'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_97 = FALSE; break; }
		$_97 = TRUE; break;
	}
	while(0);
	if( $_97 === TRUE ) { return $this->finalise($result); }
	if( $_97 === FALSE) { return FALSE; }
}

public function TreeSelector_Selectors (&$result, $sub) {
		if(!isset($result['layers'])) {
			$result['layers'] = array();
		}
		$result['layers'] []= $sub;
	}

/* Expr: TreeSelector */
protected $match_Expr_typestack = array('Expr');
function match_Expr ($stack = array()) {
	$matchrule = "Expr"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'TreeSelector'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres );
		return $this->finalise($result);
	}
	else { return FALSE; }
}

public function Expr_TreeSelector (&$result, $sub) {
		$result['expr'] = $sub;
	}


}
