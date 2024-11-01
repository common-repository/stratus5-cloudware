<?php

$f = @fopen("log.log", "r+");
if ($f !== false)
{
  ftruncate($f, 0);
  $logData = "[".date("Y-m-d h:i:sa")."] LOGS WERE DELETED.\n";
  fwrite($f, $logData);
  fclose($f);
}

?>