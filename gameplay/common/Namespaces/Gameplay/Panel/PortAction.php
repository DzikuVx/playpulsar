<?php

namespace Gameplay\Panel;

use Interfaces\Singleton;

class PortAction extends Simple implements Singleton {
	protected $panelTag = "PortAction";

	/**
	 * @var PortAction
	 */
	static private $instance = null;

	/**
	 * @throws \Exception
	 * @return PortAction
	 */
	static public function getInstance() {

		if (empty(self::$instance)) {
			throw new \Exception('Panel not initialized');
		}
		else {
			return self::$instance;
		}

	}

	static public function initiateInstance($language = 'pl', $localUserID = null) {
		self::$instance = new self($language, $localUserID);
	}

}