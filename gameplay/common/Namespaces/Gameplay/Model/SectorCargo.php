<?php

namespace Gameplay\Model;

use Database\Controller;

class SectorCargo {

    /**
     * @var Coordinates
     */
    private $position = null;

    /**
     * @var SectorEntity
     */
    private $sectorProperties = null;

    /**
     * @var \phpCache\Apc
     */
    private $cache = null;
	private $cacheValid = 3600;

	/**
	 * Pobranie właściwości sektora
	 */
	private function loadSector() {
		$this->sectorProperties = new SectorEntity($this->position);
	}

    /**
     * @param Coordinates $position
     */
	public function __construct(Coordinates $position) {
		$this->position = $position;
        $this->cache = \phpCache\Factory::getInstance()->create();
		$this->loadSector();
	}

	/**
	 * Wygenerowanie identyfikatora cache
	 */
	private function getCacheID() {
		return $this->position->System.'|'.$this->position->X.'|'.$this->position->Y;
	}

	private function clearCache($type = 'product') {
		$this->cache->clear(new \phpCache\CacheKey('SectorCargo::sGetList::'.$type, '|'.$this->getCacheID()));
	}

    /**
     * @param string $type
     * @param int $productID
     * @return null|int
     */
    public function getAmount($type, $productID) {

		$out = null;

		//@todo to chyba też jednak cachowane w memcache

		if ($this->sectorProperties->SectorID == null) {
			$tQuery = "SELECT sectorcargo.Amount AS Amount FROM sectorcargo WHERE sectorcargo.CargoID='$productID' AND sectorcargo.Type='$type' AND sectorcargo.System='{$this->position->System}' AND sectorcargo.X='{$this->position->X}' AND sectorcargo.Y='{$this->position->Y}'";
		} else {
			$tQuery = "SELECT sectorcargo.Amount AS Amount FROM sectorcargo WHERE sectorcargo.CargoID='$productID' AND sectorcargo.Type='$type' AND sectorcargo.SectorID='{$this->sectorProperties->SectorID}'";
		}
		$tQuery = Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = Controller::getInstance()->fetch ( $tQuery ) ) {
			$out = $tR1->Amount;
		}

