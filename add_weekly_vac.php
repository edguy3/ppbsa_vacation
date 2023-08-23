<?PHP
/******************************************************************************
**  File Name: add_weekly_vac.php
**  Description: Form to add vacation for a week
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
define("DIR_PATH", "");//you must change the path for each sub folder

//If the employee hasn't logged in. stop them
if(!$_SESSION["ses_first_name"]){
	header ("Location: index.php");
}

//SET PAGE VARIABLES
$GoToDay = $_POST["GoToDay"];
$EmployeesOff = $_SESSION["ses_employeesoff"];
$OffType = $_SESSION["ses_offtype"];
$minimum_hours = $_SESSION["ses_minimum_hours"];
$contact_email = $_SESSION["ses_contact_email"];
$include_default_dept = $_SESSION["ses_include_default_dept"];
//SET DEPARTMENT AND SUB DEPT
$DeptID = $_SESSION["ses_dept_id"];
$employee_sub_dept = $_SESSION["ses_sub_dept_id"];
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];//Concatenate first and last name
$employee = $_SESSION["ses_emp_id"];
$submit_date = $_POST["submit_date"];
$do_display = $_POST["do_display"];
$hours = $_POST["hours"];
$type = $_POST["type"];
$add_vacation = $_POST["add_vacation"];

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
/***********************************************************************/
//MAKE SURE THE DAY IS NOT FULL AND THE USER DOESN'T ALREADY HAVE THE DAY
//SCHEDULED
/***********************************************************************/
//THIS FUNCTION WILL BE CALLED FROM THE THE SCRIPT JUST BELOW THE FUNCTION END
function Check_employees_off(){
global $submit_date,$DeptID,$emp_sub_dept,$i,$chk_sub,$OffType,$message,$error_on_page,$day_of_wk;
    global $EmployeesOff,$minimum_hours,$contact_email,$get_emp_off_sql;
    global $employee,$whole_dept,$dept_get_emp_off_sql,$whole_dept_EmployeesOff;

    //VARIABLES TO TOTAL THE TIME ALREADY USED ON THE CURRENT DATE
    $count_number_off = 0;
    $ttl_off_hours = 0;
    $already_scheduled = 0;

    //SELECT ALL TIME OFF BEING USED FOR THE CURRENT DATE
    if($whole_dept == 1){
        $get_emp_off = @mysql_query($dept_get_emp_off_sql);
        $ttl_employees_off = $whole_dept_EmployeesOff;
    }else{
        $get_emp_off = @mysql_query($get_emp_off_sql);
        $ttl_employees_off = $EmployeesOff;
    }

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
            $count_number_off = $count_number_off + 1;
        }elseif($OffType == "H"){
            $ttl_off_hours = $ttl_off_hours + $off_hours;
        }
    }

    //DEPENDING ON THE TRACKING TYPE. CHECK IF THERE ARE ALREADY TOO MANY
    //PEOPLE OFF
    if($OffType == "P"){
        if($count_number_off >= $ttl_employees_off){
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
        if($ttl_off_hours >= $ttl_employees_off){
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

	//GET ALL ENTRY BOX INFORMATION AND VALIDATE ENTRY
    //SINCE THERE A 7 BOXES OF TYPE AND HOURS ITERATE 7 TIMES
	for ($i=0; $i<7; $i++) {
		//GET THE CURRENT DAY OF THE WEEK
       	switch ($i) {
          case 0:
            $day_of_wk = "Sunday";
            break;
          case 1:
            $day_of_wk = "Monday";
            break;
          case 2:
            $day_of_wk = "Tuesday";
            break;
          case 3:
            $day_of_wk = "Wednesday";
            break;
          case 4:
            $day_of_wk = "Thursday";
            break;
          case 5:
            $day_of_wk = "Friday";
            break;
          case 6:
            $day_of_wk = "Saturday";
            break;
        }

		//CHECK IF THE USER ENTERED ANYTHING IN THE HOURS FIELD. IF SO VALIDATE.
		if($do_display[$i] == 0){
		  //CHECK IF THE USER ENTERED ANYTHING IN THE HOURS FIELD OR VACATION TYPE
		  if($hours[$i] != "" || $type[$i] != "Select Type"){
	        //MAKE SURE THE HOURS ARE NOT EMPTY IF A TYPE WAS SELECTED
			if($hours[$i] == ""){
	            //SET ERROR VARIABLE TO ON
				$error_on_page = 1;
		       	$message[] = "You entered a Time Off type but have not entered an hours " .
			       "value for the<b> " . $day_of_wk . "</b> field. <br />";
			}
	      }

	      //IF THE HOURS FIELD IS NOT BLANK. CONTINUE CHECKING
		  if($hours[$i] != ""){
			  if($hours[$i] > 16){
	           	//SET ERROR VARIABLE TO ON
			  	$error_on_page = 1;
			   	$message[] = "You are only allowed a maximum of 16 hours requested " .
			  	   "for <b> " . $day_of_wk . "</b>. If you need more you must contact " .
		              "your supervisor.<br />";
		      }

		      //CHECK THAT THE MINIMUM HOURS HAVE BEEN ENTERED
			  if($minimum_hours != 0){
					if($hours[$i] < $minimum_hours){
			            //SET ERROR VARIABLE TO ON
						$error_on_page = 1;
				       	$message[] = "You must enter a minimum of $minimum_hours hours requested " .
					   "for <b> " . $day_of_wk . "</b>. If you need a lower amount you must contact " .
		               "your supervisor.<br />";
			        }
		        }

		        //IF DATA IS IN THE HOUR FIELD MAKE SURE THE USER SELECTS A TYPE
				if($type[$i] == "Select Type"){
		            //SET ERROR VARIABLE TO ON
					$error_on_page = 1;
			       	$message[] = "You entered a time value but have not selected a time off " .
				       "type for the<b> " . $day_of_wk . "</b> field. <br />";
		        }
		        //CHECK THAT ONLY NUMBERS ARE ENTERED IN THE HOURS FIELD
				$ValidChar = (preg_match("/^[0-9]+$/i",$hours[$i]));
				if($ValidChar == 0){
		            //SET ERROR VARIABLE TO ON
					$error_on_page = 1;
			       	$message[] = "Incorrect hours entry in the <b>" . $day_of_wk . "</b> field.<br /> " .
			        	"Only numbers are allowed. If you don't want a value enter 0.<br />";
				}

		        //CHECK VACATION HOURS IF THE USER SELECTED A TYPE
				if($type[$i] != "Select Type"){
		            //CHECK THE ARRAY BUILT EARLIER TO COMPARE TIME OFF REQUESTED WITH TIME OFF AVAILABLE
			        for ($t=0; $t<$time_array_cnt; $t++) {
		                //ONCE YOU FIND THE TIME OFF TYPE IN THE ARRAY CHECK VALUES
			        	if($time_off_used[$t][0] == $type[$i]){
		                	$time_off_used[$t][1] = $time_off_used[$t][1] - $hours[$i];
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

	        //****************************************************************
	        //CHECK IF TOO MANY PEOPLE FROM THE EMPLOYEES DEPT ARE ON VACATION
	        $get_emp_off_sql = "SELECT vf_vacation.hours,vf_vacation.emp_id FROM " .
				"vf_vacation, vf_employee WHERE " .
	        	"vf_vacation.date = $submit_date[$i] AND vf_vacation.dept_id = '$DeptID' " .
	            "AND vf_employee.sub_dept_id = '$employee_sub_dept' AND vf_employee.enabled != 'N' AND " .
	            "vf_vacation.emp_id = vf_employee.emp_id";
	        //CALL FUNCTION
	        Check_employees_off();

			/**********************************************************************
			** IF THE ABOVE DEPARTMENT WAS A SUB_DEPARTMENT AND $include_default_dept
	        ** EQUALS 1. CHECK THE DEFAULT DEPT CONFIG AND RUN CHECKS AGAINST IT  **
			**********************************************************************/
	         if($include_default_dept == 1){
	            //VARIABLE USED TO TRIGGER THE CORRECT RESPONSE TO THE USER
	            $whole_dept = 1;
				//RETRIEVE CURRENT VALUES FROM THE CONFIGURATION TABLE
				$conf_sql = @mysql_query("SELECT * FROM vf_config WHERE dept_id = '$DeptID' AND " .
						" sub_dept_id = '0'");
				$whole_config_array = @mysql_fetch_array($conf_sql);
					$whole_dept_EmployeesOff = $whole_config_array["emp_off_ttl"];

	            //AFTER GETTING CONFIG INFORMATION. CHECK EMPLOYEES OFF VALUES
		        $dept_get_emp_off_sql = "SELECT vf_vacation.hours,vf_vacation.emp_id FROM " .
				"vf_vacation, vf_employee WHERE " .
	        	"vf_vacation.date = $submit_date[$i] AND vf_vacation.dept_id = '$DeptID' " .
	            "AND vf_vacation.emp_id = vf_employee.emp_id";

		        //CHECK IF TOO MANY PEOPLE FROM THE EMPLOYEES DEFAULT DEPT ARE ON VACATION
		        Check_employees_off();
	            $whole_dept = 0;
	         }//END OF if($include_default_dept == 1)
      	  }//END OF if($hours[$i] != "")
		}//END OF if($do_display[$i] == 0)
	}//END OF FOR LOOP


	//IF THERE WERE ANY ERRORS DON'T UPDATE THE TABLE AND LET THE USER KNOW
	if($message){
    	$error_msg = "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$error_msg .= "$numeric_text. $value ";
	            $numeric_text = $numeric_text + 1;
            }
		$error_msg .= "</font><br />\n";
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

	    //CONVERT SUNDAY DATE TO A READABLE FORMAT
	    $sunday_pick = date("m/d/Y",strtotime("$sunday_choice"));

        //VARIABLE TO SEE IF THE SUBMITTED FORM HAS ANYTHING TO ADD
        $check_entry = 0;

	    //ENTER ANY BOX THAT HAS DATA INTO THE VACATION DATABASE
	    //SINCE THERE A 7 BOXES OF TYPE AND HOURS ITERATE 7 TIMES
		$add_feedback = "";
		for ($i=0; $i<7; $i++) {
			if($do_display[$i] == 0){
			  if($type[$i] != "Select Type"){
	            $ValidChar = (preg_match("/^[0-9]+$/i",$hours[$i]));
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
	                ('$employee','$DeptID','$submit_date[$i]','$hours[$i]','$type[$i]',
	                '$_SESSION[ses_emp_id]','$cur_date_time','$selected_year')";
	                @mysql_query($query_add) or die ("Error entering time off for $submit_date[$i]". mysql_error());

	                //CHANGE VARIABLE TO ON
	                $check_entry = 1;

	                //BUILD EMAIL INFORMATION

	                //GET THE TIME OFF TYPES FROM THE DATABASE
	                $get_type_sql = @mysql_query("SELECT descr from vf_to_type WHERE to_id = '$type[$i]'");
	                $get_type_arry = @mysql_fetch_array($get_type_sql);
	                    $to_type_descr = $get_type_arry["descr"];

	                $email_data[$i] = "$hours[$i] hours \"$to_type_descr\" for " .
	                    date("F j, Y",strtotime($submit_date[$i]))." on ".date("F j, Y g:i:sa");

	                //ADD MORE INFORMATION TO THE FEEDBACK THAT LISTS EACH DAY REQUESTED
	                $add_feedback .= "$hours[$i] hours on " . date("l F d, Y",strtotime($submit_date[$i])) . "<br />";
				}
	      	  }
			}
		}

        if($check_entry == 1){

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
				$body = "$user_name has requested the following timeoff:\n ";

                for ($x=0; $x<7; $x++) {
	                $body .= $email_data[$x]."\n";
				}

                       $body .= " Please go to the website and review the time for approval.\n" .
                                 $cfg['vacation_url'];
			   	mail($to, $subject, $body, "From: Employee Vacation Request");
			}

		    //START A STRING TO GIVE FEEDBACK TO THE USER AFTER UPDATING THE ENTRY.
			$feedback = "<div style=\"margin-left:30px;width:600px;\"><p align=\"center\"><font color=\"#FF0000\" size=\"5\"><b>**
		    	Time off updated for $user_name! **</b></font><br />" .
                $add_feedback  ." The request has been emailed to ".$contact_email."<br />" .
                "<p align=\"center\"><font color=\"#FF0000\" size=\"5\">
    				<b>**********</b></font><br />
	 	 				<form method=\"POST\" action=\"vacation_cal.php\">
		                 <input type=\"hidden\" value=\"$GoToDay\" name=\"GoToDay\">
		                 <input type=\"submit\" value=\"Add more time off\" name=\"B1\">
	                     </form>                
                </p></div>";
         }

	     //CLEAR ALL THE VARIABLES
	     unset($type[0]);
   	     unset($type[1]);
	     unset($type[2]);
	     unset($type[3]);
	     unset($type[4]);
	     unset($type[5]);
	     unset($type[6]);
	     unset($hours[0]);
	     unset($hours[1]);
	     unset($hours[2]);
	     unset($hours[3]);
	     unset($hours[4]);
	     unset($hours[5]);
	     unset($hours[6]);
         unset($submit_date[0]);
         unset($submit_date[1]);
         unset($submit_date[2]);
         unset($submit_date[3]);
         unset($submit_date[4]);
         unset($submit_date[5]);
         unset($submit_date[6]);
	}
}
/**********  ---------END OF FORM SUBMITION---------  **********/

//CALL FUNCTION TO UPDATE INFORMATION AFTER BEING ENTERED
time_checks();

/*******************************************************************************
*** BUILD AND PIECE TOGETHER PARTS OF THE FORM                               ***
*******************************************************************************/
/*******************************************************************************
** DETERMINE IF ANY DAY OF THE SELECTED WEEK IS FULL.                         **
** IF IT IS FULL CHANGE THE VARIABLE TO 1 TO INDICATE SO. LATER IN THE SCRIPT **
** IF THE VARAIBLE = 1 DISPLAY "FULL" OTHERWISE DISPLAY A TEXTBOX AND         **
** DROP DOWN BOX                                                              **
*******************************************************************************/
for ($i=0; $i<7; $i++) {
    //HOLDS THE SUNDAY DATE THE USER SELECTED
	$check_day = date("Ymd",strtotime("$GoToDay +" . $i . " days"));

    //CHECK IF THERE ARE SPECIAL DATES THAT THE DEPARTMENT HAS RESTRICTED
    //IF NO INFORMATION FOR THE CURRENT DATE IS IN THE TABLE. THE ALLOWED
    //TIME OFF DEFAULTS TO THE DEFAULT config TABLE people_off_per_day VALUE
    $date_allow = @mysql_query("SELECT * FROM vf_off_perday WHERE " .
      			"day = '$check_day' AND dept_id = '$DeptID' AND sub_dept_id = '$employee_sub_dept'");
    $date_restrict = @mysql_fetch_array($date_allow);
    $restriction = $date_restrict["total_off"];

	//MAKE SURE THE VARIABLES ARE EMPTY
	$total_hrs = 0;
	$do_display[$i] = 0;

    /*#############################################################################
      IF USER IS GROUPED WITH A SUB DEPT AND CONFIG FILE IS SETUP TO CHECK THE
      WHOLE DEPT. RUN THIS WITH THE SQL ABOVE.
    ###############################################################################*/
    if($include_default_dept == '1'){
        //RESET VARIABLE
        $dflt_restriction = "";

        //GET DEFAULT DEPT CONFIG
        $dflt_dept_sql = @mysql_query("SELECT * FROM vf_config
                WHERE dept_id = '$DeptID'
                AND sub_dept_id = '0'");

        $default_array = @mysql_fetch_array($dflt_dept_sql);
            $dflt_EmployeesOff = $default_array["emp_off_ttl"];
            $dflt_OffType = $default_array["people_off_type"];

        //CHECK DATE RESTRICTIONS FOR THE DEFAULT DEPARTMENT
        $dflt_date_allow = @mysql_query("SELECT * FROM vf_off_perday WHERE " .
            "day = '$check_day' AND dept_id = '$DeptID' " .
            "AND sub_dept_id = '0'");
            $dflt_date_restrict = @mysql_fetch_array($dflt_date_allow);
                $dflt_restriction = $dflt_date_restrict["total_off"];
	}
    /*#############################################################################*/


    /*******************************************************************
    ** IF THE CONFIG FILE IS COUNTING BY PEOPLE OFF PER DAY USE THIS. **
    *******************************************************************/
    if($OffType == "P"){

        /*#############################################################################
          IF USER IS GROUPED WITH A SUB DEPT AND CONFIG FILE IS SETUP TO CHECK THE
          WHOLE DEPT. RUN THIS WITH THE SQL ABOVE.
          #############################################################################*/
	    if($include_default_dept == '1'){
            //COUNT THE NUMBER OF PEOPLE OFF
 	        $dflt_sql=("SELECT COUNT(*) AS Total FROM vf_vacation, vf_employee
	        	WHERE vf_vacation.date = $check_day
	            AND vf_vacation.dept_id = '$DeptID'
 	        	AND vf_employee.enabled != 'N'
	            AND vf_vacation.emp_id = vf_employee.emp_id");

	            $dflt_sql_result = @mysql_query($dflt_sql);
	            $dflt_sql_result_array = @mysql_fetch_array($dflt_sql_result);
	            $dflt_total = $dflt_sql_result_array['Total'];
		}
        /*#############################################################################*/

		//RUN THE QUERY TO GET A TOTAL COUNT OF PEOPLE OFF THE CURRENT DAY
		$SQL=("SELECT COUNT(*) AS Total FROM vf_vacation, vf_employee
        	WHERE vf_vacation.date = $check_day
            AND vf_vacation.dept_id = '$DeptID'
			AND vf_employee.enabled != 'N'
            AND vf_employee.sub_dept_id = '$employee_sub_dept'
            AND vf_vacation.emp_id = vf_employee.emp_id");
		$SQL_Result = @mysql_query($SQL);
		$SQL_Result_Array = @mysql_fetch_array($SQL_Result);
		$Total=$SQL_Result_Array['Total'];

    }elseif($OffType == "H"){
        /*#############################################################################
          IF USER IS GROUPED WITH A SUB DEPT AND CONFIG FILE IS SETUP TO CHECK THE
          WHOLE DEPT. RUN THIS WITH THE SQL ABOVE.
          #############################################################################*/
	    if($include_default_dept == '1'){
            //COUNT THE NUMBER OF PEOPLE OFF
 	        $dflt_sql=("SELECT * FROM vf_vacation, vf_employee
	        	WHERE vf_vacation.date = $check_day
	            AND vf_vacation.dept_id = '$DeptID'
 	        	AND vf_employee.enabled != 'N'
	            AND vf_vacation.emp_id = vf_employee.emp_id");
	            $dflt_sql_result = @mysql_query($dflt_sql);

	            $dflt_total = $dflt_sql_result_array['Total'];
	            while($dflt_sql_result_array = @mysql_fetch_array($dflt_sql_result)){
	                $dflt_type_ttl = $dflt_sql_result_array["hours"];
	                $dflt_total = $dflt_total + $dflt_type_ttl;
	            }
 		}
        /*#############################################################################*/

		//TALLY UP ALL HOURS OF TIME OFF FOR THE DAY
		$Select_Vac = @mysql_query("SELECT * FROM vf_vacation, vf_employee
        	WHERE vf_vacation.date = '$check_day'
            AND vf_vacation.dept_id = '$DeptID'
			AND vf_employee.enabled != 'N'
            AND vf_employee.sub_dept_id = '$employee_sub_dept'
            AND vf_vacation.emp_id = vf_employee.emp_id");

		while($Get_Vac = @mysql_fetch_array($Select_Vac)){
			$Type_TTL = $Get_Vac["hours"];
			$Total = $Total + $Type_TTL;
		}
    }

    /*#############################################################################
      IF USER IS GROUPED WITH A SUB DEPT AND CONFIG FILE IS SETUP TO CHECK THE
      WHOLE DEPT. RUN THIS WITH THE SQL ABOVE.
      #############################################################################*/
    if($include_default_dept == '1'){
	    if($dflt_restriction != "X"){
	        //IF THERE ARE RESTRICTIONS PRESENT THEM. OTHERWISE USE
	        //THE DEFAULT VALUE
	        if($dflt_restriction != ""){
	            $dflt_TtlOff = $dflt_restriction;
	        }else{
	            $dflt_TtlOff = $dflt_EmployeesOff;
	        }

	        //CALCULATE THE AMOUNT OF PEOPLE WHO CAN STILL TAKE OFF
	        $dflt_Available = $dflt_TtlOff - $dflt_total;

	        if($dflt_Available <= 0){
	            //IF NO TIME IS AVAILABLE SET THE VARIABLE ON
	            $do_display[$i] = 1;
            }
	    }else{
	        //IF THE DATE IS RESTRICTED SET THE VARIABLE ON
	        $do_display[$i] = 1;
	    }
	}
    /*#############################################################################*/

    //IF THERE ARE RESTRICTIONS FOR THIS DATE LET THE USER KNOW
    if($restriction != "X"){
        //IF THERE ARE RESTRCITIONS PRESENT THEM. OTHERWISE USE
        //THE DEFAULT VALUE
        if($restriction != ""){
            $ttl_off = $restriction;
        }else{
            $ttl_off = $EmployeesOff;
        }

        //CALCULATE THE AMOUNT OF HOURS STILL ALLOWED TO TAKE OFF
        $available = $ttl_off - $Total;

        if($available <= 0){
            //IF NO TIME IS AVAILABLE SET THE VARIABLE ON
            $do_display[$i] = 1;
        }
    }else{
        //IF THE DATE IS RESTRICTED SET THE VARIABLE ON
        $do_display[$i] = 1;
    }
}

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

/**** CREATE 7 DROP DOWN BOXES FOR VACTION TYPES ( 1 FOR EACH DAY OF THE WEEK) ****/
for ($x=0; $x<7; $x++) {
	//LOAD ALL DAY OFF TYPES
	$off_type[$x] = "";

	$off_type[$x] .= "<option>Select Type</option>\n";
	for ($i=0; $i<$array_count; $i++) {
	    $type_value = $time_off_type[$i][0];
	    $type_descr = $time_off_type[$i][1];

        if($type_value == $type[$x]){
			$select = "selected";
		}

	    //LOAD THE DROP DOWN
		$off_type[$x] .= "<option $select value=\"$type_value\">$type_descr</option>\n";
        $select = "";
	}
}
/************ END OF BUILD AN ARRAY LIST OF DAY OFF TYPES ****************/
$hdr_detail = 'Time Off Information For<br />'.$user_name;
$cur_page_title = "Add weekly vacation";
require(DIR_PATH."includes/header.inc.php");
?>

       <table border="0" cellpadding="0" cellspacing="0" width="690" style="margin-left:10;">
		  <tr>
		    <td><b>To request a vacation or other time off type:</b></td>
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
		    <td></td>
		  </tr>
		</table>
        <?PHP echo $feedback; 
        if(isset($error_msg))
        	echo $error_msg;
        ?>
        <form method="POST" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>">
         <table border="0" cellpadding="0" cellspacing="0" width="700">
		  <tr>
		   <td>
		     <!--MAIN TABLE--->
			  <table border="3" cellpadding="0" cellspacing="0" style="border-color:#00319C" width="475" align="center">
			    <tr>
			      <td>
		            <!--EMPLOYEE TABLE-->
			          <table border="0" cellpadding="0" cellspacing="0" style="background-color:#C0C0C0;" width="100%">
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
	          <table border="0" cellpadding="0" cellspacing="0" style="background-color:#C0C0C0;" width="100%" >
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
	              <input type="hidden" value="<?PHP echo date("Ymd",strtotime($GoToDay)); ?>" name="submit_date[0]">
	              &nbsp;</td>
	              <td align="right">Sunday-<?PHP echo $GoToDay; ?>&nbsp;</td>
				<?PHP
	            //IF do_display[x] = 1 THEN THE DAY IS NOT AVAILABLE IF IT EQUALS 0 THEN DISPLAY THE OPTION
			 	if($do_display[0] == 1){
					echo'
		              <td><b><span style="color:#FF0000;">Full</span></b></td>
		              <td><b><span style="color:#FF0000">Full</span></b></td>';
				}else{
					echo '
		              <td><input style="text-align: Center;" type="text" name="hours[0]" size="8" value="'.$hours[0].'"></td>
		              <td><select size="1" name="type[0]">'.$off_type[0].'</select></td>';
				}
				?>
	           <td width="10">&nbsp;</td>
	            </tr>
	            <tr>
	              <td width="10">
	              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$GoToDay +1 days")); ?>" name="submit_date[1]">
	              &nbsp;</td>
	              <td align="right">Monday-<?PHP echo date("m/d/y",strtotime("$GoToDay +1 days")); ?>&nbsp;</td>
				<?PHP 
	            //IF do_display[x] = 1 THEN THE DAY IS NOT AVAILABLE IF IT = 0 THEN DISPLAY THE OPTION
			 	if($do_display[1] == 1){
					echo'
		              <td><b><span style="color:#FF0000">Full</spant></b></td>
		              <td><b><span style="color:#FF0000">Full</span></b></td>';
				}else{
					echo '
		              <td><input style="text-align: Center;" type="text" name="hours[1]" size="8" value="'.$hours[1].'"></td>
		              <td><select size="1" name="type[1]">'.$off_type[1].'</select></td>';
				}
				?>
	           
	              <td width="10">&nbsp;</td>
	            </tr>
	            <tr>
	              <td width="10">
	              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$GoToDay +2 days")); ?>" name="submit_date[2]">
	              &nbsp;</td>
	              <td align="right">Tuesday-<?PHP echo date("m/d/y",strtotime("$GoToDay +2 days")); ?>&nbsp;</td>
				<?PHP
	            //IF do_display[x] = 1 THEN THE DAY IS NOT AVAILABLE IF IT = 0 THEN DISPLAY THE OPTION
			 	if($do_display[2] == 1){
					echo '
		              <td><b><span style="color:#FF0000">Full</span></b></td>
		              <td><b><span style="color:#FF0000">Full</span></b></td>';
				}else{
					echo '
	              <td><input style="text-align: Center;" type="text" name="hours[2]" size="8" value="' . $hours[2] . '"></td>
	              <td><select size="1" name="type[2]">' . $off_type[2] . '</select></td>';
				}
				?>
	
	              <td width="10">&nbsp;</td>
	            </tr>
	            <tr>
	              <td width="10">
	              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$GoToDay +3 days")); ?>" name="submit_date[3]">
	              &nbsp;</td>
	              <td align="right">Wednesday-<?PHP echo date("m/d/y",strtotime("$GoToDay +3 days")); ?>&nbsp;</td>
				<?PHP
	            //IF do_display[x] = 1 THEN THE DAY IS NOT AVAILABLE IF IT = 0 THEN DISPLAY THE OPTION
			 	if($do_display[3] == 1){
					echo '
		              <td><b><span style="color:#FF0000">Full</span></b></td>
		              <td><b><span style="color:#FF0000">Full</span></b></td>';
				}else{
					echo '
		              <td><input style="text-align: Center;" type="text" name="hours[3]" size="8" value="' . $hours[3] . '"></td>
		              <td><select size="1" name="type[3]">' . $off_type[3] . '</select></td>';
				}
				?>
	           
	              <td width="10">&nbsp;</td>
	            </tr>
	            <tr>
	              <td width="10">
	              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$GoToDay +4 days")); ?>" name="submit_date[4]">
	              &nbsp;</td>
	              <td align="right">Thursday-<?PHP echo date("m/d/y",strtotime("$GoToDay +4 days")); ?>&nbsp;</td>
				<?PHP 
	            //IF do_display[x] = 1 THEN THE DAY IS NOT AVAILABLE IF IT = 0 THEN DISPLAY THE OPTION
			 	if($do_display[4] == 1){
					echo '
		              <td><b><span style="color:#FF0000">Full</span></b></td>
		              <td><b><span style="color:#FF0000">Full</span></b></td>';
				}else{
					echo '
		              <td><input style="text-align: Center;" type="text" name="hours[4]" size="8" value="' . $hours[4] . '"></td>
		              <td><select size="1" name="type[4]">' . $off_type[4] . '</select></td>';
				}
				?>
	           
	              <td width="10">&nbsp;</td>
	            </tr>
	            <tr>
	              <td width="10">
	              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$GoToDay +5 days")); ?>" name="submit_date[5]">
	              &nbsp;</td>
	              <td align="right">Friday-<?PHP echo date("m/d/y",strtotime("$GoToDay +5 days")); ?>&nbsp;</td>
				<?PHP
	            //IF do_display[x] = 1 THEN THE DAY IS NOT AVAILABLE IF IT = 0 THEN DISPLAY THE OPTION
			 	if($do_display[5] == 1){
					echo '
		              <td><b><span style="color:#FF0000">Full</span></b></td>
		              <td><b><span style="color:#FF0000">Full</span></b></td>';
				}else{
					echo '
		              <td><input style="text-align: Center;" type="text" name="hours[5]" size="8" value="' . $hours[5] . '"></td>
		              <td><select size="1" name="type[5]">' . $off_type[5] . '</select></td>';
				}
				?>
	           
	              <td width="10">&nbsp;</td>
	            </tr>
	            <tr>
	              <td width="10">
	              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$GoToDay +6 days")); ?>" name="submit_date[6]">
	              &nbsp;</td>
	              <td align="right">Saturday-<?PHP echo date("m/d/y",strtotime("$GoToDay +6 days")); ?>&nbsp;</td>
				<?PHP 
	            //IF do_display[x] = 1 THEN THE DAY IS NOT AVAILABLE IF IT = 0 THEN DISPLAY THE OPTION
			 	if($do_display[6] == 1){
					echo '
		              <td><b><span style="color:#FF0000">Full</span></b></td>
		              <td><b><span style="color:#FF0000">Full</span></b></td>';
				}else{
					echo '
		              <td><input style="text-align: Center;" type="text" name="hours[6]" size="8" value="' . $hours[6] . '"></td>
		              <td><select size="1" name="type[6]">' . $off_type[6] . '</select></td>';
				}
				?>
	           
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
	              <input type="hidden" value="<?PHP echo $do_display[0]; ?>" name="do_display[0]">
	              <input type="hidden" value="<?PHP echo $do_display[1]; ?>" name="do_display[1]">
	              <input type="hidden" value="<?PHP echo $do_display[2]; ?>" name="do_display[2]">
	              <input type="hidden" value="<?PHP echo $do_display[3]; ?>" name="do_display[3]">
	              <input type="hidden" value="<?PHP echo $do_display[4]; ?>" name="do_display[4]">
	              <input type="hidden" value="<?PHP echo $do_display[5]; ?>" name="do_display[5]">
	              <input type="hidden" value="<?PHP echo $do_display[6]; ?>" name="do_display[6]">
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
  </td>
 </tr>
</table>
<!--END OF MAIN TABLE--->
 <?PHP echo $time_summary; ?>
</form>
<p>&nbsp;</p>
<?PHP require(DIR_PATH."includes/footer.inc.php");?>