<?php

namespace Gameplay\Actions;

use Gameplay\PlayerModelProvider;

class Bank {

	//@todo transfer gotówki do innych graczy

	/**
	 * @param int $value
	 * @throws \securityException
	 */
	static public function sWithdraw($value) {

		global $userStats, $action;

        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');

		$value = \Database\Controller::getInstance()->quote($value);

		if (!is_numeric($value)) {
			throw new \securityException();
		}

		if ($value < 0) {
			throw new \securityException();
		}

		if ($value > $userStats->Bank) {
			throw new \securityException();
		}

		if ($shipPosition->Docked != 'yes') {
			throw new \securityException();
		}

		if ($portProperties->Type != 'station') {
			throw new \securityException();
		}

		$userStats->Cash += $value;
		$userStats->Bank -= $value;

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', \TranslateController::getDefault()->get('opSuccess'));
		$action = 'portBank';

	}

	/**
	 * @param int $value
	 * @throws \securityException
	 */
	static public function sDeposit($value) {

		global $userStats, $action;

        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');
		$value = \Database\Controller::getInstance()->quote($value);

		if (!is_numeric($value)) {
			throw new \securityException();
		}

		if ($value < 0) {
			throw new \securityException();
		}

		if ($value > $userStats->Cash) {
			throw new \securityException();
		}

		if ($shipPosition->Docked != 'yes') {
			throw new \securityException();
		}

		if ($portProperties->Type != 'station') {
			throw new \securityException();
		}

		$userStats->Cash -= $value;
		$userStats->Bank += floor($value * 0.9);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', \TranslateController::getDefault()->get('opSuccess'));
		$action = 'portBank';
	}
}