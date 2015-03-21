<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The pagination model, support the pagination through sql grammar
 *
 * @author Jack
 * @date Tue Feb 17 14:14:22 2015
 */
class Pagination {

	/**
	 * The table to select from
	 */
	public $from;

	/**
	 * The total records of this type in database
	 */
	public $dbtotal;

	/**
	 * The total records count
	 */
	public $total;

	/**
	 * The current record offset 
	 */
	public $offset;

	/**
	 * The total page item array length
	 */
	public $length;

	/**
	 * The items
	 */
	public $items;

	/**
	 * The join options
	 */
	public $joins = array();

	/**
	 * The order by parameters
	 */
	public $orderBy = array();

	/**
	 */
	public $groupBy = array();

	/**
	 * The where parameters
	 */
	public $where = array();

	/**
	 * The tag object for carrying data between queries
	 */
	public $tag = null; 

	/**
	 * The query object for query pagination using queries
	 *
	 * The query object has these fields (query, total_query, filtered_query)
	 */
	public $customized_query = null;

	public function __construct($total = 0, $offset = 0, $length = -1) {
		if($length != -1)
			$this->length = $length;
		else {
			$pagination_length = config('pagination_length');
			if($pagination_length) {
				$pagination_length = $pagination_length[0];
			}
			else {
				$pagination_length = 15;
			}
			$this->length = $pagination_length;
		}
		$this->offset = $offset;
	}

	public function first() {
		$this->offset = 0;
	}

	public function last() {
		$this->offset = ($this->totalCount() - 1) * $this->length;
	}

	public function page($page) {
		if($page <= 0 || $page > $this->totalCount()) {
			return false;
		}
		$this->offset = ($page - 1) * $this->length;
		return $page;
	}

	public function next() {
		if($this->hasNext()) {
			$this->offset = ($this->current() + 1) * $this->length;
			return $this->current();
		}
		return false;
	}

	public function prev() {
		if($this->hasPrev()) {
			$this->offset = ($this->current() - 1) * $this->length;
			return $this->current();
		}
		return false;
	}

	public function hasNext() {
		return $this->current() < $this->totalCount();
	}

	public function hasPrev() {
		return $this->offset - $this->length > 0;
	}

	public function totalCount() {
		if($this->length) {
			return ceil($this->total / $this->length);
		}
		return 0;
	}

	public function current() {
		if($this->length) {
			$ret = ceil($this->offset / $this->length);
			return $ret? $ret: 1;
		}
		return 0;
	}

	public function bundleFields() {
		return $this->_bundleFields;
	}

	/**
	 * Get the filtered columns using security engine
	 *
	 * @return
	 * 		The filtered columns
	 */
	public function columns() {
		if(!isset($this->columns))
			return null;

		if(isset($this->_filtered_columns))
			return $this->_filtered_columns;

		if(!isset($this->security)) {
			return $this->columns;
		}

		$this->_filtered_columns = array();
		foreach($this->columns as $col) {
			$c = copy_object($col);
			$c->name = get_default($this, 'name');
			$result = $this->security->test($c);
			if($result) { // This result has eject in it.
				log('Rejecting column [{0}] of pagination [{1}] for reason [{2}]', array($c->data, $c->name, $result[0]->reason));
				continue;
			}
			$this->_filtered_columns []= $col;
		}
		return $this->_filtered_columns;
	}

	public function fields() {
		$columns = $this->columns();
		if($columns) {
			$fields = array();
			$this->_bundleFields = array();
			foreach($columns as $i) {
				if(strpos($i->data, ' ') === false) {
					$fields []= $i->data.' as '.smooth($i->data);
				}
				else
				   	$fields []= $i->data; 

				if(isset($i->bundle)) {
					$this->_bundleFields []= $i;
				}

				if(isset($i->refer)) {
					if(strpos($i->refer, ' ') === false) {
						$fields []= $i->refer.' as '.smooth($i->refer);
					}
					else
						$fields []= $i->refer; 
				}
			}
			return $fields;
		}
		else
			return array('*');
	}

	public static function fromJson($json) {
		$obj = parse_json($json);
		if(isset($obj->where)) {
			$obj->where = (array)$obj->where;
		}
		if($obj)
			return copy_new($obj, "Clips\\Pagination");
		return null;
	}

	public function update($params) {
		$tool = &get_clips_tool();
		$tool->library('sql');
		$this->offset = $params['start'];
		$this->length = $params['length'];

		if(isset($params['search']))  {
			if(isset($params['search']['value']) && trim($params['search']['value'])) {
				$or = array();
				$or_value = $params['search']['value'];
			}
		}
		foreach($this->columns() as $col) {
			if(get_default($col, 'searchable', true)) {
				if(isset($or)) {
					$or[$col->data] = $or_value;
				}
			}
		}
		// Update the where configuration using request columns
		$i = 0;
		foreach($params['columns'] as $col) {

			if(isset($col['search'])) {
				$search = $col['search'];
				if($search['value']) {
					$field = $this->columns[$i];
					if(isset($field->refer)) {
						$f = $field->refer;
					}
					else {
						$f = $field->data;
					}
					if($search['regex'] && $search['regex'] != 'false') {
						$this->where []= Libraries\_like($f, $search['value']);
					}
					else
						$this->where[$f] = $search['value'];
				}
				$i++;
			}
		}

		if(isset($or)) {
			$this->where []= Libraries\_or($or);
		}

		$order = $params['order'];
		$arr = array();
		if($order) {
			foreach($order as $o) {
				$col = $params['columns'][$o['column']]['data'];
				$arr []= $col.' '.$o['dir'];
			}
		}
		$this->orderBy = $arr;
	}

	public function toJson() {
		$obj = copy_object($this);
		if(isset($obj->security)) {
			unset($obj->security);
			unset($obj->_filtered_columns);
		}
		return json_encode($obj);
	}
}
