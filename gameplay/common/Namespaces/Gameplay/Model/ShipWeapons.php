<?php

namespace Gameplay\Model;

use Database\MySQLiWrapper;

class ShipWeapons {
	protected $userID = null;
	protected $language = 'pl';
	protected $nameField = "";
	protected $tableName = "shipweapons";
	protected $addCondition = "";
	protected $changed = false;

    /**
     * @var MySQLiWrapper
     */
    protected $db;

    /**
     * @param $userID
     * @param string $language
     * @param MySQLiWrapper $db
     */
    function __construct($userID, $language = 'pl', MySQLiWrapper $db = null) {
        $this->language = $language;
        $this->userID = $userID;
        $this->nameField = "Name" . strtoupper($this->language);

        if (!empty($db)) {
            $this->db = $db;
        } else {
            $this->db = \Database\Controller::getInstance();
        }

    }

    //FIXME replace with dynamic
	static public function sGetDamagedCount($userID) {

		$tQuery = "SELECT COUNT(*) AS ILE FROM shipweapons WHERE UserID='{$userID}' AND Enabled IS NOT NULL AND Damaged='1'";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		return \Database\Controller::getInstance()->fetch($tQuery)->ILE;

	}

	/**
	 * @return boolean
	 */
	public function damageRandom() {

		$tQuery = "UPDATE shipweapons SET Damaged='1' WHERE UserID='{$this->userID}' AND Damaged='0' ORDER BY Rand() LIMIT 1";
		$this->db->execute($tQuery);

		if ($this->db->getAffectedRows() == 0) {
			return false;
		} else {
			return true;
		}

	}

	/**
	 * Przeliczenie OffRating Statku
	 *
	 * @param \Gameplay\Model\ShipProperties $shipProperties
	 */
	public function computeOffensiveRating(\Gameplay\Model\ShipProperties $shipProperties) {

		$tQuery = "SELECT
		    SUM(
		    (wt.ShieldMax +
		    wt.ShieldMin +
		    wt.ArmorMin +
		    wt.ArmorMax) / 8
		    ) AS ILE
		  FROM
		    shipweapons AS sw JOIN weapontypes AS wt ON wt.WeaponID = sw.WeaponID
		  WHERE
		    UserID = '{$this->userID}'
		";

		$tQuery = $this->db->execute ( $tQuery );
		while ( $resultRow = $this->db->fetch ( $tQuery ) ) {
			$shipProperties->OffRating = round ( $resultRow->ILE );
		}
	}

	public function __destruct() {
		global $shipProperties;
		if ($this->changed) {
			$this->computeOffensiveRating($shipProperties);
		}
	}

	/**
	 * Pobranie najwyższego sequence uzbrojenia statku
	 *
	 * @return int
	 */
	private function getMaxSequence() {

		$tQuery = "SELECT MAX(Sequence) AS ILE FROM shipweapons WHERE UserID='{$this->userID}'";
		$tQuery = $this->db->execute ( $tQuery );
		$retVal = $this->db->fetch ( $tQuery )->ILE;

		if (empty($retVal)) {
			$retVal = 0;
		}

		return $retVal;
	}

	/**
	 * @param WeaponType $weapon
	 * @param ShipProperties $shipProperties
	 * @return boolean
	 */
	public function insert(WeaponType $weapon, ShipProperties $shipProperties) {

		if ($shipProperties->CurrentWeapons >= $shipProperties->MaxWeapons) {
		    return false;
        }

		if ($weapon->Ammo == null) {
			$tString = "null";
		} else {
			$tString = "'" . $weapon->Ammo . "'";
		}

		$tSequence = $this->getMaxSequence() + 1;

		$tQuery = "INSERT INTO shipweapons(UserID, WeaponID, Ammo, Sequence) VALUES('{$this->userID}','{$weapon->WeaponID}',$tString,'{$tSequence}')";
		$this->db->execute ( $tQuery );
		$shipProperties->CurrentWeapons += 1;

		$this->changed = true;

		return true;
	}

	/**
	 * Wstawienie amunicji do broni
	 *
	 * @param int $shipWeaponID
	 * @param int $ammo
	 * @return boolean
	 */
	public function reload($shipWeaponID, $ammo) {

		$tQuery = "UPDATE shipweapons SET Ammo='$ammo' WHERE ShipWeaponID='{$shipWeaponID}' AND UserID='{$this->userID}' LIMIT 1";
		$this->db->execute ( $tQuery );

		$this->changed = true;

		return true;
	}

