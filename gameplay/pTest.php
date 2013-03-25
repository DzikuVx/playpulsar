<?php

require_once 'common.php';

$object = new product();

$product = $object->load(1,true, true);

echo $object->getExperienceForSell(0);

//psDebug::print_r($_SESSION);

