<?php

class userProperties extends baseItem {
	protected $tableName = "users";
	protected $tableID = "UserID";
	protected $tableUseFields = array ("NPCTypeID", "Type", "Login", "AllowSpam", "Password", "Email", "Name", "UserLocked", "UserActivated", "Country", "Language", "About", "FacebookID" );
	protected $defaultCacheExpire = 3600;
	protected $useMemcached = true;

	static public function quickLoad($ID, $useCache = true) {
		$item = new userProperties ( );
		$retVal = $item->load ( $ID, $useCache, $useCache );
		return $retVal;
	}

	static function quickInsert($data) {

		if (!isset($data->FacebookID)) {
			$data->FacebookID = null;
		}

		$item = new userProperties ( );
		$retVal = $item->insert ( $data );
		unset($item);

		return $retVal;
	}

}