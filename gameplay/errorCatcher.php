<?php

require_once 'common.php';

header ( 'Content-Type: text/html; charset=utf-8' );

$tArray = unserialize($HTTP_RAW_POST_DATA);

$tQuery = "
  INSERT INTO st_errormessages(
    CreateTime,
    Text,
    Parameters,
    Referer,
    UserName
  )
  VALUES (
    '".time()."',
    '".$tArray['text']."',
    '".$tArray['parameters']."',
    '".$tArray['referer']."',
    '".$tArray['userName']."'
  )
";
\Database\Controller::getBackendInstance()->execute($tQuery);