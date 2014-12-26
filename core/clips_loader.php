<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The loader support for all the loading
 */
class Clips_Loader {
	private $clips;

	public function __construct($clips) {
		$this->clips = $clips;
	}

	public function load($class, $init = TRUE) {
		$env = $clips->switchCore(); // Switch to core to do the load
		if($env) // Let's switch back
			$clips->switchEnv($env);
	}
}
