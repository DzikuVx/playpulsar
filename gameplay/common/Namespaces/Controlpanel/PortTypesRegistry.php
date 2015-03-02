<?php

namespace Controlpanel;

class PortTypesRegistry extends \cpBaseRegistry{
	
	protected $itemClass = "\Controlpanel\PortTypes";

	protected $selectList = "
    porttypes.*";
	protected $tableList = "porttypes";
	protected $extraList = "porttypes.Type='port' ";
	protected $defaultSorting = "porttypes.NameEN";
	protected $registryTitle = "Port Types Registry";
	protected $selectCountField = "porttypes.PortTypeID";
	protected $registryIdField = "PortTypeID";

	protected function prepare() {

		$this->searchTable ['porttypes.NameEN'] = "Name";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['PortTypeID'] = "ID";
		$this->tableColumns ['NameEN'] = "Name";
		$this->tableColumns ['__operations__'] = "&nbsp;";

		$this->sortTable ['porttypes.NameEN'] = "Name";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}
}