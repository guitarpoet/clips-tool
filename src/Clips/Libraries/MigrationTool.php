<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The helper library for Phinx migration, will take a json configuration file to make the migration
 *
 * @author Jack
 * @date Sat Feb 21 12:50:37 2015
 */
class MigrationTool {

	public function insert($migration, $table, $data) {
		$ret = array();
		foreach($data as $d) {
			$sql = array('insert', 'into', $table, '(');
			$keys = array();
			$values = array();
			foreach($d as $k => $v) {
				$keys []= $k;
				$values []= '"'.$v.'"';
			}
			$sql []= implode(', ', $keys);
			$sql []= ') values (';
			$sql []= implode(', ', $values);
			$sql []= ')';
			$sql = implode(' ', $sql);
			$ret []= $migration->execute($sql, $data);
		}
		return $ret;
	}

	public function up($migration, $config) {
		foreach($config as $name => $options) {
			$table = $migration->table($name);
			$keys = array();
			$foreign_keys = array();

			// Add the columns
			foreach($options as $colname => $colopts) {
				if(!is_array($colopts))
					$colopts = (array) $colopts;

				$type = \Clips\get_default($colopts, 'type', 'integer');
				$opts = \Clips\get_default($colopts, 'options', array());

				if(isset($colopts['key']) && $colopts['key'])
					$keys []= $colname;

				if(isset($colopts['foreign_key'])) {
					switch(count($colopts['foreign_key'])) {
					case 1: // Only the table name, using id as the reference field
						$foreign_keys []= array($colname, $colopts['foreign_key'], "id");
						break;
					case 2:
						$foreign_keys []= array($colname, $colopts['foreign_key'][0], $colopts['foreign_key'][1]);
						break;
					}
				}	

				$table->addColumn($colname, $type, (array) $opts);
			}

			$table->addIndex($keys);

			foreach($foreign_keys as $fk) {
				$table->addForeignKey($fk[0], $fk[1], $fk[2]);
			}

			$table->create();
		}
	}

	public function down($migration, $config) {
		foreach(array_reverse(array_keys((array) $config)) as $name) {
			$migration->dropTable($name);
		}		
	}
}
