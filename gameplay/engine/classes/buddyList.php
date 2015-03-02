<?php

use Gameplay\Exception\SecurityException;

class buddyList extends baseItem {
	protected $tableName = "buddylist";
	protected $tableID = 'BuddyID';
	protected $tableUseFields = array ("UserID", "SecondUserID", "Accepted");
	protected $defaultCacheExpire = 120;

	static public function sAcceptExe($id) {
		global $userID;

		/*
		 * Warunki bezpieczeństwa
		*/
		if (!is_numeric($id)) {
			throw new SecurityException();
		}

		if (empty($id)) {
			throw new SecurityException();
		}

		if (!self::sCheck($userID, $id)) {
			throw new SecurityException();
		}

		/*
		 * Dla bezpieczeństwa, zrzuć
		*/
		self::sRemove($userID, $id);
		self::sRemove($id, $userID);

		self::sInsert($id, $userID, 'yes');
		self::sInsert($userID, $id, 'yes');

		$tSecondPlayer = new \Gameplay\Model\UserEntity($userID);
		$t2 = new translation($tSecondPlayer->Language, dirname ( __FILE__ ) . '/../translations.php');
		$tString = $t2->get('buddyRequestAccepted');
		$tString = str_replace('{name}',$tSecondPlayer->Name, $tString);
		\Gameplay\Model\Message::sInsert(null, $id, $tString);
		unset($tSecondPlayer);

        \phpCache\Factory::getInstance()->create()->clear('buddyRegistry::get', $userID);
        \phpCache\Factory::getInstance()->create()->clear('buddyRequestRegistry::get', $userID);
        \phpCache\Factory::getInstance()->create()->clear('buddyRegistry::get', $id);
        \phpCache\Factory::getInstance()->create()->clear('buddyRequestRegistry::get', $id);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', TranslateController::getDefault()->get('opSuccess'));
		self::sRenderList();
	}

	static public function sAccept($id) {
		global $userID;

		/*
		 * Warunki bezpieczeństwa
		*/
		if (!is_numeric($id)) {
			throw new SecurityException();
		}

		if (empty($id)) {
			throw new SecurityException();
		}

		if (!self::sCheck($userID, $id)) {
			throw new SecurityException();
		}

		$tString = TranslateController::getDefault()->get('wantAcceptBuddy');

        $oUser = new \Gameplay\Model\UserEntity($id);
        $tString = str_replace('{name}',$oUser->Name, $tString);

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::sRenderDialog(TranslateController::getDefault()->get ( 'confirm' ), $tString,"Playpulsar.gameplay.execute('buddyAcceptExecute',null,null,'{$id}')","Playpulsar.gameplay.execute('showBuddy')"));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	static public function sDeclineDialog($id) {
		global $userID;

		/*
		 * Warunki bezpieczeństwa
		*/
		if (!is_numeric($id)) {
			throw new SecurityException();
		}

		if (empty($id)) {
			throw new SecurityException();
		}

		if (!self::sCheck($userID, $id)) {
			throw new SecurityException();
		}

		$tString = TranslateController::getDefault()->get('wantDeclineBuddy');

        $oUser = new \Gameplay\Model\UserEntity($id);
		$tString = str_replace('{name}',$oUser->Name, $tString);

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::sRenderDialog(TranslateController::getDefault()->get ( 'confirm' ), $tString,"Playpulsar.gameplay.execute('buddyDecline',null,null,'{$id}')","Playpulsar.gameplay.execute('showBuddy')"));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	static public function sDecline($id) {

		global $userID;

		if (!is_numeric($id)) {
			throw new SecurityException();
		}

		self::sRemove($userID, $id);
		self::sRemove($id, $userID);

		/*
		 * Oczyść cache
		*/
        \phpCache\Factory::getInstance()->create()->clear('buddyRegistry::get', $userID);
        \phpCache\Factory::getInstance()->create()->clear('buddyRequestRegistry::get', $userID);
        \phpCache\Factory::getInstance()->create()->clear('buddyRegistry::get', $id);
        \phpCache\Factory::getInstance()->create()->clear('buddyRequestRegistry::get', $id);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', TranslateController::getDefault()->get('opSuccess'));
		self::sRenderList();
	}

	static public function sRenderList() {

		global $userID;

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
	 * @throws SecurityException
	 * @return boolean
	 */
	static public function sCheckEntry($userID, $secondUserID) {

		if (!is_numeric($userID)) {
			throw new SecurityException();
		}
		if (!is_numeric($secondUserID)) {
			throw new SecurityException();
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
	 * @throws SecurityException
	 */
	static public function sSendRequest($id) {
		global $userID;

        $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');

		if (!is_numeric($id)) {
			throw new SecurityException();
		}

		if (self::sCheckEntry($userID, $id)) {
			throw new SecurityException();
		}

		/**
		 * Wstaw wpis jednostronny
		 */
		self::sInsert($id, $userID, 'no');

		/**
		 * Wyślij wiadomość
		 */
        $tSecondPlayer = new \Gameplay\Model\UserEntity($id);
		$t2 = new translation($tSecondPlayer->Language, dirname ( __FILE__ ) . '/../translations.php');
		$tString = $t2->get('newBuddyRequestSend');
		$tString = str_replace('{name}',$userProperties->Name, $tString);
		\Gameplay\Model\Message::sInsert(null, $id, $tString);
		unset($tSecondPlayer);

        \phpCache\Factory::getInstance()->create()->clear('buddyRequestRegistry::get', $id);

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', TranslateController::getDefault()->get('Request has been sent'));
		shipExamine ( $id, $userID );
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * Usunięcie wpisu z BuddyList
	 * @param int $userID
	 * @param int $secondUserID
	 * @throws SecurityException
     * @return bool
	 */
	static private function sRemove($userID, $secondUserID) {

		if (!is_numeric($userID)) {
			throw new SecurityException();
		}
		if (!is_numeric($secondUserID)) {
			throw new SecurityException();
		}

		\Database\Controller::getInstance()->execute("DELETE FROM buddylist WHERE UserID= '{$userID}' AND SecondUserID='{$secondUserID}'");

		return true;
	}

	/**
	 * Wstawienie wpisu do buddyList
	 * @param int $userID
	 * @param int $secondUserID
	 * @param string $accepted
	 * @throws SecurityException
     * @return int
	 */
	static private function sInsert($userID, $secondUserID, $accepted) {

		$retVal = null;

		if (!is_numeric($userID)) {
			throw new SecurityException();
		}
		if (!is_numeric($secondUserID)) {
			throw new SecurityException();
		}

		if ($accepted != 'yes') {
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