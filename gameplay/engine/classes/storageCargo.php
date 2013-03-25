<?php
/**
 * Klasa magazynu gracza w stacji
 *
 * @version $Rev: 453 $
 * @package Engine
 */
class storageCargo extends shipCargo {
	protected $tableName = "portcargo";
	protected $portID = null;
	
	/**
	 * (non-PHPdoc)
	 * @see shipCargo::insert()
	 */
	protected function insert($ID, $type, $value) {
	
		$tQuery = "INSERT
            INTO " . $this->tableName . "(PortID, UserID, Type, CargoID, Amount)
            VALUES('{$this->portID}','{$this->userID}','{$type}','{$ID}','{$value}')";
		\Database\Controller::getInstance()->execute ( $tQuery );
	}
	
	/**
	 * Konstruktor
	 *
	 * @param int $userID
	 * @param int $portID
	 * @param string $language
	 */
	function __construct($userID, $portID, $language = 'pl') {

		$this->language = $language;
		$this->userID = $userID;
		$this->nameField = "Name" . strtoupper ( $this->language );
		$this->addCondition = " portcargo.PortID = '$portID' AND ";
		$this->portID = $portID;
	}

	static public function sGetTotalUserSpace($userID, $PortID) {
	
		$out = 0;
		$tQuery = "SELECT
	      userportcargo.Size
	    FROM
	      userportcargo
	    WHERE
	      UserID='$userID' AND
	      PortID='$PortID'
	    ";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$out = $tR1->Size;
		}
		return $out;
	}
	
}