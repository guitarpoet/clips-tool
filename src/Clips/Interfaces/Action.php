<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * Action is the heart of the wiget and toolbar.
 * The action object is a tree node. It can have as many subaction nodes as it wants.
 *
 * The navigation and toolbar can take use of the tree node function to manipulate the 
 * action and subactions(use subaction as subnavigation, for example).
 *
 * @author Jack
 * @date Thu Mar  5 19:38:58 2015
 */
interface Action extends TreeNode {

	const CLIENT = 'client';
	const SERVER = 'server';
	const EXTERNAL = 'external';

	/**
	 * The type of the action, can be client ,server or external.
	 */
	public function type();

	/**
	 * Test if the action is active
	 */
	public function active();

	/**
	 * The content of the action, will be like this:
	 *
	 * client => JavaScript
	 * server => Uri
	 * external => The external url
	 */
	public function content();


	/**
	 * The parameters for this action
	 *
	 * client => The data attributes in the tag
	 * server / external => The request parameters
	 */
	public function params();
}
