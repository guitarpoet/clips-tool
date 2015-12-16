<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The stream filter for output the data using streaming.
 *
 * @author Jack
 * @date Wed Dec 16 15:02:12 2015
 * @version 1.2
 */
class StreamViewFilter extends ViewFilter {
	public function doRender($controller, $method, $args, $request, $template, $data, $controller_ret) {
		$stream = $controller_ret->template;

		if($stream && is_resource($stream)) {
			$s = fopen('php://output', 'wb');
			stream_copy_to_stream($stream, $s);
			fclose($stream);
			fclose($s);
		}
		else {
			\Clips\error('No stream to output!');
		}
	}
}
