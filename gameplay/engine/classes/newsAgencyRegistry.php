<?php
/**
 * Klasa listy notices w systemie
 * @version $Rev: 460 $
 * @package Engine
 *
 */
class newsAgencyRegistry extends simpleRegistry {

	static public function sRender() {

		global $userID, $actionPanel, $shipPosition, $portPanel;

		$registry = new newsAgencyRegistry ( $userID );
		$actionPanel .= $registry->get ( $shipPosition );
		unset($registry);

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		$portPanel = "&nbsp;";

	}

	/**
	 * Pobranie rejestru
	 *
	 * @param stdClass $shipPosition
	 * @return string
	 */
	public function get($shipPosition) {

		global $userProperties;

		$module = 'newsAgency::get';
		$property = $shipPosition->System . '|' . $userProperties->Language;

		if (! \Cache\Controller::getInstance()->check ( $module, $property )) {

			$retVal = '';
			//@todo: nawigacja po stronach
			//@todo: dostęp do innych systemów dla kont pro
			$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'newsAgency' ) . " System " . $shipPosition->System . "</h1>";

			$retVal .= "<table class=\"table table-striped table-condensed\">";

			$tQuery = "SELECT
        newsagency.*
      FROM
        newsagency JOIN newsagencytypes ON newsagencytypes.ID=newsagency.Type
      WHERE
        newsagencytypes.Visible='yes' AND
        newsagency.System='{$shipPosition->System}'
      ORDER BY
        Date DESC
      LIMIT 30
        ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

				$tObject = unserialize ( $tR1->Text );
				$retVal .= $tObject->render ( false );
				$tObject->doSave = false;
				unset ( $tObject );
			}

			$retVal .= '</tbody>';
			$retVal .= '</table>';
			$retVal .= '</div>';
			\Cache\Controller::getInstance()->set ( $module, $property, $retVal, 3600 );
		} else {
			$retVal = \Cache\Controller::getInstance()->get ( $module, $property );
		}
		return $retVal;
	}

}