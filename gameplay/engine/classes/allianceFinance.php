<?php
class allianceFinance extends baseItem {
	protected $tableName = "alliancefinance";
	protected $tableID = "OperationID";
	protected $tableUseFields = array ("AllianceID", "UserID", "Date", "Type", "Value", "Comment","ForUserID");
	protected $defaultCacheExpire = 1800;

	static public function sCashoutExe($id, $value) {

		global $userAlliance, $userID;

		$value = \Database\Controller::getInstance()->quote($value);
		$id    = \Database\Controller::getInstance()->quote($id);

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (!userAlliance::sCheckMembership($id, $userAlliance->AllianceID)) {
			throw new securityException();
		}

		$tRight = allianceRights::sCheck($userID, $userAlliance->AllianceID, 'cash');
		if (empty($tRight)) {
			throw new securityException();
		}

		if (!is_numeric($value)) {
			throw new securityException();
		}

		if ($value < 0) {
			throw new securityException();
		}

		$item = new alliance();
		$data = $item->load($userAlliance->AllianceID, true, true);

		if ($value > $data->Cash) {
			throw new securityException();
		}

        $tUserStats = new \Gameplay\Model\UserStatistics($id);

		$tUserStats->Cash += $value;
		$data->Cash -= $value;

		$tUserStats->synchronize();
		$item->synchronize($data, true, true);

        //TODO is this really nessesary? synchronize is setting cache right?
        \Gameplay\Model\UserStatistics::sFlushCache($id);

		//todo czyszczenie cache dla wszystkich członków sojuszu

		unset ($item);
		unset($tUserStatsObject);

		/*
		 * Wstaw wpis do listy operacji finansowych sojuszu
		*/
		$data = new stdClass();
		$data->AllianceID = $userAlliance->AllianceID;
		$data->UserID = $userAlliance->UserID;
		$data->Date = time();
		$data->Type = 'out';
		$data->Value = $value;
		$data->Comment = '';
		$data->ForUserID = $id;

		$item = new allianceFinance();
		$item->insert($data);
		unset ($item);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', TranslateController::getDefault()->get('opSuccess'));

		\Gameplay\Panel\Action::getInstance()->add(alliance::sGetDetail($userAlliance->AllianceID));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	static public function sCashoutDialog($id) {
		global $userAlliance, $userID;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (!userAlliance::sCheckMembership($id, $userAlliance->AllianceID)) {
			throw new securityException();
		}

		$tRight = allianceRights::sCheck($userID, $userAlliance->AllianceID, 'cash');
		if (empty($tRight)) {
			throw new securityException();
		}

		$template  = new \General\Templater('../templates/allianceCashout.html');

		$tAlliance = alliance::quickLoad($userAlliance->AllianceID);
		$tUser = userProperties::quickLoad($id);

		$template->add('FormName', TranslateController::getDefault()->get('allianceCashout'));
		$template->add('playerName', $tUser->Name);
		$template->add('maxCash', $tAlliance->Cash);
		$template->add('action', "alliance.cashout('{$id}');");

		\Gameplay\Panel\Action::getInstance()->add((string) $template);

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	static public function sDeposit() {
		global $userStats, $portProperties, $action, $value, $userAlliance;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
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

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		$userStats->Cash -= $value;

		$item = new alliance();
		$data = $item->load($userAlliance->AllianceID, true, true);
		$data->Cash += $value;
		$item->synchronize($data, true, true);
		unset ($item);

		/*
		 * Wstaw wpis do listy operacji finansowych sojuszu
		*/
		$data = new stdClass();
		$data->AllianceID = $userAlliance->AllianceID;
		$data->UserID = $userAlliance->UserID;
		$data->Date = time();
		$data->Type = 'in';
		$data->Value = $value;
		$data->Comment = '';
		$data->ForUserID = null;

		$item = new allianceFinance();
		$item->insert($data);
		unset ($item);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', TranslateController::getDefault()->get('opSuccess'));
		$action = 'portBank';
	}

}
