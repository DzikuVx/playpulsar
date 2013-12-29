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
     * @param UserFastTimes $userFastTimes
     */
    static public function genAuthCode(UserTimes $object, UserFastTimes $userFastTimes) {
		$userFastTimes->AuthCode = rand ( 1, 999999 );
		$object->LastAction = floor(time() / 100) * 100;
	}

}