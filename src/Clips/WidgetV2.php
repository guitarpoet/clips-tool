<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * This is the version 2 widget base class. The version 2 widget will support speed up functions:
 *
 * If the widget use WidgetV2 as the base class, all the WidgetV2's widget's initialization 
 * will be deffered to widget filter.
 *
 * If didn't setup the trigger configuration of debugWidget, the widget filter will try to load
 * the configurations from cache than initilizing the widget.
 *
 * But, if there is no configuration in the cache, the widget filter will init all the widget one by
 * one, and store the configuration to the cache.
 *
 * @author Jack
 * @version 1.1
 * @date Sat Jun 13 14:26:05 2015
 */
class WidgetV2 extends Widget {
	public function init() {
		context('widgetsv2', $this, true); // Add this widget to widgetsv2 for speedup
	}

	protected function initDepends() {
		$depends = get_default($this->config, 'depends');
		if($depends) {
			if(!is_array($depends)) {
				$depends = array($depends);
			}
			foreach($depends as $d) {
				$w = $this->tool->widget($d);
				if(valid_obj($w, 'Clips\\WidgetV2')) { // Call the init function to force init the widget
					$w->init_v2();
				}
			}
        }
	}

	public function init_v2() {
		parent::init();
	}
}
