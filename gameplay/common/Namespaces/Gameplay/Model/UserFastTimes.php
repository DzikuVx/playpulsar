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

            $this->originalData = new \stdClass();
            $this->originalData->AuthCode = $this->AuthCode;
            $this->originalData->LastRepair = $this->LastRepair;
		}
	}

	public function synchronize() {
		$this->toCache();
	}
}