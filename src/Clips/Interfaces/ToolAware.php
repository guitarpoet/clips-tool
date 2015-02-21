<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This interface indicates that this obejct should set clips tool's reference when enhanced
 *
 * @author Jack
 * @date Sat Feb 21 11:58:50 2015
 */
interface ToolAware {
	public function setTool($tool);
}
