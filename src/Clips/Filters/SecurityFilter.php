<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\AbstractFilter;

/**
 * @Clips\Object("securityEngine")
 */
class SecurityFilter extends AbstractFilter {
	public function filter_before($chain, $controller, $method, $args, $request) {
		$action = \Clips\context('action');
		if($action) {
			$result = $this->securityengine->test($action);
			if($result) { // We got an rejects
				$reasons = array();
				$cause = 'security';

				foreach($result as $item) {
					$reasons []= $item->reason;
					if(isset($item->cause)) {
						$cause = $item->cause;
					}
				}
				\Clips\error($cause, $reasons);
				$chain->stop();
				return false;
			}
		}
		// Default pass
		return true;
	}
}
