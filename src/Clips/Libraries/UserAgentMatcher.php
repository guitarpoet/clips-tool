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


/* Word: /[a-zA-Z_]+/ */
protected $match_Word_typestack = array('Word');
function match_Word ($stack = array()) {
	$matchrule = "Word"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[a-zA-Z_]+/' ) ) !== FALSE) {
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


/* VersionNumber: Number+ ('.' Number+ )? */
protected $match_VersionNumber_typestack = array('VersionNumber');
function match_VersionNumber ($stack = array()) {
	$matchrule = "VersionNumber"; $result = $this->construct($matchrule, $matchrule, null);
	$_13 = NULL;
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
		else { $_13 = FALSE; break; }
		$res_12 = $result;
		$pos_12 = $this->pos;
		$_11 = NULL;
		do {
			if (substr($this->string,$this->pos,1) == '.') {
				$this->pos += 1;
				$result["text"] .= '.';
			}
			else { $_11 = FALSE; break; }
			$count = 0;
			while (true) {
				$res_10 = $result;
				$pos_10 = $this->pos;
				$matcher = 'match_'.'Number'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else {
					$result = $res_10;
					$this->pos = $pos_10;
					unset( $res_10 );
					unset( $pos_10 );
					break;
				}
				$count++;
			}
			if ($count >= 1) {  }
			else { $_11 = FALSE; break; }
			$_11 = TRUE; break;
		}
		while(0);
		if( $_11 === FALSE) {
			$result = $res_12;
			$this->pos = $pos_12;
			unset( $res_12 );
			unset( $pos_12 );
		}
		$_13 = TRUE; break;
	}
	while(0);
	if( $_13 === TRUE ) { return $this->finalise($result); }
	if( $_13 === FALSE) { return FALSE; }
}


/* Name:  Word ( > Word ) * */
protected $match_Name_typestack = array('Name');
function match_Name ($stack = array()) {
	$matchrule = "Name"; $result = $this->construct($matchrule, $matchrule, null);
	$_20 = NULL;
	do {
		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_20 = FALSE; break; }
		while (true) {
			$res_19 = $result;
			$pos_19 = $this->pos;
			$_18 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_18 = FALSE; break; }
				$_18 = TRUE; break;
			}
			while(0);
			if( $_18 === FALSE) {
				$result = $res_19;
				$this->pos = $pos_19;
				unset( $res_19 );
				unset( $pos_19 );
				break;
			}
		}
		$_20 = TRUE; break;
	}
	while(0);
	if( $_20 === TRUE ) { return $this->finalise($result); }
	if( $_20 === FALSE) { return FALSE; }
}


/* Operator: '>=' | '<=' | '>' | '<' | '!='  */
protected $match_Operator_typestack = array('Operator');
function match_Operator ($stack = array()) {
	$matchrule = "Operator"; $result = $this->construct($matchrule, $matchrule, null);
	$_37 = NULL;
	do {
		$res_22 = $result;
		$pos_22 = $this->pos;
		if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_37 = TRUE; break;
		}
		$result = $res_22;
		$this->pos = $pos_22;
		$_35 = NULL;
		do {
			$res_24 = $result;
			$pos_24 = $this->pos;
			if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_35 = TRUE; break;
			}
			$result = $res_24;
			$this->pos = $pos_24;
			$_33 = NULL;
			do {
				$res_26 = $result;
				$pos_26 = $this->pos;
				if (substr($this->string,$this->pos,1) == '>') {
					$this->pos += 1;
					$result["text"] .= '>';
					$_33 = TRUE; break;
				}
				$result = $res_26;
				$this->pos = $pos_26;
				$_31 = NULL;
				do {
					$res_28 = $result;
					$pos_28 = $this->pos;
					if (substr($this->string,$this->pos,1) == '<') {
						$this->pos += 1;
						$result["text"] .= '<';
						$_31 = TRUE; break;
					}
					$result = $res_28;
					$this->pos = $pos_28;
					if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_31 = TRUE; break;
					}
					$result = $res_28;
					$this->pos = $pos_28;
					$_31 = FALSE; break;
				}
				while(0);
				if( $_31 === TRUE ) { $_33 = TRUE; break; }
				$result = $res_26;
				$this->pos = $pos_26;
				$_33 = FALSE; break;
			}
			while(0);
			if( $_33 === TRUE ) { $_35 = TRUE; break; }
			$result = $res_24;
			$this->pos = $pos_24;
			$_35 = FALSE; break;
		}
		while(0);
		if( $_35 === TRUE ) { $_37 = TRUE; break; }
		$result = $res_22;
		$this->pos = $pos_22;
		$_37 = FALSE; break;
	}
	while(0);
	if( $_37 === TRUE ) { return $this->finalise($result); }
	if( $_37 === FALSE) { return FALSE; }
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

