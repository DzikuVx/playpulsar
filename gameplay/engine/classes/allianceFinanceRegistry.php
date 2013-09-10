<?php
class allianceFinanceRegistry extends simpleRegistry {

	static public function sRender() {

		global $userID, $portPanel, $userAlliance;

		/*
		 * Wyrenderowanie sojuszu
		 */
		$registry = new allianceFinanceRegistry ($userID);

		\Gameplay\Panel\Action::getInstance()->add($registry->get ($userAlliance->AllianceID));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		$portPanel = "&nbsp;";

	}

	public function get($allianceID) {

		global $config, $userID, $userAlliance;

		if (\Database\Controller::getInstance()->getHandle() === false) {
			throw new \Database\Exception('Connection lost');
		}

		if (!allianceRights::sCheck($this->userID, $allianceID, 'cash')) {
			throw new securityException();
		}

		$retVal = '';

		$tAlliance = alliance::quickLoad($userAlliance->AllianceID);

		$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'financeOperations' ) . "</h1>";
		$retVal .= "<h2>Saldo: " . \General\Formater::formatInt($tAlliance->Cash) . "</h2>";

		$retVal .= "<table class='table table-striped table-condensed'>";

		$retVal .= "<tr>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'date' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'Value' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'Player' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'Type' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'Other' ) . "</th>";
		$retVal .= "</tr>";

		$tQuery = "SELECT
					users.Name,
					forUser.Name AS ForUserName,
					alliancefinance.*
			FROM
				 alliancefinance JOIN users ON users.UserID=alliancefinance.UserID
				 LEFT JOIN users AS forUser ON forUser.UserID=alliancefinance.ForUserID
			WHERE
				alliancefinance.AllianceID='{$allianceID}'
			ORDER BY
				Date DESC";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$retVal .= '<tr>';
			$retVal .= '<td>' . \General\Formater::formatDateTime($tR1->Date) . '</td>';
			$retVal .= '<td>' . \General\Formater::formatInt($tR1->Value) . '</td>';
			$retVal .= '<td>' . $tR1->Name . '</td>';
			$retVal .= '<td>' . TranslateController::getDefault()->get('cashType_'.$tR1->Type) . '</td>';
			$retVal .= '<td>' . $tR1->ForUserName . '</td>';
			$retVal .= '</tr>';
		}
		$retVal .= "</table>";
		$retVal .= "<div style='text-align: center;'>" . \General\Controls::bootstrapButton( '{T:close}', "Playpulsar.gameplay.execute('allianceDetail',null,null,'{$allianceID}');") . "</div>";
		return $retVal;
	}

}