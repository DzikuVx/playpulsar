<?php

class userAlliance extends baseItem {

	protected $tableName = "alliancemembers";
	protected $tableID = "UserID";
	protected $tableUseFields = array ('UserID', 'AllianceID', 'Rank');
	protected $defaultCacheExpire = 3600;
	protected $useMemcached = true;

    /**
     * @param $ID
     * @return stdClass
     * @deprecated
     */
    static public function quickLoad($ID) {
		$item = new userAlliance ( );
		$retVal = $item->load ( $ID, true, true );
		unset($item);
		return $retVal;
	}

	/**
	 * Sprawdzenie, czy gracz jest członkiem sojuszu
	 * @param int $userID
	 * @param int $allianceID
	 * @return boolean
	 * @since 2011-04-04
	 */
	static public function sCheckMembership($userID, $allianceID) {
		$retVal = false;
		
		$tQuery = \Database\Controller::getInstance()->execute("SELECT COUNT(*) AS ILE FROM alliancemembers WHERE UserID='$userID' AND AllianceID='{$allianceID}'");
		if (\Database\Controller::getInstance()->fetch($tQuery)->ILE == 1) {
			$retVal = true;
		}
		
		return $retVal;
	}

	/**
	 * (non-PHPdoc)
	 * @see engine/classes/baseItem::get()
	 */
	function get($ID) {

		$this->dataObject = new stdClass();
		$tResult = \Database\Controller::getInstance()->execute ( "
      SELECT
        alliancemembers.*,
        alliances.*
      FROM
      	alliancemembers JOIN alliances USING(AllianceID)
      WHERE
      	alliancemembers.UserID='$ID'
      LIMIT
        1
      " );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
			$this->dataObject = $resultRow;
		}

		if (empty($this->dataObject->Name)) {
			$this->dataObject->Name = null;
		}

		if (!isset($this->dataObject->AllianceID)) {
			$this->dataObject->AllianceID = null;
		}

		$this->ID = $this->parseCacheID ( $ID );
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see engine/classes/baseItem::formatUpdateQuery()
	 */
	protected function formatUpdateQuery($object, $ID = null) {

		if ($ID == null) {
			$ID = $this->ID;
		}

		if (!empty($object->AllianceID) && !empty($this->dataObject->AllianceID)) {
			$retVal = parent::formatUpdateQuery($object, $ID);
		}elseif (!empty($object->AllianceID) && empty($this->dataObject->AllianceID)) {
			/*
			 * Dołączenie do sojuszu
			 */
			$retVal  = parent::formatInsertQuery($object);
		}else {
			$retVal = "DELETE FROM alliancemembers WHERE UserID='{$ID}'";
		}

		return $retVal;
	}

	//@todo: przypadek, kiedy osoba odchodząca zabiera cały zestaw praw
	//@todo : w przypadku usunięcia AllianceID, usunąć wpis w alliances
}