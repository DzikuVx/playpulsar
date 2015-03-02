<?php

namespace Gameplay\Model;

class ShipRouting extends Standard {
	protected $tableName = "shiprouting";
	protected $tableID = "UserID";
	protected $tableUseFields = array ('System', 'X', 'Y' );
	protected $cacheExpire = 1800;

    protected $entryIdIsTableId = true;

    public $UserID;
    public $System;
    public $X;
    public $Y;

	/**
	 * Reset coordinates
	 */
	private function reset() {
		$this->System = null;
		$this->X = null;
		$this->Y = null;
	}

	/**
	 * Check if ship arrived to destination sector
	 * @param Coordinates $position
     * @return bool
	 */
	public function checkArrive(Coordinates $position) {
		if ($position->System == $this->System && $position->X == $this->X && $position->Y == $this->Y) {
			$this->reset();
			return true;
		} else {
			return false;
		}
	}

	protected function set() {

		if ($this->System == null) {
			\Database\Controller::getInstance()->execute ( "DELETE FROM shiprouting WHERE UserID = '{$this->dbID}'" );
		} else {
			if (!empty($this->originalData->System)) {
				\Database\Controller::getInstance()->execute ( $this->formatUpdateQuery());
			} else {
				\Database\Controller::getInstance()->execute ( $this->formatInsertQuery());
			}
		}
		return true;
	}

    /**
     * @return Coordinates
     */
    public function getCoordinates() {
        return new Coordinates($this->System, $this->X, $this->Y);
    }

    protected function checkIfChanged() {
        $retVal = false;

        if (empty($this->originalData->System) && (!empty($this->System))) {
            return true;
        }

        foreach ($this->tableUseFields as $tField) {
            if ($this->originalData && property_exists($this->originalData, $tField) && $this->{$tField} != $this->originalData->{$tField}) {
                $retVal = true;
                break;
            }
        }

        return $retVal;
    }

}