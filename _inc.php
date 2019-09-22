<?php
set_time_limit(0);
ini_set('max_execution_time', 0);
error_reporting(E_ALL ^ E_DEPRECATED);

//MYSQL data MODIFY TO FIT YOUR DATABASE
$mysql_host = "127.0.0.1";
$mysql_database = "psvapors_wtg";
$mysql_user = "root";
$mysql_password = "12345";
//Connect to Database
ini_set('display_errors', '1');
$con = mysql_connect($mysql_host, $mysql_user, $mysql_password);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
$con2 = mysql_select_db($mysql_database, $con);
if (!$con2)
  {
  die('Could not connect: ' . mysql_error());
  }
if (!isset($_SESSION['loggedin']) || empty($_SESSION['loggedin'])) { 
	$_SESSION['loggedin']=0;
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function whatsthedate(){
		$today = getdate();
		$year = $today['year'];
		$mday = $today['mday'];
		$mon = $today['mon'];
		$date = "$year-$mon-$mday";
		return $date;
}


?>