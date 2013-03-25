<?php

namespace Controlpanel;

class NewsRegistry extends \cpBaseRegistry  {

	protected $itemClass = "\Controlpanel\News";

	protected $allowDetail = true;

	protected $selectList = "
    portal_news.*
    ";

	protected $tableList = "portal_news";
	protected $extraList = "portal_news.Type='news' AND portal_news.MainNews = 'no'";
	protected $selectCountField = "portal_news.NewsID";
	protected $defaultSorting = "portal_news.Time";

	protected $defaultSortingDirection = 'DESC';

	protected $registryIdField = "NewsID";
	protected $registryTitle = "Portal messages";

	protected function prepare() {

		$this->searchTable ['portal_news.Title'] = "Title";

		$this->useSearchSelects ['dateSelect'] = true;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['Time'] = "Time";
		$this->tableColumns ['Language'] = "Language";
		$this->tableColumns ['Title'] = "Title";
		$this->tableColumns ['UserName'] = "Author";
		$this->tableColumns ['__operations__'] = "&nbsp;";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = true;
		$this->rightsSet ['allowEdit'] = true;
		$this->rightsSet ['allowDelete'] = true;
		$this->rightsSet ['addRight'] = "operator";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}

}