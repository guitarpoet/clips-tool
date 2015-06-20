<?php namespace Clips\DataSources; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Libraries\DataSource;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Clips\DataSourceException;

/**
 * @Clips\Library({"sqlParser", "fileCache"})
 */
class ClipsDataSource extends DataSource implements LoggerAwareInterface {

	public function __construct($config = null) {
		parent::__construct($config);
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	protected function init($config) {
		// Fixed the bug that when initializing this datasource, didn't run the enhance
		$this->tool = \Clips\get_clips_tool();
		// Get the underlying datasource
		$ds = \Clips\get_default($config, 'datasource');
		// Since this datasource will use an underlying datasource as the delegate, so must have
		// the datasource configuration set for this datasource, not try uing the first one
		if(!$ds) {
			throw new DataSourceException('No datasource configured for this datasource!');
		}

		$ds = $this->get($ds); // TODO : This will initialize all the datasource list again( the first one is in datasource itself, this will waste a little more time
		if(!$ds) {
			throw new DataSourceException('No datasource configured for this datasource!');
		}

		// Setting the underlying datasource
		$this->ds = $ds;

		// Sql Creator will use MySQL as default.
		$sqlCreator = \Clips\get_default($config, 'sql_creator',  'PHPSQLParser\\PHPSQLCreator');
		if(!class_exists($sqlCreator)) {
			throw new DataSourceException('No sql creator found!');
		}

		// Setting the sql creator
		$this->creator = $this->tool->create($sqlCreator);

		// Initializing the SQL filters
		$this->filters = array();
		foreach(\Clips\get_default($config, 'filters', array()) as $filter) {
			$f = $this->tool->library($filter, true, 'SqlFilter');
			if($f)
				$this->filters []= $f;
		}
	}

	/**
	 * Prepare the query, this operation will do the step like this:
	 *
	 * 1. Parse the sql
	 * 2. Filter the sql throught all the filters
	 * 3. Generate the filtered sql using sql creator
	 *
	 * The query must be the mysql prepared statement's syntax, and can have any
	 * bind args as the arguments of this function, like this:
	 * 
	 * <code>
	 * 		$sql, $arg1, $arg2
	 * </code>
	 *
	 * or this:
	 *
	 * <code>
	 * 		$sql, array($arg1, $arg2)
	 * </code>
	 */
	public function prepare() {
		$args = func_get_args();
		if($args) {
			$sql = array_shift($args);
			if(is_string($sql)) {
				if($args) {
					if(is_array($args[0])) {
						$bind_args = $args[0];
					}
					else {
						$bind_args = $args;
					}
				}
				$sql = $this->filter($sql);
				if($sql) {
					$ret = array($sql);
					if(isset($bind_args)) {
						$ret []= $bind_args;
					}
					return $ret;
				}
			}
		}
		return null;
	}

	protected function filter($sql) {
		$cache = 'sql_'.md5($sql).'.sql';
		if(!$this->filecache->exists($cache)) {
			$model = $this->sqlparser->parse($sql);
			foreach($this->filters as $f) {
				$model = $f->filter($model, $this->config);
			}
			$sql = $this->creator->create($model);
			$this->filecache->save($cache, $sql);
			return $sql;
		}
		else {
			return $this ->filecache->contents($cache);
		}
	}

	protected function doQuery($query, $args = array()) {
		if($this->ds) {
			$q = $this->prepare($query, $args);
			$query = array_shift($q);
			return $this->ds->doQuery($query, $q);
		}
		return null;
	}

	protected function doUpdate($id, $args) {
		if($this->ds) {
			$orig = $this->ds->context;
			$ret = $this->ds->update($id, $args);
			$this->ds->context = $orig;
			return $ret;
		}
		return null;
	}

	protected function doDelete($id) {
		if($this->ds) {
			$orig = $this->ds->context;
			$ret = $this->ds->delete($id);
			$this->ds->context = $orig;
			return $ret;
		}
		return null;
	}

	protected function doFetch($args) {
		if($this->ds) {
			$orig = $this->ds->context;
			$ret = $this->ds->fetch($args);
			$this->ds->context = $orig;
			return $ret;
		}
		return null;
	}

	protected function doClear() {
		if($this->ds) {
			$orig = $this->ds->context;
			$ret = $this->ds->doClear();
			$this->ds->context = $orig;
			return $ret;
		}
		return null;
	}

	protected function doIterate($query, $args, $callback, $context = array()) {
		throw new DataSourceException('Only support prepared statement!');
	}

	protected function doInsert($args) {
		if($this->ds) {
			$orig = $this->ds->context;
			$ret = $this->ds->insert($args);
			$this->ds->context = $orig;
			return $ret;
		}
		return null;
	}
}
