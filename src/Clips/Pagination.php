<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

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
			$pagination_length = clips_config('pagination_length');
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

	public function fields() {
		if(isset($this->columns)) {
			$fields = array();
			$this->_bundleFields = array();
			foreach($this->columns as $i) {
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
		if($obj)
			return copy_new($obj, "Clips\\Pagination");
		return null;
	}

	public function update($params) {
		clips_library('sql');
		$this->offset = $params['start'];
		$this->length = $params['length'];

		// Update the where configuration using request columns
		$i = 0;
		foreach($params['columns'] as $col) {
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
		return json_encode($this);
	}
}
