<?php
use Gameplay\Model\UserAlliance;
use Gameplay\PlayerModelProvider;

/**
 * Lista sojuszy
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class allianceRegistry extends simpleRegistry {

	/**
	 * Konstruktor statyczny
	 *
	 */
	static public function sRender() {

		global $userID;

        /** @var UserAlliance $userAlliance */
        $userAlliance = PlayerModelProvider::getInstance()->get('UserAlliance');

		/*
		 * Lista operacji na liście sojuszy
		 */
		$tOperations = '';
		if (empty($userAlliance->AllianceID)) {
			$tOperations .=	 \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'allianceCraete' ), "Playpulsar.gameplay.execute('allianceCreate',null,null,null,null);", "width: 140px; margin: 2px;" );
		}

		if (!empty($tOperations)) {
			\Gameplay\Panel\Action::getInstance()->add('<div class="panel ui-shadow-all"	style="width: 150px; float: right; text-align: center;">'.$tOperations.'</div>');
		}

		/*
		 * Wyrenderowanie sojuszu
		 */
		$registry = new allianceRegistry ($userID);
		\Gameplay\Panel\Action::getInstance()->add($registry->get());

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

	}

	/**
	 * Pobranie rejestru
	 *
	 * @return string
	 */
	public function get() {

		$oCacheKey = new \phpCache\CacheKey('alliance::getRegistry', null);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (!$oCache->check ( $oCacheKey )) {

			$retVal = '';
			//@todo: nawigacja po stronach
			$retVal .= "<h1>{T:alliances}</h1>";
			$retVal .= "<table class=\"table table-striped table-condensed linked\">";

			$retVal .= '<thead>';
			$retVal .= '<tr>';
			$retVal .= '<th>#</th>';
			$retVal .= '<th>{T:Symbol}</th>';
			$retVal .= '<th>{T:Name}</th>';
			$retVal .= '<th>{T:Members}</th>';
			$retVal .= '</tr>';
			$retVal .= '</thead>';
			$retVal .= '<tbody>';

			$tQuery = "SELECT
                alliances.*,
                (SELECT COUNT(*) FROM alliancemembers WHERE alliancemembers.AllianceID=alliances.AllianceID) AS MembersCount
              FROM
                alliances
              WHERE
                1
              ORDER BY
                Name ASC
              LIMIT 30
                ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			$tIndex = 0;
			while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

				$tIndex ++;

				$retVal .= '<tr onclick="Playpulsar.gameplay.execute(\'allianceDetail\',null,null,\''.$tResult->AllianceID.'\')">';
				$retVal .= '<td>' . $tIndex . '</td>';
				$retVal .= '<td>' . $tResult->Symbol . '</td>';
				$retVal .= '<td>' . $tResult->Name . '</td>';
				$retVal .= '<td>' . $tResult->MembersCount . '</td>';
				$retVal .= '</tr>';
			}

			$retVal .= '</table>';
			$retVal .= '</div>';
			$oCache->set ( $oCacheKey, $retVal, 3600 );
		} else {
			$retVal = $oCache->get ( $oCacheKey );
		}
		return $retVal;
	}
}