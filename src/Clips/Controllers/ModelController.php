<?php namespace Clips\Controllers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Controller;

/**
 * This controller will expose model's method to JavaScript request.
 *
 * TODO: This will cause security issues, so user must provide a detail security rule (white list rule)
 * for this function, must provide an easier way to help user to do this.
 *
 * @author Jack
 * @date Sat Jul 11 09:34:40 2015
 * @version 1.1
 */
class ModelController extends Controller {
	public function invoke($model, $method) {
		$m = $this->tool->model($model);
		if($m && \Clips\method_is_public(get_class($m), $method)) {
			$parameters = $this->request->param("parameters", array());
			return $this->json(call_user_func_array(array($m, $method), $parameters));
		}
		return $this->json([]);
	}
}
