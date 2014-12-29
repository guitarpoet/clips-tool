<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Mysqli_Datasource extends Clips_Datasource {

	public function __construct($name = null) {
		parent::__construct($name);
	}

	protected function init($config) {
		print_r($config);
	}

	protected function doQuery($query, $args = array()) {
	}

	protected function doLoad($id) {
	}

	protected function doUpdate($id, $args) {
	}

	public function doDelete($id) {
	}

	public function beginBatch() {
	}

	public function endBatch() {
	}

}
