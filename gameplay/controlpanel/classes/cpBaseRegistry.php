<?php

/**
 * Klasa bazowa rejestrów CP
 *
 * @package ControlPanel
 * @version $Rev: 440 $
 */
abstract class cpBaseRegistry{

	/**
	 * Nazwa sesji
	 *
	 * @var string
	 */
	protected $sessionName;

	/**
	 * Czy używać zaawansowanych selektów
	 *
	 * @var array
	 */
	protected $useSearchSelects = array ();

	/**
	 * Tablica pomocnicza selektora wyszukiwania
	 *
	 * @var array
	 */
	protected $tSearchArray = array ();

	/**
	 * Params
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * User
	 *
	 * @var users
	 */
	protected $user;

	/**
	 * Czy istnieje metoda show dla pozycji rejestru
	 *
	 * @var boolean
	 */
	protected $allowDetail = true;

	/**
	 * Definicja klasy obsługującej pozycje rejestru
	 * @var string
	 */
	protected $itemClass = "";

	/**
	 * Ustawienia praw do rejestru
	 *
	 * @var array
	 */
	protected $rightsSet = array ();

	/**
	 * Tytuł rejestru
	 *
	 * @var string
	 */
	protected $registryTitle = "";

	/**
	 * Nazwa pola bazy danych identyfikującego rejestr
	 *
	 * @var string
	 */
	protected $registryIdField = "";

	/**
	 * Warunek WHERE zapytania
	 *
	 * @var string
	 */
	protected $selectCondition = "";

	/**
	 * Warunek ORDER BY zapytania
	 *
	 * @var string
	 */
	protected $sortCondition = "";

	/*
	 * Lista pól zwaracanych z bazy danych
	*/
	protected $selectList = "";

	/*
	 * Lista tabel uczestniczących w zapytaniu (warunek FROM)
	*/
	protected $tableList = "";

	protected $tableDateField = "";

	/*
	 * Lista kolumn tabeli rejestru
	*/
	protected $tableColumns = array ();

	/*
	 * Pole tabeli używane do zliczania liczby rekordów
	*/
	protected $selectCountField = "";

	/*
	 * Dodatkowy, stały warunek zapytania (WHERE)
	*/
	protected $extraList = "";

	/**
	 * Lista kolumn użytych do wyszukiwania
	 *
	 * @var array
	 */
	protected $searchTable = array ();

	/**
	 * Lista kolumn użytych do sortowania
	 *
	 * @var array
	 */
	protected $sortTable = array ();

	/**
	 * Przesunięcie zapytania
	 *
	 * @var int
	 */
	protected $limitSkip = 0;

	/**
	 * LIczba wyników na stronę
	 *
	 * @var int
	 */
	protected $limitNumber = 30;

	/**
	 * Czy stosować nawigację pomiędzy stronami
	 *
	 * @var boolean
	 */
	protected $usePageNav = true;

	/**
	 * Liczba rekordów w rejestrze
	 *
	 * @var int
	 */
	protected $resultCount = 0;

	/*
	 * Domyśla kolumna używana w sortowaniu
	*/
	protected $defaultSorting = "";

	/*
	 * Domyślny kierunek sortowania
	*/
	protected $defaultSortingDirection = "ASC";

	protected $queryResult;
	
	/**
	 * To force no navigation
	 * @var boolean
	 */
	protected $disableNavigation = false;

	/**
	 * Czy uruchomić wyszukiwanie w rejestrze
	 *
	 * @var boolean
	 */
	protected $enableSearch = true;

	protected $db = null;

	/**
	 * Konstruktor
	 *
	 * @param dataBase $db
	 */
	public function __construct($dbObject = null) {

		if (empty($dbObject)) {
			$this->db = \Database\Controller::getInstance();
		}else {
			$this->db = $dbObject;
		}

		$this->sessionName = get_class ( $this ) . "Search";
	}

	/**
	 * Przeglądanie rejestru
	 *
	 * @param users $user
	 * @param xml $xml
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

	protected function renderSearch() {

		$retVal = "";


		/*
		 * Wstępne parsowanie danych wyszukiwania
		*/

		if ($this->rightsSet ['allowAdd'] && (user::sGetRole() == $this->rightsSet ['addRight'] || user::sGetRole() == 'admin')) {
			$retVal .= '<div style="float: right;">';
			$retVal .= \General\Controls::bootstrapButton ( 'New', "document.location='?class=" . $this->itemClass . "&amp;method=add'",'btn-success','icon-plus' );
			$retVal .= '</div>';
		}

