<?php
/**
 * Rejestr top graczy
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class topPlayersRegistry extends simpleRegistry {

	/**
	 * Konstruktor statyczny
	 *
	 */
	static public function sRender($sortOrder = 'Experience') {

		global $userID, $actionPanel, $portPanel;

		$registry = new topPlayersRegistry ( $userID );
		$actionPanel .= $registry->get ( $sortOrder );
		unset($registry);

		sectorShipsPanel::getInstance()->hide ();
		sectorResourcePanel::getInstance()->hide ();
		$portPanel = "&nbsp;";

	}

	/**
	 * Wyrenderowanie top graczy
	 *
	 * @param string $sortOrder
	 * @return string
	 */
	public function get($sortOrder = 'Experience') {

		global $config;

		$retVal = '';

		switch ($sortOrder) {

			case 'Deaths' :
				$orderField = 'userstats.Deaths';
				break;
			case 'Kills' :
				$orderField = 'userstats.Kills';
				break;
			case 'Pirates' :
				$orderField = 'userstats.Pirates';
				break;
			case 'Raids' :
				$orderField = 'userstats.Raids';
				break;
			default :
				$orderField = 'userstats.Experience';
				break;

		}

		$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'topPlayers' ) . "</h1>";

		$retVal .= '<div style="text-align: center;">';

		if ($sortOrder == 'Experience') {
			$tStyle = 'font-weight: bold;';
		} else {
			$tStyle = '';
		}
		$retVal .= \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'byExperience' ), "executeAction('topPlayersShow','Experience', null, null);", $tStyle );

		if ($sortOrder == 'Kills') {
			$tStyle = 'font-weight: bold;';
		} else {
			$tStyle = '';
		}
		$retVal .= \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'byKills' ), "executeAction('topPlayersShow','Kills', null, null);", $tStyle );
		
		if ($sortOrder == 'Deaths') {
			$tStyle = 'font-weight: bold;';
		} else {
			$tStyle = '';
		}
		$retVal .= \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'deaths' ), "executeAction('topPlayersShow','Deaths', null, null);", $tStyle );

		/*if ($sortOrder == 'Pirates') {
			$tStyle = 'font-weight: bold;';
		} else {
			$tStyle = '';
		}
		$retVal .= \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'byPirates' ), "executeAction('topPlayersShow','Pirates', null, null);", $tStyle );

		if ($sortOrder == 'Raids') {
			$tStyle = 'font-weight: bold;';
		} else {
			$tStyle = '';
		}
		$retVal .= \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'byRaids' ), "executeAction('topPlayersShow','Raids', null, null);", $tStyle );
*/
		$retVal .= '</div>';

		$retVal .= "<table class=\"transactionList\" cellspacing=\"2\" cellpadding=\"0\">";

		$retVal .= "<tr>";
		$retVal .= "<th style='width: 2em;'>#</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'level' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'kills' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'deaths' ) . "</th>";
//		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'pirates' ) . "</th>";
//		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'raids' ) . "</th>";
		$retVal .= "<th style=\"width: 4em;\">&nbsp;</th>";
		$retVal .= "</tr>";
		$tIndex = 0;
		$tQuery = "SELECT
		    userstats.Level,
		    userstats.Kills,
		    userstats.Deaths,
		    userstats.Pirates,
		    userstats.Raids,
		    users.Name,
		    users.UserID
		  FROM
		    usertimes JOIN users USING(UserID) JOIN userstats USING(UserID)
		  WHERE
		    users.Type='player' ORDER BY {$orderField} DESC LIMIT " . $config ['user'] ['topPlayers'];
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$tIndex ++;

			$retVal .= '<tr>';
			$retVal .= '<td>' . $tIndex . '</td>';
			$retVal .= '<td>' . $tR1->Name . '</td>';
			$retVal .= '<td>' . $tR1->Level . '</td>';
			$retVal .= '<td>' . $tR1->Kills . '</td>';
			$retVal .= '<td>' . $tR1->Deaths . '</td>';
//			$retVal .= '<td>' . $tR1->Pirates . '</td>';
//			$retVal .= '<td>' . $tR1->Raids . '</td>';

			$tString = \General\Controls::renderImgButton('info', "executeAction('shipExamine','',null,{$tR1->UserID});", TranslateController::getDefault()->get ( 'examine' ));

			$retVal .= '<td>' . $tString . '</td>';

			$retVal .= '</tr>';
		}
		$retVal .= "</table>";

		return $retVal;
	}

}