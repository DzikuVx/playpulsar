<?php

/**
 * Rejestr szybkiego sprawdzania tabel
 * @version $Rev: 418 $
 * @package ControlPanel
 *
 */
class tableCheckRegistry extends tableStatusRegistry {

	protected $registryIdField = 'Name';
	protected $limitNumber = 1000;
	protected $itemClass = "tableCheck";
	protected $registryTitle = "Tables Check";

	/**
	 * Czy wyłączać kolorowanie wierszy dla tabel z overhead
	 *
	 * @var boolean
	 */
	protected $hideOverheadColoring = true;

	protected function prepare() {

		$this->useSearchSelects ['dateSelect'] = false;

		$this->tableColumns ['#'] = "Lp.";
		$this->tableColumns ['Name'] = "Name";
		$this->tableColumns ['Engine'] = "Engine";
		$this->tableColumns ['Rows'] = "Rows";
		$this->tableColumns ['Data_free'] = "Overhead";
		$this->tableColumns ['Check_time'] = "Last Check";
		$this->tableColumns ['Status'] = "Status";
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

?>