<?php
/******************************************************************************
**  Description:
**  Written By: Gary Barber
**  Original Date: 8/27/04
**  
*******************************************************************************
**********************  LAST MODIFIED  ****************************************
**
**  Date:
**  Programmer:
**  Notes:
**
******************************************************************************/
session_start();
define("DIR_PATH", "../");//you must change the path for each sub folder

//IF THE EMPLOYEE ISN'T AN ADMIN. STOP THE USER
if($_SESSION["ses_is_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

//SET PAGE VARIABLES
$GoToDay = $_GET["GoToDay"];
$sub_id = $_GET["sub_id"];
$hours =  $_GET["hours"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information

//SET DEPARTMENT
$DeptID = $_SESSION["ses_dept_id"];

//IF THE USER TRIES TO GO DIRECTLY TO THIS PAGE STOP THEM
if($GoToDay == ""){
	echo "You can't access this page directly.";
    exit();
}

//ADD DATE VARIABLE FOR THE FOLLOWING QUERY
$current_date = date("Ymd", strtotime($GoToDay));

$remove = @mysql_query("DELETE FROM vf_off_perday WHERE sub_dept_id = '$sub_id' AND dept_id = '$DeptID' AND
		day = '$current_date' AND total_off = '$hours'");

$message = urlencode("<b>The following restriction was removed: " .
		"</b><br />".date("l F j, Y",strtotime($GoToDay))." has been removed.<br /></font><br />");

header("Location: date_restrictions.php?error_message=$message&GoToDay=$GoToDay&sub_id=$sub_id");

?>