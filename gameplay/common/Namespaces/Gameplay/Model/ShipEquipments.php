<?php

namespace Gameplay\Model;

use Database\MySQLiWrapper;
use Gameplay\PlayerModelProvider;

class ShipEquipments {

	protected $entityId = null;
	protected $language = 'pl';
	protected $nameField = "";
	protected $tableName = "shipequipment";

    /**
     * name of table field determining entity owner
     * @var string
     */
    protected $ownerIdFieldName = 'UserID';

    protected $entityIdFieldName = 'ShipEquipmentID';

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
     * @return int
     */
    public function getDamagedCount() {
		$tQuery = "SELECT COUNT(*) AS ILE FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND Damaged='1'";
		$tQuery = $this->db->execute($tQuery);
		return $this->db->fetch($tQuery)->ILE;
	}

	/**
	 * Get number o fully operational equipment
	 * return int
	 */
	public function getOperationalCount() {
		$tQuery = "SELECT COUNT(*) AS ILE FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND Damaged='0'";
		$tQuery = $this->db->execute($tQuery);
		$retVal = $this->db->fetch($tQuery)->ILE;
		return $retVal;
	}

	/**
	 * Uszkodzenie losowego wyposażenia
	 *
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

	public function synchronize() {
        if ($this->changed) {
            $shipProperties = PlayerModelProvider::getInstance()->get('ShipProperties');
            $shipProperties->computeDefensiveRating();
		}
	}

	/**
	 * @param int|\stdClass $ID
	 * @return boolean
	 */
	public function checkExists($ID) {

        $retVal = false;

		if (is_numeric ( $ID )) {
			$tQuery = "SELECT COUNT({$this->entityIdFieldName}) AS ILE FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND {$this->entityIdFieldName}='{$ID}'";
		} else {
			$tQuery = "SELECT COUNT({$this->entityIdFieldName}) AS ILE FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}' AND EquipmentID='{$ID->EquipmentID}'";
		}
		$tQuery = $this->db->execute($tQuery);
		while ( $resultRow = $this->db->fetch($tQuery)) {
			if ($resultRow->ILE == 0) {
				$retVal = false;
			} else {
				$retVal = true;
			}
		}

		return $retVal;
	}

	/**
	 * Wstawienie wyposażenia
	 *
	 * @param EquipmentType $equipment
	 * @param ShipProperties $shipProperties
	 * @return bool
     * @throws \securityException
	 */
	public function insert($equipment, ShipProperties $shipProperties) {

		if ($shipProperties->CurrentEquipment >= $shipProperties->MaxEquipment) {
			throw new \securityException();
		}

		/*
		 * Sparawdz unikalność
		 */
		if ($equipment->Unique == 'yes') {
			if ($this->checkExists($equipment->EquipmentID)) {
			    return false;
            }
		}

		$tQuery = "INSERT INTO {$this->tableName}({$this->ownerIdFieldName}, EquipmentID) VALUES('{$this->entityId}','{$equipment->EquipmentID}')";
		$this->db->execute ( $tQuery );
		$shipProperties->CurrentEquipment += 1;
		//@todo uwzględnić przypadek, w którym rozmiar equipmentu może być większy od 1
		$this->changed = true;

		return true;
	}

    /**
     * @param int $equipmentID
     * @return bool
     */
    public function repair($equipmentID) {

		$tQuery = "UPDATE {$this->tableName} SET Damaged='0' WHERE {$this->entityIdFieldName}='{$equipmentID}'";
		$this->db->execute ( $tQuery );

		$this->changed = true;

		return true;
	}

	/**
	 * @param int $equipmentID
	 * @return boolean
	 */
	public function damage($equipmentID) {

		$tQuery = "UPDATE {$this->tableName} SET Damaged='1' WHERE {$this->entityIdFieldName}='{$equipmentID}'";
		$this->db->execute ( $tQuery );

		$this->changed = true;

		return true;
	}

	/**
	 * @param ShipProperties $shipProperties
	 * @return boolean
	 */
	public function removeAll(ShipProperties $shipProperties) {

		$tQuery = "DELETE FROM {$this->tableName} WHERE {$this->ownerIdFieldName}='{$this->entityId}'";
		$this->db->execute($tQuery);

		$shipProperties->CurrentEquipment = 0;
		$this->changed = true;
		return true;
	}

	/**
	 * @return boolean
	 */
	public function damageAll() {
		$tQuery = "UPDATE {$this->tableName} SET Damaged='1' WHERE {$this->ownerIdFieldName}='{$this->entityId}'";
		$this->db->execute($tQuery);

		$this->changed = true;
		return true;
	}

	/**
	 * Usunięcie wybranego wyposażenia
	 *
	 * @param int $ID
	 * @param ShipProperties $shipProperties
	 * @return boolean
	 */
	public function remove($ID, ShipProperties $shipProperties) {

		$tQuery = "DELETE FROM {$this->tableName} WHERE {$this->entityIdFieldName}='{$ID}' AND {$this->ownerIdFieldName}='$this->entityId}'";
		$this->db->execute($tQuery);
		$shipProperties->CurrentEquipment -= 1;
		$this->changed = true;
		return true;
	}

	/**
	 * @param string $mode
	 * @return \mysqli_result
	 */
	function get($mode = "working") {

		switch ($mode) {
			case "all" :
				$addQuery = "";
				break;

			default :
			case "working" :
				$addQuery = "AND shipequipment.Damaged = '0'";
				break;
		}

		$tQuery = "SELECT
                equipmenttypes.*,
                equipmenttypes.{$this->nameField} AS Name,
                {$this->tableName}.{$this->entityIdFieldName},
                {$this->tableName}.Damaged
            FROM
                {$this->tableName} LEFT JOIN equipmenttypes ON equipmenttypes.EquipmentID = {$this->tableName}.EquipmentID
            WHERE
                {$this->tableName}.{$this->ownerIdFieldName}='{$this->entityId}' {$addQuery}";
        return $this->db->execute ( $tQuery );
	}

    /**
     * @param $equipmentID
     * @return \stdClass
     */
    function getSingle($equipmentID) {
        //FIXME return real equipment object
        $retVal = null;
		$tQuery = "SELECT
            equipmenttypes.*,
            equipmenttypes.{$this->nameField} AS Name,
            {$this->tableName}.{$this->entityIdFieldName},
            {$this->tableName}.Damaged
        FROM
            {$this->tableName} LEFT JOIN equipmenttypes ON equipmenttypes.EquipmentID = {$this->tableName}.EquipmentID
        WHERE
            {$this->tableName}.{$this->entityIdFieldName}='{$equipmentID}'";
		$tQuery = $this->db->execute ( $tQuery );
		while ( $tResult = $this->db->fetch ( $tQuery ) ) {
			$retVal = $tResult;
		}
		return $retVal;
	}

}