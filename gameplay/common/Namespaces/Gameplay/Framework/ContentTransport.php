<?php

namespace Gameplay\Framework;

/**
 * Content transport class responsible for transporting backend
 * generated html/variables/data into frontend
 *
 * @author pawel
 *
 */
class ContentTransport implements \Interfaces\Singleton {

	/**
	 * Array of game panels
	 * @var array
	 */
	private $aPanels;

	/**
	 * Array of announcemnets/notifications
	 * @var array
	 */
	private $aNotifications;


	/**
	 * Array of variables
	 * @var array
	 */
	private $aVariables;

	/**
	 * @var ContentTransport
	 */
	private static $instance;

	/**
	 * @return ContentTransport
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor
	 */
	private function __construct() {

	}

	/**
	 * add panel object for transort
	 * @param \Gameplay\Panel\Base $oPanel
	 * @return ContentTransport
	 */
	public function addPanel($oPanel) {
		//TODO Check if parent is from proper class
		$this->aPanels[$oPanel->getPanelTag()] = $oPanel->getTransport();

		return $this;
	}

	/**
	 * Method returns JSON encoded content for browser
	 * @return string
	 */
	public function get() {

		$out = new \stdClass();
		$out->panels 	= $this->aPanels;
		$out->variables = $this->aVariables;
		$out->notifications = $this->aNotifications;

		return json_encode($out);
	}

	/**
	 * Method adds variable for transport
	 * @param string $name
	 * @param mixed $value
	 * @return ContentTransport
	 */
	public function addVariable($name, $value) {
		$this->aVariables[$name] = $value;

		return $this;
	}

	/**
	 * Method adds notification for transport
	 * @param string $sType
	 * @param string $sText
	 * @return ContentTransport
	 */
	public function addNotification($sType, $sText) {
		$this->aNotifications[] = array('type' => $sType, 'text' => $sText);

		return $this;
	}

}