<?php

namespace Controlpanel;

class QueriesRegistry extends \cpBaseRegistry {

	protected $itemClass = "\Controlpanel\Queries";
	protected $allowDetail = true;
	protected $selectList = "st_queries.*, (Time/Count) AS Avg";
	protected $tableList = "st_queries";
	protected $extraList = "st_queries.Hash IS NOT NULL";
	protected $selectCountField = "st_queries.Hash";
	protected $defaultSorting = "st_queries.Time";
	protected $defaultSortingDirection = 'DESC';
	protected $registryIdField = "Hash";
	protected $registryTitle = "Queries";

	protected function prepare() {

		$this->searchTable ['st_queries.Query'] = "Zapytanie";
		$this->searchTable ['st_queries.Hash'] = "Hash";

		$this->useSearchSelects ['dateSelect'] = false;
		$this->useSearchSelects ['sort'] = true;

		$this->sortTable ['st_queries.Query'] = "Query";
		$this->sortTable ['st_queries.Count'] = "Count";
		$this->sortTable ['st_queries.Time'] = "Time";
		$this->sortTable ['Avg'] = "Avg. Time";

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['Query'] = "Query";
		$this->tableColumns ['Count'] = "Count";
		$this->tableColumns ['Time'] = "Time";
		$this->tableColumns ['Avg'] = "Avg";
		$this->tableColumns ['__operations__'] = "&nbsp;";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}

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

					case "Time" :
					case "Avg" :
						$retVal .= "<td>" . number_format($tResult->{$tKey},4) . "</td>";
						break;

					case "__operations__" :
						$retVal .= "<td>" . $this->renderOperationsColumn ( $tResult->{$this->registryIdField}, $tResult ) . "</td>";
						break;

					case 'Query':
						$retVal .= "<td>" . mb_substr($tResult->{$tKey},0,128) . "</td>";
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

	protected function renderSearch() {

		$retVal = '<div style="float: right;">';
		$retVal .= \General\Controls::bootstrapButton ( 'Clean', "document.location='?class=" . $this->itemClass . "&amp;method=clear'", 'btn-danger','icon-trash' );
		$retVal .= '</div>';

		$retVal .= parent::renderSearch ();
		return $retVal;
	}

}