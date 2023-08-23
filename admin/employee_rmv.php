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
$selected_year = $_POST["selected_year"];
$selected_emp_id = $_POST["selected_emp_id"];
$DeptID = $_SESSION["ses_dept_id"];//Set department
$chosenaction = $_POST["chosenaction"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

//LOOK UP THE EMPLOYEE
$get_employee = @mysql_query("SELECT fname,lname FROM vf_employee WHERE dept_id = '$DeptID' AND emp_id = '$selected_emp_id'");
while($emp_array = @mysql_fetch_array($get_employee)){
        $emp_fname = $emp_array["fname"];
        $emp_lname = $emp_array["lname"];
        $emp_name = $emp_fname . " " . $emp_lname;
}

if(isset($chosenaction)){

	if($chosenaction == "remove"){
		$rmv = @mysql_query("DELETE FROM `vf_emp_sup_rel` WHERE `emp_id` = '$selected_emp_id'");
		$rmv = @mysql_query("DELETE FROM `vf_emp_to_hours` WHERE `emp_id` = '$selected_emp_id'");
		$rmv = @mysql_query("DELETE FROM `vf_employee` WHERE `emp_id` = '$selected_emp_id'");
		$rmv = @mysql_query("DELETE FROM `vf_vacation` WHERE `emp_id` = '$selected_emp_id'");
		header ("Location: employee_select.php");
	}elseif($chosenaction == "disable"){
		$update_emp = @mysql_query("UPDATE `vf_employee` SET `enabled` = 'N' WHERE `emp_id` = '$selected_emp_id'");
		$page_message = '<p style="color:red; font-size:18px; text-decoration: bold;">Employee '.$emp_name.' has been disabled</p>';
	}
}

$hdr_addin .= ' 
<script language="JavaScript" type="text/javascript">
<!--

function confirmSubmit()
{

	if(document.emp_status.dis.checked==true){
		var agree=confirm("This action will disable \"'.$emp_name.'\". Are you sure you wish to continue?");
		if(agree){
			return true;
		}else{
			return false;
		}
	}else if(document.emp_status.rmv.checked==true){
		var agree=confirm("********** CAUTION ********** \n \nThis action will remove \"'.$emp_name.'\" \nand all associated data from the Vacation program. \n\nTHIS ACTION CAN NOT BE REVERSED. \n\nAre you sure you wish to continue?");
		if(agree){
			return true ;
		}else{
			return false ;
		}
	}else{
		alert(\'Nothing to do. You have not made a selection\');
		return false ;
	}
}
	// -->
	</script>';

$cur_page_title = "Disable or remove employee";
require(DIR_PATH."includes/header.inc.php");
?>

<p class="text18BkB">Disable or remove "<?PHP echo $emp_name; ?>" from the Vacation program</p>
<br /><a href="javascript:directForm('<?PHP echo $selected_year; ?>','<?PHP echo $selected_emp_id; ?>','employee_menu.php')">Return to the employee menu</a>
<p><a href="javascript:directForm('<?PHP echo $selected_year; ?>','<?PHP echo $selected_emp_id; ?>','employee_select.php')">Select a different employee or year</a></p>
<?PHP echo $page_message; ?>
<form method="POST" name="emp_status" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>" onsubmit="return confirmSubmit()">
<table border="2" cellpadding="0" cellspacing="0" width="600" style="margin-left:25;">
 <tr>
  <td>
  <p>&nbsp;</p>
  <p style="margin-left:25;"><input type="radio" name="chosenaction" value="disable" id="dis"><label for="dis"> Disable this account</label></p>
  <p style="margin-left:25;"><input type="radio" name="chosenaction" value="remove" id="rmv"><label for="rmv"> Remove this account</label></p>
  <p style="margin-left:25;"><input type="submit" value="Submit" name="update">&nbsp;<input type="reset" value="Cancel" name="B2"></p>
  <p><input type="hidden" name="selected_year" value="<?PHP echo $selected_year; ?>">
  	 <input type="hidden" name="selected_emp_id" value="<?PHP echo $selected_emp_id; ?>">
  	&nbsp;
  </p>  
  </td>
 </tr>
</table>
</form>

<!-- Form to redirect the input -->
<form method="post" name="page_redirect" action="#">
<input type=hidden name="selected_year" value="">
<input type=hidden name="selected_emp_id" value="">
</form>
<script language="JavaScript" type="text/javascript">
  function directForm(yr,id,page){ 
  	document.page_redirect.selected_year.value = yr;
	document.page_redirect.selected_emp_id.value = id;
	document.page_redirect.action = page;
    document.page_redirect.submit()
  }
</script>  
    
<?PHP     
require(DIR_PATH."includes/footer.inc.php");
?>