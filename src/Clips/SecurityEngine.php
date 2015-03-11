<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed"); 

use Clips\Interfaces\Action;

/**
 * The security engine based on clips
 *
 * @author Jack
 * @date Sat Mar  7 09:54:07 2015
 */
class SecurityEngine extends BaseService {
	const SECURITY = 'SECURITY';

	protected function doInit() {
		// Create the security environment
		$this->clips->createEnv(self::SECURITY);

		// Loading the rules
		$this->clips->runWithEnv(self::SECURITY, function($clips, $self){
			// Clear the context by default
			$clips->clear();
			$dir = config('rules_dir');

			$clips->template('Clips\\Reject');
			$clips->template('Clips\\SecurityItem');
			foreach($dir as $d) {
				$p = try_path(path_join($d, 'security.rules'));
				if($p)
					break;
			}

			if($p) {
				$this->logger->debug('Loading rules from {0}.', array($p));
				$clips->load($p);
			}
		}, $this);
	}

	/**
	 * Test if the item should pass
	 *
	 * @return result
	 * 		The result should be the security engine test result, pass if it is
	 * 		empty, and if failed, it will be the array of Clips\Reject objects
	 * 		
	 */
	public function test($item) {
		if(valid_obj($item, 'Clips\\Interfaces\\Action')) { // If this item is action
			if($item->type() != Action::SERVER) // We only filter server actions
				return array();
		}

		return $this->clips->runWithEnv(self::SECURITY, function($clips, $item){
			// Reset the facts first
			$clips->reset();
			$clips->assertFacts(array(new SecurityItem($item)));
			$clips->run();
			return $clips->queryFacts("Clips\\Reject");
		}, $item);
	}
}
