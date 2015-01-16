<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Git implements \Psr\Log\LoggerAwareInterface {
	public function repo($path) {
		return new \Gitonomy\Git\Repository($path, array('logger' => $this->logger));
	}

	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function getRevision($repo, $rev = null) {
		if($rev)
			return $repo->getRevision($repo, $rev);

		return $this->getHeadRevision($repo);
	}

	public function getHeadRevision($repo) {
		return ($repo->getRevision($repo->getHead()->getFullname()));
	}

	public function getHeadCommit($repo) {
		return $this->getHeadRevision($repo)->getCommit();
	}
}
