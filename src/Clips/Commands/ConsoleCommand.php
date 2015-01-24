<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Libraries\ConsoleBase;
use PhpParser\Parser;
use PhpParser\Lexer\Emulative;
use PhpParser\Error;

class PHPConsole extends ConsoleBase {
	public function __construct() {
		$this->parser = new Parser(new Emulative);
	}

	protected function prompt($continue = false) {
		if($continue)
			return '... ';
		return 'php$ ';
	}

	protected function doRun($line) {
		echo eval($line.';')."\n";
	}

	protected function isComplete($line) {
		try {
			($this->parser->parse('<?php '.$line.';'));
			return true;
		} catch (Error $e) {
			return false;
		}
	}
}

class ConsoleCommand extends \Clips\Command {

	public function __construct() {
		parent::__construct();
		$this->console = new PHPConsole();
		
	}


	public function execute($args) {
		$this->console->console();
	}
}
