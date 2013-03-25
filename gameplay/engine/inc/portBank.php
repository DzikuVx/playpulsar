<?php
$portPanel .= "<h1>" . TranslateController::getDefault()->get ( 'Bank' ) . "</h1>";
$portPanel .= '<div style="text-align: left; ">';
$portPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'cashOnHand' ) . ": </b> " . \General\Formater::formatInt($userStats->Cash) . "</div>";
$portPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'cashInBank' ) . ": </b> " .  \General\Formater::formatInt($userStats->Bank) . "</div>";
$portPanel .= '<hr />';
$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'Deposit' ) . "</h2>";
$portPanel .= "<p style='font-size: 0.85em; color: gray;'>".TranslateController::getDefault()->get('depostFreeDescription')."</p>";
$portPanel .= "<input onkeyup=\"javascript:return maskNumber(this.value,this,0,{$userStats->Cash})\" onblur=\"javascript:return maskNumber(this.value,this,0,{$userStats->Cash})\" class='ui-corner-all ui-state-default' type=\"text\" size=\"10\" id=\"bankDepositValue\" value=\"0\" />";
$portPanel .= \General\Controls::renderImgButton('yes', "bank.deposit();", TranslateController::getDefault()->get ( 'Deposit' ));

$portPanel .= '<hr />';
$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'Withdraw' ) . "</h2>";
$portPanel .= "<input onkeyup=\"javascript:return maskNumber(this.value,this,0,{$userStats->Bank})\" onblur=\"javascript:return maskNumber(this.value,this,0,{$userStats->Bank})\" class='ui-corner-all ui-state-default' type=\"text\" size=\"10\" id=\"bankWithdrawValue\" value=\"0\" />";
$portPanel .= \General\Controls::renderImgButton('yes', "bank.withdraw();", TranslateController::getDefault()->get ( 'Withdraw' ));

if (!empty($userAlliance->AllianceID)) {
	$portPanel .= '<hr />';
	$portPanel .= "<h2>" . TranslateController::getDefault()->get ( 'paymentForAlliance' ) . "</h2>";
	$portPanel .= "<input onkeyup=\"javascript:return maskNumber(this.value,this,0,{$userStats->Cash})\" onblur=\"javascript:return maskNumber(this.value,this,0,{$userStats->Cash})\" class='ui-corner-all ui-state-default' type=\"text\" size=\"10\" id=\"allianceDepositValue\" value=\"0\" />";
	$portPanel .= \General\Controls::renderImgButton('yes', "alliance.deposit();", TranslateController::getDefault()->get ( 'Deposit' ));
}
$portPanel .= "</div>";