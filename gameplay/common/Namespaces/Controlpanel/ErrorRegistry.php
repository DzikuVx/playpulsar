<?php

namespace Controlpanel;

class ErrorRegistry extends \cpBaseRegistry {

	protected $itemClass = "\Controlpanel\Error";
	protected $allowDetail = true;
	protected $selectList = "*";
	protected $tableList = "st_errormessages";
	protected $extraList = "";
	protected $selectCountField = "MessageID";
	protected $defaultSorting = "MessageID";
	protected $defaultSortingDirection = 'DESC';
	protected $registryIdField = "MessageID";
	protected $registryTitle = "Error Messages";

	protected function renderSearch() {

		$retVal = '<div style="float: right;">';
		$retVal .= \General\Controls::bootstrapButton ( 'Clean', "document.location='?class=" . $this->itemClass . "&amp;method=clear'", 'btn-danger','icon-trash' );
		$retVal .= '</div>';

		$retVal .= parent::renderSearch ();
		return $retVal;
	}

	protected function prepare() {

		$this->searchTable ['Text'] = "Text";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['CreateTime'] = "Time";
		$this->tableColumns ['ErrorText'] = "Text";
		$this->tableColumns ['__operations__'] = "";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = true;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}

}