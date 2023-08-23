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
	header ("Location: ".DIR_PATH.".php");
}

//SET PAGE VARIABLES
$DeptID = $_SESSION["ses_dept_id"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

/************ BUILD A DROP DOWN LIST OF YEARS ****************/
$today = date("Ymd");

$get_year = @mysql_query("SELECT * FROM vf_year");
$year_box = '<select size="1" name="selected_year">';
while($emp_array = @mysql_fetch_array($get_year)){
        $cur_year = $emp_array["year"];
        $year_start = $emp_array["start"];
        $year_end = $emp_array["end"];
		
        $year_start = str_replace("-","",$year_start);
        $year_end = str_replace("-","",$year_end);
         
        if($today >= $year_start && $today <= $year_end){
        	$select = "selected";
        }else{
        	$select = "";
        }
        
		$year_box .= "<option $select>$cur_year</option>\n";
}
$year_box .= "</select>";
/************ END OF BUILD A DROP DOWN LIST OF YEARS ****************/

/************ BUILD A DROP DOWN LIST OF EMPLOYEES ****************/
//LOOK UP ALL THE EMPLOYEES
$get_employee = @mysql_query("SELECT * FROM vf_employee WHERE dept_id = '$DeptID' AND vf_employee.enabled != 'N' ORDER BY lname,fname ASC");
$employee_box = "<select size=\"1\" name=\"selected_emp_id\">
				<option>Select an employee</option>";
$select = "";
while($emp_array = @mysql_fetch_array($get_employee)){
        $Emp_EmpID = $emp_array["emp_id"];
        $Emp_FName = $emp_array["fname"];
        $Emp_LName = $emp_array["lname"];
        $Emp_NAME = $Emp_LName . ", " . $Emp_FName;

		//MAKE CHECKS TO SEE IF THE USER CAN CHANGE THEIR OWN VACTION
		//IF THE USERS SUPERVISOR IS SET TO N/A THE USER CAN MANAGE THEIR OWN VACATION
		if($_SESSION["ses_is_sup"] == 2){
			$display_ok = 1;			
		}else{
			if($Emp_EmpID != $_SESSION["ses_emp_id"]){
				$display_ok = 1;
			}else{
				$display_ok = 0;			
			}				
		}
		
        if($display_ok == 1){		
			$employee_box .= "<option $select value=\"$Emp_EmpID\">$Emp_NAME</option>\n";

        }
}

$employee_box .= "</select>";
/************ END OF BUILD A DROP DOWN LIST OF EMPLOYEES ****************/

$cur_page_title = "Edit Vacation";
$itemcnt = 2; //Count of select boxes on the current page
require(DIR_PATH."includes/header.inc.php");
?>



<form method="POST" action="change_vac_type_step2.php">
<table width="450" border="0" cellspacing="0" cellpadding="0" style="margin-left: 100;">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">Select the physcal year to view:</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">
     <div id="HideItem0" style="POSITION:relative">
      <?PHP echo $year_box; ?>
     </div> 
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">Select the employee you wish to update vacations for:</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>    
    <td align="center">
     <div id="HideItem1" style="POSITION:relative">    
      <?PHP echo $employee_box; ?>
     </div> 
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><input type="submit" value="Retrieve Vacation Information" name="B1"></td>
  </tr>	
</table>

</form>
<?PHP 
require(DIR_PATH."includes/footer.inc.php");
?> 