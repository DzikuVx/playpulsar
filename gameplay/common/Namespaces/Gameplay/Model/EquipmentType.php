<?php

namespace Gameplay\Model;

class EquipmentType extends Standard {
	protected $tableName = "equipmenttypes";
	protected $tableID = "EquipmentID";
	protected $tableUseFields = array(
        'EquipmentID',
        'Active',
        'Unique',
        'NamePL',
        'NameEN',
        'Size',
        'Price',
        'Fame',
        'Type',
        'Targetting',
        'Shield',
        'Armor',
        'ArmorStrength',
        'ArmorPiercing',
        'Power',
        'Cargo',
        'Weapons',
        'Space',
        'Speed',
        'Maneuver',
        'ShieldRegeneration',
        'ArmorRegeneration',
        'PowerRegeneration',
        'ShieldRepair',
        'ArmorRepair',
        'PowerRepair',
        'Scan',
        'Cloak',
        'Gather',
        'Emp',
        'CanRepairWeapons',
        'CanRepairEquipment',
        'CanActiveScan',
        'CanWarpJump'
    );
	protected $cacheExpire = 86400;

    public $EquipmentID;
    public $Active;
    public $Unique;
    public $NamePL;
    public $NameEN;
    public $Size;
    public $Price;
    public $Fame;
    public $Type;
    public $Targetting;
    public $Shield;
    public $Armor;
    public $ArmorStrength;
    public $ArmorPiercing;
    public $Power;
    public $Cargo;
    public $Weapons;
    public $Space;
    public $Speed;
    public $Maneuver;
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
    public $CanRepairWeapons;
    public $CanRepairEquipment;
    public $CanActiveScan;
    public $CanWarpJump;
    public $Name;

    /**
     * @return float|int
     */
    public function getRepairPrice() {
        global $config;

        if (empty($this->Price)) {
            return 0;
        } else {
            return ceil($this->Price * $config ['equipment'] ['repairCost'] ['cash']);
        }
    }

	/**
	 * @param array $params
	 * @return string
	 */
	public function renderDetail($params) {

		$t = new \Translate($params [0], dirname ( __FILE__ ) . '/../translations.php' );

		$template = new \General\Templater(dirname ( __FILE__ ) . '/../../templates/equipmentDetail.html', $t );

		$tObject = new EquipmentType($params[1]);

		if ($params [0] == 'pl') {
			$tObject->Name = $tObject->NamePL;
		} else {
			$tObject->Name = $tObject->NameEN;
		}

		$template->add ( $tObject );

		return (string) $template;
	}
}