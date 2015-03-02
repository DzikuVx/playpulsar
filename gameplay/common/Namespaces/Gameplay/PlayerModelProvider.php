<?php

namespace Gameplay;

use Gameplay\Exception\General;

class PlayerModelProvider {

    /**
     * @var PlayerModelProvider
     */
    private static $instance;

    /**
     * @var array
     */
    private $aModels = array();

    private function __construct() {

    }

    /**
     * @return PlayerModelProvider
     */
    static public function getInstance() {

        if (empty(self::$instance)) {
            self::$instance = new PlayerModelProvider();
        }

        return self::$instance;
    }

    /**
     * @param string $sType
     * @param mixed $oObject
     * @return mixed
     */
    public function register($sType, $oObject) {
        //TODO check if object is a real instance of model
        $this->aModels[$sType] = $oObject;
        return $oObject;
    }

    /**
     * @param string $sType
     * @return mixed
     * @throws Exception\General
     */
    public function get($sType) {
        if (empty($this->aModels[$sType])) {
            throw new General('Model ' . $sType . ' not initialized');
        } else {
            return $this->aModels[$sType];
        }
    }

} 