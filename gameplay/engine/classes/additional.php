<?php

class additional {
	
	/**
	 * Zwraca losową pozycję z listy oddzielonej przecinkami
	 *
	 * @param string $list
	 * @return string
	 * @static 
	 */
	static function randFormList($list) {
		
		$tList = explode ( ",", $list );
		$tCount = count ( $tList );
		$tKeys = array_keys ( $tList );
		return $tList [$tKeys [rand ( 0, $tCount - 1 )]];
	}
	
	/**
	 * Funkcja losująca liczbę
	 *
	 * @param int $min
	 * @param int $max
	 * @return int
	 * @static 
	 */
	static function rand($min, $max) {
		
		return rand ( $min, $max );
	}
	
	/**
	 * Losowanie imienia i nazwiska z bazy danych
	 *
	 * @return string
	 */
	static function getRandomName() {

        $npcName = '';

		$tQuery2 = "SELECT Name FROM names WHERE Type='first' ORDER BY RAND() LIMIT 1";
		$tQuery2 = \Database\Controller::getInstance()->execute ( $tQuery2 );
		while ( $row2 = \Database\Controller::getInstance()->fetch ( $tQuery2 ) ) {
			$npcName = $row2->Name;
		}
		
		$tQuery2 = "SELECT Name FROM names WHERE Type='last' ORDER BY RAND() LIMIT 1";
		$tQuery2 = \Database\Controller::getInstance()->execute ( $tQuery2 );
		while ( $row2 = \Database\Controller::getInstance()->fetch ( $tQuery2 ) ) {
			$npcName .= " " . $row2->Name;
		}
		
		return $npcName;
	}
	
	/**
	 * Randomizacja wartości
	 *
	 * @param int $value - wartość oryginalna
	 * @param int $range - zakres +/- w %
	 * @param int $threshold - dzielnik (100,1000, etc)
	 * @return int
	 */
	static function randomizeValue($value, $range, $threshold) {
		
		if ($value == 0)
			return 0;
		
		$tValue = floor ( $value / $threshold );
		$tRange = floor ( ($tValue / 100) * $range );
		$tValue = rand ( $tValue - $tRange, $tValue + $tRange );
		$tValue = $tValue * $threshold;
		
		if ($tValue < 0)
			$tValue = 0;
		
		return $tValue;
	}
	
	/**
	 * Sparwdzenie, czy losowy warunek został spełniony
	 *
	 * @param int $prc - np 50
	 * @param int $base - np 100
	 * @return boolean
	 */
	static function checkRand($prc, $base) {
		
		$out = false;
		if (rand ( 1, $base ) <= $prc)
			$out = true;
		return $out;
	}

}
