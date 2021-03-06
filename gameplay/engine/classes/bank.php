<?php
class bank {

	//@todo transfer gotówki do innych graczy

	/**
	 * Pobieranie kasy w banku
	 * @param int $value
	 * @throws securityException
	 */
	static public function sWithdraw($value) {

		global $userStats, $portProperties, $shipPosition, $action, $shipProperties, $shortUserStatsPanel;

		$value = \Database\Controller::getInstance()->quote($value);

		if (!is_numeric($value)) {
			throw new securityException();
		}

		if ($value < 0) {
			throw new securityException();
		}

		if ($value > $userStats->Bank) {
			throw new securityException();
		}

		if ($shipPosition->Docked != 'yes') {
			throw new securityException();
		}

		if ($portProperties->Type != 'station') {
			throw new securityException();
		}

		$userStats->Cash += $value;
		$userStats->Bank -= $value;

		announcementPanel::getInstance()->write('info', TranslateController::getDefault()->get('opSuccess'));
		$action = 'portBank';

	}

	/**
	 * Deponowanie kasy w banku
	 * @param int $value
	 * @throws securityException
	 */
	static public function sDeposit($value) {

		global $userStats, $portProperties, $shipPosition, $action, $shortUserStatsPanel, $shipProperties;

		$value = \Database\Controller::getInstance()->quote($value);

		if (!is_numeric($value)) {
			throw new securityException();
		}

		if ($value < 0) {
			throw new securityException();
		}

		if ($value > $userStats->Cash) {
			throw new securityException();
		}

		if ($shipPosition->Docked != 'yes') {
			throw new securityException();
		}

		if ($portProperties->Type != 'station') {
			throw new securityException();
		}

		$userStats->Cash -= $value;
		$userStats->Bank += floor($value * 0.9);

		announcementPanel::getInstance()->write('info', TranslateController::getDefault()->get('opSuccess'));
		$action = 'portBank';

	}

}