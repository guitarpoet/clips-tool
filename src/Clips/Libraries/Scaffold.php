<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

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

	/**
	 * Wrap the form field to fit form configuration generation template
	 */
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
		$nullable = \Clips\get_default($field, 'null');
		$input_type = \Clips\get_default($field, 'input_type');
		$required = false;

		$r = array();

		// Add the type as the default rule
		if($input_type) {
			$r []= array(
				'ffirst' => true,
				'key' => 'type',
				'value' => $input_type
			);
		}
		else {
			$r []= array(
				'ffirst' => true,
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

		// All foreign key and not null field will be required
		if(!$required && ($foreign_key || !$nullable))
			$r []= array('key' => 'required');

		$ret = array('label' => $label, 'rules' => $r, 'field' => $field['field']);
		if($state)
			$ret['state'] = $state;
		if($first)
			$ret['first'] = true;

		return $ret;
	}

	/**
	 * Turn the table configuration to form configuration
	 *
	 * @param table
	 * 		The table name
	 * @param options
	 * 		The options
	 * @param form_folder
	 * 		The form folder to store the configuration
	 * @param create (default true)
	 * 		If this configuration is create form or edit form
	 */
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

	protected function tableName($table) {
		if(\Clips\str_end_with($table, 's')) {
			// Remove the trailing s
			return substr($table, 0, strlen($table) - 1);
		}
		else
			return $table;
	}

	protected function tableToPagination($table, $options, $schema, $pagi_folder) {
		$name = $this->tableName($table);
		$data = array('from' => $table);
		$columns = array();
		$columns []= array(
			'first' => true,
			'fields' => array(
				array(
					'ffirst' => true,
					'key' => 'data',
					'value' => $table.'.id'
				),
				array(
					'key' => 'action',
					'value' => $name.'/show'
				),
				array(
					'key' => 'title',
					'value' => 'ID'
				)
			),
		);
		$joins = array();

		foreach($options as $col => $config) {
			if(!\Clips\get_default($config, 'pagination', true)) {
				continue;
			}
			$col_name = $table.'.'.$col;
			// By default the title of this columns is TableName ColumnName
			$title = \Clips\get_default($config, 'label', \Clips\to_camel($name).' '.\Clips\to_camel($col));
			$foreign_key = \Clips\get_default($config, 'foreign_key');

			$c = array();

			$c['fields'] = array(
				array(
					'ffirst' => true,
					'key' => 'data',
					'value' => $col_name
				),
				array(
					'key' => 'title',
					'value' => $title
				)
			);

			// Processing for foreign keys
			if($foreign_key) {
				$c['fields'] []= array(
					'key' => 'action',
					'value' => $this->tableName($foreign_key).'/show'
				);
				$c['fields'] []= array(
					'key' => 'refer',
					'value' => $foreign_key.'.id'
				);
				$joins []= array(
					'table' => $foreign_key,
					'left' => $col_name,
					'right' => $foreign_key.'.id'
				);

				// Finally, let's find the refer
				$foreign_table_config = \Clips\get_default($schema, $foreign_key);
				if($foreign_table_config) {
					foreach($foreign_table_config as $foreign_col => $fconfig) {
						if(\Clips\get_default($fconfig, 'refer')) {
							$c['fields'][0]['value'] = $foreign_key.'.'.$foreign_col;
						}
					}
				}
			}

			// Add the customize action
			$action = \Clips\get_default($config, 'action');
			if($action) {
				$c['action'] = $action;
			}

			$columns []= $c;
		}

		if($joins) {
			$joins[0]['first'] = true;
		}

		$file = \Clips\path_join($pagi_folder, $name.'.json');

		if(\Clips\try_path($file)) {
			echo "The pagination configuration for table $table exists at $file!".PHP_EOL;
		}
		else {
			echo "Creating config $file...".PHP_EOL;
			file_put_contents($file, \Clips\clips_out('pagination', array(
				'from' => $table,
				'columns' => $columns,
				'joins' => $joins
			), false));
		}
	}

	public function pagination($schema, $config) {
		$namespace = \Clips\config('namespace');
		$pagi_folder = \Clips\config('pagination_config_dir');
		$pagi_folder = $pagi_folder[0];
		$this->logger->debug('Using pagination folder {0}.', array($pagi_folder));

		foreach($schema as $table => $options) {
			if(\Clips\get_default($options, 'pagination') !== false) {
				$this->tableToPagination($table, $options, $schema, $pagi_folder);
			}
		}
	}

	public function controller($schema, $config) {
		$namespace = \Clips\config('namespace');
		foreach($schema as $table => $options) {
			$table_name = $this->tableName($table);
			$model_name = \Clips\to_camel($table_name);
			$controller_name = ucfirst($model_name).'Controller';
			$refer_name = strtolower($model_name);

			$models = array(
				array(
					'model' => lcfirst($model_name),
					'first' => true
				)
			);
			$refers = array();
			foreach($options as $col => $o) {
				$foreign_key = \Clips\get_default($o, 'foreign_key');
				if($foreign_key) {
					$models []= array('model' => $this->tableName($foreign_key));
					$refers []= array(
						'key' => $foreign_key,
						'model' => $this->tableName($foreign_key)
					);
				}
			}

			if($refers) {
				$refers[0]['first'] = true;
			}

			$file = 'application/Controllers/'.$controller_name.'.php';

			if(\Clips\try_path($file)) {
				echo "Controller $controller_name exists in file $file!".PHP_EOL;
				continue;
			}
			else {
				echo "Creating controller $controller_name...".PHP_EOL;
				file_put_contents($file, \Clips\clips_out('controller', array(
					'namespace' => $namespace[0],
					'controller_name' => $controller_name,
					'refer_name' => $refer_name,
					'refers' => $refers,
					'table_name' => $table_name,
					'table' => $table,
					'author' => $config->author,
					'version' => $config->version,
					'date' => \Clips\timestamp(),
					'models' => $models
				), false));
			}
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
