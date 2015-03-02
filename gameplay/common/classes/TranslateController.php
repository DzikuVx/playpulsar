<?php
class TranslateController {

    /**
     * @var Translate[]
     */
    private static $data = array();

	private static $defaultLanguage = 'en';
	private static $file = 'translations.php';

	/**
	 * Translate all {T:} occurences in string
	 * @param string $sString
	 * @return string
	 */
	static public function translate($sString) {

		$oTranslate = self::getDefault();
		
		$fTranslation = function ($matches) use ($oTranslate){

			$retval = $matches [1];
			$retval = mb_substr ( $retval, 3, - 1 );

			$retval = $oTranslate->get ( $retval );

			return $retval;
		};

		return preg_replace_callback ( '!({T:[^}]*})!', $fTranslation, $sString );
	}

	private function __construct() {

	}

    /**
     * @param $language
     * @return Translate
     */
    public static function get($language) {

		if (!isset(self::$data[$language])) {
			self::connect($language);
		}

		return self::$data[$language];
	}

	/**
	 * @return Translate
	 */
	public static function getDefault() {

		return self::get(self::$defaultLanguage);
	}

	private static function connect($language) {
		self::$data[$language] = new Translate($language, self::$file);
	}

	public static function setDefaultLanguage($language) {
		self::$defaultLanguage = $language;
	}

	public static function setFile($file) {
		self::$file = $file;
	}

}