<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Datasource {
	public function __construct($name = null) {
		if(isset($name)) {
			$config = get_default(clips_config('datasources'), $name, null);
			if(isset($config)) {
			}
			else {
				trigger_error('No datasource config for this datasource '.$namd);
			}
		}
	}

	protected function doQuery($query, $args = array()) {
	}

	protected function doLoad($id) {
	}

	protected function doUpdate($id, $args) {
	}

	public function load($id) {
	}

	public function update($id, $args) {
	}

	public function delete($id) {
	}

	public function query() {
		$c = func_num_args();
		switch($c) {
		case 0:
			trigger_error('No query found!');
			return array();
		case 1:
			return $this->doQuery($query);
		default:
			$args = func_get_args();
			$query = array_shift($args);
			return $this->doQuery($query, $args);
		}
	}
}
