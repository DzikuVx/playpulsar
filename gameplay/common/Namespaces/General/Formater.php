<?php

namespace General;

class Formater {

	private function __construct() {}
	
	/**
	 * Obliczenie procentów wartości max.
	 *
	 * @param int|float $value
	 * @param int|float $max
	 * @return int
	 */
	static public function sGetPercentage($value, $max) {

		if (empty ( $max )) {
			return 0;
		}

		return round ( (100 / $max) * $value );
	}

	/**
	 * Funkcja formatująca datę do postaci YYYY-MM-DD
	 * @param $date
	 * @return string
	 */
	static public function formatDate($date) {

		if (! is_numeric ( $date )) {
			$date = strtotime ( $date );
		}

		$retVal = date ( "Y-m-d", $date );
		return $retVal;
	}

	static function getmicrotime() {
		list ( $usec, $sec ) = explode ( " ", microtime () );
		return (( float ) $usec + ( float ) $sec);
	}

	/**
	 * Funkcja formatująca datę do postaci YYYY-MM-DD HH:ii
	 * @param int|string $date
	 * @return string
	 */
	static public function formatDateTime($date) {

		if (!is_numeric($date)) {
			$date = strtotime($date);
		}

		$retVal = date ( "Y-m-d H:i", $date );
		if ($retVal == '1970-01-01 01:00') {
			$retVal = null;
		}
		return $retVal;
	}

	/**
	 * Funkcja formatująca datę do postaci HH-ii
	 * @param string $date
	 * @return string
	 */
	function formatTime($date) {

		$retVal = date ( "H:i", strtotime ( $date ) );
		return $retVal;
	}

	/**
	 * Funkcja zwaracająca datę w postaci YYYY-MM-DD z UNIX Timestam
	 * @param int $date
	 * @return string
	 */
	function getDate($date) {

		$retVal = date ( "Y-m-d", $date );
		return $retVal;
	}

    /**
     * Funkcja formatująca wartość do postaci xxx xxx,xx
     * @param int|float $value
     * @param string $unit jednostka wartości
     * @return string
     */
	static public function formatValue($value, $unit = "$") {

		$retVal = number_format ( $value, 2, ",", " " ) . " " . $unit;
		return $retVal;
	}
	
	static public function formatInt($value, $unit = "$") {

		$retVal = number_format ( $value, 0, ",", " " ) . " " . $unit;
		return $retVal;
	}

	/**
	 * Formatuje liczbę
	 *
	 * @param int $value
	 * @return string
	 */
	function formatCount($value) {

		$retVal = number_format ( $value, 0, "", " " );
		return $retVal;
	}

	static public function sSuperTrim($str) {

		$char = "\n";
		while ( true ) {
			if (substr ( $str, - (strlen ( $char )) ) == $char) {
				$str = substr ( $str, 0, - (strlen ( $char )) );
			} else {
				break;
			}
		}
        return trim($str);
	}

	static public function sParseYesNo($in) {
		
		if (empty($in)) {
			return \TranslateController::getDefault()->get('no');
		}else {
			return \TranslateController::getDefault()->get('yes');
		}
		
	}
	
}
