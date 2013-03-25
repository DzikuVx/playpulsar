<?php

/**
 * Klasa bazowa paneli gry
 *
 * @version $Rev: 455 $
 * @package Engine
 *
 */
abstract class basePanel {
	protected $panelTag = "empty";
	protected $userID;
	protected $retVal = "";
	protected $dataObject = null;
	protected $renderCloser = false;
	protected $rendered = false;
	protected $onEmpty = "none"; //Jak ma się zachować panel gdy jego zawartość jest pusta: none / hide / clear
	protected $language = 'pl';
	protected $forceAction = null;

	/**
	 * @return string
	 */
	final public function getRetVal() {

		return $this->retVal;
	}

	/**
	 * @param string $onEmpty
	 */
	final public function setOnEmpty($onEmpty) {

		$this->onEmpty = $onEmpty;
	}

	/**
	 * @param string $panelTag
	 */
	final public function setPanelTag($panelTag) {

		$this->panelTag = $panelTag;
	}

	/**
	 * @param boolean $renderCloser
	 */
	final public function setRenderCloser($renderCloser) {

		$this->renderCloser = $renderCloser;
	}

	function __construct($language = 'pl', $localUserID = null) {

		if (empty ( $localUserID )) {
			global $userID;
			$this->userID = $userID;
		} else {
			$this->userID = $localUserID;
		}

		$this->language = $language;
	}

	protected function renderCloser() {

		$retVal = "<div style=\"float: right;\"><img src=\"gfx/del2.gif\" class=\"link\" onclick=\"panel.hide('" . $this->panelTag . "');\" /></div>";
		return $retVal;
	}

	/**
	 * Dokonuje enkapsulacji wartości zwracanej przez klasę przez tag XML
	 *
	 * @param string $str
	 * @return string
	 */
	protected function encapsulate($action, $str = "") {

		$retVal = "<" . $this->panelTag . ">";
		$retVal .= "<action>" . $action . "</action>";
		if ($str != "")
			$retVal .= "<content>" . $str . "</content>";
		$retVal .= "</" . $this->panelTag . ">";
		return $retVal;
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

	/**
	 * Domyślna stopka wewnątrz panelu
	 *
	 * @return unknown
	 */
	public function renderFooter() {

		return "</table></div>";
	}

	/**
	 * Zwraca panel
	 *
	 * @return string
	 */
	public function out() {

		if ($this->forceAction == null) {

			if ($this->retVal != "") {
				return $this->encapsulate ( "show", $this->retVal );
			} else {
				switch ($this->onEmpty) {
					case "clear" :
						return $this->encapsulate ( "clear" );
						break;

					case "hideIfRendered" :
						if ($this->rendered) {
							return $this->encapsulate ( "hide" );
						} else {
							return "";
						}
						break;

					case "clearIfRendered" :
						if ($this->rendered) {
							return $this->encapsulate ( "clear" );
						} else {
							return "";
						}
						break;

					case "hide" :
						return $this->encapsulate ( "hide" );
						break;

					default :
					case "none" :
						return "";
						break;
				}
			}
		} else {
			return $this->encapsulate ( $this->forceAction, "" );
		}
	}

}
