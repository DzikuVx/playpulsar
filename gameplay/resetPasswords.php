<?php

require_once 'common.php';

$sQuery = "SELECT * FROM users";
$rQuery = \Database\Controller::getInstance()->execute($sQuery);
while ($oResult = \Database\Controller::getInstance()->fetch($rQuery)) {
	
	\Database\Controller::getInstance()->execute("UPDATE users SET Password='".user::sPasswordHash($oResult->Login, 'superpassword')."' WHERE UserID='{$oResult->UserID}'");
	
}