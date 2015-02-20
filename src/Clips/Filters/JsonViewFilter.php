<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This filter will just render the data using json_encode. (maybe a better serializer future)
 *
 * @author Jack
 * @date Fri Feb 20 21:27:40 2015
 */
class JsonViewFilter extends ViewFilter {
	protected function doRender($controller, $method, $args, $request, $template, $data, $controller_ret) {
		header("Content-Type: application/json");
		echo json_encode($data);
	}
}
