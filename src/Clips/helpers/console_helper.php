<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed"); 

function interactive($config, $callback = null) {
	if(is_string($config)) {
		// This is the config location
		$conf_dir = clips_config('config_dir');
		foreach($conf_dir as $dir) {
			$p = try_path(path_join($dir, $config.'.json'));
			if($p)
				break;
		}
		$config = parse_json(file_get_contents($p));
	}

	$prompt = get_default($config, 'prompt', '$');

	if(isset($config->header)) {
		echo $config->header."\n";
	}

	$tool = &get_clips_tool();
	$ret = array();

	$validator = new Validator();

	for($i = 0; $i < count($config->steps); $i++) {
		$step = $config->steps[$i];
		$operations = array();
		$default = null;
		if(isset($step->field)) {

			if(isset($step->default)) {
				$default = $step->default;
				if(strpos($default, '!') === 0) {
					$default = call_user_func('\\'.str_replace('!', '', $default));
				}
			}

			if(isset($step->prompt))
				$field = readline($step->prompt.(isset($default)?' ('.$default.') ':' '));
			else
				$field = readline($prompt.isset($default)?' ('.$default.') ':' ');

			if(!$field) {
				if(isset($default))
					$field = $default;
			}

			if(isset($step->rules)) {
				// TODO Add the validation
			}
			$ret[$step->field] = $field;
		}
		else {
			if(isset($step->confirm)) {
				// This is the confirm step
				if(isset($step->confirm->options)) {
					$options = $step->confirm->options;
				}
				else {
					$options = array('yes', 'no');
				}

				if(isset($step->prompt))
					$confirm = readline($step->prompt.' ['.implode(', ', $options).']'.' ');
				else
					$confirm = readline($prompt.' ');

				if(!is_array($step->confirm)) {
					$result_array = array('', 'y', 'Y', 'yes', 'Yes');
				}
				else
					$result_array = $step->confirm;

				if(array_search($confirm, $result_array) !== false) {
					$condition = 'confirm';
				}
				else {
					$condition = 'cancel';
				}

				if(isset($step->condition)) {
					if(isset($step->condition->$condition))
						$operations = array_merge($operations, $step->condition->$condition);
				}
			}
			else {
				echo $step->prompt."\n"; // Just print the prompt
			}
		}

		if(isset($step->jump)) {
			$operations []= (object) array('jump' => $step->jump);
		}

		if(isset($step->call)) {
			$operations []= (object) array('call' => $step->call);
		}

		if($operations) {
			foreach($operations as $operation) {
				if(isset($operation->jump)) {
					$i = $operation->jump - 1;
				}
				if($callback && isset($operation->call)) {
					\call_user_func_array(array($callback, $operation->call), array($ret));
				}
			}
		}
	}
	return (object) $ret;
}