    /**
     * @param int $ID
     * @param ShipProperties $shipProperties
     * @return bool
     */
    public function remove($ID, ShipProperties $shipProperties) {

		$tQuery = "DELETE FROM
                shipweapons
            WHERE
                ShipWeaponID='{$ID}' AND UserID='$this->userID}'";
		$this->db->execute($tQuery);

		$shipProperties->CurrentWeapons -= 1;
		$this->changed = true;
		return true;
	}

    /**
     * @param ShipProperties $shipProperties
     * @return bool
     */
    public function removeAll(ShipProperties $shipProperties) {

		$tQuery = "DELETE FROM shipweapons WHERE UserID='{$this->userID}'";
		$this->db->execute($tQuery);
		$shipProperties->CurrentWeapons = 0;
		$this->changed = true;
		return true;
	}

	/**
	 * Uszkodzenie całego uzbrojenia gracza
	 *
	 * @return boolean
	 */
	public function damageAll() {

		$tQuery = "UPDATE shipweapons SET Damaged='1' WHERE UserID='{$this->userID}'";
		$this->db->execute($tQuery);
		$this->changed = true;
		return true;
	}

	/**
	 * Wyłączenie uzbrojenia
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function disable($ID) {

		$tQuery = "UPDATE shipweapons SET Enabled = '0' WHERE ShipWeaponID='{$ID}' LIMIT 1";
		$this->db->execute($tQuery);
		$this->changed = true;
		return true;
	}

	/**
	 * Włączenie uzbrojenia
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function enable($ID) {

		$tQuery = "UPDATE shipweapons SET Enabled = '1' WHERE ShipWeaponID='{$ID}' LIMIT 1";
		$this->db->execute($tQuery);
		$this->changed = true;
		return true;
	}

	/**
	 * Uszkodzenie uzbrojenia
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function damage($ID) {

		$tQuery = "UPDATE shipweapons SET Damaged = '1' WHERE ShipWeaponID='{$ID}' LIMIT 1";
		$this->db->execute ( $tQuery );
		$this->changed = true;
		return true;
	}

	/**
	 * Naprawienie uzbrojenia
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function repair($ID) {

		$tQuery = "UPDATE shipweapons SET Damaged = '0' WHERE ShipWeaponID='{$ID}' LIMIT 1";
		$this->db->execute ( $tQuery );
		$this->changed = true;
		return true;
	}

	public function repairAll() {
		$tQuery = "UPDATE shipweapons SET Damaged = '0' WHERE UserID='{$this->userID}'";
		$this->db->execute ( $tQuery );
		$this->changed = true;
		return true;
	}

    /**
     * @param int $ID
     * @return bool
     * @throws \securityException
     */
    public function switchState($ID) {

		if ($this->userID == null) {
		    throw new \securityException();
        }

		$tId = null;
		$tEnabled = null;
		$tQuery = "SELECT UserID, Enabled FROM shipweapons WHERE ShipWeaponID='$ID'";
		$tQuery = $this->db->execute($tQuery);
		while($tR1 = $this->db->fetch($tQuery)) {
			$tId = $tR1->UserID;
			$tEnabled = $tR1->Enabled;
		}

		if ($tId != $this->userID) {
		    throw new \securityException();
        }

        $this->changed = true;

        if ($tEnabled == '0') {
            $tNewState = '1';
        } else {
            $tNewState = '0';
        }

        $tQuery = "UPDATE shipweapons SET Enabled='{$tNewState}' WHERE ShipWeaponID={$ID}";
        $this->db->execute ( $tQuery );
        return true;
	}

	/**
	 * Get number o fully operational weapons
	 * return int
	 */
	public function getOperationalCount() {
		$tQuery = "SELECT COUNT(*) AS ILE FROM shipweapons WHERE UserID='{$this->userID}' AND Enabled='1' AND Damaged='0' AND (Ammo IS NULL OR Ammo > 0)";
		$tQuery = $this->db->execute($tQuery);
		return $this->db->fetch($tQuery)->ILE;
	}

