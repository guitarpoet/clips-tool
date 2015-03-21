<?php namespace Clips\Controllers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Controller;

class ClipsController extends Controller {
	public function commands() {
		// Clear the rule engine first
		$this->clips->clear();
		$json = $this->request->post('commands');
		$commands = \CLips\parse_json($json);

		// Executing the commands
		foreach($commands as $c) {
			$command = $c->command;
			$data = $c->data;
			switch($command) {
			case 'assert':
				$this->clips->assertFacts($data);
				break;
			case 'load':
				$this->clips->load($data);
				break;
			}
		}
		$facts = $this->clips->queryFacts();
		return $this->json($facts);
	}
}
