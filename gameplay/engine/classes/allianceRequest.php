<?php
/**
 *
 * Podanie do sojuszu
 * @author Paweł
 * @see alliance
 * @package Engine
 * @version  $Rev: 460 $
 *
 */
class allianceRequest extends baseItem {

	/**
	 * Zapisanie do bazy podania do sojuszu
	 * @param int $allianceID
	 * @param string $xml
	 * @throws securityException
	 */
	static public function sNewExecute($allianceID, $xml) {
		global $userAlliance, $userID, $t, $userProperties;

		if (!empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (empty($allianceID)) {
			throw new securityException();
		}

		$tAlliance = alliance::quickLoad($allianceID);

		if (empty($tAlliance)) {
			throw new securityException();
		}

		if ($tAlliance->NPCAlliance != 'no') {
			throw new securityException();
		}

		if (self::sCheckRequest($userID, $allianceID)) {
			throw new securityException();
		}

		self::sInsert($userID, $allianceID, xml::sGetValue($xml, '<text>', '</text>'));

		/**
		 * Wysłanie wiadomości do wszystich uprowanionych członków o nowym podaniu
		 * @since 2010-07-31
		 */
		$tMembers = allianceRights::sGetMembersWithRight($allianceID, 'accept');
		foreach ($tMembers as $tMember) {
			$tSecondPlayer = userProperties::quickLoad($tMember);
			$t = new translation($tSecondPlayer->Language, dirname ( __FILE__ ) . '/../translations.php');
			$tString = TranslateController::getDefault()->get('allianceNewAppliance');
			$tString = str_replace('{name}',$userProperties->Name, $tString);
			message::sInsert(null, $tMember, $tString);
		}

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::displayConfirmDialog(TranslateController::getDefault()->get('confirm'), TranslateController::getDefault()->get('allianceApplianceSaved'),'Playpulsar.gameplay.execute(\'allianceDetail\',null,null,\''.$allianceID.'\')'));

		\phpCache\Factory::getInstance()->create()->clear('allianceRequest::sGetCount', $allianceID);

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 *
	 * Formularz podania o przyjęcie do sojuszu
	 * @param int $allianceID
	 * @throws securityException
	 */
	static public function sNew($allianceID) {
		global $userAlliance, $userID, $t, $userProperties;

		if (!empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (empty($allianceID)) {
			throw new securityException();
		}

		$tAlliance = alliance::quickLoad($allianceID);

		if (empty($tAlliance)) {
			throw new securityException();
		}

		if ($tAlliance->NPCAlliance != 'no') {
			throw new securityException();
		}

		if (self::sCheckRequest($userID, $allianceID)) {
			throw new securityException();
		}

		$template  = new \General\Templater('../templates/allianceRequest.html');
		$template->add('FormName', TranslateController::getDefault()->get('allianceAppliance'));
		$template->add('PlayerName', $userProperties->Name);
		$template->add('AllianceName', $tAlliance->Name);
		$template->add('action', "alliance.applySave('{$allianceID}');");

		\Gameplay\Panel\Action::getInstance()->add((string) $template);
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * Sprawdzenie, czy jest złożone podanie
	 * @param int $userID
	 * @param int $allianceID
	 * @return boolean
	 */
	static public function sCheckRequest($userID, $allianceID) {

		$retVal = false;

		$tQuery = "SELECT COUNT(*) AS ILE FROM alliancerequests WHERE UserID='{$userID}' AND AllianceID='{$allianceID}'";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);

		if (\Database\Controller::getInstance()->fetch($tQuery)->ILE == 0) {
			$retVal = false;
		}else {
			$retVal = true;
		}

		return $retVal;
	}

	/**
	 *
	 * Wysłanie zgłoszenia do sojuszu
	 * @param int $userID
	 * @param int $allianceID
	 * @param string $text
	 * @throws securityException
	 */
	static private function sInsert($userID, $allianceID, $text = '') {

		$tSecondAlliance = userAlliance::quickLoad($userID);

		if (!empty($tSecondAlliance->AllianceID)) {
			throw new securityException();
		}

		$text = \Database\Controller::getInstance()->quote($text);

		$tQuery = "INSERT INTO alliancerequests(UserID, AllianceID, Date, Text) VALUES('{$userID}','{$allianceID}','".time()."','{$text}')";
		\Database\Controller::getInstance()->execute($tQuery);

	}

	/**
	 *
	 * usunięcie zgłoszenia
	 * @param int $userID
	 * @param int $allianceID
	 */
	static private function sDelete($userID, $allianceID) {
		$tQuery = "DELETE FROM  alliancerequests WHERE UserID='{$userID}' AND AllianceID='{$allianceID}'";
		\Database\Controller::getInstance()->execute($tQuery);
	}

	/**
	 *
	 * usunięcie wszystkich zgłoszeń użytkownika
	 * @param int $userID
	 */
	static public function sDeleteAll($userID) {
		$tQuery = "DELETE FROM  alliancerequests WHERE UserID='{$userID}'";
		\Database\Controller::getInstance()->execute($tQuery);
	}

    /**
     * @param $allianceID
     * @return int
     */
    static public function sGetCount($allianceID) {

		try {

			$oCacheKey = new \phpCache\CacheKey('allianceRequest::sGetCount', $allianceID);
            $oCache    = \phpCache\Factory::getInstance()->create();

			if (!$oCache->check($oCacheKey)) {

				if (\Database\Controller::getInstance()->getHandle() === false) {
					throw new \Database\Exception('Connection lost');
				}

				$tQuery = "SELECT COUNT(*) AS ILE FROM alliancerequests WHERE AllianceID='{$allianceID}'";
				$tQuery = \Database\Controller::getInstance()->execute($tQuery);
				$retVal = \Database\Controller::getInstance()->fetch($tQuery)->ILE;

				$oCache->set($oCacheKey, $retVal);

			}else {
				$retVal = $oCache->get($oCacheKey);
			}

		}catch (Exception $e) {
			psDebug::cThrow(null, $e, array('display' => false));
			$retVal = 0;
		}

		return $retVal;
	}

    /**
     * @throws securityException
     */
    static public function sRender() {

		global $userID, $userAlliance;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (!allianceRights::sCheck($userID, $userAlliance->AllianceID, 'accept')) {
			throw new securityException();
		}

		/*
		 * Wyrenderowanie sojuszu
		 */
		$registry = new allianceRequestsRegistry ( $userID );
		\Gameplay\Panel\Action::getInstance()->add($registry->get ($userAlliance->AllianceID));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

	}

    /**
     * @param int $apprenticeID
     * @throws securityException
     */
    static public function sAccept($apprenticeID) {
		global $userAlliance, $userID;

		/*
		 * Warunki bezpieczeństwa
		 */
		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (empty($apprenticeID)) {
			throw new securityException();
		}

		$tSecondAlliance = userAlliance::quickLoad($apprenticeID);
		if (! empty($tSecondAlliance->AllianceID)) {
			throw new securityException();
		}

		if (allianceRights::sCheck($userID, $userAlliance->AllianceID, 'accept') === false) {
			throw new securityException();
		}

		if (self::sCheckRequest($apprenticeID, $userAlliance->AllianceID) === false) {
			throw new securityException();
		}

		$tString = TranslateController::getDefault()->get('wantAcceptPlayer');

		$tName = userProperties::quickLoad($apprenticeID)->Name;
		$tString = str_replace('{name}',$tName, $tString);

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::sRenderDialog(TranslateController::getDefault()->get ( 'confirm' ), $tString,"Playpulsar.gameplay.execute('allianceAcceptExecute',null,null,'{$apprenticeID}')","Playpulsar.gameplay.execute('allianceAppliances')"));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 *
	 * Zaakceptowanie podania do sojuszu, wykonanie
	 * @param int $apprenticeID
	 * @throws securityException
	 * @since 2010-07-31
	 */
	static public function sAcceptExecute($apprenticeID) {
		global $userAlliance, $userID;

		/*
		 * Warunki bezpieczeństwa
		 */
		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (empty($apprenticeID)) {
			throw new securityException();
		}

		$tSecondAlliance = userAlliance::quickLoad($apprenticeID);
		if (! empty($tSecondAlliance->AllianceID)) {
			throw new securityException();
		}

		if (allianceRights::sCheck($userID, $userAlliance->AllianceID, 'accept') === false) {
			throw new securityException();
		}

		if (self::sCheckRequest($apprenticeID, $userAlliance->AllianceID) === false) {
			throw new securityException();
		}

		$secondPlayerAllianceObject = new userAlliance();
		$secondPlayerAlliance = $secondPlayerAllianceObject->load($apprenticeID, true, true);

		$secondPlayerAlliance->UserID = $apprenticeID;
		$secondPlayerAlliance->AllianceID = $userAlliance->AllianceID;

		$secondPlayerAllianceObject->synchronize($secondPlayerAlliance, true, true);

		self::sDeleteAll($apprenticeID);

		/*
		 * Oczyść cache
		 */
        //FIXME to jest bez sensu
		\Cache\Controller::forceClear($apprenticeID, 'userAlliance');
		\Cache\Controller::forceClear($apprenticeID, 'allianceRights');

        $oCache = \phpCache\Factory::getInstance()->create();
        $oCache->clearModule(new \phpCache\CacheKey('alliance::getRegistry'));
		$oCache->clearModule(new \phpCache\CacheKey('allianceMembersRegistry::get'));
		$oCache->clearModule(new \phpCache\CacheKey('allianceRequest::sGetCount'));

		/*
		 * Nadaj puste prawa
		 */
		allianceRights::sGiveNone($apprenticeID, $userAlliance->AllianceID);

		/*
		 * Wyślij wiadomość
		 */
		$tSecondPlayer = userProperties::quickLoad($apprenticeID);
		$t = new translation($tSecondPlayer->Language, dirname ( __FILE__ ) . '/../translations.php');
		$tString = TranslateController::getDefault()->get('allianceApplianceAccepted');
		$tString = str_replace('{name}',$userAlliance->Name, $tString);
		message::sInsert(null, $apprenticeID, $tString);

		/*
		 * Wyrenderuj listę innych podań
		 */
		self::sRender();

	}

	/**
	 *
	 * Odrzucanie podania o przyjęcie do sojuszu
	 * @param int $apprenticeID
	 * @throws securityException
	 * @since 2010-07-31
	 */
	static public function sDecline($apprenticeID) {
		global $userAlliance, $userID;

		/*
		 * Warunki bezpieczeństwa
		 */
		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (empty($apprenticeID)) {
			throw new securityException();
		}

		$tSecondAlliance = userAlliance::quickLoad($apprenticeID);
		if (! empty($tSecondAlliance->AllianceID)) {
			throw new securityException();
		}

		if (allianceRights::sCheck($userID, $userAlliance->AllianceID, 'accept') === false) {
			throw new securityException();
		}

		if (self::sCheckRequest($apprenticeID, $userAlliance->AllianceID) === false) {
			throw new securityException();
		}

		$tString = TranslateController::getDefault()->get('wantDeclinePlayer');

		$tName = userProperties::quickLoad($apprenticeID)->Name;
		$tString = str_replace('{name}',$tName, $tString);

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::sRenderDialog(TranslateController::getDefault()->get ( 'confirm' ), $tString,"Playpulsar.gameplay.execute('allianceDeclineExecute',null,null,'{$apprenticeID}')","Playpulsar.gameplay.execute('allianceAppliances')"));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 *
	 * Odrzucenie podania do sojuszu, wykonanie
	 * @param int $apprenticeID
	 * @throws securityException
	 * @since 2010-07-31
	 */
	static public function sDeclineExecute($apprenticeID) {
		global $userAlliance, $userID;

		/*
		 * Warunki bezpieczeństwa
		 */
		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (empty($apprenticeID)) {
			throw new securityException();
		}

		$tSecondAlliance = userAlliance::quickLoad($apprenticeID);
		if (! empty($tSecondAlliance->AllianceID)) {
			throw new securityException();
		}

		if (allianceRights::sCheck($userID, $userAlliance->AllianceID, 'accept') === false) {
			throw new securityException();
		}

		if (self::sCheckRequest($apprenticeID, $userAlliance->AllianceID) === false) {
			throw new securityException();
		}

		self::sDelete($apprenticeID, $userAlliance->AllianceID);

		$tSecondPlayer = userProperties::quickLoad($apprenticeID);
		$tString = TranslateController::getDefault()->get('allianceApplianceDeclined');
		$tString = str_replace('{name}',$userAlliance->Name, $tString);
		message::sInsert(null, $apprenticeID, $tString);

		\phpCache\Factory::getInstance()->create()->clear('allianceRequest::sGetCount', $userAlliance->AllianceID);

		/*
		 * Wyrenderuj listę innych podań
		 */
		self::sRender();
	}

}