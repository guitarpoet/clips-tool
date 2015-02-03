<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

/**
 * @Clips\Requires({"sass", "FileCache"})
 */
class ScssFilter extends AbstractFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$scsses = clips_context('scss');
		if($scsses) {
			// Add the sass_dir into include pathes
			$result = $this->sass->compile($scsses);
			if($result) {
				$cache = $this->FileCache->cacheDir();
				$full_name = to_flat(get_class($controller)).'_'.$method;
				$uri = path_join(path_join($cache, 'css'), $full_name).'.css';
				$this->FileCache->save(to_flat(get_class($controller).'_'.$method).'.css', path_join($cache, 'css'), $result, true);
				clips_add_css(base_url($uri));
			}
		}
	}
}

