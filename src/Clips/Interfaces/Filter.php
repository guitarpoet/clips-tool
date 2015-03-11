<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The filter interface for all the filters
 *
 * @author Jack <guiarpoet@gmail.com>
 * @date Mon Feb  2 20:44:18 2015
 *
 */
interface Filter {

	/**
	 * This method will be invoke before any filter invocation
	 *
	 * @param chain
	 * 		The filter chain object
	 * @param controller
	 * 		The controller object that needs to be filtered
	 * @param method
	 * 		The method that needs to be executed
	 * @param args
	 * 		The args that will go into the method
	 * @param request
	 * 		The request object
	 * @param controller_ret
	 * 		The return value of the controller
	 */
	public function accept($chain, $controller, $method, $args, $request, $controller_ret = null);
	/**
	 * This method will be invoke before the controller method
	 *
	 * @param chain
	 * 		The filter chain object
	 * @param controller
	 * 		The controller object that needs to be filtered
	 * @param method
	 * 		The method that needs to be executed
	 * @param args
	 * 		The args that will go into the method
	 * @param request
	 * 		The request object
	 */
	public function filter_before($chain, $controller, $method, $args, $request);

	/**
	 * This method will be invoke after the controller method
	 *
	 * @param chain
	 * 		The filter chain object
	 * @param controller
	 * 		The controller object that needs to be filtered
	 * @param method
	 * 		The method that needs to be executed
	 * @param args
	 * 		The args that will go into the method
	 * @param request
	 * 		The request object
	 * @param controller_ret
	 * 		The value that the controller returned
	 */
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret);

	/**
	 * This method will be invoke before render
	 *
	 * @param chain
	 * 		The filter chain object
	 * @param controller
	 * 		The controller object that needs to be filtered
	 * @param method
	 * 		The method that needs to be executed
	 * @param args
	 * 		The args that will go into the method
	 * @param request
	 * 		The request object
	 * @param controller_ret
	 * 		The value that the controller returned
	 */
	public function filter_render($chain, $controller, $method, $args, $request, $view, $view_context, $controller_ret);
}
