<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

require_once 'Console/ProgressBar.php';

class Clips_Progress_Manager {
	public function __construct() {
		if(class_exists('Console_ProgressBar') && is_cli()) {
			$this->show = true;
		}
		else {
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

			$this->progressbar = new Console_ProgressBar('%current%/%max% [%bar%] %percent% %elapsed%', '=>',
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
