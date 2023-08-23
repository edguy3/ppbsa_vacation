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
if($_SESSION["ses_super_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year


$select_employee = $_POST["select_employee"];


$cur_page_title = "Edit Employee";
$itemcnt = 1; //Count of select boxes on the current page
require(DIR_PATH."includes/header.inc.php");

if(isset($select_employee)){
	$dept_select = $_POST["department"];
	
	if($dept_select == "all"){
		$set_query = "SELECT * FROM vf_employee ORDER BY lname,fname ASC";
	}else{
		$set_query = "SELECT * FROM vf_employee WHERE `dept_id` = '$dept_select' ORDER BY lname,fname ASC";		
	}
	
	/************ BUILD A DROP DOWN LIST OF EMPLOYEES ****************/
	//LOOK UP ALL THE EMPLOYEES
	$get_employee = @mysql_query($set_query);
	$employee_box = '<select size="1" name="new_emp_id">
					<option>Select an employee</option>';
	while($emp_array = @mysql_fetch_array($get_employee)){
	        $Emp_EmpID = $emp_array["emp_id"];
	        $Emp_FName = $emp_array["fname"];
	        $Emp_LName = $emp_array["lname"];
	        $Emp_NAME = $Emp_LName . ", " . $Emp_FName;
	
			$employee_box .= "<option $select value=\"$Emp_EmpID\">$Emp_NAME</option>\n";
	}
	$employee_box .= "</select>";
	/************ END OF BUILD A DROP DOWN LIST OF EMPLOYEES ****************/
	//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
	echo '
		<p style="margin-left: 10;"><a href="'.$_SERVER["PHP_SELF"].'">Select a different dapartment</a></p>
	    <form method="POST" action="employee_edit_step2.php">
		<table width="450" border="0" cellspacing="0" cellpadding="0" style="margin-left: 100;">
		  <tr>
		    <td align="center">Select the employee you wish to update:</td>
		  </tr>
		  <tr>
		    <td>&nbsp;</td>
		  </tr>
		  <tr>
		    <td align="center">
			 <div id="HideItem0" style="POSITION:relative">
			' . $employee_box . '
			</div>
			</td>
		  </tr>
		  <tr>
		    <td>&nbsp;</td>
		  </tr>
		  <tr>
		    <td align="center"><input type="submit" value="Retrieve Current Information" name="B1"></td>
		  </tr>	
		</table>
		</form>';
	
}else{
	
	/************ BUILD A DROP DOWN LIST OF DEPARTMENTS ****************/
	$get_department = @mysql_query("SELECT * FROM `vf_department` ORDER BY descr ASC");
	$department_box = '<select size="1" name="department">
					<option value="all">- ALL -</option>';
	while($deptartment_array = @mysql_fetch_array($get_department)){
	        $dept_id = $deptartment_array["dept_id"];
	        $dept_descr = $deptartment_array["descr"];
	
			$department_box .= "<option $select value=\"$dept_id\">$dept_descr</option>\n";
	}
	$department_box .= "</select>";
	/************ END OF BUILD A DROP DOWN LIST OF DEPARTMENTS ****************/
	echo '
	<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
		<table width="450" border="0" cellspacing="0" cellpadding="0" style="margin-left: 100;">
		  <tr>
		    <td align="center">Select the department you wish to select from:</td>
		  </tr>
		  <tr>
		    <td>&nbsp;</td>
		  </tr>
		  <tr>
		    <td align="center">
			 <div id="HideItem0" style="POSITION:relative">
				' . $department_box . '
			</div>
			</td>
		  </tr>
		  <tr>
		    <td>&nbsp;</td>
		  </tr>
		  <tr>
		    <td align="center"><input type="hidden" value="1" name="select_employee"><input type="submit" value="Submit" name="B1"></td>
		  </tr>	
		</table>		
	</form>';
}

require(DIR_PATH."includes/footer.inc.php");
?> 