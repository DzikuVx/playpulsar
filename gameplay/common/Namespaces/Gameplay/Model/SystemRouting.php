<?php

namespace Gameplay\Model;

use Gameplay\Helpers\RoutingCoordinates;
use Gameplay\Helpers\RoutingSector;
use phpCache\CacheKey;

class SystemRouting {
	protected $routeTable = null;

    /**
     * @var Coordinates
     */
    protected $destination = null;

    /**
     * @var SystemProperties
     */
    protected $systemObject = null;
	protected $go = false;

    /**
     * @var Coordinates[]
     */
    protected $tToGo;

    /**
     * @var RoutingCoordinates[]
     */
    protected $routingOutput;

    /**
     * @var Coordinates[]
     */
    protected $temporaryRoutingCoordinates;

    /**
     * @var Array
     */
    protected $tRoute;

    /**
     * @var Coordinates
     */
    protected $current;

	/**
	 * @var int
	 */
	protected $cacheTime = 604800;

	protected function nextPush($tx, $ty, $direction) {

		if ($tx > 0 && $tx <= $this->systemObject->Width && $ty > 0 && $ty <= $this->systemObject->Height) {
			array_push($this->routingOutput, new RoutingCoordinates($tx, $ty, $this->routeTable[$tx][$ty], $direction));
		}
		return true;
	}

	/**
	 * @param Coordinates $current
	 * @return \stdClass
	 */
	public function next(Coordinates $current) {

		if ($this->routeTable == null) {
		    return false;
        }

		$this->routingOutput = array ();

		$this->nextPush($current->X, $current->Y - 1, 'up');
		$this->nextPush($current->X, $current->Y + 1, 'down');
		$this->nextPush($current->X - 1, $current->Y, 'left');
		$this->nextPush($current->X + 1, $current->Y, 'right');

		usort($this->routingOutput, "routingSort");

		return array_pop($this->routingOutput);
	}

	/**
	 * @return array
	 */
	public function getRouteTable() {

		return $this->routeTable;
	}

	/**
	 * @param array $routeTable
	 */
	public function setRouteTable($routeTable) {

		$this->routeTable = $routeTable;
	}

	/**
	 * @return string
	 */
	protected function getCacheModule() {
		return get_class($this);
	}

	/**
	 * Pobranie drugiej składowej identyfikatora cache
	 * @return string
	 */
	protected function getCacheProperty() {
		return $this->destination->System.'|'.$this->destination->X.'|'.$this->destination->Y;
	}

	/**
	 * @return bool
	 */
	protected function delete() {
        \phpCache\Factory::getInstance()->create()->clear(new CacheKey($this->getCacheModule(), $this->getCacheProperty()));
		return true;
	}

	/**
	 * Zapisanie tablicy routingu do bazy danych
	 *
	 */
	protected function put() {

		if ($this->routeTable == null) {
			return false;
		}

		$oCacheKey = new CacheKey($this->getCacheModule(), $this->getCacheProperty());
        \phpCache\Factory::getInstance()->create()->set($oCacheKey, serialize ( $this->routeTable ), $this->cacheTime);

		return true;
	}

	/**
	 * Pobranie tablicy routingu z bazy danych
	 *
	 */
	protected function get() {

		$this->routeTable = null;

		$oCacheKey = new CacheKey($this->getCacheModule(), $this->getCacheProperty());
        $oCache    = \phpCache\Factory::getInstance()->create();

		if ($oCache->check($oCacheKey)) {
			$this->routeTable = unserialize ($oCache->get($oCacheKey));
		}
	}

    /**
     * @param Coordinates $destination
     * @return bool
     */
    public function load(Coordinates $destination) {

		$this->systemObject = new SystemProperties($destination->System);
		$this->destination = $destination;

		$this->get();

		/**
		 * Jeśli w bazie nie ma tablicy, wygenruj ją i zapisz
		 */
		if ($this->routeTable == null) {
			$this->routeTable = $this->generate($this->destination);
			$this->put();
		}

		return true;
	}

