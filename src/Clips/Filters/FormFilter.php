<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;
use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;

class FormFilter extends AbstractFilter implements ToolAware, Initializable {

	public function init() {
		$this->validator = $this->tool->load_class('validator', true);
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function accept($chain, $controller, $method, $args, $request, $controller_ret = null) {
		$form_name = $request->param(\Clips\Form::FORM_FIELD);
		if($form_name) // If we can get the form name from the request, it must be the form post(even in get request)
			return true;

		$form = \Clips\clips_context('form');
		if($form) {
			if($form->get) // If force to use get validation
				return true;
		}

		// For the default operation, all the post request, will be processed as form request
		return $request->method == 'post';
	}

	public function filter_before($chain, $controller, $method, $args, $request) {
		// Get the form name from the form field
		$form = \Clips\clips_context('form');
		if($form) {
			// Try to get form name from the request parameter
			$form_name = $request->param(\Clips\Form::FORM_FIELD);

			// If not found, try to find the form configuration as the first one
			if(!$form_name) {
				$form_name = $form->defaultFormName();
			}

			// Get the current form config
			$config = $form->config($form_name);

			$params = $request->param();
			if(!$params)
				$params = array();
			$ret = $this->validator->validate($params, $config);
			if($ret) {
				$chain->succeed = false;
				\Clips\clips_error('form_validation', $ret);
			}
		}
	}
}
