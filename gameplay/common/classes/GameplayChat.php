<?php

/**
 * Chat wewnętrzny gameplay
 * @author Paweł
 * @package gameplay-addons
 * @example echo GameplayChat::getInstance()->controller();
 */
class GameplayChat {

	/**
	 * Obiekt klasy -> Singleton
	 * @var GameplayChat
	 */
	static private $instance = null;

	/**
	 * Serwer mongoDB
	 * @var Mongo
	 */
	private $server = null;

	/**
	 * Baza danych
	 * @var MongoDB
	 */
	private $db = null;

	/**
	 * Kolekcja chatu
	 * @var MongoCollection
	 */
	private $collection = null;

	/**
	 * Adres serwera bazy danych
	 * @var string
	 */
	static private $serverAddress = '10.0.0.190';

	/**
	 * Port serwera bazy danych
	 * @var int
	 */
	static private $serverPort = 27017;

	/**
	 * Nazwa bazy danych
	 * @var string
	 */
	static private $dbName = 'PulsarGlobalChat';

	/**
	 * Nazwa kolekcji
	 * @var string
	 */
	static private $collenctionName = 'Entries';

	/**
	 * Konstruktor
	 */
	private function __construct() {
		$this->connect();
	}

	/**
	 * Podłączenie do bazy danych
	 */
	private function connect() {

		/**
		 * Połącz do serwera
		 */
		$this->server = new Mongo(self::$serverAddress.":".self::$serverPort);

		if (empty($this->server)) {
			throw new Exception('Error while connecting to mongoDB server');
		}

		/*
		 * Połącz do bazy danych
		*/
		$this->db = $this->server->selectDB(self::$dbName);

		if (empty($this->db)) {
			throw new Exception('Error while connecting to mongoDB database');
		}

		/*
		 * Wybierz kolekcję
		*/
		$this->collection = $this->db->selectCollection(self::$collenctionName);

		if (empty($this->collection)) {
			throw new Exception('Error while connecting to mongoDB collection');
		}

	}

	/**
	 * Konstruktor statyczny
	 * @return GameplayChat
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Wstawienie wpisu do chatu
	 * @param string $userName
	 * @param string $message
	 * @param int $userID
	 * @throws Exception
	 */
	private function write($userName = 'Test User', $message = 'Test Message', $userID = null) {

		/*
		 * Przygotuj tablicę wpisu
		*/
		$entryData = array();
		$entryData['ctime'] = microtime(true)*10000000;
		$entryData['time'] = time();
		$entryData['user'] ['id'] = $userID;
		$entryData['user'] ['name'] = $userName;
		$entryData['message'] = $message;

		/*
		 * Wstaw
		*/
		$result = $this->collection->insert($entryData, array('safe' => true, "upsert" => true));

		/*
		 * W przypadku będu wstawienia, rzuć wyjątkiem
		*/
		if (empty($result['ok'])) {
			throw new Exception($result['err'], $result['code']);
		}

	}

	/**
	 * Pobranie wpisów
	 * @param string $syncTime
	 * @param int $count
	 * @throws Exception
	 * @return MongoCursor
	 */
	private function get($syncTime = null, $count = 30) {

		/*
		 * Walidacja atrybutu
		*/
		if (!is_int($count)) {
			throw new Exception('Count attribute must be integer');
		}

		if ($count < 1) {
			throw new Exception('Count attribute must be bigger than 1');
		}

		if ($count > 100) {
			throw new Exception('Count attribute must be lower than 100');
		}

		if (!is_numeric($syncTime) && $syncTime !== null) {
			throw new Exception('syncTime attribute must be numeric');
		}

		/**
		 * Pobierz posortowany cursor
		 */
		if (empty($syncTime)) {
			$cursor = $this->collection->find();
		}else {
			$cursor = $this->collection->find(array('ctime' => array('$gt' => $syncTime)));
		}

		$cursor = $cursor->sort(array('_id' => -1))->limit($count);

		return $cursor;
	}

	/**
	 * formatowanie pojedynczego wiersza
	 * @param array $result
	 * @return string
	 */
	private function formatEntry($result) {
		return '[' . date ( 'H:i', $result['time'] ) . '] ' . $result['user'] ['name'] . ': ' . $result['message'];
	}

	/**
	 * Przygotowanie obiektu do wyłania do przeglądarki
	 * @param MongoCursor $cursor
	 * @return GameplayChatReply
	 */
	private function prepareOutput($cursor) {

		$retVal = new GameplayChatReply();

		foreach ($cursor as $result) {
			$retVal->push($this->formatEntry($result), number_format($result['ctime'],0,'',''));

		}

		return $retVal;

	}

	/**
	 * Quotowanie wiadomości w chacie
	 * @param string $text
	 * @return string
	 */
	private function quote($text) {
		return htmlspecialchars(strip_tags($text));
	}
	
	/**
	 * Kontroler
	 * @return string
	 * @throws Exception
	 */
	public function controller() {

		try {

			/*
			 * Sprawdzenie zalogowania użytkownika
		 */
			if (empty($_SESSION ['userID'])) {
				throw new Exception('User not authorized',403);
			}

			if (!isset($_REQUEST['action'])) {
				$_REQUEST['action'] = null;
			}

			$retVal = '';

			switch ($_REQUEST['action']) {
					
				/*
				 * Pobranie listy wiadomości z serwera 
				 */
				case 'get':

					if (!isset($_REQUEST['cTime'])) {
						$_REQUEST['cTime'] = null;
					}

					$retVal = json_encode($this->prepareOutput($this->get((float) $_REQUEST['cTime'])));

					break;

					/*
					 * Zapisanie wiadomości na serwerze
					 */
				case 'save':

					if (!isset($_REQUEST['message'])) {
						$_REQUEST['message'] = '';
					}

					/*
					 * Zapisz wiadomość do bazy
					 */
					$this->write($_SESSION ['userName'], $this->quote($_REQUEST['message']), $_SESSION ['userID']);
					
					$retVal = json_encode(new GameplayChatReply());
						
					break;

			}

			return $retVal;

		}catch (Exception $e) {

			/*
			 * Przygotuj komunikat błędu wraz ze state
			*/
			$retVal = new GameplayChatReply();
			$retVal->state = $e->getCode();
			$retVal->message = $e->getMessage();
				
			return json_encode($retVal);
		}

	}

}

/**
 * Klasa odpowiedzi serwera czatu
 * @author Paweł
 * @see GameplayChat
 */
class GameplayChatReply {

	/**
	 * Status odpowiedzi serwera
	 * @var int
	 */
	public $state = 0;

	/**
	 * Ew. komunikat błędu
	 * @var string
	 */
	public $message = '';

	/**
	 * Liczba wpisów
	 * @var int
	 */
	public $count = 0;

	/**
	 * cTime ostatniej odpowiedzi
	 */
	public $cTime = 0;

	/**
	 * tablica wpisów
	 * @var array
	 */
	public $data = array ();

	public function push($text, $entryTime) {
		array_push ( $this->data, $text );
		$this->count = $this->count + 1;
		if ($entryTime > $this->cTime) {
			$this->cTime = $entryTime;
		}
	}

}