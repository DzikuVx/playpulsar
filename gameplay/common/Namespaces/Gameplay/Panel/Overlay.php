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
	 * @return Overlay
	 */
	static public function getInstance() {
		if (empty(self::$instance)) {
			$className = __CLASS__;

            $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');
			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;

	}

}