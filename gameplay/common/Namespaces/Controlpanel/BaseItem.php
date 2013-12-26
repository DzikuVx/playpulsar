<?php

namespace Controlpanel;

use \General\Controls as Controls;

abstract class BaseItem {

	//@todo some kind of general usage auto form generator

	protected $db;
	protected $tableName = null;
	protected $tableIdField = null;
	protected $templateFileName = null;
	protected $itemID = null;

	/**
	 * Zwrócenie oryginału dataObject
	 *
	 * @return stdClass
	 */
	public function giveDataObject() {

		return $this->dataObject;
	}

	/**
	 * Zwrócenie kopii dataObject
	 *
	 * @return stdClass
	 */
	public function takeDataObject() {

		return clone $this->dataObject;
	}

	/**
	 *
	 * Pobranie elementu z bazy danych
	 * @param int $itemID
	 * @return stdClass
	 */
	protected function getDataObject($itemID) {

		$tQuery = $this->db->execute ( "SELECT
				*
				FROM
				{$this->tableName}
				WHERE
				{$this->tableIdField}='{$itemID}' LIMIT 1" );
				return $this->db->fetch ( $tQuery );
	}


	protected function assignDbObject() {
		$this->db = \Database\Controller::getInstance();
	}

	/**
	 * Konstruktor
	 *
	 * @param int $itemID
	 */
	function __construct($itemID = null) {

		$this->assignDbObject();

		$this->itemID = $itemID;

		if ($this->itemID != null) {
			$this->dataObject = $this->getDataObject ( $this->itemID );
		}

		$this->prepare ();

	}

	protected function loadDataObject($id = null) {

		if (empty($id)) {
			$id = $this->itemID;
		}

		if (!empty($id)) {
			$this->dataObject = $this->getDataObject($id);
		}
	}

	/**
	 * DUMMY
	 *
	 */
	protected function prepare() {

	}

	/**
		* Czy zawartość obiektu została zmodyfikowana
		*
		* @var boolean
		*/
	protected $modified = false;

	/**
    * Date obiektu
    *
    * @var \stdClass
    */
	protected $dataObject = null;

	public function dummy() {

		return true;
	}

	/**
	 * Przeglądanie rejestru
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function browse($user, $params) {

		$className = get_class ( $this ) . "Registry";

		$item = new $className ( $this->db );
		$retVal = $item->browse ( $user, $params );

		return $retVal;
	}

	/**
	 * Tytuł strony
	 *
	 * @param string $text
	 * @return string
	 */
	protected function renderTitle($text) {

		return "<h1>{$text}</h1>";
	}

	/**
	 * Otwarcie formularza
	 *
	 * @return string
	 */
	protected function openForm() {

		return Controls::sOpenForm(get_class($this));
	}

	/**
	 * Zamknięcie formularza
	 *
	 * @return string
	 */
	protected function closeForm() {

		return Controls::sCloseForm();
	}

	/**
	 *
	 * Dialog usuwania elementu
	 * @param user $user
	 * @param array $params
	 * @throws customException
	 * @return string
	 * @since 2010-12-05
	 */
	public function delete($user, $params) {

	 	global $config;

	 	//@todo ustawić prawa dostępu w CP
	 	return Controls::dialog( "Confirm", "Do you want to delete selected element?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=deleteExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	/**
	 * Usunięcie elementu
	 *
	 * @param users $user
	 * @param xml $xml
	 * @return string
	 * @throws customException
	 * @since 2010-12-05
	 */
	public function deleteExe($user, $params) {

		//@todo ustawić prawa dostępu w CP

		$this->db->execute ( "DELETE FROM {$this->tableName} WHERE {$this->tableIdField}='" . $params ['id'] . "'" );

		global $config;

		Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=browse&clearSearch=true", "Selected element has been deleted");
	}

	static public function sMakeUpdateQuery($tableName, $tableID, $tableUseFields, $params) {

		$tQuery = "UPDATE {$tableName} SET ";

		$tIsPrevious = false;

		foreach ($tableUseFields as $tField) {
			if (isset($params[$tField])) {

				if ($tIsPrevious) {
					$tQuery .= ', ';
				}

				$tQuery .= " {$tField}='{$params[$tField]}' ";

				$tIsPrevious = true;

			}
		}

		$tQuery .= " WHERE {$tableID} = '{$params['id']}' LIMIT 1";

		return $tQuery;
	}

	static public function sRenderEditForm($object, $data, $id, $masterClass = null) {

		if (empty($masterClass)) {
			$masterClass = get_class($object);
		}

		$retVal = '';
		$retVal .= Controls::sOpenForm(get_class($object));
		$retVal .= Controls::sBuilEditTable($data, 3);

		$retVal .= Controls::renderInput('hidden',$masterClass,'class');
		$retVal .= Controls::renderInput('hidden',$id,'id');
		$retVal .= Controls::renderInput('hidden','editExe','method');
		$retVal .= Controls::sCloseForm();
		$retVal .= "<p>";
		$retVal .= Controls::bootstrapButton ( "Save", "document.myForm.onsubmit();",'btn-primary','icon-ok');
		$retVal .= Controls::bootstrapButton ( "Cancel", "window.history.back();",'btn-inverse','icon-off');
		$retVal .= "</p>";


		return $retVal;
	}

}