<?php

/**
 * Plik nagłowkowy odwołań AJAX
 * @author Paweł Spychalski
 *
 */


require_once '../../common.php';

if (empty( $_SESSION ['userID'])) {
	header('HTTP/1.1 403 Forbidden');
	exit();
}

$userID = $_SESSION ['userID'];

$userPropertiesObject = new userProperties ( );
$userProperties = $userPropertiesObject->load ( $userID, true, true );

TranslateController::setDefaultLanguage($userProperties->Language);
$t = TranslateController::getDefault();