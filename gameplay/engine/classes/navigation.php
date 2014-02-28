<?php
/**
 * Metody nawigacyjne
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class navigation {

	static public function sAddCurrentToFavourities() {

        $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');
        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		/*
		 * Próba update
		 */
		$tQuery = "SELECT COUNT(*) AS Ile FROM favouritesectors WHERE UserID='{$userProperties->UserID}' AND System='{$shipPosition->System}' AND X='{$shipPosition->X}' AND Y='{$shipPosition->Y}' ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

		if (\Database\Controller::getInstance()->fetch ( $tQuery )->Ile == 0) {
			$tQuery = "INSERT INTO favouritesectors(UserID, System, X, Y) VALUES('{$userProperties->UserID}','{$shipPosition->System}','{$shipPosition->X}','{$shipPosition->Y}')";
			\Database\Controller::getInstance()->execute ( $tQuery );
		}

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:sectorAddedToFavs}');
	}

	/**
	 * Usunięcie sektora z ulubionych
	 *
	 * @param string $sector
	 */
	static public function sDeleteFavSector($sector) {
		$tArray = explode ( '/', $sector );
        $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');

		$tQuery = "DELETE FROM favouritesectors WHERE UserID='{$userProperties->UserID}' AND System='{$tArray[0]}' AND X='{$tArray[1]}' AND Y='{$tArray[2]}'";
		\Database\Controller::getInstance()->execute ( $tQuery );

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:sectorDeletedFromFavs}');
	}

	/**
	 * Reset puntu nawigacyjnego
	 *
	 */
	static public function sPlotReset() {

		global $shipRouting;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

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

		global $shipRouting, $action, $subaction;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');

		/**
		 * Pobierz współrzędne docelowe
		 */
		$tCoords = new \stdClass();

		if ($shipPosition->System == $shipRouting->System) {
			$tCoords->System = $shipRouting->System;
			$tCoords->X = $shipRouting->X;
			$tCoords->Y = $shipRouting->Y;
		} else {
			/**
			 * Przypadek gdy lecisz do innego systemu, pobierz współrzędne następnego Jump Node
			 */

			/**
			 * Inicjacja obiektu galaxyRoute
			 */
			$galaxyRoute = new \galaxyRouting ( \Database\Controller::getInstance(), $shipRouting );
			$nextSystem = $galaxyRoute->next ( $shipPosition );

			/**
			 * Inicjacja obiektu TransNode
			 */
			$tNode = new \stdClass();
			$tNode->Source = $shipPosition->System;
			$tNode->Destination = $nextSystem;
			$transNode = new \Gameplay\Model\TransNode($tNode);

			$tCoords->System = $shipPosition->System;
			$tCoords->X = $transNode->X;
			$tCoords->Y = $transNode->Y;
		}

		$route = new \systemRouting(\Database\Controller::getInstance(), $tCoords);

		if ($shipPosition->System != $shipRouting->System && $shipPosition->X == $tCoords->X && $shipPosition->Y == $tCoords->Y) {
			$action = "shipNodeJump";
		} else {
			$action = "shipMove";
			$subaction = $route->next ( $shipPosition )->direction;
		}
	}

}