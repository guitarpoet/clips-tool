<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This is the extendable resource facade for all the resource handlers
 */
class Clips_Resource {
	public $uri;
	protected $stream;

	public function __construct($uri) {
		if(strpos($uri, '://') === false) {
			throw new Exception('The resource of uri '.$uri.' is not valid!');
		}
		$this->uri = $uri;
		$info = parse_url($uri);

		$tool = get_clips_tool();

		// Loading the Resource_Handler base class
		$tool->load_class('resource_handler', false, new Load_Config($tool->config->core_dir));

		$handler = $tool->library('resources/'.$info['scheme'], true, "_resource_handler");
		if(!isset($handler)) {
			throw new Exception('No handler found for resource of uri '.$uri.'!');
		}
		$this->handler = $handler;
	}

	public function run($callback) {
		if($callback && is_callable($callback)) {
			try {
				$stream = $this->openStream();
				if($stream) {
					return $callback($stream);
				}
			}
			finally {
				$this->closeStream();
			}
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
