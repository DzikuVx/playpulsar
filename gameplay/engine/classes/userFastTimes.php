<?php
class userFastTimes extends extendedItem {


	protected $tableName = "userfasttimes";
	protected $tableID = "UserID";
	protected $tableUseFields = array ('AuthCode','LastRepair');
	protected $cacheExpire = 2592000;

	public $AuthCode;
	public $LastRepair;

	/**
	 * (non-PHPdoc)
	 * @see baseItem::load()
	 */
	protected function load() {

		if (!$this->fromCache()) {
			$this->AuthCode = 0;
			$this->LastRepair = time();
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see baseItem::synchronize()
	 */
	public function synchronize() {
		$this->toCache();
	}

}