<?php
/**
 * Uzbrojenie okrętu biorącego udział w walce
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class combatShipWeaponsRegistry extends simpleRegistry {
	
	//@todo poprawić deklarację
	public function get(\Gameplay\Model\ShipWeapons $shipWeapons, Translate $translate) {
		
		global $colorTable;
		
		$retVal = '';
		
		$retVal .= "<table>";
		
		$retVal .= "<tr>";
		$retVal .= "<th>" . $translate->get ( 'name' ) . "</th>";
		$retVal .= "<th style=\"width: 60px;\">" . $translate->get ( 'ammo' ) . "</th>";
		$retVal .= "</tr>";
		
		$tQuery = $shipWeapons->get ( "all" );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			
			if ($tR1->Enabled == '1') {
				$modifier = "style=\"color: " . $colorTable ['green'] . "\"";
				if ($tR1->Ammo == '0') {
					$modifier = "style=\"color: " . $colorTable ['red'] . "\"";
				}
			} else {
				$modifier = "style=\"color: " . $colorTable ['yellow'] . "\"";
			}
			
			if ($tR1->Damaged != '0') {
				$modifier = "style=\"color: " . $colorTable ['red'] . "\"";
				$tR1->Name .= ' [' . $translate->get ( 'damaged' ) . ']';
			}
			$retVal .= '<tr>';
			$retVal .= '<td ' . $modifier . '>' . $tR1->Name . '</td>';
			$retVal .= '<td ' . $modifier . '>' . $tR1->Ammo . '/' . $tR1->MaxAmmo . '</td>';
			$retVal .= '</tr>';
		}
		$retVal .= "</table>";
		
		return $retVal;
	}

}