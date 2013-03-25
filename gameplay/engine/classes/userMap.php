<?php
class userMap extends baseItem {

	static public function sInsert($userID, $systemID) {

		$retVal = false;

		$tQuery = "SELECT COUNT(*) AS ILE FROM usermaps WHERE UserID='{$userID}' AND SystemID='{$systemID}'";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);

		if (\Database\Controller::getInstance()->fetch($tQuery)->ILE == 0) {
			\Database\Controller::getInstance()->execute("INSERT INTO usermaps(UserID, SystemID) VALUES('{$userID}','{$systemID}')");
		}

		return $retVal;
	}

}

