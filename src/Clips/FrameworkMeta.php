<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed"); 

/**
 * This is the frame work meta object. Contains all the metadata about the framework
 *
 * @author Jack
 * @date Sun Mar  8 18:59:17 2015
 *
 * @Clips\Library({"git", "sass", "repository"})
 */
class FrameworkMeta extends BaseService {

	protected function doInit() {
		$this->commit = $this->repository->getHeadCommit()->getShortHash();
		$this->branch = $this->repository->getBranch();
		$this->lastCommitterDate = $this->repository->lastCommitterDate();
	}
}
