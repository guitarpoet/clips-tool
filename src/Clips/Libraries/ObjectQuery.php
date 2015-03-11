<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");
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


/* Operator: '=' | '>' | '<' | '>=' | '<=' | '~=' | 'like' | 'not like' | '!=' */
protected $match_Operator_typestack = array('Operator');
function match_Operator ($stack = array()) {
	$matchrule = "Operator"; $result = $this->construct($matchrule, $matchrule, null);
	$_39 = NULL;
	do {
		$res_8 = $result;
		$pos_8 = $this->pos;
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
			$_39 = TRUE; break;
		}
		$result = $res_8;
		$this->pos = $pos_8;
		$_37 = NULL;
		do {
			$res_10 = $result;
			$pos_10 = $this->pos;
			if (substr($this->string,$this->pos,1) == '>') {
				$this->pos += 1;
				$result["text"] .= '>';
				$_37 = TRUE; break;
			}
			$result = $res_10;
			$this->pos = $pos_10;
			$_35 = NULL;
			do {
				$res_12 = $result;
				$pos_12 = $this->pos;
				if (substr($this->string,$this->pos,1) == '<') {
					$this->pos += 1;
					$result["text"] .= '<';
					$_35 = TRUE; break;
				}
				$result = $res_12;
				$this->pos = $pos_12;
				$_33 = NULL;
				do {
					$res_14 = $result;
					$pos_14 = $this->pos;
					if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_33 = TRUE; break;
					}
					$result = $res_14;
					$this->pos = $pos_14;
					$_31 = NULL;
					do {
						$res_16 = $result;
						$pos_16 = $this->pos;
						if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
							$result["text"] .= $subres;
							$_31 = TRUE; break;
						}
						$result = $res_16;
						$this->pos = $pos_16;
						$_29 = NULL;
						do {
							$res_18 = $result;
							$pos_18 = $this->pos;
							if (( $subres = $this->literal( '~=' ) ) !== FALSE) {
								$result["text"] .= $subres;
								$_29 = TRUE; break;
							}
							$result = $res_18;
							$this->pos = $pos_18;
							$_27 = NULL;
							do {
								$res_20 = $result;
								$pos_20 = $this->pos;
								if (( $subres = $this->literal( 'like' ) ) !== FALSE) {
									$result["text"] .= $subres;
									$_27 = TRUE; break;
								}
								$result = $res_20;
								$this->pos = $pos_20;
								$_25 = NULL;
								do {
									$res_22 = $result;
									$pos_22 = $this->pos;
									if (( $subres = $this->literal( 'not like' ) ) !== FALSE) {
										$result["text"] .= $subres;
										$_25 = TRUE; break;
									}
									$result = $res_22;
									$this->pos = $pos_22;
									if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
										$result["text"] .= $subres;
										$_25 = TRUE; break;
									}
									$result = $res_22;
									$this->pos = $pos_22;
									$_25 = FALSE; break;
								}
								while(0);
								if( $_25 === TRUE ) { $_27 = TRUE; break; }
								$result = $res_20;
								$this->pos = $pos_20;
								$_27 = FALSE; break;
							}
							while(0);
							if( $_27 === TRUE ) { $_29 = TRUE; break; }
							$result = $res_18;
							$this->pos = $pos_18;
							$_29 = FALSE; break;
						}
						while(0);
						if( $_29 === TRUE ) { $_31 = TRUE; break; }
						$result = $res_16;
						$this->pos = $pos_16;
						$_31 = FALSE; break;
					}
					while(0);
					if( $_31 === TRUE ) { $_33 = TRUE; break; }
					$result = $res_14;
					$this->pos = $pos_14;
					$_33 = FALSE; break;
				}
				while(0);
				if( $_33 === TRUE ) { $_35 = TRUE; break; }
				$result = $res_12;
				$this->pos = $pos_12;
				$_35 = FALSE; break;
			}
			while(0);
			if( $_35 === TRUE ) { $_37 = TRUE; break; }
			$result = $res_10;
			$this->pos = $pos_10;
			$_37 = FALSE; break;
		}
		while(0);
		if( $_37 === TRUE ) { $_39 = TRUE; break; }
		$result = $res_8;
		$this->pos = $pos_8;
		$_39 = FALSE; break;
	}
	while(0);
	if( $_39 === TRUE ) { return $this->finalise($result); }
	if( $_39 === FALSE) { return FALSE; }
}


