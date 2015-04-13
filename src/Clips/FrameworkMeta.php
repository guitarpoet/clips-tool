<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed"); 

/**
 * This is the frame work meta object. Contains all the metadata about the framework
 *
 * @author Jack
 * @date Sun Mar  8 18:59:17 2015
 *
 */
class FrameworkMeta extends BaseService {
	protected function doInit() {
		$meta = config('framework_meta');
		if($meta) {
			$meta = $meta[0];
			copy_object($meta, $this);
		}
		else {
			$this->repository = $this->tool->library('repository');
			$this->git = $this->tool->library('git');
			$this->commit = $this->repository->getHeadCommit()->getShortHash();
			$this->branch = $this->repository->getBranch();
			$this->lastCommitterDate = $this->repository->lastCommitterDate();
		}
	}
}
