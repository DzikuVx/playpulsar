<?php

/**
 * Skrypt przenosi AllianceID z userships do alliancemembers
 */

require_once '../common.php';

$tQuery = "SELECT UserID, AllianceID FROM userships WHERE AllianceID IS NOT NULL";
$tQuery = \Database\Controller::getInstance()->execute($tQuery);
while ($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {

	$tQuery2 = "INSERT INTO alliancemembers(UserID, AllianceID) VALUES('{$tResult->UserID}','{$tResult->AllianceID}')";
	\Database\Controller::getInstance()->execute($tQuery2);
}
