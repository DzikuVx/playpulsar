<?php
/**
 * Prosty rejestr
 *
 * @version $Rev: 457 $
 * @package Engine
 */
abstract class simpleRegistry {

	//@todo to by się fajnie dało zrobić jako singleton
	
	/**
	 * Czy umoliwić wyłączenie cache w rejestrze
	 * @var boolean
	 */
	protected $disableCache = false;

	protected $userID = null;
	protected $language;

	/**
	 * Konstruktor statyczny
	 *
	 */
	static public function sRender() {

		global $userID, $actionPanel, $portPanel;

		$registry = new static ( $userID );
		$actionPanel .= $registry->get ();
		unset($registry);

		sectorShipsPanel::getInstance()->hide ();
		sectorResourcePanel::getInstance()->hide ();
		$portPanel = "&nbsp;";
	}

	/**
	 * Setter
	 * @param boolean $value
	 */
	public function setDisableCache($value) {
		$this->disableCache = $value;
	}

	/**
	 * Konstruktor
	 *
	 * @param int $userID
	 * @param string $language
	 */
	function __construct($userID, $language = null) {

		$this->userID = $userID;

		if (empty ( $language )) {
			global $userProperties;
			$this->language = $userProperties->Language;
		} else {
			$this->language = $language;
		}

	}

}

?>