<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

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
		if(is_object($data)) {
			$data = (array) $data;
		}

		if(!isset($data['type']))
			$this->type = Action::SERVER;

		parent::__construct($data);
	}

	protected function nodeClass() {
		return "\\Clips\\SimpleAction";
	}

	public function active() {
		if(isset($this->_active))
			return $this->_active;

		$children = $this->children();
		if($children) {
			foreach($children as $c) {
				if($c->active()) { // If children is active, then this one is active.
					$this->_active = true;
					return true;
				}
			}
		}

		if($this->type() == Action::SERVER) { // Only action is at server, can be active
			$router = context('router');
			if($router) {
				// Test if the route result of this action is equals to current uri
				$r = $router->routeResult(trim($this->content()), $this->params());
				$r = $r[0];
				if(valid_obj($r, 'Clips\\RouteResult')) {
					$controller_class = context('controller_class');
					$controller_method = context('controller_method');
					$args = context('args');
					if($controller_class == $r->controller &&
						$controller_method == $r->method) {
						if(isset($this->withArgs) && $this->withArgs) {
							if($args != $r->args) {
								log('The action is ', array($this->params, $r, $args));
								$this->_active = false;
								return false;
							}
						}
						$this->_active = true;
						return true;
					}
				}
			}
			$this->_active = false;
		}
		return false;
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
