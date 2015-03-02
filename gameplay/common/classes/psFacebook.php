<?php
/**
 * Wrapper dla PHP Facebook API
 * @author Paweł
 * @see Facebook
 */
class psFacebook {

	/**
	 * Obiekt klasy Facebook
	 * @var Facebook
	 */
	private $fbObject = null;

	/**
	 * Obiekt aktualnej sesji użytkownika
	 */
	private $fbSession = null;

	/**
	 * Tablica konfiguracyjna
	 * @var array
	 */
	private $config = null;

	/**
	 * Pobranie obiektu facebook
	 * @return Facebook
	 */
	public function getFbObject() {
		return $this->fbObject;
	}

	/**
	 * Pobranie sesji
	 * @return array
	 */
	public function getSessionObject() {
		return $this->fbSession;
	}

	private $appName = 'Pulsar Online';
	private $appLink = 'http://playpulsar.com';
	private $appDescription = 'Free Sci-Fi browser MMORPG';
	private $appIcon = 'http://pl.playpulsar.com/gfx/logo3.png';

	/**
	 * Ustawienie danych aplikacji FB
	 * @param string $appName
	 * @param string $appLink
	 * @param string $appDescription
	 * @param string $appIcon
	 */
	public function setAppData($appName, $appLink, $appDescription, $appIcon) {
		$this->appDescription = $appDescription;
		$this->appLink = $appLink;
		$this->appIcon = $appIcon;
		$this->appName = $appName;
	}
	
	/**
	 * Konstruktor
	 * @param array $config
	 */
	public function __construct($config) {

		$this->config = $config;
		$this->connect();
	}

	/**
	 * Inicjalizacja połączenia z FB
	 * @throws psFacebookException
	 */
	private function connect() {

		if (!empty($this->fbObject)) {
			throw new psFacebookException('Already connected', 1);
		}

		$this->fbObject = new Facebook(array(
 			 	'appId'  => $this->config['appId'],
  			'secret' => $this->config['secret'],
  			'cookie' => $this->config['cookie']
		));

		if (empty($this->fbObject)) {
			throw new psFacebookException('Unable to get facebook object', 2);
		}
	}

	/**
	 * Pobranie danych sesji z API
	 * @return array
	 */
	public function getSession() {
		$this->fbSession = $this->fbObject->getSession();
		return $this->fbSession;
	}

	/**
	 * Rozłączenie
	 */
	public function disconnect() {
		unset($this->fbObject);
	}

	/**
	 * Wykonanie FQL'a
	 * @param string $query
	 * @param string $callback
	 * @return array
	 */
	private function executeFql($query, $callback = '') {
		$param  =   array(
 			'method'    => 'fql.query',
 			'query'     => $query,
 			'callback'  => $callback
		);

		$tData = $this->fbObject->api($param);

		return $tData;
	}

	/**
	 * Sprawdzenie uprawnienia przyznanego apliakcji
	 * @param string $perm_name
	 * @param int $userID - If not set, me() i used
	 * @return bool
	 */
	public function checkPermission($perm_name, $userID = 'me()') {

		$fql    =   "SELECT {$perm_name} FROM permissions WHERE uid={$userID}";

		$tData = $this->executeFql($fql);

		if (!empty($tData[0][$perm_name])) {
			$tData = true;
		}else {
			$tData = false;
		}

		return $tData;
	}

	/**
	 * Opublikowanie postu na ścianie użytkownika
	 * @param string $message
	 * @param mixed $userID
	 * @param bool $sendAppData
	 * @return string
	 */
	public function wallPost($message = null, $userID = 'me', $sendAppData = false) {

		$tArray = array(
				"message"=> $message,
				'uid' => $this->config['appId'],
				'target_id' => $userID
		);

		if ($sendAppData) {
			$tArray['name'] = $this->appName;
			$tArray['link'] = $this->appLink;
			$tArray['description'] = $this->appDescription;
			$tArray['picture'] = $this->appIcon;
		}
		$data = $this->fbObject->api("/{$userID}/feed", "post", $tArray);
		return $data;
	}

	/**
	 * Pobranie danych odostępnionych przez użytkownika
	 * @param mixed $userID
	 */
	public function getUserData($userID = '/me') {

		$retVal = null;
		$retVal = $this->fbObject->api($userID);

		return $retVal;
	}

	/**
	 * Pobranie danych użytkownika zalogowanego do aplikacji
	 * @throws psFacebookException
	 * @return array
	 */
	public function getMe() {

		$this->getSession();

		if (empty($this->fbSession)) {
			throw new psFacebookException('Session empty, user did not authorized app to access', 3);
		}

		$retVal = $this->getUserData('/me');

		if (empty($retVal)) {
			throw new psFacebookException('Error while getting users data', 4);
		}

		return $retVal;
	}

}

class psFacebookException extends Exception {

}