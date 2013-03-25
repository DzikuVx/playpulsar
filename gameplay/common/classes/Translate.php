<?php
/**
 *
 * @author Paweł
 * coś mi nie pasi gdy trzeba mieć dwie instancje
 */
class Translate implements ArrayAccess {

	private $language;
	private $table;

	static public $useCache = false;

	/**
	 * Konstruktor
	 *
	 * @param string $language
	 */
	public function __construct($language, $file = 'translations.php') {

		$this->language = $language;

		if (!self::$useCache || ! \Cache\Controller::getInstance()->check ( 'translationList', $this->language )) {
			require dirname ( __FILE__ ).'/../../engine/'.$file;
			
			$this->table = $translationTable [$this->language];
			unset ( $translationTable );

			if (self::$useCache) {
				\Cache\Controller::getInstance()->set ( 'translationList', $this->language, $this->table, 86400 );
			}
		} else {
			$this->table = \Cache\Controller::getInstance()->get ( 'translationList', $this->language );
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

	public function offsetSet($offset, $value) {
		$this->table[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->table[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->table[$offset]);
	}

	public function offsetGet($offset) {

		if (isset($this->table[$offset])) {
			return $this->table[$offset];
		}else {
			return false;
		}

	}

}