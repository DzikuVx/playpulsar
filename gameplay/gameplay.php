<?php

require_once 'common.php';

if (empty( $_SESSION ['userID'] )) {
	exit ();
}

$userProperties = userProperties::quickLoad($_SESSION ['userID']);

TranslateController::setDefaultLanguage($userProperties->Language);
$t = TranslateController::getDefault();

$template = new \General\Templater ( 'templates/gameplay.html' );

$template->add ( 'backgroundNumber', rand ( 1, $maxBackgroundCount ) );

$template->add('sessionUserID',$_SESSION ['userID']);
$template->add('pageTitle',$config ['general'] ['pageTitle']);
$template->add('cdnUrl',$config['general']['cdn']);

if (empty($config ['debug'] ['gameplayDebugOutput'])) {
	$template->remove('debugPanel');
}

echo $template;