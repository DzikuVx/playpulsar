<?php

namespace Gameplay\Model;

use Database\MySQLiWrapper;

class ShipWeapons {
    /**
     * identifier of entity (owner) of weapons
     * @var int
     */
    protected $entityId = null;
	protected $language = 'pl';
	protected $nameField = "";

    /**
     * @var string
     */
    protected $tableName = "shipweapons";

    /**
     * name of table field determining entity owner
     * @var string
     */
    protected $ownerIdFieldName = 'UserID';

    protected $entityIdFieldName = 'ShipWeaponID';

    /**
     * @var bool
     */
    protected $changed = false;

    /**
     * @var MySQLiWrapper
     */
    protected $db;

    /**
     * @param $entityId
     * @param string $language
     * @param MySQLiWrapper $db
     */
    public function __construct($entityId, $language = 'pl', MySQLiWrapper $db = null) {
        $this->language = $language;
        $this->entityId = $entityId;
        $this->nameField = "Name" . strtoupper($this->language);

        if (!empty($db)) {
            $this->db = $db;
        } else {
            $this->db = \Database\Controller::getInstance();
        }
    }

    /**
     * Returns number of damaged weapons
     * @return mixed
     */
    public function getDamagedCount() {
		$tQuery = "SELECT COUNT(*) AS ILE FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND Enabled IS NOT NULL AND Damaged='1'";
		$tQuery = $this->db->execute($tQuery);
		return $this->db->fetch($tQuery)->ILE;
	}

	/**
	 * @return boolean
	 */
	public function damageRandom() {

		$tQuery = "UPDATE {$this->tableName} SET Damaged='1' WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND Damaged='0' ORDER BY Rand() LIMIT 1";
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
	 * @param ShipProperties $shipProperties
	 */
	public function computeOffensiveRating(ShipProperties $shipProperties) {

		$tQuery = "SELECT
		    SUM(
		    (wt.ShieldMax +
		    wt.ShieldMin +
		    wt.ArmorMin +
		    wt.ArmorMax) / 8
		    ) AS ILE
		  FROM
		    {$this->tableName} AS sw JOIN weapontypes AS wt ON wt.WeaponID = sw.WeaponID
		  WHERE
		    {$this->ownerIdFieldName} = '{$this->entityId}'";
		$tQuery = $this->db->execute ( $tQuery );
		while ( $resultRow = $this->db->fetch ( $tQuery ) ) {
			$shipProperties->OffRating = round ( $resultRow->ILE );
		}
	}

	public function synchronize() {
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

		$tQuery = "SELECT MAX(Sequence) AS ILE FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}'";
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

		$tQuery = "INSERT INTO {$this->tableName}({$this->ownerIdFieldName}, WeaponID, Ammo, Sequence) VALUES('{$this->entityId}','{$weapon->WeaponID}',$tString,'{$tSequence}')";
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

		$tQuery = "UPDATE {$this->tableName} SET Ammo='$ammo' WHERE {$this->entityIdFieldName}='{$shipWeaponID}' AND {$this->ownerIdFieldName}='{$this->entityId}' LIMIT 1";
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

		$tQuery = "DELETE FROM {$this->tableName} WHERE {$this->entityIdFieldName}='{$ID}' AND {$this->ownerIdFieldName}='$this->entityId}'";
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

		$tQuery = "DELETE FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}'";
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

		$tQuery = "UPDATE {$this->tableName} SET Damaged='1' WHERE {$this->ownerIdFieldName}='{$this->entityId}'";
		$this->db->execute($tQuery);
		$this->changed = true;
		return true;
	}

	/**
	 * @param int $ID
	 * @return boolean
	 */
	public function disable($ID) {
		$tQuery = "UPDATE {$this->tableName} SET Enabled = '0' WHERE {$this->entityIdFieldName}='{$ID}' LIMIT 1";
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

		$tQuery = "UPDATE {$this->tableName} SET Enabled = '1' WHERE {$this->entityIdFieldName}='{$ID}' LIMIT 1";
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

		$tQuery = "UPDATE {$this->tableName} SET Damaged = '1' WHERE {$this->entityIdFieldName}='{$ID}' LIMIT 1";
		$this->db->execute($tQuery);
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

		$tQuery = "UPDATE {$this->tableName} SET Damaged = '0' WHERE {$this->entityIdFieldName}='{$ID}' LIMIT 1";
		$this->db->execute ( $tQuery );
		$this->changed = true;
		return true;
	}

	public function repairAll() {
		$tQuery = "UPDATE {$this->tableName} SET Damaged = '0' WHERE {$this->ownerIdFieldName}='{$this->entityId}'";
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

		if ($this->entityId == null) {
		    throw new \securityException();
        }

		$tId = null;
		$tEnabled = null;
		$tQuery = "SELECT {$this->ownerIdFieldName}, Enabled FROM {$this->tableName} WHERE {$this->entityIdFieldName}='$ID'";
		$tQuery = $this->db->execute($tQuery);
		while($tR1 = $this->db->fetch($tQuery)) {
			$tId = $tR1->{$this->ownerIdFieldName};
			$tEnabled = $tR1->Enabled;
		}

		if ($tId != $this->entityId) {
		    throw new \securityException();
        }

        $this->changed = true;

        if ($tEnabled == '0') {
            $tNewState = '1';
        } else {
            $tNewState = '0';
        }

        $tQuery = "UPDATE {$this->tableName} SET Enabled='{$tNewState}' WHERE {$this->entityIdFieldName}={$ID}";
        $this->db->execute ( $tQuery );
        return true;
	}

	/**
	 * Get number o fully operational weapons
	 * return int
	 */
	public function getOperationalCount() {
		$tQuery = "SELECT COUNT(*) AS ILE FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND Enabled='1' AND Damaged='0' AND (Ammo IS NULL OR Ammo > 0)";
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
		    $ID = $this->entityId;
        }

		switch ($mode) {
			case "all" :
				$addQuery = "";
				break;

			case "fireable" :
				$addQuery = "AND {$this->tableName}.Enabled = '1' AND {$this->tableName}.Damaged = '0'";
				break;

			default :
			case "enabled" :
				$addQuery = "AND {$this->tableName}.Enabled = '1'";
				break;
		}

		$tQuery = "SELECT
                weapontypes.*,
                weapontypes.{$this->nameField} AS Name,
                weapontypes.Ammo AS MaxAmmo,
                {$this->tableName}.Ammo AS Ammo,
                {$this->tableName}.Enabled AS Enabled,
                {$this->tableName}.Sequence AS Sequence,
                {$this->tableName}.{$this->entityIdFieldName},
                {$this->tableName}.Damaged
            FROM
                {$this->tableName} LEFT JOIN weapontypes ON weapontypes.WeaponID = {$this->tableName}.WeaponID
            WHERE
                {$this->tableName}.{$this->ownerIdFieldName}='{$ID}' {$addQuery}
            ORDER BY
                {$this->tableName}.Sequence ASC";
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
            {$this->tableName}.Ammo AS Ammo,
            {$this->tableName}.Enabled AS Enabled,
            {$this->tableName}.Sequence AS Sequence,
            {$this->tableName}.{$this->entityIdFieldName},
            {$this->tableName}.Damaged
          FROM
            {$this->tableName} LEFT JOIN weapontypes ON weapontypes.WeaponID = shipweapons.WeaponID
          WHERE
            {$this->tableName}.{$this->entityIdFieldName}='{$weaponID}' AND
            {$this->tableName}.{$this->ownerIdFieldName}='{$this->entityId}'
          LIMIT 1";
		$tQuery = $this->db->execute ( $tQuery );
		while ($tResult = $this->db->fetch ( $tQuery ) ) {
			$retVal = $tResult;
		}
		return $retVal;
	}

