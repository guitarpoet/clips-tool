<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed"); 

use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;
use Clips\Interfaces\ClipsAware;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;


/**
 * The base class for all the service, will save lots of typing for implementing the 
 * interfaces that service will need.
 *
 * This really makes me miss eclipse.
 *
 * @author Jack
 * @date Sat Mar  7 10:00:14 2015
 */
class BaseService implements Initializable, ToolAware, ClipsAware, LoggerAwareInterface {
	public function init() {
		$this->doInit();
	}

	/**
	 * The initialize method to init the service
	 */
	protected function doInit() {
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function setClips($clips) {
		$this->clips = $clips;
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}
}
