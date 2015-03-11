<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * This interface indicates that the object needs clips engine. Clips tool should
 * set the clips engine to it when enhancing it.
 *
 * @author Jack
 * @date Sat Feb 21 11:56:34 2015
 */
interface ClipsAware {
	public function setClips($clips);
}
