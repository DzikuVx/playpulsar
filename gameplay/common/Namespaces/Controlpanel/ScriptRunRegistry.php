<?php

namespace Controlpanel;

class ScriptRunRegistry extends \cpBaseRegistry {

	protected $itemClass = "\Controlpanel\ScriptRun";
	protected $allowDetail = false;
	protected $selectList = "st_scriptruns.*, (Time/Count) AS Avg";
	protected $tableList = "st_scriptruns";
	protected $extraList = "st_scriptruns.Hash IS NOT NULL";
	protected $selectCountField = "st_scriptruns.Hash";
	protected $defaultSorting = "st_scriptruns.Time";
	protected $defaultSortingDirection = 'DESC';
	protected $registryIdField = "Hash";
	protected $registryTitle = "Script Runs";

	protected function prepare() {

		$this->searchTable ['st_scriptruns.Action'] = "Action";
		$this->searchTable ['st_scriptruns.Subaction'] = "Subaction";
		$this->searchTable ['st_scriptruns.Hash'] = "Hash";

		$this->useSearchSelects ['dateSelect'] = false;
		$this->useSearchSelects ['sort'] = true;

		$this->sortTable ['st_scriptruns.Action'] = "Action";
		$this->sortTable ['st_scriptruns.Subction'] = "Subaction";
		$this->sortTable ['st_scriptruns.Count'] = "Count";
		$this->sortTable ['st_scriptruns.Time'] = "Time";
		$this->sortTable ['Avg'] = "Avg. time";

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['Action'] = "Action";
		$this->tableColumns ['Subaction'] = "Subaction";
		$this->tableColumns ['Count'] = "Count";
		$this->tableColumns ['Time'] = "Time";
		$this->tableColumns ['Avg'] = "Avg";

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