		return $out;
	}

	/**
	 * @param string $type
	 * @return array
	 */
	public function getList($type = 'product') {

		$oCacheKey = new \phpCache\CacheKey('sectorCargo::sGetList::'.$type, '|'.$this->getCacheID());

		if ($this->cache->check($oCacheKey)) {
			$retVal = unserialize($this->cache->get($oCacheKey));
		}else {

			$retVal = array();

            $oDb = Controller::getInstance();

			switch ($type) {

				/*
				 * Pobranie listy towarów handlowych
				 */
				case 'product':

					if ($this->sectorProperties->SectorID == null) {
						$tQuery = "SELECT products.Size AS Size, sectorcargo.Amount AS Amount, products.NamePL, products.NameEN, sectorcargo.CargoID AS CargoID FROM sectorcargo LEFT JOIN products ON products.ProductID=sectorcargo.CargoID WHERE sectorcargo.Type='product' AND sectorcargo.System='{$this->position->System}' AND sectorcargo.X='{$this->position->X}' AND sectorcargo.Y='{$this->position->Y}'";
					} else {
						$tQuery = "SELECT products.Size AS Size, sectorcargo.Amount AS Amount, products.NamePL, products.NameEN, sectorcargo.CargoID AS CargoID FROM sectorcargo LEFT JOIN products ON products.ProductID=sectorcargo.CargoID WHERE sectorcargo.Type='product' AND sectorcargo.SectorID='{$this->sectorProperties->SectorID}'";
					}
					$tQuery = $oDb->execute ( $tQuery );

					while ( $tR1 = $oDb->fetch ( $tQuery ) ) {
						$retVal[] = $tR1;
					}
					break;

					/*
					 * Pobranie listy broni
					 */
				case 'weapon':
					if ($this->sectorProperties->SectorID == null) {
						$tQuery = "SELECT sectorcargo.Amount AS Amount, weapontypes.NamePL, weapontypes.NameEN, sectorcargo.CargoID AS CargoID FROM sectorcargo LEFT JOIN weapontypes ON weapontypes.WeaponID=sectorcargo.CargoID WHERE sectorcargo.Type='weapon' AND sectorcargo.System='{$this->position->System}' AND sectorcargo.X='{$this->position->X}' AND sectorcargo.Y='{$this->position->Y}'";
					} else {
						$tQuery = "SELECT sectorcargo.Amount AS Amount, weapontypes.NamePL, weapontypes.NameEN, sectorcargo.CargoID AS CargoID FROM sectorcargo LEFT JOIN weapontypes ON weapontypes.WeaponID=sectorcargo.CargoID WHERE sectorcargo.Type='weapon' AND sectorcargo.SectorID='{$this->sectorProperties->SectorID}'";
					}
					$tQuery = $oDb->execute ( $tQuery );
					while ( $tR1 = $oDb->fetch ( $tQuery ) ) {
						$retVal[] = $tR1;
					}
					break;

					/*
					 * Pobranie listy wyposażenia
					 */
				case 'equipment':
					if ($this->sectorProperties->SectorID == null) {
						$tQuery = "SELECT sectorcargo.Amount AS Amount, equipmenttypes.NamePL, equipmenttypes.NameEN,  sectorcargo.CargoID AS CargoID FROM sectorcargo LEFT JOIN equipmenttypes ON equipmenttypes.EquipmentID=sectorcargo.CargoID WHERE sectorcargo.Type='equipment' AND sectorcargo.System='{$this->position->System}' AND sectorcargo.X='{$this->position->X}' AND sectorcargo.Y='{$this->position->Y}'";
					} else {
						$tQuery = "SELECT sectorcargo.Amount AS Amount, equipmenttypes.NamePL, equipmenttypes.NameEN, sectorcargo.CargoID AS CargoID FROM sectorcargo LEFT JOIN equipmenttypes ON equipmenttypes.EquipmentID=sectorcargo.CargoID WHERE sectorcargo.Type='equipment' AND sectorcargo.SectorID='{$this->sectorProperties->SectorID}'";
					}
					$tQuery = $oDb->execute ( $tQuery );
					while ( $tR1 = $oDb->fetch ( $tQuery ) ) {
						$retVal[] = $tR1;
					}
					break;

					/*
					 * Pobranie listy itemów
					 */
				case 'item':
					if ($this->sectorProperties->SectorID == null) {
						$tQuery = "SELECT sectorcargo.Amount AS Amount, itemtypes.NamePL, itemtypes.NameEN, sectorcargo.CargoID AS CargoID FROM sectorcargo LEFT JOIN itemtypes ON itemtypes.ItemID=sectorcargo.CargoID WHERE sectorcargo.Type='item' AND sectorcargo.System='{$this->position->System}' AND sectorcargo.X='{$this->position->X}' AND sectorcargo.Y='{$this->position->Y}'";
					} else {
						$tQuery = "SELECT sectorcargo.Amount AS Amount, itemtypes.NamePL, itemtypes.NameEN, sectorcargo.CargoID AS CargoID FROM sectorcargo LEFT JOIN itemtypes ON itemtypes.ItemID=sectorcargo.CargoID WHERE sectorcargo.Type='item' AND sectorcargo.SectorID='{$this->sectorProperties->SectorID}'";
					}
					$tQuery = $oDb->execute ( $tQuery );
					while ( $tR1 = $oDb->fetch ( $tQuery ) ) {
						$retVal[] = $tR1;
					}
					break;

			}
			$this->cache->set($oCacheKey, serialize($retVal), $this->cacheValid);
		}
		return $retVal;
	}

	/**
	 * Wstawienie rzeczy do sektora
	 * @param string $type
	 * @param int $id
	 * @param int $amount
     * @return bool
	 */
	public function insert($type, $id, $amount) {

		$tAmount = $this->getAmount($type, $id);

		if ($tAmount === null) {
			$recordExists = false;
		}else {
			$recordExists = true;
		}

		$newState = $tAmount + $amount;

		if ($this->sectorProperties->Name != 'deepspace') {
			if ($recordExists) {
				$tQuery = "UPDATE sectorcargo SET Amount='$newState' WHERE Type='$type' AND SectorID='{$this->sectorProperties->SectorID}' AND CargoID='$id'";
			} else {
				$tQuery = "INSERT INTO sectorcargo(SectorID, CargoID, Amount, Type) VALUES('{$this->sectorProperties->SectorID}', '$id', '$newState', '$type')";
			}
		} else {
			if ($recordExists) {
				$tQuery = "UPDATE sectorcargo SET Amount='$newState' WHERE Type='$type' AND System='{$this->position->System}' AND X='{$this->position->X}' AND Y='{$this->position->Y}' AND CargoID='$id'";
			} else {
				$tQuery = "INSERT INTO sectorcargo(System, X, Y, CargoID, Amount, Type) VALUES('{$this->position->System}','{$this->position->X}','{$this->position->Y}', '$id', '$newState', '$type')";
			}
		}
		Controller::getInstance()->execute ( $tQuery );

		$this->clearCache($type);

		return true;
	}

	/**
	 * uaktualnienie ilości rzeczy w sektorze
	 * @param string $type
	 * @param int $id
	 * @param int $amount
     * @return bool
	 */
	public function update($type, $id, $amount) {

		$tAmount = $this->getAmount($type, $id);

		if ($tAmount === null) {
			$recordExists = false;
		}else {
			$recordExists = true;
		}

		$newState = $amount;

		if ($this->sectorProperties->Name != 'deepspace') {
			if ($recordExists) {
				$tQuery = "UPDATE sectorcargo SET Amount='$newState' WHERE Type='$type' AND SectorID='{$this->sectorProperties->SectorID}' AND CargoID='$id'";
			} else {
				$tQuery = "INSERT INTO sectorcargo(SectorID, CargoID, Amount, Type) VALUES('{$this->sectorProperties->SectorID}', '$id', '$newState', '$type')";
			}
		} else {
			if ($recordExists) {
				$tQuery = "UPDATE sectorcargo SET Amount='$newState' WHERE Type='$type' AND System='{$this->position->System}' AND X='{$this->position->X}' AND Y='{$this->position->Y}' AND CargoID='$id'";
			} else {
				$tQuery = "INSERT INTO sectorcargo(System, X, Y, CargoID, Amount, Type) VALUES('{$this->position->System}','{$this->position->X}','{$this->position->Y}', '$id', '$newState', '$type')";
			}
		}
		Controller::getInstance()->execute ( $tQuery );

		//@todo ujednolicić insert i update, większość kodu jest identyczna
		//@todo nadmiarowy ruch przy czyszczeniu. Trzeba przemyśleć
		$this->clearCache($type);

		return true;
	}

	/**
	 * usunięcie całego cargo w danym miejscu
	 * @param string $type
     * @return bool
	 */
	public function drop($type) {

		if ($this->sectorProperties->Name != 'deepspace') {
			$tQuery = "DELETE FROM sectorcargo WHERE Type='{$type}' AND SectorID='{$this->sectorProperties->SectorID}'";
		} else {
			$tQuery = "DELETE FROM sectorcargo WHERE Type='{$type}' AND System='{$this->position->System}' AND X='{$this->position->X}' AND Y='{$this->position->Y}'";
		}

		Controller::getInstance()->execute($tQuery);

		$this->clearCache($type);

		return true;
	}

}