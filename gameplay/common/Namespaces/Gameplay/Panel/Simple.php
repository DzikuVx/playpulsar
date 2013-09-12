<?php

namespace Gameplay\Panel;

use Gameplay\Framework\PanelTransport;

abstract class Simple extends Base {

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
		return new PanelTransport(null, $this->encodeOutput(), $this->rendered, $this->aParams);
	}

}