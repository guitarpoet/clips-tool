<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

/**
 * @Clips\Object("securityEngine")
 */
class SecurityFilter extends AbstractFilter {
	public function filter_before($chain, $controller, $method, $args, $request) {
		$action = \Clips\context('action');
		if($action) {
			$result = $this->securityengine->test($action);
			if($result) { // We got an reject
				$result = array_map(function($item){
					return $item->reason;
				}, $result);
				\Clips\error('Security', $result);
				$chain->stop();
				return false;
			}
		}
		// Default pass
		return true;
	}
}
