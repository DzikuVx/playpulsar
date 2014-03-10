<?php

namespace Gameplay\Actions;

use Gameplay\Exception\SecurityException;
use Gameplay\Framework\ContentTransport;
use Gameplay\Model\GalaxyRouting;
use Gameplay\Model\ShipPosition;
use Gameplay\Model\ShipProperties;
use Gameplay\Model\ShipRouting;
use Gameplay\Model\SystemProperties;
use Gameplay\Panel\MiniMap;
use Gameplay\Panel\PortAction;
use Gameplay\PlayerModelProvider;

class FtlDrive {

    /**
     * @param ShipProperties $shipProperties
     * @return float
     */
    static private function sGetPowerUsage(ShipProperties $shipProperties) {
		return ceil($shipProperties->PowerMax / 2);
	}

    /**
     * @param ShipRouting $shipRouting
     * @param ShipPosition $shipPosition
     * @return int
     */
    static private function sGetAmUsage(ShipRouting $shipRouting, ShipPosition $shipPosition) {

		$galaxyRoute = new GalaxyRouting($shipRouting->getCoordinates());
		$tDistance = $galaxyRoute->getDistance($shipPosition->getCoordinates());

		$retVal = 20 + ($tDistance * 20);

		return $retVal;
	}

	static public function sEngage() {
		global $userID, $shipProperties, $userStats, $config, $sectorProperties;

        /** @var ShipPosition $shipPosition */
        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');
        /** @var SystemProperties $systemProperties */
        $systemProperties = PlayerModelProvider::getInstance()->get('SystemProperties');
        /** @var \Gameplay\Model\PortEntity $portProperties */
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');

        /** @var ShipRouting $shipRouting */
        $shipRouting = PlayerModelProvider::getInstance()->get('ShipRouting');

		if ($shipProperties->checkMalfunction()) {
			ContentTransport::getInstance()->addNotification( 'error', '{T:shipMalfunctionEmp}');
			return false;
		}

		if ($shipPosition->Docked != 'no') {
			throw new SecurityException();
		}

		if (empty($shipRouting->System)) {
			throw new SecurityException();
		}

		if (empty($shipProperties->CanWarpJump)) {
			throw new SecurityException();
		}

		$tPowerUsage = self::sGetPowerUsage($shipProperties);
		$tAmUsage = self::sGetAmUsage($shipRouting, $shipPosition);

		if ($shipProperties->Power < $tPowerUsage) {
			ContentTransport::getInstance()->addNotification('warning', '{T:notEnoughPower}');
			return false;
		}

		if ($shipProperties->Turns < $tAmUsage) {
			ContentTransport::getInstance()->addNotification('warning', '{T:notEnoughTurns}');
			return false;
		}

		$targetSystemProperties = new SystemProperties($shipRouting->System);

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

        $sectorProperties->reload($shipPosition->getCoordinates());
		$portProperties->reload($shipPosition->getCoordinates());
		$systemProperties->reload($shipPosition->System);

        /** @var \Gameplay\Model\JumpNode $jumpNode */
        $jumpNode = PlayerModelProvider::getInstance()->register('JumpNode', new \Gameplay\Model\JumpNode($shipPosition));

        $sectorProperties->resetResources();
        $portProperties->reset();

		\Gameplay\Panel\Sector::getInstance()->render($sectorProperties, $systemProperties, $shipPosition);
		\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
		\Gameplay\Panel\SectorResources::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
		\Gameplay\Panel\Port::getInstance()->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );

		MiniMap::getInstance()->load ( $userID, $shipPosition->System, $shipPosition );

		if ($shipRouting->checkArrive($shipPosition->getCoordinates())) {
			\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting);
			ContentTransport::getInstance()->addNotification( 'success', '{T:infoArrived}');
		}
		ContentTransport::getInstance()->addNotification( 'success', '{T:Jump completed}');

		PortAction::getInstance()->clear ();
        return true;
	}

}