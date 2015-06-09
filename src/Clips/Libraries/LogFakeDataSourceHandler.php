<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\FakeDataSourceHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;


/**
 * The fake datasource handler that only print the using logging
 *
 * @author Jack
 * @date Tue Jun  9 16:50:57 2015
 * @version 1.0
 */
class LogFakeDataSourceHandler implements FakeDataSourceHandler, LoggerAwareInterface {

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function doQuery($query, $args = array()) {
		$this->logger->info('Querying DataSource using Query [{0}] and args', array($query, $args));
		return array();
	}

	public function doUpdate($id, $args) {
		$this->logger->info('Updating DataSource using id [{0}] and args', array($id, $args));
		return true;
	}

	public function doDelete($id) {
		$this->logger->info('Deleting using id [{0}]', array($id));
		return true;
	}

	public function doFetch($args) {
		$this->logger->info('Fetching using args', array($args));
		return null;
	}

	public function doClear() {
		$this->logger->info('Clearing DataSource');
	}

	public function doInsert($args) {
		$this->logger->info('Inserting using args', array($args));
		return 0;
	}

	public function expect() {
		return $this;
	}

	public function result($result) {
		return $this;
	}
}
