<?php

/*
 * Klasa wiadomości do wszystkich graczy wyświetlana na głównym ekranie gry
*/
class gameplayMessage {

	static private function set(array $data) {

		\phpCache\Factory::getInstance()->create()->set(new \phpCache\CacheKey('globalMessage', 'all'), $data, 86400);
		return true;
	}

	static private function get() {
		return \phpCache\Factory::getInstance()->create()->get(new \phpCache\CacheKey('globalMessage', 'all'));
	}

	public static function write($type, $text) {

		$data = array();
		$data['type'] = $type;
		$data['text'] = $text;

		self::set($data);
	}

	static public function getRaw() {
		$tData = self::get();

		if (empty($tData['type'])) {
			$tData['type'] = 'info';
		}
		if (empty($tData['text'])) {
			$tData['text'] = '';
		}
		return $tData;
	}

	static public function populate($announcement) {

		$tData = self::get();

		if (!empty($tData['text'])) {
			$announcement->write($tData['type'], $tData['text']);
		}

	}

}