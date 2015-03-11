<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\TreeNode;

class SimpleTreeNode implements TreeNode {

	public function __construct($data = array()) {
		if($data) {
			copy_object($data, $this); // Copy all the data from data to this

			$c = $this->children();

			$children_field = get_default($this, 'children_field', 'children');

			$this->$children_field = array(); // Reset the children field to empty array

			foreach($c as $child) {
				if(valid_obj($child, 'Clips\\Interfaces\\TreeNode')) { // If this is tree node
					$child->detach(); // Detach the child from its parent
				}
				else {
					$child = new SimpleTreeNode($child);
				}
				// Append the child
				$this->append($child);
			}
		}
	}

	public function childAt($index) {
		$c = $this->children();
		if($index >= 0 && count($c) > $index)
			return $c[$index];
		return null;
	}

	public function iterator($mode = 'width') {
		return new SimpleTreeNodeIterator($this, $mode);
	}

	public function nextSibling() {
		$p = $this->parent();
		if($p) {
			$index = $p->hasChild($this);
			return $p->childAt($index + 1);
		}
		return null;
	}

	public function prevSibling() {
		$p = $this->parent();
		if($p) {
			$index = $p->hasChild($this);
			return $p->childAt($index - 1);
		}
		return null;
	}

	protected function getSearcher() {
		if(!isset($this->_searcher)) {
			$this->_searcher = searcher();
		}
		return $this->_searcher;
	}

	public function label() {
		$label_field = get_default($this, 'label_field', 'label');
		if(isset($this->$label_field))
			return $this->$label_field; 
		return null;
	}

	public function parent() {
		$parent_field = get_default($this, 'parent_field', 'parent');
		if(isset($this->$parent_field))
			return $this->$parent_field;
		return null;
	}

	public function query($filter = null, $args = array(), array $alias = array()) {
		if($filter) {
			return $this->getSearcher()->tree($filter, $this, $args, $alias);
		}
		return $this->children();
	}

	public function hasChild(TreeNode $child) {
		$children_field = get_default($this, 'children_field', 'children');
		return array_search($child, $this->$children_field);
	}

	public function children($filter = null, $args = array(), $alias = array()) {
		$children_field = get_default($this, 'children_field', 'children');
		if(isset($this->$children_field)) {
			$c = $this->$children_field;
			if(!is_array($c)) {
				$this->$children_field = array($c);
			}

			if($filter) {
				return $this->getSearcher()->search($filter, $this, $args, $alias);
			}
			return $this->$children_field;
		}
		return array();
	}

	public function append(TreeNode $child, $index = -1) {
		if($this->hasChild($child) !== false)
			return false;

		// Setting the parent of child
		$parent_field = get_default($child, 'parent_field', 'parent');
		$child->$parent_field = $this;	

		// Adding the child
		$children_field = get_default($this, 'children_field', 'children');
		if($index < 0 || count($this->$children_field) < $index)
			array_push($this->$children_field, $child);
		else
			$this->$children_field = array_splice($this->$children_field, $index, 0, $child);

		return $child;
	}

	public function detach() {
		$parent = $this->parent();
		if($parent && $parent->hasChild($this) !== false) {
			$children_field = get_default($parent, 'children_field', 'children');
			$index = array_search($this, $parent->$children_field);
			$parent->$children_field = array_splice($parent->$children_field, $index, 1);
		}
		$parent_field = get_default($child, 'parent_field', 'parent');
		unset($this->$parent_field);
	}
}
