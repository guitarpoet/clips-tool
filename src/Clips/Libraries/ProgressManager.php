<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ProgressManager {
	public function __construct() {
		if(class_exists('Elkuku\Console\Helper\ConsoleProgressBar') && \is_cli()) {
			$this->show = true;
		}
		else {
			if(\is_cli())
				trigger_error('No ProgressBar support!');
		}
	}

	public function start($total = 100, $width = -1) {
		if($this->show) {
			if(isset($this->progressbar)) { // If we have the progress bar, remove it
				$this->progressbar->erase();
			}

			$meta = console_meta();

			if($width == -1)
				$width = $meta['width'];

			$this->progressbar = new \Elkuku\Console\Helper\ConsoleProgressBar('%current%/%max% [%bar%] %percent% %elapsed%', '=>',
				' ', $width, $total);
			$this->current_value = 0;
		}
	}

	public function update($value) {
		if($this->show && isset($this->progressbar)) {
			$this->current_value = $value;
			$this->progressbar->update($value);
		}
	}

	public function incre($value = 1) {
		if($this->show && isset($this->progressbar)) {
			$this->current_value += $value;
			$this->update($this->current_value);
		}
	}
}
