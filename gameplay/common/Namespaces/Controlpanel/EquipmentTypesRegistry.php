<?php

namespace Controlpanel;

class EquipmentTypesRegistry extends \cpBaseRegistry{

	protected $itemClass = "\Controlpanel\EquipmentTypes";

	protected $selectList = "equipmenttypes.*";
	protected $tableList = "equipmenttypes";
	protected $extraList = "";
	protected $selectCountField = "equipmenttypes.EquipmentID";
	protected $registryTitle = "Equipment Types Registry";
	protected $defaultSorting = "equipmenttypes.NameEN";
	protected $registryIdField = "EquipmentID";

	protected function prepare() {

		$this->searchTable ['equipmenttypes.NameEN'] = "Name";
		$this->searchTable ['equipmenttypes.Active'] = "Active";
		$this->searchTable ['equipmenttypes.Unique'] = "Unique";
		$this->searchTable ['equipmenttypes.Size'] = "Size";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['EquipmentID'] = "ID";
		$this->tableColumns ['NameEN'] = "Name";
		$this->tableColumns ['Size'] = "Size";
		$this->tableColumns ['Active'] = "Active";
		$this->tableColumns ['Unique'] = "Unique";
		$this->tableColumns ['__operations__'] = "";

		$this->sortTable ['equipmenttypes.NameEN'] = "Name";
		$this->sortTable ['equipmenttypes.Size'] = "Size";
		$this->sortTable ['equipmenttypes.EquipmentID'] = "ID";
		$this->sortTable ['equipmenttypes.Active'] = "Active";
		$this->sortTable ['equipmenttypes.Unique'] = "Unique";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}
}