<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Libraries\PropertyQuery;
use Clips\Libraries\ObjectQuery;
use Clips\Interfaces\TreeNode;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * The utitlity for query php collections
 *
 * @author Jack
 * @date Sun Mar  1 12:28:43 2015
 *
 * @Clips\Library("Cache")
 */
class Searcher implements LoggerAwareInterface {
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function parsePropertyQuery($query) {
		if($this->cache->has($query))
			return $this->cache->get($query);
		$q = new PropertyQuery($query);
		$r =  $q->match_Expr();
		if($r) {
			$this->cache->put($query, $r);
		}
		else
			$this->cache->put($query, false);
		return $r;
	}

	/**
	 * Create and parse the object query
	 */
	public function parseObjectQuery($query) {
		if($this->cache->has($query))
			return $this->cache->get($query);
		$q = new ObjectQuery($query);
		$r =  $q->match_Expr();
		if($r) {
			$this->cache->put($query, $r);
		}
		else
			$this->cache->put($query, false);
		return $r;
	}

	protected function processAlias($value, $alias) {
		return get_default($alias, str_replace('$', '', $value));
	}

	protected function val($value, $alias) {
		if(strpos($value, '$') !== false) {
			return $this->processAlias($value, $alias);
		}
		return $value;
	}

	public function matches($selectors, $obj, &$args, $alias) {
		if(is_array($selectors) && $selectors) {
			if(isset($selectors['type'])) {
				// This is selector
				$conditions = get_default($selectors, 'conditions', array());
				$type = $this->val($selectors['type'], $alias);
				$failed = false;

				// Test for type
				if($type != '*' && !valid_obj($obj, $type)) {
					$failed = true;
				}

				// Test for conditions
				foreach($conditions as $condition) {
					$var = $this->val($condition['var'], $alias);
					$val = $this->val($condition['val'], $alias);

					if($val == '?') { // The value is prepare parameter
						$val = array_shift($args);
					}

					$op = $condition['op'];

					$obj_val = get_default($obj, $var);
					if(!$obj_val) { // If object don't have this attribute
						$failed = true;
					}


					switch($op) {
					case '>':
						if($obj_val <= $val)
							$failed = true;
						break;
					case '>=':
						if($obj_val < $val)
							$failed = true;
						break;
					case '<':
						if($obj_val >= $val)
							$failed = true;
						break;
					case '<=':
						if($obj_val > $val)
							$failed = true;
						break;
					case '=':
						if($val != $obj_val)
							$failed = true;
						break;
					case '~=':
						if(preg_match('/'.$val.'/', $obj_val) === false)
							$failed = true;
						break;
					case '!=':
						if($val != $obj_val)
							$failed = true;
						break;
					case 'like':
						if(preg_match('/'.str_replace('%', '.*', $val).'/', $obj_val) === false)
							$failed = true;
						break;
					case 'not like':
						if(preg_match('/'.str_replace('%', '.*', $val).'/', $obj_val) !== false)
							$failed = true;
						break;
					}
				}

				if($failed)
					return false;

				// Return the object by default
				return $obj;
			}
			else {
				$ret = null;
				$args_copy = copy_arr($args);
				foreach($selectors as $selector) {
					$match = $this->matches($selector, $obj, $args_copy, $alias);
					if($match)
						$ret = $obj;
				}
				return $ret;
			}
		}
	}

	protected function matchCollection($selectors, $collection, $args, $alias) {
		$ret = array();
		foreach($collection as $obj) {
			if($this->matches($selectors, $obj, $args, $alias)) {
				$ret []= $obj;
			}
		}
		return $ret;
	}

	/**
	 * Query the collection using the args and aliases
	 *
	 * @param query
	 * 		The query string
	 * @param collection
	 * 		The collection to query
	 * @param args
	 * 		The args of the query
	 * @param alias
	 * 		The aliases
	 * @return
	 * 		The result
	 */
	public function search($query, $collection, $args = array(), $alias = array()) {
		$result = $this->parseObjectQuery($query);
		if($result && is_array($collection) && $collection) {
			$argc = get_default($result, 'args');
			if($argc != count($args)) {
				throw new Exception('Argument count '.$argc.' is not equals parameter count '.count($args).'!');
				return array();
			}
			return $this->matchCollection($result['expr']['layers'][0]
				['selectors'], $collection, $args, $alias);
		}
		return array();
	}

	protected function matchChildren($layers, TreeNode $node, $args = array(), $alias = array()) {
		$children = $node->children();
		if($children && $layers) {
			$selectors = array_shift($layers);
			$argc = get_default($selectors, 'args', 0);
			if($argc)
				$layer_args = array_slice($args, 0, $argc);
			else
				$layer_args = array();

			$result = $this->matchCollection($selectors, $children, $layer_args, $alias);
			$ret = array();
			if($result) {
				foreach($result as $child) {
					array_merge($ret, $this->matchChildren($layers, $child, $args, $alias));
				}
			}
			return $ret;
		}
		return array();
	}

	protected function propertyGet($oper, $obj) {
		if(is_string($oper)) {
			if($oper == '*') {
				// This is for wildcard
				return array_values((array) $obj);
			}
			else {
				// This is for property
				return array(get_default($obj, $oper));
			}
		}
		else {
			if(is_array($oper)) {
				switch($oper['name']) {
				case 'ObjOper':
					return $this->propertyGet($oper['property'], $obj);
				case 'ArrOper':
					$index = $oper['index'];
					if($index == '*')
						return array_values((array) $obj);
					return array(get_default($obj, $index));
				}
			}
		}
		return null;
	}

	public function property($query, $obj) {
		return $this->propertySearch($query, $obj);
	}

	public function propertySearch($query, $obj) {
		$result = $this->parsePropertyQuery($query);
		if($result) {
			$expr = $result['expr'];
			$prop = get_default($expr, 'property');
			$opers = get_default($expr, 'opers', array());
			if($prop) {
				$objs = $this->propertyGet($prop, $obj);
				while(true) {
					if($opers && $objs) {
						$arr = array();
						$oper = array_shift($opers);
						// If we still have opers and object to test
						foreach($objs as $o) {
							$arr = array_merge($arr, $this->propertyGet($oper,$o));
						}
						$objs = $arr;
					}
					else {
						break;
					}
				}
				if(count($objs) == 1)
					return $objs[0];
				return $objs;
			}
			return $ret;
		}
		return null;
	}

	/**
	 * Searching using the tree
	 */
	public function treeSearch($query, TreeNode $node, $args = array(), $alias = array()) {
		$result = $this->parseObjectQuery($query);
		if($result && valid_obj($node, 'Clips\\Interfaces\\TreeNode')) {
			$layers = $result['expr']['layers'];
			return $this->matchChildren($layers, $node, $args, $alias);
		}
		return array();
	}
}
