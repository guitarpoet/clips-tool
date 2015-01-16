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
			$this->path = clips_path('/../../.git/'); // If no path is given, let read clips tool's git

		$this->readonly = $readonly;
	}

	/**
	 * Get the current revisions of the given path, if no path is set, will return all of the revisions of this repo.
	 *
	 * @return
	 */
	public function revisions($path = null) {
		return $this->gitrepo->getLog($path);
	}

	public function init() {
		$this->gitrepo = $this->git->repo($this->path);
	}

	public function lastCommitterDate() {
		return $this->git->getHeadCommit($this->gitrepo)->getCommitterDate();
	}

	public function commit($message, $author) {
	}

	public function show($path, $revision = null) {
		if(!$revision)
			$revision = 'HEAD';
		return $this->gitrepo->run('show', array($revision.':'.$path));
	}

	public function create($path) {
		if($this->readonly)
			return false;

		return null;
	}

	public function exists($path, $revision = null) {
		$r = $this->git->getRevision($this->gitrepo, $revision);
		return $r->getLog($path)->count() > 0;
	}

	public function get($path, $revision = null) {
		$r = $this->git->getRevision($this->gitrepo, $revision);
		return null;
	}

	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}
}
