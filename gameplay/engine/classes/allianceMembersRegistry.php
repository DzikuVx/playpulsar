<?php
/**
 * Klasa rejestru graczy należących do sojuszu
 * @author Paweł
 * @version $Rev: 460 $
 * @package Engine
 * @see alliance
 */
class allianceMembersRegistry extends simpleRegistry {

	protected function renderActionButtons($tR, $tRights, $userID) {

		$tString = \General\Controls::renderImgButton ( 'info',"executeAction('shipExamine','',null,{$tR->UserID});",TranslateController::getDefault()->get ( 'examine' ) );

		if (!empty($tRights['kick']) && $tR->UserID != $userID) {
			$tString .= \General\Controls::renderImgButton ( 'remove', "executeAction('allianceKick',null,null,{$tR->UserID});", TranslateController::getDefault()->get ( 'kick' ) );
		}

		if (!empty($tRights['cash'])) {
			$tString .= \General\Controls::renderImgButton ( 'dollar', "executeAction('allianceCashout',null,null,{$tR->UserID});", TranslateController::getDefault()->get ( 'cashType_out' ) );
		}

		return $tString;
	}

	/**
	 * Wyrenderowanie graczy online
	 *
	 * @param int $allianceID
	 * @return string
	 */
	public function get($allianceID) {

		global $config, $userID, $userAlliance;

		$retVal = '';

		$tRights['kick'] = allianceRights::sCheck($userID, $allianceID, 'kick');
		$tRights['cash'] = allianceRights::sCheck($userID, $allianceID, 'cash');

		$module = 'allianceMembersRegistry::get::'.$allianceID;
		$property = md5($allianceID.'|'.serialize($tRights));

		if(!empty($this->disableCache) || !\Cache\Controller::getInstance()->check($module, $property)) {

			$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'allianceMembers' ) . "</h1>";

			$retVal .= "<table class=\"transactionList\" cellspacing=\"2\" cellpadding=\"0\">";

			$retVal .= "<tr>";
			$retVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
			$retVal .= "<th>" . TranslateController::getDefault()->get ( 'level' ) . "</th>";
			$retVal .= "<th style=\"width: 6em;\">&nbsp;</th>";
			$retVal .= "</tr>";

			$tQuery = "SELECT
					userstats.Level,
					users.Name,
					users.UserID
			FROM
				alliancemembers JOIN users USING(UserID)
				JOIN userstats USING(UserID)
			WHERE
				alliancemembers.AllianceID='{$allianceID}'";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

				$retVal .= '<tr>';
				$retVal .= '<td>' . $tR1->Name . '</td>';
				$retVal .= '<td>' . $tR1->Level . '</td>';

				$retVal .= '<td>' . $this->renderActionButtons($tR1, $tRights, $userID) . '</td>';
				$retVal .= '</tr>';
			}
			$retVal .= "</table>";

			if (empty($this->disableCache)) {
				\Cache\Controller::getInstance()->set($module, $property, $retVal, 7200);
			}
		}else {
			$retVal = \Cache\Controller::getInstance()->get($module, $property);
		}
		return $retVal;
	}

}