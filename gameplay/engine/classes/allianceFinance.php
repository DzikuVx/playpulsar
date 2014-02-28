<?php
use Gameplay\Model\UserAlliance;
use Gameplay\PlayerModelProvider;

class allianceFinance extends baseItem {
	protected $tableName = "alliancefinance";
	protected $tableID = "OperationID";
	protected $tableUseFields = array ("AllianceID", "UserID", "Date", "Type", "Value", "Comment","ForUserID");
	protected $defaultCacheExpire = 1800;

	static public function sCashoutExe($id, $value) {

		global $userID;

        /** @var UserAlliance $userAlliance */
        $userAlliance = PlayerModelProvider::getInstance()->get('UserAlliance');

		$value = \Database\Controller::getInstance()->quote($value);
		$id    = \Database\Controller::getInstance()->quote($id);

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (!\Gameplay\Model\UserAlliance::sCheckMembership($id, $userAlliance->AllianceID)) {
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

		$data = new \Gameplay\Model\Alliance($userAlliance->AllianceID);

		if ($value > $data->Cash) {
			throw new securityException();
		}

        $tUserStats = new \Gameplay\Model\UserStatistics($id);

		$tUserStats->Cash += $value;
		$data->Cash -= $value;

		$tUserStats->synchronize();
        $data->synchronize();

        //TODO is this really nessesary? synchronize is setting cache right?
        $tUserStats->clearCache();

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

		\Gameplay\Panel\Action::getInstance()->add(\Gameplay\Model\Alliance::sGetDetail($userAlliance->AllianceID));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	static public function sCashoutDialog($id) {
		global $userID;

        /** @var UserAlliance $userAlliance */
        $userAlliance = PlayerModelProvider::getInstance()->get('UserAlliance');

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (UserAlliance::sCheckMembership($id, $userAlliance->AllianceID)) {
			throw new securityException();
		}

		$tRight = allianceRights::sCheck($userID, $userAlliance->AllianceID, 'cash');
		if (empty($tRight)) {
			throw new securityException();
		}

		$template  = new \General\Templater('../templates/allianceCashout.html');

		$tAlliance = new \Gameplay\Model\Alliance($userAlliance->AllianceID);
        $tUser = new \Gameplay\Model\UserEntity($id);

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
		global $userStats, $action, $value;

        /** @var UserAlliance $userAlliance */
        $userAlliance = PlayerModelProvider::getInstance()->get('UserAlliance');
        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = \Gameplay\PlayerModelProvider::getInstance()->get('PortEntity');
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

		$data = new \Gameplay\Model\Alliance($userAlliance->AllianceID);
		$data->Cash += $value;
        $data->synchronize();

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
