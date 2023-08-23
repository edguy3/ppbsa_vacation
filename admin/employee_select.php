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
$error_message = $_GET["error_message"];
$selected_year = $_POST["selected_year"];
$selected_emp_id = $_POST["selected_emp_id"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year
$hdr_detail = 'Select Employee';

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
        
        if(isset($selected_year)){
			if($cur_year == $selected_year){
        		$select = "selected";
        	}else{
        		$select = "";
        	}        	
        }else{	 
        	if($today >= $year_start && $today <= $year_end){
        		$select = "selected";
        	}else{
        		$select = "";
        	}
        }
		$year_box .= "<option $select>$cur_year</option>\n";
}
$year_box .= "</select>";
/************ END OF BUILD A DROP DOWN LIST OF YEARS ****************/

/************ BUILD A DROP DOWN LIST OF EMPLOYEES ****************/
//LOOK UP ALL THE EMPLOYEES
$get_employee = @mysql_query("SELECT * FROM vf_employee WHERE dept_id = '$DeptID' AND enabled != 'N' ORDER BY lname,fname ASC");
$employee_box = '<select size="1" name="selected_emp_id">
				<option>Select an employee</option>';
$select = "";
while($emp_array = @mysql_fetch_array($get_employee)){
        $Emp_EmpID = $emp_array["emp_id"];
        $Emp_FName = $emp_array["fname"];
        $Emp_LName = $emp_array["lname"];
        $Emp_NAME = $Emp_LName . ", " . $Emp_FName;

        if(isset($selected_emp_id)){
        	if($Emp_EmpID == $selected_emp_id){
         		$select = "selected";
         	}else{
         		$select = "";	
         	}
        }
        	
		$employee_box .= "<option $select value=\"$Emp_EmpID\">$Emp_NAME</option>\n";
}
$employee_box .= "</select>";
/************ END OF BUILD A DROP DOWN LIST OF EMPLOYEES ****************/

$cur_page_title = "Select Employee";
$itemcnt = 2; //Count of select boxes on the current page
require(DIR_PATH."includes/header.inc.php");
?>


<form method="POST" action="employee_menu.php">
<table width="450" border="0" cellspacing="0" cellpadding="0" style="margin-left: 100;">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>    
  </tr>  
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>    
  </tr>  
  <tr>
    <td align="right">Select the physcal year to view:&nbsp;</td>
    <td align="left">
     <div id="HideItem0" style="POSITION:relative">    
      <?PHP echo $year_box; ?>
     </div>  
    </td>    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>    
  </tr>
  <tr>
    <td align="right">Select employee to work with:&nbsp;</td>
    <td align="left">
     <div id="HideItem1" style="POSITION:relative">    
      <?PHP echo $employee_box; ?>
     </div> 
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>    
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="View Employee Options" name="B1"></td>
  </tr>	
</table>		
</form>
<?PHP 
require(DIR_PATH."includes/footer.inc.php");
?>