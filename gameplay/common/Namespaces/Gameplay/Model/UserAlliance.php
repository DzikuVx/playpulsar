<?php

namespace Gameplay\Model;

class UserAlliance extends CustomGet {
    protected $tableName = "alliancemembers";
    protected $tableID = "UserID";
    protected $tableUseFields = array ('UserID', 'AllianceID', 'Rank');
    protected $cacheExpire = 3600;

    /** @var int */
    public $AllianceID;

    /** @var string */
    public $Name;

    /** @var string */
    public $Rank;

    /** @var int */
    public $UserID;

    public $NPCAlliance;

    /**
     * @var string
     */
    public $Symbol;

    /**
     * @var string
     */
    public $Motto;

    /**
     * @var string
     */
    public $Description;

    /**
     * @var int
     */
    public $Cash;

    /**
     * @var string
     */
    public $Defendable;

    protected function formatUpdateQuery() {

        if (!empty($this->AllianceID) && !empty($this->originalData->AllianceID)) {
            $retVal = parent::formatUpdateQuery();
        } elseif (!empty($this->AllianceID) && empty($this->originalData->AllianceID)) {
            /*
             * Case: join alliance
             */
            $retVal  = parent::formatInsertQuery();
        } else {
            $retVal = "DELETE FROM alliancemembers WHERE UserID='{$this->entryId}'";
        }

        return $retVal;
    }

    /**
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

    protected function get() {

        $oDb = \Database\Controller::getInstance();

        $oData = new \stdClass();

        $tResult = $oDb->execute ( "
            SELECT
                alliancemembers.*,
                alliances.*
            FROM
                alliancemembers JOIN alliances USING(AllianceID)
            WHERE
                alliancemembers.UserID='{$this->dbID}'
            LIMIT 1");
        while($resultRow = $oDb->fetch($tResult)) {
            foreach($resultRow as $sKey => $sValue) {
                $oData->{$sKey} = $sValue;
            }
        }

        $this->loadData($oData, false);

        return true;
    }

    //TODO przypadek, kiedy osoba odchodząca zabiera cały zestaw praw
    //TODO w przypadku usunięcia AllianceID, usunąć wpis w alliances

}