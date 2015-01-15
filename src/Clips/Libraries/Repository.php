<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This is the repository facade for store files in repository
 *
 * @Requires({"git"})
 */
class Repository implements \Psr\Log\LoggerAwareInterface, 
	\Clips\Interfaces\Initializable {

	public function __construct($path = null, $readonly = false) {
		if($path)
			$this->path = $path;
		else
			$this->path = clips_path('/../../.git/', true); // If no path is given, let read clips tool's git
	}

	public function init() {
		$this->gitrepo = $this->git->repo($this->path);
	}

	public function lastCommitterDate() {
		return $this->git->getHeadCommit($this->gitrepo)->getCommitterDate();
	}

	public function create($path) {
		return null;
	}

	public function exists($path, $revision = 'HEAD') {
	}

	public function get($path) {
		return null;
	}

	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}
}
