<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed"); 

class SecurityItem {
	const ACTION = 'action';
	const FIELD = 'field';
	const COLUMN = 'column';

	public $type = SecurityItem::ACTION;
	public $name;
	public $content;
	/**
	 * @Clips\Multi
	 */
	public $params;

	public function __construct($item = null) {
		if($item) {
			if(valid_obj($item, 'Clips\\Interfaces\\Action')) { // If this item is action
				$this->name = $item->label();
				$this->content = $item->content();
				$this->params = $item->params();
			}
		}
	}
}
