<?php

namespace Portal;

use \Cache\Controller as Cache;
use \Database\Controller as Database;

class Menu {

	/**
	 * Pobranie listy artykułów z bazy danych
	 * @param array $tList
	 * @param array $params
	 */
	static private function sGetArticles(&$tList, $params) {

		self::sGetConstantArticles($tList, $params);

		$tQuery = "SELECT * FROM portal_news WHERE Type='article' AND Published='yes' AND Language='{$params['language']}'";
		$tQuery = Database::getPortalInstance()->execute($tQuery);
		while ($tResult = Database::getPortalInstance()->fetch($tQuery)) {
			$tObject = new MenuObject();
			$tObject->link = Article::sGenUrl($tResult->NewsID, $tResult->Title);
			$tObject->title = $tResult->Title;
			$tObject->description = $tResult->Title;
			array_push($tList, clone $tObject);
		}

	}

	/**
	 * Pobranie stałej listy linków w menu głównym
	 * @param array $tList
	 * @param array $params
	 */
	static private function sGetConstantArticles(&$tList, $params) {

		$tObject = new MenuObject();

		switch ($params['language']) {
			case 'pl':
				$tObject->description = 'Pomoc do gry';
				$tObject->link = 'http://pl.guide.playpulsar.com/';
				$tObject->title = 'Pomoc';
				break;

			default:
			case 'en':
				$tObject->description = 'Help and user gruide';
				$tObject->link = 'http://en.guide.playpulsar.com/';
				$tObject->title = 'User Guide';
				break;
		}

		array_push($tList, $tObject);

		$tObject = new MenuObject();
		$tObject->description = 'Forum';
		$tObject->link = 'http://board.playpulsar.com/';
		$tObject->title = 'Forum';
		array_push($tList, $tObject);

	}

	static private function sSortMe($a, $b) {
		return strcmp($a->title, $b->title);
	}

	static private function sRenderList($tList, $params) {
		$retVal = '';

		$tNumber = 0;
		foreach ($tList as $tValue) {
			$retVal .= $tValue->render($tNumber);
			$tNumber++;
		}

		return $retVal;
	}

	public static function render($params) {

		$oCacheKey = new \Cache\CacheKey('menuNavigator::sRender', $params['language']);
		
		if (!Cache::getInstance()->check($oCacheKey)) {

			$retVal = '';

			$tList = array();

			self::sGetArticles($tList, $params);

			usort($tList, "\Portal\Menu::sSortMe");

			$retVal = self::sRenderList($tList, $params);
			Cache::getInstance()->set($oCacheKey, $retVal, 86400);
		}else {
			$retVal = Cache::getInstance()->get($oCacheKey);
		}

		return $retVal;
	}

}