public function Browser_Name (&$result, $sub) {
		$result['browser'] = $sub['text'];
	}

/* Platform: '{' > Name > '}' */
protected $match_Platform_typestack = array('Platform');
function match_Platform ($stack = array()) {
	$matchrule = "Platform"; $result = $this->construct($matchrule, $matchrule, null);
	$_45 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_45 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_45 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_45 = FALSE; break; }
		$_45 = TRUE; break;
	}
	while(0);
	if( $_45 === TRUE ) { return $this->finalise($result); }
	if( $_45 === FALSE) { return FALSE; }
}

public function Platform_Name (&$result, $sub) {
		$result['platform'] = $sub['text'];
	}

/* Device: '(' > Name > ')' */
protected $match_Device_typestack = array('Device');
function match_Device ($stack = array()) {
	$matchrule = "Device"; $result = $this->construct($matchrule, $matchrule, null);
	$_52 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_52 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Name'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_52 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_52 = FALSE; break; }
		$_52 = TRUE; break;
	}
	while(0);
	if( $_52 === TRUE ) { return $this->finalise($result); }
	if( $_52 === FALSE) { return FALSE; }
}

public function Device_Name (&$result, $sub) {
		$result['device'] = $sub['text'];
	}

/* VersionMatcher: Operator > VersionNumber */
protected $match_VersionMatcher_typestack = array('VersionMatcher');
function match_VersionMatcher ($stack = array()) {
	$matchrule = "VersionMatcher"; $result = $this->construct($matchrule, $matchrule, null);
	$_57 = NULL;
	do {
		$matcher = 'match_'.'Operator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_57 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'VersionNumber'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_57 = FALSE; break; }
		$_57 = TRUE; break;
	}
	while(0);
	if( $_57 === TRUE ) { return $this->finalise($result); }
	if( $_57 === FALSE) { return FALSE; }
}

public function VersionMatcher_Operator (&$result, $sub) {
		$result['operator'] = $sub['text'];
	}

public function VersionMatcher_VersionNumber (&$result, $sub) {
		$result['version'] = $sub['text'];
	}

/* VersionBetweenMatcher: VersionNumber > '~' > VersionNumber */
protected $match_VersionBetweenMatcher_typestack = array('VersionBetweenMatcher');
function match_VersionBetweenMatcher ($stack = array()) {
	$matchrule = "VersionBetweenMatcher"; $result = $this->construct($matchrule, $matchrule, null);
	$_64 = NULL;
	do {
		$matcher = 'match_'.'VersionNumber'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_64 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '~') {
			$this->pos += 1;
			$result["text"] .= '~';
		}
		else { $_64 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'VersionNumber'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_64 = FALSE; break; }
		$_64 = TRUE; break;
	}
	while(0);
	if( $_64 === TRUE ) { return $this->finalise($result); }
	if( $_64 === FALSE) { return FALSE; }
}

public function VersionBetweenMatcher_VersionNumber (&$result, $sub) {
		if(!isset($result['version'])) {
			$result['version'] = array();
		}
		$result['version'] []= $sub['text'];
	}

/* VersionOp: VersionMatcher | VersionBetweenMatcher | VersionNumber */
protected $match_VersionOp_typestack = array('VersionOp');
function match_VersionOp ($stack = array()) {
	$matchrule = "VersionOp"; $result = $this->construct($matchrule, $matchrule, null);
	$_73 = NULL;
	do {
		$res_66 = $result;
		$pos_66 = $this->pos;
		$matcher = 'match_'.'VersionMatcher'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_73 = TRUE; break;
		}
		$result = $res_66;
		$this->pos = $pos_66;
		$_71 = NULL;
		do {
			$res_68 = $result;
			$pos_68 = $this->pos;
			$matcher = 'match_'.'VersionBetweenMatcher'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_71 = TRUE; break;
			}
			$result = $res_68;
			$this->pos = $pos_68;
			$matcher = 'match_'.'VersionNumber'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres );
				$_71 = TRUE; break;
			}
			$result = $res_68;
			$this->pos = $pos_68;
			$_71 = FALSE; break;
		}
		while(0);
		if( $_71 === TRUE ) { $_73 = TRUE; break; }
		$result = $res_66;
		$this->pos = $pos_66;
		$_73 = FALSE; break;
	}
	while(0);
	if( $_73 === TRUE ) { return $this->finalise($result); }
	if( $_73 === FALSE) { return FALSE; }
}

