<?php

namespace Controlpanel;

use \Database\Controller as Database;

class Statistics extends BaseItem{

	private function getPlayerCount() {

		$tQuery = Database::getInstance()->execute("SELECT COUNT(*) AS ile FROM users WHERE Type='player'");
		$retVal = Database::getInstance()->fetch($tQuery)->ile;

		if (empty($retVal)) {
			$retVal = 0;
		}

		return $retVal;
	}

	private function getActivePlayerCount() {

		$tQuery = Database::getInstance()->execute("SELECT COUNT(*) AS ile FROM users u JOIN userstats us USING(UserID) WHERE u.Type='player' AND us.Experience>0");
		$retVal = Database::getInstance()->fetch($tQuery)->ile;

		if (empty($retVal)) {
			$retVal = 0;
		}

		return $retVal;
	}

	private function getPlayerWithKillCount() {

		$tQuery = Database::getInstance()->execute("SELECT COUNT(*) AS ile FROM users u JOIN userstats us USING(UserID) WHERE u.Type='player' AND us.Kills>0");
		$retVal = Database::getInstance()->fetch($tQuery)->ile;

		if (empty($retVal)) {
			$retVal = 0;
		}

		return $retVal;
	}

	private function getRookieProtectedCount() {

		$tQuery = Database::getInstance()->execute("SELECT COUNT(*) AS ile FROM users u JOIN userships us USING(UserID) WHERE u.Type='player' AND us.RookieTurns>0");
		$retVal = Database::getInstance()->fetch($tQuery)->ile;

		if (empty($retVal)) {
			$retVal = 0;
		}

		return $retVal;
	}

	private function getActivePlayersCountInTime($days) {

		$tTime = time() - ($days * 86400);
		$tQuery = Database::getInstance()->execute("SELECT COUNT(*) AS ile FROM users u JOIN usertimes ut USING(UserID) WHERE u.Type='player' AND ut.LastAction>'{$tTime}'");

		$retVal = Database::getInstance()->fetch($tQuery)->ile;

		if (empty($retVal)) {
			$retVal = 0;
		}

		return $retVal;
	}

	/**
	 * render statistics table
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function detail($user, $params) {

		$retVal = $this->renderTitle ( "Statistics" );

		$retVal .= "<table class='table table-striped table-bordered table-condensed'>";

		$retVal .= "<thead>";
		$retVal .= "<tr>";
		$retVal .= "<th>Type</th>";
		$retVal .= "<th>#</th>";
		$retVal .= "<th>Type</th>";
		$retVal .= "<th>#</th>";
		$retVal .= "</tr>";
		$retVal .= "</thead>";
		$retVal .= "<tbody>";

		$retVal .= "<tr>";
		$retVal .= "<td>Registered players</td>";
		$retVal .= "<td>".$this->getPlayerCount()."</td>";
		$retVal .= "<td>Active players (Exp > 0)</td>";
		$retVal .= "<td>".$this->getActivePlayerCount()."</td>";
		$retVal .= "</tr>";
		$retVal .= "<tr>";
		$retVal .= "<td>Active players in last 2days</td>";
		$retVal .= "<td>".$this->getActivePlayersCountInTime(2)."</td>";
		$retVal .= "<td>Active players in last 7days</td>";
		$retVal .= "<td>".$this->getActivePlayersCountInTime(7)."</td>";
		$retVal .= "</tr>";
		$retVal .= "<tr>";
		$retVal .= "<td>Active players in last 14days</td>";
		$retVal .= "<td>".$this->getActivePlayersCountInTime(14)."</td>";
		$retVal .= "<td>Active players in last 30days</td>";
		$retVal .= "<td>".$this->getActivePlayersCountInTime(30)."</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>Rookie protected</td>";
		$retVal .= "<td>".$this->getRookieProtectedCount()."</td>";
		$retVal .= "<td>Players with kills</td>";
		$retVal .= "<td>".$this->getPlayerWithKillCount()."</td>";
		$retVal .= "</tr>";

		$retVal .= "</tbody>";
		$retVal .= "</table>";

		return $retVal;
	}
}