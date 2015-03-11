<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed"); 

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
			else if(valid_obj($item, 'Clips\\FormField')) { // If this item is form field
				$this->type = self::FIELD;
				$this->name = get_default($item, 'form');
				$this->content = get_default($item, 'name');
				$this->params = array(get_default($item, 'value'));
			}
			else { // This must be the column of pagination
				$this->type = self::COLUMN;
				$this->name = get_default($item, 'name');
				$this->content = get_default($item, 'data');
			}
		}
	}
}
