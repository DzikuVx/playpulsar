<?php
class buddyList extends baseItem {
	protected $tableName = "buddylist";
	protected $tableID = 'BuddyID';
	protected $tableUseFields = array ("UserID", "SecondUserID", "Accepted");
	protected $defaultCacheExpire = 120;

	static public function sAcceptExe($id) {
		global $userAlliance, $userID;

		/*
		 * Warunki bezpieczeństwa
		*/
		if (!is_numeric($id)) {
			throw new securityException();
		}

		if (empty($id)) {
			throw new securityException();
		}

		if (!self::sCheck($userID, $id)) {
			throw new securityException();
		}

		/*
		 * Dla bezpieczeństwa, zrzuć
		*/
		self::sRemove($userID, $id);
		self::sRemove($id, $userID);

		self::sInsert($id, $userID, 'yes');
		self::sInsert($userID, $id, 'yes');

		$tSecondPlayer = userProperties::quickLoad($userID);
		$t2 = new translation($tSecondPlayer->Language, dirname ( __FILE__ ) . '/../translations.php');
		$tString = $t2->get('buddyRequestAccepted');
		$tString = str_replace('{name}',$tSecondPlayer->Name, $tString);
		message::sInsert(null, $id, $tString);
		unset($tSecondPlayer);

		\Cache\Controller::getInstance()->clear('buddyRegistry::get', $userID);
		\Cache\Controller::getInstance()->clear('buddyRequestRegistry::get', $userID);
		\Cache\Controller::getInstance()->clear('buddyRegistry::get', $id);
		\Cache\Controller::getInstance()->clear('buddyRequestRegistry::get', $id);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', TranslateController::getDefault()->get('opSuccess'));
		self::sRenderList();
	}

	static public function sAccept($id) {
		global $userAlliance, $userID;

		/*
		 * Warunki bezpieczeństwa
		*/
		if (!is_numeric($id)) {
			throw new securityException();
		}

		if (empty($id)) {
			throw new securityException();
		}

		if (!self::sCheck($userID, $id)) {
			throw new securityException();
		}

		$tString = TranslateController::getDefault()->get('wantAcceptBuddy');

		$tName = userProperties::quickLoad($id)->Name;
		$tString = str_replace('{name}',$tName, $tString);

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::sRenderDialog(TranslateController::getDefault()->get ( 'confirm' ), $tString,"Playpulsar.gameplay.execute('buddyAcceptExecute',null,null,'{$id}')","Playpulsar.gameplay.execute('showBuddy')"));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	static public function sDeclineDialog($id) {
		global $userAlliance, $userID;

		/*
		 * Warunki bezpieczeństwa
		*/
		if (!is_numeric($id)) {
			throw new securityException();
		}

		if (empty($id)) {
			throw new securityException();
		}

		if (!self::sCheck($userID, $id)) {
			throw new securityException();
		}

		$tString = TranslateController::getDefault()->get('wantDeclineBuddy');

		$tName = userProperties::quickLoad($id)->Name;
		$tString = str_replace('{name}',$tName, $tString);

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::sRenderDialog(TranslateController::getDefault()->get ( 'confirm' ), $tString,"Playpulsar.gameplay.execute('buddyDecline',null,null,'{$id}')","Playpulsar.gameplay.execute('showBuddy')"));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	static public function sDecline($id) {

		global $userID;

		if (!is_numeric($id)) {
			throw new securityException();
		}

		self::sRemove($userID, $id);
		self::sRemove($id, $userID);

		/*
		 * Oczyść cache
		*/
		\Cache\Controller::getInstance()->clear('buddyRegistry::get', $userID);
		\Cache\Controller::getInstance()->clear('buddyRequestRegistry::get', $userID);
		\Cache\Controller::getInstance()->clear('buddyRegistry::get', $id);
		\Cache\Controller::getInstance()->clear('buddyRequestRegistry::get', $id);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', TranslateController::getDefault()->get('opSuccess'));
		self::sRenderList();
	}

