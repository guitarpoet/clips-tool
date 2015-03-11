<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The resource facade
 *
 * @author Jack
 * @date Mon Feb 23 16:07:56 2015
 */
class Resource {
	public $uri;
	protected $stream;

	public function __construct($uri) {
		if(strpos($uri, '://') === false) {
			throw new Exception('The resource of uri '.$uri.' is not valid!');
		}
		$this->uri = $uri;

		if(strpos($uri, "string://") === false) {
			$info = parse_url($uri);

			$proto = $info['scheme'];
			$clips_class = "Clips\\ResourceHandlers\\".ucfirst($proto)."ResourceHandler";
			if(class_exists($clips_class)) {
				$handler = new $clips_class();
			}
			else {
				// Using the configuration to load the handlers
				foreach(clips_config("resource_handlers") as $c) {
					if(isset($c->$proto)) {
						$class = $c->$proto;
						if(class_exists($class))
							$handler = new $class();
					}
				}

				// We really can't find the handler, let's doing a load.
				if(!isset($handler)) {
					$tool = &get_clips_tool();
					$handler = $tool->load_class(ucfirst($info['scheme']), true, new LoadConfig($tool->config->resource_handler_dir, "ResourceHandler", "ResourceHandlers\\"));
				}
			}
		}
		else {
			$handler = new ResourceHandlers\StringResourceHandler();
		}

		if(!$handler) {
			throw new Exception('No handler found for resource of uri '.$uri.' !');
		}
		$this->handler = $handler;
	}

	public function run($callback) {
		if($callback && is_callable($callback)) {
			$stream = $this->openStream();
			$ret = null;
			if($stream) {
				$ret = $callback($stream);
			}
			$this->closeStream();
			return $ret;
		}
	}

	public function openStream() { // Get the stream of this resource
		$this->stream = $this->handler->openStream($this->uri);
		return $this->stream;
	}

	public function closeStream() {
		if($this->stream && is_resource($this->stream))
			return $this->handler->closeStream($this->stream);
		return false;
	}

	public function contents() { // Get all the contents of this resource
		if(isset($this->uri))
			return $this->handler->contents($this->uri);
		return null;
	}
}
