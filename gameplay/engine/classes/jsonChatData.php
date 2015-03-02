<?php
/**
 * Wpis na czacie
 *
 * @version $Rev: 446 $
 * @package Engine
 */
class jsonChatData {
	public $State = 0;
	public $Count = 0;
	public $LastID = 0;
	public $Data = array ();
	
	public function reverse() {
		$this->Data = array_reverse ( $this->Data );
	}
	
	public function push($text, $id) {
		array_push ( $this->Data, $text );
		$this->Count = $this->Count + 1;
		if ($id > $this->LastID) {
			$this->LastID = $id;
		}
	}

}