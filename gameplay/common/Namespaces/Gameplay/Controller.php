<?php

namespace Gameplay;

class Controller implements \Interfaces\Singleton {
	
	private $aRequest;
	
	/**
	 * @var \Gameplay\Controller
	 */
	private static $instance;
	
	/**
	 * @return \Gameplay\Controller
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}
	
	private function __construct() {
		
	}
	
	public function registerParameters($aRequest) {
		$this->aRequest = $this->escape($aRequest);
	}
	
	private function escape($aRequest) {
		//FIXME escape all request entries
		return $aRequest;
	}
	
	
	public function getParameter($name) {
		
		if (isset($this->aRequest[$name])) {
			return $this->aRequest[$name];
		} else {
			return null;
		}
		
	}
	
}