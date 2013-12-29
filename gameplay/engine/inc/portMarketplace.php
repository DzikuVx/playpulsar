<?php

global $userProperties, $shipProperties, $userStats, $config, $userID;

$sRetVal = "<h1>{T:marketplace}</h1>";

$nameField = "Name" . strtoupper ( $userProperties->Language );

$sRetVal .= "<h2>{T:buy}</h2>";
$sRetVal .= "<table class='table table-striped table-condensed'>";

$sRetVal .= "<tr>";
$sRetVal .= "<th>{T:cargo}</th>";
$sRetVal .= "<th>{T:instock}</th>";
$sRetVal .= "<th>{T:price} [$]</th>";
$sRetVal .= "<th style=\"width: 10em;\">&nbsp;</th>";
$sRetVal .= "</tr>";

$portCargo = new portCargo ( $userID, $portProperties, $userProperties->Language );

$tQuery = $portCargo->getProductsSell ();
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

	if ($tR1->Amount == null)
	$tR1->Amount = 0;
	$productPrice = product::computePrice ( $tR1->Amount, $tR1->PriceMin, $tR1->PriceMax );
	$buyAmount = floor ( ($shipProperties->CargoMax - $shipProperties->Cargo) / $tR1->Size );
	if ($buyAmount > $tR1->Amount)
	$buyAmount = $tR1->Amount;

	if ($productPrice != 0) {
		$cashAmount = floor ( $userStats->Cash / $productPrice );
	} else {
		$cashAmount = $buyAmount;
	}
	if ($buyAmount > $cashAmount)
	$buyAmount = $cashAmount;

	if ($buyAmount < 0)
	$buyAmount = 0;

	$sRetVal .= "<tr>";
	$sRetVal .= "<td>" . $tR1->Name . "</td>";
	$sRetVal .= "<td>" . number_format ( $tR1->Amount, 0 ) . "</td>";
	$sRetVal .= "<td>" . number_format ( $productPrice, 0 ) . "</td>";
	$sRetVal .= "<td><input class='input-mini noSpacing' onkeyup='maskNumber(this.value,this,0,{$buyAmount})' onblur=\"javascript:return maskNumber(this.value,this,0,$buyAmount)\" type='text' id=\"buy_" . $tR1->ID . "\" value='{$buyAmount}' />";
	$sRetVal .= \General\Controls::renderImgButton('buy', "Playpulsar.gameplay.execute('productBuy',null,null,'{$tR1->ID}',null);", TranslateController::getDefault()->get('buy'));
	$sRetVal .= "</td>";
	$sRetVal .= "</tr>";

}
$sRetVal .= "</table>";

$sRetVal .= "<h2>" . TranslateController::getDefault()->get ( 'sell' ) . "</h2>";
$sRetVal .= "<table class='table table-striped table-condensed'>";

$sRetVal .= "<tr>";
$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'cargo' ) . "</th>";
$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'instock' ) . "</th>";
$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
$sRetVal .= "<th style=\"width: 10em;\">&nbsp;</th>";
$sRetVal .= "</tr>";

$tQuery = $portCargo->getProductsBuy ();
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
	if ($tR1->Amount == null)
	$tR1->Amount = 0;
	if ($tR1->ShipAmount == null)
	$tR1->ShipAmount = 0;
	$sRetVal .= "<tr>";
	$sRetVal .= "<td>" . $tR1->Name . "</td>";
	$sRetVal .= "<td>" . number_format ( $tR1->Amount, 0 ) . "</td>";
	$sRetVal .= "<td>" . number_format ( product::computePrice ( $tR1->Amount, $tR1->PriceMin, $tR1->PriceMax ), 0 ) . "</td>";

	$sRetVal .= "<td>";
	if ($tR1->ShipAmount == 0) {
		$sRetVal .= "&nbsp;";
	} else {
		$sRetVal .= "<input class='input-mini noSpacing' onkeyup='maskNumber(this.value,this,0,{$tR1->ShipAmount})' onblur=\"javascript:return maskNumber(this.value,this,0,{$tR1->ShipAmount})\" type=\"text\" size=\"3\" id=\"sell_" . $tR1->ID . "\" value=\"" . $tR1->ShipAmount . "\" />";
		$sRetVal .= \General\Controls::renderImgButton('sell', "Playpulsar.gameplay.execute('productSell',null,null,'{$tR1->ID}',null);", TranslateController::getDefault()->get('sell'));
	}
	$sRetVal .= "</td>";
	$sRetVal .= "</tr>";
}