/* Name: Word ( Word | Number ) * */
protected $match_Name_typestack = array('Name');
function match_Name ($stack = array()) {
	$matchrule = "Name"; $result = $this->construct($matchrule, $matchrule, null);
	$_49 = NULL;
	do {
		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_49 = FALSE; break; }
		while (true) {
			$res_48 = $result;
			$pos_48 = $this->pos;
			$_47 = NULL;
			do {
				$_45 = NULL;
				do {
					$res_42 = $result;
					$pos_42 = $this->pos;
					$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_45 = TRUE; break;
					}
					$result = $res_42;
					$this->pos = $pos_42;
					$matcher = 'match_'.'Number'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_45 = TRUE; break;
					}
					$result = $res_42;
					$this->pos = $pos_42;
					$_45 = FALSE; break;
				}
				while(0);
				if( $_45 === FALSE) { $_47 = FALSE; break; }
				$_47 = TRUE; break;
			}
			while(0);
			if( $_47 === FALSE) {
				$result = $res_48;
				$this->pos = $pos_48;
				unset( $res_48 );
				unset( $pos_48 );
				break;
			}
		}
		$_49 = TRUE; break;
	}
	while(0);
	if( $_49 === TRUE ) { return $this->finalise($result); }
	if( $_49 === FALSE) { return FALSE; }
}


/* ClassName: ( Name '\\' ) * Name */
protected $match_ClassName_typestack = array('ClassName');
function match_ClassName ($stack = array()) {
	$matchrule = "ClassName"; $result = $this->construct($matchrule, $matchrule, null);
	$_56 = NULL;
	do {
		while (true) {
			$res_54 = $result;
			$pos_54 = $this->pos;
			$_53 = NULL;
			do {
				$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_53 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == '\\') {
					$this->pos += 1;
					$result["text"] .= '\\';
				}
				else { $_53 = FALSE; break; }
				$_53 = TRUE; break;
			}
			while(0);
			if( $_53 === FALSE) {
				$result = $res_54;
				$this->pos = $pos_54;
				unset( $res_54 );
				unset( $pos_54 );
				break;
			}
		}
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_56 = FALSE; break; }
		$_56 = TRUE; break;
	}
	while(0);
	if( $_56 === TRUE ) { return $this->finalise($result); }
	if( $_56 === FALSE) { return FALSE; }
}


/* Alias: DLR Name */
protected $match_Alias_typestack = array('Alias');
function match_Alias ($stack = array()) {
	$matchrule = "Alias"; $result = $this->construct($matchrule, $matchrule, null);
	$_60 = NULL;
	do {
		$matcher = 'match_'.'DLR'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_60 = FALSE; break; }
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_60 = FALSE; break; }
		$_60 = TRUE; break;
	}
	while(0);
	if( $_60 === TRUE ) { return $this->finalise($result); }
	if( $_60 === FALSE) { return FALSE; }
}


/* Value: Alias | QM */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = array()) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, null);
	$_65 = NULL;
	do {
		$res_62 = $result;
		$pos_62 = $this->pos;
		$matcher = 'match_'.'Alias'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_65 = TRUE; break;
		}
		$result = $res_62;
		$this->pos = $pos_62;
		$matcher = 'match_'.'QM'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_65 = TRUE; break;
		}
		$result = $res_62;
		$this->pos = $pos_62;
		$_65 = FALSE; break;
	}
	while(0);
	if( $_65 === TRUE ) { return $this->finalise($result); }
	if( $_65 === FALSE) { return FALSE; }
}


