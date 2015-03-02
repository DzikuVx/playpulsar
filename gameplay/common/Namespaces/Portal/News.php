<?php

namespace Portal;

use \Database\Controller as Database;
use General\Templater;

class News extends BaseItem {

	/**
	 * Krótki link dla systemu newsów
	 * @param int $id
	 * @param string $title
	 * @return string
	 */
	static public function sGenUrl($id, $title) {
		//		return '?class=portalNews&method=detail&id='.$id;
		return 'news_' . $id . '_' . urlencode ( mb_substr ( $title, 0, 64 ) ) . '_.html';
	}

	/**
	 * Pobranie newsa z bazy danych
	 *
	 * @param int $ID
	 * @return \stdClass
	 */
	function get($ID) {

        if ($ID == null) {
			return false;
        }

		$oCacheKey = new \phpCache\CacheKey('portalNews::get', $ID);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if ($oCache->check($oCacheKey)) {
			$this->dataObject = unserialize($oCache->get($oCacheKey));
		} else {
			$query = "
			SELECT
			portal_news.NewsID AS NewsID,
			portal_news.Time AS Time,
			portal_news.Title AS Title,
			portal_news.Text AS Text,
			portal_news.UserName AS Name,
			portal_news.Language
			FROM
			portal_news
			WHERE
			portal_news.NewsID = '{$ID}'
			";
			$result = Database::getPortalInstance()->execute ( $query );
			$this->dataObject = Database::getPortalInstance()->fetch ( $result );

			$oCache->set($oCacheKey, serialize($this->dataObject), 86400);

		}

		return $this->dataObject;
	}

    /**
     * @param \stdClass $object
     * @param bool $renderNav
     * @return null|string
     */
    public function render($object = null, $renderNav = true) {
		if (empty($object)) {
			/**
			 * @var \Portal\News
			 */
			$object = $this->dataObject;
		}
		if (empty($object)) {
			return null;
		}

		$retVal = '';

		$retVal .= "<h1>";
		$retVal .= $object->Title;
		$retVal .= "</h1>";

		$retVal .= "<h4>" . $object->Name . "</h4>";
		$retVal .= "<h5>" . date ( "Y-m-d H:i", $object->Time ) . "</h5>";

		$retVal .= "<div>" . $object->Text . "</div>";

		if ($renderNav) {

			$oNext= $this->getNext($object);
			$oPrev= $this->getPrev($object);

			$retVal .= '<div class="pagination pagination-right">';
			$retVal .= '<ul>';

			if (!empty($oPrev->NewsID)) {
				$retVal .= '<li>';
				$retVal .= '<a href="'.static::sGenUrl($oPrev->NewsID, $oPrev->Title).'" title="{T:Prev}">&laquo;</a>';
				$retVal .= '</li>';
			}
			else {
				$retVal .= '<li class="disabled">';
				$retVal .= '<a href="#" title="{T:Prev}">&laquo;</a>';
				$retVal .= '</li>';
			}

			if (!empty($oNext->NewsID)) {
				$retVal .= '<li>';
				$retVal .= '<a href="'.static::sGenUrl($oNext->NewsID, $oNext->Title).'" title="{T:Next}">&raquo;</a>';
				$retVal .= '</li>';
			}
			else {
				$retVal .= '<li class="disabled">';
				$retVal .= '<a href="#" title="{T:Next}">&raquo;</a>';
				$retVal .= '</li>';
			}
			$retVal .= '</ul>';
			$retVal .= '</div>';
		}
		return $retVal;
	}

	/**
	 * @return \stdClass
	 * @param \stdClass $object
	 */
	private function getNext($object = null) {

		if (empty($object)) {
			/**
			 * @var \Portal\News
			 */
			$object = $this->dataObject;
		}
		if (empty($object)) {
			return null;
		}

		$sQuery = "SELECT
		*
		FROM
		portal_news
		WHERE
		Type='news' AND
		Published='yes' AND
		Language='{$object->Language}' AND
		MainNews='no' AND
		NewsID<'{$object->NewsID}'
		ORDER BY
		NewsID DESC
		LIMIT
		1
		";

		$rStatement = Database::getPortalInstance()->execute($sQuery);

		$oRetVal = new \stdClass();

		while ($oResult = Database::getPortalInstance()->fetch($rStatement)) {
			$oRetVal = $oResult;
		}

		return $oRetVal;
	}

	/**
	 * @return \stdClass
	 * @param \stdClass $object
	 */
	private function getPrev($object = null) {

		if (empty($object)) {
			/**
			 * @var \Portal\News
			 */
			$object = $this->dataObject;
		}
		if (empty($object)) {
			return null;
		}

		$sQuery = "SELECT
		*
		FROM
		portal_news
		WHERE
		Type='news' AND
		Published='yes' AND
		Language='{$object->Language}' AND
		MainNews='no' AND
		NewsID>'{$object->NewsID}'
		ORDER BY
		NewsID ASC
		LIMIT
		1
		";

		$rStatement = Database::getPortalInstance()->execute($sQuery);

		$oRetVal = new \stdClass();

		while ($oResult = Database::getPortalInstance()->fetch($rStatement)) {
			$oRetVal = $oResult;
		}

		return $oRetVal;
	}

    /**
     * Pobranie i wyświetlenie szczegółow
     * @param array $params
     * @param Templater $template
     * @return string
     */
	public function detail($params, $template) {

		$retVal = '';

		$tObject = new self();
		$tObject->get($params['id']);

		$dataObject = $tObject->getDataObject();

		if (empty($dataObject)) {
			header("Location: missing.html",TRUE,302);
			exit();
		}

		$retVal .= $tObject->render();

		/*
		 * Jeśli wywołany ze strony głównej, nadpisz tytuł strony
		*/
		if ($params['class'] == '\Portal\News' && $params['method'] == 'detail' ) {
			$template->add('pageTitle', $tObject->getDataObject()->Title);
		}

		return $retVal;
	}

}