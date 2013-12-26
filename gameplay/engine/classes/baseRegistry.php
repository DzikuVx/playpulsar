<?php
/**
 * Klasa bazowa dla rejestrów portalu
 *
 * @package Portal
 * @version $Rev: 455 $
 */
abstract class baseRegistry {
	//TODO Klasa bazowa dla rejestrów
	protected $selectFields;
	protected $selectTables;
	protected $selectCondition;
	protected $orderCondition;
	protected $countSelect;
	protected $limiter;
	protected $itemToPage = 10;
	protected $retVal = "";
	protected $dbResult;
	protected $language;
	protected $skip = 0;
	protected $query;
	protected $rowCount = 0;

    /**
     * @param string $language
     * @param int $item2page
     */
    function __construct($language, $item2page) {

		$this->language = $language;
		$this->itemToPage = $item2page;
		if (isset ( $_GET ['skip'] ))
			$this->skip = $_GET ['skip'];
		return true;
	}

	/**
	 * Ustala liczbę rekordów w zapytaniu
	 *
	 * @return boolean
	 */
	protected function getCount() {

		$this->dbResult = \Database\Controller::getInstance()->execute ( "SELECT COUNT({$this->countSelect}) AS ILE FROM {$this->selectTables} WHERE {$this->selectCondition}" );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $this->dbResult ) ) {
			$this->rowCount = $resultRow->ILE;
		}
		return true;
	}

	protected function renderHeader() {

	}

	protected function renderFooter() {

	}

	/**
	 * Pobranie wyników zapytania do rejestru
	 *
	 * @return boolean
	 */
	function getData() {
		
		$query = "SELECT {$this->selectFields} FROM {$this->selectTables} WHERE {$this->selectCondition} ORDER BY {$this->orderCondition} {$this->limiter}";
		$this->dbResult = \Database\Controller::getInstance()->execute ( $query );
		return true;
	}

	function get() {

		$this->renderHeader ();
		$this->getCount ();

		$this->getData ();
		$this->renderFooter ();
	}
}