//Sprzedaż itemów
if ($portProperties->Items != '') {
	$tQuery = "SELECT
      itemtypes.ItemID AS ItemID,
      itemtypes.$nameField AS Name,
      itemtypes.Price AS Price,
      itemtypes.Experience AS Experience,
      shipcargo.Amount AS ShipAmount
    FROM
      itemtypes LEFT JOIN shipcargo ON shipcargo.CargoID=itemtypes.ItemID AND shipcargo.Type='item' AND shipcargo.UserID='$userID'
    WHERE
      itemtypes.ItemID IN ({$portProperties->Items}) AND
      shipcargo.Amount > '0' AND shipcargo.Amount IS NOT NULL AND
      itemtypes.Active = 'yes'
    ORDER BY
      itemtypes.$nameField
    ";
	$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		if ($tR1->ShipAmount == null)
		$tR1->ShipAmount = 0;
		$sRetVal .= "<tr class='yellow'>";
		$sRetVal .= "<td>" . $tR1->Name . "</td>";
		$sRetVal .= "<td>-</td>";
		$sRetVal .= "<td>" . number_format ( $tR1->Price ) . "</td>";

		$sRetVal .= "<td>";
		if ($tR1->ShipAmount == 0) {
			$sRetVal .= "&nbsp;";
		} else {
			$sRetVal .= "<input class='input-mini noSpacing' onkeyup=\"maskNumber(this.value,this,0,{$tR1->ShipAmount})\" onblur=\"maskNumber(this.value,this,0,{$tR1->ShipAmount})\" type=\"text\" id=\"item_sell_" . $tR1->ItemID . "\" value=\"" . $tR1->ShipAmount . "\" />";
			$sRetVal .= \General\Controls::renderImgButton('yes', "Playpulsar.gameplay.execute('itemSell',null,null,'{$tR1->ItemID}',null);", TranslateController::getDefault()->get('buy'));
		}
		$sRetVal .= "</td>";
		$sRetVal .= "</tr>";
	}
}
$sRetVal .= "</table>";


$sRetVal .= "<h1>" . TranslateController::getDefault()->get ( 'Maps' ) . "</h1>";

$sRetVal .= "<h2>" . TranslateController::getDefault()->get ( 'buy' ) . "</h2>";
$sRetVal .= "<table class='table table-striped table-condensed'>";

$sRetVal .= "<tr>";
$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'System' ) . "</th>";
$sRetVal .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
$sRetVal .= "<th style=\"width: 10em;\">&nbsp;</th>";
$sRetVal .= "</tr>";

$portCargo = new portCargo ( $userID, $portProperties, $userProperties->Language );

$tQuery = $portCargo->getMapsSell ();
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

	$sRetVal .= "<tr>";
	$sRetVal .= "<td>" . $tR1->Name . ' ['.$tR1->Number."]</td>";
	$sRetVal .= "<td>" . number_format ( $config ['port'] ['mapPrice'], 0 ) . "</td>";
	$sRetVal .= "<td>";

	if ($userStats->Cash >=  $config ['port'] ['mapPrice']) {
		$sRetVal .= \General\Controls::renderImgButton('buy', "Playpulsar.gameplay.execute('mapBuy',null,null,'{$tR1->SystemID}',null);", 'OK');
	}else {
		$sRetVal .= '&nbsp;';
	}
	$sRetVal .= "</td>";
	$sRetVal .= "</tr>";

}
$sRetVal .= "</table>";

\Gameplay\Panel\PortAction::getInstance()->add($sRetVal);