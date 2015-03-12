<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Psr\Log\LoggerAwareInterface;
use Clips\Interfaces\ClipsAware;
use Psr\Log\LoggerInterface;
use Clips\Interfaces\ToolAware;
use Clips\Models\ViewModel;
use Addendum\Annotation;

/**
 * The base class for all the controllers
 *
 * @author Jack
 * @date Mon Feb 23 14:40:28 2015
 *
 */
class Controller extends Annotation implements ClipsAware, LoggerAwareInterface, ToolAware {

	protected function title($title, $translate = false) {
		if($translate && isset($this->bundle)) {
			$title = $this->message($title);
		}
		html_title($title);
	}

	/**
	 * The shorthand method for getting the request parameters
	 */
	protected function param($param = null, $default = null) {
		$this->request->param($param, $default);
	}

	/**
	 * The short hand method for request->get
	 */
	protected function get($param = null, $default = null) {
		return $this->request->get($param, $default = null);
	}

	/**
	 * Reporting the error
	 */
	protected function error($message, $cause = null) {
		if(!\is_array($message)) {
			$message = array($message);
		}
		\Clips\error($cause, $message);
	}

	/**
	 * Get the value from $_SERVER
	 */
	protected function server($key, $default = null) {
		return $this->request->server($key, $default);
	}

	/**
	 * Get the value from cookie
	 */
	protected function cookie($key, $default = null) {
		return $this->request->cookie($key, $default);
	}

	/**
	 * Get the post parameters
	 */
	protected function post($param = null, $default = null) {
		return $this->request->post($param, $default);
	}

