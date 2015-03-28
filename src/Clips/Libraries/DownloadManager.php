<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;

/**
 * The download progress manager
 *
 * @author Jack
 * @version 1.0
 * @date Sat Mar 28 14:52:48 2015
 */
function download_progress($resource, $download_size, $downloaded, $upload_size, $uploaded) {
    if($downloaded > 0 && $download_size > 0) {
		$tool = &\Clips\get_clips_tool();
		$progress = $tool->library('progressManager');
		if($progress->isStarted()) {
			$progress->update($downloaded);
		}
		else {
			$progress->start($download_size);
		}
	}
}

/**
 * The download manager library
 *
 * @author Jack
 * @version 1.0
 * @date Sat Mar 28 12:05:32 2015
 */
class DownloadManager extends BaseService {
	public function download($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'Clips\Libraries\download_progress');
		curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if(isset($_SERVER['HTTP_USER_AGENT']))
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		else
			curl_setopt($ch, CURLOPT_USERAGENT, 'Clips Tool');
		$html = curl_exec($ch);
		curl_close($ch);
		return $html;
	}
}
