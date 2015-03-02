<?php
use Gameplay\Model\UserAlliance;
use Gameplay\PlayerModelProvider;

class alliancePostsRegistry extends simpleRegistry {

	/**
	 * Wyrenderowanie graczy online
	 *
	 * @param int $allianceID
	 * @return string
	 */
	public function get($allianceID) {

		global $userID;

        /** @var UserAlliance $userAlliance */
        $userAlliance = PlayerModelProvider::getInstance()->get('UserAlliance');

		$tRights['post'] = allianceRights::sCheck($userID, $userAlliance->AllianceID, 'post');

		$oCacheKey = new \phpCache\CacheKey('alliancePostsRegistry::get', md5($allianceID.'|'.serialize($tRights['post'])));
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (!$oCache->check($oCacheKey)) {

			$retVal = '';

			$retVal .= "<h1>{T:allianceWall}</h1>";

			$tQuery = "SELECT
					allianceposts.Text,
					allianceposts.Date,
					allianceposts.PostID,
					users.Name,
					users.UserID
			FROM
				allianceposts JOIN users USING(UserID)
			WHERE
				allianceposts.AllianceID='{$allianceID}'
			ORDER BY
				allianceposts.Date DESC
			LIMIT 2";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

				$retVal .= '<div class="panel ui-shadow-all">';
					
				if ($tRights['post']) {
					$retVal .= '<div style="float: right;">'.\General\Controls::renderImgButton('delete', "Playpulsar.gameplay.execute('alliancePostDelete','',null,{$tR1->PostID});", TranslateController::getDefault()->get('delete')).'</div>';
				}
				$retVal .= '<p><strong>' . $tR1->Name . '</strong> <span style="font-size: 0.8em;">'.\General\Formater::formatDateTime($tR1->Date).'</span></p>';
				$retVal .= '<p>' . $tR1->Text . '</p>';
				$retVal .= '</div>';
			}

			$oCache->set($oCacheKey, $retVal, 7200);

		}else {
			$retVal = $oCache->get($oCacheKey);
		}

		return $retVal;
	}
}