	/**
	 * Get or set the context value
	 */
	protected function context($key, $value = null) {
		return $this->tool->context($key, $value);
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function setClips($clips) {
		$this->clips = $clips;
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	/**
	 * Render wrapper, will create the ViewModel based on the parameter
	 */
	protected function render($template, $args = array(), $engine = null, $headers = array()) {
		if(!$engine) {
			$default = clips_config('default_view');
			if($default) {
				$engine = $default[0];
			}
		}

		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
		foreach($trace as $t) {
			if($t['class'] == get_class($this)) {
				$method = $t['function'];
				$actions = get_annotation($this, 'Clips\\Actions', $method);
				if($actions) {
					$f = get_annotation($this, 'Clips\\Form', $method);
					if($f) {
						$data = $this->formData($f->value);
						$args['edit'] = $this->_action($actions->value.'/edit', $data, 'Edit');
						$args['delete'] = $this->_action($actions->value.'/delete', $data, 'Delete');
					}
					$args['create'] = $this->_action($actions->value.'/create', null, 'Create');
				}
				break;
			}
		}
		return new ViewModel($template, $args, $engine, $headers);
	}

	/**
	 * Write the meta of the output html
	 */
	protected function meta($key, $value) {
		$meta = context('html_meta');
		if(!$meta)
			$meta = array();
		
		$res = array();
		$found = false;
		foreach($meta as $m) {
			if(isset($m[$key])) {
				$m[$key] = $value;
				$found = true;
			}
			$res []= $m;
		}
		if(!$found)
			$res []= array($key => $value);
		context('html_meta', $res);
	}

	protected function getPagination($config) {
		$config_dir = config('pagination_config_dir');
		if($config_dir) {
			$config_dir = $config_dir[0];
			$p = path_join($config_dir, $config.'.json');

			if(!file_exists($p) && func_num_args() >= 2) {
				$config = func_get_arg(1);
				// Try find the configuration by remove the first part
				$p = path_join($config_dir, $config.'.json');
			}
			if(file_exists($p)) {
				$pagination = Pagination::fromJson(file_get_contents($p));
				$pagination->name = $config;
				$pagination->security = $this->tool->load_class('securityEngine', true);
				$pagination->update($this->request->param());

				// Update the pagination using session where
				$conf = $this->request->session($config);
				if($conf) {
					$pagination->where []= new Libraries\AndOperator($conf);
				}
				return $pagination;
			}
		}
		return null;
	}

	protected function processPagination($pagination) {
		if($pagination) {
			$sql = $this->tool->library('sql');

			// Get the first datasource
			$datasource = $this->tool->library('dataSource')->first();

			if(isset($pagination->join) && is_array($pagination->join) 
				&& is_array($pagination->join[0])) {
				$pagination->join = array_reverse($pagination->join);
			}

			$query = $sql->count($pagination);
			if(is_string($query)) {
				$result = $datasource->query($query);
			}
			else {
				if(isset($query[1]))
					$result = $datasource->query($query[0], $query[1]);
				else
					$result = $datasource->query($query[0]);
			}

			if($result) {
				$count = $result[0]->count;
				$query = $sql->pagination($pagination);
				if(isset($query[1]))
					$result = $datasource->query($query[0], $query[1]);
				else
					$result = $datasource->query($query[0]);

				if($result) {
					if($pagination->bundleFields()) {
						// We need to translate some fields using bundle
						foreach($pagination->bundleFields() as $f) {
							$bundle = bundle($f->bundle);
							foreach($result as $row) {
								$name = smooth($f->data);
								if(isset($row->$name)) {
									if(isset($f->format))
										$row->$name = $bundle->message($f->format, $row->$name);
									else
										$row->$name = $bundle->message($row->$name);
								}
							}
						}
					}
					return $this->render("", array('data' => $result, 'start' => $pagination->offset, 'length' => $pagination->length, 'recordsTotal' => $count, 'recordsFiltered' => $count), 'json');
				}
			}
		}
		// Output empty by default
		return $this->render("", array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0), 'json');
	}

	/**
	 * The overall paginate query support.
	 */
	public function pagination($config) {
		$pagination = $this->getPagination($config);
		return $this->processPagination($pagination);
	}

	/**
	 * Shorthand method to the bundle message
	 */
	protected function message() {
		if(isset($this->bundle)) {
			return call_user_func_array(array($this->bundle, 'message'), func_get_args());
		}
	}

	/**
	 * Send the redirect response
	 */
	protected function redirect($url, $post = false, $args = array()) {
		if($post) {
			return $this->render(post_redirect($url, $args), array(), 'direct');
		}
		http_response_code(302);
		return $this->render("", array(), 'direct', array('Location' => $url));
	}

	protected function json($data) {
		return $this->render("", $data, 'json');
	}

	protected function formData($form, $data = null) {
		if($data)
			context('form_'.$form, $data);
		return context('form_'.$form);
	}

	protected function _action($uri, $data, $label) {
		if(isset($data->id))
			return new SimpleAction(array('content' => $uri, 'params' => array($data->id), 'label' => $label));
		return new SimpleAction(array('content' => $uri, 'params' => array(), 'label' => $label));
	}

	/**
	 * Forward to another controller method
	 */
	protected function forward() {
		$args = func_get_args();
		if($args) {
			$method = array_shift($args);
			$re = new \Addendum\ReflectionAnnotatedClass($this);
			$m = $re->getMethod($method);
			foreach($m->getAnnotations() as $a) {
				$this->tool->annotationEnhance($a, $this);
			}
			return call_user_func_array(array($this, $method), $args);
		}
	}

	/**
	 * Send the image file
	 */
	protected function image($img) {
		if(file_exists($img)) {
			$path_parts = \pathinfo($img);
			$ext = $path_parts['extension'];

			if($ext == 'jpg' || $ext == 'jpeg') {
				$header = array('Content-Type' => 'image/jpg');
			}
			else if($ext == 'png') {
				$header = array('Content-Type' => 'image/png');
			}
			else if($ext == 'gif') {
				$header = array('Content-Type' => 'image/gif');
			}
			return $this->render(file_get_contents($img), array(), 'direct', $header);
		}
		$this->error('Can\'t find the image ['.$img.'] to render!', 'render');
	}
}