	/**
	 * Obsługa pojedynczego sektora podczas generowania tablicy routingu
	 *
	 * @param int $tx
	 * @param int $ty
	 */
	protected function setSector($tx, $ty) {

		if ($tx > 0 && $tx <= $this->systemObject->Width && $ty > 0 && $ty <= $this->systemObject->Height && $this->tRoute [$tx] [$ty]->analyzed == false) {
			$tObject = new Coordinates(null, $tx, $ty);
			if (!in_array($tObject, $this->temporaryRoutingCoordinates)) {
				array_push ( $this->temporaryRoutingCoordinates, $tObject );
				$this->go = true;
			}

			if ($this->tRoute [$this->current->X] [$this->current->Y]->value + $this->tRoute [$tx] [$ty]->cost < $this->tRoute [$tx] [$ty]->value) {
				$this->tRoute [$tx] [$ty]->value = $this->tRoute [$this->current->X] [$this->current->Y]->value + $this->tRoute [$tx] [$ty]->cost;
			}
		}
	}

	/**
	 * Pobranie listy wszystkich sektorów w systemie
	 */
	private function getSectors($systemID) {

		$oCacheKey = new CacheKey('systemRouting::getSectors', $systemID);
        $oCache    = \phpCache\Factory::getInstance()->create();
		
		if (!$oCache->check($oCacheKey)) {
			$this->tRoute = null;

            $oDb = \Database\Controller::getInstance();

			/*
			 * Inicjuj
			 */
            $oBaseSector = new RoutingSector();
			for($indexX = 1; $indexX <= $this->systemObject->Width; $indexX ++) {
				for($indexY = 1; $indexY <= $this->systemObject->Height; $indexY ++) {
					$this->tRoute [$indexX] [$indexY] = clone $oBaseSector;
				}
			}

			/*
			 * Pobierz sektory
			 */
			$tQuery = "SELECT
                sectortypes.MoveCost AS MoveCost,
                sectors.X AS X,
                sectors.Y AS Y
              FROM
                sectors JOIN sectortypes ON sectortypes.SectorTypeID = sectors.SectorTypeID
              WHERE
                sectors.System = '{$systemID}'
              ";
			$tQuery = $oDb->execute ( $tQuery );
			while ( $tR1 = $oDb->fetch ( $tQuery ) ) {
				$this->tRoute[$tR1->X][$tR1->Y]->cost = $tR1->MoveCost;
			}

			$oCache->set($oCacheKey, serialize($this->tRoute), $this->cacheTime);

		}else {
			$this->tRoute = unserialize($oCache->get($oCacheKey));
		}

	}

	/**
	 * @param Coordinates $destination
	 * @return array
	 */
	protected function generate(Coordinates $destination) {

		$this->getSectors($destination->System);

		$this->tToGo = array ();

		array_push($this->tToGo, clone $destination);

		$this->tRoute[$destination->X][$destination->Y]->value = 0;

		$this->go = true;
		while ( $this->go ) {

			$this->temporaryRoutingCoordinates = array();
			$this->go = false;

			while ($this->current = array_pop ($this->tToGo)) {

				$this->tRoute [$this->current->X] [$this->current->Y]->analyzed = true;

				$this->setSector ( $this->current->X - 1, $this->current->Y );
				$this->setSector ( $this->current->X + 1, $this->current->Y );
				$this->setSector ( $this->current->X, $this->current->Y - 1 );
				$this->setSector ( $this->current->X, $this->current->Y + 1 );
			}

			$this->tToGo = $this->temporaryRoutingCoordinates;
		}

		$tRoute = null;

		for($indexX = 1; $indexX <= $this->systemObject->Width; $indexX ++) {
			for($indexY = 1; $indexY <= $this->systemObject->Height; $indexY ++) {
				$tRoute [$indexX] [$indexY] = $this->tRoute [$indexX] [$indexY]->value;
			}
		}

		return $tRoute;
	}

	public function __construct(Coordinates $destination) {
        $this->load($destination);
	}

}