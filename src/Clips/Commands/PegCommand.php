<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips;
use Clips\Command;
use hafriedlander\Peg\Compiler;

/**
 * @FullArgs
 */
class PegCommand extends Command {
	public function execute($args) {
		array_shift($args); // Remove the script name

		if(count($args) >= 2) {
			$name = ucfirst($args[1]);
		    foreach(Clips\config('peg_dir') as $peg_dir) {
				$file = Clips\try_path(Clips\path_join($peg_dir, $name.'.peg.php'));
				if($file)
					break;
			}

			if(!$file) {
				$this->error('No peg file found ');
				return;
			}
			else {
				$args[1] = $file;
			}

		    foreach(Clips\config('library_dir') as $lib_dir) {
				$out_dir = Clips\try_path($lib_dir);
				if($out_dir)
					break;
			}

			if($out_dir) {
				$out = Clips\path_join($out_dir, $name.'.php');
				$args[2] = $out;
				$this->output('Writing file %s.', $out);
			}
		}
		Compiler::cli($args) ;
	}	
}
