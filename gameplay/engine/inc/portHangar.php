<?php

global $config, $shipProperties, $userID, $userStats;

$userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');

/*
 * Wykonaj przeliczenie max wartości okrętu
 */
\Gameplay\Model\ShipProperties::sRecomputeValues($shipProperties, $userID);

$sRetVal = "<h1>" . TranslateController::getDefault()->get ( 'hangar' ) . "</h1>";

$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/shipRepairTable.html' );
$template->add ( $shipProperties );
$template->add ( 'EmpRegeneration', $config ['emp'] ['repairRatio'] );

//@todo: zbiorcza naprawa wszystkiego jednym przyciskiem

\Gameplay\Model\ShipProperties::sRenderRepairButtons ( $template, 'hangar' );

$sRetVal .= $template;

if ($portProperties->Type == 'station') {

	$nameField = "Name" . strtoupper ( $userProperties->Language );

	$sRetVal .= "<h2>" . TranslateController::getDefault()->get ( 'weapons' ) . "</h2>";
	$sRetVal .= "<table class='table table-striped table-condensed'>";

	$sRetVal .= "<tr>";
	$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
	$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
	$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'Fame' ) . "</th>";
	$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
	$sRetVal .= "<th style='width: 75px;'>&nbsp;</th>";
	$sRetVal .= "</tr>";

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

			$sRetVal .= "<tr>";
			$sRetVal .= "<td>" . $tR1->Name . "</td>";
			$sRetVal .= "<td>" . number_format ( $tR1->Price, 0 ) . "</td>";
			$sRetVal .= "<td>" . number_format ( $tR1->Fame, 0 ) . "</td>";
			$sRetVal .= "<td>" . $tR1->Size . "</td>";
			$sRetVal .= "<td>";

			$sRetVal .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','weapon::renderDetail','{$userProperties->Language}','{$tR1->WeaponID}')", 'Info' );

			if ($userStats->Fame >= $tR1->Fame && $userStats->Cash >= $tR1->Price && $shipProperties->CurrentWeapons < $shipProperties->MaxWeapons && $shipProperties->WeaponSize >= $tR1->Size) {
				$sRetVal .= \General\Controls::renderImgButton ( 'buy', "Playpulsar.gameplay.execute('buyWeapon','',null,{$tR1->WeaponID},null);", TranslateController::getDefault()->get ( 'buy' ) );
			}
			$sRetVal .= "</td>";
			$sRetVal .= "</tr>";

		}
	}
	$sRetVal .= "</table>";

	$sRetVal .= "<h2>" . TranslateController::getDefault()->get ( 'equipment' ) . "</h2>";
	$sRetVal .= "<table class='table table-striped table-condensed'>";

	$sRetVal .= "<tr>";
	$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
	$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
	$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'Fame' ) . "</th>";
	$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
	$sRetVal .= "<th style='width: 75px;'>&nbsp;</th>";
	$sRetVal .= "</tr>";

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

			$sRetVal .= "<tr>";
			$sRetVal .= "<td>" . $tR1->Name . "</td>";
			$sRetVal .= "<td>" . number_format ( $tR1->Price, 0 ) . "</td>";
			$sRetVal .= "<td>" . number_format ( $tR1->Fame, 0 ) . "</td>";
			$sRetVal .= "<td>" . $tR1->Size . "</td>";
			$sRetVal .= "<td>";

			$sRetVal .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','equipment::renderDetail','{$userProperties->Language}','{$tR1->EquipmentID}')", 'Info' );

			if ($tR1->Type == 'equipment' && $shipEquipment->checkExists ( $tR1 )) {
				$goUniqueBuy = false;
			} else {
				$goUniqueBuy = true;
			}

			if ($goUniqueBuy && $userStats->Fame >= $tR1->Fame && $userStats->Cash >= $tR1->Price && ($shipProperties->MaxEquipment - $shipProperties->CurrentEquipment) >= $tR1->Size) {
				$sRetVal .= \General\Controls::renderImgButton ( 'buy', "Playpulsar.gameplay.execute('buyEquipment','',null,{$tR1->EquipmentID},null);", TranslateController::getDefault()->get ( 'buy' ) );
			}
			$sRetVal .= "</td>";
			$sRetVal .= "</tr>";

		}
	}
	$sRetVal .= "</table>";
}

\Gameplay\Panel\PortAction::getInstance()->add($sRetVal);