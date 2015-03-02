<?php
/**
 * Klasa noticeów gracza
 *
 * @see newsAgencyNotice
 * @version $Rev: 418 $
 * @package ControlPanel
 */
class playerNoticesRegistry extends cpBaseRegistry{
	protected $itemClass = "cpNotices";
	protected $allowDetail = true;
	protected $selectList = "newsagency.*, newsagency.Text AS NoticeObject, newsagencytypes.NameEN AS TypeName";
	protected $tableList = "newsagency JOIN newsagencytypes ON newsagencytypes.ID=newsagency.Type";
	protected $extraList = "newsagency.Date IS NOT NULL";
	protected $selectCountField = "NewsagencyID";
	protected $defaultSorting = "newsagency.Date";
	protected $defaultSortingDirection = 'DESC';
	protected $registryIdField = "NewsagencyID";
	protected $registryTitle = "";
	protected $limitNumber = 20;
	protected $disableNavigation = true;

	protected function prepare() {

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['System'] = "System";
		$this->tableColumns ['TypeName'] = "Type";
		$this->tableColumns ['Date'] = "Date";
		$this->tableColumns ['NoticeObject'] = "Text";

		$this->rightsSet ['moduleName'] = "News";
		$this->rightsSet ['allowAdd'] = false;
		$this->rightsSet ['allowEdit'] = false;
		$this->rightsSet ['allowDelete'] = false;
		$this->rightsSet ['addRight'] = "admin";
		$this->rightsSet ['editRight'] = "admin";
		$this->rightsSet ['deleteRight'] = "admin";

	}

	protected function prepareCondition() {

		$this->selectCondition .= " (newsagency.UserID='{$this->params['playerID']}' OR newsagency.ByUserID='{$this->params['playerID']}')";

		parent::prepareCondition ();

	}

}