	/**
	 *
	 * @param int $ID
	 * @return boolean
	 */
	public function checkExists($ID) {

		$retVal = false;

		$tQuery = "SELECT * FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND {$this->entityIdFieldName}='{$ID}' LIMIT 1";
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
		$retVal->{$this->entityIdFieldName} = null;

		$tQuery = "SELECT {$this->entityIdFieldName}, Sequence FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND Sequence<'{$currentSequence}' ORDER BY Sequence DESC LIMIT 1";
		$tQuery = $this->db->execute ( $tQuery );
		while ( $tResult = $this->db->fetch ( $tQuery ) ) {
			$retVal->{$this->entityIdFieldName} = $tResult->{$this->entityIdFieldName};
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
		$retVal->{$this->entityIdFieldName} = null;

		$tQuery = "SELECT {$this->entityIdFieldName}, Sequence FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND Sequence>'{$currentSequence}' ORDER BY Sequence ASC LIMIT 1";
		$tQuery = $this->db->execute ( $tQuery );
		while ( $tResult = $this->db->fetch ( $tQuery ) ) {
			$retVal->{$this->entityIdFieldName} = $tResult->{$this->entityIdFieldName};
			$retVal->Sequence = $tResult->Sequence;
		}

		return $retVal;
	}

	/**
	 * @param int $shipWeaponID
	 * @param int $sequence
	 */
	public function setSequence($shipWeaponID, $sequence) {
		$tQuery = "UPDATE {$this->tableName} SET Sequence='{$sequence}' WHERE {$this->entityIdFieldName}='{$shipWeaponID}' AND {$this->ownerIdFieldName}='{$this->entityId}'";
        $this->db->execute ( $tQuery );
	}
}