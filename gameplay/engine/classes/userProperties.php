<?php
/**
 * właściwości gracza
 *
 * @version $Rev: 369 $
 * @package Engine
 */
class userProperties extends baseItem {
	protected $tableName = "users";
	protected $tableID = "UserID";
	protected $tableUseFields = array ("NPCTypeID", "Type", "Login", "AllowSpam", "Password", "Email", "Name", "UserLocked", "UserActivated", "Country", "Language", "About", "FacebookID" );
	protected $defaultCacheExpire = 3600;
	protected $useMemcached = true;

	/**
	 * konstruktor statyczny
	 * @param int $ID
	 */
	static public function quickLoad($ID, $useCache = true) {
		$item = new userProperties ( );
		$retVal = $item->load ( $ID, $useCache, $useCache );
		unset($item);
		return $retVal;
	}

	/**
	 * Szybkie wstawienie
	 *
	 * @param stdClass $data
	 * @return int
	 */
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
?>