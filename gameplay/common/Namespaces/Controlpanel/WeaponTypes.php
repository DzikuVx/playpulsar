<?php

namespace Controlpanel;

use \General\Controls as Controls;

class WeaponTypes extends GameplayItem{

	protected $detailTitle = 'Weapon Data';
	protected $editTitle = '';
	protected $addTitle = '';

	protected $tableName = 'weapontypes';
	protected $tableIdField = 'WeaponID';

	public function edit($user, $params) {
		$retVal = '';

		$tData = $this->getDataObject($params['id']);

		$retVal = '';
		$retVal .= Controls::sOpenForm('weapontypes');
		$retVal .= Controls::sBuilEditTable($tData, 2);

		$retVal .= Controls::renderInput('hidden',get_class($this),'class');
		$retVal .= Controls::renderInput('hidden',$params['id'],'id');
		$retVal .= Controls::renderInput('hidden','editExe','method');
		$retVal .= Controls::sCloseForm();
		$retVal .= "<p>";
		$retVal .= Controls::bootstrapButton ( "Save", "document.myForm.onsubmit();",'btn-primary','icon-ok');
		$retVal .= Controls::bootstrapButton ( "Cancel", "window.history.back();",'btn-inverse' ,'icon-off');
		$retVal .= "</>";

		return $retVal;
	}

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
			if ($tKey != 'WeaponID' && $tKey!='ClassName') {
				array_push($tFields, $tKey);
			}
		}

		$tQuery = BaseItem::sMakeUpdateQuery('weapontypes', 'WeaponID', $tFields, $params);
		\Database\Controller::getInstance()->execute($tQuery);
		\Cache\Controller::getInstance()->clear('weapon',$params['id']);

		$retVal .= Controls::sUiDialog( "Confirmation", "Data has been <strong>set</strong>", "document.location='{$_SESSION['returnLink']}'");

		return $retVal;
	}

	protected function getDataObject($itemID) {

		$tQuery = \Database\Controller::getInstance()->execute ( "
				SELECT 
					weapontypes.*, 
					weaponclasses.NameEN AS ClassName 
				FROM 
					weapontypes LEFT JOIN weaponclasses USING(WeaponClassID) 
				WHERE 
					WeaponID='{$itemID}' LIMIT 1" );
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
					CONCAT(',',porttypes.Weapons,',') LIKE '%,{$id},%'
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

	static private function sNpcListData($id) {
		$retVal = array();

		$tQuery = "SELECT
					NPCTypeID,
					Name,
					Systems
				FROM
					npctypes
				WHERE
					CONCAT(',',Weapons,',') LIKE '%,{$id},%'
		";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		while($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {
			$tArray = array();
			$tArray['Id'] = $tResult->NPCTypeID;
			$tArray['Name'] = $tResult->Name.' ['.$tResult->Systems.']';
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

		$module = 'cpWeaponTypes::sGetStationList';
		$property = $id;

		if ($cacheAble && \Cache\Controller::getInstance()->check($module, $property)) {
			$retVal = \Cache\Controller::getInstance()->get($module, $property);
		}else {
			$retVal = self::sStationListData($id);
			\Cache\Controller::getInstance()->set($module, $property, $retVal);
		}

		return $retVal;
	}

	static public function sGetNpcList($id, $cacheAble = true) {

		$retVal = array();

		$module = 'cpWeaponTypes::sGetNpcList';
		$property = $id;

		if ($cacheAble && \Cache\Controller::getInstance()->check($module, $property)) {
			$retVal = \Cache\Controller::getInstance()->get($module, $property);
		}else {
			$retVal = self::sNpcListData($id);
			\Cache\Controller::getInstance()->set($module, $property, $retVal);
		}

		return $retVal;
	}

	protected function getAdditionalData($user, $params) {
		$retVal = '';

		$retVal .= '<h2>Avaible at Stations</h2>';

		$tArray = self::sGetStationList($params['id'], false);

		$retVal .= Controls::sBuilUl($tArray);

		$retVal .= '<h2>Used by NPC</h2>';

		$tArray = self::sGetNpcList($params['id'], false);
		$retVal .= Controls::sBuilUl($tArray);

		return $retVal;
	}

}