public function VersionOp_VersionNumber (&$result, $sub) {
		$result['type'] = 'version';
		$result['version'] = $sub['text'];
	}

public function VersionOp_VersionMatcher (&$result, $sub) {
		$result['type'] = 'matcher';
		$result['version'] = $sub['version'];
		$result['operator'] = $sub['operator'];
	}

public function VersionOp_VersionBetweenMatcher (&$result, $sub) {
		$result['type'] = 'between';
		$result['versions'] = $sub['version'];
	}

/* Version: '[' > VersionOp ( > ',' > VersionOp )* > ']' */
protected $match_Version_typestack = array('Version');
function match_Version ($stack = array()) {
	$matchrule = "Version"; $result = $this->construct($matchrule, $matchrule, null);
	$_86 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_86 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'VersionOp'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_86 = FALSE; break; }
		while (true) {
			$res_83 = $result;
			$pos_83 = $this->pos;
			$_82 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_82 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'VersionOp'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_82 = FALSE; break; }
				$_82 = TRUE; break;
			}
			while(0);
			if( $_82 === FALSE) {
				$result = $res_83;
				$this->pos = $pos_83;
				unset( $res_83 );
				unset( $pos_83 );
				break;
			}
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_86 = FALSE; break; }
		$_86 = TRUE; break;
	}
	while(0);
	if( $_86 === TRUE ) { return $this->finalise($result); }
	if( $_86 === FALSE) { return FALSE; }
}

public function Version_VersionOp (&$result, $sub) {
		if(!isset($result['op'])) {
			$result['op'] = array();
		}
		$result['op'] []= $sub;
	}

/* Expr: Browser (> Platform)? (> Device)? (> Version)? */
protected $match_Expr_typestack = array('Expr');
function match_Expr ($stack = array()) {
	$matchrule = "Expr"; $result = $this->construct($matchrule, $matchrule, null);
	$_101 = NULL;
	do {
		$matcher = 'match_'.'Browser'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_101 = FALSE; break; }
		$res_92 = $result;
		$pos_92 = $this->pos;
		$_91 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Platform'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_91 = FALSE; break; }
			$_91 = TRUE; break;
		}
		while(0);
		if( $_91 === FALSE) {
			$result = $res_92;
			$this->pos = $pos_92;
			unset( $res_92 );
			unset( $pos_92 );
		}
		$res_96 = $result;
		$pos_96 = $this->pos;
		$_95 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Device'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_95 = FALSE; break; }
			$_95 = TRUE; break;
		}
		while(0);
		if( $_95 === FALSE) {
			$result = $res_96;
			$this->pos = $pos_96;
			unset( $res_96 );
			unset( $pos_96 );
		}
		$res_100 = $result;
		$pos_100 = $this->pos;
		$_99 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Version'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_99 = FALSE; break; }
			$_99 = TRUE; break;
		}
		while(0);
		if( $_99 === FALSE) {
			$result = $res_100;
			$this->pos = $pos_100;
			unset( $res_100 );
			unset( $pos_100 );
		}
		$_101 = TRUE; break;
	}
	while(0);
	if( $_101 === TRUE ) { return $this->finalise($result); }
	if( $_101 === FALSE) { return FALSE; }
}

public function Expr_Browser (&$result, $sub) {
		$result['browser'] = $sub['browser'];
	}

public function Expr_Platform (&$result, $sub) {
		$result['platform'] = $sub['platform'];
	}

public function Expr_Device (&$result, $sub) {
		$result['device'] = $sub['device'];
	}

public function Expr_Version (&$result, $sub) {
		$result['version'] = $sub;
	}


}
