<?php
/**
 * Klasa czasów użytkownika
 *
 * @version $Rev: 409 $
 * @package Engine
 */
class userTimes extends extendedItem {
	
	protected $tableName = "usertimes";
	protected $tableID = "UserID";
	protected $tableUseFields = array ("LastLogin", "LastAction", "TurnReset", "FameReset", "LastSalvo" );
	protected $defaultCacheExpire = 3600;

	public $LastLogin;
	public $LastAction;
	public $TurnReset;
	public $FameReset;
	public $LastSalvo;
	
	/**
	 * Wygenerowanie nowego kodu autoryzacji
	 *
	 * @param stdClass $object
	 * @return stdClass
	 */
	static public function genAuthCode($object, $userFastTimes) {
		global $actualTime;

		$userFastTimes->AuthCode = rand ( 1, 999999 );
		$object->LastAction = floor($actualTime/100) * 100;
	}

}