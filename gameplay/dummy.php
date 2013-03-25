<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<meta name="robots" content="noarchive" />
<meta http-equiv="refresh" content="600;url=dummy.php" />
</head>
<body>
<?php
  if (isset($_SESSION['userID'])) echo $_SESSION['userID'];
?>
</body>
</html>