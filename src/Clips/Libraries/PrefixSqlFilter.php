<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\SqlFilter;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * The sql filter that filters the sql model to add the table prefix.
 *
 * @author Jack
 * @date Fri Jun 19 14:50:14 2015
 * @version 1.1
 */
class PrefixSqlFilter implements SqlFilter, LoggerAwareInterface {
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function filter($sql, $config) {
		if(is_array($sql)) {
			$this->tables = array();
			$this->aliases = array();
			$this->scan($sql);
			$prefix = \Clips\get_default($config, 'table_prefix', '');
			return $this->prefix($sql, $prefix);
		}
		return $sql;
	}

	protected function scan($model) {
		if(is_array($model)) {
			if(isset($model['table'])) {
				$this->tables []= $model['table'];

				if(isset($model['alias'])) {
					if(isset($model['alias']['name'])) {
						$this->aliases []= $model['alias']['name'];
					}
				}
			}
			foreach($model as $k => $v) {
				$this->scan($v);
			}
		}
	}

	protected function prefix($model, $prefix) {
		if(is_array($model)) {
			// Yes, we need to prefix the model
			if(isset($model['table'])) {
				$model['table'] = $prefix.$model['table'];

				if(isset($model['alias'])) {
					if(isset($model['alias']['name'])) {
						$this->aliases []= $model['alias']['name'];
					}
				}
			}

			// OK, let's try the children to find table
			foreach($model as $k => $v) {
				$model[$k] = $this->prefix($v, $prefix);
			}

			if(isset($model['expr_type']) && $model['expr_type'] == 'colref') {
				if(isset($model['no_quotes'])) {
					$nq = $model['no_quotes'];
					if(isset($nq['parts'])) {
						$parts = $nq['parts'];
						$table = $parts[0];
						if(array_search($table, $this->tables) !== false) {
							// Yes, this is a table
							if(array_search($table, $this->aliases) === false) {
								$model['no_quotes']['parts'][0] = $prefix.$table;
								$model['base_expr'] = implode('.', $model['no_quotes']['parts']);
							}
						}

					}
				}
			}
		}
		return $model;
	}
}
