<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The builder for building where arguments
 *
 * @author Jack
 * @version 1.1
 * @date Fri Jun 19 17:53:02 2015
 */
class WhereBuilder {

	const PASS = '__yield__';

	public function __construct($model, $args = null, $oper = ' and ') {
		$this->model = $model;
		$this->args = array();
		$this->ops = array();
		if($args) {
			return $this->expression($args, $oper);
		}
	}

	public function expression($arr, $oper = ' and ') {
		if(is_array($arr) || is_object($arr)) {
			$where = array();
			foreach($arr as $k => $v) {
				if(strpos($k, '?') !== false) { // It has alreay has the question mark
					if(is_array($v)) { 
						if(substr_count($k, '?') < count($v)) { // The query is like 'length(?)' => array('name', 3)
							$value = $v[count($v) -1];
							if($value == null) {
								$where []= '('.$k.' is null)';
								array_pop($v); // Pop the null out
							}
							else {
								$where []= '('.$k.' = ?)';
							}
							$this->args = array_merge($this->args, $v);
						}
						else { // The query like 'date between ? and ?' => array()
							$where []= '('.$k.')';
							$this-> args = array_merge($this->args, $v);
						}
					}
					else { // The query like 'name like ?' => 'test'
						$where []= '('.$k.')';
						$this->args []= $v;
					}
				}
				else {
					if($v == self::PASS) {
						$where []= '('.$k.')';
					}
					else {
						if($v == null) {
							$where []= '('.$k.' is null)';
						}
						else {
							$where []= '('.$k.' = ?)';
							$this->args []= $v;
						}
					}
				}
			}
			$this->ops []= '('.implode($oper, $where).')';
		}
		return $this;
	}

	public function wand($args) {
		return $this->expression($args);
	}

	public function wor($args) {
		return $this->expression($args, ' or ');
	}

	public function compile() {
		if($this->ops) {
			$this->model->where []= implode(' and ', $this->ops);
			$this->model->args = array_merge($this->model->args, $this->args);
			$this->ops = array();
			$this->args = array();
			return $this->model;
		}
		return array();
	}
}
