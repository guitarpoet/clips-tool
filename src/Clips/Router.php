<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Psr\Log\LoggerAwareInterface;
use Clips\Interfaces\ClipsAware;
use Clips\Interfaces\ToolAware;

class RouteResult {
	public $controller;
	public $method;
	/** @Multi */
	public $args;
}

class Router implements LoggerAwareInterface, ClipsAware, ToolAware {

	public function setClips($clips) {
		$this->clips = $clips;
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
		$request = new HttpRequest();
		// Empty the main envrionment
		$this->clips->clear();
		$this->clips->template("Clips\\RouteResult");
		$this->clips->load(clips_config('route_rules', array('/rules/route.rules')));
		$this->clips->assertFacts(array('uri', $this->getRequestURI()), array('RequestType', $request->getType()), array('RequestMethod', $request->method));
		$this->clips->run();
		$error = $this->clips->queryFacts("RouteError");
		if($error) {
			$this->showError($error);
		}
		else {
			$result = $this->clips->queryFacts("Clips\\RouteResult");
			$result = $result[0];
			$controller = new $result->controller; 
			$controller->request = $request;

			$this->filterChain = $this->tool->load_class('FilterChain', true);
			$this->filterChain->addFilter(clips_config('filters'));

			$this->filterChain->filter_before($this->filterChain, $controller, $result->method, $result->args, $request);
			$ret = call_user_func_array(array($controller, $result->method), $result->args);

			$this->filterChain->filter_after($this->filterChain, $controller, $result->method, $result->args, $request, $ret);
		}
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function showError($error) {
		var_dump($error);
	}
}
