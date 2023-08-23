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

$cur_page_title = "Site Setup Options";
$hdr_detail = "Site Setup";
require(DIR_PATH."includes/header.inc.php");
?>

     <table border="0" cellpadding="0" cellspacing="0" width="600" style="margin-left: 10;">
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
		<td><a href="employee_category.php">Add/edit employee categories</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="departments.php">Add/edit company departments</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="sub_dept_menu.php">Add/edit sub-departments</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="config.php">Configure vacation restrictions for your department</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>	  
	  <tr>
	    <td><a href="time_off_types.php">Add/edit time off types</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="fiscal_year.php">Add/edit fiscal year information</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="notable_dates.php">Add holidays and notable dates</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="employee_add.php">Add a new employee</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="timeoff_step1.php">Add/update earned time off</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>	  
	</table>
	
<?PHP
require(DIR_PATH."includes/footer.inc.php");
?>