<?php

namespace Portal;

use \Cache\Controller as Cache;
use \Database\Controller as Database;

class NewsRegistry extends \baseRegistry{

	/**
	 * Konstruktor
	 *
	 * @param string $language
	 * @param item $item2page
	 * @return boolean
	 */
	function __construct($language, $item2page) {
		parent::__construct ( $language, $item2page );

		$this->selectFields = "
				portal_news.NewsID AS NewsID,
        portal_news.Time AS Time,
        portal_news.Title AS Title,
        portal_news.Text AS Text,
        portal_news.UserName AS Name,
        portal_news.Language
      ";

		$this->selectTables = "
        portal_news
      ";

		$this->selectCondition = "
        portal_news.Type='news' AND
        portal_news.Published='yes' AND
        portal_news.Language='{$this->language}' AND
        portal_news.MainNews='no'
      ";

		$this->orderCondition = "portal_news.Time DESC";

		$this->limiter = "LIMIT {$this->skip}, {$this->itemToPage}";

		$this->countSelect = "portal_news.NewsID";

		return true;
	}

	function getData() {
		$query = "SELECT {$this->selectFields} FROM {$this->selectTables} WHERE {$this->selectCondition} ORDER BY {$this->orderCondition} {$this->limiter}";
		$this->dbResult = Database::getPortalInstance()->execute ( $query );
		return true;
	}

	function get() {
		$retVal = "";
		global $config;

		$module = 'newsRegistry';
		$property = $this->language;

		if (!Cache::getInstance()->check($module, $property)) {

			$this->getData ();
			$news = new \Portal\News ( );
			while ( $resultRow = Database::getPortalInstance()->fetch ( $this->dbResult ) ) {
				$retVal .= $news->render ( $resultRow );
			}

			Cache::getInstance()->set($module, $property, $retVal);

		}else {
			$retVal = Cache::getInstance()->get($module, $property);
		}
		return $retVal;
	}

}