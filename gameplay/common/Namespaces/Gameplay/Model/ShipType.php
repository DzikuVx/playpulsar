<?php

namespace Gameplay\Model;

class ShipType extends Standard {
	protected $tableName = "shiptypes";
	protected $tableID = "ShipID";
	protected $tableUseFields = array('UserBuyable', 'NamePL', 'NameEN', 'Price', 'Fame', 'Size', 'Targetting', 'Weapons', 'WeaponSize', 'Cargo', 'Space', 'Speed', 'Maneuver', 'Shield', 'Armor', 'ArmorStrength', 'ArmorPiercing', 'Power', 'ShieldRegeneration', 'ArmorRegeneration', 'PowerRegeneration', 'ShieldRepair', 'ArmorRepair', 'PowerRepair', 'Scan', 'Cloak', 'Gather', 'Emp', 'CanWarpJump', 'CanActiveScan');
	protected $cacheExpire = 86400;

    public $ShipID;

    public $UserBuyable;
    public $NamePL;
    public $NameEN;
    public $Price;
    public $Fame;
    public $Size;
    public $Targetting;
    public $Weapons;
    public $WeaponSize;
    public $Cargo;
    public $Space;
    public $Speed;
    public $Maneuver;
    public $Shield;
    public $Armor;
    public $ArmorStrength;
    public $ArmorPiercing;
    public $Power;
    public $ShieldRegeneration;
    public $ArmorRegeneration;
    public $PowerRegeneration;
    public $ShieldRepair;
    public $ArmorRepair;
    public $PowerRepair;
    public $Scan;
    public $Cloak;
    public $Gather;
    public $Emp;
    public $CanWarpJump;
    public $CanActiveScan;

    public $Name;

	/**
	 * @param array $params
	 * @return string
	 */
	public function renderDetail($params) {

        $t = new \Translate($params [0], dirname ( __FILE__ ) . '/../translations.php');
		$template = new \General\Templater ( dirname ( __FILE__ ) . '/../../templates/shipDetail.html', $t );

		$tObject = new ShipType($params[1]);

		if ($params [0] == 'pl') {
			$tObject->Name = $tObject->NamePL;
		} else {
			$tObject->Name = $tObject->NameEN;
		}

		$template->add($tObject);

		return ( string ) $template;
	}
}