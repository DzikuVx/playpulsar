<?php
namespace Gameplay\Model;

use \Database\Controller as Database;

class Galaxy {

    /**
     * @return int
     */
    static public function sGetRandomSystem() {
        $retVal = null;

        $tQuery = "SELECT SystemID FROM systems WHERE Enabled='yes' ORDER BY RAND() LIMIT 1";
        $tQuery = Database::getInstance()->execute ( $tQuery );
        while ( $row = Database::getInstance()->fetch ( $tQuery ) ) {
            $retVal = $row->SystemID;
        }

        return $retVal;
    }

    /**
     * @param int
     * @return int
     */
    static public function sGetRandomWithoutMap($galaxy) {
        $retVal = null;

        $tQuery = "SELECT SystemID FROM systems WHERE Enabled='yes' AND MapAvaible='no' AND Galaxy='{$galaxy}' ORDER BY RAND() LIMIT 1";
        $tQuery = Database::getInstance()->execute ( $tQuery );
        while ( $row = Database::getInstance()->fetch ( $tQuery ) ) {
            $retVal = $row->SystemID;
        }

        return $retVal;
    }

} 