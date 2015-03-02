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


/* Operator: '=' | '>' | '<' | '>=' | '<=' | '~=' | 'like' | 'not' > 'like' | '!=' */
protected $match_Operator_typestack = array('Operator');
function match_Operator ($stack = array()) {
	$matchrule = "Operator"; $result = $this->construct($matchrule, $matchrule, null);
	$_43 = NULL;
	do {
		$res_8 = $result;
		$pos_8 = $this->pos;
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
			$_43 = TRUE; break;
		}
		$result = $res_8;
		$this->pos = $pos_8;
		$_41 = NULL;
		do {
			$res_10 = $result;
			$pos_10 = $this->pos;
			if (substr($this->string,$this->pos,1) == '>') {
				$this->pos += 1;
				$result["text"] .= '>';
				$_41 = TRUE; break;
			}
			$result = $res_10;
			$this->pos = $pos_10;
			$_39 = NULL;
			do {
				$res_12 = $result;
				$pos_12 = $this->pos;
				if (substr($this->string,$this->pos,1) == '<') {
					$this->pos += 1;
					$result["text"] .= '<';
					$_39 = TRUE; break;
				}
				$result = $res_12;
				$this->pos = $pos_12;
				$_37 = NULL;
				do {
					$res_14 = $result;
					$pos_14 = $this->pos;
					if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_37 = TRUE; break;
					}
					$result = $res_14;
					$this->pos = $pos_14;
					$_35 = NULL;
					do {
						$res_16 = $result;
						$pos_16 = $this->pos;
						if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
							$result["text"] .= $subres;
							$_35 = TRUE; break;
						}
						$result = $res_16;
						$this->pos = $pos_16;
						$_33 = NULL;
						do {
							$res_18 = $result;
							$pos_18 = $this->pos;
							if (( $subres = $this->literal( '~=' ) ) !== FALSE) {
								$result["text"] .= $subres;
								$_33 = TRUE; break;
							}
							$result = $res_18;
							$this->pos = $pos_18;
							$_31 = NULL;
							do {
								$res_20 = $result;
								$pos_20 = $this->pos;
								if (( $subres = $this->literal( 'like' ) ) !== FALSE) {
									$result["text"] .= $subres;
									$_31 = TRUE; break;
								}
								$result = $res_20;
								$this->pos = $pos_20;
								$_29 = NULL;
								do {
									$res_22 = $result;
									$pos_22 = $this->pos;
									$_26 = NULL;
									do {
										if (( $subres = $this->literal( 'not' ) ) !== FALSE) { $result["text"] .= $subres; }
										else { $_26 = FALSE; break; }
										if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
										if (( $subres = $this->literal( 'like' ) ) !== FALSE) { $result["text"] .= $subres; }
										else { $_26 = FALSE; break; }
										$_26 = TRUE; break;
									}
									while(0);
									if( $_26 === TRUE ) { $_29 = TRUE; break; }
									$result = $res_22;
									$this->pos = $pos_22;
									if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
										$result["text"] .= $subres;
										$_29 = TRUE; break;
									}
									$result = $res_22;
									$this->pos = $pos_22;
									$_29 = FALSE; break;
								}
								while(0);
								if( $_29 === TRUE ) { $_31 = TRUE; break; }
								$result = $res_20;
								$this->pos = $pos_20;
								$_31 = FALSE; break;
							}
							while(0);
							if( $_31 === TRUE ) { $_33 = TRUE; break; }
							$result = $res_18;
							$this->pos = $pos_18;
							$_33 = FALSE; break;
						}
						while(0);
						if( $_33 === TRUE ) { $_35 = TRUE; break; }
						$result = $res_16;
						$this->pos = $pos_16;
						$_35 = FALSE; break;
					}
					while(0);
					if( $_35 === TRUE ) { $_37 = TRUE; break; }
					$result = $res_14;
					$this->pos = $pos_14;
					$_37 = FALSE; break;
				}
				while(0);
				if( $_37 === TRUE ) { $_39 = TRUE; break; }
				$result = $res_12;
				$this->pos = $pos_12;
				$_39 = FALSE; break;
			}
			while(0);
			if( $_39 === TRUE ) { $_41 = TRUE; break; }
			$result = $res_10;
			$this->pos = $pos_10;
			$_41 = FALSE; break;
		}
		while(0);
		if( $_41 === TRUE ) { $_43 = TRUE; break; }
		$result = $res_8;
		$this->pos = $pos_8;
		$_43 = FALSE; break;
	}
	while(0);
	if( $_43 === TRUE ) { return $this->finalise($result); }
	if( $_43 === FALSE) { return FALSE; }
}


