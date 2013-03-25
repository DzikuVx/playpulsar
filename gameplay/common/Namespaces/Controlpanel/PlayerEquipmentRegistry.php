<?php

namespace Controlpanel;

class PlayerEquipmentRegistry extends \cpBaseRegistry{
	protected $itemClass = "\Controlpanel\PlayerEquipment";
	protected $allowDetail = false;
	protected $selectList = "shipequipment.*, equipmenttypes.*";
	protected $tableList = "shipequipment JOIN equipmenttypes USING(EquipmentID)";
	protected $extraList = "shipequipment.ShipEquipmentID IS NOT NULL";
	protected $selectCountField = "ShipEquipmentID";
	protected $defaultSorting = "equipmenttypes.NameEN";
	protected $defaultSortingDirection = 'ASC';
	protected $registryIdField = "ShipEquipmentID";
	protected $registryTitle = "";
	protected $limitNumber = 1000;
	protected $disableNavigation = true;

	protected function prepare() {

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['NameEN'] = "Equipment Type";
		$this->tableColumns ['Damaged'] = "Damaged";
		$this->tableColumns ['__operations__'] = '';

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = true;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = true;
		$this->rightsSet ['addRight'] = "operator";
		$this->rightsSet ['editRight'] = "operator";
		$this->rightsSet ['deleteRight'] = "operator";

	}

	protected function prepareCondition() {

		$this->selectCondition .= " shipequipment.UserID='{$this->params['playerID']}' ";

		parent::prepareCondition ();

	}

}