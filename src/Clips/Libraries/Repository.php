<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This is the repository facade for store files in repository
 *
 * @Requires({"git"})
 */
class Repository implements \Psr\Log\LoggerAwareInterface, 
	\Clips\Interfaces\Initializable {

	public function __construct($path = null, $readonly = true) {
		if($path)
			$this->path = $path;
		else
			$this->path = clips_path('/../../'); // If no path is given, let read clips tool's git

		$this->readonly = $readonly;
	}

	/**
	 * Get the current revisions of the given path, if no path is set, will return all of the revisions of this repo.
	 *
	 * @return
	 * 		All the revisions
	 */
	public function revisions($path = null) {
		if(!isset($this->gitrepo))
			return false;

		$ret = array();
		foreach($this->gitrepo->getLog(null, $path) as $r) {
			$ret []= $r->getRevision();
		}
		return $ret;
	}

	public function logs($path = null, $revision = null) {
		if(!isset($this->gitrepo))
			return false;

		return $this->gitrepo->getLog($revision, $path);
	}

	public function init() {
		$path = path_join($this->path, '.git');
		if(file_exists($path))
			$this->gitrepo = $this->git->repo($path);
	}

	public function lastCommitterDate() {
		if(!isset($this->gitrepo))
			return false;

		return $this->git->getHeadCommit($this->gitrepo)->getCommitterDate();
	}

	public function save($path, $content) {
	}

	public function remove() {
		if($readonly || is_writable($this->path))
			return false;
		
		return rmdir($this->path);
	}

	public function commit($message, $author) {
		if($this->readonly || !isset($this->gitrepo))
			return false;
	}

	public function show($path, $revision = null) {
		if(!isset($this->gitrepo))
			return false;

		if($this->exists($path, $revision)) {
			if(!$revision)
				$revision = 'HEAD';
			return $this->gitrepo->run('show', array($revision.':'.$path));
		}
		return false;
	}

	public function create() {
		if($this->readonly || isset($this->gitrepo))
			return false;

		$this->git->create($this->path);
		$this->init();
		return $this;
	}

	public function exists($path, $revision = null) {
		if(!isset($this->gitrepo))
			return file_exists($this->path.'/'. $path);
		$r = $this->git->getRevision($this->gitrepo, $revision);
		return $r->getLog($path)->count() > 0;
	}

	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}
}
