<?php
namespace Gameplay\Model;


use stdClass;

class JumpNode extends CustomGet {

    protected $tableName = "nodes";
    protected $tableID = "NodeID";
    protected $tableUseFields = null;
    protected $cacheExpire = 604800;

    public $NodeID;
    public $SrcSystem;
    public $DstSystem;
    public $DstX;
    public $DstY;
    public $SrcX;
    public $SrcY;
    public $Active;

    /**
     * @param int|ShipPosition $ID
     * @return string
     */
    protected function parseCacheID($ID) {

        if (!is_numeric($ID)) {
            return md5($ID->System . "/" . $ID->X . "/" . $ID->Y );
        } else {
            return "ID:" . $ID;
        }
    }

    /**
     * @param ShipPosition $position
     * @return stdClass
     */
    public function getDestination(ShipPosition $position) {

        $retVal = new stdClass();

        if ($this->SrcSystem == $position->System && $this->SrcX == $position->X && $this->SrcY == $position->Y) {
            $retVal->X = $this->DstX;
            $retVal->Y = $this->DstY;
            $retVal->System = $this->DstSystem;
        } else {
            $retVal->X = $this->SrcX;
            $retVal->Y = $this->SrcY;
            $retVal->System = $this->SrcSystem;
        }

        return $retVal;
    }

    /**
     * @return bool
     */
    public function get() {

        if (! is_numeric ($this->entryId)) {
            $whereCondition = "(
                (
                    nodes.SrcSystem = '{$this->entryId->System}' AND
                    nodes.SrcX = '{$this->entryId->X}' AND
                    nodes.SrcY = '{$this->entryId->Y}') OR
                (
                    nodes.DstSystem = '{$this->entryId->System}' AND
                    nodes.DstX = '{$this->entryId->X}' AND
                    nodes.DstY = '{$this->entryId->Y}')
                )";
        } else {
            $whereCondition = " nodes.NodeID = '{$this->entryId}' ";
        }

        $tResult = \Database\Controller::getInstance()->execute ( "
            SELECT
                NodeID,
                Active,
                SrcSystem,
                SrcX,
                SrcY,
                DstSystem,
                DstX,
                DstY
            FROM
                nodes
            WHERE
                " . $whereCondition . "
            LIMIT 1");
        while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
            $this->loadData($resultRow, false);
        }

        return true;
    }
} 