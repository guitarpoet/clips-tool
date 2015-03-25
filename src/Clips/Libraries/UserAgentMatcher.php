<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");
use hafriedlander\Peg\Parser;

class UserAgentMatcher extends Parser\Basic {
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


/* VersionNumber: ( Number+ . )? Number+ */
protected $match_VersionNumber_typestack = array('VersionNumber');
function match_VersionNumber ($stack = array()) {
	$matchrule = "VersionNumber"; $result = $this->construct($matchrule, $matchrule, null);
	$_12 = NULL;
	do {
		$res_10 = $result;
		$pos_10 = $this->pos;
		$_9 = NULL;
		do {
			$count = 0;
			while (true) {
				$res_8 = $result;
				$pos_8 = $this->pos;
				$matcher = 'match_'.'Number'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else {
					$result = $res_8;
					$this->pos = $pos_8;
					unset( $res_8 );
					unset( $pos_8 );
					break;
				}
				$count++;
			}
			if ($count >= 1) {  }
			else { $_9 = FALSE; break; }
			$_9 = TRUE; break;
		}
		while(0);
		if( $_9 === FALSE) {
			$result = $res_10;
			$this->pos = $pos_10;
			unset( $res_10 );
			unset( $pos_10 );
		}
		$count = 0;
		while (true) {
			$res_11 = $result;
			$pos_11 = $this->pos;
			$matcher = 'match_'.'Number'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else {
				$result = $res_11;
				$this->pos = $pos_11;
				unset( $res_11 );
				unset( $pos_11 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_12 = FALSE; break; }
		$_12 = TRUE; break;
	}
	while(0);
	if( $_12 === TRUE ) { return $this->finalise($result); }
	if( $_12 === FALSE) { return FALSE; }
}


/* Name: ( Word+ > )* Word+ */
protected $match_Name_typestack = array('Name');
function match_Name ($stack = array()) {
	$matchrule = "Name"; $result = $this->construct($matchrule, $matchrule, null);
	$_19 = NULL;
	do {
		while (true) {
			$res_17 = $result;
			$pos_17 = $this->pos;
			$_16 = NULL;
			do {
				$count = 0;
				while (true) {
					$res_14 = $result;
					$pos_14 = $this->pos;
					$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else {
						$result = $res_14;
						$this->pos = $pos_14;
						unset( $res_14 );
						unset( $pos_14 );
						break;
					}
					$count++;
				}
				if ($count >= 1) {  }
				else { $_16 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
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
		$count = 0;
		while (true) {
			$res_18 = $result;
			$pos_18 = $this->pos;
			$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else {
				$result = $res_18;
				$this->pos = $pos_18;
				unset( $res_18 );
				unset( $pos_18 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_19 = FALSE; break; }
		$_19 = TRUE; break;
	}
	while(0);
	if( $_19 === TRUE ) { return $this->finalise($result); }
	if( $_19 === FALSE) { return FALSE; }
}


/* Operator: '>' | '<' | '>=' | '<=' | '~'  */
protected $match_Operator_typestack = array('Operator');
function match_Operator ($stack = array()) {
	$matchrule = "Operator"; $result = $this->construct($matchrule, $matchrule, null);
	$_36 = NULL;
	do {
		$res_21 = $result;
		$pos_21 = $this->pos;
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
			$_36 = TRUE; break;
		}
		$result = $res_21;
		$this->pos = $pos_21;
		$_34 = NULL;
		do {
			$res_23 = $result;
			$pos_23 = $this->pos;
			if (substr($this->string,$this->pos,1) == '<') {
				$this->pos += 1;
				$result["text"] .= '<';
				$_34 = TRUE; break;
			}
			$result = $res_23;
			$this->pos = $pos_23;
			$_32 = NULL;
			do {
				$res_25 = $result;
				$pos_25 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_32 = TRUE; break;
				}
				$result = $res_25;
				$this->pos = $pos_25;
				$_30 = NULL;
				do {
					$res_27 = $result;
					$pos_27 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_30 = TRUE; break;
					}
					$result = $res_27;
					$this->pos = $pos_27;
					if (substr($this->string,$this->pos,1) == '~') {
						$this->pos += 1;
						$result["text"] .= '~';
						$_30 = TRUE; break;
					}
					$result = $res_27;
					$this->pos = $pos_27;
					$_30 = FALSE; break;
				}
				while(0);
				if( $_30 === TRUE ) { $_32 = TRUE; break; }
				$result = $res_25;
				$this->pos = $pos_25;
				$_32 = FALSE; break;
			}
			while(0);
			if( $_32 === TRUE ) { $_34 = TRUE; break; }
			$result = $res_23;
			$this->pos = $pos_23;
			$_34 = FALSE; break;
		}
		while(0);
		if( $_34 === TRUE ) { $_36 = TRUE; break; }
		$result = $res_21;
		$this->pos = $pos_21;
		$_36 = FALSE; break;
	}
	while(0);
	if( $_36 === TRUE ) { return $this->finalise($result); }
	if( $_36 === FALSE) { return FALSE; }
}


/* Browser: Name */
protected $match_Browser_typestack = array('Browser');
function match_Browser ($stack = array()) {
	$matchrule = "Browser"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres );
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Platform: '{' > Name > '}' */
protected $match_Platform_typestack = array('Platform');
function match_Platform ($stack = array()) {
	$matchrule = "Platform"; $result = $this->construct($matchrule, $matchrule, null);
	$_44 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_44 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_44 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_44 = FALSE; break; }
		$_44 = TRUE; break;
	}
	while(0);
	if( $_44 === TRUE ) { return $this->finalise($result); }
	if( $_44 === FALSE) { return FALSE; }
}


/* Device: '(' > Name > ')' */
protected $match_Device_typestack = array('Device');
function match_Device ($stack = array()) {
	$matchrule = "Device"; $result = $this->construct($matchrule, $matchrule, null);
	$_51 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_51 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_51 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_51 = FALSE; break; }
		$_51 = TRUE; break;
	}
	while(0);
	if( $_51 === TRUE ) { return $this->finalise($result); }
	if( $_51 === FALSE) { return FALSE; }
}


/* VersionMatcher: Operator > VersionNumber */
protected $match_VersionMatcher_typestack = array('VersionMatcher');
function match_VersionMatcher ($stack = array()) {
	$matchrule = "VersionMatcher"; $result = $this->construct($matchrule, $matchrule, null);
	$_56 = NULL;
	do {
		$matcher = 'match_'.'Operator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_56 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'VersionNumber'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_56 = FALSE; break; }
		$_56 = TRUE; break;
	}
	while(0);
	if( $_56 === TRUE ) { return $this->finalise($result); }
	if( $_56 === FALSE) { return FALSE; }
}


/* VersionBetweenMatcher: VersionNumber > '~' > VersionNumber */
protected $match_VersionBetweenMatcher_typestack = array('VersionBetweenMatcher');
function match_VersionBetweenMatcher ($stack = array()) {
	$matchrule = "VersionBetweenMatcher"; $result = $this->construct($matchrule, $matchrule, null);
	$_63 = NULL;
	do {
		$matcher = 'match_'.'VersionNumber'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_63 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '~') {
			$this->pos += 1;
			$result["text"] .= '~';
		}
		else { $_63 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'VersionNumber'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_63 = FALSE; break; }
		$_63 = TRUE; break;
	}
	while(0);
	if( $_63 === TRUE ) { return $this->finalise($result); }
	if( $_63 === FALSE) { return FALSE; }
}


