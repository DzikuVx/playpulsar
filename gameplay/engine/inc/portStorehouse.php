<?php

$portPanel .= "<h1>" . TranslateController::getDefault()->get ( 'storehouse' ) . "</h1>";

$storageCargo = new storageCargo ( $userID, $portProperties->PortID, $userProperties->Language );

//Sprawdz, czy gracz ma wykupione miejsce w magazynie
$totalStorageRoom = storageCargo::sGetTotalUserSpace ( $userID, $portProperties->PortID );
$usedStorageRoom = $storageCargo->getUsage ();

if ($totalStorageRoom == 0) {
	//Brak wykupionego miejsca w magazynie
	$portPanel .= "<h2 style='color: #f00000;'>" . TranslateController::getDefault()->get ( 'noStorageSpace' ) . "</h2>";
} else {
	//Pokaż przedmioty w magazynie

	$portPanel .= "<table class='transactionList' cellspacing='2' cellpadding='0'>";

	$portPanel .= "<tr>";
	$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'cargo' ) . "</th>";
	$portPanel .= "<th style='width: 60px;'>" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
	$portPanel .= "<th style='width: 60px;'>" . TranslateController::getDefault()->get ( 'amount' ) . "</th>";
	$portPanel .= "<th style='width: 60px;'>" . TranslateController::getDefault()->get ( 'total' ) . "</th>";
	$portPanel .= "<th style='width: 6em;'>&nbsp;</th>";
	$portPanel .= "</tr>";

	$tQuery = $storageCargo->getProducts ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		if ($shipPosition->Docked == 'yes') {

			$actionString .= \General\Controls::renderImgButton ( 'left', "executeAction('toCargohold','product','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "executeAction('toCargohold','product','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );

		} else {
			$actionString = "&nbsp;";
		}

		$portPanel .= storageCargo::displayTableRow ( $tR1, $actionString, "transactionListGreen" );
	}

	//Itemy
	$tQuery = $storageCargo->getItems ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		if ($shipPosition->Docked == 'yes') {

			$actionString .= \General\Controls::renderImgButton ( 'left', "executeAction('toCargohold','item','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "executeAction('toCargohold','item','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );
		} else {
			$actionString = "&nbsp;";
		}

		$portPanel .= storageCargo::displayTableRow ( $tR1, $actionString, "transactionListYellow" );

	}

	//Uzbrojenie
	$tQuery = $storageCargo->getWeapons ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		
		$actionString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','weapon::renderDetail','{$userProperties->Language}','{$tR1->WeaponID}')", 'Info' );
		
		if ($shipPosition->Docked == 'yes') {
			$actionString .= \General\Controls::renderImgButton ( 'left', "executeAction('toCargohold','weapon','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "executeAction('toCargohold','weapon','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );
		} else {
			$actionString = "&nbsp;";
		}

		$portPanel .= storageCargo::displayTableRow ( $tR1, $actionString, "transactionListRed" );
	}

	//Equipment
	$tQuery = $storageCargo->getEquipments ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		
		$actionString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','equipment::renderDetail','{$userProperties->Language}','{$tR1->EquipmentID}')", 'Info' );
		
		if ($shipPosition->Docked == 'yes') {
			$actionString .= \General\Controls::renderImgButton ( 'left', "executeAction('toCargohold','equipment','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "executeAction('toCargohold','equipment','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );
				
		} else {
			$actionString = "&nbsp;";
		}
		$portPanel .= storageCargo::displayTableRow ( $tR1, $actionString, "transactionList" );
	}
	$portPanel .= "</table>";
}

unset($storageCargo);

//Możliwość zakupu nowego miejsca
$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'storageSpaceParameters' ) . "</h2>";
$portPanel .= "<div class='infoLine'><b>" . TranslateController::getDefault()->get ( 'totalSpace' ) . ": </b> " . $totalStorageRoom . "</div>";
$portPanel .= "<div class='infoLine'><b>" . TranslateController::getDefault()->get ( 'freeSpace' ) . ": </b> " . ($totalStorageRoom - $usedStorageRoom) . "</div>";
$portPanel .= "<div class='infoLine'><b>" . TranslateController::getDefault()->get ( 'buy' ) . ": </b>";
$portPanel .= \General\Controls::renderButton($config ['port'] ['storageSpace'] . " " . TranslateController::getDefault()->get ( 'buyFor' ) . " " . $config ['port'] ['storageSpacePrice'] . "$", "executeAction('buyStorageRoom',null,null,null,null);");
$portPanel .= "</div>";