		if (empty($this->searchTable)) {
			return $retVal;
		}

		$retVal .= "<form method='get' action='' name='searchForm'>";
		$retVal .= "<div>";
		if ($this->useSearchSelects ['search']) {
			/*
			 * Formularz wyszukiwania
			*/
			$retVal .= "<b>Search: </b>";
			$retVal .= \General\Controls::renderInput ( 'text', $_SESSION [$this->sessionName] ['searchValue'], 'searchText', 'searchText', 20 );
			$retVal .= " <b>in</b> ";

			$tOptions ['id'] = 'searchSelect';
			$retVal .= \General\Controls::renderSelect ( 'searchSelect', $_SESSION [$this->sessionName] ['searchIn'], $this->tSearchArray, $tOptions );
			$retVal .= \General\Controls::bootstrapButton ( 'Filter', "document.searchForm.submit(); return true;",'btn-info','icon-search' );
			$retVal .= \General\Controls::bootstrapButton ( 'Clean filter', "clearSearch()",'btn-warning','icon-trash' );
		}
		$retVal .= "</div>";

		/*
		 * Seletory zaawansowane
		*/
		if ($this->useSearchSelects ['dateSelect']) {
			$retVal .= "<div>";

			/*
			 * Selektor dat
			*/
			if ($this->useSearchSelects ['dateSelect']) {
				$retVal .= "<b>Od: </b><span id='startDate'>" . date ( "Y-m-d", $_SESSION [$this->sessionName] ['startDate'] ) . "</span>" . \General\Controls::renderImgButton ( 'popupOpen', "dateSelect(" . date ( "Y", $_SESSION [$this->sessionName] ['startDate'] ) . "," . date ( "n", $_SESSION [$this->sessionName] ['startDate'] ) . "," . date ( "j", $_SESSION [$this->sessionName] ['startDate'] ) . ",'startDate',0,0);", 'Wybierz datę' );
				$retVal .= " <b>Do: </b><span id='endDate'>" . date ( "Y-m-d", $_SESSION [$this->sessionName] ['endDate'] ) . "</span>" . \General\Controls::renderImgButton ( 'popupOpen', "dateSelect(" . date ( "Y", $_SESSION [$this->sessionName] ['endDate'] ) . "," . date ( "n", $_SESSION [$this->sessionName] ['endDate'] ) . "," . date ( "j", $_SESSION [$this->sessionName] ['endDate'] ) . ",'endDate',0,0);", 'Wybierz datę' );

				//@todo dodać obsługę kalendarza z jQuery UI
			}

			$retVal .= "<div>";
		}

		/*
		 * Sortowanie
		*/
		if ($this->useSearchSelects ['sort']) {
			$retVal .= "<div>";

			$retVal .= "<b>Sort by: </b>";

			$tOptions ['id'] = 'SearchSortBy';
			$tOptions ['class'] = '';
			$retVal .= \General\Controls::renderSelect ( 'SearchSortBy', $_SESSION [$this->sessionName] ['SearchSortBy'], $this->sortTable, $tOptions );

			$retVal .= "<select id='SearchSortDirection' name='SearchSortDirection'>";
			if ($_SESSION [$this->sessionName] ['SearchSortDirection'] == 'ASC') {
				$tString1 = "selected";
				$tString2 = "";
			} else {
				$tString2 = "selected";
				$tString1 = "";
			}
			$retVal .= "<option value='ASC' $tString1>ASC</option>";
			$retVal .= "<option value='DESC' $tString2>DESC</option>";
			$retVal .= "</select>";

			$retVal .= "</div>";
		}

		$retVal .= \General\Controls::renderInput ( 'hidden', $this->itemClass, 'class' );
		$retVal .= \General\Controls::renderInput ( 'hidden', 'browse', 'method' );

		$retVal .= "</form>";

