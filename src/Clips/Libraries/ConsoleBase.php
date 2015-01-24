<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ConsoleBase {
	protected function prompt($continue = false) {
		if($continue)
			return '... ';
		return 'echo$ ';
	}

	protected function isComplete($line) {
		return true;
	}

	protected function doRun($line) {
		$l = trim($line);
		if($l == 'quit' || $l == 'exit')
			$this->running = false;
			
		echo $line;
	}

	public function console() {
		$line = readline($this->prompt())."\n";
		$this->running = true;
		while($this->running) {
			if($this->isComplete($line)) {
				$this->doRun($line, true);
				readline_add_history($line);
				if($this->running)
					$line = readline($this->prompt())."\n";
			}
			else {
				if($this->running)
					$line .= readline($this->prompt(true))."\n";
			}
		}
	}
}
