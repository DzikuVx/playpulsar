<?php

require_once 'common.php';

$language = 'pl';

if ((isset ( $_GET ['lang'] )) and ($_GET ['lang'] == 'en'))
$language = 'en';
if ((isset ( $_POST ['lang'] )) and ($_POST ['lang'] == 'en'))
$language = 'en';

TranslateController::setDefaultLanguage($language);

$_SESSION ['userActivated'] = 'no';
$_SESSION ['userLocked'] = 'yes';
$_SESSION ['userID'] = null;
$_SESSION ['userName'] = '';
$_SESSION ['language'] = $language;
$_SESSION ['canAct'] = true;

$template=new \General\Templater('templates/loginForms.html');

$template->add('pageTitle',$config ['general'] ['pageTitle']);

$registry = new portalMainNews ( $language );
$template->add('mottoText', $registry->render ());
unset($registry);

$template->add('cdnUrl',$config['general']['cdn']);
$template->add('appId',$config['facebook']['appId']);

if (isset($_REQUEST['fbCreate']) && $_REQUEST['fbCreate'] == 'true') {

	$fbMe = user::sFbConnect();

	$rV = user::sCreateAccountFromFb(\Database\Controller::getInstance()->quote($_REQUEST['login']), $fbMe);

	if ($rV != 'OK') {
		psDebug::halt($rV, null, array('dispay'=>false, 'send'=>false));
	}

	$_REQUEST['useFacebookID'] = true;
}

if (isset($_REQUEST['useFacebookID']) && $_REQUEST['useFacebookID'] == 'true') {

	/*
	 * Logowanie przez konto facebooka
	 */
	$fbMe = user::sFbConnect();

	/*
	 * Sprawdź, czy istnieje użytkownik o podanym FaceBook ID
	 */
	$tQuery = "SELECT
      users.UserID AS UserID,
      users.Name AS Name,
      users.Login AS Login,
      users.UserActivated AS UserActivated,
      users.UserLocked AS UserLocked,
      users.Language AS Language
    FROM users WHERE
      users.FacebookID='{$fbMe['id']}'";
	$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$_SESSION ['language'] = $tR1->Language;
		$_SESSION ['userID'] = $tR1->UserID;
		$_SESSION ['userActivated'] = $tR1->UserActivated;
		$_SESSION ['userLocked'] = $tR1->UserLocked;
		$_SESSION ['userName'] = $tR1->Name;
	}

	if ($_SESSION ['userID'] == null) {
		$_SESSION ['canAct'] = false;

		user::sRenderFbForm($template, $fbMe);

	} else {

		if ($_SESSION ['userLocked'] == 'yes') {
			$_SESSION ['canAct'] = false;
			$template->add('title',TranslateController::getDefault()->get('accountLocked'));
			$template->add('text',TranslateController::getDefault()->get('toContinueLocked'));
			$template->add('action',\General\Controls::bootstrapButton(TranslateController::getDefault()->get('continue'), "document.location='index.php'",'btn-warning','icon-exclamation-sign'));
		}
	}

	if ($_SESSION ['canAct'] && (! isset ( $_GET ['action'] ) || $_GET ['action'] == 'null')) {
		$template->add('title',TranslateController::getDefault()->get('loginSuccess'));
		$template->add('text',TranslateController::getDefault()->get('toContinue'));
		$template->add('action',\General\Controls::bootstrapButton(TranslateController::getDefault()->get('continue'), "document.location='gameplay.php'",'btn-success','icon-ok'));
	}

}elseif (isset ( $_POST ['login'] )) {

	/*
	 * Logowanie przez login i hasło
	 */

	$_SESSION ['userID'] = null;
	$_SESSION ['userActivated'] = 'no';
	$_SESSION ['userLocked'] = 'yes';
	$_SESSION ['userID'] = null;
	$_SESSION ['language'] = $language;
	$_SESSION ['canAct'] = true;

	$tPassword = user::sPasswordHash(\Database\Controller::getInstance()->quote($_POST['login']), \Database\Controller::getInstance()->quote($_REQUEST['password']));

	//sprawdz, czy istnieje takie konto
	$tQuery = "SELECT
      users.UserID AS UserID,
      users.Name AS Name,
      users.Login AS Login,
      users.UserActivated AS UserActivated,
      users.UserLocked AS UserLocked,
      users.Language AS Language
    FROM users WHERE
      users.Type = 'player' AND
      users.Login = '".\Database\Controller::getInstance()->quote($_POST['login'])."' AND
      users.Password = '".$tPassword."'";
	$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
	while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
		$_SESSION ['language'] = $tR1->Language;
		$_SESSION ['userID'] = $tR1->UserID;
		$_SESSION ['userActivated'] = $tR1->UserActivated;
		$_SESSION ['userLocked'] = $tR1->UserLocked;
		$_SESSION ['userName'] = $tR1->Name;
	}

	if ($_SESSION ['userID'] == null) {
		$_SESSION ['canAct'] = false;

		$template->add('title',TranslateController::getDefault()->get('loginFailed'));
		$template->add('text',TranslateController::getDefault()->get('toContinueFailed'));
		$template->add('action',\General\Controls::bootstrapButton(TranslateController::getDefault()->get('continue'), "document.location='index.php'",'btn-danger','icon-exclamation-sign'));

	} else {
		if ($_SESSION ['userActivated'] == 'no') {
			$_SESSION ['canAct'] = false;

			$template->add('title',TranslateController::getDefault()->get('accountNotActivated'));
			$template->add('text',TranslateController::getDefault()->get('toContinueNotActivated'));
			$template->add('action',\General\Controls::bootstrapButton(TranslateController::getDefault()->get('continue'), "document.location='index.php'",'btn-warning','icon-exclamation-sign'));

		}

		if ($_SESSION ['userLocked'] == 'yes') {
			$_SESSION ['canAct'] = false;

			$template->add('title',TranslateController::getDefault()->get('accountLocked'));
			$template->add('text',TranslateController::getDefault()->get('toContinueLocked'));
			$template->add('action',\General\Controls::bootstrapButton(TranslateController::getDefault()->get('continue'), "document.location='index.php'",'btn-warning','icon-exclamation-sign'));

		}
	}

	if ($_SESSION ['canAct'] && (! isset ( $_GET ['action'] ) || $_GET ['action'] == 'null')) {
		$template->add('title',TranslateController::getDefault()->get('loginSuccess'));
		$template->add('text',TranslateController::getDefault()->get('toContinue'));
		$template->add('action',\General\Controls::bootstrapButton(TranslateController::getDefault()->get('continue'), "document.location='gameplay.php'",'btn-success','icon-ok'));
	}


}

echo $template;