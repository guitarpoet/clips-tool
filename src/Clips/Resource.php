<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Resource {
	public $uri;
	protected $stream;

	public function __construct($uri) {
		if(strpos($uri, '://') === false) {
			throw new Exception('The resource of uri '.$uri.' is not valid!');
		}
		$this->uri = $uri;
		$info = parse_url($uri);

		$tool = &get_clips_tool();

		// Loading the Resource_Handler base class
		$tool->load_class('ResourceHandler', false, new LoadConfig($tool->config->core_dir));

		$handler = $tool->load_class($info['scheme'], true, new LoadConfig($tool->config->resource_handler_dir, "ResourceHandler", "Clips\\ResourceHandlers\\"));

		if(!isset($handler)) {
			throw new Exception('No handler found for resource of uri '.$uri.'!');
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
