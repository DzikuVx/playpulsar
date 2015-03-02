<?php

namespace Controlpanel;

class NpcNameRegistry extends \cpBaseRegistry {

	protected $itemClass = "\Controlpanel\NpcName";
	protected $allowDetail = false;
	protected $selectList = "names.*";
	protected $tableList = "names";
	protected $extraList = "";
	protected $selectCountField = "*";
	protected $defaultSorting = "Name";
	protected $defaultSortingDirection = 'ASC';
	protected $registryIdField = "ID__";
	protected $registryTitle = "NPC Names";

	protected function prepare() {

		$this->searchTable ['Name'] = "Name";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['Type'] = "Type";
		$this->tableColumns ['Name'] = "Name";
		$this->tableColumns ['__operations__'] = "";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = true;
		$this->rightsSet ['allowEdit'] = true;
		$this->rightsSet ['allowDelete'] = true;
		$this->rightsSet ['addRight'] = "operator";
		$this->rightsSet ['editRight'] = "operator";
		$this->rightsSet ['deleteRight'] = "operator";

	}

}