<?php

namespace Portal;

use \Database\Controller as Database;

class NewsRegistry extends \baseRegistry{

    /**
     * @param string $language
     * @param int $item2page
     */
    public function __construct($language, $item2page) {
		parent::__construct ( $language, $item2page );

		$this->selectFields = "
				portal_news.NewsID AS NewsID,
                portal_news.Time AS Time,
                portal_news.Title AS Title,
                portal_news.Text AS Text,
                portal_news.UserName AS Name,
                portal_news.Language
              ";

		$this->selectTables = " portal_news ";

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

		$oCachekey = new \phpCache\CacheKey('newsRegistry', $this->language);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (!$oCache->check($oCachekey)) {

			$this->getData ();
			$news = new \Portal\News ( );
			while ( $resultRow = Database::getPortalInstance()->fetch ( $this->dbResult ) ) {
				$retVal .= $news->render ( $resultRow );
			}

			$oCache->set($oCachekey, $retVal);

		}else {
			$retVal = $oCache->get($oCachekey);
		}
		return $retVal;
	}

}