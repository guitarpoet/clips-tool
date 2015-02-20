<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

/**
 * This filter will pass all the scss files to compile to the Scss compiler, and use
 * the file cache library to save the scss file to the cache location.
 *
 * The file name of the cached css file is like this:
 *
 * {the flat name of controller}_{the controller method}.
 *
 * So the path of function index of Demo\Controllers\TestController will be like this:
 * 
 * demo_controllers_test_controller_index.css
 *
 * And, debug_sass option will disable the cache test, will generate scss file everytime.
 *
 * @author Jack
 * @date Fri Feb 20 21:50:41 2015
 *
 * @Clips\Library({"sass", "fileCache"})
 */
class ScssFilter extends AbstractFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$scsses = \Clips\context('scss');
		if($scsses) {
			$cache = $this->fileCache->cacheDir();
			$full_name = \Clips\to_flat(get_class($controller)).'_'.$method;
			$uri = \Clips\path_join(\Clips\path_join($cache, 'css'), $full_name);

			$cache_filename = \Clips\path_join($cache, 'css', \Clips\to_flat(get_class($controller).'_'.$method).'.css');
			if(file_exists($cache_filename) && !\Clips\config('debug_sass')) {
				\Clips\add_css(\Clips\static_url($uri));
				return;
			}
			// Add the sass_dir into include pathes
			$result = $this->sass->compile($scsses);
			if($result) {
				$this->fileCache->save(\Clips\to_flat(get_class($controller).'_'.$method).'.css', \Clips\path_join($cache, 'css'), $result, true);
				\Clips\add_css(\Clips\static_url($uri));
			}
		}
	}
}
