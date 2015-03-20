<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Psr\Log\LoggerAwareInterface;
use Clips\Interfaces\ClipsAware;
use Clips\Interfaces\ToolAware;
use Clips\Interfaces\Action;
use Clips\Models\ViewModel;

/**
 * Routing the http request using routing rules(default rules for routing is same as CI)
 *
 * @author Jack
 * @date Mon Feb 23 16:11:24 2015
 *
 * @Clips\Object("frameworkMeta")
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

	public function baseUrl($url = '', $full = false) {
		$index = config('use_rewrite')? '': '/index.php';
		if(!isset($this->base))
			$this->base = dirname($_SERVER['SCRIPT_NAME']);
		if($full) {
			$host = $_SERVER['SERVER_NAME'];
			$port = $_SERVER['SERVER_PORT'];
			if($port != 80) {
				return "http://".path_join($host.':'.$port, $this->base.$index, $url);
			}
			else {
				return "http://".path_join($host, $this->base.$index, $url);
			}
		}
		return path_join($this->base.$index, $url);
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

	public function routeResult($uri, $params = array(), $method = 'get', $type = 'http') {
		$this->clips->clear();
		$this->clips->template("Clips\\RouteResult");
		$this->clips->load(clips_config('route_rules', array('/rules/route.rules')));
		$this->clips->assertFacts(array('uri', $uri), array('RequestType', $type), array('RequestMethod', $method));

		// Assert the parameters
		$p = array();
		if($params) {
			foreach($params as $k => $v) {
				$p []= array('Parameter', $k, $v);
			}
			$this->clips->assertFacts($p);
		}

		$this->clips->run();
		$error = $this->clips->queryFacts("RouteError");

		if($error)
			return $error;

		return $this->clips->queryFacts("Clips\\RouteResult");
	}

	public function route() {
		profile_start('route');
		profile_start('load_controller');
		$request = $this->tool->create('Clips\\HttpRequest');
		html_meta('generator', 'clips-tool '.$this->frameworkmeta->branch.'('.
			$this->frameworkmeta->commit.')');
		$this->tool->context(array(
			'request' => $request,
			'router' => $this
		)); // Set the request context to the context

		// Empty the main envrionment
		$this->clips->clear();
		$this->clips->template("Clips\\RouteResult");
		$this->clips->load(clips_config('route_rules', array('/rules/route.rules')));
		// Assert the uris
		$uri = $this->getRequestURI();

		// Record the breadscrumb
		if($request->method == 'get' && $request->getType() != 'ajax' && strpos($uri, 'responsive/size') === false) {
			$bs = $request->breadscrumb();
			if(count($bs) > 1) {
				if($bs[count($bs) - 1] != $uri) {
					$request->breadscrumb($uri);
				}
			}
			else {
				$request->breadscrumb($uri);
			}
		}
	
		context('uri', $uri);
		$this->clips->assertFacts(array('uri', $uri), array('RequestType', $request->getType()), array('RequestMethod', $request->method));

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
			$controller_seg = 'error';
			http_response_code(404);
			error('RouteError', array($error[0][0]));
		}
		else {
			$result = $this->clips->queryFacts("Clips\\RouteResult");
			$controller_seg = $this->clips->queryFacts("controller");
			$server_uri = $this->clips->queryFacts("server-uri");
			$result = $result[0];
			$controller_seg = $controller_seg[0][0];
			$server_uri = strtolower(str_replace('\\', '/', $server_uri[0][0]));
		}
		if(!isset($server_uri))
			$server_uri = 'error';
		profile_end('load_controller');
		profile_start('controller_init');
		$cc = $result->controller;
		$controller = new $cc();
		$this->tool->context(array(
			'controller_class' => $result->controller,
			'controller_seg' => $controller_seg,
			'controller' => $controller,
			'controller_method' => $result->method,
			'args' => $result->args,
			'action' => new SimpleAction(array(
				'type' => Action::SERVER,
				'content' => $server_uri,
				'params' => $result->args
			))
		));
		$controller->request = $request;
		$this->tool->enhance($controller);

		$this->filterChain = $this->tool->load_class('FilterChain', true);
		$this->filterChain->addFilter(config('filters'));

        $re = new \Addendum\ReflectionAnnotatedClass($controller);
		// Trying to get the definition from class and the method annotation
		$m = $re->getMethod($result->method);
		foreach($m->getAllAnnotations() as $a) {
			$this->tool->annotationEnhance($a, $controller);
		}

		profile_end('controller_init');
		profile_start('filter_before');
		$ret = null;
		if($this->filterChain->filter_before($this->filterChain, $controller, $result->method, $result->args, $request)) {
			// Let the filter before can prevent the run of the controller method
			try { 
				profile_end('filter_before');
				profile_start('controller');
				$ret = call_user_func_array(array($controller, $result->method), $result->args);
				profile_end('controller');
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

		profile_end('route');
		// Always run filter after(since the filter after will render the views)
		$this->filterChain->filter_after($this->filterChain, $controller, $result->method, $result->args, $request, $ret);
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}
}
