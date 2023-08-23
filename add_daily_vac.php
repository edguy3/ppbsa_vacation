<?PHP
/******************************************************************************
**  File Name: add_daily_vac.php
**  Description: Form to add vacation for a single day off
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
session_start();//start the session
define("DIR_PATH", "");//you must change the path for each sub folder

//IF THE EMPLOYEE HASN'T LOGGED IN. STOP THEM
if(!$_SESSION["ses_first_name"]){
	header ("Location: index.php");
}

//SET PAGE VARIABLES
$GoToDay = $_POST["GoToDay"];
$add_vacation = $_POST["add_vacation"];
$hours = $_POST["hours"];
$submit_date = $_POST["submit_date"];
$type = $_POST["type"];
$EmployeesOff = $_SESSION["ses_employeesoff"];
$OffType = $_SESSION["ses_offtype"];
$minimum_hours = $_SESSION["ses_minimum_hours"];
$contact_email = $_SESSION["ses_contact_email"];
$include_default_dept = $_SESSION["ses_include_default_dept"];
//SET DEPARTMENT AND SUB DEPT
$DeptID = $_SESSION["ses_dept_id"];
$employee_sub_dept = $_SESSION["ses_sub_dept_id"];
//CONCATENATE FIRST AND LAST NAME
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];
$employee = $_SESSION["ses_emp_id"];

//IF THE USER TRIES TO GO DIRECTLY TO THIS PAGE STOP THEM
if($GoToDay == ""){
	echo "You can't access this page directly.";
    exit();
}

require_once(DIR_PATH."includes/config.inc.php");
require(DIR_PATH."includes/db_info.inc.php");
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year        
require(DIR_PATH."includes/selected_year.inc.php");//get the selected fiscal year        
require(DIR_PATH."includes/time_summary.inc.php");//displays a summary of time off for the employee      
//ADD TO THE FORM BODY TAG
$on_load = 'onload=""';

/***********************************************************************
** -- GET SUMMARY INFORMATION TO DISPLAY TO THE EMPLOYEE --           **
***********************************************************************/
time_checks();

 /*****************************************************************************
 ******************************************************************************
 *** ------------- IF THE FORM WAS SUBMITTED. VALIDATE -------------------- ***
 ******************************************************************************
 *****************************************************************************/
