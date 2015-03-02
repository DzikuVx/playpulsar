<?php
/** @var \Gameplay\Model\UserStatistics $userStats */
$userStats = \Gameplay\PlayerModelProvider::getInstance()->get('UserStatistics');

$sRetVal = "<h1>{T:Bank}</h1>";
$sRetVal .= '<div style="text-align: left; ">';
$sRetVal .= "<div><b>{T:cashOnHand}: </b> " . \General\Formater::formatInt($userStats->Cash) . "</div>";
$sRetVal .= "<div><b>{T:cashInBank}: </b> " .  \General\Formater::formatInt($userStats->Bank) . "</div>";
$sRetVal .= '<hr />';
$sRetVal .= "<h2>{T:Deposit}</h2>";
$sRetVal .= "<p style='font-size: 0.85em; color: gray;'>{T:depositFreeDescription}</p>";
$sRetVal .= "<input onkeyup=\"maskNumber(this.value,this,0," . $userStats->Cash . ")\" onblur=\"maskNumber(this.value,this,0," . $userStats->Cash . ")\" class='input-mini noSpacing' type=\"text\" id=\"bankDepositValue\" value=\"0\" />";
$sRetVal .= \General\Controls::renderImgButton('yes', "bank.deposit();", TranslateController::getDefault()->get ( 'Deposit' ));

$sRetVal .= '<hr />';
$sRetVal .= "<h2>{T:Withdraw}</h2>";
$sRetVal .= "<input onkeyup=\"maskNumber(this.value,this,0," . $userStats->Bank . ")\" onblur=\"maskNumber(this.value,this,0," . $userStats->Bank . ")\" class='input-mini noSpacing' type=\"text\" id=\"bankWithdrawValue\" value=\"0\" />";
$sRetVal .= \General\Controls::renderImgButton('yes', "bank.withdraw();", TranslateController::getDefault()->get ( 'Withdraw' ));

if (!empty($userAlliance->AllianceID)) {
	$sRetVal .= '<hr />';
	$sRetVal .= "<h2>{T:paymentForAlliance}</h2>";
	$sRetVal .= "<input onkeyup=\"maskNumber(this.value,this,0," . $userStats->Cash . ")\" onblur=\"maskNumber(this.value,this,0," . $userStats->Cash . ")\" class='input-mini noSpacing' type=\"text\" id=\"allianceDepositValue\" value=\"0\" />";
	$sRetVal .= \General\Controls::renderImgButton('yes', "alliance.deposit();", TranslateController::getDefault()->get ( 'Deposit' ));
}
$sRetVal .= "</div>";

\Gameplay\Panel\PortAction::getInstance()->add($sRetVal);