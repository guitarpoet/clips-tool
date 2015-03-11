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
			$fields []= array(
				'field' => 'id',
				'label' => 'ID',
				'state' => 'readonly',
				'first' => true,
				'rules' => array(
					array('key' => 'type', 'value' => 'number', 'rfirst' => true),
					array('key' => 'required')
				)
			);
			$first = false;
		}

		foreach($options as $field => $config) {
			$f = array();
			if($first) {
				$f['first'] = true;
				$first = false;
			}

			$f['type'] = \Clips\get_default($config, 'type', 'number');
			$f['field'] = $field;
			$f['label'] = \Clips\get_default($config, 'label', \Clips\to_camel($field));
			$fields []= $f;
		}
		echo $file;
		echo \Clips\clips_out('form', $fields);
	}

	public function form($schema, $config) {
		$namespace = \Clips\config('namespace');

		$form_folder = \Clips\config('form_config_dir');
		$form_folder = $form_folder[0];
		$this->logger->debug('Using form folder {0}.', array($form_folder));

		foreach($schema as $table => $options) {
			$this->tableToForm($table, $options, $form_folder);
			$this->tableToForm($table, $options, $form_folder, false);
		}
	}
}
