<?php

require_once '../common.php';

$timek1 = microtime ();

$out = "";
$error = false;
$actionPanel = '';
$portPanel = "";
$debug = "";

$xml = $HTTP_RAW_POST_DATA;

$retXml = "";

try {

	$userID = xml::sGetValue ( $xml, "<userID>", "</userID>" );

	/*
	 * Sprawdz, czy user podał właściwe dane logowania
	*/
	if (! isset ( $_SESSION ['userID'] ) || $userID != $_SESSION ['userID']) {
		echo '<xml><logout>true</logout></xml>';
		exit ();
	}

	/**
	 * Sprawdz, czy jest polecenie wyczyszczenia cache
	 */
	$tExists = false;
	$tQuery = \Database\Controller::getInstance()->execute ( "SELECT Module FROM cacheclear WHERE UserID='$userID'" );
	while ( $tRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		\Cache\Session::getInstance()->clear ( $tRow->Module );
		$tExists = true;
	}
	if ($tExists) {
		$tQuery = \Database\Controller::getInstance()->execute ( "DELETE FROM cacheclear WHERE UserID='$userID'" );
	}

	/*
	 * Koniec czyszczenia cache
	*/
	$action = xml::sGetValue ( $xml, "<action>", "</action>" );
	$subaction = xml::sGetValue ( $xml, "<subaction>", "</subaction>" );
	$id = xml::sGetValue ( $xml, "<id>", "</id>" );
	$value = xml::sGetValue ( $xml, "<value>", "</value>" );
	$lastTurnsResetTime = xml::sGetValue ( $xml, "<turnReset>", "</turnReset>" );
	$lastShipRepairTime = xml::sGetValue ( $xml, "<shipRepair>", "</shipRepair>" );

	if (!empty($config ['debug'] ['gameplayDebugOutput'])) {
		writeDebug ( $action );
	}

	/**
	 * Inicjalizacja tabeli users
	 */
	$userPropertiesObject = new userProperties ( );
	$userProperties = $userPropertiesObject->load ( $userID, true, true );

	/**
	 * Inicjalizacja klasy translacji
	 */
	TranslateController::setDefaultLanguage($userProperties->Language);
	$t = TranslateController::getDefault();
	
	if (empty($config ['general'] ['enableGameplay']) && empty($_SESSION['cpLoggedUserID'])) {
		echo '<xml><actionPanel>'.\General\Controls::displayConfirmDialog(TranslateController::getDefault()->get('Announcement'), TranslateController::getDefault()->get('Gameplay disabled')).'</actionPanel></xml>';
		exit();
	}

	//Inicjalizacja pozycji statku
	$shipPosition = new shipPosition($userID);

	/*
	 * Inicjacja walki
	*/

	/*
	 * Wyczyść stare combatlock
	*/
	combat::sCombatLockGarbageCollection ();

	/**
	 * Niech NPC związane walką otworzą ogień
	 */
	$tQuery = "SELECT
	     combatlock.UserID,
	     npctypes.Behavior
	   FROM
	     combatlock JOIN users ON users.UserID=combatlock.UserID AND users.Type='npc'
	     JOIN userships ON userships.UserID=combatlock.UserID
	     JOIN usertimes ON usertimes.UserID = combatlock.UserID
	     JOIN npctypes USING(NPCTypeID)
	   WHERE
	     userships.RookieTurns = '0' AND
	     combatlock.Active = 'yes' AND
	     usertimes.LastSalvo <= '" . (time () - $config ['combat'] ['salvoInterval']) . "'
	   LIMIT {$config ['combat'] ['npcSimultanousCombat']}
	   ";
	$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
	while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

		/*
		 * Sprawdź, czy na tego kolesia jest założona blokada
		*/

		if (\Cache\Controller::getInstance()->check('npcCombatLocked', $tResult->UserID) === false) {

			\Cache\Controller::getInstance()->set('npcCombatLocked', $tResult->UserID, 1, 2);

			$Combat = new combat ( $tResult->UserID, 'en' );
			switch ($tResult->Behavior) {

				case 'defensive':
					$tAction = 'disengage';
					break;

				default:
					$tAction = 'fireWeapons';
				break;

			}

			$Combat->execute ( $tAction, array(
				'renderResult' => false,
				'doSummon' => false
			)	);
			unset ( $Combat );
			\Cache\Controller::getInstance()->clear('npcCombatLocked', $tResult->UserID);
		}
	}

	/*
	 * jeśli przyszedł rozkaz rozpoczęcia walki
	*/
	if ($action == 'shipAttack') {
		combat::sSetCombatLock ( $userID, $id );

		/**
		 * Summon NPC typu protective do walki
		 */
		combat::sProtectiveNpcController($shipPosition, $id, true);

	}

	/*
	 * Sprawdz, czy jesteśmy walce
	*/
	if (combat::sCheckCombatLock ( $userID )) {
		throw new combatException ( );
	}

	/*
	 * Sprawdz, czy są osierocone combatMessages
	*/
	if (weaponFireResult::sCheckMessages ( $userID )) {
		throw new combatException ( );
	}

	$userStatsObject = new userStats ( );
	$userStats = $userStatsObject->load ( $userID, true, true );

	/*
	 * Inicjalizacja tabeli userTimes
	*/
	$userTimes = new userTimes ($userID);

	$userFastTimes = new userFastTimes($userID);


	//Sprawdz authorize code
	if (($action != "pageReload") and (xml::sGetValue ( $xml, "<auth>", "</auth>" ) != $userFastTimes->AuthCode)) {
		$action = null;
	}

	/**
	 * Inicjalizacja parametrów sektora
	 */
	$sectorPropertiesObject = new sectorProperties ( );
	$sectorProperties = $sectorPropertiesObject->load ( $shipPosition, true, true );

	/**
	 * Inicjalizacja parametrów portu
	 */
	$portPropertiesObject = new portProperties ( );
	$portProperties = $portPropertiesObject->load ( $shipPosition, true, true );

	/*
	 * Inicjalizacja JumpNode
	*/
	$jumpNodeObject = new jumpNode ( );
	$jumpNode = $jumpNodeObject->load ( $shipPosition, true, true );

	/**
	 * Inicjalizaja parametrów systemu
	 */
	$systemProperties = new systemProperties ($shipPosition->System);

	/**
	 * Inicjalizacja właściwości statku użytkownika
	 */
	$shipPropertiesObject = new shipProperties ( );
	$shipProperties = $shipPropertiesObject->load ( $userID, true, true );

	$shipRoutingObject = new shipRouting ( );
	$shipRouting = $shipRoutingObject->load ( $userID, true, true );

	/**
	 * Obiekt userAlliance
	 * @var userAlliance
	 */
	$userAllianceObject = new userAlliance ( );
	$userAlliance = $userAllianceObject->load ( $userID, false, false);

	/**
	 * Mini Mapa
	 */
	$miniMap = new miniMap ( $userID, $shipPosition->System, $shipPosition, true);
	$sectorPanel = new sectorPanel ( $userProperties->Language );
	$portInfoPanel = new portInfoPanel ( $userProperties->Language );
	$shipMovePanel = new shipMovePanel ( $userProperties->Language );
	$weaponsPanel = new weaponsPanel ( $userProperties->Language );
	$cargoPanel = new cargoPanel ( $userProperties->Language );
	
	$shortShipStatsPanel = new shortShipStatsPanel ( $userProperties->Language );
	$shortUserStatsPanel = new shortUserStatsPanel ( $userProperties->Language );

	$activeScanner = new activeScanner ( $userProperties->Language, $userID );

	$shipCargo = new shipCargo ( $userID, $userProperties->Language );
	$shipWeapons = new shipWeapons ( $userID, $userProperties->Language );
	$shipEquipment = new shipEquipment ( $userID, $userProperties->Language );

	/*
	 * Autonaprawa statku
	*/
	$shipPropertiesObject->autoRepair ( $shipProperties, $userFastTimes );
	$shipPropertiesObject->generateTurns ( $shipProperties, $userTimes );

	//Wyzeruj odpowiednie pozycje....
	if ($shipPosition->Docked == 'yes') {
		sectorResourcePanel::getInstance()->hide ();
	} else {
		$portPanel = "&nbsp;";
	}

	//NPC Reset
	if (time () - $_SESSION ['lastNPCResetTime'] > $config ['timeThresholds'] ['npcReset']) {
		$tQuery = "SELECT
		    users.UserID
		  FROM
		    users JOIN userships USING(UserID)
		    JOIN shippositions USING(UserID)
		  WHERE
		    userships.RookieTurns > '0' AND
		    users.Type='npc' AND
		    shippositions.System='{$shipPosition->System}'
		  ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			npc::sResetNpc ( $tResult->UserID );
		}
		$_SESSION ['lastNPCResetTime'] = time ();
	}

	//NPC Move
	npc::sMove ( $userID, $actualTime, $shipPosition );

	/**
	 * Wywołanie modułów
	 */
	switch ($action) {

		case 'engageFtl':
			ftlDrive::sEngage();
			break;
				
		case 'engageActiveScanner':
			activeScanner::sEngage();
			break;

		case 'addToFiends':
			buddyList::sSendRequest($id);
			break;

		case 'buddyDecline':
			buddyList::sDecline($id);
			break;

		case 'buddyDeclineCurrent':
			buddyList::sDeclineDialog($id);
			break;

		case 'buddyAccept':
			buddyList::sAccept($id);
			break;

		case 'buddyAcceptExecute':
			buddyList::sAcceptExe($id);
			break;

		case 'showBuddy':
			buddyList::sRenderList();
			break;

		case 'accountSettingsExe':
			user::sEditOwnExe($value);
			break;

		case 'accountSettings':
			user::sEditOwnDialog();
			break;

		case 'examineMe':
			userStats::sExamineMe();
			break;

		case 'allianceCashout':
			allianceFinance::sCashoutDialog($id);
			break;

		case 'allianceCashoutExe':
			allianceFinance::sCashoutExe($id, $value);
			break;

		case 'allianceFinanceData':
			allianceFinanceRegistry::sRender();
			break;

		case 'allianceDeposit':
			allianceFinance::sDeposit();
			break;

		case 'bankDeposit':
			bank::sDeposit($value);
			break;

		case 'bankWithdraw':
			bank::sWithdraw($value);
			break;

		case 'stationRepair' :
			shipProperties::sStationRepair ();
			break;

		case 'showAlliances' :
			allianceRegistry::sRender ();
			break;

		case 'newsAgency' :
			newsAgencyRegistry::sRender ();
			break;

		case 'showFavSectors' :
			favSectorsRegistry::sRender ();
			break;

		case 'showMyMaps' :
			userMapsRegistry::sRender ();
			break;

		case 'topPlayersShow' :
			topPlayersRegistry::sRender ( $subaction );
			break;

		case 'allianceDetail':
			alliance::sRender($id);
			break;

			/*
			 * Utworzenie nowego sojuszu
			*/
		case 'allianceCreate':
			alliance::sNew();
			break;

			/*
			 * Utworzenie nowego sojuszu
			*/
		case 'allianceNewExe':
			alliance::sNewExe($value);
			break;

		case 'allianceEditData':
			alliance::sEdit();
			break;

		case 'allianceEditExe':
			alliance::sEditExe($value);
			break;

			/**
			 * Nowa wiadomość na ścianie sojuszu
			 * @since 2011-03-14
			 */
		case 'alliancPostMessage':
			alliancePost::sNew();
			break;

			/**
			 * Usuwanie wiadomości ze ściany sojuszu
			 * @since 2011-03-28
			 */
		case 'alliancePostDelete':
			alliancePost::sDelete($id);
			break;

			/**
			 * Usuwanie wiadomości ze ściany sojuszu
			 * @since 2011-03-28
			 */
		case 'alliancePostDeleteExe':
			alliancePost::sDeleteExe($id);
			break;

			/**
			 * Nowa wiadomość na ścianie sojuszu, zapis
			 * @since 2011-03-14
			 */
		case 'alliancPostMessageExe':
			alliancePost::sNewExe($value);
			break;

			/*
			 * Opuszczenia sojuszu
			*/
		case 'allianceLeave':
			alliance::sLeave();
			break;


			/**
			 * Opuszczenie sojuszu, wykonanie operacji
			 */
		case 'allianceLeaveExecute':
			alliance::sLeaveExecute();
			break;

			/*
			 * Zaakceptowanie podania do sojuszu
			*/
		case 'allianceAccept':
			allianceRequest::sAccept($id);
			break;

			/*
			 * Zaakceptowanie podania do sojuszu, wykonanie
			*/
		case 'allianceAcceptExecute':
			allianceRequest::sAcceptExecute($id);
			break;

			/*
			 * Odrzucenie podania do sojuszu
			*/
		case 'allianceDecline':
			allianceRequest::sDecline($id);
			break;

			/*
			 * Odrzucenie podania, wykonanie
			*/
		case 'allianceDeclineExecute':
			allianceRequest::sDeclineExecute($id);
			break;

			/*
			 * Dialog wyrzycania z sojuszu
			*/
		case 'allianceKick':
			alliance::sKick($id);
			break;

			/*
			 * Wyrzucenie z sojuszu
			*/
		case 'allianceKickExecute':
			alliance::sKickExe($id);
			break;

			/*
			 * Podanie o przyjęcie od sojuszu
			*/
		case 'allianceApply':
			allianceRequest::sNew($id);
			break;

			/*
			 * Podanie o przyjęcie, wykonanie
			*/
		case 'allianceApplyExe':
			allianceRequest::sNewExecute($id, $value);
			break;

			/*
			 * Lista podań o przyjęcie do sojuszu
			*/
		case 'allianceAppliances':
			allianceRequest::sRender();
			break;

		case 'allianceRightsRegistry':
			allianceRights::sRender();
			break;

		case 'setAllianceRight':
			allianceRights::sRenderForm($id);
			break;

		case 'setAllianceRightExecute':
			allianceRights::sPlayerSet($id, $value);
			break;

		case 'showMessages' :
			messageRegistry::sRender ();
			break;

		case 'showMessageText' :
			message::sGetDetail ( $id );
			break;

		case 'sendMessage' :
			message::sSend ( $userID, $id );
			break;

		case 'wantDeleteMessageExecute' :
			message::sDelete ( $id );
			break;

		case 'deleteMessage' :
			announcementPanel::getInstance()->populate(\General\Controls::sRenderDialog ( TranslateController::getDefault()->get ( 'confirm' ), TranslateController::getDefault()->get ( 'wantDeleteMessage' ), "executeAction('wantDeleteMessageExecute','',null,'$id')", 'executeAction()' ));
			break;

		case 'dropRookie' :
			announcementPanel::getInstance()->populate(\General\Controls::sRenderDialog ( TranslateController::getDefault()->get ( 'confirm' ), TranslateController::getDefault()->get ( 'Do you want to drop rookie turns?' ), "executeAction('dropRookieExe','',null,null)", 'executeAction()' ));
			break;

		case 'dropRookieExe':
			shipProperties::sDropRookie($shipProperties, $shipPropertiesObject);
			break;

		case 'sendMessageExecute' :
			message::sSendExecute ( $userID, $id, $value );
			break;

		case 'deleteFavSector' :
			navigation::sDeleteFavSector ( $id );
			favSectorsRegistry::sRender ();
			break;

		case 'addToFavSectors' :
			navigation::sAddCurrentToFavourities ();
			break;

		case 'sellWeapon' :
			shipWeapons::sSell ( $id );
			break;

		case 'sellWeaponFromCargo' :
			shipWeapons::sSellFromCargo ( $id );
			break;

		case 'buyWeapon' :
			shipWeapons::sBuy ( $id );
			break;

		case 'weaponReload' :
			shipWeapons::sReload ( $id );
			break;

		case 'weaponRepair' :
			shipWeapons::sRepair ( $id );
			break;

		case 'weaponMoveUp' :
			shipWeapons::sMoveUp ( $id );
			break;

		case 'weaponMoveDown' :
			shipWeapons::sMoveDown ( $id );
			break;

		case 'buyEquipment' :
			shipEquipment::sBuy ( $id );
			break;

		case 'stationRepairEquipment' :
			shipEquipment::sStationRepair ( $id );
			break;

		case 'sellEquipment' :
			shipEquipment::sSell ( $id );
			break;

		case 'sellEquipmentFromCargo' :
			shipEquipment::sSellFromCargo ( $id );
			break;

		case 'buyShip' :
			announcementPanel::getInstance()->populate(\General\Controls::sRenderDialog ( TranslateController::getDefault()->get ( 'confirm' ), TranslateController::getDefault()->get ( 'wantBuyShip' ), "executeAction('buyShipExecute','',null,'$id')", 'executeAction()' ));
			break;

		case 'buyShipExecute' :
			shipProperties::sBuy ( $id );
			break;

		case 'shipExamine' :
			shipExamine ( $id, $userID );
			$portPanel = "&nbsp;";
			break;

		case 'showOnlinePlayers' :
			onlinePlayersRegistry::sRender ();
			break;

		case 'nextWaypoint' :
			navigation::sNextWaypoint ();
			break;

		case 'plotReset' :
			navigation::sPlotReset ();
			break;

		case 'weaponsManagement' :
			shipProperties::sRecomputeValues($shipProperties, $userID);
			shipWeaponsRegistry::sRender ();
			break;

		case 'equiapmentManagement' :
			shipProperties::updateUsedCargo ( $shipProperties );
			shipProperties::sRecomputeValues($shipProperties, $userID);
			shipEquipmentRegistry::sRender ();
			break;

		case 'switchWeaponState' :
			$shipWeapons->switchState ( $id );
			shipProperties::sUpdateRating ( $userID );
			$weaponsPanel->render ();
			if ($subaction == 'slow') {
				shipWeaponsRegistry::sRender ();
			}
			break;

		case 'reportAbusement':
			abusement::sNew($id);
			break;

		case 'reportAbusementExe':
			abusement::sNewExe($id, $value);
			break;

	}

	if ($action == "plotSet") {
		$tCoords = explode ( "/", $value );

		foreach ( $tCoords as $key => $tValue ) {
			if (! is_numeric ( $tValue ) || $tValue == "" || $tValue == null || $tValue < 1 || $tValue > 64) {
				$error = true;
				throw new warningException ( TranslateController::getDefault()->get ( 'unknownCoords' ) );
				break;
			}
		}

		if (! $error) {
			list ( $tPlot->System, $tPlot->X, $tPlot->Y ) = $tCoords;
			unset ( $tCoords );

			//pobierz parametry systemu docelowego
			$tSystem = systemProperties::quickLoad ( $tPlot->System );

			//Warunek rozmiaru systemu
			if ($tPlot->X > $tSystem->Width || $tPlot->Y > $tSystem->Height || $tSystem->Enabled == 'no') {
				$error = true;
				throw new warningException ( TranslateController::getDefault()->get ( 'unknownCoords' ) );
			}

		}

		if (! $error) {
			$shipRouting->System = $tPlot->System;
			$shipRouting->X = $tPlot->X;
			$shipRouting->Y = $tPlot->Y;
		}
		unset ( $tSystem );

		shipRouting::checkArrive ( $shipPosition, $shipRouting );

		navigationPanel::getInstance()->render ( $shipPosition, $shipRouting, $shipProperties );

	}

	if ($action == "portBank" || $action == "portHangar" || $action == "portMarketplace" || $action == "portShipyard" || $action == "portBar" || $action == "portStorehouse") {
		portProperties::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
	}

	include "cargo.php";

	if ($action == "shipDock") {

		if ($shipProperties->Turns < $sectorProperties->MoveCost) {
			$error = true;
			throw new warningException ( TranslateController::getDefault()->get ( 'notEnoughTurns' ) );
		}

		//Sprawdzenie, czy w sektorze jest port lub stacja
		if ($portProperties->PortID == null) {
			throw new securityException ( );
		}

		if (shipProperties::sCheckMalfunction ( $shipProperties )) {
			throw new securityException (TranslateController::getDefault()->get('shipMalfunctionEmp') );
		}

		if (! $error) {
			$shipPosition->Docked = 'yes';
			$shipProperties->Turns -= $sectorProperties->MoveCost;
			if ($shipProperties->Turns < 0)
			$shipProperties->Turns = 0;
			if ($shipProperties->RookieTurns > 0) {
				$shipProperties->RookieTurns -= $sectorProperties->MoveCost;
				if ($shipProperties->RookieTurns < 0)
				$shipProperties->RookieTurns = 0;
			}

			userStats::incExperience ( $userStats, $config ['general'] ['expForMove'] );

			sectorProperties::sResetResources ( $shipPosition, $sectorProperties );
			portProperties::sReset ( $portProperties );

			sectorShipsPanel::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );

			sectorResourcePanel::getInstance()->hide ();

			$portInfoPanel->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );

			$action = 'portMarketplace';
			portProperties::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
			clearActionPanel ();
			navigationPanel::getInstance()->render ( $shipPosition, $shipRouting, $shipProperties );
		}
	}

	if ($action == "shipUnDock") {

		if (shipProperties::sCheckMalfunction ( $shipProperties )) {
			$error = true;
			announcementPanel::getInstance()->write ( 'error', TranslateController::getDefault()->get ( 'shipMalfunctionEmp' ) );
		}

		if ($shipProperties->Turns < $sectorProperties->MoveCost) {
			$error = true;
			throw new warningException ( TranslateController::getDefault()->get ( 'notEnoughTurns' ) );
		}

		//Sprawdzenie, czy w sektorze jest port lub stacja
		if ($portProperties->PortID == null) {
			throw new securityException ( );
		}

		if (! $error) {
			$shipPosition->Docked = 'no';
			$shipProperties->Turns -= $sectorProperties->MoveCost;
			if ($shipProperties->Turns < 0)
			$shipProperties->Turns = 0;
			if ($shipProperties->RookieTurns > 0) {
				$shipProperties->RookieTurns -= $sectorProperties->MoveCost;
				if ($shipProperties->RookieTurns < 0)
				$shipProperties->RookieTurns = 0;
			}

			userStats::incExperience ( $userStats, $config ['general'] ['expForMove'] );

			sectorProperties::sResetResources ( $shipPosition, $sectorProperties );
			portProperties::sReset ( $portProperties );

			sectorShipsPanel::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
			$portPanel = "&nbsp;";
			$portInfoPanel->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );
			sectorResourcePanel::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
			sectorResourcePanel::getInstance()->clearForceAction ();
			navigationPanel::getInstance()->render ( $shipPosition, $shipRouting, $shipProperties );
			clearActionPanel ();
		}
	}

	if ($action == "shipNodeJump") {

		if (shipProperties::sCheckMalfunction ( $shipProperties )) {
			$error = true;
			announcementPanel::getInstance()->write ( 'error', TranslateController::getDefault()->get ( 'shipMalfunctionEmp' ) );
		}

		if ($shipPosition->Docked == 'yes') {
			throw new securityException ( );
		}

		if ($shipProperties->Power < $config ['node'] ['jumpCostPower']) {
			$error = true;
			throw new warningException ( TranslateController::getDefault()->get ( 'notEnoughPower' ) );
		}

		if ($shipProperties->Turns < $config ['node'] ['jumpCostTurns']) {
			$error = true;
			throw new warningException ( TranslateController::getDefault()->get ( 'notEnoughTurns' ) );
		}

		if ($jumpNode == null) {
			throw new securityException ( );
		} else {
			$destination = $jumpNodeObject->getDestination ( $shipPosition );
		}

		//Jeśli nie było będu, przenieś statek do nowego sektora
		if (! $error) {
			$shipPosition->X = $destination->X;
			$shipPosition->Y = $destination->Y;
			$shipPosition->System = $destination->System;

			$shipProperties->Power -= $config ['node'] ['jumpCostPower'];
			if ($shipProperties->Power < 0)
			$shipProperties->Power = 0;
			$shipProperties->Turns -= $config ['node'] ['jumpCostTurns'];
			if ($shipProperties->Turns < 0)
			$shipProperties->Turns = 0;
			if ($shipProperties->RookieTurns > 0) {
				$shipProperties->RookieTurns -= $config ['node'] ['jumpCostTurns'];
				if ($shipProperties->RookieTurns < 0)
				$shipProperties->RookieTurns = 0;
			}

			userStats::incExperience ( $userStats, $config ['general'] ['expForMove'] );

			//Odświerz informacje o sektorze
			$sectorProperties = $sectorPropertiesObject->reload ( $shipPosition, $sectorProperties, true, true );
			$portProperties = $portPropertiesObject->reload ( $shipPosition, $portProperties, true, true );

			$systemProperties->reload( $shipPosition->System);

			$jumpNode = $jumpNodeObject->load ( $shipPosition, true, true );

			sectorProperties::sResetResources ( $shipPosition, $sectorProperties );
			portProperties::sReset ( $portProperties );

			$sectorPanel->render ( $sectorProperties, $systemProperties, $shipPosition );

			sectorShipsPanel::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
			sectorResourcePanel::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
			$portInfoPanel->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );

			$miniMap->load ( $userID, $shipPosition->System, $shipPosition );

			if (shipRouting::checkArrive ( $shipPosition, $shipRouting )) {
				navigationPanel::getInstance()->render ( $shipPosition, $shipRouting, $shipProperties );
				announcementPanel::getInstance()->write ( 'info', TranslateController::getDefault()->get ( 'infoArrived' ) );
			}

			clearActionPanel ();
		}
	}

	if ($action == "shipMove") {
		if (shipProperties::sCheckMalfunction ( $shipProperties )) {
			$error = true;
			announcementPanel::getInstance()->write ( 'error', TranslateController::getDefault()->get ( 'shipMalfunctionEmp' ) );
		}

		if ($shipPosition->Docked == 'yes') {
			throw new securityException ( );
		}

		//Sprawdzenie, czy statek może się ruszyć
		if ($shipProperties->Turns < $sectorProperties->MoveCost) {
			$error = true;
			announcementPanel::getInstance()->write ( 'warning', TranslateController::getDefault()->get ( 'notEnoughTurns' ) );
		}

		switch ($subaction) {
			case "up" :
				$newX = $shipPosition->X;
				$newY = $shipPosition->Y - 1;
				break;

			case "down" :
				$newX = $shipPosition->X;
				$newY = $shipPosition->Y + 1;
				break;

			case "left" :
				$newX = $shipPosition->X - 1;
				$newY = $shipPosition->Y;
				break;

			case "right" :
				$newX = $shipPosition->X + 1;
				$newY = $shipPosition->Y;
				break;
		}

		if ($newX < 1 || $newY < 1 || $newX > $systemProperties->Width || $newY > $systemProperties->Height) {
			throw new securityException ( );
		}

		if (! $error) {
			$shipPosition->X = $newX;
			$shipPosition->Y = $newY;
			$shipProperties->Turns -= $sectorProperties->MoveCost;
			if ($shipProperties->Turns < 0)
			$shipProperties->Turns = 0;
			if ($shipProperties->RookieTurns > 0) {
				$shipProperties->RookieTurns -= $sectorProperties->MoveCost;
				if ($shipProperties->RookieTurns < 0)
				$shipProperties->RookieTurns = 0;
			}

			$shipPosition->synchronize();

			userStats::incExperience ( $userStats, $config ['general'] ['expForMove'] );

			//Odświerz informacje o sektorze
			$sectorProperties = $sectorPropertiesObject->reload ( $shipPosition, $sectorProperties, true, true );
			$jumpNode = $jumpNodeObject->load ( $shipPosition, true, true );
			$portProperties = $portPropertiesObject->reload ( $shipPosition, $portProperties, true, true );

			portProperties::sReset ( $portProperties );
			sectorProperties::sResetResources ( $shipPosition, $sectorProperties );

			$sectorPanel->render ( $sectorProperties, $systemProperties, $shipPosition );

			sectorShipsPanel::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
			sectorResourcePanel::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
			$portInfoPanel->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );

			if (shipRouting::checkArrive ( $shipPosition, $shipRouting )) {
				announcementPanel::getInstance()->write ( 'info', TranslateController::getDefault()->get ( 'infoArrived' ) );
			}
			navigationPanel::getInstance()->render ( $shipPosition, $shipRouting, $shipProperties );

			clearActionPanel ();
		}
	}

	if ($action == "shipRefresh") {
		sectorProperties::sResetResources ( $shipPosition, $sectorProperties, false );
		portProperties::sReset ( $portProperties );
		sectorResourcePanel::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
		sectorShipsPanel::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
		$portInfoPanel->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );
		shipStatsPanel::getInstance()->render ();
		portProperties::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );

		clearActionPanel ();
	}

	if ($action == "pageReload" || $action == 'shipAttack' || $action == 'refresh' || $action == 'fireWeapons' || $action == 'disengage') {
		sectorProperties::sResetResources ( $shipPosition, $sectorProperties, false );
		portProperties::sReset ( $portProperties );

		$sectorPanel->render ( $sectorProperties, $systemProperties, $shipPosition );
		$portInfoPanel->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );

		$weaponsPanel->render ();

		sectorResourcePanel::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
		sectorShipsPanel::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
		navigationPanel::getInstance()->render ( $shipPosition, $shipRouting, $shipProperties );
		shipStatsPanel::getInstance()->render ();

		portProperties::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
		$cargoPanel->render ( $shipProperties );
		clearActionPanel ();
	}

	/**
	 * Czyszczenie cache przy pełnym odświeżeniu strony
	 */
	if ($action == "pageReload") {
		\Cache\Controller::getInstance()->clear('user::sGetOnlineCount','');
	}

	/*
	 * Renderowanie panelu linków
	*/
	linksPanel::getInstance()->render ();

	/**
	 * Wyrenderuj panel notyfikacji
	 */
	iconPanel::getInstance()->render ();

	newsAgencyPanel::getInstance()->render ( $shipPosition );

	/*
	 * Wyrenderuj stałe elementy ekranu
	*/
	$shortUserStatsPanel->render ( $userStats, $shipProperties );
	$shortShipStatsPanel->render ( $shipProperties );
	$shipMovePanel->render($systemProperties, $shipPosition, $portProperties, $shipRouting, $shipProperties);
	$miniMap->render ();

	userTimes::genAuthCode ( $userTimes, $userFastTimes );

	$out .= "<authCode>" . $userFastTimes->AuthCode . "</authCode>";

	if ($actionPanel != "")
	$out .= "<actionPanel>" . $actionPanel . "</actionPanel> ";
	if ($portPanel != "")
	$out .= "<portPanel>" . $portPanel . "</portPanel> ";

	$timek2 = microtime ();
	$arr_time = explode ( " ", $timek1 );
	$timek1 = $arr_time [1] + $arr_time [0];
	$arr_time = explode ( " ", $timek2 );
	$timek2 = $arr_time [1] + $arr_time [0];
	$czas_gen = round ( $timek2 - $timek1, 4 );

	if (!empty($config ['debug'] ['gameplayDebugOutput'])) {
		writeDebug ( "T: " . $czas_gen );
	}

	if (!empty($config ['debug'] ['script'])) {
		psScriptDebug::sSaveExecution($action, $subaction, $czas_gen);
	}

	unset($shipWeapons);
	unset($shipEquipment);

	/*
	 * Wiadomość do wszystkich
	*/
	gameplayMessage::populate(announcementPanel::getInstance());

	/**
	 * Zapisz obiekty do bazy danych i ew. cache
	 *
	 */
	$shipPosition->synchronize ();
	$userFastTimes->synchronize();
	$userTimes->synchronize ();

	$sectorPropertiesObject->synchronize ( $sectorProperties, true, true );
	$portPropertiesObject->synchronize ( $portProperties, true, true );
	$shipPropertiesObject->synchronize ( $shipProperties, true, true );
	$userPropertiesObject->synchronize ( $userProperties, true, true );
	$userStatsObject->synchronize ( $userStats, true, true );
	$shipRoutingObject->synchronize ( $shipRouting, true, true );
	$userAllianceObject->synchronize($userAlliance, true, true);

	if ($debug == "" || empty($config ['debug'] ['gameplayDebugOutput'])) {
		$debug = "&nbsp;";
	}
	$out .= "<debugPanel>" . $debug . "</debugPanel>";
	$out .= $miniMap->out ();
	$out .= $sectorPanel->out ();
	$out .= $portInfoPanel->out ();
	$out .= $shipMovePanel->out ();
	$out .= $weaponsPanel->out ();
	$out .= $cargoPanel->out ();
	$out .= $shortShipStatsPanel->out ();
	$out .= $shortUserStatsPanel->out ();
	$out .= sectorShipsPanel::getInstance()->out ();
	$out .= sectorResourcePanel::getInstance()->out ();
	$out .= navigationPanel::getInstance()->out ();
	$out .= shipStatsPanel::getInstance()->out ();
	$out .= linksPanel::getInstance()->out ();
	$out .= iconPanel::getInstance()->out ();
	$out .= newsAgencyPanel::getInstance()->out ();
	$out .= announcementPanel::getInstance()->out ();
	$out .= $activeScanner->out ();

	echo "<xml>" . $out . "</xml>";
} catch ( combatException $e ) {

	$retVal = '';

	/*
	 * Zainicjuj obiekt walki
	*/

	$Combat = new combat ( $userID, $userProperties->Language );
	$Combat->execute ( $action );
	$retVal .= $Combat;

	$timek2 = microtime ();
	$arr_time = explode ( " ", $timek1 );
	$timek1 = $arr_time [1] + $arr_time [0];
	$arr_time = explode ( " ", $timek2 );
	$timek2 = $arr_time [1] + $arr_time [0];
	$czas_gen = round ( $timek2 - $timek1, 4 );

	if (!empty($config ['debug'] ['script'])) {
		psScriptDebug::sSaveExecution($action, $subaction, $czas_gen);
	}

	echo $retVal;

} catch ( securityException $e ) {

	$tMessage = $e->getMessage();

	if (empty($tMessage)) {
		announcementPanel::getInstance()->write('error', TranslateController::getDefault()->get ( 'securityError' ));
	}else {
		announcementPanel::getInstance()->write('error', $tMessage);
	}

	echo announcementPanel::getInstance()->out();

} catch ( warningException $e ) {

	$tString = $e->getMessage ();
	if (empty ( $tString )) {
		$tString = TranslateController::getDefault()->get ( 'warning' );
	}
	announcementPanel::getInstance()->write('warning', $tString);
	echo announcementPanel::getInstance()->out();
} catch ( Exception $e ) {
	$out .= '<psDebug>' . psDebug::cThrow ( null, $e ) . '</psDebug>';
	\Cache\Controller::getInstance()->clearAll();
	echo $out;
}