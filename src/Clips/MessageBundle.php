<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\Initializable;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;
use Addendum\Annotation;

/**
 * The class for the message bundle.
 *
 * The message bundle is a folder. It's name is the folder name. A simple message bundle is
 * something like this:
 *
 * + admin/
 * |
 * + - en-US.yml
 * |
 * + - zh-CN.yml
 * |
 * + - default.yml
 *
 * The message bundle can have as much locale as it has, and may have 1 default.php (this is 
 * the local file that used when no locale matching)
 *
 * And if no default matching is there, will just return the key.
 *
 * Message bundle will support sprintf style string format.
 *
 * @author Jack
 * @date Mon Feb 23 10:44:53 2015
 */
class MessageBundle extends Annotation implements Initializable, LoggerAwareInterface {

	public $name;

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function init() {
		$locale = get_locale(); // Guess the locale

		$location = context('bundle_dir');

		if($location) {
			foreach($location as $l) {
				// Try to find the folder
				$p = try_path(path_join($l, $this->name));
				if($p) {
					$this->folder = $p;
					break;
				}
			}
		}

		$this->locale = $locale;
		$this->localeData = array();

		if(isset($this->folder))
			$this->logger->info('Creating bundle {0} with locale {1} at folder {2}.', array($this->name, $locale, $this->folder));
	}

	public function isEmpty() {
		// If we didn't found the folder, we have an empty bundle
		return !isset($this->folder);
	}

	public function loadLocale($locale) {
		if(isset($this->localeData[$locale]))
			return $this->localeData[$locale]; 

		if(isset($this->folder)) {
			$p = path_join($this->folder, $locale.'.yml');
			if(file_exists($p)) { // Test if the locale file is exists
				$result = Yaml::parse($p);
				$this->localeData[$locale] = $result; 
				return $result;
			}
		}
		return false;
	}

	public function all() {
		$locale = $this->loadLocale($this->locale);
		$default = $this->loadLocale('default');
		return extend_arr($default, $locale);
	}

	private function _process() {
		$args = func_get_args();
		$func = array_shift($args);
		$key = array_shift($args);
		if(isset($this->folder)) { // Locale folder must be found!
			if(isset($this->locale)) { // Only try to load the locale when locale is set
				$locale = $this->loadLocale($this->locale);

				if($locale) { // We do found this locale
					$format = get_default($locale, $key, null); // Getting the key
				}
				else {
					// We can't find this locale, try load default
					$locale = $this->loadLocale('default');
					if($locale) { // If we do have default
						$this->locale = 'default';
					}
					else {
						// We can't even load default
						$this->locale = null;
					}
				}

				if($locale) { // We do have a locale loaded
					if(!isset($format) && $this->locale != 'default') {
						$locale = $this->loadLocale('default');
						$format = get_default($locale, $key, $key); // Getting the key, using default
					}
				}
				else
					$format = $key;
			}
		}
		if(!isset($format))
			$format = $key;
		array_unshift($args, $format);
		return call_user_func_array($func, $args);
	}

	public function template() {
		$args = func_get_args();
		$key = array_shift($args);
		array_unshift($args, $key);
		array_unshift($args, 'Clips\\str_template');
		return call_user_func_array(array($this, '_process'), $args);
	}

	public function message() {
		$args = func_get_args();
		array_unshift($args, 'sprintf');
		return call_user_func_array(array($this, '_process'), $args);
	}
}
