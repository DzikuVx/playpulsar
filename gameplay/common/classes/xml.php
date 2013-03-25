<?php

/**
 * Klasa prostej obsługi XML
 *
 * @version $Rev: 181 $
 * @package Common
 * @deprecated
 */
class xml {

	protected $xml;

	/**
	 * Naprawa znaków HTML
	 *
	 * @param string $xml
	 * @return string
	 * @deprecated
	 */
	function fixSpecialChars($xml) {

		$xml = htmlspecialchars ( $xml );
		return $xml;
	}

	/**
	 * Ustawienie nowej wartości
	 *
	 * @param string $tag
	 * @param string $value
	 * @return boolean;
	 * @deprecated
	 */
	function setValue($tag, $value) {

		$start_mark = "<" . $tag . ">";
		$end_mark = "</" . $tag . ">";

		$this->xml .= $start_mark . $value . $end_mark;

		return true;
	}

	public function dummy() {

		return true;
	}

	/**
	 * @deprecated
	 */
	static public function sGetValue($str, $start_mark, $end_mark) {

		$out = null;
		$start = strpos ( $str, $start_mark );
		$stop = strpos ( $str, $end_mark );
		if (($start !== false) and ($stop !== false)) {
			$out = substr ( $str, $start + strlen ( $start_mark ), $stop - $start - strlen ( $start_mark ) );
		}
		return $out;
	}

	/**
	 * Pobranie wartości
	 *
	 * @param string $tag
	 * @return string
	 * @deprecated
	 */
	function getValue($tag) {

		$out = null;

		$start_mark = "<" . $tag . ">";
		$end_mark = "</" . $tag . ">";

		$start = strpos ( $this->xml, $start_mark );
		$stop = strpos ( $this->xml, $end_mark );

		if (($start !== false) and ($stop !== false)) {
			$out = substr ( $this->xml, $start + strlen ( $start_mark ), $stop - $start - strlen ( $start_mark ) );
		}

		return $out;
	}

	/**
	 * Konstruktor
	 *
	 * @param string $xml
	 * @deprecated
	 */
	function __construct($xml) {

		$xml = str_replace ( "'", "\'", $xml );

		$this->xml = $xml;
	}

	/**
	 * Pobranie pełnego xml
	 *
	 * @return string
	 * @deprecated
	 */
	public function getRawData() {

		return $this->xml;
	}

}
?>