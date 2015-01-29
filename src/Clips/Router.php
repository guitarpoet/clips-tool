<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Router implements \Psr\Log\LoggerAwareInterface {
	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}
}
