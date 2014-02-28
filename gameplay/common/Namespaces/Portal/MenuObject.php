<?php

namespace Portal;

class MenuObject {
	
	public $title = '';
	public $link = '';
	public $description = '';
	
	public function render() {
		$retVal = ' ';
		
		$retVal .= "<li><a href='{$this->link}' title='{$this->description}'>{$this->title}</a></li>";
		
		return $retVal;
	}
	
}
