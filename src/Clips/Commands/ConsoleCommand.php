<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

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

	protected function php($line) {
		return '<?php '.$this->fixEnd($line);
	}

	protected function fixEnd($line) {
		if(\Clips\str_end_with($line, ';'))
			return $line;
		return $line.';';
	}

	protected function doRun($line) {
		$tool = &\Clips\get_clips_tool();
		$script = 'clips_console_internal.php';
		file_put_contents($script, $this->php($line));
		ob_start();
		include($script);
		$output = ob_get_contents();
		ob_end_clean();
		unlink($script);
		echo $output."\n";	
	}

	protected function isComplete($line) {
		try {
			($this->parser->parse($this->php($line)));
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
