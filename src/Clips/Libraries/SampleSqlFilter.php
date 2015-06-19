<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\SqlFilter;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * This is the sample sql filter for all sql filters
 *
 * @author Jack
 * @date Fri Jun 19 13:59:09 2015
 * @version 1.1
 */
class SampleSqlFilter implements SqlFilter, LoggerAwareInterface {

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function filter($sql, $config) {
		$this->logger->info('Filtering sql with data', array($sql, $config));
		return $sql;
	}
}
