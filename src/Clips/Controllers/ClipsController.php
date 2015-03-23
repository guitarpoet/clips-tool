<?php namespace Clips\Controllers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Controller;

class ClipsController extends Controller {
	public function commands() {
		// Clear the rule engine first
		$this->clips->clear();
		$json = $this->request->post('commands');
		$commands = \CLips\parse_json($json);
		$filters = array();

		// Executing the commands
		foreach($commands as $c) {
			$command = $c->command;
			$data = $c->data;
			switch($command) {
			case 'filter': 
				$filters []= $data;
				break;
			case 'assert':
				$this->clips->assertFacts($data);
				break;
			case 'load':
				$this->clips->load($data);
				break;
			}
		}
		$this->clips->run();
		$facts = $this->clips->queryFacts();
		if($filters) {
			$tmp = array();
			foreach($facts as $f) {
				foreach($filters as $filter) {
					if($f['__template__'] == $filter) {
						$tmp []= $f;
						break;
					}
				}
			}
			$facts = $tmp;
		}
		return $this->json($facts);
	}
}
