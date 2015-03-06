<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\Action;

/**
 * The default implementation of the Action interface
 *
 * @author Jack
 * @date Thu Mar  5 19:54:32 2015
 */
class SimpleAction extends SimpleTreeNode implements Action {
	public $type;
	public $content;
	public $params = array();

	public function __construct($data = array()) {
		if(!isset($data['type']))
			$this->type = Action::SERVER;

		parent::__construct($data);
	}

	public function type() {
		return get_default($this, 'type');
	}

	public function content() {
		return get_default($this, 'content');
	}

	public function params() {
		return get_default($this, 'params', array());
	}

}
