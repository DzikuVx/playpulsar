<?php

namespace Controlpanel;

class WeaponTypesRegistry extends \cpBaseRegistry{
	protected $itemClass = "\Controlpanel\WeaponTypes";

	protected $selectList = "
    weapontypes.*,
    weaponclasses.NameEN AS ClassName";
	protected $tableList = "weapontypes LEFT JOIN weaponclasses USING(WeaponClassID)";
	protected $extraList = "";
	protected $defaultSorting = "weapontypes.NameEN";
	protected $registryTitle = "Weapon Types Registry";
	protected $selectCountField = "weapontypes.WeaponID";
	protected $registryIdField = "WeaponID";

	protected function prepare() {

		$this->searchTable ['weapontypes.NameEN'] = "Name";
		$this->searchTable ['weapontypes.Active'] = "Active";
		$this->searchTable ['weapontypes.Size'] = "Size";
		$this->searchTable ['weapontypes.PortWeapon'] = "PortWeapon";
		$this->searchTable ['weaponclasses.NameEN'] = "Class";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['WeaponID'] = "ID";
		$this->tableColumns ['NameEN'] = "Name";
		$this->tableColumns ['ClassName'] = "Class";
		$this->tableColumns ['Size'] = "Size";
		$this->tableColumns ['Active'] = "Active";
		$this->tableColumns ['PortWeapon'] = "PortWeapon";
		$this->tableColumns ['__operations__'] = "";

		$this->sortTable ['weapontypes.NameEN'] = "Name";
		$this->sortTable ['weapontypes.Size'] = "Size";
		$this->sortTable ['weapontypes.WeaponID'] = "ID";
		$this->sortTable ['weapontypes.Active'] = "Active";
		$this->sortTable ['weapontypes.PortWeapon'] = "PortWeapon";
		$this->sortTable ['weaponclasses.NameEN'] = "Class";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}
}