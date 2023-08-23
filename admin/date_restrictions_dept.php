<?PHP
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
$DeptID = $_SESSION["ses_dept_id"];
$employee_sub_dept = $_SESSION["ses_sub_dept_id"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

//IF THE DEPARTMENT USES SUB DEPARTMENTS MAKE THEM SELECT THE SUB
$ck_sub = @mysql_query("SELECT * FROM vf_department WHERE dept_id = '$DeptID'");
$use_sub = @mysql_fetch_array($ck_sub);
	$dept_subs = $use_sub["sub_dept"];

if($dept_subs != "Y"){
	$sub_dept_drpdown = "0";
}


if($dept_subs == "Y"){

	$cur_page_title = "Date Restrictions";
	$itemcnt = 1; //Count of select boxes on the current page
	require(DIR_PATH."includes/header.inc.php");
	
		$subdept_dropdown = '<p><b>This department uses sub-departments. Please select the
        			sub-department you want to configure.</b></p>
						<div id="HideItem0" style="POSITION:relative; margin-left: 100;">
        				<form method="POST" action="date_restrictions.php">
    					<select size="1" name="sub_id">
    					<option value="0">Default Department</option>';

		$sql_subs = @mysql_query("SELECT sub_dept_id,descr,code FROM vf_sub_dept
	    		WHERE dept_id = '$DeptID'");
		while($sql_sub_array = @mysql_fetch_array($sql_subs)){
	    	$fetch_sub_id = $sql_sub_array["sub_dept_id"];
	    	$fetch_descr = $sql_sub_array["descr"];
	    	$fetch_code = $sql_sub_array["code"];

	       	$subdept_dropdown .= '<option value="'.$fetch_sub_id.'">'.$fetch_code.'</option>';
		}
		$subdept_dropdown .= '</select>
	    				<input type="submit" value="Submit" name="sub_dept_selection">
                       	</form>
						</div>';

	echo $subdept_dropdown.'<p>&nbsp;</p>';
	require(DIR_PATH."includes/footer.inc.php");

}else{
	header("location: date_restrictions.php?sub_id=0");
}


?>