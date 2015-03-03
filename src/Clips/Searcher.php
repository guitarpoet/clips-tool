<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

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

	/**
	 * Create and parse the query
	 */
	public function parseQuery($query) {
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
		$result = $this->parseQuery($query);
		if($result && is_array($collection) && $collection) {
			$argc = get_default($result, 'args');
			if($argc != count($args)) {
				throw new Exception('Argument count '.$argc.' is not equals parameter count '.count($args).'!');
			}
			return $this->matchCollection($result['expr']['layers'][0]
				['selectors'], $collection, $args, $alias);
		}
		return array();
	}

	/**
	 * Searching using the tree
	 */
	public function treeSearch($query, TreeNode $node, $args = array(), $alias = array()) {
		$result = $this->parseQuery($query);
		if($result && valid_obj($node, 'Clips\\Interfaces\\TreeNode')) {
			$layers = $result['expr']['layers'];
			$found = false;
			while(true) {
				$args_copy = copy_arr($args);
				$children = $node->children();
				$layer = array_shift($layers);

				$match = $this->matchCollection($layer['selectors'], $children, $args, $alias);
				foreach($selectors as $selector) {
					if($select['type'] == '**') {
						// This is wildcard
					}
				}
			}
		}
	}
}
