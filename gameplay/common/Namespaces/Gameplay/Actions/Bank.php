<?php

namespace Gameplay\Actions;

use Gameplay\Exception\SecurityException;
use Gameplay\PlayerModelProvider;

class Bank {

	//@todo transfer gotówki do innych graczy

	/**
	 * @param int $value
	 * @throws SecurityException
	 */
	static public function sWithdraw($value) {

		global $userStats, $action;

        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');

		$value = \Database\Controller::getInstance()->quote($value);

		if (!is_numeric($value)) {
			throw new SecurityException();
		}

		if ($value < 0) {
			throw new SecurityException();
		}

		if ($value > $userStats->Bank) {
			throw new SecurityException();
		}

		if ($shipPosition->Docked != 'yes') {
			throw new SecurityException();
		}

		if ($portProperties->Type != 'station') {
			throw new SecurityException();
		}

		$userStats->Cash += $value;
		$userStats->Bank -= $value;

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', \TranslateController::getDefault()->get('opSuccess'));
		$action = 'portBank';

	}

	/**
	 * @param int $value
	 * @throws SecurityException
	 */
	static public function sDeposit($value) {

		global $userStats, $action;

        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');
		$value = \Database\Controller::getInstance()->quote($value);

		if (!is_numeric($value)) {
			throw new SecurityException();
		}

		if ($value < 0) {
			throw new SecurityException();
		}

		if ($value > $userStats->Cash) {
			throw new SecurityException();
		}

		if ($shipPosition->Docked != 'yes') {
			throw new SecurityException();
		}

		if ($portProperties->Type != 'station') {
			throw new SecurityException();
		}

		$userStats->Cash -= $value;
		$userStats->Bank += floor($value * 0.9);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', \TranslateController::getDefault()->get('opSuccess'));
		$action = 'portBank';
	}
}