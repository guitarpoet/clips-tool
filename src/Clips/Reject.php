<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed"); 

/**
 * The security engine output object.  This object has 2 fields.
 *
 * Reason is the reason for this reject.
 * State is only for form, since form have (readonly, hidden and none 3 kind
 * of reject).
 *
 * @author Jack
 * @date Sat Mar  7 15:13:55 2015
 */
class Reject {
	public $reason;
	public $state;
	public $cause;
}
