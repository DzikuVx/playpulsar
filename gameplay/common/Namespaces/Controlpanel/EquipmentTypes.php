<?php

namespace Controlpanel;

use \General\Controls as Controls;

class EquipmentTypes extends GameplayItem{

	protected $detailTitle = 'Equipment Data';
	protected $editTitle = '';
	protected $addTitle = '';

	protected $tableName = 'equipmenttypes';
	protected $tableIdField = 'EquipmentID';

	/**
	 * Edycja, wykonanie
	 * @param user $user
	 * @param array $params
	 * @throws customException
	 */
	public function editExe($user, $params) {
		$retVal = '';

		if (empty($_SESSION['returnUser'])) {
			throw new \customException('Security error');
		}

		$tData = $this->getDataObject($params['id']);

		$tFields = array();
		foreach ($tData as $tKey => $tValue) {

			if ($tKey == 'Unique') {
				$tKey = '"Unique"';
			}

			if ($tKey != 'EquipmentID') {
				array_push($tFields, $tKey);
			}
		}

		$tQuery = BaseItem::sMakeUpdateQuery('equipmenttypes', 'EquipmentID', $tFields, $params);
		\Database\Controller::getInstance()->execute($tQuery);
		\Cache\Controller::getInstance()->clear('equipment',$params['id']);

		$retVal .= Controls::sUiDialog( "Confirmation", "Data has been <strong>set</strong>", "document.location='{$_SESSION['returnLink']}'");

		return $retVal;
	}

	static public function sRandomize() {

		$tQuery = "SELECT * FROM porttypes WHERE Type='station'";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		while ($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {

			$tEq1 = rand(1,16);
			$tEq2 = $tEq1;

			while ($tEq1 == $tEq2) {
				$tEq2 = rand(1,16);
			}

			\Database\Controller::getInstance()->execute("UPDATE porttypes SET Equipment='{$tEq1},{$tEq2}' WHERE PortTypeID='{$tResult->PortTypeID}'");
		}
	}

	protected function getDataObject($itemID) {

		$tQuery = \Database\Controller::getInstance()->execute ( "
				SELECT 
					*
				FROM 
					equipmenttypes  
				WHERE 
					EquipmentID='{$itemID}' LIMIT 1" );
		return \Database\Controller::getInstance()->fetch ( $tQuery );
	}

	static private function sStationListData($id) {
		$retVal = array();

		$tQuery = "SELECT
					porttypes.PortTypeID,
					porttypes.NameEN,
					ports.System,
					ports.X,
					ports.Y
				FROM
					porttypes JOIN ports USING(PortTypeID)
				WHERE
					porttypes.Type='station' AND
					CONCAT(',',porttypes.Equipment,',') LIKE '%,{$id},%'
		";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		while($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {
			$tArray = array();
			$tArray['Id'] = $tResult->PortTypeID;
			$tArray['Name'] = $tResult->NameEN.' - '.$tResult->System.'/'.$tResult->X.'/'.$tResult->Y;
			array_push($retVal, $tArray);
		}

		return $retVal;
	}

	/**
	 * Pobranie tablicy stacji w których jest dostępny dany item
	 * @param int $id
	 * @param bool $cacheAble
	 * @return array
	 */
	static public function sGetStationList($id, $cacheAble = true) {

		$retVal = array();

		$module = 'cpEquipmentTypes::sGetStationList';
		$property = $id;

		if ($cacheAble && \Cache\Controller::getInstance()->check($module, $property)) {
			$retVal = \Cache\Controller::getInstance()->get($module, $property);
		}else {
			$retVal = self::sStationListData($id);
			\Cache\Controller::getInstance()->set($module, $property, $retVal);
		}

		return $retVal;
	}

	protected function getAdditionalData($user, $params) {
		$retVal = '';

		$retVal .= '<h2>Avaible at Stations</h2>';

		$tArray = self::sGetStationList($params['id'], false);

		$retVal .= Controls::sBuilUl($tArray);

		return $retVal;
	}

}