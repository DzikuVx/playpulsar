<?php

namespace Controlpanel;

class ShipTypesRegistry extends \cpBaseRegistry{
	
	protected $itemClass = "\Controlpanel\ShipTypes";

	protected $selectList = "shiptypes.*";
	protected $tableList = "shiptypes";
	protected $extraList = "";
	protected $selectCountField = "shiptypes.ShipID";
	protected $registryTitle = "Ship Types Registry";
	protected $defaultSorting = "shiptypes.NameEN";
	protected $registryIdField = "ShipID";

	protected function prepare() {

		$this->searchTable ['shiptypes.NameEN'] = "Name";
		$this->searchTable ['shiptypes.UserBuyable'] = "UserBuyable";
		$this->searchTable ['shiptypes.Size'] = "Size";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['ShipID'] = "ID";
		$this->tableColumns ['NameEN'] = "Name";
		$this->tableColumns ['Size'] = "Size";
		$this->tableColumns ['UserBuyable'] = "UserBuyable";
		$this->tableColumns ['__operations__'] = "&nbsp;";

		$this->sortTable ['shiptypes.NameEN'] = "Name";
		$this->sortTable ['shiptypes.Size'] = "Size";
		$this->sortTable ['shiptypes.ShipID'] = "ID";
		$this->sortTable ['shiptypes.UserBuyable'] = "UserBuyable";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}
}