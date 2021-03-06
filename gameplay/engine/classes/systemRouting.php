<?php

/**
 * Klasa routingu wewnątrz systemu
 *
 * @version $Rev: 453 $
 * @package Engine
 */
class systemRouting {
	protected $routeTable = null;
	protected $destination = null;
	protected $systemObject = null;
	protected $go = false;
	protected $tArray;
	protected $tToGo;
	protected $tRoute;
	protected $current;

	/**
	 * Czas przechowywania cache
	 * @var int
	 */
	protected $cacheTime = 604800;

	protected function nextPush($tx, $ty, $direction) {

		if ($tx > 0 && $tx <= $this->systemObject->Width && $ty > 0 && $ty <= $this->systemObject->Height) {
			$object = new routingCoords ( $tx, $ty, $this->routeTable [$tx] [$ty], $direction );
			array_push ( $this->tArray, $object );
		}
		return true;
	}

	/**
	 * Pobranie następnej pozycji przy routingu
	 *
	 * @param stdClass $current
	 * @return stdClass
	 */
	public function next($current) {

		$retVal = null;

		if ($this->routeTable == null)
		return false;

		/*
		 * Wrzuć do tablicy sąsiednie sektory z tablicy routingu
		 */
		$this->tArray = array ();

		$this->nextPush ( $current->X, $current->Y - 1, 'up' );
		$this->nextPush ( $current->X, $current->Y + 1, 'down' );
		$this->nextPush ( $current->X - 1, $current->Y, 'left' );
		$this->nextPush ( $current->X + 1, $current->Y, 'right' );

		/*
		 * Dokonaj sortowania
		 */
		usort ( $this->tArray, "routingSort" );

		/*
		 * Pobierz pierwszą wartość:
		 */

		$tClass = array_pop ( $this->tArray );

		$retVal->X = $tClass->X;
		$retVal->Y = $tClass->Y;
		$retVal->direction = $tClass->direction;

		unset ( $this->tArray );
		unset ( $tClass );

		return $retVal;
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
	 * Pobranie pierwszej składowej identyfikatiora cache
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
	 * Usunięcie wpisu z pamięci podręcznej
	 *
	 * @return boolean
	 */
	protected function delete() {

		\Cache\Controller::getInstance()->clear($this->getCacheModule(), $this->getCacheProperty());

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

		\Cache\Controller::getInstance()->set($this->getCacheModule(), $this->getCacheProperty(), serialize ( $this->routeTable ), $this->cacheTime);

		return true;
	}

	/**
	 * Pobranie tablicy routingu z bazy danych
	 *
	 */
	protected function get() {

		$this->routeTable = null;

		if (\Cache\Controller::getInstance()->check($this->getCacheModule(), $this->getCacheProperty())) {
			$this->routeTable = unserialize (\Cache\Controller::getInstance()->get($this->getCacheModule(), $this->getCacheProperty()));
		}
	}

	/**
	 * Załadowanie tablicy routingu dla system
	 *
	 * @param stdClass $destination
	 */
	public function load($destination) {

		$this->systemObject = systemProperties::quickLoad ( $destination->System );

		$this->destination = $destination;

		/*
		 * Pobierz tablicę routingu z bazy danych
		 */
		$this->get ();

		/**
		 * Jeśli w bazie nie ma tablicy, wygenruj ją i zapisz
		 */
		if ($this->routeTable == null) {
			$this->routeTable = $this->generate ( $this->destination );
			$this->put ();
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

		if ($tx > 0 && $tx <= $this->systemObject->Width && $ty > 0 && $ty <= $this->systemObject->Height && $this->tRoute [$tx] [$ty]->analized == false) {
			$tObject = new simpleCoords ( $tx, $ty );
			if (! in_array ( $tObject, $this->tArray )) {
				array_push ( $this->tArray, $tObject );
				$this->go = true;
			} else {
				unset($tObject);
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

		$module = 'systemRouting::getSectors';
		$property = $systemID;

		if (!\Cache\Controller::getInstance()->check($module, $property)) {
			$this->tRoute = null;

			/*
			 * Inicjuj
			 */
			for($indexX = 1; $indexX <= $this->systemObject->Width; $indexX ++) {
				for($indexY = 1; $indexY <= $this->systemObject->Height; $indexY ++) {
					$this->tRoute [$indexX] [$indexY] = new routingSector ( );
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
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				$this->tRoute [$tR1->X] [$tR1->Y]->cost = $tR1->MoveCost;
			}

			\Cache\Controller::getInstance()->set($module, $property, serialize($this->tRoute), $this->cacheTime);

		}else {
			$this->tRoute = unserialize(\Cache\Controller::getInstance()->get($module, $property));
		}

	}

	/**
	 * Wygenerowanie tablicy routingu
	 *
	 * @param stdClass $destination
	 * @return array
	 */
	protected function generate($destination) {

		$this->getSectors($destination->System);

		$this->tToGo = array ();

		array_push ( $this->tToGo, new simpleCoords ( $destination->X, $destination->Y ) );

		$this->tRoute [$destination->X] [$destination->Y]->value = 0;

		$this->go = true;
		//Póki tablica jest wypełniona
		while ( $this->go ) {

			$this->tArray = array ();
			$this->go = false;
			//Zacznij pobierać sektory z tablicy
			while ( $this->current = array_pop ( $this->tToGo ) ) {

				$this->tRoute [$this->current->X] [$this->current->Y]->analized = true;

				$this->setSector ( $this->current->X - 1, $this->current->Y );
				$this->setSector ( $this->current->X + 1, $this->current->Y );
				$this->setSector ( $this->current->X, $this->current->Y - 1 );
				$this->setSector ( $this->current->X, $this->current->Y + 1 );

				unset($this->current);
			}

			$this->tToGo = $this->tArray;
			unset ( $this->tArray );
		}

		$tRoute = null;

		for($indexX = 1; $indexX <= $this->systemObject->Width; $indexX ++) {
			for($indexY = 1; $indexY <= $this->systemObject->Height; $indexY ++) {
				$tRoute [$indexX] [$indexY] = $this->tRoute [$indexX] [$indexY]->value;
				unset ( $this->tRoute [$indexX] [$indexY] );
			}
		}

		unset ( $this->tRoute );

		return $tRoute;
	}

	/**
	 * Konstruktor
	 *
	 * @param dataBase $db
	 * @param stdClass $destination
	 */
	function __construct($db, $destination = null) {

		//@todo usunąć db z konstruktora
		if (!empty($destination)) {
			$this->load ( $destination );
		}

	}

}