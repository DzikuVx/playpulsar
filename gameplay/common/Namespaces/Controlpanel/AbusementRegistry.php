<?php

namespace Controlpanel;

class AbusementRegistry extends \cpBaseRegistry {
	protected $itemClass = "\Controlpanel\Abusement";
	protected $allowDetail = true;
	protected $selectList = "abusements.*,
		user.Name AS AtUserName,
		by_user.Name AS ByUserName";
	protected $tableList = "abusements JOIN users AS user ON user.UserID=abusements.UserID
					JOIN users AS by_user ON by_user.UserID=abusements.ByUserID";
	protected $extraList = "";
	protected $selectCountField = "AbusementID";
	protected $defaultSorting = "CreateTime";
	protected $defaultSortingDirection = 'DESC';
	protected $registryIdField = "AbusementID";
	protected $registryTitle = "Abusements";
	
	//@todo FINISH
	
	protected function prepare() {

		$this->searchTable ['Status'] = "Status";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['CreateTime'] = "Time";
		$this->tableColumns ['AtUserName'] = "Player";
		$this->tableColumns ['ByUserName'] = "By Player";
		$this->tableColumns ['Text'] = "Text";
		$this->tableColumns ['Status'] = "Status";
		$this->tableColumns ['__operations__'] = "";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "operator";
		$this->rightsSet ['editRight'] = "operator";
		$this->rightsSet ['deleteRight'] = "operator";

	}

}