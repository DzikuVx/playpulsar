<?php

/**
 * Klasa wpisów na chacie
 *
 * @version $Rev: 440 $
 * @package Engine
 */
class chatEntry {
	protected $Date = null;
	protected $Author = '';
	protected $Text = '';

	public function __construct($Date, $Author, $Text) {
		$this->Date = $Date;
		$this->Author = $Author;
		$this->Text = $this->quote($Text);
	}

	public function render() {

		$retVal = '[' . date ( 'H:i', $this->Date ) . '] ' . $this->Author . ': ' . $this->Text;

		return $retVal;
	}

	public function save() {
		$chatDb = \Database\Controller::getChatInstance();

		$tQuery = "INSERT INTO chatglobal(Data) VALUES('" . $chatDb->quote ( $this->serialize () ) . "')";
		$chatDb->executeAndRetryOnDeadlock ( $tQuery );

	}

	protected function quote($text) {

		$retVal = strip_tags($text);
		$retVal = htmlspecialchars($retVal);

		return $retVal;
	}

	protected  function serialize() {
		return serialize ( $this );
	}

}