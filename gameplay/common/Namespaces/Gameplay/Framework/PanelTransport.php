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

	private function encode($content) {
		// 		return base64_encode($this->retVal);
		return $content;
	}

	private function translate($content) {
		return \TranslateController::translate($content);
	}

	/**
	 * @param string $action
	 * @param string $content
	 */
	public function __construct($action, $content, $rendered) {
		$this->action  	= $action;
		$this->content 	= $this->encode($this->translate($content));
		$this->rendered = $rendered;
	}

}