/* Name: Word ( Word | Number ) * */
protected $match_Name_typestack = array('Name');
function match_Name ($stack = array()) {
	$matchrule = "Name"; $result = $this->construct($matchrule, $matchrule, null);
	$_53 = NULL;
	do {
		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_53 = FALSE; break; }
		while (true) {
			$res_52 = $result;
			$pos_52 = $this->pos;
			$_51 = NULL;
			do {
				$_49 = NULL;
				do {
					$res_46 = $result;
					$pos_46 = $this->pos;
					$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_49 = TRUE; break;
					}
					$result = $res_46;
					$this->pos = $pos_46;
					$matcher = 'match_'.'Number'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_49 = TRUE; break;
					}
					$result = $res_46;
					$this->pos = $pos_46;
					$_49 = FALSE; break;
				}
				while(0);
				if( $_49 === FALSE) { $_51 = FALSE; break; }
				$_51 = TRUE; break;
			}
			while(0);
			if( $_51 === FALSE) {
				$result = $res_52;
				$this->pos = $pos_52;
				unset( $res_52 );
				unset( $pos_52 );
				break;
			}
		}
		$_53 = TRUE; break;
	}
	while(0);
	if( $_53 === TRUE ) { return $this->finalise($result); }
	if( $_53 === FALSE) { return FALSE; }
}


/* ClassName: ( Name '\\' ) * Name */
protected $match_ClassName_typestack = array('ClassName');
function match_ClassName ($stack = array()) {
	$matchrule = "ClassName"; $result = $this->construct($matchrule, $matchrule, null);
	$_60 = NULL;
	do {
		while (true) {
			$res_58 = $result;
			$pos_58 = $this->pos;
			$_57 = NULL;
			do {
				$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_57 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == '\\') {
					$this->pos += 1;
					$result["text"] .= '\\';
				}
				else { $_57 = FALSE; break; }
				$_57 = TRUE; break;
			}
			while(0);
			if( $_57 === FALSE) {
				$result = $res_58;
				$this->pos = $pos_58;
				unset( $res_58 );
				unset( $pos_58 );
				break;
			}
		}
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


/* Alias: DLR Name */
protected $match_Alias_typestack = array('Alias');
function match_Alias ($stack = array()) {
	$matchrule = "Alias"; $result = $this->construct($matchrule, $matchrule, null);
	$_64 = NULL;
	do {
		$matcher = 'match_'.'DLR'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_64 = FALSE; break; }
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_64 = FALSE; break; }
		$_64 = TRUE; break;
	}
	while(0);
	if( $_64 === TRUE ) { return $this->finalise($result); }
	if( $_64 === FALSE) { return FALSE; }
}


/* Value: Alias | QM */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = array()) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, null);
	$_69 = NULL;
	do {
		$res_66 = $result;
		$pos_66 = $this->pos;
		$matcher = 'match_'.'Alias'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_69 = TRUE; break;
		}
		$result = $res_66;
		$this->pos = $pos_66;
		$matcher = 'match_'.'QM'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_69 = TRUE; break;
		}
		$result = $res_66;
		$this->pos = $pos_66;
		$_69 = FALSE; break;
	}
	while(0);
	if( $_69 === TRUE ) { return $this->finalise($result); }
	if( $_69 === FALSE) { return FALSE; }
}


/* Type: ClassName | Alias */
protected $match_Type_typestack = array('Type');
function match_Type ($stack = array()) {
	$matchrule = "Type"; $result = $this->construct($matchrule, $matchrule, null);
	$_74 = NULL;
	do {
		$res_71 = $result;
		$pos_71 = $this->pos;
		$matcher = 'match_'.'ClassName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_74 = TRUE; break;
		}
		$result = $res_71;
		$this->pos = $pos_71;
		$matcher = 'match_'.'Alias'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_74 = TRUE; break;
		}
		$result = $res_71;
		$this->pos = $pos_71;
		$_74 = FALSE; break;
	}
	while(0);
	if( $_74 === TRUE ) { return $this->finalise($result); }
	if( $_74 === FALSE) { return FALSE; }
}


/* QuotedValue: Value | QUOTE > Value > QUOTE */
protected $match_QuotedValue_typestack = array('QuotedValue');
function match_QuotedValue ($stack = array()) {
	$matchrule = "QuotedValue"; $result = $this->construct($matchrule, $matchrule, null);
	$_85 = NULL;
	do {
		$res_76 = $result;
		$pos_76 = $this->pos;
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_85 = TRUE; break;
		}
		$result = $res_76;
		$this->pos = $pos_76;
		$_83 = NULL;
		do {
			$matcher = 'match_'.'QUOTE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_83 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_83 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'QUOTE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_83 = FALSE; break; }
			$_83 = TRUE; break;
		}
		while(0);
		if( $_83 === TRUE ) { $_85 = TRUE; break; }
		$result = $res_76;
		$this->pos = $pos_76;
		$_85 = FALSE; break;
	}
	while(0);
	if( $_85 === TRUE ) { return $this->finalise($result); }
	if( $_85 === FALSE) { return FALSE; }
}

