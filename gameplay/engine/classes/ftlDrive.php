<?php

use Gameplay\PlayerModelProvider;

class ftlDrive {

    /**
     * @param \Gameplay\Model\ShipProperties $shipProperties
     * @return float
     */
    static private function sGetPowerUsage(\Gameplay\Model\ShipProperties $shipProperties) {
		return ceil($shipProperties->PowerMax / 2);
	}

    /**
     * @param stdClass $shipRouting
     * @param \Gameplay\Model\ShipPosition $shipPosition
     * @return int
     */
    static private function sGetAmUsage($shipRouting, \Gameplay\Model\ShipPosition $shipPosition) {

		$galaxyRoute = new galaxyRouting (\Database\Controller::getInstance(), $shipRouting );
		$tDistance = $galaxyRoute->getDistance($shipPosition);

		$retVal = 20 + ($tDistance * 20);

		return $retVal;
	}

	static public function sEngage() {
		global $userID, $shipProperties, $shipRouting, $userStats, $config, $sectorProperties, $jumpNode, $sectorPropertiesObject, $jumpNodeObject;

        $shipPosition     = PlayerModelProvider::getInstance()->get('ShipPosition');
        $systemProperties = PlayerModelProvider::getInstance()->get('SystemProperties');
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');

		if ($shipProperties->checkMalfunction()) {
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'error', '{T:shipMalfunctionEmp}');
			return false;
		}

		if ($shipPosition->Docked != 'no') {
			throw new securityException();
		}

		if (empty($shipRouting->System)) {
			throw new securityException();
		}

		if (empty($shipProperties->CanWarpJump)) {
			throw new securityException();
		}

		$tPowerUsage = self::sGetPowerUsage($shipProperties);
		$tAmUsage = self::sGetAmUsage($shipRouting, $shipPosition);

		if ($shipProperties->Power < $tPowerUsage) {
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification('warning', '{T:notEnoughPower}');
			return false;
		}

		if ($shipProperties->Turns < $tAmUsage) {
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification('warning', '{T:notEnoughTurns}');
			return false;
		}

		$targetSystemProperties = \Gameplay\Model\SystemProperties::quickLoad($shipRouting->System);

		$newX = rand($shipRouting->X - 2,$shipRouting->X + 2);
		$newY = rand($shipRouting->Y - 2,$shipRouting->Y + 2);

		if ($newX < 1) {
			$newX = 1;
		}
		if ($newX > $targetSystemProperties->Width) {
			$newX = $targetSystemProperties->Width;
		}
		if ($newY < 1) {
			$newY = 1;
		}
		if ($newY > $targetSystemProperties->Height) {
			$newY = $targetSystemProperties->Height;
		}

		$shipPosition->X = $newX;
		$shipPosition->Y = $newY;
		$shipPosition->System = $shipRouting->System;

		$shipProperties->Power -= $tPowerUsage;
		if ($shipProperties->Power < 0) {
			$shipProperties->Power = 0;
		}
		$shipProperties->Turns -= $tAmUsage;
		if ($shipProperties->Turns < 0) {
			$shipProperties->Turns = 0;
		}
		if ($shipProperties->RookieTurns > 0) {
			$shipProperties->RookieTurns -= $tAmUsage;
			if ($shipProperties->RookieTurns < 0) {
				$shipProperties->RookieTurns = 0;
			}
		}

        $userStats->incExperience($config ['general'] ['expForWarpJump']);

		//Odświerz informacje o sektorze
        $sectorProperties->reload($shipPosition);
		$portProperties->reload($shipPosition);
		$systemProperties->reload($shipPosition->System);

		$jumpNode = $jumpNodeObject->load ( $shipPosition, true, true );

		\Gameplay\Model\SectorEntity::sResetResources($shipPosition, $sectorProperties);
		\Gameplay\Model\PortEntity::sReset($portProperties);

		\Gameplay\Panel\Sector::getInstance()->render($sectorProperties, $systemProperties, $shipPosition);
		\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
		\Gameplay\Panel\SectorResources::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
		\Gameplay\Panel\Port::getInstance()->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );

		\Gameplay\Panel\MiniMap::getInstance()->load ( $userID, $shipPosition->System, $shipPosition );

		if (shipRouting::checkArrive ( $shipPosition, $shipRouting )) {
			\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting, $shipProperties );
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:infoArrived}');
		}
		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:Jump completed}');

		\Gameplay\Panel\PortAction::getInstance()->clear ();
        return true;
	}

}