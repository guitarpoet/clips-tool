<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;

/**
 * The babel compiler using file cache to cache the compiled file
 *
 * @author Jack
 * @date Sat Jul 11 12:25:37 2015
 * @version 1.1
 *
 * @Clips\Library("fileCache")
 */
class Babel extends BaseService {
	public function compile($file) {
		$babel = \Clips\config('babel');
		if($babel) {
			$babel = $babel[0];

			$paths = explode('/', $file);
			$name = str_replace('.jsx', '', array_pop($paths)); // Remove the filename
			array_pop($paths); // Remove the jsx folder
			$folder_name = 'js/'.array_pop($paths);

			$time = filectime($file);
			$folder = $this->filecache->mkdir($folder_name);
			if($this->filecache->shouldUpdate($name.'.js', $time, $folder)) {
				$this->logger->debug('Trying to compile {0} to path {1} using command {2}', array($file, $folder, $babel));

				\Clips\run($babel, array('-o' => \Clips\path_join($folder, $name.'.js'), 
					'-s' => \Clips\path_join($folder, $name.'.js.map'), $file));
			}

			return 'application/cache/'.$folder_name.'/'.$name.'.js';
		}
		return false;
	}
}
