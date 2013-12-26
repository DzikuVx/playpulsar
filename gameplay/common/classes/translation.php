<?php
/**
 * Klasa realizująca tłumaczenia
 *
 * @version $Rev: 456 $
 * @package Common
 * @deprecated
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

		$this->language = $language;
		
		$oCacheKey = new \phpCache\CacheKey('translationList', $this->language);
        $cache     = \phpCache\Factory::getInstance()->create();

		if (!translation::$useCache || ! $cache->check ($oCacheKey)) {
			require $file;
            /** @noinspection PhpUndefinedVariableInspection */
            $this->table = $translationTable [$this->language];
			unset ( $translationTable );

			if (translation::$useCache) {
				$cache->set ( $oCacheKey, $this->table, 86400 );
			}
		} else {
			$this->table = $cache->get ( $oCacheKey );
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