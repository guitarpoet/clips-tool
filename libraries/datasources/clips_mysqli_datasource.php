<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Mysqli_Datasource extends Clips_Datasource {

	public $host = 'localhost';
	public $username = 'root';
	public $password = '';
	public $database = 'test';
	public $port = 3306;
	public $encoding = 'utf8';
	public $in_transaction = false;

	public function __construct($config = null) {
		parent::__construct($config);
	}

	protected function init($config) {
		foreach($config as $k => $v) {
			$this->$k = $v;
		}
		if(isset($config->table_prefix) && isset($config->context)) {
			$this->context = $config->table_prefix.$config->context;
		}
		$this->db = new mysqli($this->host, 
			$this->username, $this->password, $this->database, $this->port);

		$this->db->set_charset($this->encoding);

		if($this->db->connect_error) {
			throw new Exception($this->db->connect_error);
		}

		$tool = get_clips_tool();
		$sql = $tool->library('sql', false);
		$this->sql = new $sql();
		if(isset($config->table_prefix))
			$this->sql->table_prefix = $config->table_prefix;
	}

	protected function destroy() {
		$this->db->close();
	}

	public function fetchResult($stmt, $callback = null, $context = array()) {
		$stmt->store_result();

		$variables = array();
		$data = array();
		$meta = $stmt->result_metadata();

		while($field = $meta->fetch_field())
			$variables[] = &$data[$field->name];

		call_user_func_array(array($stmt, 'bind_result'), $variables);

		if(!$callback)
			$array = array();

		while($stmt->fetch()) {
			$tmp = array();

			foreach($data as $k=>$v)
				$tmp[$k] = $v;

			if(!$callback) {
				$array []= $tmp;
			}
			else {
				$callback((object) $tmp, $context);
			}
		}

		if(!$callback) {
			return $array;
		}
		return true;
	}

	protected function doQuery($query, $args = array()) {
		return $this->execute($query, $args, function($stmt, $context) {
			return $this->processResult($this->fetchResult($stmt));
		});
	}

	private function execute($sql, $args, $callback, $context = array()) {
		if(!$this->db) {
			throw new Exception('Didn\'t connect to database.');
		}

		$stmt = $this->db->prepare($sql);

		if($stmt) {
			if($args) {
				$params = array();
				$str = array();
				foreach($args as $arg) {
					$s = $arg;
					switch(gettype($arg)) {
					case 'integer':
						$str []= 'i';
						break;
					case 'double':
						$str []= 'd';
						break;
					case 'object':
					case 'array':
						$s = json_encode($arg);
					case 'string':
						$str []= 's';
						break;
					}
					$params []= &$s;
				}
				array_unshift($params, implode('', $str));
				call_user_func_array(array($stmt, 'bind_param'), $params); // Bind the qrgs
			}

			$stmt->execute();

			$ret = null;
			if(isset($callback) && is_callable($callback)) {
				$ret = $callback($stmt, $context);
			}

			$stmt->close();
			return $ret;
		}
		else
			throw new Exception($this->db->error);
	}

	protected function doIterate($query, $args, $callback, $context = array()) {
		$this->execute($query, $args, function($stmt, $context){
			$callback = $context['callback'];
			$c = $context['context'];
			$this->fetchResult($stmt, $callback, $c);
		}, array('callback' => $callback, 'context' => $context));
	}

	protected function processResult($result) {
		$ret = array();
		foreach($result as $obj) {
			$ret [] = (object) $obj;
		}
		return $ret;
	}

	protected function doInsert($args) {
		$sql = array('insert', 'into', $this->context, '(');
		$keys = array();
		$values = array();
		$data = array();
		foreach($args as $k => $v) {
			$keys []= $k;
			$values []= '?';
			$data []= $v;
		}
		$sql []= implode(', ', $keys);
		$sql []= ') values (';
		$sql []= implode(', ', $values);
		$sql []= ');';
		$sql []= 'select last_insert_id()';
		$sql = implode(' ', $sql);
		return $this->doQuery($sql, $data);
	}

	protected function doUpdate($id, $args) {
		$sql = array('update', $this->context, 'set');
		$keys = array();
		$values = array();
		foreach($args as $k => $v) {
			$keys []= $k.' = ?';
			$values []= $v;
		}
		$sql []= implode(', ', $keys);
		$sql []= 'where';
		$sql []= $this->idField(); 
		$sql []= '='; 
		$sql []= '?'; 
		$values []= $id;
		$sql = implode(' ', $sql);
		$this->doQuery($sql, $values);
		return true;
	}

	protected function doFetch($args) {
		if(isset($this->sql) && isset($this->context)) {
			$sql = $this->sql->select('*')->from($this->context)
				->where($args)->sql();
			switch(count($sql)) {
			case 0:
				throw new Exception('Can\'t do the query since no query generated!');
			case 1:
				return $this->doQuery($sql[0]);
			default:
				return $this->doQuery($sql[0], $sql[1]);
			}
		}
		throw new Exception('No context is set!');
	}

	public function doDelete($id) {
		if(isset($this->context)) {
			$this->doQuery('delete from '.$this->context.' where '.$this->idField().' = ?', $id);
		}
	}

	/**
	 * Start the batch execution using mysql's transaction support.
	 * Only start the transaction when the transaction is not opened, or
	 * just let the transaction handle all of this
	 */
	public function beginBatch() {
		if(isset($this->db) && !$this->in_transaction)
			$this->in_transaction = $this->db->begin_transaction();
	}

	public function endBatch() {
		if(isset($this->db) && $this->in_transaction) {
			$this->db->commit(); // If we are in the transaction, let's commit it
		}
	}
}
