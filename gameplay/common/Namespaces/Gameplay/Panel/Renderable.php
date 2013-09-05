<?php

namespace Gameplay\Panel;

use Gameplay\Framework\PanelTransport;

abstract class Renderable extends Base {
	
	protected $onEmpty 		= "none"; //Jak ma się zachować panel gdy jego zawartość jest pusta: none / hide / clear
	protected $forceAction 	= null;

	public function getTransport() {
		
		if (empty($this->forceAction)) {

			if ($this->retVal != "") {
				$sTransportAction = 'show';
			} else {
				$sTransportAction = $this->onEmpty;
			}
			
		} else {
			$sTransportAction = $this->forceAction;
		}
		
		return new PanelTransport($sTransportAction, $this->encodeOutput(), $this->rendered);
		
	}
	
	/**
	 * @param string $onEmpty
	 * //TODO Method unused
	 */
	final public function setOnEmpty($onEmpty) {

		$this->onEmpty = $onEmpty;
	}

	public function clearForceAction() {
		$this->forceAction = null;
		return true;
	}

	/**
	 * Zwraca string ukrywający panel bez czyszczenia zawartości
	 *
	 * @return string
	 */
	public function hide() {

		$this->forceAction = "hide";
		return true;
	}

	/**
	 * Zwraca string pokazujący panel bez zmiany zawartości
	 *
	 * @return string
	 */
	public function show() {

		$this->forceAction = "show";
		return true;
	}

	/**
	 * Zwraca string czyszczący i chowający panel
	 *
	 * @return string
	 */
	public function clearAndHide() {

		$this->forceAction = "clearAndHide";
		return true;
	}

	/**
	 * Zwraca string czyszczący panel
	 *
	 * @return string
	 */
	public function clear() {

		$this->forceAction = "clear";
		return true;
	}

}
