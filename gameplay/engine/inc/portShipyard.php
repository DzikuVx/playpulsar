<?php

/*
 * Wykonaj przeliczenie max wartości okrętu
 */
shipProperties::sRecomputeValues($shipProperties, $userID);

$portPanel .= "<h1>" . TranslateController::getDefault()->get ( 'shipyard' ) . "</h1>";

$currentShipValue = floor ( shipProperties::sGetValue ( $userProperties->UserID ) / 2 );

$portPanel .= "<h3>" . TranslateController::getDefault()->get ( 'shipValue' ) . ": " . $currentShipValue . "$</h3>";

//Znajdz statki, jakie port sprzedaje
$nameField = "Name" . strtoupper ( $userProperties->Language );

$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'ships' ) . "</h2>";
$portPanel .= "<table class=\"transactionList\" cellspacing=\"2\" cellpadding=\"0\">";

$portPanel .= "<tr>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'Fame' ) . "</th>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
$portPanel .= "<th style=\"width: 75px;\">&nbsp;</th>";
$portPanel .= "</tr>";

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
		$portPanel .= "<tr>";
		$portPanel .= "<td>" . $tR1->Name . "</td>";
		$portPanel .= "<td>" . number_format ( $tR1->Price, 0 ) . "</td>";
		$portPanel .= "<td>" . number_format ( $tR1->Fame, 0 ) . "</td>";
		$portPanel .= "<td>" . $tR1->Size . "</td>";
		$portPanel .= "<td>";

		$portPanel .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','ship::renderDetail','{$userProperties->Language}','{$tR1->ShipID}')", 'Info' );

		if ($userStats->Fame >= $tR1->Fame && $userStats->Cash + $currentShipValue >= $tR1->Price) {
			$portPanel .= \General\Controls::renderImgButton ( 'buy', "executeAction('buyShip','',null,{$tR1->ShipID},null);", TranslateController::getDefault()->get ( 'buy' ) );
		}
		$portPanel .= "</td>";
		$portPanel .= "</tr>";

	}
}
$portPanel .= "</table>";