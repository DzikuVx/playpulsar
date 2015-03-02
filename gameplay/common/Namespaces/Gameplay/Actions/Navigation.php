<?php

namespace Gameplay\Actions;

use Gameplay\Framework\ContentTransport;
use Gameplay\Model\Coordinates;
use Gameplay\Model\GalaxyRouting;
use Gameplay\Model\ShipPosition;
use Gameplay\Model\ShipRouting;
use Gameplay\Model\SystemRouting;
use Gameplay\Model\TransNode;
use Gameplay\PlayerModelProvider;

class Navigation {

	static public function sAddCurrentToFavourities() {

        $userProperties = PlayerModelProvider::getInstance()->get('UserEntity');
        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');

		$tQuery = "SELECT COUNT(*) AS Ile FROM favouritesectors WHERE UserID='{$userProperties->UserID}' AND System='{$shipPosition->System}' AND X='{$shipPosition->X}' AND Y='{$shipPosition->Y}' ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

		if (\Database\Controller::getInstance()->fetch ( $tQuery )->Ile == 0) {
			$tQuery = "INSERT INTO favouritesectors(UserID, System, X, Y) VALUES('{$userProperties->UserID}','{$shipPosition->System}','{$shipPosition->X}','{$shipPosition->Y}')";
			\Database\Controller::getInstance()->execute($tQuery);
		}

		ContentTransport::getInstance()->addNotification( 'success', '{T:sectorAddedToFavs}');
	}

	/**
	 * Usunięcie sektora z ulubionych
	 *
	 * @param string $sector
	 */
	static public function sDeleteFavSector($sector) {
		$tArray = explode ( '/', $sector );
        $userProperties = PlayerModelProvider::getInstance()->get('UserEntity');

		$tQuery = "DELETE FROM favouritesectors WHERE UserID='{$userProperties->UserID}' AND System='{$tArray[0]}' AND X='{$tArray[1]}' AND Y='{$tArray[2]}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		ContentTransport::getInstance()->addNotification( 'success', '{T:sectorDeletedFromFavs}');
	}

	/**
	 * Reset puntu nawigacyjnego
	 *
	 */
	static public function sPlotReset() {

        /** @var ShipRouting $shipRouting */
        $shipRouting = PlayerModelProvider::getInstance()->get('ShipRouting');

        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');

		$shipRouting->System = null;
		$shipRouting->X = null;
		$shipRouting->Y = null;

		\Gameplay\Panel\Navigation::getInstance()->render ( $shipPosition, $shipRouting);

	}

	/**
	 * Następny waypoint
	 *
	 */
	static public function sNextWaypoint() {

		global $action, $subaction;

        /** @var ShipRouting $shipRouting */
        $shipRouting = PlayerModelProvider::getInstance()->get('ShipRouting');

        /** @var ShipPosition $shipPosition */
        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');

		/**
		 * Pobierz współrzędne docelowe
		 */
		$tCoords = new Coordinates($shipRouting->System, $shipRouting->X, $shipRouting->Y);

		if ($shipPosition->System != $shipRouting->System) {
			/**
			 * Get next jump node coordinates
			 */

			$galaxyRoute = new GalaxyRouting($tCoords);
			$nextSystem = $galaxyRoute->next($shipPosition->getCoordinates());

			/**
			 * Inicjacja obiektu TransNode
			 */
			$tNode = new \stdClass();
			$tNode->Source = $shipPosition->System;
			$tNode->Destination = $nextSystem;
			$transNode = new TransNode($tNode);

			$tCoords->System = $shipPosition->System;
			$tCoords->X = $transNode->X;
			$tCoords->Y = $transNode->Y;
		}

		$route = new SystemRouting($tCoords);

		if ($shipPosition->System != $shipRouting->System && $shipPosition->X == $tCoords->X && $shipPosition->Y == $tCoords->Y) {
			$action = "shipNodeJump";
		} else {
			$action = "shipMove";
			$subaction = $route->next($shipPosition->getCoordinates())->direction;
		}
	}

}