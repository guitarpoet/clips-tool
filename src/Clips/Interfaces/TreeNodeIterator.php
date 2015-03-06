<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The iterator to iterate the tree
 *
 * @author Jack
 * @date Wed Mar  4 18:22:31 2015
 */
interface TreeNodeIterator extends \Iterator {

	/**
	 * Skip this node
	 */
	public function skip();
}
