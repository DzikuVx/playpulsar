<?php
class allianceFinance extends baseItem {
	protected $tableName = "alliancefinance";
	protected $tableID = "OperationID";
	protected $tableUseFields = array ("AllianceID", "UserID", "Date", "Type", "Value", "Comment","ForUserID");
	protected $defaultCacheExpire = 1800;

	static public function sCashoutExe($id, $value) {

		global $shipProperties, $shortUserStatsPanel, $actionPanel, $portPanel, $userAlliance, $userID;

		$value = \Database\Controller::getInstance()->quote($value);
		$id = \Database\Controller::getInstance()->quote($id);

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


		$tUserStatsObject = new userStats();
		$tUserStats = $tUserStatsObject->load($id, true, true);

		$tUserStats->Cash += $value;
		$data->Cash -= $value;

		$tUserStatsObject->synchronize($tUserStats, true, true);
		$item->synchronize($data, true, true);

		\Cache\Controller::forceClear($id, 'userStats');
		//@todo czyszczenie cache dla wszystkich członków sojuszu

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

		announcementPanel::getInstance()->write('info', TranslateController::getDefault()->get('opSuccess'));

		$actionPanel = alliance::sGetDetail($userAlliance->AllianceID);

		sectorShipsPanel::getInstance()->hide ();
		sectorResourcePanel::getInstance()->hide ();
		$portPanel = "&nbsp;";
	}

	static public function sCashoutDialog($id) {
		global $actionPanel, $portPanel, $userAlliance, $userID;

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

		$actionPanel = $template;

		sectorShipsPanel::getInstance()->hide ();
		sectorResourcePanel::getInstance()->hide ();
		$portPanel = "&nbsp;";
	}

	static public function sDeposit() {
		global $userStats, $portProperties, $shipProperties, $shipPosition, $action, $value, $userAlliance, $shortUserStatsPanel;

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

		announcementPanel::getInstance()->write('info', TranslateController::getDefault()->get('opSuccess'));
		$action = 'portBank';
	}

}