	/**
	 * @param string $mode
	 * @param int $ID
	 * @return resource
	 */
	function get($mode = "enabled", $ID = null) {

		if ($ID == null) {
		    $ID = $this->userID;
        }

		switch ($mode) {
			case "all" :
				$addQuery = "";
				break;

			case "fireable" :
				$addQuery = "AND shipweapons.Enabled = '1' AND shipweapons.Damaged = '0'";
				break;

			default :
			case "enabled" :
				$addQuery = "AND shipweapons.Enabled = '1'";
				break;
		}

		$tQuery = "SELECT
                weapontypes.*,
                weapontypes.{$this->nameField} AS Name,
                weapontypes.Ammo AS MaxAmmo,
                shipweapons.Ammo AS Ammo,
                shipweapons.Enabled AS Enabled,
                shipweapons.Sequence AS Sequence,
                shipweapons.ShipWeaponID,
                shipweapons.Damaged
            FROM
                shipweapons LEFT JOIN weapontypes ON weapontypes.WeaponID = shipweapons.WeaponID
            WHERE
                shipweapons.UserID='{$ID}' {$addQuery}
            ORDER BY
                shipweapons.Sequence ASC";
		return $this->db->execute($tQuery);
	}

	/**
	 * Pobranie pojedynczej broni na podstawie jej ID
	 *
	 * @param int $weaponID
	 * @return \stdClass
	 */
	public function getSingle($weaponID) {
        //FIXME add new WeaponEntity class
		$retVal = null;

		$tQuery = "SELECT
            weapontypes.*,
            weapontypes.{$this->nameField} AS Name,
            weapontypes.Ammo AS MaxAmmo,
            shipweapons.Ammo AS Ammo,
            shipweapons.Enabled AS Enabled,
            shipweapons.Sequence AS Sequence,
            shipweapons.ShipWeaponID,
            shipweapons.Damaged
          FROM
            shipweapons LEFT JOIN weapontypes ON weapontypes.WeaponID = shipweapons.WeaponID
          WHERE
            shipweapons.ShipWeaponID='{$weaponID}' AND
            shipweapons.UserID='{$this->userID}'
          LIMIT 1";
		$tQuery = $this->db->execute ( $tQuery );
		while ($tResult = $this->db->fetch ( $tQuery ) ) {
			$retVal = $tResult;
		}
		return $retVal;
	}

	/**
	 * Sprawdzenie, czy statek posiada broń o takim ID
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function checkExists($ID) {

		$retVal = false;

		$tQuery = "SELECT * FROM shipweapons WHERE UserID='{$this->userID}' AND ShipWeaponID='{$ID}' LIMIT 1";
		$tQuery = $this->db->execute ( $tQuery );
		while ( $tResult = $this->db->fetch ( $tQuery ) ) {
			if (! empty ( $tResult )) {
				$retVal = true;
			}
		}

		return $retVal;
	}

	/**
	 * Pobranie broni o mniejszym sequence
	 *
	 * @param int $currentSequence
	 * @return \stdClass
	 */
	public function getPrevSequence($currentSequence) {

        $retVal = new \stdClass();
		$retVal->Sequence = null;
		$retVal->ShipWeaponID = null;

		$tQuery = "SELECT ShipWeaponID, Sequence FROM shipweapons WHERE UserID='{$this->userID}' AND Sequence<'{$currentSequence}' ORDER BY Sequence DESC LIMIT 1";
		$tQuery = $this->db->execute ( $tQuery );
		while ( $tResult = $this->db->fetch ( $tQuery ) ) {
			$retVal->ShipWeaponID = $tResult->ShipWeaponID;
			$retVal->Sequence = $tResult->Sequence;
		}

		return $retVal;
	}

	/**
	 * Pobranie broni o większym sequence
	 *
	 * @param int $currentSequence
	 * @return \stdClass
	 */
	public function getNextSequence($currentSequence) {

        $retVal = new \stdClass();
		$retVal->Sequence = null;
		$retVal->ShipWeaponID = null;

		$tQuery = "SELECT ShipWeaponID, Sequence FROM shipweapons WHERE UserID='{$this->userID}' AND Sequence>'{$currentSequence}' ORDER BY Sequence ASC LIMIT 1";
		$tQuery = $this->db->execute ( $tQuery );
		while ( $tResult = $this->db->fetch ( $tQuery ) ) {
			$retVal->ShipWeaponID = $tResult->ShipWeaponID;
			$retVal->Sequence = $tResult->Sequence;
		}

		return $retVal;
	}

	/**
	 * @param int $shipWeaponID
	 * @param int $sequence
	 */
	public function setSequence($shipWeaponID, $sequence) {
		$tQuery = "UPDATE shipweapons SET Sequence='{$sequence}' WHERE ShipWeaponID='{$shipWeaponID}' AND UserID='{$this->userID}'";
        $this->db->execute ( $tQuery );
	}
}