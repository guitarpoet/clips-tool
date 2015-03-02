<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * The utitlity for query php collections
 *
 * @author Jack
 * @date Sun Mar  1 12:28:43 2015
 */
class Searcher implements LoggerAwareInterface {
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
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
	}
}
