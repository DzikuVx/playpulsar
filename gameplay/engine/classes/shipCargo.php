<?php

/**
 * Klasa zawartości ładowni statku
 * @version $Rev: 453 $
 * @package Engine
 *
 */
class shipCargo {
	protected $userID = null;
	protected $language = 'pl';
	protected $nameField = "";
	protected $tableName = "shipcargo";
	protected $addCondition = "";
	protected $productJoinCondition = "";

	static public function management($userID) {

		global $actionPanel, $shipPosition, $itemJettisonCost, $shipCargo, $shipProperties, $portProperties, $userProperties;

		$actionPanel .= "<h1>" . TranslateController::getDefault()->get ( 'cargo' ) . "</h1>";

		$actionPanel .= "<table class='transactionList' cellspacing='2' cellpadding=\"0\">";

		$actionPanel .= "<tr>";
		$actionPanel .= "<th>" . TranslateController::getDefault()->get ( 'cargo' ) . "</th>";
		$actionPanel .= "<th style='width: 60px;'>" . TranslateController::getDefault()->get ( 'size' ) . "</th>";
		$actionPanel .= "<th style='width: 60px;'>" . TranslateController::getDefault()->get ( 'amount' ) . "</th>";
		$actionPanel .= "<th style='width: 60px;'>" . TranslateController::getDefault()->get ( 'total' ) . "</th>";
		$actionPanel .= "<th style='width: 6em;'>&nbsp;</th>";
		$actionPanel .= "</tr>";

		if ($shipPosition->Docked == 'yes') {
			$storageCargo = new storageCargo ( $userID, $portProperties->PortID, $userProperties->Language );

			//Sprawdz, czy gracz ma wykupione miejsce w magazynie
			$totalStorageRoom = storageCargo::sGetTotalUserSpace ( $userID, $portProperties->PortID );
			$usedStorageRoom = $storageCargo->getUsage ();
		} else {
			$totalStorageRoom = 0;
			$usedStorageRoom = 0;
		}

		//Towary zwykłe
		$tQuery = $shipCargo->getProducts ();
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$actionString = '';
			if ($shipPosition->Docked == 'no') {
				if ($shipProperties->Turns >= $itemJettisonCost) {
					$actionString .= \General\Controls::renderImgButton ( 'delete', "executeAction('jettison','product','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'jettisonOne' ) );
					$actionString .= \General\Controls::renderImgButton ( 'deleteall', "executeAction('jettison','product','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'jettisonAll' ) );
				}
			} else {
				if ($totalStorageRoom - $usedStorageRoom > 0) {
					$actionString .= \General\Controls::renderImgButton ( 'right', "executeAction('toStorehouse','product','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toStorehouseOne' ) );
					$actionString .= \General\Controls::renderImgButton ( 'rightFar', "executeAction('toStorehouse','product','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toStorehouseAll' ) );
				}
			}

			if ($actionString == '') {
				$actionString = '&nbsp;';
			}

			$actionPanel .= shipCargo::displayTableRow ( $tR1, $actionString, "transactionListGreen" );
		}

		//Itemy
		$tQuery = $shipCargo->getItems ();
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$actionString = '';

			if ($shipPosition->Docked == 'no') {
				if ($shipProperties->Turns >= $itemJettisonCost) {
					$actionString .= \General\Controls::renderImgButton ( 'delete', "executeAction('jettison','item','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'jettisonOne' ) );
					$actionString .= \General\Controls::renderImgButton ( 'deleteall', "executeAction('jettison','item','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'jettisonAll' ) );
				}
			} else {
				if ($totalStorageRoom - $usedStorageRoom > 0) {
					$actionString .= \General\Controls::renderImgButton ( 'right', "executeAction('toStorehouse','item','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toStorehouseOne' ) );
					$actionString .= \General\Controls::renderImgButton ( 'rightFar', "executeAction('toStorehouse','item','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toStorehouseAll' ) );
				}
			}
			if ($actionString == '') {
				$actionString = '&nbsp;';
			}
			$actionPanel .= shipCargo::displayTableRow ( $tR1, $actionString, "transactionListYellow" );
		}

		//Uzbrojenie
		$tQuery = $shipCargo->getWeapons ();
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$actionString = '';
			$actionString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','weapon::renderDetail','{$userProperties->Language}','{$tR1->WeaponID}')", 'Info' );

			if ($shipPosition->Docked == 'no') {
				if ($shipProperties->Turns >= $itemJettisonCost) {
					$actionString .= \General\Controls::renderImgButton ( 'delete', "executeAction('jettison','weapon','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'jettisonOne' ) );
					$actionString .= \General\Controls::renderImgButton ( 'deleteall', "executeAction('jettison','weapon','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'jettisonAll' ) );
				}
			} else {
				if ($shipProperties->CurrentWeapons < $shipProperties->MaxWeapons && $shipProperties->WeaponSize >= $tR1->Size) {
					$actionString .= \General\Controls::renderImgButton ( 'gather', "executeAction('equipFromCargo','weapon',null,'{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'equip' ) );
				}
				if ($totalStorageRoom - $usedStorageRoom > 0) {
					$actionString .= \General\Controls::renderImgButton ( 'right', "executeAction('toStorehouse','weapon','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toStorehouseOne' ) );
					$actionString .= \General\Controls::renderImgButton ( 'rightFar', "executeAction('toStorehouse','weapon','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toStorehouseAll' ) );
				}
				if ($portProperties->Type == 'station') {
					$actionString .= \General\Controls::renderImgButton ( 'sell', "executeAction('sellWeaponFromCargo','',null,{$tR1->ID},null);", TranslateController::getDefault()->get ( 'sell' ).' 1' );
				}
			}
			if (empty ( $actionString )) {
				$actionString = '&nbsp;';
			}
			$actionPanel .= shipCargo::displayTableRow ( $tR1, $actionString, "transactionListRed" );
		}
		//Equipment
		$tQuery = $shipCargo->getEquipments ();
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$actionString = '';

			$actionString .= \General\Controls::renderImgButton ( 'info', "getXmlRpc('univPanel','equipment::renderDetail','{$userProperties->Language}','{$tR1->EquipmentID}')", 'Info' );

			if ($shipPosition->Docked == 'no') {
				if ($shipProperties->Turns >= $itemJettisonCost) {
					$actionString .= \General\Controls::renderImgButton ( 'delete', "executeAction('jettison','equipment','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'jettisonOne' ) );
					$actionString .= \General\Controls::renderImgButton ( 'deleteall', "executeAction('jettison','equipment','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'jettisonAll' ) );
				}
			} else {
				if ($shipProperties->CurrentEquipment < $shipProperties->MaxEquipment) {
					$actionString .= \General\Controls::renderImgButton ( 'gather', "executeAction('equipFromCargo','equipment',null,'{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'equip' ) );
				}
				if ($totalStorageRoom - $usedStorageRoom > 0) {
					$actionString .= \General\Controls::renderImgButton ( 'right', "executeAction('toStorehouse','equipment','1','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toStorehouseOne' ) );
					$actionString .= \General\Controls::renderImgButton ( 'rightFar', "executeAction('toStorehouse','equipment','all','{$tR1->ID}',null);", TranslateController::getDefault()->get ( 'toStorehouseAll' ) );
				}
				if ($portProperties->Type == 'station') {
					$actionString .= \General\Controls::renderImgButton ( 'sell', "executeAction('sellEquipmentFromCargo','',null,{$tR1->ID},null);", TranslateController::getDefault()->get ( 'sell' ).' 1' );
				}
			}

			if (empty ( $actionString )) {
				$actionString = '&nbsp;';
			}
			$actionPanel .= shipCargo::displayTableRow ( $tR1, $actionString );
		}

		$actionPanel .= "</table>";
	}

	static function displayTableRow($data, $action, $class = '') {

		$retVal = "";
		if (empty ( $class )) {
			$retVal .= "<tr>";
		} else {
			$retVal .= "<tr class=\"" . $class . "\">";
		}
		$retVal .= "<td>" . $data->Name . "</td>";
		$retVal .= "<td>" . $data->Size . "</td>";
		$retVal .= "<td>" . $data->Amount . "</td>";
		$retVal .= "<td>" . ($data->Size * $data->Amount) . "</td>";
		$retVal .= "<td>" . $action . "</td>";
		$retVal .= "</tr>";

		return $retVal;
	}

	public function getUsage() {

		$cargoUsed = 0;

		//Petla po towarach
		$tQuery = "SELECT
      SUM({$this->tableName}.Amount * products.Size) AS ile
    FROM
      products JOIN {$this->tableName} ON {$this->addCondition} {$this->tableName}.UserID='{$this->userID}' AND {$this->tableName}.CargoID=products.ProductID AND {$this->tableName}.Type='product'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$cargoUsed += $tR1->ile;
		}

		//Petla po itemach
		$tQuery = "SELECT
      SUM({$this->tableName}.Amount * itemtypes.Size) AS ile
    FROM
      itemtypes JOIN {$this->tableName} ON {$this->addCondition} {$this->tableName}.UserID='{$this->userID}' AND {$this->tableName}.CargoID=itemtypes.ItemID AND {$this->tableName}.Type='item'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$cargoUsed += $tR1->ile;
		}

		//Petla po wyposażeniu
		$tQuery = "SELECT
      SUM({$this->tableName}.Amount * equipmenttypes.Size) AS ile
    FROM
      equipmenttypes JOIN {$this->tableName} ON {$this->addCondition} {$this->tableName}.UserID='{$this->userID}' AND {$this->tableName}.CargoID=equipmenttypes.EquipmentID AND {$this->tableName}.Type='equipment'
	   ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$cargoUsed += $tR1->ile;
		}

		//Petla po broni
		$tQuery = "SELECT
      SUM({$this->tableName}.Amount * weapontypes.Size) AS ile
    FROM
      weapontypes JOIN {$this->tableName} ON {$this->addCondition} {$this->tableName}.UserID='{$this->userID}' AND {$this->tableName}.CargoID=weapontypes.WeaponID AND {$this->tableName}.Type='weapon'
	   ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$cargoUsed += $tR1->ile;
		}

		//Petla po dronach
		$tQuery = "SELECT
      SUM({$this->tableName}.Amount) AS ile
    FROM
		{$this->tableName}
    WHERE
    {$this->addCondition} {$this->tableName}.UserID='{$this->userID}' AND
      ({$this->tableName}.Type='ascv' OR {$this->tableName}.Type='probe')";
    $tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
    while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
    	$cargoUsed += $tR1->ile;
    }

    return $cargoUsed;
	}

	/**
	 * Konstruktor
	 *
	 * @param int $userID
	 * @param string $language
	 */
	function __construct($userID, $language = 'pl') {

		$this->language = $language;
		$this->userID = $userID;
		$this->nameField = "Name" . strtoupper ( $this->language );
	}

	public function incAmount($ID, $type, $value) {

		$current = $this->getAmount ( $ID, $type );
		$this->setAmount ( $ID, $type, $current + $value );
		return true;
	}

	public function decAmount($ID, $type, $value) {

		$current = $this->getAmount ( $ID, $type );
		$this->setAmount ( $ID, $type, $current - $value );
		return true;
	}

	/**
	 * Ustawienie liczby itemów w ładowni
	 *
	 * @param int $ID
	 * @param string $type
	 * @param int $value
	 * @return boolean
	 */
	public function setAmount($ID, $type, $value) {

		if ($value < 1) {
			$this->remove ( $ID, $type );
		} else {

			if ($this->checkExists ( $ID, $type )) {
				$this->update($ID, $type, $value);
			} else {

				try {

					$this->insert($ID, $type, $value);

				}catch (Exception $e) {

					switch ($e->getCode()) {
							
						/*
						 * Jeśli wystąpił 1062 (zduplikowany klucz), wykonaj update
						*/
						case 1062:
							$this->update($ID, $type, $value);
							break;

							/*
							 * Inny błąd, rzuć wyjątkiem
							 */
						default:
							throw new \Database\Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
						break;
					}

				}

			}
		}

		return true;
	}

	/**
	 * Wstawienie wartości do magazynu
	 * @param int $ID
	 * @param string $type
	 * @param int $value
	 */
	protected function insert($ID, $type, $value) {

		$tQuery = "INSERT INTO " . $this->tableName . "(UserID, Type, CargoID, Amount)
		    		    VALUES('{$this->userID}','{$type}','{$ID}','{$value}')";
		\Database\Controller::getInstance()->execute ( $tQuery );
	}

	/**
	 * Uaktualnienie zawartości magazynu
	 * @param int $ID
	 * @param string $type
	 * @param int $value
	 */
	protected function update($ID, $type, $value) {
		$tQuery = "UPDATE
		{$this->tableName}
		    		  SET
		    		    Amount='$value'
		    		  WHERE
				        " . $this->addCondition . "
		    		    " . $this->tableName . ".UserID='{$this->userID}' AND
				        " . $this->tableName . ".Type='{$type}' AND
				        " . $this->tableName . ".CargoID = '{$ID}' ";
		\Database\Controller::getInstance()->execute ( $tQuery );
	}

	/**
	 * Sprawdzenie, czy w ładowni jest oznaczony item
	 *
	 * @param int $ID
	 * @param string $type
	 * @return boolean
	 */
	public function checkExists($ID, $type) {

		$retVal = false;

		$tQuery = "SELECT
        " . $this->tableName . ".Amount AS Amount
      FROM
        " . $this->tableName . "
      WHERE
        " . $this->addCondition . "
        " . $this->tableName . ".UserID='{$this->userID}' AND
        " . $this->tableName . ".CargoID = '{$ID}' AND
        " . $this->tableName . ".Type='{$type}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	/**
	 * Pobranie liczby w magazynie
	 *
	 * @param int $ID
	 * @param string $type
	 * @return int
	 */
	public function getAmount($ID, $type) {

		$retVal = 0;

		$tQuery = "SELECT
        " . $this->tableName . ".Amount AS Amount
      FROM
        " . $this->tableName . "
      WHERE
        " . $this->addCondition . "
        " . $this->tableName . ".UserID='{$this->userID}' AND
        " . $this->tableName . ".CargoID = '{$ID}' AND
        " . $this->tableName . ".Type='{$type}'
        ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal = $tR1->Amount;
		}

		return $retVal;
	}

	/**
	 * Usunięcie wybranego towaru z magazynu
	 *
	 * @param int $ID
	 * @param string $type
	 * @return boolean
	 */
	public function remove($ID, $type) {

		$tQuery = "DELETE FROM
        " . $this->tableName . "
      WHERE
        " . $this->addCondition . "
        " . $this->tableName . ".UserID='{$this->userID}' AND
        " . $this->tableName . ".CargoID = '{$ID}' AND
        " . $this->tableName . ".Type='{$type}'
        ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

		return true;
	}

	/**
	 * Usunięcie towaru z magazynu
	 *
	 * @param int $ID
	 * @return boolean
	 */
	final public function removeProduct($ID) {

		return $this->remove ( $ID, 'product' );
	}

	/**
	 * Usunięcie itemu z magazynu
	 *
	 * @param int $ID
	 * @return boolean
	 */
	final public function removeItem($ID) {

		return $this->remove ( $ID, 'item' );
	}

	/**
	 * Usunięcie wszystkich itemów
	 *
	 * @return boolean
	 */
	final public function removeAllItems() {

		$tQuery = "DELETE FROM
        " . $this->tableName . "
      WHERE
        " . $this->addCondition . "
        " . $this->tableName . ".UserID='{$this->userID}'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

		return true;
	}

	/**
	 * Usunięcie uzbrojenia z magazynu
	 *
	 * @param int $ID
	 * @return boolean
	 */
	final public function removeWeapon($ID) {

		return $this->remove ( $ID, 'weapon' );
	}

	/**
	 * Usunięcie wyposażenia z magazynu
	 *
	 * @param int $ID
	 * @return boolean
	 */
	final public function removeEquipment($ID) {

		return $this->remove ( $ID, 'equipment' );
	}

	/**
	 * Usunięcie całej ładowni
	 *
	 * @param stdClass $shipProperties
	 * @return boolean
	 */
	final public function removeAll($shipProperties) {

		$tQuery = "DELETE FROM
        shipcargo
      WHERE
        UserID='{$this->userID}'";
		\Database\Controller::getInstance()->execute ( $tQuery );
		$this->changed = true;
		shipProperties::updateUsedCargo ( $shipProperties );
		return true;
	}

	/**
	 * Pobranie liczby towarów w magazynie
	 *
	 * @param int $ID - ID produktu
	 * @return int
	 */
	final public function getProductAmount($ID) {

		return $this->getAmount ( $ID, 'product' );
	}

	/**
	 * PObranie liczy itemów w magazynie
	 *
	 * @param int $ID
	 * @return int
	 */
	final public function getItemAmount($ID) {

		return $this->getAmount ( $ID, 'item' );
	}

	/**
	 * Pobranie liczby uzbrojenia z magazynu
	 *
	 * @param int $ID
	 * @return int
	 */
	final public function getWeaponAmount($ID) {

		return $this->getAmount ( $ID, 'weapon' );
	}

	/**
	 * Zwraca liczbę equipów w magazynie
	 *
	 * @param int $ID
	 * @return int
	 */
	final public function getEquipmentAmount($ID) {

		return $this->getAmount ( $ID, 'equipment' );
	}

	/**
	 * Parsuje warunek sortowanie rejstrów ładowni statku
	 *
	 * @param string $orderBy
	 * @return string
	 */
	protected function orderCondition($orderBy) {

		switch ($orderBy) {

			case "priceMax" :
				$orderBy = "PriceMax";
				break;

			case "name" :
			default :
				$orderBy = $this->nameField;
				break;
		}

		return $orderBy;
	}

	/**
	 * Pobranie wszystkich towarów z ładowni
	 *
	 * @param int $ID
	 * @param string $orderBy
	 * @return object
	 */
	public function getProducts($ID = null, $orderBy = 'name') {

		$retVal = null;
		if ($ID == null)
		$ID = $this->userID;

		$tQuery = "SELECT
	  	  products.ProductID AS ID,
	  	  products.{$this->nameField} AS Name,
	  	  products.*,
	  	  " . $this->tableName . ".Amount AS Amount
	  	FROM
	  	  " . $this->tableName . " JOIN products ON {$this->addCondition} {$this->tableName}.UserID='{$this->userID}' AND  products.ProductID = " . $this->tableName . ".CargoID " . $this->productJoinCondition . " AND {$this->tableName}.Type='product'
	  	ORDER BY products.{$this->orderCondition($orderBy)}";
		$retVal = \Database\Controller::getInstance()->execute ( $tQuery );

		return $retVal;
	}

	/**
	 * Pobranie wszystkich itemów z ładowni gracza
	 *
	 * @param int $ID
	 * @param string $orderBy
	 * @return object
	 */
	public function getItems($ID = null, $orderBy = 'name') {

		$retVal = null;
		if ($ID == null)
		$ID = $this->userID;

		$tQuery = "SELECT
        itemtypes.ItemID AS ID,
        itemtypes.{$this->nameField} AS Name,
        itemtypes.*,
        " . $this->tableName . ".Amount AS Amount
      FROM
        " . $this->tableName . "  JOIN itemtypes ON {$this->addCondition} {$this->tableName}.UserID='{$this->userID}' AND itemtypes.ItemID = " . $this->tableName . ".CargoID AND {$this->tableName}.Type='item'
      ORDER BY itemtypes.{$this->orderCondition($orderBy)}";
		$retVal = \Database\Controller::getInstance()->execute ( $tQuery );

		return $retVal;
	}

	/**
	 * Pobranie wszystkich broni z ładowni gracza
	 *
	 * @param int $ID
	 * @param string $orderBy
	 * @return object
	 */
	public function getWeapons($ID = null, $orderBy = 'name') {

		$retVal = null;
		if ($ID == null)
		$ID = $this->userID;

		$tQuery = "SELECT
        weapontypes.WeaponID AS ID,
        weapontypes.{$this->nameField} AS Name,
        weapontypes.*,
        " . $this->tableName . ".Amount AS Amount
      FROM
        " . $this->tableName . " JOIN  weapontypes ON {$this->addCondition} {$this->tableName}.UserID='{$this->userID}' AND weapontypes.WeaponID = " . $this->tableName . ".CargoID AND {$this->tableName}.Type='weapon'
      ORDER BY weapontypes.{$this->orderCondition($orderBy)}";
		$retVal = \Database\Controller::getInstance()->execute ( $tQuery );

		return $retVal;
	}

	/**
	 * Pobranie wszystkich upgradów z ładowni gracza
	 *
	 * @param int $ID
	 * @param string $orderBy
	 * @return object
	 */
	public function getEquipments($ID = null, $orderBy = 'name') {

		$retVal = null;
		if ($ID == null)
		$ID = $this->userID;

		$tQuery = "SELECT
        equipmenttypes.EquipmentID AS ID,
        equipmenttypes.{$this->nameField} AS Name,
        equipmenttypes.*,
        " . $this->tableName . ".Amount AS Amount
      FROM
        " . $this->tableName . " JOIN equipmenttypes ON {$this->addCondition} {$this->tableName}.UserID='{$this->userID}' AND equipmenttypes.EquipmentID = " . $this->tableName . ".CargoID AND {$this->tableName}.Type='equipment'
      ORDER BY equipmenttypes.{$this->orderCondition($orderBy)}";
		$retVal = \Database\Controller::getInstance()->execute ( $tQuery );

		return $retVal;
	}

}
