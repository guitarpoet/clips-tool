<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This is the base class for all the interactive console.
 * This base implments a simple echo console.
 *
 * @author Jack
 * @date Sat Feb 21 12:02:24 2015
 */
class ConsoleBase {

	/**
	 * Get the prompt message
	 *
	 * @param continue (default false)
	 * 		Is the promt indicates use to continue input?
	 */
	protected function prompt($continue = false) {
		if($continue)
			return '... ';
		return 'echo$ ';
	}

	/**
	 * Is use's input is a complete command?
	 */
	protected function isComplete($line) {
		return true;
	}

	/**
	 * Run the command. The method that every subclass should override.
	 */
	protected function doRun($line) {
		$l = trim($line);
		if($l == 'quit' || $l == 'exit')
			$this->running = false;
			
		echo $line;
	}

	/**
	 * The interface method, to start the console
	 */
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
