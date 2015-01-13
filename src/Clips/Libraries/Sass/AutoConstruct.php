<?php namespace Clips\Libraries\Sass; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * Auto add constructor invocation in every sass.
 *
 * Here is the usage, let's suppose there is a button widget's scss named button.scss
 * When precompiling, the auto construct plugin will search for init_button function in the content.
 * If found, will auto add the construction function invoke @include .init_button() to the content
 *
 * This function is extremely useful for theme based widgets, since the widget itself won't know anything
 * of the theme settings or variables, since it is in the variables.scss and can't be found or known until
 * compile time, so add the function invcation after all the scss code, will make this quite easy to write 
 * and maintain.
 *
 * @author Jack
 * @date Tue Jan 13 11:35:54 2015
 */
class AutoConstruct extends SassPlugin {
	public function suffix($compiler) {
		foreach($compiler->sasses as $s) {
			if(strpos($s, "string://") !== false) // Skip the string resource
				continue;

			$s = str_replace('.scss', '', $s);
			$basename = basename($s);
			$name = strtolower(str_replace('/', '_', $s));

			if($basename != $name) {
				$this->addConstruct($basename, $compiler);
			}
			$this->addConstruct($name, $compiler);
		}
	}

	protected function addConstruct($name, $compiler) {
		$the_name = 'init_'.$name;
		if(strpos($compiler->content, $the_name) !== FALSE) {
			$compiler->suffix .= '@include '.$the_name.'();'."\n";
		}
	}
	
}
