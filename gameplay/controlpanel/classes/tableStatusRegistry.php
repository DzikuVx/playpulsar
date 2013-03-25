<?php

/**
 * Status tabel bazy danych
 *
 * @package ControlPanel
 * @version $Rev: 455 $
 */
class tableStatusRegistry extends cpBaseRegistry  {

	protected $registryIdField = 'Name';
	protected $limitNumber = 1000;
	protected $itemClass = "tableStatus";
	protected $registryTitle = "Tables Status";

	protected function prepare() {

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['Name'] = "Name";
		$this->tableColumns ['Engine'] = "Engine";
		$this->tableColumns ['Rows'] = "Rows";
		$this->tableColumns ['Data_length'] = "Data size";
		$this->tableColumns ['Index_length'] = "Index size";
		$this->tableColumns ['Data_free'] = "Overhead";
		$this->tableColumns ['Check_time'] = "Last Check";
		$this->tableColumns ['__operations__'] = "&nbsp;";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}

	protected function openTable() {
	
		$retVal = "<table class='table table-striped'>";
		$retVal .= "<thead>";
		$retVal .= "<tr>";
	
		foreach ( $this->tableColumns as $tKey => $tValue ) {
	
			switch ($tKey) {
	
				case "#" :
					$retVal .= "<th style=\"width: 1em;\">" . $tValue . "</th>";
					break;
	
				case "__operations__" :
					$retVal .= "<th style='width: 16em;'>" . $tValue . "</th>";
					break;
	
				default :
					$retVal .= "<th>" . $tValue . "</th>";
					break;
			}
		}
	
		$retVal .= "</tr>";
		$retVal .= "</thead>";
		$retVal .= "<tbody>";
	
		return $retVal;
	}
	
	/**
	 * Kolumna operacji
	 *
	 * @param string $ID
	 * @param stdClass $tResult
	 * @return string
	 */
	protected function renderOperationsColumn($ID, $tResult = null) {

		$retVal = "";

		$detailString = $this->generateDetailString ( $tResult );
		
		if (!empty($detailString)) {
			$retVal .= \General\Controls::bootstrapIconButton ( "Details", $detailString, 'btn-info',"icon-list" );
		}
		
		$retVal .= \General\Controls::bootstrapIconButton ( 'Check', "document.location='?class={$this->itemClass}&amp;method=check&amp;id={$ID}&amp;returnToDetail=true'", 'btn-info',"icon-search" );
		if ($tResult->Data_free != 0) {
			$retVal .= \General\Controls::bootstrapIconButton ( 'Optimize', "document.location='?class={$this->itemClass}&amp;method=optimize&amp;id={$ID}'", 'btn-success',"icon-cog");
		}
		$retVal .= \General\Controls::bootstrapIconButton ( 'Repair', "document.location='?class={$this->itemClass}&amp;method=repair&amp;id={$ID}'",'btn-success','icon-fire' );
		$retVal .= \General\Controls::bootstrapIconButton ( 'Analyze', "document.location='?class={$this->itemClass}&amp;method=analyze&amp;id={$ID}'",'btn-success','icon-folder-open' );

		return $retVal;
	}

	protected function populateTable() {

		$retVal = "";

		$tIndex = $this->limitSkip;

		while ( $tResult = \Database\Controller::getInstance()->fetch ( $this->queryResult ) ) {
			$tIndex ++;

			if ($tResult->Data_free != 0 && empty($this->hideOverheadColoring)) {
				$retVal .= "<tr class='redRow'>";

			} else {
				$retVal .= "<tr>";
			}

			$tArrayKeys = array_keys ( $this->tableColumns );

			foreach ( $tArrayKeys as $tKey ) {
				switch ($tKey) {

					case "#" :
						$retVal .= "<td>" . $tIndex . "</td>";
						break;

					case "__operations__" :
						$retVal .= "<td>" . $this->renderOperationsColumn ( $tResult->{$this->registryIdField}, $tResult ) . "</td>";
						break;

					case 'Status' :

						/*
						 * Sprawdz tabelę
						 */
						$tString = tableCheck::sQuickCheck ( $tResult->{$this->registryIdField} );

						if (mb_strtoupper ( $tString ) != 'OK') {
							$detailString = " style='background-color: red !important;' ";
						}
						else {
							$detailString = '';
						}

						$retVal .= "<td $detailString>" . $tString . "</td>";
						break;

					default :
						$retVal .= "<td>" . $tResult->{$tKey} . "</td>";
					break;
				}
			}

			$retVal .= "</tr>";
		}
		return $retVal;
	}

