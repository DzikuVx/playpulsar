<?php

namespace Gameplay\Panel;
use Interfaces\Singleton;

class Debug extends Renderable implements Singleton {

	protected $panelTag = "Debug";
	protected $onEmpty = "clear";

	public function add($sKey, $sValue) {
		$this->rendered = true;
		$this->retVal .= '<div><label>' . $sKey . ': </label>' . $sValue . '</div>';
	}

	/**
	 *
	 * @var Debug
	 */
	private static $instance = null;

	/**
	 * @return \Gameplay\Panel\Debug
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

