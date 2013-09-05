<?php

namespace Gameplay\Framework;

class ContentTransport implements \Interfaces\Singleton {

	/**
	 * @var array
	 */
	private $aPanels;
	
	/**
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
			$className 		= __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}
	
	private function __construct() {
	
	}
	
	public function addPanel($oPanel) {
		//TODO Check if parent is from proper class
		$this->aPanels[$oPanel->getPanelTag()] = $oPanel->getTransport();
	}
	
	public function get() {
		
		$out = new \stdClass();
		$out->panels 	= $this->aPanels;
		$out->variables = $this->aVariables;
		
		return json_encode($out);
	}
	
	public function addVariable($name, $value) {
		$this->aVariables[$name] = $value;			
	}

}