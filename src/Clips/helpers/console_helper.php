<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed"); 

/**
 * The interactive wizzard function, will take the options to run.
 *
 * The options is something like this(the generate widget option):
 *
 * <code>
 * {
 * 	"header": "Clips Widget Generator v1.0",
 * 	"steps": [
 * 		{
 * 			"prompt": "The name of the widget:",
 * 			"field": "widget",
 * 			"rules": {
 * 				"minlength": 4,
 * 				"maxlength": 10
 * 			}
 * 		},
 * 		{
 * 			"prompt": "Widget Author:",
 * 			"field": "author",
 * 			"default": "!Clips\\current_user"
 * 		},
 * 		{
 * 			"prompt": "Widget Version:",
 * 			"default": "1.0",
 * 			"field": "version"
 * 		},
 * 		{
 * 			"prompt": "Widget Doc:",
 * 			"field": "doc"
 * 		},
 * 		{
 * 			"prompt": "The widget configuration is:",
 * 			"call": "dump"
 * 		},
 * 		{
 * 			"prompt": "Are you sure:",
 * 			"confirm": true,
 * 			"condition": {
 * 				"cancel": [{"jump": 0}]
 * 			}
 * 		}
 * 	]
 * }
 * </code>
 *
 * Concept in details:
 *
 * 1. Step: The interactive step, can be 2 types [step, confirm]
 * 2. Operation: Only support 2 kind of operations by this version call and jump, for call
 * 	operation, will call the method from callback, for jump operation, will jump to the step
 * 	it set, like this {"jump":0}
 * 3. Confirm Step: This step will prompt a question to user, and let the user to choose,
 * 	use the condition part to add operation to the interactive console
 * 4. Field: If step has this settings, will prompt to get the user input using readline, and set
 * 	the input as field of the return object.
 * 5. Default: If no input is there, use this as default value, support method call, like this !time,
 * 	do not support arguments for this version
 */
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