		return $retVal;
	}

	/**
	 * Wyrenderowanie nawigacji pomiędzy stronami
	 *
	 * @return string
	 */
	protected function renderPageNav() {

		$tPageCount = ceil ( $this->resultCount / $this->limitNumber );

		if ($tPageCount < 2 || $this->disableNavigation) {
			return '';
		}
		
		$retVal = "<div class='pagination' style='text-align: center;'><ul>";

		if ($this->resultCount == 0)
			$tPageCount = 1;

		$tCurrentPage = ceil ( $this->limitSkip / $this->limitNumber ) + 1;

		/*
		 * Poprzednia strona
		*/
		if ($this->limitSkip > 0) {
			$tPrevSkip = $this->limitSkip - $this->limitNumber;
			if ($tPrevSkip < 0) {
				$tPrevSkip = 0;
			}
			$retVal .= "<li><a href='index.php?class=" . $this->itemClass . "&amp;method=browse&amp;limitSkip=" . $tPrevSkip . "'>&laquo;</a></li>";
			$retVal .= "<li><a href='index.php?class=" . $this->itemClass . "&amp;method=browse&amp;limitSkip=0'>1</a></li>";
		}
		else {
			$retVal .= "<li class='disabled'><a>&laquo;</a></li>";
			$retVal .= "<li class='active'><a>1</a></li>";
		}

		$retVal .= "<li class='active'><a>" . $tCurrentPage . " / " . $tPageCount.'</a></li>';

		/*
		 * Następna strona
		*/
		if ($this->limitSkip + $this->limitNumber < $this->resultCount) {
			$tNextSkip = $this->limitSkip + $this->limitNumber;
			$retVal .= "<li><a href='index.php?class=" . $this->itemClass . "&amp;method=browse&amp;limitSkip=" . (($tPageCount-1) * $this->limitNumber) . "'>{$tPageCount}</a></li>";
			$retVal .= "<li><a href='index.php?class=" . $this->itemClass . "&amp;method=browse&amp;limitSkip=" . $tNextSkip . "'>&raquo;</a></li>";
		}
		else {
			$retVal .= "<li class='active'><a>{$tPageCount}</a></li>";
			$retVal .= "<li class='disabled'><a>&raquo;</a></li>";
		}

		$retVal .= "</ul></div>";

		return $retVal;
	}

	/**
	 * Wyświetlenie nazwy rejestru
	 *
	 * @return string
	 */
	protected function renderTitle() {
		if (! empty ( $this->registryTitle )) {
			return "<h1>{$this->registryTitle}</h1>";
		} else {
			return '';
		}
	}

	/**
	 * Naprawienie przypadku gdy warunek WHERE jest pusty
	 *
	 * @return string
	 */
	protected function fixEmptyWhere() {

		$retVal = "";

		if ($this->extraList == "" && $this->selectCondition == "") {
			$retVal = "1";
		} elseif ($this->extraList != "" && $this->selectCondition == "") {
			$retVal = $this->extraList;
		} elseif ($this->extraList == "" && $this->selectCondition != "") {
			$retVal = $this->selectCondition;
		} elseif ($this->extraList != "" && $this->selectCondition != "") {
			$retVal = $this->extraList . " AND " . $this->selectCondition;
		}

		return $retVal;
	}

	/**
	 * Pobranie rejestru z bazy danych
	 *
	 */
	protected function getResults() {

		$tQuery = "SELECT {$this->selectList} FROM {$this->tableList} WHERE {$this->fixEmptyWhere()} ORDER BY {$this->sortCondition} LIMIT {$this->limitSkip},{$this->limitNumber} ";
		$this->queryResult = $this->db->execute ( $tQuery );
	}

	/**
	 * Pobranie liczby elementów w rejestrze
	 *
	 */
	protected function getCount() {

		$tQuery = $this->db->execute ( "SELECT COUNT($this->selectCountField) AS ile FROM {$this->tableList} WHERE {$this->fixEmptyWhere()}" );
		while ( $tResult = $this->db->fetch ( $tQuery ) ) {
			$this->resultCount = $tResult->ile;
		}
	}

	/**
	 * Przygotowanie warunku sortowania
	 *
	 */
	protected function prepareSorting() {

		$this->sortCondition = $_SESSION [$this->sessionName] ['SearchSortBy'] . " " . $_SESSION [$this->sessionName] ['SearchSortDirection'];

	}

	/**
	 * Przygotowanie warunku dla rejestru
	 *
	 */
	protected function prepareCondition() {

		/*
		 * Warunek kolejnej strony
		*/
		$this->limitSkip = $_SESSION [$this->sessionName] ['limitSkip'];

		/*
		 * Warunek wyszukiwania w rejestrze
		*/
		$set = false;
		if ($_SESSION [$this->sessionName] ['searchValue'] != '') {
			$this->selectCondition .= $_SESSION [$this->sessionName] ['searchIn'] . " LIKE '%" . $_SESSION [$this->sessionName] ['searchValue'] . "%'";
			$set = true;
		}

		/*
		 * Selektor dat
		*/
		if ($this->useSearchSelects ['dateSelect']) {
			if ($set) {
				$this->selectCondition .= " AND ";
			}
			$this->selectCondition .= $this->tableDateField . " >= '" . $_SESSION [$this->sessionName] ['startDate'] . "' AND " . $this->tableDateField . " <= '" . $_SESSION [$this->sessionName] ['endDate'] . "'";
			$set = true;
		}

	}

	/**
	 * Wygenerowanie łacza do szegółów rejestru
	 * @param stdClass $tResult
	 * @return string
	 */
	protected function generateDetailString($tResult) {

		$retVal = "";

		if ($this->allowDetail) {
			$retVal = "document.location='?class={$this->itemClass}&amp;method=detail&amp;id={$tResult->{$this->registryIdField}}'";
			}

			return $retVal;
		}

		/*
		 * DUMMY
		*/
		protected function populateTable() {

			$retVal = "";

			$tIndex = $this->limitSkip;

			while ( $tResult = $this->db->fetch ( $this->queryResult ) ) {
				$tIndex ++;
				$retVal .= "<tr>";

				$tArrayKeys = array_keys ( $this->tableColumns );

				foreach ( $tArrayKeys as $tKey ) {
					switch ($tKey) {

						case "#" :
							$retVal .= "<td>" . $tIndex . "</td>";
							break;

						case "__operations__" :
							$retVal .= "<td>" . $this->renderOperationsColumn ( $tResult->{$this->registryIdField}, $tResult ) . "</td>";
							break;

						case "NoticeObject" :

							$tObject = unserialize ( $tResult->Text );
							$retVal .= "<td>" . $tObject->render ( true ) . "</td>";
							$tObject->doSave = false;
							unset ( $tObject );
							break;

						case "Date" :
						case "CreateTime" :
						case "Time" :
							$retVal .= "<td>" . \General\Formater::formatDateTime ( $tResult->{$tKey} ) . "</td>";
							break;

						case "Text" :
							$retVal .= "<td>" . urldecode ( $tResult->Text ) . "</td>";
							break;
							
						case "ErrorText" :
							$retVal .= "<td>" . urldecode ( $tResult->Text ) . "</td>";
							break;

						case "__Position__" :
							if ($tResult->Docked == 'yes') {
								$tString = 'Docked';
							} else {
								$tString = 'Space';
							}
							$retVal .= "<td>" . $tString . ' -> ' . $tResult->System . '/' . $tResult->X . '/' . $tResult->Y . "</td>";
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
		 * Renderuje pole operacji na pozycji rejestru
		 *
		 * @param int $ID
		 * @return string
		 */
		protected function renderOperationsColumn($ID, $tResult = null) {

			$retVal = "";

			$detailString = $this->generateDetailString ( $tResult );

			if (!empty($detailString)) {
				$retVal .= \General\Controls::bootstrapIconButton ( "Details", $detailString, null,"icon-list" );
			}

			if ($this->rightsSet ['allowEdit'] && (user::sGetRole() == $this->rightsSet ['editRight'] || user::sGetRole() == 'admin' )) {
				$retVal .= \General\Controls::bootstrapIconButton ( "Edit", "document.location='?class={$this->itemClass}&amp;method=edit&amp;id={$ID}'", 'btn-warning',"icon-pencil" );
			}

			if ($this->rightsSet ['allowDelete'] &&  (user::sGetRole() == $this->rightsSet ['deleteRight'] || user::sGetRole() == 'admin' )) {
				$retVal .= \General\Controls::bootstrapIconButton ( "Delete", "document.location='?class={$this->itemClass}&amp;method=delete&amp;id={$ID}'","btn-danger" ,"icon-trash" );
			}

			return $retVal;
		}

		/**
		 * Zamknięcie tabeli rejestru
		 *
		 * @return string
		 */
		protected function closeTable() {

			$retVal = "</tbody>";
			$retVal .= "</table>";
			return $retVal;
		}

		/**
		 * Otwarcie tabeli rejestru
		 *
		 * @return string
		 */
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
						$retVal .= "<th style='width: 9em;'>" . $tValue . "</th>";
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

		/*
		 * DUMMY
		*/
		protected function prepare() {

		}

		/**
		 * Wyrenderowanie przycisków górnych - DUMMY
		 *
		 * @return string
		 */
		protected function renderTopButtons() {

			return "";
		}

	}
	?>