	static public function sRenderList() {

		global $userID, $userAlliance;

		/*
		 * Wyrenderowanie sojuszu
		*/
		$registry = new buddyRequestRegistry($userID);
		\Gameplay\Panel\Action::getInstance()->add($registry->get());

		$registry = new buddyRegistry($userID);
		\Gameplay\Panel\Action::getInstance()->add($registry->get());

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

	}

	/**
	 * Sprawdzenie, czy między graczami jest relacja buddy
	 * @param int $userID
	 * @param int $secondUserID
	 * @throws securityException
	 * @return boolean
	 */
	static public function sCheckEntry($userID, $secondUserID) {

		if (!is_numeric($userID)) {
			throw new securityException();
		}
		if (!is_numeric($secondUserID)) {
			throw new securityException();
		}

		$retVal = false;

		if (self::sCheck($userID, $secondUserID)) {
			$retVal = true;
		}
		if (self::sCheck($secondUserID, $userID)) {
			$retVal = true;
		}

		return $retVal;
	}

	static private function sCheck($userID, $secondUserID) {

		//@todo tutaj chyba trzeba będzie dodać cache
		$retVal = false;

		$tQuery = "SELECT COUNT(*) AS ILE FROM buddylist WHERE UserID='{$userID}' AND SecondUserID='{$secondUserID}'";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		if (\Database\Controller::getInstance()->fetch($tQuery)->ILE != 0) {
			$retVal = true;
		}

		return $retVal;
	}

	/**
	 * Wysłanie zaproszenia do znajomych
	 * @param int $id ID użytkownika do jakiego wysyłamy
	 * @throws securityException
	 */
	static public function sSendRequest($id) {
		global $userID, $userProperties;

		if (!is_numeric($id)) {
			throw new securityException();
		}

		if (self::sCheckEntry($userID, $id)) {
			throw new securityException();
		}

		/**
		 * Wstaw wpis jednostronny
		 */
		self::sInsert($id, $userID, 'no');

		/**
		 * Wyślij wiadomość
		 */
		$tSecondPlayer = userProperties::quickLoad($id);
		$t2 = new translation($tSecondPlayer->Language, dirname ( __FILE__ ) . '/../translations.php');
		$tString = $t2->get('newBuddyRequestSend');
		$tString = str_replace('{name}',$userProperties->Name, $tString);
		message::sInsert(null, $id, $tString);
		unset($tSecondPlayer);

		\Cache\Controller::getInstance()->clear('buddyRequestRegistry::get', $id);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', TranslateController::getDefault()->get('Request has been sent'));
		shipExamine ( $id, $userID );
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * Usunięcie wpisu z BuddyList
	 * @param int $userID
	 * @param int $secondUserID
	 * @throws securityException
	 */
	static private function sRemove($userID, $secondUserID) {

		if (!is_numeric($userID)) {
			throw new securityException();
		}
		if (!is_numeric($secondUserID)) {
			throw new securityException();
		}

		\Database\Controller::getInstance()->execute("DELETE FROM buddylist WHERE UserID= '{$userID}' AND SecondUserID='{$secondUserID}'");

		return true;
	}

	/**
	 * Wstawienie wpisu do buddyList
	 * @param int $userID
	 * @param int $secondUserID
	 * @param string $accepted
	 * @throws securityException
	 */
	static private function sInsert($userID, $secondUserID, $accepted) {

		$retVal = null;

		if (!is_numeric($userID)) {
			throw new securityException();
		}
		if (!is_numeric($secondUserID)) {
			throw new securityException();
		}

		if ($accepted == 'yes') {
			$accepted == 'yes';
		}else {
			$accepted = 'no';
		}

		$tData = new stdClass();
		$tData->UserID = $userID;
		$tData->SecondUserID = $secondUserID;
		$tData->Accepted = $accepted;

		$tObject = new self();
		$retVal = $tObject->insert($tData);

		return $retVal;
	}


}
?>