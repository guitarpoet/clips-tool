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

	public function matches($selectors, $obj, $args, $alias) {
		if(is_array($selectors) && $selectors) {
			if(isset($selectors['conditions'])) {
				// This is selector
				$conditions = $selectors['conditions'];
				$type = $this->val($selectors['type']);

				// Test for type
				if(!valid_obj($obj, $type)) {
					return false;
				}

				// Test for conditions
				$i = 0;
				foreach($conditions as $condition) {
					$var = $this->val($condition['var']);
					$val = $this->val($condition['val']);

					if($val == '?') { // The value is prepare parameter
						$val = $arg[$i];
						$i++;
					}

					$op = $condition['op'];

					$obj_val = get_default($obj, $var);
					if(!$obj_var) { // If object don't have this attribute
						return false;
					}

					switch($op) {
					case '>':
						if($val <= $obj_val)
							return false;
						break;
					case '>=':
						if($val < $obj_val)
							return false;
						break;
					case '<':
						if($val >= $obj_val)
							return false;
						break;
					case '<=':
						if($val > $obj_val)
							return false;
						break;
					case '=':
						if($val != $obj_val)
							return false;
						break;
					case '~=':
						if(preg_match('/'.$val.'/', $obj_val) === false)
							return false;
						break;
					case '!=':
						if($val == $obj_val)
							return false;
						break;
					case 'like':
						if(preg_match('/'.str_replace('%', '.*', $val).'/', $obj_val) === false)
							return false;
						break;
					case 'not like':
						if(preg_match('/'.str_replace('%', '.*', $val).'/', $obj_val) !== false)
							return false;
						break;
					}
				}
			}
			else {
				foreach($selectors as $selector) {
					$match = $this->matches($selector, $obj);
					if($match)
						return $obj;
				}
			}
		}
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
			$layer = $result['expr']['layers'][0]; // Only use first layer
			$selectors = $layer['selectors'];
			$ret = array();
			foreach($collection as $obj) {
				if($this->matches($selectors, $obj)) {
					$ret []= $obj;
				}
			}
			return $ret;
		}
		return array();
	}

	/**
	 * Searching using the tree
	 */
	public function treeSearch($query, TreeNode $node, $args = array(), $alias = array()) {
	}
}
