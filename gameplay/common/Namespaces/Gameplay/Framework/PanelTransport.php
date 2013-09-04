<?php

namespace Gameplay\Framework;

class PanelTransport {

	/**
	 * @var string
	 */
	public $action;

	/**
	 * @var string
	 */
	public $content;
	
	/**
	 * @var boolean
	 */
	public $rendered;

	/**
	 * @param string $action
	 * @param string $content
	 */
	public function __construct($action, $content, $rendered) {
		$this->action  	= $action;
		$this->content 	= $content;
		$this->rendered = $rendered;
	}

}