<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\FakeDataSourceHandler;
use Clips\Interfaces\Initializable;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Clips\DataSourceException;


/**
 * @author Jack
 * @dateTue Jun  9 17:51:21 2015 
 * @version 1.0
 */
class ExpectFakeDataSourceHandler implements FakeDataSourceHandler, LoggerAwareInterface, Initializable {

	public function init() {
		$this->expects = array();
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function doQuery($query, $args = array()) {
		if($this->expects) {
			$expect = array_shift($this->expects);
			$return = \Clips\get_default($expect, 'result', null);
			$expect = $expect['args'];
			if($expect[0] == 'query') {
				// Pop the query type
				array_shift($expect);
				array_unshift($args, $query);
				if($expect == $args) {
					return $return;
				}
				throw new DataSourceException("Query [$query] with args is not match [".print_r($expect, true)."]".print_r($args, true));
			}
			throw new DataSourceException("Query [$query] with args is not a query".print_r($args, true));
		}
		throw new DataSourceException("Not expecting this query [$query] with args ".print_r($args, true));
	}

	public function doUpdate($id, $args) {
		if($this->expects) {
			$expect = array_shift($this->expects);
			$return = \Clips\get_default($expect, 'result', null);
			$expect = $expect['args'];
			if($expect[0] == 'update') {
				// Pop the query type
				array_shift($expect);
				array_unshift($args, $id);
				if($expect == $args) {
					return $return;
				}
				throw new DataSourceException("Update id [$id] with args is not match [".print_r($expect, true)."]".print_r($args, true));
			}
			throw new DataSourceException("Update [$id] with args is not a query".print_r($args, true));
		}
		throw new DataSourceException("Not expecting this update [$id] with args ".print_r($args, true));
	}

	public function doDelete($id) {
		if($this->expects) {
			$expect = array_shift($this->expects);
			$return = \Clips\get_default($expect, 'result', null);
			$expect = $expect['args'];
			if($expect[0] == 'delete') {
				// Pop the query type
				array_shift($expect);
				if($expect == array($id)) {
					return $return;
				}
			}
			throw new DataSourceException("Delete operation for id $id is failed");
		}
		throw new DataSourceException("Not expecting this delete args $id");
	}

	public function doFetch($args) {
		if($this->expects) {
			$expect = array_shift($this->expects);
			$return = \Clips\get_default($expect, 'result', null);
			$expect = $expect['args'];
			if($expect[0] == 'fetch') {
				// Pop the query type
				array_shift($expect);
				if($expect == $args) {
					return $return;
				}
				throw new DataSourceException("Fetch with args is not match [".print_r($expect, true)."]".print_r($args, true));
			}
			throw new DataSourceException("Fetch with args is not a query".print_r($args, true));
		}
		throw new DataSourceException("Not expecting this fetch with args ".print_r($args, true));
	}

	public function doClear() {
		if($this->expects) {
			$expect = array_shift($this->expects);
			$return = \Clips\get_default($expect, 'result', null);
			$expect = $expect['args'];
			if($expect[0] == 'clear') {
				return $result;
			}
			throw new DataSourceException('Expecting a clear!');
		}
		throw new DataSourceException('Clear query is not expected!');
	}

	public function doInsert($args) {
		if($this->expects) {
			$expect = array_shift($this->expects);
			$return = \Clips\get_default($expect, 'result', null);
			$expect = $expect['args'];
			if($expect[0] == 'insert') {
				// Pop the query type
				array_shift($expect);
				if($expect == $args) {
					return $return;
				}
				throw new DataSourceException("Insert with args is not match [".print_r($expect, true)."]".print_r($args, true));
			}
			throw new DataSourceException("Insert with args is not a query".print_r($args, true));
		}
		throw new DataSourceException("Not expecting this insert with args ".print_r($args, true));
	}

	public function expect() {
		if(func_num_args() >= 2) {
			// Enstack the expects
			$this->current_expect = array('args' => func_get_args());
			$this->expects []= &$this->current_expect;
		}
		return $this;
	}

	public function result($result) {
		if($this->current_expect) {
			$this->current_expect['result'] = $result;
		}
		return $this;
	}
}
