<?php
/**
 * Klasa sojuszu
 * @author Paweł
 * @version $Rev: 460 $
 *
 */
class alliance extends baseItem {
	protected $tableName = "alliances";
	protected $tableID = "AllianceID";
	protected $tableUseFields = array ("NPCAlliance", "Name", "Symbol", "Motto", "Description", "Cash","Defendable");
	protected $defaultCacheExpire = 3600;
	protected $useMemcached = true;

	/**
	 * Konstruktor statyczny
	 * @param $ID
	 */
	static public function quickLoad($ID) {
		$item = new self();
		$retVal = $item->load ( $ID, true, true );
		unset($item);
		return $retVal;
	}

	/**
	 * Sprawdzenie unikalności symbolu sojuszu
	 * @param string $string
	 * @param int $allianceID
	 */
	static private function sCheckUniqueSymbol($string, $allianceID = null) {

		$retVal = true;

		$string = mb_strtoupper($string);
		$string = \Database\Controller::getInstance()->quote($string);

		try {
			$tQuery = "SELECT COUNT(*) AS ILE FROM alliances WHERE UPPER(Symbol)='{$string}'";

			if (!empty($allianceID)) {
				$tQuery .= " AND AllianceID!='{$allianceID}'";
			}

			$tQuery = \Database\Controller::getInstance()->execute($tQuery);

			if (\Database\Controller::getInstance()->fetch($tQuery)->ILE > 0) {
				$retVal = false;
			}

		}catch (\Database\Exception $e) {
			psDebug::cThrow(null, $e, array('display'=>false));
			$retVal = false;
		}
		return $retVal;
	}

	/**
	 *
	 * Sprawdzenie unikalności nazwy sojuszu
	 * @param string $string
	 * @param int $allianceID
	 */
	static private function sCheckUniqueName($string, $allianceID = null) {

		$retVal = true;

		$string = mb_strtoupper($string);
		$string = \Database\Controller::getInstance()->quote($string);

		try {
			$tQuery = "SELECT COUNT(*) AS ILE FROM alliances WHERE UPPER(Name)='{$string}'";

			if (!empty($allianceID)) {
				$tQuery .= " AND AllianceID!='{$allianceID}'";
			}

			$tQuery = \Database\Controller::getInstance()->execute($tQuery);

			if (\Database\Controller::getInstance()->fetch($tQuery)->ILE > 0) {
				$retVal = false;
			}

		}catch (\Database\Exception $e) {
			\psDebug::cThrow(null, $e, array('display'=>false));
			$retVal = false;
		}
		return $retVal;
	}

