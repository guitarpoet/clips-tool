<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Command;
use cebe\markdown\GithubMarkdown;

/**
 * This command will generate the output using markdown
 *
 * @author Jack
 * @date Sat Mar  7 19:30:40 2015
 *
 * @Clips\Library("markup")
 */
class MarkupCommand extends Command {

	public function execute($args) {
		if($args) {
			$file = $args[0];
			if(file_exists($file)) {
				$this->output($this->markup->render(file_get_contents($file)));
			}
			else {
				$this->error('The input file %s is not exits!', $file);
			}
		}
		else {
			$this->error('Please input the markdown file.');
		}
	}

}
