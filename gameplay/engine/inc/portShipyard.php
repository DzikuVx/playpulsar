<?php

global $shipProperties, $userID, $userStats;

$userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');

\Gameplay\Model\ShipProperties::sRecomputeValues($shipProperties, $userID);

$sRetVal = "<h1>{T:shipyard}</h1>";

$currentShipValue = floor ( \Gameplay\Model\ShipProperties::sGetValue ( $userProperties->UserID ) / 2 );

$sRetVal .= "<h3>{T:shipValue}: " . $currentShipValue . "$</h3>";

$nameField = "Name" . strtoupper ( $userProperties->Language );

$sRetVal .= "<h2>{T:ships}</h2>";
$sRetVal .= "<table class='table table-striped table-condensed'>";

$sRetVal .= "<tr>";
$sRetVal .= "<th>{T:name}</th>";
$sRetVal .= "<th>{T:price} [$]</th>";
$sRetVal .= "<th>{T:Fame}</th>";
$sRetVal .= "<th>{T:size}</th>";
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