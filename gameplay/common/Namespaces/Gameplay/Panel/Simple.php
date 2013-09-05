<?php

namespace Gameplay\Panel;

use Gameplay\Framework\PanelTransport;

abstract class Simple extends Base {

	protected $panelTag 	= "empty";
	protected $retVal	 	= "";
	protected $userID		= null;
	protected $language 	= 'pl';
	protected $rendered 	= false;

	public function add($sValue) {
		$this->rendered = true;
		$this->retVal 	.= $sValue;
	}

	public function getTransport() {
		return new PanelTransport(null, $this->encodeOutput(), $this->rendered);
	}

}