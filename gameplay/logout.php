<?php

require_once 'common.php';

if (!empty($_SESSION ['userID'])) {
    $userProperties = new \Gameplay\Model\UserEntity($_SESSION ['userID']);
}else {
	$userProperties = new \Gameplay\Model\UserEntity();
	$userProperties->Language = 'en';
}

TranslateController::setDefaultLanguage($userProperties->Language);

$_SESSION ['userID'] = null;
$_SESSION ['userActivated'] = 'no';
$_SESSION ['userLocked'] = 'yes';
$_SESSION ['userID'] = null;
$_SESSION ['canAct'] = false;

$template=new \General\Templater('templates/loginForms.html');

$template->add('pageTitle',$config ['general'] ['pageTitle']);

$registry = new portalMainNews ( $userProperties->Language );
$template->add('mottoText', $registry->render ());
unset($registry);

/*
* Attach JS and CSS scripts
*/
$template->add('scripts',\General\Minify::getInstance()->getScript('portal_js'));
$template->add('css',\General\Minify::getInstance()->getCss('portal_css'));


$template->add('cdnUrl',$config['general']['cdn']);
$template->add('appId',$config['facebook']['appId']);

$template->add('title',TranslateController::getDefault()->get('Pulsar-Online'));
$template->add('text',TranslateController::getDefault()->get('You have been logged out'));
$template->add('action',\General\Controls::bootstrapButton(TranslateController::getDefault()->get('continue'), "document.location='index.php'",'btn-warning','icon-exclamation-sign'));

echo $template;