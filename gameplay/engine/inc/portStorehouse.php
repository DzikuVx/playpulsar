<?php

use General\Formater;

global $userID, $portProperties, $userProperties, $shipProperties, $shipPosition, $config;

$sRetVal = "<h1>{T:storehouse}</h1>";

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
	$sRetVal .= "<h2 style='color: #f00000;'>{T:noStorageSpace}</h2>";
} else {
	//Pokaż przedmioty w magazynie

	$sRetVal .= "<table class='table table-striped table-condensed'>";

	$sRetVal .= "<tr>";
	$sRetVal .= "<th>{T:cargo}</th>";
	$sRetVal .= "<th style='width: 60px;'>{T:size}</th>";
	$sRetVal .= "<th style='width: 60px;'>{T:amount}</th>";
	$sRetVal .= "<th style='width: 60px;'>{T:total}</th>";
	$sRetVal .= "<th style='width: 8em;'>&nbsp;</th>";
	$sRetVal .= "</tr>";

	$tQuery = $storageCargo->getProducts ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		if ($bFreeCargoAvaible && $shipPosition->Docked == 'yes') {

			$actionString .= \General\Controls::renderImgButton ( 'left', "Playpulsar.gameplay.execute('toCargohold','product','1','{$tR1->ID}',null);", '{T:toCargoholdOne}');
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "Playpulsar.gameplay.execute('toCargohold','product','all','{$tR1->ID}',null);", '{T:toCargoholdAll}');

		} else {
			$actionString = "&nbsp;";
		}

		$sRetVal .= storageCargo::displayTableRow ( $tR1, $actionString, "green" );
	}

	//Itemy
	$tQuery = $storageCargo->getItems ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';
		if ($bFreeCargoAvaible && $shipPosition->Docked == 'yes') {

			$actionString .= \General\Controls::renderImgButton ( 'left', "Playpulsar.gameplay.execute('toCargohold','item','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "Playpulsar.gameplay.execute('toCargohold','item','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );
		} else {
			$actionString = "&nbsp;";
		}

		$sRetVal .= storageCargo::displayTableRow ( $tR1, $actionString, "yellow" );

	}

	//Uzbrojenie
	$tQuery = $storageCargo->getWeapons ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';

		$actionString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','weapon::renderDetail','{$userProperties->Language}','{$tR1->WeaponID}')", 'Info' );

		if ($bFreeCargoAvaible && $shipPosition->Docked == 'yes') {
			$actionString .= \General\Controls::renderImgButton ( 'left', "Playpulsar.gameplay.execute('toCargohold','weapon','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "Playpulsar.gameplay.execute('toCargohold','weapon','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );
		} else {
			$actionString = "&nbsp;";
		}

		$sRetVal .= storageCargo::displayTableRow ( $tR1, $actionString, "red" );
	}

	//Equipment
	$tQuery = $storageCargo->getEquipments ();
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$actionString = '';

		$actionString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','equipment::renderDetail','{$userProperties->Language}','{$tR1->EquipmentID}')", 'Info' );

		if ($bFreeCargoAvaible && $shipPosition->Docked == 'yes') {
			$actionString .= \General\Controls::renderImgButton ( 'left', "Playpulsar.gameplay.execute('toCargohold','equipment','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdOne' ) );
			$actionString .= \General\Controls::renderImgButton ( 'leftFar', "Playpulsar.gameplay.execute('toCargohold','equipment','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toCargoholdAll' ) );

		} else {
			$actionString = "&nbsp;";
		}
		$sRetVal .= storageCargo::displayTableRow ( $tR1, $actionString );
	}
	$sRetVal .= "</table>";
}

unset($storageCargo);

//Możliwość zakupu nowego miejsca
$sRetVal .= "<h2>{T:storageSpaceParameters}</h2>";
$sRetVal .= "<div class='infoLine'><b>{T:totalSpace}: </b> " . $totalStorageRoom . "</div>";
$sRetVal .= "<div class='infoLine'><b>{T:freeSpace}: </b> " . ($totalStorageRoom - $usedStorageRoom) . "</div>";
$sRetVal .= "<div class='infoLine'><b>{T:buy}: </b>";
$sRetVal .= \General\Controls::bootstrapButton($config ['port'] ['storageSpace'] . " " . '{T:buyFor}' . " " . Formater::formatValue($config ['port'] ['storageSpacePrice']), "Playpulsar.gameplay.execute('buyStorageRoom',null,null,null,null);");
$sRetVal .= "</div>";

\Gameplay\Panel\PortAction::getInstance()->add($sRetVal);