<?php

namespace Gameplay\Panel;

use Interfaces\Singleton;
//TODO MiniMap as a function should be independend from MiniMap as Panel. Move Rendering to separate class
class MiniMap extends BaseTable implements Singleton {
	protected $sector;
	protected $system;

	protected $shipPosition;

	protected $X;
	protected $Y;
	protected $panelTag = "MiniMap";
	protected $getShips = false;
	protected $getStacks = false;
	protected $sectorClass = "miniMap";
	protected $useBorder = false;
	protected $onClick = null;

	static private $instance = null;

	/**
	 * @throws \Exception
	 * @return \Gameplay\Panel\ShortStats
	 */
	static public function getInstance() {

		if (empty(self::$instance)) {
			throw new \Exception('Panel not initialized');
		}
		else {
			return self::$instance;
		}

	}

	static public function initiateInstance($userID, $system, $shipPosition = null, $getShips = false, $getStacks = false) {
		self::$instance = new self($userID, $system, $shipPosition, $getShips, $getStacks);
	}

	protected function __construct($userID, $system, $shipPosition = null, $getShips = false, $getStacks = false) {
		$this->load ( $userID, $system, $shipPosition, $getShips, $getStacks );
	}

	protected function renderHeader() {

		$retVal = "<table class='{$this->sectorClass}'>";
		return $retVal;
	}

	function load($userID, $system, $shipPosition = null, $getShips = false, $getStacks = false) {
		$this->shipPosition = $shipPosition;
		if (is_numeric ( $system )) {
			$this->system = \systemProperties::quickLoad ( $system );
		} else {
			$this->system = $system;
		}
		$this->userID = $userID;
		$this->getShips = $getShips;
		$this->getStacks = $getStacks;
		return true;
	}

	/**
	 * @return \Gameplay\Panel\MiniMap
	 */
	public function render() {
		$this->rendered = true;

		if ($this->shipPosition == null) {
			return false;
		}

		$this->retVal = '';

		$this->retVal .= $this->renderHeader ();

		if (! $this->checkAvaible ()) {
			$this->retVal .= '{T:nomapavaible}';
			$this->retVal .= $this->renderFooter ();
			return true;
		}

		$this->getLimits ();
		$this->getSectors ();
		if ($this->getShips) {
			$this->getShips();
		}
		if ($this->getStacks) {
			$this->getStacks();
		}

		$this->retVal .= $this->renderSetors ();

		$this->retVal .= $this->renderFooter ();

		return $this;
	}

