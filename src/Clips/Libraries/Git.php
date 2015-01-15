<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Git implements \Psr\Log\LoggerAwareInterface {
	public function repo($path) {
		return new \Gitonomy\Git\Repository($path, array('logger' => $this->logger));
	}

	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function getHeadCommit($repo) {
		$head = $repo->getHead();
		$rev = ($repo->getRevision($head->getFullname()));
		return $rev->getCommit();
	}
}
