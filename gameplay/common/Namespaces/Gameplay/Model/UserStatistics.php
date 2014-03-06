<?php

namespace Gameplay\Model;

class UserStatistics extends Standard {
    protected $tableName = 'userstats';
    protected $tableID = 'UserID';
    protected $tableUseFields = array ('Deaths', 'Kills', 'Npc', 'Pirates', 'Raids', 'Cash', 'Bank', 'Experience', 'Level', 'TalentsUsed', 'Fame');

    /** @var int */
    public $UserID;

    /**
     * @var int
     */
    public $Deaths;

    /**
     * @var int
     */
    public $Kills;

    /**
     * @var int
     */
    public $Npc;

    /**
     * @var int
     */
    public $Pirates;

    /**
     * @var int
     */
    public $Raids;

    /**
     * @var int
     */
    public $Cash;

    /**
     * @var int
     */
    public $Bank;

    /**
     * @var int
     */
    public $Experience;

    /**
     * @var int
     */
    public $Level;

    /**
     * @var int
     */
    public $TalentsUsed;

    /**
     * @var int
     */
    public $Fame;

    static public function sExamineMe() {
        global $userID;
        shipExamine($userID, $userID);
        \Gameplay\Panel\PortAction::getInstance()->clear();
    }

    /**
     * @param int $exp
     * @return int
     */
    static public function computeLevel($exp) {
        $f_out = floor ( pow ( ($exp / 6000), (1 / 3) ) );
        return $f_out;
    }

    /**
     * Obliczenie najmniejszej ilości EXP aby dany gracz miał wymagany Level
     * @param int $level
     * @return int
     */
    static public function computeExperience($level) {
        $f_out = pow ( $level, 3 ) * 6000;
        return $f_out;
    }

    /**
     * @param int $amount
     */
    public function incCash($amount) {
        $this->Cash += $amount;
    }

    /**
     * @param int $amount
     */
    public function decCash($amount) {
        $this->Cash -= $amount;
        if ($this->Cash < 0) {
            $this->Cash = 0;
        }
    }

    /**
     * @param int $amount
     */
    public function decFame($amount) {
        $this->Fame -= $amount;
        if ($this->Fame < 0) {
            $this->Fame = 0;
        }
    }

    /**
     * @param int $amount
     */
    public function incExperience($amount) {
        $tLevel = self::computeLevel($this->Experience + $amount);
        $this->Experience += $amount;

        if ($this->Level < $tLevel) {
            $this->Fame += 1;

            /*
             * Wyczyść trochę cache
             */
            if (!empty($this->UserID)) {

                $tAlliance = new UserAlliance($this->UserID);

                if (!empty($tAlliance->AllianceID)) {
                    \phpCache\Factory::getInstance()->create()->clearModule(new \phpCache\CacheKey('allianceMembersRegistry::get::'.$tAlliance->AllianceID));
                }
            }

        }

        $this->Level = $tLevel;
    }

    /**
     * @param int $amount
     */
    public function decExperience($amount) {
        $this->Experience -= $amount;
        $this->Level = self::computeLevel($this->Experience);
    }

    /**
     * @param int $amount
     */
    public function setExperience($amount) {
        $this->Experience = $amount;
        $this->Level = self::computeLevel($this->Experience);
    }
}