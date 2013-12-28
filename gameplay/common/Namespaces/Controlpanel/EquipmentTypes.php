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
     * @param \user $user
     * @param array $params
     * @throws \customException
     */
    public function editExe(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {

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
		\phpCache\Factory::getInstance()->create()->clear(new \phpCache\CacheKey('equipment',$params['id']));

		Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');
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

		$oCacheKey = new \phpCache\CacheKey('cpEquipmentTypes::sGetStationList', $id);
        $cache = \phpCache\Factory::getInstance()->create();

		if ($cacheAble && $cache->check($oCacheKey)) {
			$retVal = $cache->get($oCacheKey);
		}else {
			$retVal = self::sStationListData($id);
			$cache->set($oCacheKey, $retVal);
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