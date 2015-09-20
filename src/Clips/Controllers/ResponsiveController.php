<?php namespace Clips\Controllers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Controller;

/**
 * The default responsive controller for create image responsively.
 *
 * The usage for this controller is something like this:
 *
 * index.php/responsive/size/120/admin/user.png
 *
 * @author Jack
 * @date Sat Feb 21 12:56:47 2015
 *
 * @Clips\Library({"fileCache", "imageUtils", "curl"})
 * @Clips\Widget("html")
 */
class ResponsiveController extends Controller {
	public function size() {
		if(func_num_args() < 2) {
			$this->error('Argument is too little for responsive!', 'responsive');
			return;
		}

		$args = array_map(function($item){ return urldecode($item); }, func_get_args());
		$size = array_shift($args);
		$file = implode('/', $args);

		$size = preg_replace('/[^0-9\.]/', '', $size);
		if(!is_numeric($size) || $size <= 0) {
			$this->error('Size ['.$size.'] must be positive number!', 'responsive');
			return;
		}

		foreach(\Clips\config('image_dir') as $dir) {
			$path = \Clips\try_path(\Clips\path_join($dir, $file));
			if($path)
				break;
		}

		$n = array_pop($args);
		if($args) { 
			// If we still have args, make it as the folder
			$folder = implode('/', $args);
		}
		else {
			$folder = '';
		}

		$cache = $this->filecache->cacheDir();

		$folder = \Clips\path_join($cache, 'img', $folder, $size);

		if(!file_exists($folder)) { // If no folder exists, make it
			mkdir($folder, 0777, true);
		}

		if(!$path) {
			foreach(\Clips\config('image_url_base') as $base) {
				$url = $base.'/'.$file;
				$f = \Clips\path_join($cache, 'img', implode('/', $args));
				$local_file = $this->filecache->fileName($n, $f);

				$path = \Clips\try_path($local_file);
				if($path)
					break;

				$this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
				$this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
				$this->curl->setOpt(CURLOPT_HEADER, false);
				$this->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
				if($this->curl->get($url) == 0) {
					file_put_contents($local_file, $this->curl->response);
					$path = \Clips\try_path($local_file);
				}
				break;
			}

			if(!$path) {
				$this->error('Can\'t find image file ['.$file.'] !', 'responsive');
				return;
			}
		}

		$name = $this->filecache->fileName($n, $folder);
		if($this->filecache->shouldUpdate($n, $path, $folder)) {
			$this->imageutils->thumbnail($path, $name, $size);
		}
		return $this->image($name);
	}
}
