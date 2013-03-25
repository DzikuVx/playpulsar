<?php
$portPanel .= "<h1>" . TranslateController::getDefault()->get ( 'marketplace' ) . "</h1>";

//Znajdz towary, jakie port sprzedaje
$nameField = "Name" . strtoupper ( $userProperties->Language );

$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'buy' ) . "</h2>";
$portPanel .= "<table class='table table-striped table-condensed'>";

$portPanel .= "<tr>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'cargo' ) . "</th>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'instock' ) . "</th>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
$portPanel .= "<th style=\"width: 10em;\">&nbsp;</th>";
$portPanel .= "</tr>";

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

	$portPanel .= "<tr>";
	$portPanel .= "<td>" . $tR1->Name . "</td>";
	$portPanel .= "<td>" . number_format ( $tR1->Amount, 0 ) . "</td>";
	$portPanel .= "<td>" . number_format ( $productPrice, 0 ) . "</td>";
	$portPanel .= "<td><input class='input-mini noSpacing' onkeyup='maskNumber(this.value,this,0,{$buyAmount})' onblur=\"javascript:return maskNumber(this.value,this,0,$buyAmount)\" type='text' id=\"buy_" . $tR1->ID . "\" value='{$buyAmount}' />";
	$portPanel .= \General\Controls::renderImgButton('buy', "executeAction('productBuy',null,null,'{$tR1->ID}',null);", TranslateController::getDefault()->get('buy'));
	$portPanel .= "</td>";
	$portPanel .= "</tr>";

}
$portPanel .= "</table>";

$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'sell' ) . "</h2>";
$portPanel .= "<table class='table table-striped table-condensed'>";

$portPanel .= "<tr>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'cargo' ) . "</th>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'instock' ) . "</th>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
$portPanel .= "<th style=\"width: 10em;\">&nbsp;</th>";
$portPanel .= "</tr>";

$tQuery = $portCargo->getProductsBuy ();
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
	if ($tR1->Amount == null)
	$tR1->Amount = 0;
	if ($tR1->ShipAmount == null)
	$tR1->ShipAmount = 0;
	$portPanel .= "<tr>";
	$portPanel .= "<td>" . $tR1->Name . "</td>";
	$portPanel .= "<td>" . number_format ( $tR1->Amount, 0 ) . "</td>";
	$portPanel .= "<td>" . number_format ( product::computePrice ( $tR1->Amount, $tR1->PriceMin, $tR1->PriceMax ), 0 ) . "</td>";

	$portPanel .= "<td>";
	if ($tR1->ShipAmount == 0) {
		$portPanel .= "&nbsp;";
	} else {
		$portPanel .= "<input class='input-mini noSpacing' onkeyup='maskNumber(this.value,this,0,{$tR1->ShipAmount})' onblur=\"javascript:return maskNumber(this.value,this,0,{$tR1->ShipAmount})\" type=\"text\" size=\"3\" id=\"sell_" . $tR1->ID . "\" value=\"" . $tR1->ShipAmount . "\" />";
		$portPanel .= \General\Controls::renderImgButton('sell', "executeAction('productSell',null,null,'{$tR1->ID}',null);", TranslateController::getDefault()->get('sell'));
	}
	$portPanel .= "</td>";
	$portPanel .= "</tr>";
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
		$portPanel .= "<tr class='yellow'>";
		$portPanel .= "<td>" . $tR1->Name . "</td>";
		$portPanel .= "<td>-</td>";
		$portPanel .= "<td>" . number_format ( $tR1->Price ) . "</td>";

		$portPanel .= "<td>";
		if ($tR1->ShipAmount == 0) {
			$portPanel .= "&nbsp;";
		} else {
			$portPanel .= "<input class='input-mini noSpacing' onkeyup=\"maskNumber(this.value,this,0,{$tR1->ShipAmount})\" onblur=\"maskNumber(this.value,this,0,{$tR1->ShipAmount})\" type=\"text\" id=\"item_sell_" . $tR1->ItemID . "\" value=\"" . $tR1->ShipAmount . "\" />";
			$portPanel .= \General\Controls::renderImgButton('yes', "executeAction('itemSell',null,null,'{$tR1->ItemID}',null);", TranslateController::getDefault()->get('buy'));
		}
		$portPanel .= "</td>";
		$portPanel .= "</tr>";
	}
}
$portPanel .= "</table>";


$portPanel .= "<h1>" . TranslateController::getDefault()->get ( 'Maps' ) . "</h1>";

$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'buy' ) . "</h2>";
$portPanel .= "<table class='table table-striped table-condensed'>";

$portPanel .= "<tr>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'System' ) . "</th>";
$portPanel .= "<th>" . TranslateController::getDefault()->get ( 'price' ) . " [$]</th>";
$portPanel .= "<th style=\"width: 10em;\">&nbsp;</th>";
$portPanel .= "</tr>";

$portCargo = new portCargo ( $userID, $portProperties, $userProperties->Language );

$tQuery = $portCargo->getMapsSell ();
while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

	$portPanel .= "<tr>";
	$portPanel .= "<td>" . $tR1->Name . ' ['.$tR1->Number."]</td>";
	$portPanel .= "<td>" . number_format ( $config ['port'] ['mapPrice'], 0 ) . "</td>";
	$portPanel .= "<td>";

	if ($userStats->Cash >=  $config ['port'] ['mapPrice']) {
		$portPanel .= \General\Controls::renderImgButton('buy', "executeAction('mapBuy',null,null,'{$tR1->SystemID}',null);", 'OK');
	}else {
		$portPanel .= '&nbsp;';
	}
	$portPanel .= "</td>";
	$portPanel .= "</tr>";

}
$portPanel .= "</table>";