<?php
/**
 * Plik inicjalizacyjny
 */
header ( 'Content-Type: text/html; charset=utf-8' );

/*
 * Require external modules
 */
require_once 'common/Namespaces/phpCache/Factory.php';
require_once 'common/Namespaces/SSMQ/SSMQ.php';

require_once 'common/Namespaces/General/Autoloader.php';
\General\Autoloader::register();

session_start ();

mb_internal_encoding ( 'UTF8' );

/**
 * Pobranie trybu wdrożenia
 */
require_once 'deployment.php';
require_once 'db.ini.php';
require_once 'config.inc.php';

include "engine/funkcje.php";

/**
 * Zmienna ostatniego czasu resetu NPC przez użytkownika
 */
if (empty ( $_SESSION ['lastNPCResetTime'] )) {
	$_SESSION ['lastNPCResetTime'] = time () - $config ['timeThresholds'] ['npcReset'];
}

/**
 * Zmienna ostatniego czasu poruszania NPC przez użytkownika
 */
if (empty ( $_SESSION ['lastNPCMoveTime'] )) {
	$_SESSION ['lastNPCMoveTime'] = time () - $config ['timeThresholds'] ['npcMove'];
}

//FIXME default caching method

$colorTable ['green'] = "#00f000";
$colorTable ['yellow'] = "#f0f000";
$colorTable ['red'] = "#f00000";

/**
 * @since 2011-05-06
 */
if (!empty($config ['debug'] ['db'])) {
	\Database\Controller::getInstance()->setStatisticsDb(\Database\Controller::getBackendInstance());
	\Database\Controller::getInstance()->debugMode = true;
}

/*
 * Cache dla silnika szablonow
*/
\General\Templater::$useCache = $deploymentMode;
translation::$useCache = $deploymentMode;
Translate::$useCache = $deploymentMode;