<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed"); 

use Clips\Interfaces\TreeNodeIterator;
use Clips\Interfaces\TreeNode;

class SimpleTreeNodeIterator implements TreeNodeIterator {

	const DEPTH = 'depth';
	const WIDTH = 'width';

	private $node;
	private $index;
	private $stack;
	private $current;
	private $mode;
	private $keys;

	public function __construct(TreeNode $node, $mode = 'width') {
		$this->node = $node;
		$this->current = $this->node;
		$this->mode = $mode;
		$this->stack = array($this->node);
	}

	public function rewind() {
		$this->current = $this->node;
		$this->stack = array($this->node);
	}

	public function current() {
		return $this->current;
	}

	public function key() {
		$keys = array($this->current->label());
		$parent = $this->current->parent();

		while($parent) {
			$keys []= $parent->label();
			$parent = $parent->parent();
		}
		$keys = array_reverse($keys);
		return implode(' / ', $keys);
	}

	public function next() {
		$orig_current = $this->current;
		while($this->stack && $orig_current == $this->current) {
			switch($this->mode) {
			case self::WIDTH:
				// Take the node using queue
				$this->current = array_shift($this->stack);
				$c = $this->current->children();
				if($c) {
					foreach($c as $i) { // Add all the children to stack
						$this->stack []= $i;
					}
				}
				break;
			case self::DEPTH:
				$this->current = array_pop($this->stack);
				$c = $this->current->children();
				if($c) {
					for($i = count($c) - 1; $i >= 0; $i--) {
						$this->stack []= $c[$i];
					}
				}
				break;
			}
		}
		if($orig_current == $this->current)
			$this->current = null;
	}

	public function valid() {
		return !!$this->current;
	}

	public function skip() {
	}
}
