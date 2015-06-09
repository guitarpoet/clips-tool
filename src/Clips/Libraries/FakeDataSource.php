<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\DataSourceException;

/**
 * The fake datasource for testing.
 *
 * @author Jack
 * @date Tue Jun  9 16:31:28 2015
 */
class FakeDataSource extends DataSource {

	public function __construct() {
		$this->type = 'mysqli';
		parent::__construct('mysqli');
	}

	protected function doQuery($query, $args = array()) {
		if($this->handler) {
			return $this->handler->doQuery($query, $args);
		}
		return null;
	}

	protected function doUpdate($id, $args) {
		if($this->handler) {
			return $this->handler->doUpdate($id, $args);
		}
		return null;
	}

	protected function doDelete($id) {
		if($this->handler) {
			return $this->handler->doDelete($id);
		}
		return null;
	}

	protected function doFetch($args) {
		if($this->handler) {
			return $this->handler->doFetch($args);
		}
		return null;
	}

	protected function doClear() {
		if($this->handler) {
			return $this->handler->doClear();
		}
		return null;
	}

	protected function doIterate($query, $args, $callback, $context = array()) {
		throw new DataSourceException('We don\'t think you should need this!');
	}

	protected function doInsert($args) {
		if($this->handler) {
			return $this->handler->doInsert($args);
		}
		return null;
	}
}
