<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\BaseService;

/**
 * The scaffold generator
 *
 * @author Jack
 * @date Wed Mar 11 08:44:45 2015
 */
class Scaffold extends BaseService {

	public function toCamelName($name) {
		$model_name = \Clips\to_camel($name);
		return substr($model_name, 0, strlen($model_name) - 1);
	}

	/**
	 * Generate the model based on the schema
	 */
	public function model($schema, $config) {
		$namespace = \Clips\config('namespace');
		foreach($schema as $name => $options) {
			$model_name = $this->toCamelName($name).'Model';
			$p = 'application/Models/'.$model_name.'.php';
			if(\Clips\try_path($p)) {
				echo "Model $model_name is exists at $p, skipping...\n";
			}
			else {
				file_put_contents($p, \Clips\clips_out('model', array(
					'date' => \Clips\timestamp(),
					'doc' => "Model to manipulate table ".$name,
					'author' => $config->author,
					'version' => $config->version,
					'name' => $model_name,
					'namespace' => $namespace[0]
				), false));
			}
		}
	}

	protected function wrapField($field) {
		// The default type is number
		$type = \Clips\get_default($field, 'type', 'number');
		// The default label of the field is the camel form of the field
		$label = \Clips\get_default($field, 'label', \Clips\to_camel($field['field']));

		$rules = \Clips\get_default($field, 'rules', array());

		$options = \Clips\get_default($field, 'options');

		$state = \Clips\get_default($field, 'state');

		$first = \Clips\get_default($field, 'first');

		$foreign_key = \Clips\get_default($field, 'foreign_key');

		$input_type = \Clips\get_default($field, 'input_type');

		$required = false;

		$r = array();


		// Add the type as the default rule
		if($input_type) {
			$r []= array(
				'first' => true,
				'key' => 'type',
				'value' => $input_type
			);
		}
		else {
			$r []= array(
				'first' => true,
				'key' => 'type',
				'value' => $type
			);
		}

		if($options) {
			$limit = \Clips\get_default($options, 'limit');
			if($limit) {
				$r []= array(
					'key' => 'maxlengh',
					'value' => $limit
				);
			}
		}

		foreach($rules as $key => $rule) {
			if($rule == 'required')
				$required = true;


			// Add each rule to it
			if(is_numeric($key)) {
				// This is only the value
				$r []= array(
					'key' => $rule
				);
			}
			else {
				$r []= array(
					'key' => $key,
					'value' => $rule
				);
			}
		}

		// All foreign key will be required
		if(!$required && $foreign_key)
			$r []= array('key' => 'required');

		$ret = array('label' => $label, 'rules' => $r, 'field' => $field['field']);
		if($state)
			$ret['state'] = $state;
		if($first)
			$ret['first'] = true;

		return $ret;
	}

	protected function tableToForm($table, $options, $form_folder, $create = true) {
		if(\Clips\str_end_with($table, 's')) {
			// Remove the trailing s
			$name = substr($table, 0, strlen($table) - 1);
		}
		else
			$name = $table;

		if($create) {
			$name .= '_create.json';
		}
		else {
			$name .= '_edit.json';
		}

		$file = \Clips\path_join($form_folder, $name);
		if(\Clips\try_path($file)) {
			echo "Form file exists at $file, skipping".PHP_EOL;
			return false;
		}

		$fields = array();

		if($create) {
			$first = true;
		}
		else {
			// This is edit, add id to it
			$fields []= $this->wrapField(
				array(
					'field' => 'id',
					'label' => 'ID',
					'state' => 'readonly',
					'first' => true,
					'type' => 'number',
					'rules' => array('required')
				)
			);
			$first = false;
		}

		foreach($options as $field => $config) {
			if($first) {
				$config['first'] = true;
				$first = false;
			}
			$config['field'] = $field;
			$fields []= $this->wrapField($config);
		}
		$p = \Clips\try_path($file);

		if(\Clips\try_path($file)) {
			echo "The form configuration for table $table exists at $file!".PHP_EOL;
		}
		else {
			echo "Creating config $file...".PHP_EOL;
			file_put_contents($file, \Clips\clips_out('form', $fields, false));
		}
	}

	public function form($schema, $config) {
		$namespace = \Clips\config('namespace');

		$form_folder = \Clips\config('form_config_dir');
		$form_folder = $form_folder[0];
		$this->logger->debug('Using form folder {0}.', array($form_folder));

		foreach($schema as $table => $options) {
			if(\Clips\get_default($options, 'form') !== false) {
				$this->tableToForm($table, $options, $form_folder);
				$this->tableToForm($table, $options, $form_folder, false);
			}
		}
	}
}
