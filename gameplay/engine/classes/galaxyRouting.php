<?php

/**
 * Klasa routingu pomiędzy systemami
 * @version $Rev: 453 $
 * @package Engine
 *
 */
class galaxyRouting extends systemRouting {
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
	 * Pobranie dystansu (ilości systemów) na drodze do celu
	 * @param stdClass $current
	 * @return int
	 */
	public function getDistance($current) {
		if ($this->routeTable == null)
		return false;
			
		/*
		 * Czy jest route do systemu docelowego
		 */
		if ($this->routeTable [$current->System] ['value'] == null) {
			return 0;
		}

		$retVal = $this->routeTable [$current->System] ['value'];

		return $retVal;
	}

	/**
	 * (non-PHPdoc)
	 * @see systemRouting::getCacheProperty()
	 */
	protected function getCacheProperty() {
		return $this->destination->System;
	}

	/**
	 * Pobranie tablicy wszystkich nodów
	 */
	private function getNodes() {

		$module = 'galaxyRouting::getNodes';
		$property = '';

		if (!\Cache\Controller::getInstance()->check($module, $property)) {

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

			\Cache\Controller::getInstance()->set($module, $property, serialize($this->tNodes), $this->cacheTime);

		}else {
			$this->tNodes = unserialize(\Cache\Controller::getInstance()->get($module, $property));
		}

		return true;
	}

	/**
	 * Pobranie listy wszystkich systemów
	 * @uses mCache
	 */
	private function getSystems() {

		$module = 'galaxyRouting::getSystems';
		$property = '';

		if (!\Cache\Controller::getInstance()->check($module, $property)) {

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

			\Cache\Controller::getInstance()->set($module, $property, serialize($this->tRoute), $this->cacheTime);

		}else {
			$this->tRoute = unserialize(\Cache\Controller::getInstance()->get($module, $property));
		}

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see systemRouting::generate()
	 */
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

				$this->tRoute [$this->current]->analized = true;

				//Pobierz wszystkie systemy sąsiednie
				foreach ( array_keys ( $this->tNodes [$this->current] ) as $key ) {

					if ($this->tRoute [$key]->analized == false) {
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