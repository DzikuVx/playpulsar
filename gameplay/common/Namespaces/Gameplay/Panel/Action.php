<?php

namespace Gameplay\Panel;

use Interfaces\Singleton;

class Action extends Simple implements Singleton {
	protected $panelTag = "Action";

	/**
	 * @var Action
	 */
	static private $instance = null;

	/**
	 * @throws \Exception
	 * @return Action
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