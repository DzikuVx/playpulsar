<?php

namespace Gameplay\Framework;

class ContentTransport implements \Interfaces\Singleton {

	/**
	 * @var array
	 */
	private $aPanels;
	
	/**
	 * @var ContentTransport
	 */
	private static $instance;
	
	/**
	 * @return ContentTransport
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className 		= __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}
	
	private function __construct() {
	
	}
	
	public function register($oPanel) {
		//Check if parent is from proper class
		
		$this->aPanels[$oPanel->getPanelTag()] = $oPanel->getTransport();
		
	}
	
}