<?php

class ftlDrive {

	/**
	 * Zużycie energii przez napęd
	 */
	static private function sGetPowerUsage($shipProperties) {
		return ceil($shipProperties->PowerMax / 2);
	}

	/**
	 * Zużycie antymaterii przez napęd
	 * @param stdClass $shipRouting
	 * @param shipPosition $shipPosition
	 */
	static private function sGetAmUsage($shipRouting, $shipPosition) {

		$galaxyRoute = new galaxyRouting ( \Database\Controller::getInstance(), $shipRouting );
		$tDistance = $galaxyRoute->getDistance($shipPosition);

		$retVal = 20+($tDistance * 20);

		return $retVal;
	}

	static public function sEngage() {
		global $userID, $shipProperties, $shipPosition, $shipRouting, $userStats, $config, $sectorProperties,$portProperties, $systemProperties, $jumpNode, $sectorPropertiesObject, $portPropertiesObject, $jumpNodeObject;

		if (shipProperties::sCheckMalfunction ( $shipProperties )) {
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

		$targetSystemProperties = systemProperties::quickLoad($shipRouting->System);

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

		userStats::incExperience ( $userStats, $config ['general'] ['expForWarpJump'] );

		//Odświerz informacje o sektorze
		$sectorProperties = $sectorPropertiesObject->reload ( $shipPosition, $sectorProperties, true, true );
		$portProperties = $portPropertiesObject->reload ( $shipPosition, $portProperties, true, true );
		$systemProperties->reload( $shipPosition->System);

		$jumpNode = $jumpNodeObject->load ( $shipPosition, true, true );

		sectorProperties::sResetResources ( $shipPosition, $sectorProperties );
		portProperties::sReset ( $portProperties );

		global $miniMap;

		\Gameplay\Panel\Sector::getInstance()->render ( $sectorProperties, $systemProperties, $shipPosition );

		\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
		\Gameplay\Panel\SectorResources::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
		\Gameplay\Panel\Port::getInstance()->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );

		$miniMap->load ( $userID, $shipPosition->System, $shipPosition );

		if (shipRouting::checkArrive ( $shipPosition, $shipRouting )) {
			\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting, $shipProperties );
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:infoArrived}');
		}

		\Gameplay\Panel\PortAction::getInstance()->clear ();

	}

}