if(isset($add_vacation)){

    //VARIABLE TO DETERMINE IF THERE ARE ERRORS. IF ERRORS CHANGE VALUE TO 1
	$error_on_page = 0;

	$day_of_wk = date("l",strtotime($GoToDay));

	  //CHECK IF THE USER ENTERED ANYTHING IN THE HOURS FIELD OR VACATION TYPE
	  if($type != "Select Type"){
        //MAKE SURE THE HOURS ARE NOT EMPTY IF A TYPE WAS SELECTED
		if($hours == ""){
            //SET ERROR VARIABLE TO ON
			$error_on_page = 1;
	       	$message[] = "You entered a Time Off type but have not entered an hours " .
		       "value for the<b> " . $day_of_wk . "</b> field. <br />";
		}
      }

	  //CHECK IF THE USER ENTERED ANYTHING IN THE HOURS FIELD
	  if($hours != ""){
		if($hours > 16){
            //SET ERROR VARIABLE TO ON
			$error_on_page = 1;
	       	$message[] = "You are only allowed a maximum of 16 hours requested " .
			   "for <b> " . $day_of_wk . "</b>. If you need more you must contact " .
               "your supervisor.<br />";
        }

        //CHECK THAT THE MINIMUM HOURS HAVE BEEN ENTERED
		if($minimum_hours != 0){
			if($hours < $minimum_hours){
	            //SET ERROR VARIABLE TO ON
				$error_on_page = 1;
		       	$message[] = "You must enter a minimum of $minimum_hours hours requested " .
				   "for <b> " . $day_of_wk . "</b>. If you need a lower amount you must contact " .
	               "your supervisor.<br />";
	        }
        }

        //IF DATA IS IN THE HOUR FIELD MAKE SURE THE USER SELECTS A TYPE
		if($type == "Select Type"){
            //SET ERROR VARIABLE TO ON
			$error_on_page = 1;
	       	$message[] = "You entered a time value but have not selected a time off " .
		       "type for the<b> " . $day_of_wk . "</b> field. <br />";
        }
        //CHECK THAT ONLY NUMBERS ARE ENTERED IN THE HOURS FIELD
		$ValidChar = (preg_match("/^[0-9.]+$/i",$hours));
		if($ValidChar == 0){
            //SET ERROR VARIABLE TO ON
			$error_on_page = 1;
	       	$message[] = "Incorrect hours entry in the <b>" . $day_of_wk . "</b> field.<br /> " .
	        	"Only numbers are allowed. If you don't want a value enter 0.<br />";
		}

        //CHECK VACATION HOURS IF THE USER SELECTED A TYPE
		if($type != "Select Type"){
            //CHECK THE ARRAY BUILT EARLIER TO COMPARE TIME OFF REQUESTED WITH TIME OFF AVAILABLE
	        for ($t=0; $t<$time_array_cnt; $t++) {
                //ONCE YOU FIND THE TIME OFF TYPE IN THE ARRAY CHECK VALUES
	        	if($time_off_used[$t][0] == $type){
                	$time_off_used[$t][1] = $time_off_used[$t][1] - $hours;
                    //IF REQUESTING MORE THAN AVAILABLE STOP THE USER AND SEND A MESSAGE
	                if($time_off_used[$t][1] < 0){
						 //SET ERROR VARIABLE TO ON
						 $error_on_page = 1;
	                     $message[] = "You are trying to use more " .
                         $time_off_used[$t][2] . " time than you " .
                         	"have available for the year $CurrentYear.<br />";
					}
				}
	        }
        }

        /***********************************************************************/
        //MAKE SURE THE DAY IS NOT FULL AND THE USER DOESN'T ALREADY HAVE THE DAY
        //SCHEDULED
        /***********************************************************************/

        //THIS FUNCTION WILL BE CALLED FROM THE THE SCRIPT JUST BELOW THE FUNCTION END
		function Check_employees_off(){
              global $submit_date,$DeptID,$emp_sub_dept,$i,$chk_sub,$OffType,$message,$error_on_page,$day_of_wk;
			  global $EmployeesOff,$minimum_hours,$contact_email,$get_emp_off_sql;
              global $employee,$whole_dept;

	        //VARIABLES TO TOTAL THE TIME ALREADY USED ON THE CURRENT DATE
	        $count_number_off = 0;
	        $ttl_off_hours = 0;
	        $already_scheduled = 0;

	        //SELECT ALL TIME OFF BEING USED FOR THE CURRENT DATE
			$get_emp_off = @mysql_query($get_emp_off_sql);

			while($emp_off_array = @mysql_fetch_array($get_emp_off)){
	            $off_hours = $emp_off_array["hours"];
	            $current_employee = $emp_off_array["emp_id"];

	            //CHECK IF THE EMPLOYEE ALREADY HAS REQUESTED THIS DATE. IF SO SET A SWITCH TO ON
	            if($current_employee == $employee){
			        $already_scheduled = 1;
	            }

	            //DEPENDING ON THE TRACKING TYPE. ADD THE VALUES OF TIME OFF. EITHER HOURS OR
	            //PEOPLE OFF
		    	if($OffType == "P"){
					$count_number_off =  $count_number_off + 1;
		        }elseif($OffType == "H"){
	                $ttl_off_hours = $ttl_off_hours + $off_hours;
		        }
	    	}

	        //DEPENDING ON THE TRACKING TYPE. CHECK IF THERE ARE ALREADY TOO MANY
	        //PEOPLE OFF
		   	if($OffType == "P"){
				if($count_number_off >= $EmployeesOff){
					//SET ERROR VARIABLE TO ON
					$error_on_page = 1;
	                if($whole_dept != 1){
		               	$message[] = "There are already the alotted number of people off
                   			for <b>" . $day_of_wk . "</b>. A supervisor must schedule this time if
                               it is an emergency.<br />";
	                }else{
		               	$message[] = "There are already the alotted number of people off
                   			for <b>" . $day_of_wk . "</b> in the whole department.
                            A supervisor must schedule this time if it is an emergency.<br />";
	                }
	            }
			}elseif($OffType == "H"){
				if($ttl_off_hours >= $EmployeesOff){
					//SET ERROR VARIABLE TO ON
					$error_on_page = 1;
	                if($whole_dept != 1){
		               	$message[] = "There are already the alotted number of hours used
                   			for <b>" . $day_of_wk . "</b>. A supervisor must schedule this time if
                               it is an emergency.<br />";
					}else{
		               	$message[] = "There are already the alotted number of hours used
                   			for <b>" . $day_of_wk . "</b> in the whole department.
                            A supervisor must schedule this time if it is an emergency.<br />";
	                }
                }
		    }
	        //IF THE USER DID ALREADY SCHEDULE THIS DAY STOP THE ENTRY
	        if($already_scheduled == 1){
	       		//SET ERROR VARIABLE TO ON
				$error_on_page = 1;
                //ONLY TELL THE USER ON THE FIRST LOOP THROUGH THE FUNCTION
                if($whole_dept != 1){
		           	$message[] = "You have already scheduled time off on <b>" . $day_of_wk . "</b>.
              			You can't schedule the same time twice.<br />";
                }
			}
		}//END OF function Check_employees_off()

        //****************************************************************
        //CHECK IF TOO MANY PEOPLE FROM THE EMPLOYEES DEPT ARE ON VACATION
        $get_emp_off_sql = "SELECT vf_vacation.hours,vf_vacation.emp_id FROM " .
				"vf_vacation, vf_employee WHERE " .
	        	"vf_vacation.date = $submit_date AND vf_vacation.dept_id = '$DeptID' " .
	            "AND vf_employee.sub_dept_id = '$employee_sub_dept' AND vf_employee.enabled != 'N' AND " .
	            "vf_vacation.emp_id = vf_employee.emp_id";
        //CALL FUNCTION
        Check_employees_off();

		/**********************************************************************
		** IF THE ABOVE DEPARTMENT WAS A SUB_DEPARTMENT AND $include_default_dept
        ** EQUALS 1. CHECK THE DEFAULT DEPT CONFIG AND RUN CHECKS AGAINST IT  **
		**********************************************************************/
         if($include_default_dept == 1){
			//RETRIEVE CURRENT VALUES FROM THE CONFIGURATION TABLE
			$conf_sql = @mysql_query("SELECT * FROM vf_config WHERE dept_id = '$DeptID' AND " .
					" sub_dept_id = '0'");
			$config_array = @mysql_fetch_array($conf_sql);
				$default_EmployeesOff = $config_array["emp_off_ttl"];

            //AFTER GETTING CONFIG INFORMATION. CHECK EMPLOYEES OFF VALUES
	        $get_emp_off_sql = "SELECT vf_vacation.hours,vf_vacation.emp_id FROM " .
				"vf_vacation, vf_employee WHERE " .
	        	"vf_vacation.date = $submit_date AND vf_vacation.dept_id = '$DeptID' " .
	            "AND vf_employee.enabled != 'N' AND vf_vacation.emp_id = vf_employee.emp_id";
	        //CHECK IF TOO MANY PEOPLE FROM THE EMPLOYEES DEFAULT DEPT ARE ON VACATION
            //VARIABLE USED TO TRIGGER THE CORRECT RESPONSE TO THE USER
            $whole_dept = 1;
	        Check_employees_off();
            $whole_dept = 0;
         }//END OF if($include_default_dept == 1)

      }//END OF if-hours
	}//END OF FOR LOOP


	//IF THERE WERE ANY ERRORS DON'T UPDATE THE TABLE AND LET THE USER KNOW
	if($message){
    	$feedback = "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$feedback .= "$numeric_text. $value ";
	            $numeric_text = $numeric_text + 1;
            }
		$feedback .= "</font><br />\n";
            unset($numeric_text);
    }
 /*****************************************************************************
 ******************************************************************************
 *** ----------------- END OF ALL FORM VALIDATION ------------------------  ***
 ******************************************************************************
 *****************************************************************************/

