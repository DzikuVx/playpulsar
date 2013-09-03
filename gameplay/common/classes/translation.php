<?php
/**
 * Klasa realizująca tłumaczenia
 *
 * @version $Rev: 456 $
 * @package Common
 */
class translation {
	protected $language;
	protected $table;

	static public $useCache = false;

	/**
	 * Konstruktor
	 *
	 * @param string $language
	 */
	function __construct($language, $file = 'engine/translations.php') {

		$oCacheKey = new \Cache\CacheKey('translationList', $this->language);
		
		if (!translation::$useCache || ! \Cache\Controller::getInstance()->check ($oCacheKey)) {
			require $file;
			$this->table = $translationTable [$this->language];
			unset ( $translationTable );

			if (translation::$useCache) {
				\Cache\Controller::getInstance()->set ( $oCacheKey, $this->table, 86400 );
			}
		} else {
			$this->table = \Cache\Controller::getInstance()->get ( $oCacheKey );
		}

	}

	/**
	 * Pobranie tłumaczenia
	 *
	 * @param string $string
	 * @return string
	 */
	function get($string) {

		if (isset ( $this->table [$string] )) {
			return $this->table [$string];
		} else {
			return $string;
		}
	}
}