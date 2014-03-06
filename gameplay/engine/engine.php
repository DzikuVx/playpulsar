<?php

use Gameplay\Exception\Overlay;
use Gameplay\Framework\Controller as GameplayController;
use Gameplay\Framework\ContentTransport;

/** @noinspection PhpIncludeInspection */
require_once '../common.php';

const TIME_MEASUREMENT_GAMEPLAY = 'Gameplay';

\General\TimeMeasurement::start(TIME_MEASUREMENT_GAMEPLAY);

$out = "";
$error = false;

$oContentTransport = ContentTransport::getInstance();
$oController = GameplayController::getInstance();

$oController->registerParameters($_REQUEST);

$userID = $_SESSION ['userID'];

$action = $oController->getParameter('action');
$subaction = $oController->getParameter('subaction');
$id = $oController->getParameter('id');
$value = $oController->getParameter('value');

try {

    /*
     * Register models for current player
    */
    $oPlayerModelProvider = \Gameplay\PlayerModelProvider::getInstance();

    if (!empty($config ['debug'] ['gameplayDebugOutput'])) {
		\Gameplay\Panel\Debug::getInstance()->add('Request action', $action);
	}

    $userProperties = $oPlayerModelProvider->register('UserEntity', new \Gameplay\Model\UserEntity($userID));

	/**
	 * Inicjalizacja klasy translacji
	 */
	TranslateController::setDefaultLanguage($userProperties->Language);
	$t = TranslateController::getDefault();

    //FIXME this will not work!!
	if (empty($config ['general'] ['enableGameplay']) && empty($_SESSION['cpLoggedUserID'])) {
		echo '<xml><actionPanel>'.\General\Controls::displayConfirmDialog(TranslateController::getDefault()->get('Announcement'), TranslateController::getDefault()->get('Gameplay disabled')).'</actionPanel></xml>';
		exit();
	}

    /** @var \Gameplay\Model\ShipPosition $shipPosition */
    $shipPosition = $oPlayerModelProvider->register('ShipPosition', new \Gameplay\Model\ShipPosition($userID));

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
		$oCacheKey = new \phpCache\CacheKey('npcCombatLocked', $tResult->UserID);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if ($oCache->check($oCacheKey) === false) {

			$oCache->set($oCacheKey, 1, 2);

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
			$oCache->clear($oCacheKey);
		}
	}

	/*
	 * jeśli przyszedł rozkaz rozpoczęcia walki
	*/
	if ($oController->getParameter('action') == 'shipAttack') {
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

    $userStats = $oPlayerModelProvider->register('UserStatistics', new \Gameplay\Model\UserStatistics($userID));
    $userTimes = $oPlayerModelProvider->register('UserTimes', new \Gameplay\Model\UserTimes($userID));
    $userFastTimes = $oPlayerModelProvider->register('UserFastTimes', new \Gameplay\Model\UserFastTimes($userID));

	//Sprawdz authorize code
	if ($oController->getParameter('action') != "pageReload" && $oController->getParameter('auth') != $userFastTimes->AuthCode) {
		throw new \Gameplay\Exception\SecurityException('Authorization Code Error');
	}

    $sectorProperties = $oPlayerModelProvider->register('SectorEntity', new \Gameplay\Model\SectorEntity($shipPosition));
    $portProperties = $oPlayerModelProvider->register('PortEntity', new \Gameplay\Model\PortEntity($shipPosition));

    /** @var \Gameplay\Model\JumpNode $jumpNode */
	$jumpNode = $oPlayerModelProvider->register('JumpNode', new \Gameplay\Model\JumpNode($shipPosition));

	/** @var \Gameplay\Model\SystemProperties $systemProperties */
    $systemProperties = $oPlayerModelProvider->register('SystemProperties', new \Gameplay\Model\SystemProperties($shipPosition->System));

    /** @var \Gameplay\Model\ShipProperties $shipProperties */
    $shipProperties = $oPlayerModelProvider->register('ShipProperties', new \Gameplay\Model\ShipProperties($userID));

	$shipRoutingObject 	= new shipRouting ( );
	$shipRouting 		= $shipRoutingObject->load ( $userID, true, true );

    /** @var \Gameplay\Model\UserAlliance $userAlliance */
    $userAlliance = $oPlayerModelProvider->register('UserAlliance', new \Gameplay\Model\UserAlliance($userID));

	/*
	 * Here place calls for all event that do not require other panels initialized
	 * or uses Gameplay\Panel\Overlay
	 */
	switch ($action) {

		case 'systemMap':

			$iSystemId = $subaction;
			if (empty($iSystemId)) {
				$iSystemId = $shipPosition->System;
			}

			$oMap = new \Gameplay\Panel\SystemMap($userID, $iSystemId, $shipPosition);
			\Gameplay\Panel\Overlay::getInstance()->add($oMap->render()->getRetVal());
			throw new Overlay();
			break;

		case 'engageActiveScanner':
			\Gameplay\Panel\ActiveScanner::sEngage();
			break;

	}


	/*
	 * Initiate all panels
	 */
	\Gameplay\Panel\Move::initiateInstance( $userProperties->Language );
	\Gameplay\Panel\ShortStats::initiateInstance( $userProperties->Language );
	\Gameplay\Panel\PlayerStats::initiateInstance($userProperties->Language);
	\Gameplay\Panel\Sector::initiateInstance($userProperties->Language);
	\Gameplay\Panel\Port::initiateInstance($userProperties->Language);
	\Gameplay\Panel\SectorShips::initiateInstance($userProperties->Language);
	\Gameplay\Panel\SectorResources::initiateInstance($userProperties->Language);
	\Gameplay\Panel\Action::initiateInstance($userProperties->Language);
	\Gameplay\Panel\PortAction::initiateInstance($userProperties->Language);
	\Gameplay\Panel\MiniMap::initiateInstance($userID, $shipPosition->System, $shipPosition, true);

	$shipCargo 		= new shipCargo ( $userID, $userProperties->Language );

    $shipWeapons = $oPlayerModelProvider->register('ShipWeapons', new \Gameplay\Model\ShipWeapons($userID, $userProperties->Language));
    $shipEquipment = $oPlayerModelProvider->register('ShipEquipments', new \Gameplay\Model\ShipEquipments($userID, $userProperties->Language));

	/*
	 * Ship auto repair
	*/
	$shipProperties->autoRepair($userFastTimes);
	$shipProperties->generateTurns ( $shipProperties, $userTimes );

	//Wyzeruj odpowiednie pozycje....
	if ($shipPosition->Docked == 'yes') {
		\Gameplay\Panel\SectorResources::getInstance()->hide();
	} else {
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

    //FIXME move npc reset into cron
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
			npc::sResetNpc($tResult->UserID);
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
			\Gameplay\Actions\FtlDrive::sEngage();
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
			\Gameplay\Model\UserStatistics::sExamineMe();
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
			\Gameplay\Actions\Bank::sDeposit($value);
			break;

		case 'bankWithdraw':
			\Gameplay\Actions\Bank::sWithdraw($value);
			break;

		case 'stationRepair' :
            \Gameplay\Model\ShipProperties::sStationRepair ();
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

		case 'myAllianceDetail':
			\Gameplay\Model\Alliance::sRender($userAlliance->AllianceID);
			break;

		case 'allianceDetail':
            \Gameplay\Model\Alliance::sRender($id);
			break;

			/*
			 * Utworzenie nowego sojuszu
			*/
		case 'allianceCreate':
            \Gameplay\Model\Alliance::sNew();
			break;

			/*
			 * Utworzenie nowego sojuszu
			*/
		case 'allianceNewExe':
            \Gameplay\Model\Alliance::sNewExe($value);
			break;

		case 'allianceEditData':
            \Gameplay\Model\Alliance::sEdit();
			break;

		case 'allianceEditExe':
            \Gameplay\Model\Alliance::sEditExe($value);
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
            \Gameplay\Model\Alliance::sLeave();
			break;


			/**
			 * Opuszczenie sojuszu, wykonanie operacji
			 */
		case 'allianceLeaveExecute':
            \Gameplay\Model\Alliance::sLeaveExecute();
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
            \Gameplay\Model\Alliance::sKick($id);
			break;

			/*
			 * Wyrzucenie z sojuszu
			*/
		case 'allianceKickExecute':
            \Gameplay\Model\Alliance::sKickExe($id);
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
			\Gameplay\Actions\Message::sGetDetail($id);
			break;

		case 'sendMessage' :
			\Gameplay\Actions\Message::sSend($userID, $id);
			break;

		case 'wantDeleteMessageExecute' :
			\Gameplay\Actions\Message::sDelete($id);
			break;

        //FIXME to chyba nie działa
		case 'deleteMessage' :
			\Gameplay\Framework\ContentTransport::getInstance()->addRawHtml(\General\Controls::sRenderDialog ( TranslateController::getDefault()->get ( 'confirm' ), TranslateController::getDefault()->get ( 'wantDeleteMessage' ), "Playpulsar.gameplay.execute('wantDeleteMessageExecute','',null,'$id')", 'Playpulsar.gameplay.execute()' ));
			break;

		case 'dropRookie' :
			\Gameplay\Framework\ContentTransport::getInstance()->addRawHtml(\General\Controls::sRenderDialog ( TranslateController::getDefault()->get ( 'confirm' ), TranslateController::getDefault()->get ( 'Do you want to drop rookie turns?' ), "Playpulsar.gameplay.execute('dropRookieExe','',null,null)", 'Playpulsar.gameplay.execute()' ));
			break;

		case 'dropRookieExe':
			\Gameplay\Model\ShipProperties::sDropRookie($shipProperties);
			break;

		case 'sendMessageExecute' :
			\Gameplay\Actions\Message::sSendExecute ( $userID, $id, $value );
			break;

		case 'deleteFavSector' :
			navigation::sDeleteFavSector ( $id );
			favSectorsRegistry::sRender ();
			break;

		case 'addToFavSectors' :
			navigation::sAddCurrentToFavourities ();
			break;

		case 'sellWeapon' :
			\Gameplay\Actions\ShipWeapons::sSell ( $id );
			break;

		case 'sellWeaponFromCargo' :
			\Gameplay\Actions\ShipWeapons::sSellFromCargo ( $id );
			break;

		case 'buyWeapon' :
			\Gameplay\Actions\ShipWeapons::sBuy($id);
			break;

		case 'weaponReload' :
			\Gameplay\Actions\ShipWeapons::sReload ( $id );
			break;

		case 'weaponRepair' :
			\Gameplay\Actions\ShipWeapons::sRepair($id);
			break;

		case 'weaponMoveUp' :
			\Gameplay\Actions\ShipWeapons::sMoveUp ( $id );
			break;

		case 'weaponMoveDown' :
			\Gameplay\Actions\ShipWeapons::sMoveDown ( $id );
			break;

		case 'buyEquipment' :
			\Gameplay\Actions\ShipEquipments::sBuy($id);
			break;

		case 'stationRepairEquipment' :
			\Gameplay\Actions\ShipEquipments::sStationRepair ( $id );
			break;

		case 'sellEquipment' :
			\Gameplay\Actions\ShipEquipments::sSell ( $id );
			break;

		case 'sellEquipmentFromCargo' :
			\Gameplay\Actions\ShipEquipments::sSellFromCargo ( $id );
			break;

		case 'buyShip' :
			\Gameplay\Framework\ContentTransport::getInstance()->addRawHtml(\General\Controls::sRenderDialog ( TranslateController::getDefault()->get ( 'confirm' ), TranslateController::getDefault()->get ( 'wantBuyShip' ), "Playpulsar.gameplay.execute('buyShipExecute','',null,'$id')", 'Playpulsar.gameplay.execute()' ));
			break;

		case 'buyShipExecute' :
            \Gameplay\Model\ShipProperties::sBuy($id);
			break;

		case 'shipExamine' :
			shipExamine($id, $userID);
			\Gameplay\Panel\PortAction::getInstance()->clear();
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
			\Gameplay\Model\ShipProperties::sRecomputeValues($shipProperties);
			shipWeaponsRegistry::sRender ();
			break;

		case 'equiapmentManagement' :
			\Gameplay\Model\ShipProperties::updateUsedCargo($shipProperties);
			\Gameplay\Model\ShipProperties::sRecomputeValues($shipProperties);
			shipEquipmentRegistry::sRender ();
			break;

		case 'switchWeaponState' :
			$shipWeapons->switchState ( $id );
			\Gameplay\Model\ShipProperties::sUpdateRating ( $userID );
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
				throw new \Gameplay\Exception\WarningException( TranslateController::getDefault()->get ( 'unknownCoords' ) );
				break;
			}
		}

		if (! $error) {

			$tPlot = new \stdClass();

			list ( $tPlot->System, $tPlot->X, $tPlot->Y ) = $tCoords;
			unset ( $tCoords );

			//pobierz parametry systemu docelowego
			$tSystem = new \Gameplay\Model\SystemProperties($tPlot->System);

			//Warunek rozmiaru systemu
			if ($tPlot->X > $tSystem->Width || $tPlot->Y > $tSystem->Height || $tSystem->Enabled == 'no') {
				$error = true;
				throw new \Gameplay\Exception\WarningException( TranslateController::getDefault()->get ( 'unknownCoords' ) );
			}

		}

		if (! $error) {
			$shipRouting->System = $tPlot->System;
			$shipRouting->X = $tPlot->X;
			$shipRouting->Y = $tPlot->Y;
		}
		unset ( $tSystem );

		shipRouting::checkArrive ( $shipPosition, $shipRouting );

		\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting);

	}

    include "cargo.php";

	if ($action == "portBank" || $action == "portHangar" || $action == "portMarketplace" || $action == "portShipyard" || $action == "portBar" || $action == "portStorehouse") {
		\Gameplay\Model\PortEntity::sPopulatePanel($userID, $shipPosition, $portProperties, $action, $subaction, $value, $id);
	}

	if ($action == "shipDock") {

		if ($shipProperties->Turns < $sectorProperties->MoveCost) {
			$error = true;
			throw new \Gameplay\Exception\WarningException( TranslateController::getDefault()->get ( 'notEnoughTurns' ) );
		}

		//Sprawdzenie, czy w sektorze jest port lub stacja
		if ($portProperties->PortID == null) {
			throw new \Gameplay\Exception\SecurityException( );
		}

		if ($shipProperties->checkMalfunction()) {
			throw new \Gameplay\Exception\SecurityException(TranslateController::getDefault()->get('shipMalfunctionEmp') );
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

            $userStats->incExperience($config ['general'] ['expForMove']);

            $sectorProperties->resetResources();

            $portProperties->reset();

			\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );

			\Gameplay\Panel\SectorResources::getInstance()->hide ();

			\Gameplay\Panel\Port::getInstance()->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );

			$action = 'portMarketplace';
			\Gameplay\Model\PortEntity::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
			\Gameplay\Panel\Action::getInstance()->clear ();
			\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting);
		}
	}

	if ($action == "shipUnDock") {

		if ($shipProperties->checkMalfunction()) {
			$error = true;
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'error', '{T:shipMalfunctionEmp}');
		}

		if ($shipProperties->Turns < $sectorProperties->MoveCost) {
			throw new \Gameplay\Exception\WarningException('{T:notEnoughTurns}');
		}

		//Sprawdzenie, czy w sektorze jest port lub stacja
		if ($portProperties->PortID == null) {
			throw new \Gameplay\Exception\SecurityException();
		}

		if (! $error) {
			$shipPosition->Docked = 'no';
			$shipProperties->Turns -= $sectorProperties->MoveCost;

			if ($shipProperties->Turns < 0) {
				$shipProperties->Turns = 0;
			}

			if ($shipProperties->RookieTurns > 0) {
				$shipProperties->RookieTurns -= $sectorProperties->MoveCost;
				if ($shipProperties->RookieTurns < 0) {
					$shipProperties->RookieTurns = 0;
				}
			}

            $userStats->incExperience($config ['general'] ['expForMove']);

            $sectorProperties->resetResources();
            $portProperties->reset();
			\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
			\Gameplay\Panel\Port::getInstance()->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );
			\Gameplay\Panel\SectorResources::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
			\Gameplay\Panel\SectorResources::getInstance()->clearForceAction ();
			\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting);
		}
	}

	if ($action == "shipNodeJump") {

		if ($shipProperties->checkMalfunction()) {
			$error = true;
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'error', '{T:shipMalfunctionEmp}');
		}

		if ($shipPosition->Docked == 'yes') {
			throw new \Gameplay\Exception\SecurityException ( );
		}

		if ($shipProperties->Power < $config ['node'] ['jumpCostPower']) {
			$error = true;
			throw new \Gameplay\Exception\WarningException ( TranslateController::getDefault()->get ( 'notEnoughPower' ) );
		}

		if ($shipProperties->Turns < $config ['node'] ['jumpCostTurns']) {
			$error = true;
			throw new \Gameplay\Exception\WarningException ( TranslateController::getDefault()->get ( 'notEnoughTurns' ) );
		}

		if (empty($jumpNode->NodeID)) {
			throw new \Gameplay\Exception\SecurityException ( );
		} else {
			$destination = $jumpNode->getDestination($shipPosition);
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

            $userStats->incExperience($config ['general'] ['expForMove']);

			$sectorProperties->reload($shipPosition);
			$portProperties->reload($shipPosition);

			$systemProperties->reload( $shipPosition->System);

			$jumpNode = $oPlayerModelProvider->register('JumpNode', new \Gameplay\Model\JumpNode($shipPosition));

            $sectorProperties->resetResources();
            $portProperties->reset();
			\Gameplay\Panel\Sector::getInstance()->render ( $sectorProperties, $systemProperties, $shipPosition );

			\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
			\Gameplay\Panel\SectorResources::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
			\Gameplay\Panel\Port::getInstance()->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );
            \Gameplay\Panel\MiniMap::initiateInstance($userID, $shipPosition->System, $shipPosition);

			if (shipRouting::checkArrive($shipPosition, $shipRouting)) {
				\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting);
				\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:infoArrived}');
			}

		}
	}

	if ($action == "shipMove") {
		if ($shipProperties->checkMalfunction()) {
			$error = true;
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'error', '{T:shipMalfunctionEmp}');
		}

		if ($shipPosition->Docked == 'yes') {
			throw new \Gameplay\Exception\SecurityException ( );
		}

		if ($shipProperties->Turns < $sectorProperties->MoveCost) {
			$error = true;
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'warning', '{T:notEnoughTurns}');
		}

        $newX = $shipPosition->X;
        $newY = $shipPosition->Y;

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
			throw new \Gameplay\Exception\SecurityException ( );
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

            $userStats->incExperience($config ['general'] ['expForMove']);

            $sectorProperties->reload($shipPosition);
            $jumpNode = $oPlayerModelProvider->register('JumpNode', new \Gameplay\Model\JumpNode($shipPosition));
            $portProperties->reload($shipPosition);

            $portProperties->reset();
            $sectorProperties->resetResources();
			\Gameplay\Panel\Sector::getInstance()->render($sectorProperties, $systemProperties, $shipPosition);
			\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
			\Gameplay\Panel\SectorResources::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
			\Gameplay\Panel\Port::getInstance()->render($shipPosition, $portProperties, $shipProperties, $jumpNode );

			if (shipRouting::checkArrive ( $shipPosition, $shipRouting )) {
				\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:infoArrived}');
			}
			\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting);
		}
	}

	if ($action == "shipRefresh") {
        $sectorProperties->resetResources(false);
        $portProperties->reset();
		\Gameplay\Panel\SectorResources::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
		\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
		\Gameplay\Panel\Port::getInstance()->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );
		\Gameplay\Model\PortEntity::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
	}

	if ($action == "pageReload" || $action == 'shipAttack' || $action == 'refresh' || $action == 'fireWeapons' || $action == 'disengage') {
        $sectorProperties->resetResources(false);
        $portProperties->reset();
		\Gameplay\Panel\Sector::getInstance()->render ( $sectorProperties, $systemProperties, $shipPosition );
		\Gameplay\Panel\Port::getInstance()->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );
		\Gameplay\Panel\SectorResources::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
		\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
		\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting);
		\Gameplay\Model\PortEntity::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
	}

	/**
	 * Czyszczenie cache przy pełnym odświeżeniu strony
	 */
	if ($action == "pageReload") {
        \phpCache\Factory::getInstance()->create()->clear(new \phpCache\CacheKey('user::sGetOnlineCount', ''));
	}

	/*
	 * Wyrenderuj stałe elementy ekranu
	*/
	\Gameplay\Panel\Icons::getInstance()->render();
	\Gameplay\Panel\PlayerStats::getInstance()->render ( $userStats, $shipProperties );
	\Gameplay\Panel\ShortStats::getInstance()->render ( $shipProperties, $shipWeapons, $shipEquipment );
	\Gameplay\Panel\Move::getInstance()->render($systemProperties, $shipPosition, $portProperties, $shipRouting, $shipProperties);
	\Gameplay\Panel\MiniMap::getInstance()->render();

    \Gameplay\Model\UserTimes::genAuthCode($userTimes, $userFastTimes);

	$oContentTransport->addVariable('AuthCode', $userFastTimes->AuthCode);

    \General\TimeMeasurement::stop(TIME_MEASUREMENT_GAMEPLAY);

    $fMeasuredTime = \General\TimeMeasurement::get(TIME_MEASUREMENT_GAMEPLAY);

	if (!empty($config ['debug'] ['gameplayDebugOutput'])) {
		\Gameplay\Panel\Debug::getInstance()->add('Execution time', $fMeasuredTime);
	}

	if (!empty($config ['debug'] ['script'])) {
		psScriptDebug::sSaveExecution($action, $subaction, $fMeasuredTime);
	}

	/*
	 * Wiadomość do wszystkich
	*/
	//FIXME implement
