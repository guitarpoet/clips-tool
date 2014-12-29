<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Mysqli_Datasource extends Clips_Datasource {

	public function __construct($name = null) {
		parent::__construct($name);
	}
}
