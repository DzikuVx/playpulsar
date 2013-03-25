<?php

namespace Controlpanel;

class PlayerRegistry extends NpcRegistry {

	protected $itemClass = "\Controlpanel\Player";

	protected $selectList = "
    users.UserID,
    users.Name AS Name,
    users.Login,
    userstats.Level,
    shippositions.*
    ";

	protected $tableList = "users JOIN shippositions USING(UserID) JOIN userstats USING(UserID)";
	protected $extraList = "users.Type='player' ";
	protected $defaultSorting = "users.Name";
	protected $registryTitle = "Player Registry";

	protected function prepare() {

		$this->searchTable ['users.Name'] = "Player Name";
		$this->searchTable ['users.Login'] = "Player Login";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['Login'] = "Login";
		$this->tableColumns ['Name'] = "Name";
		$this->tableColumns ['Level'] = "Level";
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