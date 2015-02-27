<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Psr\Log\LoggerAwareInterface;
use Clips\Interfaces\ClipsAware;
use Clips\Interfaces\ToolAware;
use Clips\Models\ViewModel;

class RouteResult {
	public $controller;
	public $method;
	/** @Multi */
	public $args;
}

/**
 * Routing the http request using routing rules(default rules for routing is same as CI)
 *
 * @author Jack
 * @date Mon Feb 23 16:11:24 2015
 */
class Router implements LoggerAwareInterface, ClipsAware, ToolAware {

	public function setClips($clips) {
		$this->clips = $clips;
	}

	public function staticUrl($url = '') {
		if(!isset($this->base))
			$this->base = dirname($_SERVER['SCRIPT_NAME']);
		return path_join($this->base, $url);
	}

	public function baseUrl($url = '') {
		$index = config('use_rewrite')? '': '/index.php';
		if(!isset($this->base))
			$this->base = dirname($_SERVER['SCRIPT_NAME']);
		return $this->base.$index.'/'.$url;
	}

	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function getRequestURI() {
		if ( ! isset($_SERVER['REQUEST_URI']) OR ! isset($_SERVER['SCRIPT_NAME'])) {
			return '';
		}

		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
			$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		}
		elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
			$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}

		// This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
		// URI is found, and also fixes the QUERY_STRING server var and $_GET array.
		if (strncmp($uri, '?/', 2) === 0) {
			$uri = substr($uri, 2);
		}
		$parts = preg_split('#\?#i', $uri, 2);
		$uri = $parts[0];
		if (isset($parts[1])) {
			$_SERVER['QUERY_STRING'] = $parts[1];
			parse_str($_SERVER['QUERY_STRING'], $_GET);
		}
		else {
			$_SERVER['QUERY_STRING'] = '';
			$_GET = array();
		}

		if ($uri == '/' || empty($uri)) {
			return '/';
		}

		$uri = parse_url($uri, PHP_URL_PATH);

		// Do some final cleaning of the URI and return it
		return str_replace(array('//', '../'), '/', trim($uri, '/'));
	}

	public function route() {
		$request = $this->tool->create('Clips\\HttpRequest');

		$this->tool->context(array(
			'request' => $request,
			'router' => $this
		)); // Set the request context to the context

		// Empty the main envrionment
		$this->clips->clear();
		$this->clips->template("Clips\\RouteResult");
		$this->clips->load(clips_config('route_rules', array('/rules/route.rules')));
		// Assert the uris
		$this->clips->assertFacts(array('uri', $this->getRequestURI()), array('RequestType', $request->getType()), array('RequestMethod', $request->method));

		// Assert the parameters
		$params = array();
		$p = $request->param();
		if($p) {
			foreach($request->param() as $k => $v) {
				$params []= array('Parameter', $k, $v);
			}
			$this->clips->assertFacts($params);
		}

		$this->clips->run();
		$error = $this->clips->queryFacts("RouteError");
		if($error) {
			$result = new RouteResult();
			$result->controller = $this->tool->controller('error');
			$result->method = 'show';
			$result->args = $error[0];
			http_response_code(404);
			error('RouteError', array($error[0][0]));
		}
		else {
			$result = $this->clips->queryFacts("Clips\\RouteResult");
			$result = $result[0];
		}
		$controller = $this->tool->create($result->controller);
		$this->tool->context(array(
			'controller_class' => $result->controller,
			'controller' => $controller,
			'controller_method' => $result->method,
			'args' => $result->args
		));
		$controller->request = $request;

		$this->filterChain = $this->tool->load_class('FilterChain', true);
		$this->filterChain->addFilter(config('filters'));

		$re = new \Addendum\ReflectionAnnotatedClass(get_class($controller));
		// Trying to get the definition from class and the method annotation
		foreach(array($re, $re->getMethod($result->method)) as $m) {
			foreach($m->getAnnotations() as $a) {
				if(get_class($a) == 'Clips\\HttpSession') {
					if(isset($a->value))  {
						if(is_array($a->value)) {
							foreach($a->value as $k => $v) {
								$request->session($k, $v);
							}
						}
						else {
							$request->session($a->key, $a->value);
						}
					}
				}
				else if(get_class($a) == 'Clips\\MessageBundle') {
					$this->tool->enhance($a);
					$controller->bundle = $a;
					if(isset($a->name))
						Clips\context('current_bundle', $a->name);
				}
				else if(get_class($a) == 'Clips\\Meta') {
					if(isset($a->value) && is_array($a->value)) {
						foreach($a->value as $k => $v) {
							html_meta($k, $v);
						}
					}
					else {
						html_meta($a->key, $a->value);
					}
				}
				else if(get_class($a) == 'Clips\\Js') {
					if(is_string($a->value))
						add_js($a->value);
					else if(is_array($a->value)) {
						foreach($a->value as $j) {
							add_js($j);
						}
					}
				}
				else if(get_class($a) == 'Clips\\Css') {
					if(is_string($a->value))
						add_css($a->value);
					else if(is_array($a->value)) {
						foreach($a->value as $c) {
							add_css($c);
						}
					}
				}
				else if(get_class($a) == 'Clips\\Scss') {
					if(is_string($a->value))
						add_scss($a->value);
					else if(is_array($a->value)) {
						foreach($a->value as $c) {
							add_scss($c);
						}
					}
				}
				else if(get_class($a) == 'Clips\\Context') {
					if(isset($a->value) && is_array($a->value)) {
						// This must be the set by array
						context($a->value, null, $a->append);
					}
					else {
						context($a->key, $a->value, $a->append);
					}
				}
				else if(get_class($a) == 'Clips\\Form') {
					// If this is the form annotation, initialize it and set it to the context
					$this->tool->enhance($a);
					context('form', $a);
				}
                else if(get_class($a) == 'Clips\\Widget') {
                    $this->tool->widget($a->value);
                }
				else if(get_class($a) == 'Clips\\Widgets\\DataTable' || get_class($a) == 'Clips\\Widgets\\ListView') {
					// If this is the datatable annotation, initialize it and set it to the context
					$this->tool->enhance($a);
					context('datatable', $a);
				}
				else if(get_class($a) == 'Clips\\Model') {
					if(isset($a->value)) {
						if(!\is_array($a->value))
							$a->value = array($a->value);

						foreach($a->value as $c) {
							$h = strtolower($this->tool->getHandleName($c));
							$controller->$h = $this->tool->model($c);
						}
					}
				}
				else if(get_class($a) == 'Clips\\Object') {
					if(isset($a->value)) {
						if(!\is_array($a->value))
							$a->value = array($a->value);

						foreach($a->value as $c) {
							$h = strtolower($this->tool->getHandleName($c));
							$controller->$h = $this->tool->load_class($c, true);
						}
					}
				}
			}
		}

		$ret = null;
		if($this->filterChain->filter_before($this->filterChain, $controller, $result->method, $result->args, $request)) {
			// Let the filter before can prevent the run of the controller method
			try { 
				$ret = call_user_func_array(array($controller, $result->method), $result->args);
			}
			catch(\Exception $e) {
				error(get_class($e), array($e->getMessage()), true);
			}
		}

		// Getting the error from the context
		$error = context('error');

		if($ret == null && $error) { // If there is no output and we can get the error, show the error
			$default_view = config('default_view');
			if($default_view) {
				if(isset($error->cause)) {
					$ret = new ViewModel('error/'.$error->cause, array('error' => $error->message), $default_view[0]);
				}
				else {
					$ret = new ViewModel('error/error', array('error' => $error), $default_view[0]);
				}
			}
			else
				$ret = $error;
		}
		else {
			if($error)
				// We can get the response, so just log the error
				$this->logger->error('Getting an error when serving the request.', array('error' => $error));
		}

		// Always run filter after(since the filter after will render the views)
		$this->filterChain->filter_after($this->filterChain, $controller, $result->method, $result->args, $request, $ret);
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}
}