	/**
	 * Dummy
	 *
	 */
	protected function getCount() {

		$this->resultCount = 999;
	}

	/**
	 * Pobranie listy tabel
	 *
	 */
	protected function getResults() {

		$tQuery = "SHOW TABLE STATUS";
		$this->queryResult = \Database\Controller::getInstance()->execute ( $tQuery );
	}

	/**
	 * Przeglądaj
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function browse($user, $params) {

		$retVal = "";

		$this->user = $user;
		$this->params = $params;

		/*
		 * Inicjacja danych rejestru
		*/
		$this->prepare ();

		/*
		 * Przygotuj tablicę pomocniczą wyszukiwania
		*/
		$this->useSearchSelects ['search'] = false;
		$this->useSearchSelects ['dateSelect'] = false;
		$this->useSearchSelects ['sort'] = false;

		if ($this->sortTable != null) {
			$this->useSearchSelects ['sort'] = true;
		}

		if ($this->searchTable != null) {
			foreach ( $this->searchTable as $tKey => $tValue ) {

				/*
				 * Ustalenie typów
				*/
				switch ($tKey) {

					case "__DateSelect__" :
						$this->useSearchSelects ['dateSelect'] = true;

						break;

					default :
						$this->useSearchSelects ['search'] = true;
					$this->tSearchArray [$tKey] = $tValue;
					break;
				}
			}
		}

		/*
		 * Jeśli nie ma ustawionej sesji, zainicjuj
		*/
		if (! isset ( $_SESSION [$this->sessionName] )) {
			$_SESSION [$this->sessionName] ['searchValue'] = "";
			$_SESSION [$this->sessionName] ['searchIn'] = "";
			$_SESSION [$this->sessionName] ['limitSkip'] = 0;
			$_SESSION [$this->sessionName] ['startDate'] = time () - 2592000;
			$_SESSION [$this->sessionName] ['endDate'] = strtotime ( date ( "Y-m-d", time () ) . " 23:59:59" );
			$_SESSION [$this->sessionName] ['SearchSortBy'] = $this->defaultSorting;
			$_SESSION [$this->sessionName] ['SearchSortDirection'] = $this->defaultSortingDirection;
		}

		/*
		 * Przepisz pojedyncze dane do sesji
		*/
		if (isset ( $this->params ['limitSkip'] )) {
			$_SESSION [$this->sessionName] ['limitSkip'] = $params ['limitSkip'];
		}

		if (isset ( $this->params ['searchText'] )) {
			$_SESSION [$this->sessionName] ['searchValue'] = $params ['searchText'];
			$_SESSION [$this->sessionName] ['searchIn'] = $params ['searchSelect'];
		}

		if (isset ( $this->params ['startDate'] )) {
			$_SESSION [$this->sessionName] ['startDate'] = strtotime ( $this->params ['startDate'] . " 00:00:00" );
		}

		if (isset ( $this->params ['endDate'] )) {
			$_SESSION [$this->sessionName] ['endDate'] = strtotime ( $this->params ['endDate'] . " 23:59:59" );
		}

		if (isset ( $this->params ['SearchSortBy'] )) {
			$_SESSION [$this->sessionName] ['SearchSortBy'] = $this->params ['SearchSortBy'];
			$_SESSION [$this->sessionName] ['SearchSortDirection'] = $this->params ['SearchSortDirection'];
		}

		$this->prepareCondition ();
		$this->prepareSorting ();
		$this->getCount ();
		$this->getResults ();
		$retVal .= $this->renderTitle ();
		$retVal .= $this->renderTopButtons ();

		$retVal .= $this->renderSearch ();

		if ($this->usePageNav)
		$retVal .= $this->renderPageNav ();

		$retVal .= $this->openTable ();
		$retVal .= $this->populateTable ();
		$retVal .= $this->closeTable ();

		if ($this->usePageNav)
		$retVal .= $this->renderPageNav ();

		return $retVal;
	}

}