<?php

use General\Formater;

$portPanel .= "<h1>{T:storehouse}</h1>";

$storageCargo = new storageCargo ( $userID, $portProperties->PortID, $userProperties->Language );

//Sprawdz, czy gracz ma wykupione miejsce w magazynie
$totalStorageRoom = storageCargo::sGetTotalUserSpace ( $userID, $portProperties->PortID );
$usedStorageRoom = $storageCargo->getUsage ();

if ($shipProperties->CargoMax == $shipProperties->Cargo) {
	$bFreeCargoAvaible = false;
}else {
	$bFreeCargoAvaible = true;
}

if ($totalStorageRoom == 0) {
	//Brak wykupionego miejsca w magazynie
	$portPanel .= "<h2 style='color: #f00000;'>{T:noStorageSpace}</h2>";
} else {
	//Pokaż przedmioty w magazynie

	$portPanel .= "<table class='table table-striped table-condensed'>";

	$portPanel .= "<tr>";
	$portPanel .= "<th>{T:cargo}</th>";
	$portPanel .= "<th style='width: 60px;'>{T:size}</th>";
	$portPanel .= "<th style='width: 60px;'>{T:amount}</th>";
	$portPanel .= "<th style='width: 60px;'>{T:total}</th>";
	$portPanel .= "<th style='width: 8em;'>&nbsp;</th>";
	$portPanel .= "</tr>";

	$tQuery = $storageCargo->getProducts ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		if ($bFreeCargoAvaible && $shipPosition->Docked == 'yes') {

			$actionString .= \General\Controls::renderImgButton ( 'left', "executeAction('toCargohold','product','1','{$tR1->ID}',null);", '{T:toCargoholdOne}');
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "executeAction('toCargohold','product','all','{$tR1->ID}',null);", '{T:toCargoholdAll}');

		} else {
			$actionString = "&nbsp;";
		}

		$portPanel .= storageCargo::displayTableRow ( $tR1, $actionString, "green" );
	}

	//Itemy
	$tQuery = $storageCargo->getItems ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		if ($bFreeCargoAvaible && $shipPosition->Docked == 'yes') {

			$actionString .= \General\Controls::renderImgButton ( 'left', "executeAction('toCargohold','item','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "executeAction('toCargohold','item','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );
		} else {
			$actionString = "&nbsp;";
		}

		$portPanel .= storageCargo::displayTableRow ( $tR1, $actionString, "yellow" );

	}

	//Uzbrojenie
	$tQuery = $storageCargo->getWeapons ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		
		$actionString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','weapon::renderDetail','{$userProperties->Language}','{$tR1->WeaponID}')", 'Info' );
		
		if ($bFreeCargoAvaible && $shipPosition->Docked == 'yes') {
			$actionString .= \General\Controls::renderImgButton ( 'left', "executeAction('toCargohold','weapon','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "executeAction('toCargohold','weapon','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );
		} else {
			$actionString = "&nbsp;";
		}

		$portPanel .= storageCargo::displayTableRow ( $tR1, $actionString, "red" );
	}

	//Equipment
	$tQuery = $storageCargo->getEquipments ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		
		$actionString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','equipment::renderDetail','{$userProperties->Language}','{$tR1->EquipmentID}')", 'Info' );
		
		if ($bFreeCargoAvaible && $shipPosition->Docked == 'yes') {
			$actionString .= \General\Controls::renderImgButton ( 'left', "executeAction('toCargohold','equipment','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "executeAction('toCargohold','equipment','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );
				
		} else {
			$actionString = "&nbsp;";
		}
		$portPanel .= storageCargo::displayTableRow ( $tR1, $actionString );
	}
	$portPanel .= "</table>";
}

unset($storageCargo);

//Możliwość zakupu nowego miejsca
$portPanel .= "<h2>{T:storageSpaceParameters}</h2>";
$portPanel .= "<div class='infoLine'><b>{T:totalSpace}: </b> " . $totalStorageRoom . "</div>";
$portPanel .= "<div class='infoLine'><b>{T:freeSpace}: </b> " . ($totalStorageRoom - $usedStorageRoom) . "</div>";
$portPanel .= "<div class='infoLine'><b>{T:buy}: </b>";
$portPanel .= \General\Controls::bootstrapButton($config ['port'] ['storageSpace'] . " " . '{T:buyFor}' . " " . Formater::formatValue($config ['port'] ['storageSpacePrice']), "executeAction('buyStorageRoom',null,null,null,null);");
$portPanel .= "</div>";