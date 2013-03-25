<?php

namespace Controlpanel;

class MainNewsRegistry extends NewsRegistry{

	protected $extraList = "portal_news.MainNews = 'yes'";
	protected $itemClass = "\Controlpanel\MainNews";

	protected function prepare() {

		$this->searchTable ['portal_news.Title'] = "Title";

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['Language'] = "Language";
		$this->tableColumns ['__operations__'] = "&nbsp;";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = true;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "operator";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}

}