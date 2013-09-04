<?php

namespace Gameplay\Framework;

class PanelTransport {
	
	public $action;
	public $content;
	
	public function __construct($action, $content) {
		$this->action  = $action;
		$this->content = $content;
	}
	
}