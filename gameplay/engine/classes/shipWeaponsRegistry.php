<?php
/**
 * rejestr uzbrojenia okrętu
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class shipWeaponsRegistry extends simpleRegistry {

	/**
	 * Konstruktor statyczny
	 *
	 */
	static public function sRender() {

		global $userID, $actionPanel, $shipPosition, $portProperties, $action, $subaction, $value, $id, $portPanel;

		$registry = new shipWeaponsRegistry ( $userID );
		$actionPanel .= $registry->get ( $shipPosition, $portProperties, $action, $subaction, $value, $id );
		unset($registry);

		sectorShipsPanel::getInstance()->hide ();
		sectorResourcePanel::getInstance()->hide ();
		$portPanel = "&nbsp;";

	}

	/**
	 * Wyrenderowanie rejestru uzbrojenia
	 *
	 * @param stdClass $shipPosition
	 * @param stdClass $portProperties
	 * @param string $action
	 * @param string $subaction
	 * @param string $value
	 * @param string $id
	 * @return string
	 */
	public function get($shipPosition, $portProperties, $action, $subaction, $value, $id) {

		global $shipWeapons, $colorTable, $shipProperties, $userStats;

		$retVal = '';

		$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'weapons' ) . "</h1>";

		$retVal .= "<table class=\"transactionList\" cellspacing=\"2\" cellpadding=\"0\">";

		$retVal .= "<tr>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
		$retVal .= "<th style=\"width: 60px;\">" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
		$retVal .= "<th style=\"width: 6em;\">" . TranslateController::getDefault()->get ( 'ammo' ) . "</th>";
		$retVal .= "<th style=\"width: 10em;\">&nbsp;</th>";
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
				$tR1->Name .= ' [' . TranslateController::getDefault()->get ( 'damaged' ) . ']';
			}
			$retVal .= '<tr>';
			$retVal .= '<td ' . $modifier . '>' . $tR1->Name . '</td>';
			$retVal .= '<td ' . $modifier . '>' . $tR1->Size . '</td>';
			$retVal .= '<td ' . $modifier . '>' . $tR1->Ammo . '/' . $tR1->MaxAmmo;

			$tReloadPrice = weapon::sGetReloadPrice ( $tR1->WeaponID, $tR1->Ammo );
			if ($shipPosition->Docked == 'yes' && $portProperties->Type == 'station' && $tR1->MaxAmmo > 0 && $tR1->Ammo != $tR1->MaxAmmo && $userStats->Cash > $tReloadPrice) {
				$retVal .= \General\Controls::renderImgButton ( 'reload', "executeAction('weaponReload',null,null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'Reload for' ) . ' ' . $tReloadPrice . '$' );
			}
			$retVal .= '</td>';

			$tString = '';
			$tString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','weapon::renderDetail','{$this->language}','{$tR1->WeaponID}')", 'Info' );

			/*
			 * Zmiana kolejności
			 */
			$tString .= \General\Controls::renderImgButton ( 'up', "executeAction('weaponMoveUp',null,null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'Move Up' ) );
			$tString .= \General\Controls::renderImgButton ( 'down', "executeAction('weaponMoveDown',null,null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'Move Down' ) );

			if ($tR1->Damaged == '0') {

				if ($tR1->Enabled == 0) {
					$tString .= \General\Controls::renderImgButton ( 'add', "executeAction('switchWeaponState','slow',null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'On') );
				} else {
					$tString .= \General\Controls::renderImgButton ( 'remove', "executeAction('switchWeaponState','slow',null,{$tR1->ShipWeaponID},null);",TranslateController::getDefault()->get (  'Off') );
				}

				if ($shipPosition->Docked == 'yes' && $portProperties->Type == 'station') {
					$tString .= \General\Controls::renderImgButton ( 'sell', "executeAction('sellWeapon','',null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'sell' ) );
				}

			} else {

				/*
			    * Jeśli zadokowany w stacji
			    */
				if ($shipPosition->Docked == 'yes') {

					$tRepairPrice = weapon::sGetRepairPrice ( $tR1->WeaponID );
					if ($userStats->Cash > $tRepairPrice) {
						$tString .= \General\Controls::renderImgButton ( 'repair', "executeAction('weaponRepair','',null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'Repair for' ) . $tRepairPrice . '$' );
					}

					if ($portProperties->Type == 'station') {
						$tString .= \General\Controls::renderImgButton ( 'sell', "executeAction('sellWeapon','',null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'sell' ) );
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