<?php

namespace Gameplay\Model;

class WeaponType extends CustomGet {
	protected $tableName = "weapontypes";
	protected $tableID = "WeaponID";
	protected $tableUseFields = array(
        'WeaponID',
        'WeaponClassID',
        'Active',
        'NamePL',
        'NameEN',
        'Symbol',
        'Price',
        'Fame',
        'Size',
        'Accuracy',
        'ShieldMin',
        'ShieldMax',
        'ArmorMin',
        'ArmorMax',
        'PowerMin',
        'PowerMax',
        'EmpMin',
        'EmpMax',
        'Ammo',
        'PowerUsage',
        'ReloadTime',
        'CriticalProbability',
        'CriticalMultiplier',
        'PortWeapon',
        'PortPriority'
    );
	protected $cacheExpire = 86400;

    public $WeaponID;
    public $WeaponClassID;
    public $Active;
    public $NamePL;
    public $NameEN;
    public $Symbol;
    public $Price;
    public $Fame;
    public $Size;
    public $Accuracy;
    public $ShieldMin;
    public $ShieldMax;
    public $ArmorMin;
    public $ArmorMax;
    public $PowerMin;
    public $PowerMax;
    public $EmpMin;
    public $EmpMax;
    public $Ammo;
    public $PowerUsage;
    public $ReloadTime;
    public $CriticalProbability;
    public $CriticalMultiplier;
    public $PortWeapon;
    public $PortPriority;
    public $ClassNamePL;
    public $ClassNameEN;
    public $Name;
    public $ClassName;

    /**
     * @param $weaponID
     * @param $currentAmmo
     * @return float|int
     */
    static public function sGetReloadPrice($weaponID, $currentAmmo) {

        $tData = new WeaponType($weaponID);

		if (empty($tData->Ammo)) {
			return 0;
		}

		$retVal = ceil ( ($tData->Price - ($tData->Price * ($currentAmmo / $tData->Ammo))) * 0.6 );

		return $retVal;
	}

    /**
     * @param $weaponID
     * @return float|int
     */
    static public function sGetRepairPrice($weaponID) {
		global $config;

		$tData = new WeaponType($weaponID);

		if (empty ( $tData->Price )) {
			return 0;
		}

		$retVal = ceil ( $tData->Price * $config ['weapon'] ['repairCost'] ['cash'] );

		return $retVal;
	}

	/**
	 * @param array $params - język jako pierwszy, ID jako drugi element tablicy
	 * @return string
	 */
	public function renderDetail($params) {

		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/weaponDetail.html', new \Translate($params [0], dirname ( __FILE__ ) . '/../translations.php'));

        $tObject = new WeaponType($params [1]);

		if ($params [0] == 'pl') {
			$tObject->Name = $tObject->NamePL;
			$tObject->ClassName = $tObject->ClassNamePL;
		} else {
			$tObject->Name = $tObject->NameEN;
			$tObject->ClassName = $tObject->ClassNameEN;
		}

		if ($tObject->Ammo === null) {
			$tObject->Ammo = '-';
		}

		$template->add ( $tObject );

		return (string) $template;
	}

	function get() {

		$tResult = $this->db->execute ( "
          SELECT
              weapontypes.*,
              weaponclasses.NamePL AS ClassNamePL,
              weaponclasses.NameEN As ClassNameEN
          FROM
              weapontypes JOIN weaponclasses USING(WeaponClassID)
          WHERE
              WeaponID='{$this->dbID}'
          LIMIT 1");
		while($resultRow = $this->db->fetch($tResult)) {
            $this->loadData($resultRow, false);
		}
		return true;
	}
}