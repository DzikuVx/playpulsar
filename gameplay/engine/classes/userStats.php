<?php
/**
 * Statystyki gracza
 *
 * @version $Rev: 456 $
 * @package Engine
 */
class userStats extends baseItem {
	protected $tableName = "userstats";
	protected $tableID = "UserID";
	protected $tableUseFields = array ("Deaths", "Kills", "Npc", "Pirates", "Raids", "Cash", "Bank", "Experience", "Level", "TalentsUsed", "Fame" );
	protected $defaultCacheExpire = 1800;
	protected $useMemcached = true;

	static public function sExamineMe() {
		global $userID, $portPanel;
		shipExamine ( $userID, $userID );
		$portPanel = "&nbsp;";
	}

	/**
	 * Obliczenie levelu usera
	 *
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
	 * Zwiększenie kasy
	 *
	 * @param userStats $userStats
	 * @param int $amount
	 */
	static public function incCash($userStats, $amount) {
		$userStats->Cash += $amount;
	}

	/**
	 * Zmniejszenie kasy
	 *
	 * @param userStats $userStats
	 * @param int $amount
	 */
	static public function decCash($userStats, $amount) {
		$userStats->Cash -= $amount;
		if ($userStats->Cash < 0) {
			$userStats->Cash = 0;
		}
	}

	/**
	 * Zmniejszenie Fame
	 *
	 * @param int $userStats
	 * @param int $amount
	 */
	static public function decFame($userStats, $amount) {
		$userStats->Fame -= $amount;
		if ($userStats->Fame < 0) {
			$userStats->Fame = 0;
		}
	}

	/**
	 * Zwiększenie doświadczenia usera
	 *
	 * @param stdClass $userStats
	 * @param int $amount
	 */
	static function incExperience($userStats, $amount) {
		$tLevel = userStats::computeLevel ( $userStats->Experience + $amount );
		$userStats->Experience += $amount;

		if ($userStats->Level < $tLevel) {
			$userStats->Fame += 1;

			/*
			 * Wyczyść trochę cache
			 */
			if (!empty($userStats->UserID)) {

				$tAlliance = userAlliance::quickLoad($userStats->UserID);

				if (!empty($tAlliance->AllianceID)) {
					\Cache\Controller::getInstance()->clearModule('allianceMembersRegistry::get::'.$tAlliance->AllianceID);
				}
			}

		}

		$userStats->Level = $tLevel;
	}

	/**
	 * Zmniejszenie doświadczenia usera
	 *
	 * @param stdClass $userStats
	 * @param int $amount
	 */
	static function decExperience($userStats, $amount) {
		$userStats->Experience -= $amount;
		$userStats->Level = userStats::computeLevel ( $userStats->Experience );
	}

	/**
	 * Ustawienie doświadczenia usera
	 *
	 * @param stdClass $userStats
	 * @param int $amount
	 */
	static function setExperience($userStats, $amount) {
		$userStats->Experience = $amount;
		$userStats->Level = userStats::computeLevel ( $userStats->Experience );
	}

}
?>