	public function checkAvaible() {

		if ($this->system->MapAvaible == "yes") {
			return true;
		}

		$mapAvaible = false;
		$tQuery = "SELECT UserID FROM usermaps WHERE UserID='{$this->userID}' AND SystemID='{$this->system->SystemID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$mapAvaible = true;
		}
		return $mapAvaible;
	}

	protected function getLimits() {
		$this->X ['start'] = $this->shipPosition->X - 2;
		$this->X ['stop'] = $this->shipPosition->X + 2;
		$this->Y ['start'] = $this->shipPosition->Y - 2;
		$this->Y ['stop'] = $this->shipPosition->Y + 2;
	}

	protected function getCacheModule() {
		return get_class($this).'::getSectors';
	}

	protected function getCacheProperty() {
		return $this->shipPosition->System.'|'.$this->shipPosition->X.'|'.$this->shipPosition->Y;
	}

	protected function getSectors() {

		global $t;

		//FIXME change to class's global cache key
// 		$module = $this->getCacheModule();
// 		$property = $this->getCacheProperty();

		$oCacheKey = new \Cache\CacheKey($this->getCacheModule(), $this->getCacheProperty());

		if (\Cache\Controller::getInstance()->check($oCacheKey)) {
			$this->sector = unserialize(\Cache\Controller::getInstance()->get($oCacheKey));
		}else {

			/**
			 * Zaincjuj tablicę sektorów
			 */
			for($indexX = $this->X ['start']; $indexX <= $this->X ['stop']; $indexX ++) {
				for($indexY = $this->Y ['start']; $indexY <= $this->Y ['stop']; $indexY ++) {
					$this->sector [$indexX] [$indexY] = new \Gameplay\Helpers\MapSector();
					$this->sector [$indexX] [$indexY]->system = $this->system->SystemID;
					$this->sector [$indexX] [$indexY]->X = $indexX;
					$this->sector [$indexX] [$indexY]->Y = $indexY;
					$this->sector [$indexX] [$indexY]->onClick = $this->onClick;
				}
			}

			/**
			 * Znajdź kolory sektorów
			 */
			$tQuery = "SELECT
        sectortypes.Color AS Color,
        sectortypes.Image,
        sectortypes.Name,
        sectortypes.Visibility,
        sectors.X AS X,
        sectors.Y AS Y
      FROM
        sectors JOIN sectortypes ON sectortypes.SectorTypeID = sectors.SectorTypeID
      WHERE
        sectors.System = '{$this->system->SystemID}' AND
        sectors.X >= '{$this->X['start']}' AND
        sectors.X <= '{$this->X['stop']}' AND
        sectors.Y >= '{$this->Y['start']}' AND
        sectors.Y <= '{$this->Y['stop']}'
      ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				$this->sector [$tR1->X] [$tR1->Y]->bgColor = $tR1->Color;
				$this->sector [$tR1->X] [$tR1->Y]->visibility = $tR1->Visibility;
				$this->sector [$tR1->X] [$tR1->Y]->Name = \TranslateController::getDefault()->get ($tR1->Name);

				if (!empty($tR1->Image)) {
					$tArray = explode('/', $tR1->Image);
					$this->sector [$tR1->X] [$tR1->Y]->gfx = $tArray[count($tArray)-1];
				}

			}

			/**
			 * Znajdź porty
			 */
			$tQuery = "SELECT
        ports.State AS State,
        ports.X AS X,
        ports.Y AS Y,
        porttypes.Type AS Type
      FROM
        ports JOIN porttypes ON porttypes.PortTypeID = ports.PortTypeID
      WHERE
        ports.System = '{$this->system->SystemID}' AND
        ports.X >= '{$this->X['start']}' AND
        ports.X <= '{$this->X['stop']}' AND
        ports.Y >= '{$this->Y['start']}' AND
        ports.Y <= '{$this->Y['stop']}'
      ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				if ($tR1->Type == "port")
				$this->sector [$tR1->X] [$tR1->Y]->icon = "P";
				if ($tR1->Type == "station")
				$this->sector [$tR1->X] [$tR1->Y]->icon = "S";
				if ($tR1->State != "normal")
				$this->sector [$tR1->X] [$tR1->Y]->iconColor = "f00000";
			}

			//Znajdz Jump Node
			$tQuery = "SELECT
        *
      FROM
        nodes
      WHERE
        nodes.Active = 'yes' AND
        (
        (nodes.SrcSystem = '{$this->system->SystemID}' AND
        nodes.SrcX >= '{$this->X['start']}' AND
        nodes.SrcX <= '{$this->X['stop']}' AND
        nodes.SrcY >= '{$this->Y['start']}' AND
        nodes.SrcY <= '{$this->Y['stop']}'
        )
        OR
        (nodes.DstSystem = '{$this->system->SystemID}' AND
        nodes.DstX >= '{$this->X['start']}' AND
        nodes.DstX <= '{$this->X['stop']}' AND
        nodes.DstY >= '{$this->Y['start']}' AND
        nodes.DstY <= '{$this->Y['stop']}'
        )
        )";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				if ($tR1->SrcSystem == $this->system->SystemID) {
					$this->sector [$tR1->SrcX] [$tR1->SrcY]->icon = "N";
				} else {
					$this->sector [$tR1->DstX] [$tR1->DstY]->icon = "N";
				}
			}

			/*
			 * Zapisz do cache
			*/
			\Cache\Controller::getInstance()->set($oCacheKey, serialize($this->sector), 86400);

		}
	}

	protected function openRow() {
		return "<tr>";
	}

	protected function closeRow() {
		return "</tr>";
	}

	protected function renderSetors() {
		$retVal = "";
		for($indexY = $this->Y ['start']; $indexY <= $this->Y ['stop']; $indexY ++) {
			$retVal .= $this->openRow ();
			for($indexX = $this->X ['start']; $indexX <= $this->X ['stop']; $indexX ++) {

				if ($this->useBorder && $this->shipPosition->System == $this->system->SystemID && $this->shipPosition->X == $indexX && $this->shipPosition->Y == $indexY) {
					$this->sector [$indexX] [$indexY]->border = true;
				}

				$retVal .= $this->sector [$indexX] [$indexY]->render (get_class($this));
			}
			$retVal .= $this->closeRow ();
		}
		return $retVal;
	}

	protected function getShips() {

		global $shipProperties, $userID;

		if ($this->shipPosition->Docked != 'no') {
			return;
		}

		$tQuery = "SELECT
				sp.UserID,
				us.Cloak,
				sp.X,
				sp.Y
			FROM
				shippositions AS sp JOIN userships AS us USING(UserID)
			WHERE
				sp.System = '{$this->system->SystemID}' AND
        sp.X >= '{$this->X['start']}' AND
        sp.X <= '{$this->X['stop']}' AND
        sp.Y >= '{$this->Y['start']}' AND
        sp.Y <= '{$this->Y['stop']}' AND
        sp.Docked='no'
		";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		while ($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {
			if ($userID == $tResult->UserID) {
				continue;
			}

			$this->sector [$tResult->X] [$tResult->Y]->shipCount += $shipProperties->Scan - $tResult->Cloak + $this->sector [$tResult->X] [$tResult->Y]->visibility;
		}

		for($indexY = $this->Y ['start']; $indexY <= $this->Y ['stop']; $indexY ++) {
			for($indexX = $this->X ['start']; $indexX <= $this->X ['stop']; $indexX ++) {

				$tA = $indexX - $this->shipPosition->X;
				$tB = $indexY - $this->shipPosition->Y;
				$tRange = sqrt(($tA*$tA)+($tB*$tB))+0.1;
				$obj = $this->sector [$indexX] [$indexY];
				$obj->showPercentage = floor($obj->shipCount/($tRange*2));

				if ($obj->showPercentage > 99) {
					$obj->showPercentage = 99;
				}

				if ($obj->showPercentage < 1) {
					$obj->showPercentage = 1;
				}

			}
		}

	}

	protected function getStacks() {

	}
}