/* VersionOp: VersionMatcher | VersionBetweenMatcher */
protected $match_VersionOp_typestack = array('VersionOp');
function match_VersionOp ($stack = array()) {
	$matchrule = "VersionOp"; $result = $this->construct($matchrule, $matchrule, null);
	$_68 = NULL;
	do {
		$res_65 = $result;
		$pos_65 = $this->pos;
		$matcher = 'match_'.'VersionMatcher'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_68 = TRUE; break;
		}
		$result = $res_65;
		$this->pos = $pos_65;
		$matcher = 'match_'.'VersionBetweenMatcher'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_68 = TRUE; break;
		}
		$result = $res_65;
		$this->pos = $pos_65;
		$_68 = FALSE; break;
	}
	while(0);
	if( $_68 === TRUE ) { return $this->finalise($result); }
	if( $_68 === FALSE) { return FALSE; }
}


/* Version: '[' > (VersionOp > ',' > )* VersionOp ']' */
protected $match_Version_typestack = array('Version');
function match_Version ($stack = array()) {
	$matchrule = "Version"; $result = $this->construct($matchrule, $matchrule, null);
	$_80 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_80 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		while (true) {
			$res_77 = $result;
			$pos_77 = $this->pos;
			$_76 = NULL;
			do {
				$matcher = 'match_'.'VersionOp'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_76 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_76 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$_76 = TRUE; break;
			}
			while(0);
			if( $_76 === FALSE) {
				$result = $res_77;
				$this->pos = $pos_77;
				unset( $res_77 );
				unset( $pos_77 );
				break;
			}
		}
		$matcher = 'match_'.'VersionOp'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_80 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_80 = FALSE; break; }
		$_80 = TRUE; break;
	}
	while(0);
	if( $_80 === TRUE ) { return $this->finalise($result); }
	if( $_80 === FALSE) { return FALSE; }
}


/* Expr: Browser (> Playtform)? (> Device)? (> Version)? */
protected $match_Expr_typestack = array('Expr');
function match_Expr ($stack = array()) {
	$matchrule = "Expr"; $result = $this->construct($matchrule, $matchrule, null);
	$_95 = NULL;
	do {
		$matcher = 'match_'.'Browser'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_95 = FALSE; break; }
		$res_86 = $result;
		$pos_86 = $this->pos;
		$_85 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Playtform'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_85 = FALSE; break; }
			$_85 = TRUE; break;
		}
		while(0);
		if( $_85 === FALSE) {
			$result = $res_86;
			$this->pos = $pos_86;
			unset( $res_86 );
			unset( $pos_86 );
		}
		$res_90 = $result;
		$pos_90 = $this->pos;
		$_89 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Device'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_89 = FALSE; break; }
			$_89 = TRUE; break;
		}
		while(0);
		if( $_89 === FALSE) {
			$result = $res_90;
			$this->pos = $pos_90;
			unset( $res_90 );
			unset( $pos_90 );
		}
		$res_94 = $result;
		$pos_94 = $this->pos;
		$_93 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Version'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_93 = FALSE; break; }
			$_93 = TRUE; break;
		}
		while(0);
		if( $_93 === FALSE) {
			$result = $res_94;
			$this->pos = $pos_94;
			unset( $res_94 );
			unset( $pos_94 );
		}
		$_95 = TRUE; break;
	}
	while(0);
	if( $_95 === TRUE ) { return $this->finalise($result); }
	if( $_95 === FALSE) { return FALSE; }
}



}
