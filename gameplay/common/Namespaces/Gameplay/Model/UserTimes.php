<?php

namespace Gameplay\Model;

class UserTimes extends Standard {
	
	protected $tableName = "usertimes";
	protected $tableID = "UserID";
	protected $tableUseFields = array ("LastLogin", "LastAction", "TurnReset", "FameReset", "LastSalvo" );
	protected $defaultCacheExpire = 3600;

    /**
     * @var int
     */
    public $LastLogin;

    /**
     * @var int
     */
    public $LastAction;

    /**
     * @var int
     */
    public $TurnReset;

    /**
     * @var int
     */
    public $FameReset;

    /**
     * @var int
     */
    public $LastSalvo;

    /**
     * @param UserTimes $object
     * @param $userFastTimes
     */
    static public function genAuthCode(UserTimes $object, $userFastTimes) {
		global $actualTime;

		$userFastTimes->AuthCode = rand ( 1, 999999 );
		$object->LastAction = floor($actualTime/100) * 100;
	}

}