<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Command;

/**
 * @author Jack
 * @date Sun Mar  8 19:20:57 2015
 *
 * @Clips\Object("frameworkMeta")
 */
class VersionCommand extends Command {

	public function execute($args) {
		$meta = $this->frameworkmeta;
		if($meta->branch == 'master') {
			$meta->branch = 'dev-master';
		}
		$meta->lastCommitterDate = $meta->lastCommitterDate->format(\DateTime::ISO8601);
		\Clips\clips_out("string://Framework Version: {{branch}} ({{commit}})\nLast Update Date: {{lastCommitterDate}}", $meta);
	}
}
