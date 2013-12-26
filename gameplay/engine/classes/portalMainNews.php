<?php
/**
 * Klasa newsa głównego
 * @version $Rev: 457 $
 * @package Portal
 *
 */
class portalMainNews extends baseItem{

	function __construct($language = 'pl', $defaultAction = null) {

		$this->language = $language;

		if ($defaultAction != null) {
			$this->{$defaultAction};
		}
	}

	/**
	 * Pobranie newsa głównego
	 *
	 */
	function get($ID = null) {

		$oCacheKey = new \phpCache\CacheKey('portalMainNews', $this->language);
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (!$oCache->check($oCacheKey)) {

			$query = "
			SELECT
			portal_news.Time AS Time,
			portal_news.Title AS Title,
			portal_news.Text AS Text,
			portal_news.UserName AS Name
			FROM
			portal_news
			WHERE
			portal_news.Published='yes' AND
			portal_news.Language='{$this->language}' AND
			portal_news.MainNews='yes'
			LIMIT 1
			";

			$result = \Database\Controller::getPortalInstance()->execute ( $query );
			$this->dataObject = \Database\Controller::getPortalInstance()->fetch ( $result );

			$oCache->set($oCacheKey, $this->dataObject);

		}else {
			$this->dataObject = $oCache->get($oCacheKey);
		}
		return true;
	}

	/**
	 * Wyświetlenie newsa głównego
	 *
	 */
	function render() {
		$this->get ();

		if ($this->dataObject == null)
			return null;

		return $this->dataObject->Text;
	}
}
