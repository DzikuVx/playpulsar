<?php

namespace Gameplay\Panel;

use Interfaces\Singleton;

class Overlay extends Simple implements Singleton {
	protected $panelTag = "Overlay";

	/**
	 * @var \Gameplay\Panel\Overlay
	 */
	static private $instance = null;

	/**
	 * @throws \Exception
	 * @return \Gameplay\Panel\Overlay
	 */
	static public function getInstance() {
		if (empty(self::$instance)) {
			$className = __CLASS__;

			global $userProperties;

			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;

	}

}