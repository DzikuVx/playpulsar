<?php

namespace Gameplay\Model;

//FIXME replace static methods with dynamic, statics are obsolete
class UserStatistics extends Standard {
    protected $tableName = 'userstats';
    protected $tableID = 'UserID';
    protected $tableUseFields = array ('Deaths', 'Kills', 'Npc', 'Pirates', 'Raids', 'Cash', 'Bank', 'Experience', 'Level', 'TalentsUsed', 'Fame');

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
     * @param UserStatistics $userStats
     * @param int $amount
     */
    static public function incCash(UserStatistics $userStats, $amount) {
        $userStats->Cash += $amount;
    }

    /**
     * @param UserStatistics $userStats
     * @param int $amount
     */
    static public function decCash(UserStatistics $userStats, $amount) {
        $userStats->Cash -= $amount;
        if ($userStats->Cash < 0) {
            $userStats->Cash = 0;
        }
    }

    /**
     * @param UserStatistics $userStats
     * @param int $amount
     */
    static public function decFame(UserStatistics $userStats, $amount) {
        $userStats->Fame -= $amount;
        if ($userStats->Fame < 0) {
            $userStats->Fame = 0;
        }
    }

    /**
     * @param UserStatistics $userStats
     * @param int $amount
     */
    static function incExperience(UserStatistics $userStats, $amount) {
        $tLevel = self::computeLevel ( $userStats->Experience + $amount );
        $userStats->Experience += $amount;

        if ($userStats->Level < $tLevel) {
            $userStats->Fame += 1;

            /*
             * Wyczyść trochę cache
             */
            if (!empty($userStats->UserID)) {

                $tAlliance = \userAlliance::quickLoad($userStats->UserID);

                if (!empty($tAlliance->AllianceID)) {
                    \phpCache\Factory::getInstance()->create()->clearModule(new \phpCache\CacheKey('allianceMembersRegistry::get::'.$tAlliance->AllianceID));
                }
            }

        }

        $userStats->Level = $tLevel;
    }

    /**
     * @param UserStatistics $userStats
     * @param int $amount
     */
    static function decExperience(UserStatistics $userStats, $amount) {
        $userStats->Experience -= $amount;
        $userStats->Level = self::computeLevel ( $userStats->Experience );
    }

    /**
     * @param UserStatistics $userStats
     * @param int $amount
     */
    static function setExperience(UserStatistics $userStats, $amount) {
        $userStats->Experience = $amount;
        $userStats->Level = self::computeLevel ( $userStats->Experience );
    }

}