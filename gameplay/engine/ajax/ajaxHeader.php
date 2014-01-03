<?php

/**
 * Plik nagłowkowy odwołań AJAX
 * @author Paweł Spychalski
 *
 */


/** @noinspection PhpIncludeInspection */
require_once '../../common.php';

if (empty( $_SESSION ['userID'])) {
	header('HTTP/1.1 403 Forbidden');
	exit();
}

$userID = $_SESSION ['userID'];

$userProperties = new \Gameplay\Model\UserEntity($userID);

TranslateController::setDefaultLanguage($userProperties->Language);
$t = TranslateController::getDefault();