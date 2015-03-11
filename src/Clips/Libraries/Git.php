<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * The git operation wrapper
 *
 * @author Jack
 * @date Sat Feb 21 12:43:56 2015
 */
class Git implements LoggerAwareInterface {

	/**
	 * Execute the git command
	 */
	protected function exec($repo, $command, $args) {
		$cmd = array();
		$cmd []= 'cd';
		$cmd []= $repo->path;
		$cmd []= '&&';
		$cmd []= 'git';
		$cmd []= $command;
		$cmd []= implode(' ', $args);
		return exec(implode(' ', $cmd));
	}

	public function branch($repo) {
		return $this->exec($repo, 'symbolic-ref', array('--short', 'HEAD'));
	}

	/**
	 * Create the git repository support
	 */
	public function repo($path) {
		return new \Gitonomy\Git\Repository($path, array('logger' => $this->logger));
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function add($repo, $path) {
		if(file_exists($repo->path)) {
			$this->exec($repo, 'add', array($path));
			return true;
		}
		return false;
	}

	public function has($repo, $path) {
		return !!$this->exec($repo, 'log', array($path));
	}

	public function commit($repo, $message, $author) {
		if(file_exists($repo->path)) {
			exec('cd '.$repo->path.' && git commit -am "'.$message.'" --author "'.$author.'"');
			return true;
		}
		return false;

	}

	public function reset($repo) {
		if(file_exists($repo->path)) {
			exec('cd '.$repo->path.' && git reset --hard');
			return true;
		}
		return false;

	}

	public function create($path) {
		if(file_exists($path)) {
			exec('git init '.$path);
			return true;
		}
		return false;
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
		return $repo->getHeadCommit();
	}
}
