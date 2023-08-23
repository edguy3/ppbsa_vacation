<?PHP


$db_info[username] = "vacation"; //Username to access the database
$db_info[password] = "user"; //Password
$db_info[host] = "localhost"; //Host name
$db_info[dbname] = "vacation"; //Database name
//Do not change the items below this line
$db_connection = mysql_connect ($db_info[host], $db_info[username], $db_info[password]) or die (mysql_error());;
mysql_select_db ($db_info[dbname], $db_connection) or die (mysql_error()."Can not connect");

?>