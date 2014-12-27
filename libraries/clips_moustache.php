<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

require_once(dirname(__FILE__).'/moustache/src/Mustache/Autoloader.php');
Mustache_Autoloader::register();

/**
 * The moustache wrapper for clips tools
 */
class Clips_Moustache {
	public function __construct() {
		$this->engine = new Mustache_Engine;
	}

	/**
	 * Render the template using the mustache engine
	 *
	 * @args
	 * 		template: The template uri for render
	 * 		args: The args that used for render
	 */
	public function render($template, $args = array()) {
		if(func_num_args() > 2) { // They must using the variable args method
			$args = func_get_args();
			$template = array_shift($args);
			return $this->render($template, $args);
		}

		$resource = new Clips_Resource($template);
		return $this->engine->render($resource->contents(), $args);
	}
}