/************************************************************************
** START ENTERING INFORMATION INTO THE DATABASE IT THERE WERE NO ERRORS**
************************************************************************/
if($error_on_page == 0){

    //VARIABLE TO SEE IF THE SUBMITTED FORM HAS ANYTHING TO ADD
    $check_entry = 0;

	//ENTER ANY BOX THAT HAS DATA INTO THE VACATION DATABASE
	if($type != "Select Type"){
		$ValidChar = (preg_match("/^[0-9.]+$/i",$hours));
		if($ValidChar != 0){

	    	//GET THE CURRENT TIME AND DATE TO USE AS TIME ENTERED ENTRY
	        $cur_date_time = date("YmdHis");

			//GET THE VALUE OF THE ENTERED CALENDAR DATE
			$request_date = strtotime($GoToDay);
			$selected_year = selected_date($request_date);
			
	        //INSERT EACH ENTRY IF THERE WAS A TIME OFF TYPE TO ADD
	        $query_add = "INSERT INTO vf_vacation
	           (emp_id,dept_id,date,hours,to_id,entered_by,date_entered,year)
	           VALUES
	           ('$employee','$DeptID',$submit_date,$hours,$type,'$_SESSION[ses_emp_id]',$cur_date_time,$selected_year)";
	        @mysql_query($query_add) or die (mysql_error());

            //GET THE TIME OFF TYPES FROM THE DATABASE
			$get_type_sql = @mysql_query("SELECT descr from vf_to_type WHERE to_id = '$type'");
            $get_type_arry = @mysql_fetch_array($get_type_sql);
            	$to_type_descr = $get_type_arry["descr"];

            //EMAIL YOUR SUPERVISOR
            if ($contact_email == ""){
				$contact_email = "\"No One\". Your supervisor has not configured an email. " .
                	"The request was submitted but you must personally tell your supervisor " .
                    "to check and approve the time. No email was sent!";
			}else{
	            //EMAIL THE SUPERVISOR OR CONTACT
	            $to = "$contact_email";
				$subject = "$user_name Time Off Request!";
				$body = "$user_name has requested the following timeoff:\n
		            $hours hours \"$to_type_descr\" for " .
	            		date("F j, Y",strtotime($submit_date))." on ".date("F j, Y g:i:sa") .
                        " Please go to the website and review the time for approval.\n" .
                        $cfg['vacation_url'];
			   	@mail($to, $subject, $body, "From: Employee Vacation Request");
			}

            //CHANGE VARIABLE TO ON
            $check_entry = 1;

			//ADD MORE INFORMATION TO THE FEEDBACK THAT LISTS EACH DAY REQUESTED
			$add_feedback = "$hours hours on " . date("l F d, Y",strtotime($submit_date)) . "<br />";
		}
    }


    if($check_entry == 1){
		//START A STRING TO GIVE FEEDBACK TO THE USER AFTER UPDATING THE ENTRY.
		$feedback = "<p align=\"center\"><font color=\"#FF0000\" size=\"5\"><b>**
		    	Time off updated for $user_name! **</b></font><br />" .
                $add_feedback ." The request has been emailed to ".$contact_email."<br />" .
                "<p align=\"center\"><font color=\"#FF0000\" size=\"5\">
    				<b>**********</b></font><br />" .
                "	 	 <form method=\"POST\" action=\"vacation_cal.php\">
		                 <input type=\"hidden\" value=\"$GoToDay\" name=\"GoToDay\">
		                 <input type=\"submit\" value=\"Add more time off\" name=\"B1\">
	                     </form>
                ";
    }

	//CLEAR ALL THE VARIABLES
	unset($type);
	unset($hours);
    unset($submit_date);
}
/**********  ---------END OF FORM SUBMITION---------  **********/

