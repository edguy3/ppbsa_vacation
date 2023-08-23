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
$category = $_POST["category"];
$to_choice = $_POST["to_choice"];
$department = $_POST["department"];
$add_holiday = $_POST["add_holiday"];
$name = $_POST["name"];
$hours = $_POST["hours"];
$emp_total = $_POST["emp_total"];
$emp = $_POST["emp"];
$this_year = $_POST["this_year"];
$add_hours = $_POST["add_hours"];

/* MAKE SURE THE USER SELECTS ALL CRITERIA OR SEND THEM BACK TO STEP 1 */
if($selected_year == "Select fiscal year"){
	$message = urlencode ("Please select a year to update.");
	header ("Location: timeoff_step1.php?error_message=$message".
    		"&status=$status&to_choice=$to_choice&selected_year=$selected_year");
	exit;
}

if($status == "Select employee category"){
	$message = urlencode ("Please select an employee category.");
	header ("Location: timeoff_step1.php?error_message=$message".
    		"&status=$status&to_choice=$to_choice&selected_year=$selected_year");
	exit;
}

if($to_choice == "Select a time off type"){
	$message = urlencode ("Please select a time off type.");
	header ("Location: timeoff_step1.php?error_message=$message".
    		"&status=$status&to_choice=$to_choice&selected_year=$selected_year");
	exit;
}

require(DIR_PATH."includes/db_info.inc.php");//database connection information



//IF FORM IS SUBMITTED CHECK FOR ERRORS AND ADD EMPLOYEE
if(isset($add_hours)){
	//VARIABLE THAT WILL BE USED TO SEE IF THERE ARE ANY ERRORS AFTER THE FORM IS SUBMITTED
	$check_form = 0;

   /****************************************************************************
   **            --     VALIDATE THE FORM      --                             **
   ****************************************************************************/
    //MAKE SURE THEY SELECT A TIME OFF TYPE
	if($to_choice == "Select a time off type"){
        $message[] = "You have not selected a time off type.";
		$check_form = 1;
	}

    //SCROLL THOUGH THE HOURS EARNED BOXES
    for ($i=1; $i<=$emp_total; $i++) {
	   //VALIDATE THE HOURS EARNED VALUE. NO NON-NUMERIC VALUES.
	   if(strlen($hours[$i]) != 0){
		   $ValidChar = (preg_match("/^[0-9.]+$/i",$hours[$i]));
		   if($ValidChar == 0){
		        $message[] = "The Hours Earned can only be numeric values. " .
		        	"You entered a non-numeric value for <b>" . $name[$i] . "</b>.";
				$check_form = 1;
			}
		}
	}

    /***************** END OF FORM VALIDATION *********************************/

    //ECHO MESSAGE BACK TO THE USER IF THERE WERE ANY ERRORS ON THE FORM OTHERWISE
    //INSERT THE INFROMATION TO THE DATABASE
	if($message){
    	$content .= "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$content .= "$numeric_text. $value <br />\n";
	            $numeric_text = $numeric_text + 1;
            }
		$content .= "</font><br />";
            unset($numeric_text);
	}else{

	    /********* IF NO ERRORS ENTER THE DATA INTO THE EMPLOYEE TABLE ************/

	    $content .= "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The " .
	    		"Employees were updated.</b></font></div><br />";

	    //SCROLL THOUGH THE EMPLOYEES AND UPDATE THE TABLE
	    for ($i=1; $i<=$emp_total; $i++) {

		    //REMOVE ALL ENTRIES FOR THE USERS THEN ADD NEW ENTRIES FOR UPDATING
		    $remove_old_entry = @mysql_query("DELETE FROM vf_emp_to_hours WHERE `to_id` = '$to_choice'" .
		    	"AND `emp_id` = '$emp[$i]' AND year = '$this_year'");

	        $checkbox_name = $_POST["add_eight".$i];

	        if($checkbox_name == "ON"){
	        	$hours_update = 8;
	        }else{
	           	$hours_update = $hours[$i];
			}

	        //INSERT VALUES INTO THE EMPLOYEE DATABASE
	        if($hours_update != ""){
		        $result = @mysql_query("INSERT INTO vf_emp_to_hours
		        	(to_id,emp_id,hours,year)values('$to_choice','$emp[$i]',
		            '$hours_update','$this_year')")or die ("Unable to update employee $name[$i]");

				//LET THE USER KNOW THE EMPLOYEE WAS UPDATED
				//echo "<font color=\"red\">$name[$i]</font><br />\n";
			}

	        //UNSET ALL THE VARIABLES
			unset($hours[$i]);
	        unset($name[$i]);
	        unset($checkbox_name);
	        unset($emp[$i]);
		}
	}
    unset($this_year);
    unset($emp_total);
}


