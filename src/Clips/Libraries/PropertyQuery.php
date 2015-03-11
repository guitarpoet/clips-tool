<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");
use hafriedlander\Peg\Parser;

class PropertyQuery extends Parser\Basic {
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


/* Wild: '*' */
protected $match_Wild_typestack = array('Wild');
function match_Wild ($stack = array()) {
	$matchrule = "Wild"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '*') {
		$this->pos += 1;
		$result["text"] .= '*';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Name: Word ( Word | Number ) * | QM | Wild */
protected $match_Name_typestack = array('Name');
function match_Name ($stack = array()) {
	$matchrule = "Name"; $result = $this->construct($matchrule, $matchrule, null);
	$_25 = NULL;
	do {
		$res_9 = $result;
		$pos_9 = $this->pos;
		$_18 = NULL;
		do {
			$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_18 = FALSE; break; }
			while (true) {
				$res_17 = $result;
				$pos_17 = $this->pos;
				$_16 = NULL;
				do {
					$_14 = NULL;
					do {
						$res_11 = $result;
						$pos_11 = $this->pos;
						$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_14 = TRUE; break;
						}
						$result = $res_11;
						$this->pos = $pos_11;
						$matcher = 'match_'.'Number'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
							$_14 = TRUE; break;
						}
						$result = $res_11;
						$this->pos = $pos_11;
						$_14 = FALSE; break;
					}
					while(0);
					if( $_14 === FALSE) { $_16 = FALSE; break; }
					$_16 = TRUE; break;
				}
				while(0);
				if( $_16 === FALSE) {
					$result = $res_17;
					$this->pos = $pos_17;
					unset( $res_17 );
					unset( $pos_17 );
					break;
				}
			}
			$_18 = TRUE; break;
		}
		while(0);
		if( $_18 === TRUE ) { $_25 = TRUE; break; }
		$result = $res_9;
		$this->pos = $pos_9;
		$_23 = NULL;
		do {
			$res_20 = $result;
			$pos_20 = $this->pos;
			$matcher = 'match_'.'QM'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_23 = TRUE; break;
			}
			$result = $res_20;
			$this->pos = $pos_20;
			$matcher = 'match_'.'Wild'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_23 = TRUE; break;
			}
			$result = $res_20;
			$this->pos = $pos_20;
			$_23 = FALSE; break;
		}
		while(0);
		if( $_23 === TRUE ) { $_25 = TRUE; break; }
		$result = $res_9;
		$this->pos = $pos_9;
		$_25 = FALSE; break;
	}
	while(0);
	if( $_25 === TRUE ) { return $this->finalise($result); }
	if( $_25 === FALSE) { return FALSE; }
}


/* Index: Number+ | '*' */
protected $match_Index_typestack = array('Index');
function match_Index ($stack = array()) {
	$matchrule = "Index"; $result = $this->construct($matchrule, $matchrule, null);
	$_30 = NULL;
	do {
		$res_27 = $result;
		$pos_27 = $this->pos;
		$count = 0;
		while (true) {
			$res_28 = $result;
			$pos_28 = $this->pos;
			$matcher = 'match_'.'Number'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else {
				$result = $res_28;
				$this->pos = $pos_28;
				unset( $res_28 );
				unset( $pos_28 );
				break;
			}
			$count++;
		}
		if ($count >= 1) { $_30 = TRUE; break; }
		$result = $res_27;
		$this->pos = $pos_27;
		if (substr($this->string,$this->pos,1) == '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_30 = TRUE; break;
		}
		$result = $res_27;
		$this->pos = $pos_27;
		$_30 = FALSE; break;
	}
	while(0);
	if( $_30 === TRUE ) { return $this->finalise($result); }
	if( $_30 === FALSE) { return FALSE; }
}


/* ArrOper: LB > Index > RB */
protected $match_ArrOper_typestack = array('ArrOper');
function match_ArrOper ($stack = array()) {
	$matchrule = "ArrOper"; $result = $this->construct($matchrule, $matchrule, null);
	$_37 = NULL;
	do {
		$matcher = 'match_'.'LB'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_37 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Index'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_37 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'RB'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_37 = FALSE; break; }
		$_37 = TRUE; break;
	}
	while(0);
	if( $_37 === TRUE ) { return $this->finalise($result); }
	if( $_37 === FALSE) { return FALSE; }
}

public function ArrOper_Index (&$result, $sub) {
		$result['index'] = $sub['text'];
	}

/* ObjOper: '.' > Name */
protected $match_ObjOper_typestack = array('ObjOper');
function match_ObjOper ($stack = array()) {
	$matchrule = "ObjOper"; $result = $this->construct($matchrule, $matchrule, null);
	$_42 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '.') {
			$this->pos += 1;
			$result["text"] .= '.';
		}
		else { $_42 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_42 = FALSE; break; }
		$_42 = TRUE; break;
	}
	while(0);
	if( $_42 === TRUE ) { return $this->finalise($result); }
	if( $_42 === FALSE) { return FALSE; }
}

public function ObjOper_Name (&$result, $sub) {
		$result['property'] = $sub['text'];
	}

/* Opers:  Name ( > ArrOper | ObjOper ) * */
protected $match_Opers_typestack = array('Opers');
function match_Opers ($stack = array()) {
	$matchrule = "Opers"; $result = $this->construct($matchrule, $matchrule, null);
	$_55 = NULL;
	do {
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_55 = FALSE; break; }
		while (true) {
			$res_54 = $result;
			$pos_54 = $this->pos;
			$_53 = NULL;
			do {
				$_51 = NULL;
				do {
					$res_45 = $result;
					$pos_45 = $this->pos;
					$_48 = NULL;
					do {
						if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
						$matcher = 'match_'.'ArrOper'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres );
						}
						else { $_48 = FALSE; break; }
						$_48 = TRUE; break;
					}
					while(0);
					if( $_48 === TRUE ) { $_51 = TRUE; break; }
					$result = $res_45;
					$this->pos = $pos_45;
					$matcher = 'match_'.'ObjOper'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres );
						$_51 = TRUE; break;
					}
					$result = $res_45;
					$this->pos = $pos_45;
					$_51 = FALSE; break;
				}
				while(0);
				if( $_51 === FALSE) { $_53 = FALSE; break; }
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
		$_55 = TRUE; break;
	}
	while(0);
	if( $_55 === TRUE ) { return $this->finalise($result); }
	if( $_55 === FALSE) { return FALSE; }
}

public function Opers_Name (&$result, $sub) {
		$result['property'] = $sub['text'];
		$result['opers'] = array();
	}

public function Opers_ArrOper (&$result, $sub) {
		$result['opers'] []= $sub;
	}

public function Opers_ObjOper (&$result, $sub) {
		$result['opers'] []= $sub;
	}

/* Expr: Opers */
protected $match_Expr_typestack = array('Expr');
function match_Expr ($stack = array()) {
	$matchrule = "Expr"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'Opers'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres );
		return $this->finalise($result);
	}
	else { return FALSE; }
}

public function Expr_Opers (&$result, $sub) {
		$result['expr'] = $sub;
	}


}
