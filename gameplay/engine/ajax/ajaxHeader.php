<?php

/**
 * Plik nagłowkowy odwołań AJAX
 * @author Paweł Spychalski
 * 
 */

require_once '../../common.php';

$xml = $HTTP_RAW_POST_DATA;

$userID = xml::sGetValue ( $xml, "<userID>", "</userID>" );
if (! isset ( $_SESSION ['userID'] ) || $userID != $_SESSION ['userID'])
	exit ();

$userPropertiesObject = new userProperties ( );
$userProperties = $userPropertiesObject->load ( $userID, true, true );

TranslateController::setDefaultLanguage($userProperties->Language);
$t = TranslateController::getDefault();