/* Type: ClassName | Alias | '*' | '**' */
protected $match_Type_typestack = array('Type');
function match_Type ($stack = array()) {
	$matchrule = "Type"; $result = $this->construct($matchrule, $matchrule, null);
	$_78 = NULL;
	do {
		$res_67 = $result;
		$pos_67 = $this->pos;
		$matcher = 'match_'.'ClassName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_78 = TRUE; break;
		}
		$result = $res_67;
		$this->pos = $pos_67;
		$_76 = NULL;
		do {
			$res_69 = $result;
			$pos_69 = $this->pos;
			$matcher = 'match_'.'Alias'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_76 = TRUE; break;
			}
			$result = $res_69;
			$this->pos = $pos_69;
			$_74 = NULL;
			do {
				$res_71 = $result;
				$pos_71 = $this->pos;
				if (substr($this->string,$this->pos,1) == '*') {
					$this->pos += 1;
					$result["text"] .= '*';
					$_74 = TRUE; break;
				}
				$result = $res_71;
				$this->pos = $pos_71;
				if (( $subres = $this->literal( '**' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_74 = TRUE; break;
				}
				$result = $res_71;
				$this->pos = $pos_71;
				$_74 = FALSE; break;
			}
			while(0);
			if( $_74 === TRUE ) { $_76 = TRUE; break; }
			$result = $res_69;
			$this->pos = $pos_69;
			$_76 = FALSE; break;
		}
		while(0);
		if( $_76 === TRUE ) { $_78 = TRUE; break; }
		$result = $res_67;
		$this->pos = $pos_67;
		$_78 = FALSE; break;
	}
	while(0);
	if( $_78 === TRUE ) { return $this->finalise($result); }
	if( $_78 === FALSE) { return FALSE; }
}


/* QuotedValue: Value | QUOTE > Value > QUOTE */
protected $match_QuotedValue_typestack = array('QuotedValue');
function match_QuotedValue ($stack = array()) {
	$matchrule = "QuotedValue"; $result = $this->construct($matchrule, $matchrule, null);
	$_89 = NULL;
	do {
		$res_80 = $result;
		$pos_80 = $this->pos;
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_89 = TRUE; break;
		}
		$result = $res_80;
		$this->pos = $pos_80;
		$_87 = NULL;
		do {
			$matcher = 'match_'.'QUOTE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_87 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_87 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'QUOTE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_87 = FALSE; break; }
			$_87 = TRUE; break;
		}
		while(0);
		if( $_87 === TRUE ) { $_89 = TRUE; break; }
		$result = $res_80;
		$this->pos = $pos_80;
		$_89 = FALSE; break;
	}
	while(0);
	if( $_89 === TRUE ) { return $this->finalise($result); }
	if( $_89 === FALSE) { return FALSE; }
}

public function QuotedValue_Value (&$result, $sub) {
		$result['val'] = $sub['text'];
	}

/* Condition: (Name | Alias) > Operator > QuotedValue */
protected $match_Condition_typestack = array('Condition');
function match_Condition ($stack = array()) {
	$matchrule = "Condition"; $result = $this->construct($matchrule, $matchrule, null);
	$_102 = NULL;
	do {
		$_96 = NULL;
		do {
			$_94 = NULL;
			do {
				$res_91 = $result;
				$pos_91 = $this->pos;
				$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_94 = TRUE; break;
				}
				$result = $res_91;
				$this->pos = $pos_91;
				$matcher = 'match_'.'Alias'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_94 = TRUE; break;
				}
				$result = $res_91;
				$this->pos = $pos_91;
				$_94 = FALSE; break;
			}
			while(0);
			if( $_94 === FALSE) { $_96 = FALSE; break; }
			$_96 = TRUE; break;
		}
		while(0);
		if( $_96 === FALSE) { $_102 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Operator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_102 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'QuotedValue'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_102 = FALSE; break; }
		$_102 = TRUE; break;
	}
	while(0);
	if( $_102 === TRUE ) { return $this->finalise($result); }
	if( $_102 === FALSE) { return FALSE; }
}

