<?php

namespace Gameplay\Panel;

use Gameplay\Framework\PanelTransport;

abstract class Simple extends Base {

	protected $panelTag 	= "empty";
	protected $retVal	 	= "";
	protected $userID		= null;
	protected $language 	= 'pl';
	protected $rendered 	= false;

	/**
	 * Add new content to panel
	 * @param string $sValue
	 */
	public function add($sValue) {
		$this->rendered = true;
		$this->retVal 	.= $sValue;
	}

	/**
	 * Clear panel content
	 */
	public function clear() {
		$this->rendered = true;
		$this->retVal 	= '';
	}

	/**
	 * Method return PanelTransport object for its rendered content
	 * @return PanelTransport
	 */
	public function getTransport() {
		return new PanelTransport(null, $this->encodeOutput(), $this->rendered);
	}

}