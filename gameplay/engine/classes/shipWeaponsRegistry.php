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

		global $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id;

		$registry = new shipWeaponsRegistry ( $userID );

		\Gameplay\Panel\Action::getInstance()->add($registry->get ( $shipPosition, $portProperties, $action, $subaction, $value, $id ));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

	}

	/**
	 * Wyrenderowanie rejestru uzbrojenia
	 *
	 * @param \Gameplay\Model\ShipPosition $shipPosition
	 * @param stdClass $portProperties
	 * @param string $action
	 * @param string $subaction
	 * @param string $value
	 * @param string $id
	 * @return string
	 */
	public function get(\Gameplay\Model\ShipPosition $shipPosition, $portProperties, $action, $subaction, $value, $id) {

		global $shipWeapons, $colorTable, $userStats;

		$retVal = '';

		$retVal .= "<h1>{T:weapons}</h1>";

		$retVal .= "<table class=\"table table-striped table-condensed\">";

		$retVal .= "<tr>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'name' ) . "</th>";
		$retVal .= "<th style=\"width: 4em;\">" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
		$retVal .= "<th style=\"width: 3em;\">" . TranslateController::getDefault()->get ( 'ammo' ) . "</th>";
		$retVal .= "<th style=\"width: 16em;\">&nbsp;</th>";
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
			$retVal .= '</td>';

			$tString = '';
			$tString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','weapon::renderDetail','{$this->language}','{$tR1->WeaponID}')", 'Info' );

			/*
			 * Zmiana kolejności
			 */
			$tString .= \General\Controls::renderImgButton ( 'up', "Playpulsar.gameplay.execute('weaponMoveUp',null,null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'Move Up' ) );
			$tString .= \General\Controls::renderImgButton ( 'down', "Playpulsar.gameplay.execute('weaponMoveDown',null,null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'Move Down' ) );

			if ($tR1->Damaged == '0') {

				if ($tR1->Enabled == 0) {
					$tString .= \General\Controls::renderImgButton ( 'on', "Playpulsar.gameplay.execute('switchWeaponState','slow',null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'On') );
				} else {
					$tString .= \General\Controls::renderImgButton ( 'off', "Playpulsar.gameplay.execute('switchWeaponState','slow',null,{$tR1->ShipWeaponID},null);",TranslateController::getDefault()->get (  'Off') );
				}

				if ($shipPosition->Docked == 'yes' && $portProperties->Type == 'station') {
					$tString .= \General\Controls::renderImgButton ( 'sell', "Playpulsar.gameplay.execute('sellWeapon','',null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'sell' ) );
				}

			} else {

				/*
			    * Jeśli zadokowany w stacji
			    */
				if ($shipPosition->Docked == 'yes') {

					$tRepairPrice = weapon::sGetRepairPrice ( $tR1->WeaponID );
					if ($userStats->Cash > $tRepairPrice) {
						$tString .= \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('weaponRepair','',null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'Repair for' ) . $tRepairPrice . '$' );
					}

					if ($portProperties->Type == 'station') {
						$tString .= \General\Controls::renderImgButton ( 'sell', "Playpulsar.gameplay.execute('sellWeapon','',null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'sell' ) );
					}
				}

			}

			$tReloadPrice = weapon::sGetReloadPrice ( $tR1->WeaponID, $tR1->Ammo );
			if ($shipPosition->Docked == 'yes' && $portProperties->Type == 'station' && $tR1->MaxAmmo > 0 && $tR1->Ammo != $tR1->MaxAmmo && $userStats->Cash > $tReloadPrice) {
				$tString .= \General\Controls::renderImgButton ( 'reload', "Playpulsar.gameplay.execute('weaponReload',null,null,{$tR1->ShipWeaponID},null);", TranslateController::getDefault()->get ( 'Reload for' ) . ' ' . $tReloadPrice . '$' );
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