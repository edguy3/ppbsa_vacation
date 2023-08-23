<?PHP
/******************************************************************************
**  File Name:
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

//SET PAGE VARIBALES
$TOType = $_GET["TOType"];

$tab_selection = 1;//sets the color of the tab that corresponds to this page
require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$cur_page_title = "Time Off Summary";
require(DIR_PATH."includes/header.inc.php");

echo '&nbsp;<br />
		<table style="margin-left:10;">
		<tr>
		 <td><b><u>Select the time off type you wish to view</u></b></td>
		</tr>';
     
//GET ALL OF THE TIME OFF TYPES FOR THE DEPT
$SQL = @mysql_query("SELECT * from vf_to_type WHERE dept_id = 0 OR
		dept_id = '$_SESSION[ses_dept_id]'");
while($Result = @mysql_fetch_array($SQL)){
	$TypeID = $Result["to_id"];
    $Descr = $Result["descr"];
    $Valid_date = $Result["type_date"];

    $summary = "vacation_summary_step2.php?TOType=" . $TypeID;

    echo '<tr>
	    <td><a href="' . $summary . '">' . $Descr . '</a></td>
	  </tr>';
}

echo '</table>
		<p>&nbsp;</p>';

require(DIR_PATH."includes/footer.inc.php");
?>