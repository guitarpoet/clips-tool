<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;
use Clips\Libraries\FlowStep;

/**
 * The workflow support class.
 *
 * @author Jack
 * @date Wed Jun 10 18:55:09 2015
 */
class Flow extends BaseService {
	/**
	 * Initialize the flow exectuion environment
	 */
	protected function doInit() {
		if(!$this->clips->isEnvExists('FLOW')) {
			$this->clips->createEnv('FLOW');
		}
	}

	/**
	 * Load the flow definition(the rules file)
	 */
	public function load($flow) {
		$this->clips->runWithEnv('FLOW', function($clips, $flow) {
			$clips->reset();
			$clips->load($flow);
		}, $flow);
	}

	public function actions() {
		$status = $this->status();
		if($status) {
			return $this->clips->runWithEnv('FLOW', function($clips, $status) {
				$rules = $this->clips->rules();
				$ret = array();
				foreach($rules as $rule) {
					if(strpos($rule, $status) !== false) {
						$step = new FlowStep($rule);
						$result = $step->match_Rule();
						if($result) {
							if($result['from'] == $status)
								$ret []= $result['action'];
						}
					}
				}
				return $ret;
			}, $status);
		}
		return array();
	}

	/**
	 * Set or get the status
	 */
	public function status($status = null) {
		if($status) {
			$this->clips->runWithEnv('FLOW', function($clips, $status) {
				$clips->assertFacts(array('status', $status));
			}, $status);
		}
		else {
			$s = $this->clips->runWithEnv('FLOW', function($clips, $status) {
				return $clips->queryFacts('status');
			}, $status);
			if($s)
				return $s[0][0];
			return null;
		}
	}

	public function all() {
		return $this->clips->runWithEnv('FLOW', function($clips) {
			var_dump($clips->queryFacts());
		});
	}

	/**
	 * Run the action for the flow
	 */
	public function action($action, $args = array()) {
		$this->clips->runWithEnv('FLOW', function($clips, $data) {
			$clips->assertFacts(array('action', $data['action']));
			$facts = array();
			foreach($data['args'] as $k => $v) {
				$facts []= array('arg', $k, $v);
			}
			$clips->assertFacts($facts);
			$clips->run();
		}, array('action' => $action, 'args' => $args));
	}
}
