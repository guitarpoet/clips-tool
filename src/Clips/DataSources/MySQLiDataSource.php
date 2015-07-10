<?php namespace Clips\DataSources; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The MySQLi DataSource, impmement the datasource functions using PHP's mysqli driver.
 *
 * @author Jack
 * @date Fri Feb 20 21:00:20 2015
 */
class MySQLiDataSource extends \Clips\Libraries\DataSource implements \Psr\Log\LoggerAwareInterface {
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
		$table_prefix = \Clips\get_default($config, 'table_prefix');
		$context = \Clips\get_default($config, 'context');
		if($table_prefix !== null && $context) {
			$this->context = $table_prefix.$context;
		}
		$this->db = new \mysqli($this->host, 
			$this->username, $this->password, $this->database, $this->port);

		$this->db->set_charset($this->encoding);

		if($this->db->connect_error) {
			throw new \Clips\DataSourceException($this->db->connect_error);
		}

		$this->sql = new \Clips\Libraries\Sql(); // Should we have a better idea?
		if($table_prefix !== null) {
			$this->sql->table_prefix = $table_prefix;
			$this->table_prefix = $table_prefix;
		}
	}

	protected function destroy() {
		$this->db->close();
	}

	public function fetchResult($stmt, $callback = null, $context = array(), $store = false) {
		if($store)
			$stmt->store_result();

		if(!$callback)
			$array = array();

		if(method_exists($stmt, 'get_result')) {
			$stmt->execute();
			$result = $stmt->get_result();
			while($obj = $result->fetch_object()) {
				if(!$callback) {
					$array []= $obj;
				}
				else {
					$callback($obj, $context);
				}
			}
		}
		else {
			$variables = array();
			$data = array();
			$meta = $stmt->result_metadata();

			while($field = $meta->fetch_field()) {
				$variables[] = &$data[$field->name];
			}

			call_user_func_array(array($stmt, 'bind_result'), $variables);

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
		}

		if(!$callback) {
			return $array;
		}

		return true;
	}

	protected function doQuery($query, $args = array()) {
		return $this->execute($query, $args, function($stmt, $self) {
			return $self->processResult($self->fetchResult($stmt));
		}, $this);
	}

	private function execute($sql, $args = array(), $callback = null, $context = array()) {
		if(!$this->db) {
			throw new \Clips\DataSourceException('Didn\'t connect to database.');
		}

		$this->logger->debug('Executing sql [{0}] with args', array($sql, $args));

		$stmt = $this->db->prepare($sql);

		if($stmt) {
			if($args) {
                $params = array();
                $str = array();                                                                          
                $i = 0;
                foreach($args as $arg) {                                                                 
                    $name = 'var'.$i++;
                    $$name = $arg;
                    switch(gettype($arg)) {                                                              
                    case 'integer':                                                                      
                        $str []= 'i';
                        break;
                    case 'double':                                                                       
                        $str []= 'd';
                        break;                                                                           
                    case 'object':                                                                       
                    case 'array':                                                                        
                        $$name = json_encode($arg);                                                      
                    case 'string':                                                                       
                        $str []= 's';                                                                    
                        $$name = $arg;                                                                   
                        break;                                                                           
                    }
                    $params []= &$$name;
                }
                array_unshift($params, implode('', $str));
                call_user_func_array(array($stmt, 'bind_param'), $params); // Bind the qrgs
            }

			if(!$stmt->execute()) {
				throw new \Clips\DataSourceException($this->db->error);
			}
			else {
				$ret = null;
				if(isset($callback) && is_callable($callback)) {
					$ret = $callback($stmt, $context);
				}

				$stmt->close();
				return $ret;
			}
		}
		else
			throw new \Clips\DataSourceException($this->db->error);
	}

	protected function doIterate($query, $args, $callback, $context = array()) {
		$this->execute($query, $args, function($stmt, $context){
			$callback = $context['callback'];
			$self = $context['self'];
			$c = $context['context'];
			$self->fetchResult($stmt, $callback, $c);
		}, array('self' => $this, 'callback' => $callback, 'context' => $context));
	}

	public function processResult($result) {
		if(!$result)
			return array();
		$ret = array();
		foreach($result as $obj) {
			$ret [] = (object) $obj;
		}
		return $ret;
	}

	protected function doInsert($args) {
		if(isset($this->table_prefix)) {
			$sql = array('insert', 'into', $this->table_prefix.$this->context, '(');
		}
		else {
			$sql = array('insert', 'into', $this->context, '(');
		}
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
		$sql []= ')';
		$sql = implode(' ', $sql);
		$this->execute($sql, $data);
		return $this->db->insert_id;
	}

	protected function doUpdate($id, $args) {
		if(isset($this->table_prefix)) {
			$sql = array('update', $this->table_prefix.$this->context, 'set');
		}
		else {
			$sql = array('update', $this->context, 'set');
		}
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
		$this->execute($sql, $values);
		return true;
	}

	protected function doFetch($args) {
		if(isset($this->sql) && isset($this->context)) {
			$sql = $this->sql->select('*')->from($this->context)
				->where($args)->sql();
			switch(count($sql)) {
			case 0:
				throw new \Exception('Can\'t do the query since no query generated!');
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
			if(isset($this->table_prefix)) {
				$this->execute('delete from '.$this->table_prefix.$this->context.' where '.$this->idField().' = ?', array($id));
			}
			else {
				$this->execute('delete from '.$this->context.' where '.$this->idField().' = ?', array($id));
			}
		}
	}

	public function doClear() {
		if(isset($this->context)) {
			if(isset($this->table_prefix)) {
				$this->execute('delete from '.$this->table_prefix.$this->context);
			}
			else {
				$this->execute('delete from '.$this->context);
			}
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

	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}
}
