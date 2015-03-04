<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The interface that all tree node must impmement
 *
 * @author Jack
 * @date Fri Feb 27 22:43:18 2015
 */
interface TreeNode {

	/**
	 * Get the label of this node
	 *
	 * @return string
	 * 		The label of this node
	 */
	public function label();

	/**
	 * Get the parent of this node, if no parent is there,
	 * just return null
	 *
	 * @return TreeNode
	 * 		The parent of this node
	 */
	public function parent();

	/**
	 * Query all the children using filter pattern
	 * The filter pattern is little same as jQuery's match pattern.
	 * Filter pattern will use The class name of the node as the tagname, such as:
	 *
	 * <code>Clips\ActionNode</code>
	 *
	 * Filter can use the fields to match too, like this:
	 *
	 * <code>
	 * 		Clips\Action[name=test label=Test]
	 * </code>
	 *
	 * Filter can also using comma as the or operator, like this:
	 *
	 * <code>
	 * 		Clips\Action[name=test], Clips\Action[name="test again"]
	 * </code>
	 *
	 * For parent or children, can use > filter operation:
	 *
	 * <code>
	 * 		Clips\Action[status=active] > Clips\Action
	 * </code>
	 *
	 * Sometimes, the class name or the value is a little long to write.
	 * You can set the second parameter as the alias map, and alias can be
	 * referenced like this:
	 *
	 * <code>
	 * 		$alias = array('action' => 'Clips\\Action');
	 * 		$node->query('$action[status=?] > *');
	 * </code>
	 *
	 * @param filter
	 * 		The filter string
	 * @param args
	 * 		The args of the query
	 * @param alias
	 * 		The alias array
	 */
	public function query($filter = null, $args = array(), array $alias = array());

	/**
	 * Get all the children of this node using filter pattern.
	 * The filter pattern is little same as jQuery's match pattern.
	 * You can using query patterns like below for this method:
	 *
	 * <code>
	 * 		Clips\Action[name=Test], *[name=Demo]
	 * </code>
	 *
	 * @param filter
	 * 		The filter pattern for get the children,
	 * @param args
	 * 		The filter args
	 * @param alias
	 * 		The filter aliases
	 * @return Array
	 * 		The children of this Node
	 */
	public function children($filter = null, $args = array(), $alias = array());

	/**
	 * Get the child at index
	 */
	public function childAt($index);

	/**
	 * Get the next sibling, if there is no sibling, return null
	 */
	public function nextSibling();

	/**
	 * Get the previous sibling, if there is no sibling, return null
	 */
	public function prevSibling();

	/**
	 * Append the child TreeNode
	 */
	public function append(TreeNode $child, $index = -1);

	/**
	 * Detach the node from parent
	 */
	public function detach();

	/**
	 * Test if has this child, if not exists will return false
	 * But, may return the index of the child, so please check this return
	 * using === false
	 */
	public function hasChild(TreeNode $child);
}