public function Condition_Operator (&$result, $sub) {
		$result['op'] = $sub['text'];
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
	$_109 = NULL;
	do {
		$matcher = 'match_'.'Condition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_109 = FALSE; break; }
		while (true) {
			$res_108 = $result;
			$pos_108 = $this->pos;
			$_107 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Condition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_107 = FALSE; break; }
				$_107 = TRUE; break;
			}
			while(0);
			if( $_107 === FALSE) {
				$result = $res_108;
				$this->pos = $pos_108;
				unset( $res_108 );
				unset( $pos_108 );
				break;
			}
		}
		$_109 = TRUE; break;
	}
	while(0);
	if( $_109 === TRUE ) { return $this->finalise($result); }
	if( $_109 === FALSE) { return FALSE; }
}

public function Conditions_Condition (&$result, $sub) {
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

/* Selector: Type ( > LB > Conditions > RB )? */
protected $match_Selector_typestack = array('Selector');
function match_Selector ($stack = array()) {
	$matchrule = "Selector"; $result = $this->construct($matchrule, $matchrule, null);
	$_120 = NULL;
	do {
		$matcher = 'match_'.'Type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_120 = FALSE; break; }
		$res_119 = $result;
		$pos_119 = $this->pos;
		$_118 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'LB'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_118 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Conditions'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_118 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'RB'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_118 = FALSE; break; }
			$_118 = TRUE; break;
		}
		while(0);
		if( $_118 === FALSE) {
			$result = $res_119;
			$this->pos = $pos_119;
			unset( $res_119 );
			unset( $pos_119 );
		}
		$_120 = TRUE; break;
	}
	while(0);
	if( $_120 === TRUE ) { return $this->finalise($result); }
	if( $_120 === FALSE) { return FALSE; }
}

public function Selector_Type (&$result, $sub) {
		$result['type'] = $sub['text'];
	}

public function Selector_Conditions (&$result ,$sub) {
		if(isset($sub['conditions'])) {
			$result['conditions'] = $sub['conditions'];
			if(isset($sub['args']))
				$result['args'] = $sub['args'];
		}
	}

/* Selectors: ( Selector > ',' > )* Selector */
protected $match_Selectors_typestack = array('Selectors');
function match_Selectors ($stack = array()) {
	$matchrule = "Selectors"; $result = $this->construct($matchrule, $matchrule, null);
	$_129 = NULL;
	do {
		while (true) {
			$res_127 = $result;
			$pos_127 = $this->pos;
			$_126 = NULL;
			do {
				$matcher = 'match_'.'Selector'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_126 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_126 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$_126 = TRUE; break;
			}
			while(0);
			if( $_126 === FALSE) {
				$result = $res_127;
				$this->pos = $pos_127;
				unset( $res_127 );
				unset( $pos_127 );
				break;
			}
		}
		$matcher = 'match_'.'Selector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_129 = FALSE; break; }
		$_129 = TRUE; break;
	}
	while(0);
	if( $_129 === TRUE ) { return $this->finalise($result); }
	if( $_129 === FALSE) { return FALSE; }
}

public function Selectors_Selector (&$result, $sub) {
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

/* TreeSelector: ( Selectors > '>' > )* Selectors */
protected $match_TreeSelector_typestack = array('TreeSelector');
function match_TreeSelector ($stack = array()) {
	$matchrule = "TreeSelector"; $result = $this->construct($matchrule, $matchrule, null);
	$_138 = NULL;
	do {
		while (true) {
			$res_136 = $result;
			$pos_136 = $this->pos;
			$_135 = NULL;
			do {
				$matcher = 'match_'.'Selectors'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_135 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == '>') {
					$this->pos += 1;
					$result["text"] .= '>';
				}
				else { $_135 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$_135 = TRUE; break;
			}
			while(0);
			if( $_135 === FALSE) {
				$result = $res_136;
				$this->pos = $pos_136;
				unset( $res_136 );
				unset( $pos_136 );
				break;
			}
		}
		$matcher = 'match_'.'Selectors'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_138 = FALSE; break; }
		$_138 = TRUE; break;
	}
	while(0);
	if( $_138 === TRUE ) { return $this->finalise($result); }
	if( $_138 === FALSE) { return FALSE; }
}

public function TreeSelector_Selectors (&$result, $sub) {
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
		if(isset($sub['args']))
			$result['args'] = $sub['args'];
		$result['expr'] = $sub;
	}


}
