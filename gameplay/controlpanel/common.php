<?php

header('Content-Type: text/html; charset=utf-8');

ini_set ( 'date.timezone', 'Europe/Warsaw' );
ini_set ( 'date.default_latitude', '31.7667' );
ini_set ( 'date.default_longitude', '35.2333' );
ini_set ( 'date.sunrise_zenith', '90.583333' );
ini_set ( 'date.sunset_zenith', '90.583333' );
date_default_timezone_set ( "Europe/Warsaw" );

if (empty ( $_SESSION ['cpLoggedUserID'] )) {
	$_SESSION ['cpLoggedUserID'] = null;
}
if (empty ( $_SESSION ['cpLoggedUserName'] )) {
	$_SESSION ['cpLoggedUserName'] = '';
}
if (! isset ( $_REQUEST ['class'] )) {
	$_REQUEST ['class'] = '\Controlpanel\Welcome';
}
if (! isset ( $_REQUEST ['method'] )) {
	$_REQUEST ['method'] = 'browse';
}

/** @noinspection PhpIncludeInspection */
require_once '../common/Namespaces/phpCache/Factory.php';
/** @noinspection PhpIncludeInspection */
require_once '../common/Namespaces/SSMQ/SSMQ.php';
/** @noinspection PhpIncludeInspection */
require_once '../common/Namespaces/General/Autoloader.php';
\General\Autoloader::register();

/**
 * Rozpocznij sesję
 */
session_start ();

/** @noinspection PhpIncludeInspection */
require_once '../deployment.php';
/** @noinspection PhpIncludeInspection */
require_once '../db.ini.php';
/** @noinspection PhpIncludeInspection */
require_once '../config.inc.php';
require_once 'config.inc.php';

psDebug::create ();

TranslateController::setDefaultLanguage('en');
$t = TranslateController::getDefault();

$userProperties = new \Gameplay\Model\UserEntity();
$userProperties->Language = 'en';