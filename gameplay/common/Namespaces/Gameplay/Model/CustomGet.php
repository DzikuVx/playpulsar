<?php

namespace Gameplay\Model;

use Gameplay\Exception\Model;

class CustomGet extends Standard {

    protected function serializeData() {

        $retVal = new \stdClass();

        if (!empty($this->originalData)) {
            foreach ($this->originalData as $tField => $mValue) {
                if (isset($this->{$tField})) {
                    $retVal->{$tField} = $this->{$tField};
                } else {
                    throw new Model('Trying to synchronize ' . $tField . ' while class field not exists');
                }
            }
        }

        return serialize($retVal);
    }


    /**
     * @param \stdClass $data
     * @param bool $serialize
     * @return bool
     */
    protected function loadData($data, $serialize) {

        if (!empty($serialize)) {
            $data = unserialize($data);
        }

        $this->originalData = $data;

        foreach ($data as $sKey => $mValue) {
            if (property_exists($this, $sKey)) {
                $this->{$sKey} = $data->{$sKey};
            }
        }

        return true;
    }

} 