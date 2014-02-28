<?php
namespace Gameplay\Model;

/**
 * Class TransNode
 * @package Gameplay\Model
 */
class TransNode extends CustomGet {

    protected $tableName = "nodes";
    protected $tableID = "NodeID";
    protected $tableUseFields = null;
    protected $cacheExpire = 604800;

    /** @var int */
    public $System;
    /** @var int */
    public $X;
    /** @var int */
    public $Y;

    public function get($ID) {

        $tResult = \Database\Controller::getInstance()->execute ( "
            SELECT
                SrcSystem,
                SrcX,
                SrcY,
                DstSystem,
                DstX,
                DstY
            FROM
                nodes
            WHERE
                Active = 'yes' AND
                ((SrcSystem='{$this->entryId->Source}' AND DstSystem='{$this->entryId->Destination}') OR
                (DstSystem='{$this->entryId->Source}' AND SrcSystem='{$this->entryId->Destination}'))
            LIMIT 1");
        while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tResult ) ) {
            $this->System = $this->entryId->Source;
            if ($resultRow->SrcSystem == $this->entryId->Source) {
                $this->X = $resultRow->SrcX;
                $this->Y = $resultRow->SrcY;
            } else {
                $this->X = $resultRow->DstX;
                $this->Y = $resultRow->DstY;
            }
        }
        return true;
    }

    /**
     * @return string
     */
    protected function getCacheModule() {
        return 'trans-node';
    }

    /**
     * @param \stdClass $ID
     * @return string
     */
    protected function parseCacheID($ID) {
        return $ID->Source . "/" . $ID->Destination;
    }

} 