// 	gameplayMessage::populate(announcementPanel::getInstance());

	/**
	 * Zapisz obiekty do bazy danych i ew. cache
	 */

    $shipEquipment->synchronize();
    $shipWeapons->synchronize();

	$shipPosition->synchronize ();
	$userFastTimes->synchronize();
	$userTimes->synchronize();
    $shipProperties->synchronize();
    $userStats->synchronize();
    $portProperties->synchronize();
    $sectorProperties->synchronize();
    $userProperties->synchronize();

	$shipRoutingObject->synchronize($shipRouting, true, true);

// 	$out .= announcementPanel::getInstance()->out ();

	$oContentTransport->addPanel(\Gameplay\Panel\Move::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\Port::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\SectorResources::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\SectorShips::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\PlayerStats::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\Sector::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\ShortStats::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\Action::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\PortAction::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\MiniMap::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\Navigation::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\Icons::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\Overlay::getInstance());
	$oContentTransport->addPanel(\Gameplay\Panel\Debug::getInstance());

	/*
	 * Echo prepared JSON for panel transport
	 */
	echo $oContentTransport->get();

} catch (\Gameplay\Exception\Overlay $e) {

	$oContentTransport->addPanel(\Gameplay\Panel\Overlay::getInstance());

	echo $oContentTransport->get();

} catch ( combatException $e ) {

    /** @noinspection PhpUndefinedVariableInspection */
    $Combat = new combat($userID, $userProperties->Language);
	$Combat->execute($action);

	\Gameplay\Panel\Overlay::getInstance()->setParams(array('closer' => false));
	\Gameplay\Panel\Overlay::getInstance()->clear();
	\Gameplay\Panel\Overlay::getInstance()->add((string) $Combat);

    \General\TimeMeasurement::stop(TIME_MEASUREMENT_GAMEPLAY);

	if (!empty($config ['debug'] ['script'])) {
		psScriptDebug::sSaveExecution($action, $subaction, \General\TimeMeasurement::get(TIME_MEASUREMENT_GAMEPLAY));
	}

	$oContentTransport->addVariable('AuthCode', $Combat->getAuthCode());

	$oContentTransport->addPanel(\Gameplay\Panel\Overlay::getInstance());

	echo $oContentTransport->get();

} catch (\Gameplay\Exception\SecurityException $e ) {

	if (empty($oContentTransport)) {
		$oContentTransport	= ContentTransport::getInstance();
	}

	/**
	 * @var string
	 */
	$tMessage = $e->getMessage();

	if (empty($tMessage)) {
		$oContentTransport->addNotification('error', '{T:securityError}');
	}else {
		$oContentTransport->addNotification('error', $tMessage);
	}

	echo $oContentTransport->get();

} catch ( \Gameplay\Exception\WarningException $e ) {

	if (empty($oContentTransport)) {
		$oContentTransport 	= ContentTransport::getInstance();
	}

	$tString = $e->getMessage ();
	if (empty ( $tString )) {
		$tString = '{T:warning}';
	}

	$oContentTransport->addNotification('warning', $tString);

	echo $oContentTransport->get();

} catch ( Exception $e ) {

	/*
	 * Rethrow exception and send error notification
	 */
	psDebug::cThrow(null, $e);

	if (empty($oContentTransport)) {
		$oContentTransport 	= ContentTransport::getInstance();
	}

	$oContentTransport->addNotification('error', '{T:An error occurred, try again or contact game administrators}');

    \phpCache\Factory::getInstance()->create()->clearAll();

	echo $oContentTransport->get();
}