//CALL FUNCTION TO UPDATE INFORMATION AFTER BEING ENTERED
time_checks();

/*******************************************************************************
*** BUILD AND PIECE TOGETHER PARTS OF THE FORM                               ***
*******************************************************************************/

/************ BUILD AN ARRAY LIST OF DAY OFF TYPES ****************/
$time_off_type = array();
$array_cnt = 0;

//GET THE TIME OFF TYPES FROM THE DATABASE
$type_sql = @mysql_query("SELECT * from vf_to_type WHERE emp_viewable = 'Y' AND
    					(dept_id = '0' OR dept_id = '$DeptID')");

while($type_result = @mysql_fetch_array($type_sql)){
	$type_id = $type_result["to_id"];
    $descr = $type_result["descr"];

    //LOAD THE ARRAY WITH THE VALUES
	$time_off_type[$array_cnt][0] = $type_id;
	$time_off_type[$array_cnt][1] = $descr;
	$array_cnt = $array_cnt + 1;
}

//GET A COUNT OF HOW MANY ITEMS WERE ADDED TO THE ARRAY
$array_count = count ($time_off_type);

//CREATE DROP DOWN BOX FOR VACTION TYPES
//LOAD ALL DAY OFF TYPES
$off_type = "";
$off_type .= "<option>Select Type</option>\n";
for ($i=0; $i<$array_count; $i++) {
    $type_value = $time_off_type[$i][0];
    $type_descr = $time_off_type[$i][1];

    if($type_value == $type){
		$select = "selected";
	}

    //LOAD THE DROP DOWN
	$off_type .= "<option $select value=\"$type_value\">$type_descr</option>\n";
    $select = "";
}
/************ END OF BUILD AN ARRAY LIST OF DAY OFF TYPES ****************/

$cur_page_title = "Add daily vacation";
require(DIR_PATH."includes/header.inc.php");
?>
       <table border="0" cellpadding="0" cellspacing="0" width="700">
		  <tr>
		    <td>
		       <table border="0" cellpadding="0" cellspacing="0" width="100%">
				  <tr>
				    <td>
		            	<b>To request a vacation or other time off type:</b>
		            </td>
				  </tr>
				  <tr>
				    <td>
		            	1. Add the hours requested on the correct day of the week.
		            </td>
				  </tr>
				  <tr>
				    <td>
		            	2. Select the time off type from the drop down that corresponds
		            	with the day of the week.
		            </td>
				  </tr>
				  <tr>
				    <td>
		            	3. Click on the Add Time Off button to submit your request.
		            </td>
				  </tr>
				  <tr>
				    <td>
		            	4. The request will be emailed to your supervisor to be approved.<br />
		            	&nbsp;&nbsp;&nbsp;<span style="color:#FF0000;">
		                (The time off must be approved before you can use it.)</span>
		            </td>
				  </tr>
				  <tr>
				    <td>
		            	5. If there are errors, they will be displayed after clicking the
		            	Add Time Off button.<br />&nbsp;&nbsp;&nbsp;&nbsp;You must correct all
		                errors before any submission will be complete.
		            </td>
				  </tr>
		 		  <tr>
				    <td>
		            	6. After your request is complete. Click on a tab at the top of the
		            	page to navigate the site or cloes the browser.
		            </td>
				  </tr>
				  <tr>
				    <td>
		            	<b>Note:</b> At the bottom of the page you should see a summary
		            	of all your time off types that you are eligible to use.
		            </td>
				  </tr>
				  <tr>
				    <td>&nbsp;</td>
				  </tr>
				</table>
			   </td>
			  </tr>	
			  <tr>
			   <td>			
			         <?PHP echo $feedback; ?>
			        <form method="POST" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>">
			     <!--MAIN TABLE--->
				  <table border="3" cellpadding="0" cellspacing="0" style="border-color:#00319C" width="475" align="center">
				    <tr>
				      <td>
			            <!--EMPLOYEE TABLE-->
				          <table bgcolor="#C0C0C0" border="0" cellpadding="0" cellspacing="0" style="background-color:#C0C0C" width="100%">
				            <tr>
				              <td>&nbsp;</td>
				              <td>&nbsp;</td>
				            </tr>
				            <tr>
				              <td align="right">Name:&nbsp;</td>
				              <td align="left"><b><?PHP echo $user_name; ?></b></td>
				            </tr>
				            <tr>
				              <td>&nbsp;</td>
				              <td>&nbsp;</td>
				            </tr>
				            <tr>
				              <td>&nbsp;</td>
				              <td>&nbsp;</td>
				            </tr>
				          </table>
			            <!--END OF EMPLOYEE TABLE-->
					      </td>
					    </tr>
					    <tr>
					      <td>
					        <!--INFORMATION TABLE-->
					          <table border="0" cellpadding="0" cellspacing="0" style="background-color:#C0C0C0" width="100%" align="center">
					            <tr>
					              <td width="10">&nbsp;</td>
					              <td>&nbsp;</td>
					              <td>&nbsp;</td>
					              <td>&nbsp;</td>
					              <td width="10">&nbsp;</td>
					            </tr>
					            <tr>
					              <td width="10">&nbsp;</td>
					              <td align="right"></td>
					              <th align="center">Daily<br />Hours</th>
					              <th align="center">Type</th>
					              <td width="10">&nbsp;</td>
					            </tr>
					            <tr>
					              <td width="10">
					              <input type="hidden" value="<?PHP echo  date("Ymd",strtotime($GoToDay)); ?>" name="submit_date">
					              &nbsp;</td>
					              <td align="right"><?PHP echo date("l",strtotime($GoToDay)) . '-' . $GoToDay; ?>
					              &nbsp;</td>
					              <td><input style="text-align: Center;" type="text" name="hours" size="8" value="<?PHP echo $hours; ?>"></td>
					              <td><select size="1" name="type"><?PHP echo $off_type; ?></select></td>
					              <td width="10">&nbsp;</td>
					            </tr>
					            <tr>
					              <td width="10">&nbsp;</td>
					              <td align="right"></td>
					              <td>&nbsp;</td>
					              <td></td>
					              <td width="10">&nbsp;</td>
					            </tr>
					            <tr>
					              <td width="10">
					              <input type="hidden" value="<?PHP echo $GoToDay; ?>" name="GoToDay">
					              &nbsp;</td>
					              <td align="center" colspan="3">
					              <input type="submit" value="Request Time Off" name="add_vacation"></td>
					              <td width="10">&nbsp;</td>
					            </tr>
					            <tr>
					              <td width="10">&nbsp;</td>
					              <td align="right"></td>
					              <td></td>
					              <td></td>
					              <td width="10">&nbsp;</td>
					            </tr>
					          </table>
					        <!--END OF INFORMATION TABLE-->
					      </td>
					    </tr>
					  </table>
					  </form>
					</td>
				  </tr>
				</table> 
<!--END OF MAIN TABLE--->
  <?PHP echo $time_summary; ?>

<p>&nbsp;</p>
<?PHP require(DIR_PATH."includes/footer.inc.php");?>