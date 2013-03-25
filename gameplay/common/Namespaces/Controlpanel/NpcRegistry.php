<?php

namespace Controlpanel;

class NpcRegistry extends \cpBaseRegistry  {

	protected $itemClass = "\Controlpanel\Npc";

	protected $allowDetail = true;

	protected $selectList = "
    npctypes.Name AS NpcTypeName,
    users.UserID,
    users.Name AS Name,
    shippositions.*
    ";

	protected $tableList = "users JOIN npctypes USING(NPCTypeID) JOIN shippositions USING(UserID)";

	protected $extraList = "";
	protected $selectCountField = "users.UserID";
	protected $defaultSorting = "users.NPCTypeID";
	protected $defaultSortingDirection = 'ASC';
	protected $registryIdField = "UserID";
	protected $registryTitle = "NPC Registry";

	protected function prepare() {

		$this->searchTable ['npctypes.Name'] = "Type Name";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['NpcTypeName'] = "Type";
		$this->tableColumns ['Name'] = "Name";
		$this->tableColumns ['__Position__'] = "Position";
		$this->tableColumns ['__operations__'] = "&nbsp;";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}

}