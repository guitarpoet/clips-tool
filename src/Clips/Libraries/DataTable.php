<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\ToolAware;
use Clips\Interfaces\Initializable;

class DataTable implements ToolAware, Initializable {
	public function init() {
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

}
