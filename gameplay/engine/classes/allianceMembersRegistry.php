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

		$tString = \General\Controls::renderImgButton ( 'info',"Playpulsar.gameplay.execute('shipExamine','',null,{$tR->UserID});",TranslateController::getDefault()->get ( 'examine' ) );

		if (!empty($tRights['kick']) && $tR->UserID != $userID) {
			$tString .= \General\Controls::renderImgButton ( 'remove', "Playpulsar.gameplay.execute('allianceKick',null,null,{$tR->UserID});", TranslateController::getDefault()->get ( 'kick' ) );
		}

		if (!empty($tRights['cash'])) {
			$tString .= \General\Controls::renderImgButton ( 'dollar', "Playpulsar.gameplay.execute('allianceCashout',null,null,{$tR->UserID});", TranslateController::getDefault()->get ( 'cashType_out' ) );
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

		$oCacheKey = new \phpCache\CacheKey('allianceMembersRegistry::get::'.$allianceID, md5($allianceID.'|'.serialize($tRights)));
        $oCache    = \phpCache\Factory::getInstance()->create();

		if(!empty($this->disableCache) || !$oCache->check($oCacheKey)) {

			$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'allianceMembers' ) . "</h1>";

			$retVal .= "<table class='table table-striped table-condensed'>";

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
				$oCache->set($oCacheKey, $retVal, 7200);
			}
		} else {
			$retVal = $oCache->get($oCacheKey);
		}
		return $retVal;
	}

}