	/**
	 *
	 * Dialog wyrzucania gracza z sojuszu
	 * @param int $kickedID
	 * @throws securityException
	 */
	static public function sKick($kickedID) {

		global $userAlliance, $userID;

		/*
		 * Warunki bezpieczeństwa
		 */
		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (empty($kickedID)) {
			throw new securityException();
		}

		$tSecondAlliance = userAlliance::quickLoad($kickedID);
		if (empty($tSecondAlliance->AllianceID)) {
			throw new securityException();
		}

		if ($userAlliance->AllianceID != $tSecondAlliance->AllianceID) {
			throw new securityException();
		}

		$tString = TranslateController::getDefault()->get('wantKickPlayer');

		$tName = userProperties::quickLoad($kickedID)->Name;
		$tString = str_replace('{name}',$tName, $tString);

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::sRenderDialog(TranslateController::getDefault()->get ( 'confirm' ), $tString,"Playpulsar.gameplay.execute('allianceKickExecute',null,null,'{$kickedID}')","Playpulsar.gameplay.execute('allianceDetail',null,null,'{$userAlliance->AllianceID}')"));

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 *
	 * Wyrzucenie gracza z sojuszu
	 * @param int $kickedID
	 * @throws securityException
	 */
	static public function sKickExe($kickedID) {

		global $userAlliance, $userID;

		/*
		 * Warunki bezpieczeństwa
		 */
		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (empty($kickedID)) {
			throw new securityException();
		}

		$tSecondAlliance = userAlliance::quickLoad($kickedID);
		if (empty($tSecondAlliance->AllianceID)) {
			throw new securityException();
		}

		if ($userAlliance->AllianceID != $tSecondAlliance->AllianceID) {
			throw new securityException();
		}

		allianceRights::sGiveNone($kickedID, $tSecondAlliance->AllianceID);

		$secondPlayerAllianceObject = new userAlliance();
		$secondPlayerAlliance = $secondPlayerAllianceObject->load($kickedID, true, true);

		$secondPlayerAlliance->AllianceID = null;
		$secondPlayerAlliance->Name = null;

		$secondPlayerAllianceObject->synchronize($secondPlayerAlliance, true, true);
		/*
		 * Oczyść cache
		 */
		\Cache\Controller::forceClear($kickedID, 'userAlliance');
		\Cache\Controller::forceClear($kickedID, 'allianceRights');
		\Cache\Controller::getInstance()->clearModule('alliance::getRegistry');
		\Cache\Controller::getInstance()->clearModule('allianceMembersRegistry::get');

		/*
		 * Wyślij wiadomość
		 */
		$tSecondPlayer = userProperties::quickLoad($kickedID);
		$t = new translation($tSecondPlayer->Language, dirname ( __FILE__ ) . '/../translations.php');
		$tString = TranslateController::getDefault()->get('youBeenKickedFromAlliance');
		$tString = str_replace('{name}',$userAlliance->Name, $tString);
		message::sInsert(null, $kickedID, $tString);

		\Gameplay\Panel\Action::getInstance()->add(self::sGetDetail($userAlliance->AllianceID));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * Dialog opuszczenia sojuszu
	 * @throws securityException
	 */
	static public function sLeave() {

		global $userAlliance, $userID;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::sRenderDialog(TranslateController::getDefault()->get ( 'confirm' ), TranslateController::getDefault()->get('wantLeaveAlliance'),"Playpulsar.gameplay.execute('allianceLeaveExecute')","Playpulsar.gameplay.execute('allianceDetail',null,null,'{$userAlliance->AllianceID}')"));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 *
	 * Opuszczenie sojuszu
	 * @throws securityException
	 */
	static public function sLeaveExecute() {

		global $action, $userAlliance, $userID, $userAllianceObject;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		allianceRights::sGiveNone($userID, $userAlliance->AllianceID);
		$userAlliance->AllianceID = null;

		$userAllianceObject->synchronize($userAlliance, true, true);

		/*
		 * usuń puste sojusze
		 */
		self::sDeleteEmptyAlliances();

		$action = "pageReload";

		\Cache\Controller::getInstance()->clearModule('alliance::getRegistry');
		\Cache\Controller::getInstance()->clearModule('allianceMembersRegistry::get');


	}

	/**
	 *
	 * Usunięcie sojuszy bez członków
	 */
	static public function sDeleteEmptyAlliances() {

		$tQuery = "DELETE FROM alliances WHERE NPCAlliance='no' AND (SELECT COUNT(*) FROM alliancemembers WHERE alliancemembers.AllianceID = alliances.AllianceID) = 0";
		\Database\Controller::getInstance()->execute($tQuery);

	}

	/**
	 *
	 * Plansza szczegółów sojuszu
	 * @param int $allianceID
	 */
	static public function sRender($allianceID) {

		global $userID;

		\Gameplay\Panel\Action::getInstance()->add(self::sGetDetail($allianceID));

		//@todo RELACJE z INNYMI SOJUSZAMI

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 *
	 * Dialog tworzenia nowego sojuszu
	 * @throws securityException
	 */
	static public function sNew() {

		global $userAlliance, $userStats, $userID, $config;

		if (!empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if ($userStats->Fame < $config ['alliance']['createFameCost']) {
			throw new warningException(TranslateController::getDefault()->get('notEnoughFame').' ['.$config ['alliance']['createFameCost'].']');
		}

		$template  = new \General\Templater('../templates/allianceAdd.html');

		$template->add('Cost', $config ['alliance']['createFameCost']);
		$template->add('FormName', TranslateController::getDefault()->get('alliance'));
		$template->add('AllianceSymbolValue', '');
		$template->add('AllianceNameValue', '');
		$template->add('AllianceMottoValue', '');
		$template->add('AllianceDescriptionValue', '');
		$template->add('action', 'alliance.newSave();');

		\Gameplay\Panel\Action::getInstance()->add((string) $template);
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * Utworzenie nowego sojuszu, wykonanie
	 * @param array $values
	 * @throws securityException
	 * @throws warningException
	 */
	static public function sNewExe($values) {

		global $userAlliance, $userID, $t, $userAllianceObject, $userStats, $config, $shipProperties;

		if (!empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		$data->NPCAlliance = 'no';
		$data->Symbol = xml::sGetValue($values, '<allianceSymbol>', '</allianceSymbol>');
		$data->Name = xml::sGetValue($values, '<allianceName>', '</allianceName>');
		$data->Motto = xml::sGetValue($values, '<allianceMotto>', '</allianceMotto>');
		$data->Description = xml::sGetValue($values, '<allianceDescription>', '</allianceDescription>');

		if (empty($data->Symbol)) {
			throw new warningException(TranslateController::getDefault()->get('allianceSymbolCantBeEmpty'));
		}
		if (empty($data->Name)) {
			throw new warningException(TranslateController::getDefault()->get('allianceNameCantBeEmpty'));
		}

		if (self::sCheckUniqueSymbol($data->Symbol) == false) {
			throw new warningException(TranslateController::getDefault()->get('allianceSymbolNotUnique'));
		}
		if (self::sCheckUniqueName($data->Name) == false) {
			throw new warningException(TranslateController::getDefault()->get('allianceNameNotUnique'));
		}

		if ($userStats->Fame < $config ['alliance']['createFameCost']) {
			throw new warningException(TranslateController::getDefault()->get('notEnoughFame').' ['.$config ['alliance']['createFameCost'].']');
		}

		/**
		 * @since 2011-03-14
		 */
		\Database\Controller::getInstance()->quoteAll($data);

		$item = new alliance();
		$item->insert($data);
		unset ($item);

		$userAlliance->UserID = $userID;
		$userAlliance->AllianceID = \Database\Controller::getInstance()->lastUsedID();
		$userAlliance->Name = $data->Name;

		/*
		 * Zsychronizuj
		 */
		$userAllianceObject->synchronize($userAlliance, true, true);

		/*
		 * Ustaw prawa dla sojuszu
		 */
		allianceRights::sGiveAll($userID, $userAlliance->AllianceID);

		userStats::decFame($userStats, $config ['alliance']['createFameCost']);

		/*
		 * Usuń wszystkie wysłane zaproszenia
		 */
		allianceRequest::sDeleteAll($userID);
		\Cache\Controller::getInstance()->clearModule('allianceRequest::sGetCount');

		\Cache\Controller::getInstance()->clearModule('alliance::getRegistry');

		\Gameplay\Panel\Action::getInstance()->add(self::sGetDetail($userAlliance->AllianceID));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 *
	 * Formularz edycji danych sojuszu
	 * @throws securityException
	 */
	static public function sEdit() {

		global $userAlliance, $userID;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		$tRight = allianceRights::sCheck($userID, $userAlliance->AllianceID, 'edit');
		if (empty($tRight)) {
			throw new securityException();
		}

		$template  = new \General\Templater('../templates/allianceEdit.html');

		$data = self::quickLoad($userAlliance->AllianceID);

		$template->add('FormName', TranslateController::getDefault()->get('alliance'));
		$template->add('AllianceSymbolValue', $data->Symbol);
		$template->add('AllianceNameValue', $data->Name);
		$template->add('AllianceMottoValue', $data->Motto);
		$template->add('AllianceDescriptionValue', $data->Description);
		$template->add('AllianceDescriptionValue', $data->Description);
		$template->add('action', 'alliance.editSave();');

		\Gameplay\Panel\Action::getInstance()->add((string) $template);
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * Zapisanie zamian w danych sojuszu
	 * @param string $values
	 * @throws securityException
	 */
	static public function sEditExe($values) {

		global $userAlliance, $userID;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		$tRight = allianceRights::sCheck($userID, $userAlliance->AllianceID, 'edit');
		if (empty($tRight)) {
			throw new securityException();
		}

		$item = new alliance();
		$data = $item->load($userAlliance->AllianceID, true, true);
		$data->Symbol = xml::sGetValue($values, '<allianceSymbol>', '</allianceSymbol>');
		$data->Name = xml::sGetValue($values, '<allianceName>', '</allianceName>');
		$data->Motto = xml::sGetValue($values, '<allianceMotto>', '</allianceMotto>');
		$data->Description = xml::sGetValue($values, '<allianceDescription>', '</allianceDescription>');

		if (empty($data->Symbol)) {
			throw new warningException(TranslateController::getDefault()->get('allianceSymbolCantBeEmpty'));
		}
		if (empty($data->Name)) {
			throw new warningException(TranslateController::getDefault()->get('allianceNameCantBeEmpty'));
		}

		if (self::sCheckUniqueSymbol($data->Symbol, $userAlliance->AllianceID) == false) {
			throw new warningException(TranslateController::getDefault()->get('allianceSymbolNotUnique'));
		}
		if (self::sCheckUniqueName($data->Name, $userAlliance->AllianceID) == false) {
			throw new warningException(TranslateController::getDefault()->get('allianceNameNotUnique'));
		}

		/**
		 * @since 2011-03-14
		 */
		\Database\Controller::getInstance()->quoteAll($data);

		$item->synchronize($data, true, true);
		unset ($item);

		//@todo mechanizm czyszczenia cache dla wszystkich graczy -> memcached
		\Gameplay\Panel\Action::getInstance()->add(self::sGetDetail($userAlliance->AllianceID));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

		announcementPanel::getInstance()->write ( 'info', TranslateController::getDefault()->get ( 'saved' ) );
	}

	/**
	 * Wyświetlenie szczegółów sojuszu
	 * @param int $allianceID
	 * @return string
	 */
	static public function sGetDetail($allianceID) {

		global $shipProperties, $userID, $userAlliance;

		$template  = new \General\Templater('../templates/allianceDetail.html');

		$tObject = new alliance();
		$tData = $tObject->load($allianceID, true, true);

		$template->add($tData);

		/*
		 * Uprawnienia na sojuszu
		 */
		$tOperations = '';

		if ($userAlliance->AllianceID == $allianceID) {

			/**
			 * Wyświetl posty na ścianie sojuszu
			 * @since 2011-03-14
			 */
			$registry = new alliancePostsRegistry($userID);
			$template->add('alliancePosts',$registry->get ($allianceID));
			unset($registry);

			$tOperations .=	 \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'dialogLeaveAlliance' ), "Playpulsar.gameplay.execute('allianceLeave',null,null,null,null);", "width: 140px; margin: 2px;" );
			/*
			 * Edycja danych sojuszu
			 */
			if (allianceRights::sCheck($userID, $allianceID, 'edit')) {
				$tOperations .=	 \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'dialogEdit' ), "Playpulsar.gameplay.execute('allianceEditData',null,null,null,null);", "width: 140px; margin: 2px;" );
			}

			/**
			 * Lista podań
			 * @since 2010-07-27
			 */
			if (allianceRights::sCheck($userID, $allianceID, 'accept')) {
				$tOperations .=	 \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'allianceAppliances' ).' ['.allianceRequest::sGetCount($allianceID).']', "Playpulsar.gameplay.execute('allianceAppliances',null,null,null,null);", "width: 140px; margin: 2px;" );
			}

			if (allianceRights::sCheck($userID, $allianceID, 'cash')) {
				$tOperations .=	 \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'allianceFinace' ), "Playpulsar.gameplay.execute('allianceFinanceData',null,null,null,null);", "width: 140px; margin: 2px;" );
			}

			/**
			 * Postowanie na ścianie sojuszu
			 * @since 2011-03-14
			 */
			if (allianceRights::sCheck($userID, $allianceID, 'post')) {
				$tOperations .=	 \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'newMessage' ), "Playpulsar.gameplay.execute('alliancPostMessage',null,null,null,null);", "width: 140px; margin: 2px;" );
			}

			if (allianceRights::sCheck($userID, $allianceID, 'rank')) {
				$tOperations .=	 \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'allianceRank' ), "Playpulsar.gameplay.execute('allianceRightsRegistry',null,null,null,null);", "width: 140px; margin: 2px;" );
			}
		}
		elseif (empty($userAlliance->AllianceID) && allianceRequest::sCheckRequest($userID, $allianceID) == false && $tData->NPCAlliance == 'no') {
			$tOperations .=	 \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'dialogJoinApply' ), "Playpulsar.gameplay.execute('allianceApply',null,null,'{$allianceID}',null);", "width: 140px; margin: 2px;" );

			$template->add('alliancePosts','');

		}

		$template->add('alliancePosts','');

		if (empty($tOperations)) {
			$template->remove('operationsDiv');
		}else {
			$template->add('operations',$tOperations);
		}

		/**
		 * Wyświetl listę członków sojuszu
		 */
		$registry = new allianceMembersRegistry ( $userID );
		$template->add('allianceMembers',$registry->get ($allianceID));
		unset($registry);

		return (string) $template;
	}

}