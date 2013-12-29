<?php

namespace Gameplay\Model;

class UserFastTimes extends Standard {

	protected $tableName = "userfasttimes";
	protected $tableID = "UserID";
	protected $tableUseFields = array ('AuthCode','LastRepair');
	protected $cacheExpire = 2592000;

    /**
     * @var int
     */
    public $AuthCode;

    /**
     * @var int
     */
    public $LastRepair;

	protected function load() {
		if (!$this->fromCache()) {
			$this->AuthCode = 0;
			$this->LastRepair = time();
		}
	}

	public function synchronize() {
		$this->toCache();
	}
}