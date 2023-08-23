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
$DeptID = $_SESSION["ses_dept_id"];//Set department
$selected_year = $_POST["selected_year"];
$selected_emp_id = $_POST["selected_emp_id"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

//LOOK UP THE EMPLOYEE
$get_employee = @mysql_query("SELECT fname,lname,dept_id,sub_dept_id FROM vf_employee WHERE emp_id = '$selected_emp_id'")or die("Can't retrieve employee information") ;
$emp_array = @mysql_fetch_array($get_employee);
        $emp_fname = $emp_array["fname"];
        $emp_lname = $emp_array["lname"];
        $emp_dept = $emp_array["dept_id"];
        $emp_sub_dept = $emp_array["sub_dept_id"];
        $emp_name = $emp_fname . " " . $emp_lname;

$hdr_detail = 'Employee Options';
$cur_page_title = "Employee Options";

if($selected_emp_id == "Select an employee" || $selected_emp_id == ""){
	$message = urlencode ("Please select an employee.");
	header ("Location: employee_select.php?error_message=$message");
	exit();
}

require(DIR_PATH."includes/header.inc.php");
?>

<table width="450" border="0" cellspacing="0" cellpadding="0" style="margin-left: 100;">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><a href="javascript:directForm('<?PHP echo $selected_year; ?>','<?PHP echo $selected_emp_id; ?>','employee_select.php')">Select a different employee or year</a></td>
  </tr>   
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><b>Employee:</b> <?PHP echo $emp_name; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Year:</b> <?PHP echo $selected_year; ?></td>
  </tr>  
  <tr>
    <td><hr style="color:blue"></td>
  </tr>   
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><a href="javascript:directForm('<?PHP echo $selected_year; ?>','<?PHP echo $selected_emp_id; ?>','employee_vacation_add.php')">Add time off</a></td>
  </tr>    
  <tr>
    <td>&nbsp;</td>
  </tr>   
  <tr>
    <td><a href="javascript:directForm('<?PHP echo $selected_year; ?>','<?PHP echo $selected_emp_id; ?>','employee_change_vac_type.php')">Change or delete a vacation</a></td>
  </tr>   
  <tr>
    <td>&nbsp;</td>
  </tr>
      <tr>
    <td><a href="javascript:directForm('<?PHP echo $selected_year; ?>','<?PHP echo $selected_emp_id; ?>','employee_add_note.php')">Add notes to time off</a></td>
  </tr>     
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>    
  <tr>
    <td>&nbsp;</td>
  </tr>    
  <tr>
    <td>&nbsp;</td>
  </tr>    
  <tr>
    <td>&nbsp;</td>
  </tr>    
  <tr>
    <td><a href="javascript:directForm('<?PHP echo $selected_year; ?>','<?PHP echo $selected_emp_id; ?>','employee_edit_step2.php')">Edit information for <?PHP echo $emp_name; ?></a></td>
  </tr>    
  <tr>
    <td>&nbsp;</td>
  </tr>        
  <tr>
    <td><a href="javascript:directForm('<?PHP echo $selected_year; ?>','<?PHP echo $selected_emp_id; ?>','employee_rmv.php')">Disable/Remove <?PHP echo $emp_name; ?></a></td>
  </tr>   
  <tr>
    <td>&nbsp;</td>
  </tr>    
  <tr>
    <td>&nbsp;</td>
  </tr>  
</table>

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