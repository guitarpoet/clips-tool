<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

interface FakeDataSourceHandler {

	public function expect();

	public function result($result);

	public function doQuery($query, $args = array());

	public function doUpdate($id, $args);

	public function doDelete($id);

	public function doFetch($args);

	public function doClear();

	public function doInsert($args);
}
