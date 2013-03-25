<?php

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

		global $userID, $actionPanel, $portPanel, $userAlliance;

		/*
		 * Lista operacji na liście sojuszy
		 */
		$tOperations = '';
		if (empty($userAlliance->AllianceID)) {
			$tOperations .=	 \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'allianceCraete' ), "executeAction('allianceCreate',null,null,null,null);", "width: 140px; margin: 2px;" );
		}

		if (!empty($tOperations)) {
			$actionPanel .= '<div class="panel ui-shadow-all"	style="width: 150px; float: right; text-align: center;">'.$tOperations.'</div>';
		}

		/*
		 * Wyrenderowanie sojuszu
		 */
		$registry = new allianceRegistry ( $userID );
		$actionPanel .= $registry->get ();
		unset($registry);

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		$portPanel = "&nbsp;";

	}

	/**
	 * Pobranie rejestru
	 *
	 * @return string
	 */
	public function get() {

		$module = 'alliance::getRegistry';
		$property = null;

		if (! \Cache\Controller::getInstance()->check ( $module, $property )) {

			$retVal = '';
			//@todo: nawigacja po stronach
			$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'alliances' ) . "</h1>";
			$retVal .= "<table class=\"table table-striped table-condensed linked\">";

			$retVal .= '<thead>';
			$retVal .= '<tr>';
			$retVal .= '<th>#</th>';
			$retVal .= '<th>' . TranslateController::getDefault()->get ( 'Symbol' ) . '</th>';
			$retVal .= '<th>' . TranslateController::getDefault()->get ( 'Name' ) . '</th>';
			$retVal .= '<th>' . TranslateController::getDefault()->get ( 'Members' ) . '</th>';
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

				$retVal .= '<tr onclick="executeAction(\'allianceDetail\',null,null,\''.$tResult->AllianceID.'\')">';
				$retVal .= '<td>' . $tIndex . '</td>';
				$retVal .= '<td>' . $tResult->Symbol . '</td>';
				$retVal .= '<td>' . $tResult->Name . '</td>';
				$retVal .= '<td>' . $tResult->MembersCount . '</td>';
				$retVal .= '</tr>';
			}

			$retVal .= '</table>';
			$retVal .= '</div>';
			\Cache\Controller::getInstance()->set ( $module, $property, $retVal, 3600 );
		} else {
			$retVal = \Cache\Controller::getInstance()->get ( $module, $property );
		}
		return $retVal;
	}

}