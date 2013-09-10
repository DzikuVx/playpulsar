<?php

/*
 * Wykonaj przeliczenie max wartości okrętu
 */
shipProperties::sRecomputeValues($shipProperties, $userID);

$sRetVal = "<h1>" . TranslateController::getDefault()->get ( 'shipyard' ) . "</h1>";

$currentShipValue = floor ( shipProperties::sGetValue ( $userProperties->UserID ) / 2 );

$sRetVal .= "<h3>" . TranslateController::getDefault()->get ( 'shipValue' ) . ": " . $currentShipValue . "$</h3>";

//Znajdz statki, jakie port sprzedaje
$nameField = "Name" . strtoupper ( $userProperties->Language );

$sRetVal .= "<h2>" . TranslateController::getDefault()->get ( 'ships' ) . "</h2>";
$sRetVal .= "<table class='table table-striped table-condensed'>";

$sRetVal .= "<tr>";
$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'Fame' ) . "</th>";
$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
$sRetVal .= "<th style=\"width: 75px;\">&nbsp;</th>";
$sRetVal .= "</tr>";

if (!empty($portProperties->Ships)) {

	$tQuery = "SELECT
      shiptypes.*,
      shiptypes.{$nameField} AS Name
    FROM
      shiptypes
    WHERE
      shiptypes.UserBuyable='yes' AND
      shiptypes.ShipID IN (" . $portProperties->Ships . ")
    ORDER BY
      shiptypes.{$nameField} ASC
  ";
	$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$sRetVal .= "<tr>";
		$sRetVal .= "<td>" . $tR1->Name . "</td>";
		$sRetVal .= "<td>" . number_format ( $tR1->Price, 0 ) . "</td>";
		$sRetVal .= "<td>" . number_format ( $tR1->Fame, 0 ) . "</td>";
		$sRetVal .= "<td>" . $tR1->Size . "</td>";
		$sRetVal .= "<td>";

		$sRetVal .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','ship::renderDetail','{$userProperties->Language}','{$tR1->ShipID}')", 'Info' );

		if ($userStats->Fame >= $tR1->Fame && $userStats->Cash + $currentShipValue >= $tR1->Price) {
			$sRetVal .= \General\Controls::renderImgButton ( 'buy', "Playpulsar.gameplay.execute('buyShip','',null,{$tR1->ShipID},null);", TranslateController::getDefault()->get ( 'buy' ) );
		}
		$sRetVal .= "</td>";
		$sRetVal .= "</tr>";

	}
}
$sRetVal .= "</table>";

\Gameplay\Panel\PortAction::getInstance()->add($sRetVal);