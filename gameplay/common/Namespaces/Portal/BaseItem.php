<?php

namespace Portal;

class BaseItem {

    /**
     * @var \stdClass
     */
    protected $dataObject = null;

    /**
     * @return \stdClass
     */
    public function getDataObject() {
		return $this->dataObject;
	}
	
}