<?php
/**
 * rejestr wyposażenia statku
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class shipEquipmentRegistry extends simpleRegistry {

	/**
	 * Konstruktor statyczny
	 *
	 */
	static public function sRender() {

		global $config, $userID, $shipProperties, $actionPanel, $shipPosition, $portProperties, $action, $subaction, $value, $id, $portPanel;

		$repairTemplate = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/shipRepairTable.html' );
		$repairTemplate->add ( $shipProperties );
		$repairTemplate->add ( 'EmpRegeneration', $config ['emp'] ['repairRatio'] );
		shipProperties::sRenderRepairButtons ( $repairTemplate, 'summary' );

		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/shipSummary.html' );

		$sP = clone $shipProperties;
		
		$sP->CanWarpJump = \General\Formater::sParseYesNo($sP->CanWarpJump);
		$sP->CanActiveScan = \General\Formater::sParseYesNo($sP->CanActiveScan);
		
		$template->add ( $sP );

		$template->add ( 'REPAIR_TABLE', ( string ) $repairTemplate );

		$actionPanel .= $template;

		$registry = new shipEquipmentRegistry ( $userID );
		$actionPanel .= $registry->get ( $shipPosition, $portProperties, $action, $subaction, $value, $id );
		unset($registry);

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		$portPanel = "&nbsp;";

	}

	/**
	 * Lista wyposażenia statku
	 *
	 * @param stdClass $shipPosition
	 * @param stdClass $portProperties
	 * @param string $action
	 * @param string $subaction
	 * @param string $value
	 * @param int $id
	 * @return string
	 */
	public function get($shipPosition, $portProperties, $action, $subaction, $value, $id) {

		global $shipEquipment, $colorTable, $userStats;

		$retVal = '';

		$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'equipment' ) . "</h1>";
		$retVal .= "<table class='table table-striped table-condensed'>";

		$retVal .= "<tr>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
		$retVal .= "<th style='width: 3em;'>" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
		$retVal .= "<th style='width: 2em;'>&nbsp;</th>";
		$retVal .= "</tr>";

		$tQuery = $shipEquipment->get ( "all" );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			if ($tR1->Damaged != '0') {
				$modifier = "style=\"color: " . $colorTable ['red'] . "\"";
				$tR1->Name .= ' [' . TranslateController::getDefault()->get ( 'damaged' ) . ']';
			} else {
				$modifier = "style=\"color: " . $colorTable ['green'] . "\"";
			}
			$retVal .= '<tr>';
			$retVal .= '<td ' . $modifier . '>' . $tR1->Name . '</td>';
			$retVal .= '<td ' . $modifier . '>' . $tR1->Size . '</td>';

			$tString = '';

			$tString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','equipment::renderDetail','{$this->language}','{$tR1->EquipmentID}')", 'Info' );

			if ($tR1->Damaged == '0') {

				if ($shipPosition->Docked == 'yes' && $portProperties->Type == 'station') {

					$tString .= \General\Controls::renderImgButton ( 'sell', "Playpulsar.gameplay.execute('sellEquipment','',null,{$tR1->ShipEquipmentID},null);", TranslateController::getDefault()->get ( 'sell' ) );
				}

			} else {
 
				/*
				 * Jeśli zadokowany w stacji
				 */
				if ($shipPosition->Docked == 'yes') {

					$tRepairPrice = equipment::sGetRepairPrice ( $tR1->EquipmentID );

					if ($userStats->Cash > $tRepairPrice) {
						$tString .= \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepairEquipment','',null,{$tR1->ShipEquipmentID},null);", TranslateController::getDefault()->get ( 'RepairFor' ) . $tRepairPrice . '$' );
					}

					if ($portProperties->Type == 'station') {
						$tString .= \General\Controls::renderImgButton ( 'sell', "Playpulsar.gameplay.execute('sellEquipment','',null,{$tR1->ShipEquipmentID},null);", TranslateController::getDefault()->get ( 'sell' ) );
					}
				}

			}

			if (empty ( $tString )) {
				$tString = '&nbsp;';
			}

			$retVal .= '<td ' . $modifier . '>' . $tString . '</td>';

			$retVal .= '</tr>';
		}
		$retVal .= "</table>";

		return $retVal;
	}

}