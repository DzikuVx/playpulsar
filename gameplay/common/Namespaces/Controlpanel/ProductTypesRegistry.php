<?php

namespace Controlpanel;

class ProductTypesRegistry extends \cpBaseRegistry{
	protected $itemClass = "\Controlpanel\ProductTypes";

	protected $selectList = "
    products.*";
	protected $tableList = "products";
	protected $extraList = "";
	protected $defaultSorting = "products.NameEN";
	protected $registryTitle = "Goods Types Registry";
	protected $selectCountField = "products.ProductID";
	protected $registryIdField = "ProductID";

	protected function prepare() {

		$this->searchTable ['products.NameEN'] = "Name";
		$this->searchTable ['products.Active'] = "Active";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['ProductID'] = "ID";
		$this->tableColumns ['NameEN'] = "Name";
		$this->tableColumns ['Active'] = "Active";
		$this->tableColumns ['__operations__'] = "&nbsp;";

		$this->sortTable ['products.NameEN'] = "Name";
		$this->sortTable ['products.Active'] = "Active";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}
}