public function QuotedValue_Value (&$result, $sub) {
		$result['val'] = $sub['text'];
	}

/* Condition: (Name | Alias) > Operator > QuotedValue */
protected $match_Condition_typestack = array('Condition');
function match_Condition ($stack = array()) {
	$matchrule = "Condition"; $result = $this->construct($matchrule, $matchrule, null);
	$_98 = NULL;
	do {
		$_92 = NULL;
		do {
			$_90 = NULL;
			do {
				$res_87 = $result;
				$pos_87 = $this->pos;
				$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_90 = TRUE; break;
				}
				$result = $res_87;
				$this->pos = $pos_87;
				$matcher = 'match_'.'Alias'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres );
					$_90 = TRUE; break;
				}
				$result = $res_87;
				$this->pos = $pos_87;
				$_90 = FALSE; break;
			}
			while(0);
			if( $_90 === FALSE) { $_92 = FALSE; break; }
			$_92 = TRUE; break;
		}
		while(0);
		if( $_92 === FALSE) { $_98 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Operator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_98 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'QuotedValue'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_98 = FALSE; break; }
		$_98 = TRUE; break;
	}
	while(0);
	if( $_98 === TRUE ) { return $this->finalise($result); }
	if( $_98 === FALSE) { return FALSE; }
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
	$_105 = NULL;
	do {
		$matcher = 'match_'.'Condition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_105 = FALSE; break; }
		while (true) {
			$res_104 = $result;
			$pos_104 = $this->pos;
			$_103 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Condition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_103 = FALSE; break; }
				$_103 = TRUE; break;
			}
			while(0);
			if( $_103 === FALSE) {
				$result = $res_104;
				$this->pos = $pos_104;
				unset( $res_104 );
				unset( $pos_104 );
				break;
			}
		}
		$_105 = TRUE; break;
	}
	while(0);
	if( $_105 === TRUE ) { return $this->finalise($result); }
	if( $_105 === FALSE) { return FALSE; }
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
	$_116 = NULL;
	do {
		$matcher = 'match_'.'Type'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_116 = FALSE; break; }
		$res_115 = $result;
		$pos_115 = $this->pos;
		$_114 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'LB'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_114 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Conditions'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_114 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'RB'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_114 = FALSE; break; }
			$_114 = TRUE; break;
		}
		while(0);
		if( $_114 === FALSE) {
			$result = $res_115;
			$this->pos = $pos_115;
			unset( $res_115 );
			unset( $pos_115 );
		}
		$_116 = TRUE; break;
	}
	while(0);
	if( $_116 === TRUE ) { return $this->finalise($result); }
	if( $_116 === FALSE) { return FALSE; }
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
	$_125 = NULL;
	do {
		while (true) {
			$res_123 = $result;
			$pos_123 = $this->pos;
			$_122 = NULL;
			do {
				$matcher = 'match_'.'Selector'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_122 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_122 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$_122 = TRUE; break;
			}
			while(0);
			if( $_122 === FALSE) {
				$result = $res_123;
				$this->pos = $pos_123;
				unset( $res_123 );
				unset( $pos_123 );
				break;
			}
		}
		$matcher = 'match_'.'Selector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_125 = FALSE; break; }
		$_125 = TRUE; break;
	}
	while(0);
	if( $_125 === TRUE ) { return $this->finalise($result); }
	if( $_125 === FALSE) { return FALSE; }
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
	$_134 = NULL;
	do {
		while (true) {
			$res_132 = $result;
			$pos_132 = $this->pos;
			$_131 = NULL;
			do {
				$matcher = 'match_'.'Selectors'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_131 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == '>') {
					$this->pos += 1;
					$result["text"] .= '>';
				}
				else { $_131 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$_131 = TRUE; break;
			}
			while(0);
			if( $_131 === FALSE) {
				$result = $res_132;
				$this->pos = $pos_132;
				unset( $res_132 );
				unset( $pos_132 );
				break;
			}
		}
		$matcher = 'match_'.'Selectors'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_134 = FALSE; break; }
		$_134 = TRUE; break;
	}
	while(0);
	if( $_134 === TRUE ) { return $this->finalise($result); }
	if( $_134 === FALSE) { return FALSE; }
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
