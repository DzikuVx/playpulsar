<?php
class shipEquipmentRegistry extends simpleRegistry {

	static public function sRender() {

		global $config, $userID, $shipProperties, $action, $subaction, $value, $id;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = \Gameplay\PlayerModelProvider::getInstance()->get('PortEntity');

		$repairTemplate = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/shipRepairTable.html' );
		$repairTemplate->add ( $shipProperties );
		$repairTemplate->add ( 'EmpRegeneration', $config ['emp'] ['repairRatio'] );
        \Gameplay\Model\ShipProperties::sRenderRepairButtons ( $repairTemplate, 'summary' );

		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/shipSummary.html' );

		$sP = clone $shipProperties;

		$sP->CanWarpJump = \General\Formater::sParseYesNo($sP->CanWarpJump);
		$sP->CanActiveScan = \General\Formater::sParseYesNo($sP->CanActiveScan);

		$template->add ( $sP );

		$template->add ( 'REPAIR_TABLE', ( string ) $repairTemplate );

		\Gameplay\Panel\Action::getInstance()->add((string) $template);

		$registry = new shipEquipmentRegistry ( $userID );
		\Gameplay\Panel\Action::getInstance()->add($registry->get ( $shipPosition, $portProperties, $action, $subaction, $value, $id ));

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

	}

    /**
     * @param \Gameplay\Model\ShipPosition $shipPosition
     * @param \Gameplay\Model\PortEntity $portProperties
     * @return string
     */
    public function get(\Gameplay\Model\ShipPosition $shipPosition, \Gameplay\Model\PortEntity $portProperties) {

		global $colorTable, $userStats;

        $shipEquipment = \Gameplay\PlayerModelProvider::getInstance()->get('ShipEquipments');

		$retVal = '';

		$retVal .= "<h1>{T:equipment}</h1>";
		$retVal .= "<table class='table table-striped table-condensed'>";

		$retVal .= "<tr>";
		$retVal .= "<th>{T:name}</th>";
		$retVal .= "<th style='width: 3em;'>{T:size}</th>";
		$retVal .= "<th style='width: 5em;'>&nbsp;</th>";
		$retVal .= "</tr>";

		$tQuery = $shipEquipment->get ( "all" );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			if ($tR1->Damaged != '0') {
				$modifier = "style=\"color: " . $colorTable ['red'] . "\"";
				$tR1->Name .= ' [{T:damaged}]';
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

					$tString .= \General\Controls::renderImgButton ( 'sell', "Playpulsar.gameplay.execute('sellEquipment','',null,{$tR1->ShipEquipmentID},null);", '{T:sell}');
				}

			} else {

				/*
				 * Jeśli zadokowany w stacji
				 */
				if ($shipPosition->Docked == 'yes') {

					$tRepairPrice = equipment::sGetRepairPrice ( $tR1->EquipmentID );

					if ($userStats->Cash > $tRepairPrice) {
						$tString .= \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('stationRepairEquipment','',null,{$tR1->ShipEquipmentID},null);", '{T:RepairFor}' . $tRepairPrice . '$' );
					}

					if ($portProperties->Type == 'station') {
						$tString .= \General\Controls::renderImgButton ( 'sell', "Playpulsar.gameplay.execute('sellEquipment','',null,{$tR1->ShipEquipmentID},null);", '{T:sell}' );
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