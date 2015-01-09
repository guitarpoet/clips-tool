<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class DBModel extends \Clips\Libraries\Sql {
	public function __construct() {
		$tool = &get_clips_tool();
		$tool->library('datasource'); // Load the datasource library

		$config = $tool->config;
		$name = get_class($this);
		$name = strtolower(substr($name, 0, strlen($name) - 6));

		// Check for models config first
		if($config->models) {
			foreach($config->models as $mc) {
				if(isset($mc->$name)) {
					if(isset($mc->$name->datasource)) {
						$datasource = $mc->$name->datasource;
						break;	
					}
				}
				if(isset($mc->datasource)) { // Let's try the overall configuration
					$datasource = $mc->datasource;
					break;
				}
			}
		}

		if(!isset($datasource)) {
			// There is still no datasource information, let's try using first one of the datasource
			foreach($tool->datasource->datasources() as $ds) {
				$this->db = $tool->datasource->get($ds);
				break;
			}
		}
		else {
			$this->db = $tool->datasource->get($datasource);
		}
		if(isset($this->db)) {
			parent::__construct($this->db->type);
			if(isset($this->db->table_prefix))
				$this->table_prefix = $this->db->table_prefix;
			return;
		}

		throw new Exception('Cant\'t find any datasource for this model.');
	}

	public function result() {
		$sql = $this->sql();
		switch(count($sql)) {
		case 0:
			throw new \Clips\DataSourceException('Can\'t do the query since no query generated!');
		case 1:
			return $this->db->query($sql[0]);
		default:
			return $this->db->query($sql[0], $sql[1]);
		}
	}
}
