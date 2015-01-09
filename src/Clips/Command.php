<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Command {
	public function execute($args) {
	}

	public function getDepends() {
		$name = get_class($this);
		$reflection = new \Addendum\ReflectionAnnotatedClass($name);
		if($reflection->hasAnnotation('Depends')) {
			$a = $reflection->getAnnotation('Depends');
			return $a->value;
		}
		return array();
	}

	public function incre($value = 1) {
		$tool = &get_clips_tool();
		if(isset($tool->ProgressManager)) {
			$tool->ProgressManager->incre($value);
		}
	}

	public function progress($value) {
		$tool = &get_clips_tool();
		if(isset($tool->ProgressManager)) {
			$tool->ProgressManager->update($value);
		}
	}

	public function start($total = 100) {
		$tool = &get_clips_tool();
		if(isset($tool->ProgressManager)) {
			$tool->ProgressManager->start($total);
		}
	}
}
