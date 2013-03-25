<?php

/*
 * Wykonaj przeliczenie max wartości okrętu
 */
shipProperties::sRecomputeValues($shipProperties, $userID);

$portPanel .= "<h1>" . TranslateController::getDefault()->get ( 'hangar' ) . "</h1>";

$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/shipRepairTable.html' );
$template->add ( $shipProperties );
$template->add ( 'EmpRegeneration', $config ['emp'] ['repairRatio'] );

//@todo: zbiorcza naprawa wszystkiego jednym przyciskiem

shipProperties::sRenderRepairButtons ( $template, 'hangar' );

$portPanel .= $template;

if ($portProperties->Type == 'station') {

	$nameField = "Name" . strtoupper ( $userProperties->Language );

	$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'weapons' ) . "</h2>";
	$portPanel .= "<table class='table table-striped table-condensed'>";

	$portPanel .= "<tr>";
	$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
	$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
	$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'Fame' ) . "</th>";
	$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
	$portPanel .= "<th style='width: 75px;'>&nbsp;</th>";
	$portPanel .= "</tr>";

	if (!empty($portProperties->Weapons)) {

		$tQuery = "SELECT
      weapontypes.*,
      weapontypes.{$nameField} AS Name
    FROM
      weapontypes
    WHERE
      weapontypes.Active='yes' AND
      weapontypes.PortWeapon='no' AND
      weapontypes.WeaponID IN (" . $portProperties->Weapons . ")
    ORDER BY
      weapontypes.{$nameField} ASC
  ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$portPanel .= "<tr>";
			$portPanel .= "<td>" . $tR1->Name . "</td>";
			$portPanel .= "<td>" . number_format ( $tR1->Price, 0 ) . "</td>";
			$portPanel .= "<td>" . number_format ( $tR1->Fame, 0 ) . "</td>";
			$portPanel .= "<td>" . $tR1->Size . "</td>";
			$portPanel .= "<td>";

			$portPanel .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','weapon::renderDetail','{$userProperties->Language}','{$tR1->WeaponID}')", 'Info' );

			if ($userStats->Fame >= $tR1->Fame && $userStats->Cash >= $tR1->Price && $shipProperties->CurrentWeapons < $shipProperties->MaxWeapons && $shipProperties->WeaponSize >= $tR1->Size) {
				$portPanel .= \General\Controls::renderImgButton ( 'buy', "executeAction('buyWeapon','',null,{$tR1->WeaponID},null);", TranslateController::getDefault()->get ( 'buy' ) );
			}
			$portPanel .= "</td>";
			$portPanel .= "</tr>";

		}
	}
	$portPanel .= "</table>";

	$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'equipment' ) . "</h2>";
	$portPanel .= "<table class='table table-striped table-condensed'>";

	$portPanel .= "<tr>";
	$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
	$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
	$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'Fame' ) . "</th>";
	$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
	$portPanel .= "<th style='width: 75px;'>&nbsp;</th>";
	$portPanel .= "</tr>";

	global $shipEquipment;

	if (!empty($portProperties->Equipment)) {
		$tQuery = "SELECT
      equipmenttypes.*,
      equipmenttypes.{$nameField} AS Name
    FROM
      equipmenttypes
    WHERE
      equipmenttypes.Active='yes' AND
      equipmenttypes.EquipmentID IN (" . $portProperties->Equipment . ")
    ORDER BY
      equipmenttypes.{$nameField} ASC
  ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$portPanel .= "<tr>";
			$portPanel .= "<td>" . $tR1->Name . "</td>";
			$portPanel .= "<td>" . number_format ( $tR1->Price, 0 ) . "</td>";
			$portPanel .= "<td>" . number_format ( $tR1->Fame, 0 ) . "</td>";
			$portPanel .= "<td>" . $tR1->Size . "</td>";
			$portPanel .= "<td>";

			$portPanel .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','equipment::renderDetail','{$userProperties->Language}','{$tR1->EquipmentID}')", 'Info' );

			if ($tR1->Type == 'equipment' && $shipEquipment->checkExists ( $tR1 )) {
				$goUniqueBuy = false;
			} else {
				$goUniqueBuy = true;
			}

			if ($goUniqueBuy && $userStats->Fame >= $tR1->Fame && $userStats->Cash >= $tR1->Price && ($shipProperties->MaxEquipment - $shipProperties->CurrentEquipment) >= $tR1->Size) {
				$portPanel .= \General\Controls::renderImgButton ( 'buy', "executeAction('buyEquipment','',null,{$tR1->EquipmentID},null);", TranslateController::getDefault()->get ( 'buy' ) );
			}
			$portPanel .= "</td>";
			$portPanel .= "</tr>";

		}
	}
	$portPanel .= "</table>";
}