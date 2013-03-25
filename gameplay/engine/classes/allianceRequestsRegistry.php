<?php
/**
 *
 * Lista podań do sojuszu
 * @author Paweł
 * @since 2010-07-27
 * @see alliance
 * @see allianceRequest
 *
 */
class allianceRequestsRegistry extends simpleRegistry {

	/**
	 * Wyrenderowanie listy
	 * @param int $allianceID
	 * @return string
	 * @since 2010-07-27
	 * @throws \Database\Exception
	 */
	public function get($allianceID) {

		global $config, $userID, $userAlliance;

		if (\Database\Controller::getInstance()->getHandle() === false) {
			throw new \Database\Exception('Connection lost');
		}

		$retVal = '';

		$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'allianceAppliances' ) . "</h1>";

		$retVal .= "<table class=\"transactionList\" cellspacing=\"2\" cellpadding=\"0\">";

		$retVal .= "<tr>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'level' ) . "</th>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'text' ) . "</th>";
		$retVal .= "<th style=\"width: 6em;\">&nbsp;</th>";
		$retVal .= "</tr>";

		$tQuery = "SELECT
					userstats.Level,
					users.Name,
					users.UserID,
					alliancerequests.Text
			FROM
				alliancerequests JOIN users ON users.UserID=alliancerequests.UserID JOIN
				userstats ON userstats.UserID=alliancerequests.UserID
			WHERE
				alliancerequests.AllianceID='{$allianceID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$retVal .= '<tr>';
			$retVal .= '<td>' . $tR1->Name . '</td>';
			$retVal .= '<td>' . $tR1->Level . '</td>';
			$retVal .= '<td>' . $tR1->Text . '</td>';

			$tString = \General\Controls::renderImgButton ( 'info', "executeAction('shipExamine','',null,'{$tR1->UserID}');", TranslateController::getDefault()->get ( 'examine' ) );
			$tString .= \General\Controls::renderImgButton ('add' , "executeAction('allianceAccept','',null,'{$tR1->UserID}');", TranslateController::getDefault()->get ( 'accept' ) );
			$tString .= \General\Controls::renderImgButton ( 'delete', "executeAction('allianceDecline','',null,'{$tR1->UserID}');", TranslateController::getDefault()->get ( 'decline' ) );

			$retVal .= '<td>' . $tString . '</td>';

			$retVal .= '</tr>';
		}
		$retVal .= "</table>";
		$retVal .= "<div style=\"text-align: center;\">" . \General\Controls::sStandardButton( TranslateController::getDefault()->get('close'), "executeAction('allianceDetail',null,null,'{$allianceID}');") . "</div>";
		return $retVal;
	}

}