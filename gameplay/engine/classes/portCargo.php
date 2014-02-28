<?php
/**
 * Ładownia portu
 *
 * @version $Rev: 453 $
 * @package Engine
 */
class portCargo {
	protected $language = 'pl';
	protected $nameField = "";

    /**
     * @var string
     */
    protected $tableName = "portcargo";

    /**
     * @var \Gameplay\Model\PortEntity
     */
    protected $portProperties = null;

	protected $userID;

    /**
     * @param $userID
     * @param \Gameplay\Model\PortEntity $portProperties
     * @param string $language
     */
    function __construct($userID, \Gameplay\Model\PortEntity $portProperties, $language = 'pl') {
		$this->language = $language;
		$this->nameField = "Name" . strtoupper ( $this->language );
		$this->portProperties = $portProperties;
		$this->userID = $userID;
	}

	/**
	 * lista przedmiotów jakie port kupuje
	 *
	 * @return mysqli_result
	 */
	public function getProductsBuy() {

		$ID = $this->portProperties->PortID;

		$addQuery = "";
		$addQuery2 = "";

		if ($this->portProperties->SpecialBuy != '') {
			$addQuery = "products.ProductID IN (" . $this->portProperties->SpecialBuy . ") OR ";
		}
		if ($this->portProperties->NoBuy != '') {
			$addQuery2 = "products.ProductID NOT IN (" . $this->portProperties->NoBuy . ") AND ";
		}

		$tQuery = "SELECT
		    products.ProductID AS ID,
		    products.$this->nameField AS Name,
		    portcargo.Amount AS Amount,
		    products.PriceMin AS PriceMin,
		    products.PriceMax AS PriceMax,
		    products.ExpMin AS ExpMin,
		    products.ExpMax AS ExpMax,
		    shipcargo.Amount AS ShipAmount
		  FROM
		    (products JOIN portcargo ON portcargo.CargoID=products.ProductID AND portcargo.Type='product' AND portcargo.PortID='{$ID}' AND portcargo.Mode='buy' AND portcargo.UserID IS NULL)
		    LEFT JOIN shipcargo ON shipcargo.CargoID=products.ProductID AND shipcargo.Type='product' AND shipcargo.UserID='{$this->userID}'
		  WHERE
		    ($addQuery products.RegularBuy='yes') AND
		$addQuery2
		    portcargo.Amount >= '0' AND portcargo.Amount IS NOT NULL AND
		    products.Active = 'yes'
		  ORDER BY
		    products.{$this->nameField}";
		$retVal = \Database\Controller::getInstance()->execute ( $tQuery );

		return $retVal;
	}

	/**
	 * Lista przedmiotów jakie sprzedaje port
	 *
	 * @return mysqli_result
	 */
	public function getProductsSell() {
		
		$ID = $this->portProperties->PortID;

		$addQuery = "";
		$addQuery2 = "";

		if ($this->portProperties->SpecialSell != '') {
			$addQuery = "products.ProductID IN (" . $this->portProperties->SpecialSell . ") OR ";
		}

		if ($this->portProperties->NoSell != '') {
			$addQuery2 = "products.ProductID NOT IN (" . $this->portProperties->NoSell . ") AND ";
		}

		$tQuery = "SELECT
		    products.ProductID AS ID,
		    products.{$this->nameField} AS Name,
		    portcargo.Amount AS Amount,
		    products.PriceMin AS PriceMin,
		    products.PriceMax AS PriceMax,
		    products.ExpMin AS ExpMin,
		    products.ExpMax AS ExpMax,
		    products.Size AS Size
		  FROM
		    products JOIN portcargo ON portcargo.PortID='{$ID}'  AND portcargo.UserID IS NULL AND portcargo.CargoID=products.ProductID AND portcargo.Type='product' AND portcargo.Mode='sell'
		  WHERE
		    ($addQuery products.RegularSell='yes') AND
		$addQuery2
		    portcargo.Amount > '0' AND portcargo.Amount IS NOT NULL AND
		    products.Active = 'yes'
		  ORDER BY
		    products.{$this->nameField}";
		$retVal = \Database\Controller::getInstance()->execute ( $tQuery );

		return $retVal;
	}

	public function getMapsSell() {

		$ID = $this->portProperties->PortID;

		$tQuery = "SELECT
		    systems.SystemID,
		    systems.Number,
		    systems.Name
		  FROM
		    systems JOIN portcargo ON portcargo.PortID='{$ID}'  AND portcargo.UserID IS NULL AND portcargo.CargoID=systems.SystemID AND portcargo.Type='map' AND portcargo.Mode='buy'
		  WHERE
		    systems.Enabled='yes' AND
		    portcargo.Amount > '0' AND portcargo.Amount IS NOT NULL";
		$retVal = \Database\Controller::getInstance()->execute ( $tQuery );

		return $retVal;
	}

}