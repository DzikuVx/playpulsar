<?php

namespace Controlpanel;

class StationTypesRegistry extends PortTypesRegistry{
	protected $itemClass = "\Controlpanel\StationTypes";
	protected $extraList = "porttypes.Type='station' ";
}