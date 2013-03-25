<?php

/**
 * Zalogowani gracze
 * @version $Rev: 460 $
 * @package Engine
 *
 */
class onlinePlayersRegistry extends simpleRegistry {

	/**
	 * Wyrenderowanie graczy online
	 *
	 * @return string
	 */
	public function get() {

		global $config;

		$retVal = '';

		$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'onlinePlayers' ) . "</h1>";

		$retVal .= "<table class='table table-striped table-condensed'>";

		$retVal .= "<tr>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'alliance' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'level' ) . "</th>";
		$retVal .= "<th style=\"width: 4em;\">&nbsp;</th>";
		$retVal .= "</tr>";

		$tQuery = "SELECT
				userstats.Level, 
				users.Name, 
				users.UserID,
				alliances.Name AS AllianceName
			FROM 
				usertimes JOIN users USING(UserID) 
				JOIN userstats USING(UserID) 
				LEFT JOIN alliancemembers USING(UserID)
				LEFT JOIN alliances USING(AllianceID)
			WHERE 
				users.Type='player' AND 
				usertimes.LastAction>'" . (time () - $config ['user'] ['onlineThreshold']) . "'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$retVal .= '<tr>';
			$retVal .= '<td>' . $tR1->Name . '</td>';
			$retVal .= '<td>' . $tR1->AllianceName . '</td>';
			$retVal .= '<td>' . $tR1->Level . '</td>';

			$tString = \General\Controls::renderImgButton('info', "executeAction('shipExamine','',null,{$tR1->UserID});", TranslateController::getDefault()->get ( 'examine' ));

			$retVal .= '<td>' . $tString . '</td>';

			$retVal .= '</tr>';
		}
		$retVal .= "</table>";

		return $retVal;
	}

}