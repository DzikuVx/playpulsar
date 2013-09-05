<?php

namespace Gameplay\Panel;

abstract class Base {
	
	protected $panelTag 	= "empty";
	protected $retVal	 	= "";
	protected $userID		= null;
	protected $language 	= 'pl';
	protected $rendered 	= false;
	
	protected function __construct($language = 'pl', $localUserID = null) {
	
		if (empty ( $localUserID )) {
			global $userID;
			$this->userID = $userID;
		} else {
			$this->userID = $localUserID;
		}
	
		$this->language = $language;
	}
	
	protected function encodeOutput() {
		// 		return base64_encode($this->retVal);
		return $this->retVal;
	}
	
	final public function getPanelTag() {
		return $this->panelTag;
	}
	
	/**
	 * @return string
	 */
	final public function getRetVal() {
	
		return $this->retVal;
	}
	
	/**
	 * @param string $panelTag
	 */
	final public function setPanelTag($panelTag) {
	
		$this->panelTag = $panelTag;
	}
	
}