/*** GET TIME OFF TYPE NAME ***/
$get_time_off = @mysql_query("SELECT * FROM vf_to_type WHERE to_id = '$to_choice'");
$to_array = @mysql_fetch_array($get_time_off);
	$current_to_descr = $to_array["descr"];

/**** GET CATEGORY ****/
$cat_sql = @mysql_query("SELECT `descr`  FROM `vf_category` WHERE `cat_id` = '$category'");
$cat_result = @mysql_fetch_array($cat_sql);
	$dislay_category = $cat_result["descr"];

/**** GET DEPARTMENT ****/
if($department != "all"){
	$dept_sql = @mysql_query("SELECT `dept_id`, `descr` FROM `vf_department` WHERE `dept_id` = '$department'");
	$dept_result = @mysql_fetch_array($dept_sql);
		$dislay_dept = $dept_result["descr"];
}else{
	$dislay_dept= "All Departments";	
}

//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
if($_SESSION["ses_super_admin"] == 1){
    //CREATE A VARIABLE WITH ALL THE PAGE DISPLAY INFORMATION
	$content .= '
	<form name="etime" method="POST" action="' . $_SERVER["PHP_SELF"] . '">
		  <table border="0" cellpadding="0" cellspacing="0" width="600" style="margin-left: 10;">
		    <tr>
		      <td><a href="timeoff_step1.php">Select another Category to update</a></td>
		    </tr>
		    <tr>
		      <td>&nbsp;</td>
		    </tr>
		    <tr>
		      <td>For the year: <b>'.$selected_year.'</b></td>
		    </tr>
		    <tr>
		      <td>Time off type: <b>'.$current_to_descr.'</b></td>
		    </tr>
		    <tr>
		      <td>Category: <b>'.$dislay_category.'</b></td>
		    </tr>
		    <tr>
		      <td>Department: <b>'.$dislay_dept.'</b></td>
		    </tr>	
		    <tr>
		      <td>&nbsp;</td>
		    </tr>
		    <tr>
		      <td>
		        <table border="0" cellpadding="0" cellspacing="0" width="100%">
		          <tr>
		            <td width="350"></td>
		            <td><input type="checkbox" Name="select_all" onClick="selectAll(this.form);">
		              Select/deselect all</td>
		          </tr>
		        </table>
		        &nbsp;</td>
		    </tr>
		  </table>
		  <table border="2" cellpadding="0" cellspacing="0" bordercolorlight="#C0C0C0" bordercolordark="#808080" bordercolor="#808080" style="margin-left: 10;">
		    <tr>
		      <th bgcolor="#808080" style="color:#FFFFFF;">&nbsp;Hours Earned&nbsp;</th>
		      <th bgcolor="#808080" style="color:#FFFFFF;">&nbsp;Employee&nbsp;</th>
		      <th bgcolor="#808080" style="color:#FFFFFF;">&nbsp;Check for 8 hours&nbsp;</th>
		    </tr>
			';

            //VARIABLE TO COUNT THE NUMBER OF EMPLOYEES DISPLAYED
            $count_value = 1;
            $display_btn = 0;
            if($department == "all"){
				$get_employee = @mysql_query("SELECT emp_id,lname,fname from vf_employee WHERE status = '$category' ORDER BY lname, fname ASC");
            }else{
				$get_employee = @mysql_query("SELECT emp_id,lname,fname from vf_employee WHERE status = '$category' AND dept_id = '$department' ORDER BY lname, fname ASC");
            }
            while($emp_array = @mysql_fetch_array($get_employee)){
                $cur_emp_id = $emp_array["emp_id"];
                $lname = $emp_array["lname"];
                $fname = $emp_array["fname"];
                $full_name = $lname . ", " . $fname;


                if($check_form != 1){
                	$current_to_time = @mysql_query("SELECT * FROM vf_emp_to_hours WHERE " .
                    	"emp_id = '$cur_emp_id' and to_id = '$to_choice' AND year = '$selected_year'");
					$my_to_time = @mysql_fetch_array($current_to_time);
                    	$current_hours_credit = $my_to_time["hours"];
	                    $employee_hours = $current_hours_credit;
                }else{
	                $employee_hours = $hours[$count_value];
				}

				$checkbox_name = $_POST["add_eight".$count_value];
                
        		if($checkbox_name == "ON"){
                	 $checked = "checked";
                }

        		$content .= '
				    <tr>
				      <td align="center">
                        <input type="hidden" name="name[' . $count_value . ']" value="' . $full_name . '">
                        <input type="hidden" name="emp[' . $count_value . ']" value="' . $cur_emp_id . '">
				        <input type="text" name="hours[' . $count_value . ']" value="' . $employee_hours . '" size="9"></td>
				      <td>&nbsp;' . $full_name . '&nbsp;</td>
				      <td align="center">
				        <input type="checkbox" name="add_eight' . $count_value . '" value="ON" ' . $checked . '></td>
				    </tr>';

                $checked = "";
            	$count_value = $count_value + 1;
            	$display_btn = $display_btn + 1;
            	
            	if($display_btn == 20){
            		$content .= '
            		<tr>
            		 <td colspan="3" align="center">
            		  <input type="submit" value="Update Hours" name="add_hours" style="margin-top:5;margin-bottom:5;">
            		 </td>
            		</tr>';
            		$display_btn = 0;
            	}
            }
            //TOTAL EMPLOYEES
            $total_employees = $count_value - 1;
			if($count_value == 1){
				$content .= '
				 <tr>
					<td colspan="3" align="center"> No employees match your selections</td> 
				 </tr>
				</table>
				</form>';	
			}else{
				$content .= '
					 <tr> 
					  <td colspan="3" align="center">
					   <input type="hidden" name="category" value="' . $category . '">
					   <input type="hidden" name="to_choice" value="' . $to_choice . '">
			   		   <input type="hidden" name="selected_year" value="' . $selected_year . '">
			   		   <input type="hidden" name="this_year" value="' . $selected_year . '">
					   <input type="hidden" name="emp_total" value="' . $total_employees . '">
					   <input type="hidden" name="department" value="' . $department . '">
				 	   <input type="submit" value="Update Hours" name="add_hours" style="margin-top:5;margin-bottom:5;">
				      </td>
					 </tr>
					</table>
					</form>';
			}

}else{
	$content = 'At this time, only Human Resourses can make changes to employees.';
}


/*******************************************************************************
** ADD JAVASCRIPT TO THE HEADING AREA                                        ***
*******************************************************************************/
	$hdr_addin = '
	<script language="JavaScript" type="text/javascript">
    function selectAll(form){
		var howmany = ' . $total_employees . ';
		for (i=1;i<= howmany;i++) {
            if(document.etime.elements[\'select_all\'].checked==true){
				document.etime.elements[\'add_eight\'+i].checked=true;
            }else{
				document.etime.elements[\'add_eight\'+i].checked=false;
            }
		}
	}
	</script>';

$cur_page_title = "Update time off hours";
require(DIR_PATH."includes/header.inc.php");

echo $content;

require(DIR_PATH."includes/footer.inc.php");
?>