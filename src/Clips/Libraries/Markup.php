<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\BaseService;
use cebe\markdown\Markdown;
use cebe\markdown\MarkdownExtra;
use cebe\markdown\GithubMarkdown;

/**
 * The markup engine, using github markdown syntax as default
 *
 * @author Jack
 * @date Sat Mar  7 21:12:04 2015
 */
class Markup extends BaseService {

	const TRADITIONAL = 'traditional';
	const GITHUB = 'github';
	const EXTRA = 'extra';

	protected function get($flavor) {
		if(isset($this->$flavor))
			return $this->$flavor;

		switch($flavor) {
		case self::TRADITIONAL:
			$this->$flavor = new Markdown();
			break;
		case self::GITHUB:
			$this->$flavor = new GithubMarkdown();
			break;
		case self::EXTRA:
			$this->$flavor = new MarkdownExtra();
			break;
		}

		return $this->$flavor;
	}

	/**
	 * Render the markdown
	 *
	 * @param markdown
	 * 		The markdown string
	 * @param flavor (default github)
	 * 		The flavor of the markdown
	 */
	public function render($markup, $flavor = 'github') {
		return $this->get($flavor)->parse($markup);
	}
}
