<?php

namespace Controlpanel;

class NpcTypesRegistry extends \cpBaseRegistry{
	protected $itemClass = "\Controlpanel\NpcTypes";

	protected $selectList = "
   				npctypes.*, 
					alliances.Name As AllianceName";
	protected $tableList = "npctypes LEFT JOIN alliances USING(AllianceID) ";
	protected $extraList = "";
	protected $defaultSorting = "npctypes.Name";
	protected $registryTitle = "NPC Types Registry";
	protected $selectCountField = "npctypes.NPCTypeID";
	protected $registryIdField = "NPCTypeID";

	protected function prepare() {

		$this->searchTable ['npctypes.Name'] = "Name";
		$this->searchTable ['npctypes.Dock'] = "Dock";
		$this->searchTable ['npctypes.Moveable'] = "Moveable";
		$this->searchTable ['alliances.Name'] = "Alliance";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['NPCTypeID'] = "ID";
		$this->tableColumns ['Name'] = "Name";
		$this->tableColumns ['AllianceName'] = "AllianceName";
		$this->tableColumns ['Moveable'] = "Moveable";
		$this->tableColumns ['Dock'] = "Dock";
		$this->tableColumns ['__operations__'] = "&nbsp;";

		$this->sortTable ['npctypes.Name'] = "Name";
		$this->sortTable ['alliances.Name'] = "Alliance";
		$this->sortTable ['npctypes.Dock'] = "Dock";
		$this->sortTable ['npctypes.Moveable'] = "Moveable";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}

	protected function renderSearch() {

		if ((\user::sGetRole () == 'admin')) {
			$retVal = '<div style="float: right;">';
			$retVal .= \General\Controls::bootstrapButton ( 'Drop All', "document.location='?class=" . $this->itemClass . "&amp;method=dropAll'", 'btn-danger','icon-trash' );
			$retVal .= \General\Controls::bootstrapButton ( 'Create All', "document.location='?class=" . $this->itemClass . "&amp;method=createAll'", 'btn-danger', 'icon-plus' );
			$retVal .= '</div>';
		}

		$retVal .= parent::renderSearch ();
		return $retVal;
	}

}