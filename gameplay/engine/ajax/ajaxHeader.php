<?php

/**
 * Plik nagłowkowy odwołań AJAX
 * @author Paweł Spychalski
 *
 */
use Gameplay\Model\UserEntity;


/** @noinspection PhpIncludeInspection */
require_once '../../common.php';

if (empty( $_SESSION ['userID'])) {
	header('HTTP/1.1 403 Forbidden');
	exit();
}

$userID = $_SESSION ['userID'];

/** @var UserEntity */
$userProperties = \Gameplay\PlayerModelProvider::getInstance()->register('UserEntity', new \Gameplay\Model\UserEntity($userID));

TranslateController::setDefaultLanguage($userProperties->Language);
$t = TranslateController::getDefault();