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

//SET DEPARTMENT
$DeptID = $_SESSION["ses_dept_id"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$cur_page_title = "Sub-Department options";
require(DIR_PATH."includes/header.inc.php");

//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
echo'<table border="0" cellpadding="0" cellspacing="0" width="600" style="margin-left: 10;">
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
		<td><a href="sub_dept_new.php">Add/Edit a sub-department</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="sub_dept_employees.php">Add employees associations to sub-department</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	</table>';

require(DIR_PATH."includes/footer.inc.php");
?>