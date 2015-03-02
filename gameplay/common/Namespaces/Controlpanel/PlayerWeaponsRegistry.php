<?php

namespace Controlpanel;

class PlayerWeaponsRegistry extends \cpBaseRegistry{
	protected $itemClass = "\Controlpanel\PlayerWeapon";
	protected $allowDetail = false;
	protected $selectList = "shipweapons.*, weapontypes.*, weapontypes.Ammo AS MaxAmmo, shipweapons.Ammo AS CurrentAmmo";
	protected $tableList = "shipweapons JOIN weapontypes USING(WeaponID)";
	protected $extraList = "shipweapons.ShipWeaponID IS NOT NULL";
	protected $selectCountField = "ShipWeaponID";
	protected $defaultSorting = "shipweapons.Sequence";
	protected $defaultSortingDirection = 'ASC';
	protected $registryIdField = "ShipWeaponID";
	protected $registryTitle = "";
	protected $limitNumber = 1000;

	protected function prepare() {

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['NameEN'] = "Weapon Type";
		$this->tableColumns ['Enabled'] = "Enabled";
		$this->tableColumns ['CurrentAmmo'] = "Ammo";
		$this->tableColumns ['MaxAmmo'] = "Max Ammo";
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

		$this->selectCondition .= " shipweapons.UserID='{$this->params['playerID']}' ";

		parent::prepareCondition ();

	}

}