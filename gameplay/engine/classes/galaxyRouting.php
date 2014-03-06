<?php

class galaxyRouting extends \Gameplay\Model\SystemRouting {
	protected $tNodes;

	/**
	 * Pobranie numeru nastepnego systemu po drodze
	 *
	 * @param stdClass $current
	 * @return int
	 */
	public function next($current) {

		if ($this->routeTable == null)
		return false;
			
		/*
		 * Czy jest route do systemu docelowego
		 */
		if ($this->routeTable [$current->System] ['previous'] == null)
		return false;

		$retVal = $this->routeTable [$current->System] ['previous'];

		return $retVal;
	}

    /**
     * Get distance between current and remote location (number of systems)
     * @param \Gameplay\Model\ShipPosition $current
     * @return bool|int
     */
    public function getDistance(\Gameplay\Model\ShipPosition $current) {

        if ($this->routeTable == null) {
		    return false;
        }

		/*
		 * Czy jest route do systemu docelowego
		 */
		if ($this->routeTable [$current->System] ['value'] == null) {
			return 0;
		}

		$retVal = $this->routeTable [$current->System] ['value'];

		return $retVal;
	}

	protected function getCacheProperty() {
		return $this->destination->System;
	}

	private function getNodes() {

		$oCacheKey = new \phpCache\CacheKey('galaxyRouting::getNodes', '');
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (!$oCache->check($oCacheKey)) {

			$this->tNodes = array ();

			$tQuery = "
		      SELECT
		        nodes.SrcSystem,
		        nodes.DstSystem
		      FROM
		        (nodes JOIN systems AS s1 ON s1.SystemID = nodes.SrcSystem)
		        JOIN systems AS s2 ON s2.SystemID = nodes.DstSystem
		      WHERE
		        nodes.Active = 'yes' AND
		        s1.Enabled = 'yes' AND s2.Enabled = 'yes'
		    ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				$this->tNodes [$resultRow->SrcSystem] [$resultRow->DstSystem] = true;
				$this->tNodes [$resultRow->DstSystem] [$resultRow->SrcSystem] = true;
			}

			$oCache->set($oCacheKey, serialize($this->tNodes), $this->cacheTime);

		} else {
			$this->tNodes = unserialize($oCache->get($oCacheKey));
		}

		return true;
	}

	/**
	 * Pobranie listy wszystkich systemów
	 * @uses mCache
	 */
	private function getSystems() {

		$oCacheKey = new \phpCache\CacheKey('galaxyRouting::getSystems', '');
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (!$oCache->check($oCacheKey)) {

			$this->tRoute = array ();

			$tQuery = \Database\Controller::getInstance()->execute ( "SELECT
                systems.SystemID
              FROM
                systems
              WHERE
                systems.Enabled = 'yes'
                " );
			while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				$this->tRoute [$resultRow->SystemID] = new routingSystem ( );
			}

			$oCache->set($oCacheKey, serialize($this->tRoute), $this->cacheTime);

		}else {
			$this->tRoute = unserialize($oCache->get($oCacheKey));
		}

		return true;
	}

	public function generate($destination) {
		
		/**
		 * Wygeneruj mapę nodów
		 */
		$this->getNodes();

		/*
		 * Zainicjuj mapę systemów
		 */
		$this->getSystems();

		$this->tToGo = array ();

		array_push ( $this->tToGo, $destination->System );

		$this->tRoute [$destination->System]->value = 0;

		$this->go = true;
		//Póki tablica jest wypełniona
		while ( $this->go ) {

			$this->tArray = array ();
			$this->go = false;
			//Zacznij pobierać sektory z tablicy
			while ( $this->current = array_pop ( $this->tToGo ) ) {

				$this->tRoute [$this->current]->analyzed = true;

				//Pobierz wszystkie systemy sąsiednie
				foreach ( array_keys ( $this->tNodes [$this->current] ) as $key ) {

					if ($this->tRoute [$key]->analyzed == false) {
						if (! in_array ( $key, $this->tArray )) {
							array_push ( $this->tArray, $key );
							$this->go = true;
						}

						if ($this->tRoute [$this->current]->value + 1 < $this->tRoute [$key]->value) {
							$this->tRoute [$key]->value = $this->tRoute [$this->current]->value + 1;
							$this->tRoute [$key]->previous = $this->current;
						}
					}
				}
			}

			$this->tToGo = $this->tArray;
			unset ( $this->tArray );
		}

		/*
		 * Po zakończeniu budowania tablicy, przepisz do prostszej formy
		 */

		$tRoute = array ();

		foreach ( $this->tRoute as $key => $value ) {
			$tRoute [$key] ['value'] = $value->value;
			$tRoute [$key] ['previous'] = $value->previous;
		}

		unset ( $this->tRoute );

		return $tRoute;
	}
}