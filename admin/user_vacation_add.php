<?PHP
/******************************************************************************
**  File Name: user_vacation_add.php
**  Description: Allows an administrator to add vacation for an employee.
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

//IF THE EMPLOYEE ISN'T A DEPARTMENT ADMIN. STOP THEM FROM USING THE SCRIPT
if($_SESSION["ses_is_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}


//SET PAGE VARIABLES
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];//Concatenate first and last name
$ename = $_POST["ename"];
$edept = $_POST["edept"];
$emp_sub_dept = $_POST["emp_sub_dept"];
$submit_date = $_POST["submit_date"];
$add_vacation = $_POST["add_vacation"];
$hours = $_POST["hours"];
$type = $_POST["type"];
$sunday_choice = $_POST["sunday_choice"];
$employee = $_POST["employee"];
$check_conf = $_POST["check_conf"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year
require(DIR_PATH."includes/selected_year.inc.php");//get the selected fiscal year

//THIS FUNCTION WILL BE CALLED FROM THE THE SCRIPT JUST BELOW THE FUNCTION END
function Ck_employees_off($emp_off_ttl){
  global $submit_date,$emp_sub_dept,$i,$chk_sub,$message,$error_on_page,$day_of_wk;
  global $get_emp_off_sql;

    /***********************************************************************/
    //MAKE SURE THE DAY IS NOT FULL
    /***********************************************************************/
    //VARIABLES TO TOTAL THE TIME ALREADY USED ON THE CURRENT DATE
    $count_number_off = 0;
    $ttl_off_hours = 0;
    $already_scheduled = 0;

    //SELECT ALL TIME OFF BEING USED FOR THE CURRENT DATE
    $get_emp_off = @mysql_query($get_emp_off_sql);

    while($emp_off_array = @mysql_fetch_array($get_emp_off)){
        $off_hours = $emp_off_array["hours"];
        $current_employee = $emp_off_array["emp_id"];

      //DEPENDING ON THE TRACKING TYPE. ADD THE VALUES OF TIME OFF. EITHER HOURS OR
        //PEOPLE OFF
        if($_SESSION["ses_offtype"] == "P"){
            $count_number_off =  $count_number_off + 1;
        }elseif($_SESSION["ses_offtype"] == "H"){
            $ttl_off_hours = $ttl_off_hours + $off_hours;
        }
    }

    //DEPENDING ON THE TRACKING TYPE. CHECK IF THERE ARE ALREADY TOO MANY
    //PEOPLE OFF
    if($_SESSION["ses_offtype"] == "P"){
        if($count_number_off >= $emp_off_ttl){
            //SET ERROR VARIABLE TO ON
            $error_on_page = 1;
            if($chk_sub == 1){
                $message[] = "There are already the alotted number of people off
                    for <b>" . $day_of_wk . "</b> in the department. To override this issue,
                    check the \"Override number of people off check\" checkbox and resubmit.<br />";
            }else{
                $message[] = "There are already the alotted number of people off
                    for <b>" . $day_of_wk . "</b> in the default department. To override this issue,
                    check the \"Override number of people off check\" checkbox and resubmit.<br />";
            }
        }
    }elseif($_SESSION["ses_offtype"] == "H"){
        if($ttl_off_hours >= $emp_off_ttl){
        	
            //SET ERROR VARIABLE TO ON
            $error_on_page = 1;
            if($chk_sub == 1){
                $message[] = "There are already the alotted number of hours used
                    for <b>" . $day_of_wk . "</b> in the department. To override this issue,
                    check the \"Override number of people off check\" checkbox and resubmit.<br />";
            }else{
                $message[] = "There are already the alotted number of hours used
                    for <b>" . $day_of_wk . "</b> in the default department. To override this issue,
                    check the \"Override number of people off check\" checkbox and resubmit.<br />";
            }
        }
    }
}//END OF Ck_employees_off() FUNCTION

/***********************************************************************
** IF THE FORM WAS SUBMITTED UPDATE INFORMATION                       **
***********************************************************************/
if(isset($add_vacation)){

	//RETRIEVE CURRENT VALUES FROM THE CONFIGURATION TABLE
	$conf_sql = @mysql_query("SELECT * FROM vf_config WHERE dept_id = '$edept' AND " .
			" sub_dept_id = '$emp_sub_dept'");
	$config_array = @mysql_fetch_array($conf_sql);
		$include_default_dept = $config_array["include_default"];
		$emp_off_ttl = $config_array["emp_off_ttl"];

    //VARIABLE TO DETERMINE IF THERE ARE ERRORS. IF ERRORS CHANGE VALUE TO 1
	$error_on_page = 0;

    //MAKE SURE AN EMPLOYEE IS SELECTED
    if($employee == "Select an employee"){
		$error_on_page = 1;
	    $message[] = "You have not selected an <b>EMPLOYEE</b>.<br />";
	}

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

	  //CHECK IF THE USER ENTERED ANYTHING IN THE HOURS FIELD OR VACATION TYPE
	  if($hours[$i] != "" || $type[$i] != "Select Type"){
        //MAKE SURE THE HOURS ARE NOT EMPTY IF A TYPE WAS SELECTED
		if($hours[$i] == ""){
            //SET ERROR VARIABLE TO ON
			$error_on_page = 1;
	       	$message[] = "You entered a Time Off type but have not entered an hours " .
		       "value for the<b> " . $day_of_wk . "</b> field. <br />";
		}

        //IF DATA IS IN THE HOUR FIELD MAKE SURE THE USER SELECTS A TYPE
		if($type[$i] == "Select Type"){
            //SET ERROR VARIABLE TO ON
			$error_on_page = 1;
	       	$message[] = "You entered a time value but have not selected a time off " .
		       "type for the<b> " . $day_of_wk . "</b> field. <br />";
        }
        //CHECK THAT ONLY NUMBERS ARE ENTERED IN THE HOURS FIELD
		$ValidChar = (preg_match("/^[0-9.]+$/i",$hours[$i]));
		if($ValidChar == 0){
            //SET ERROR VARIABLE TO ON
			$error_on_page = 1;
	       	$message[] = "Incorrect hours entry in the <b>" . $day_of_wk . "</b> field.<br /> " .
	        	"Only numbers are allowed. If you don't want a value enter 0.<br />";
		}

        //MAKE SURE THE EMPLOYEE DOESN'T ALREADY HAVE THE TIME OFF
		$get_emp_off = @mysql_query("SELECT * FROM vf_vacation WHERE date = '$submit_date[$i]' AND emp_id = '$employee'");
		while($emp_off_array = @mysql_fetch_array($get_emp_off)){
            $current_employee = $emp_off_array["emp_id"];

            //CHECK IF THE EMPLOYEE ALREADY HAS REQUESTED THIS DATE. IF SO SET A SWITCH TO ON
            if($current_employee == $employee){
	            //SET ERROR VARIABLE TO ON
				$error_on_page = 1;
		       	$message[] = "The employee already has scheduled time for ". date("m/d/Y",strtotime($submit_date[$i])) . ".<br />";
            }
    	}

        //**********************************************************************************
        //IF THE USER DOES NOT CHECK THE "Override number of people off check" BOX. RUN THIS
		//**********************************************************************************
        if($check_conf != "ON"){
            //CHECK EMPLOYEES OFF VALUES
            $get_emp_off_sql ="SELECT vf_vacation.hours,vf_vacation.emp_id FROM " .
					"vf_vacation, vf_employee WHERE " .
		        	"vf_vacation.date = $submit_date[$i] AND vf_vacation.dept_id = '$_SESSION[ses_dept_id]' " .
		            "AND vf_employee.sub_dept_id = '$emp_sub_dept' AND vf_employee.enabled != 'N' AND " .
		            "vf_vacation.emp_id = vf_employee.emp_id";
		            
            //USE AS A SWITCH TO GIVE FEEDBACK TO HTE USER WHILE RUNNING THE FUNCTION
            $chk_sub = 1;
            //CALL THE FUNCTION
            Ck_employees_off($emp_off_ttl);
            $chk_sub = 0;
			/**********************************************************************
			** IF THE ABOVE DEPARTMENT WAS A SUB_DEPARTMENT AND $include_default_dept
            ** EQUALS 1. CHECK THE DEFAULT DEPT CONFIG AND RUN CHECKS AGAINST IT  **
			**********************************************************************/

            if($include_default_dept == 1){
            	//CHECK EMPLOYEES OFF VALUES
	            $get_emp_off_sql ="SELECT vf_vacation.hours,vf_vacation.emp_id FROM " .
						"vf_vacation, vf_employee WHERE " .
			        	"vf_vacation.date = $submit_date[$i] AND vf_vacation.dept_id = '$_SESSION[ses_dept_id]' " .
			            "AND vf_employee.enabled != 'N' AND vf_vacation.emp_id = vf_employee.emp_id";
                //CALL THE FUNCTION
	            Ck_employees_off($emp_off_ttl);
            }
	    }//END OF if($check_conf != "ON")
	  }//END OF if($hours[$i] != "" || $type[$i] != "Select Type")
	}//END OF for ($i=0; $i<7; $i++) LOOP

	//IF THERE WERE ANY ERRORS DON'T UPDATE THE TABLE AND LET THE USER KNOW
	if($message){
    	$page_error = "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$page_error .= "$numeric_text. $value ";
	            $numeric_text = $numeric_text + 1;
            }
		$page_error .= "</font><br />\n";
            unset($numeric_text);
    }

	/************************************************************************
	** START ENTERING INFORMATION INTO THE DATABASE IF THERE WERE NO ERRORS**
	************************************************************************/
	if($error_on_page == 0){

     	//LOAD AN ARRAY OF TIME OFF TYPES
         $vacation_types = array();
         $type_count = 0;
		 $vac_sql = @mysql_query("SELECT * from vf_to_type WHERE dept_id = '0' OR dept_id = '$_SESSION[ses_dept_id]'");
		 while($vac_result = @mysql_fetch_array($vac_sql)){
		 	$TypeID = $vac_result["to_id"];
		    $Descr = $vac_result["descr"];

		    $vacation_types[$type_count][0] = $TypeID;
		    $vacation_types[$type_count][1] = $Descr;

		    $type_count = $type_count + 1;
		 }

	    //CONVERT SUNDAY DATE TO A READABLE FORMAT
	    $sunday_pick = date("m/d/Y",strtotime("$sunday_choice"));

		//INITIALIZE A VARIABLE TO OPEN A WINDOW TO PRINT CHANGES
        $new_win_content = "";

	    //START A STRING TO GIVE FEEDBACK TO THE USER AFTER UPDATING THE ENTRY.
		$feedback = "<p style=\"margin-left:10;\"><font color=\"#FF0000\" size=\"5\"><b>**
	    	Time off updated for $ename! **</b></font><br />";

	    //ENTER ANY BOX THAT HAS DATA INTO THE VACATION DATABASE
	    //SINCE THERE A 7 BOXES OF TYPE AND HOURS ITERATE 7 TIMES
		for ($i=0; $i<7; $i++) {
		  if($type[$i] != "Select Type"){
			$ValidChar = (preg_match("/^[0-9.]+$/i",$hours[$i]));
			if($ValidChar != 0){
		        switch ($i) {
		          case 0:
		            $day_of_wk = date("Ymd",strtotime("$sunday_choice"));
		            break;
		          case 1:
		            $day_of_wk = date("Ymd",strtotime("$sunday_pick +1 days"));
		            break;
		          case 2:
		            $day_of_wk = date("Ymd",strtotime("$sunday_pick +2 days"));
		            break;
		          case 3:
		            $day_of_wk = date("Ymd",strtotime("$sunday_pick +3 days"));
		            break;
		          case 4:
		            $day_of_wk = date("Ymd",strtotime("$sunday_pick +4 days"));
		            break;
		          case 5:
		            $day_of_wk = date("Ymd",strtotime("$sunday_pick +5 days"));
		            break;
		          case 6:
		            $day_of_wk = date("Ymd",strtotime("$sunday_pick +6 days"));
		            break;
		        }
		        
		        //IF THE ADMIN OVER RIDES THE CHECK FOR THE NUMBER OF PEOPLE OFF SET TO Y
				if($check_conf == "ON"){		        
					$ck_if_ovrride = "Y";
				}else{
					$ck_if_ovrride = "N";	
				}
				
	            //GET THE CURRENT TIME AND DATE TO USE AS TIME ENTERED ENTRY
	            $cur_date_time = date("YmdHis");

            	//DETERMINE THE YEAR OF THE SELECTED DATE
				//CALL FUNCTION TO RETURN THE VALUE
			    $selected_year = strtotime($day_of_wk);
				$year_select = selected_date($selected_year);     

	            //INSERT EACH ENTRY IF THERE WAS A TIME OFF TYPE TO ADD
	            $query_add = "INSERT INTO vf_vacation
	            (emp_id,dept_id,date,hours,to_id,apprv_by,entered_by,ovrride_time_ck,date_entered,year)
	            VALUES
	            ('$employee','$edept','$day_of_wk','$hours[$i]','$type[$i]','$_SESSION[ses_emp_id]','$_SESSION[ses_emp_id]','$ck_if_ovrride','$cur_date_time','$year_select')";
	              
	            @mysql_query($query_add) or die (mysql_error());

                //RETRIEVE THE VACTION NAME
                for ($x=0; $x<$type_count; $x++) {
	                if($type[$i] == $vacation_types[$x][0]){
						$vac_name = $vacation_types[$x][1];
                    }
                }

			    //ADD MORE INFORMATION TO THE FEEDBACK THAT LISTS EACH DAY REQUESTED
				$feedback .= "Added $hours[$i] hours $vac_name for " . date("l F d, Y",strtotime($day_of_wk)) . "<br />";

                //ADD INFORMATION TO THE VARIABLE TO PRINT INFORMATION
                $new_win_content .="<tr>".
		             "<td>" . date("m/d/Y",strtotime($day_of_wk)) ."</td>".
		             "<td>" . $hours[$i] . "</td>".
		             "<td>" . $vac_name . "</td>".
		            "</tr>";

                //RESET THE VALUE
                $vac_name = "";
			}
	      }
		}

	    /**************************************************************************
	    ** GIVE THE USER FEEDBACK BY CREATING A SCRIPT AND OPENING IT IN A NEW   **
	    ** WINDOW THAT CAN BE PRINTED                                            **
	    **************************************************************************/
        //BUILD HEADER INFORMATION
    	$new_win_header = "The following was added by " . $_SESSION["ses_first_name"] . " " .
		$_SESSION["ses_last_name"] . " on " . date("l F d, Y");

        //ADD INFO TO VARIABLE THAT IS SET LATER IN THIS SCRIPT
        $hdr_addin = "<script language=\"javascript\">
			window.open('print_time_off_request.php?content=$new_win_content&header=$new_win_header&ename=$ename','newWind', \"width=450,height=400,toolbar=no,scrollbars=yes,resizable=no\")
		    </script>";


	     //CLEAR ALL THE VARIABLES
	     unset($employee);
	     unset($sunday_choice);
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
	}
}

/***********************************************************************
** IF THE USER SELECTS AN EMPLOYEE TO ADD A VACATION FOR. GET SUMMARY **
***********************************************************************/
if(isset($employee)){

	//GET THE VALUE OF THE SELECTED CALENDAR DATE
	$request_date = strtotime($sunday_choice);
	$summary_year = selected_date($request_date);	                	

	$time_summary = '
	    <p style="margin-left: 10;"><b><u>SUMMARY INFORMATION</u></b></p>
	    <table border="3" cellpadding="0" cellspacing="0" bgcolor="#C0C0C0" width="720" style="margin-left: 10; border-color:#00319C">
	      <tr>
	        <td>
				<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#C0C0C0">
                  <tr>
                    <th bgcolor="#00319C" width="161">&nbsp;</th>
				    <th bgcolor="#00319C" colspan="3" width="247" style="border-bottom-style: solid; border-bottom-width: 1px"><font color="#FFFFFF">Already
                      Scheduled time</font></th>
				    <th bgcolor="#00319C"><b><font color="#FFFFFF">|</font></b></th>
				    <th bgcolor="#00319C" colspan="2" width="276" style="border-bottom-style: solid; border-bottom-width: 1px"><font color="#FFFFFF">Total time</font></th>
                  </tr>
				  <tr>
				    <th bgcolor="#00319C"><font color="#FFFFFF">Description</font></th>
				    <th bgcolor="#00319C"><font color="#FFFFFF">Scheduled</font></th>
				    <th bgcolor="#00319C"><font color="#FFFFFF">Used</font></th>
			    	<th bgcolor="#00319C"><font color="#FFFFFF">Remaining</font></th>
			    	<th bgcolor="#00319C"><font color="#FFFFFF"> | </font></th>
				    <th bgcolor="#00319C"><font color="#FFFFFF">Total Unused</font></th>
				    <th bgcolor="#00319C"><font color="#FFFFFF">Left to Schedule</font></th>
				  </tr>';

	//GET ALL OF THE TIME OFF TYPES
	$SQL = @mysql_query("SELECT * from vf_to_type");
    while($Result = @mysql_fetch_array($SQL)){
		$TypeID = $Result["to_id"];
        $Descr = $Result["descr"];
        $Valid_date = $Result["type_date"];

        //IF THERE IS A VALID DATE FOR THIS VACATION TYPE LET THE USER KNOW
        //VALID DATE DESIGNATES THAT THE VACATION TYPE CANNOT BE SCHEDULED
        //UNTIL THAT DATE OR LATER.
        if($Valid_date != "0000-00-00"){
	        $Type_Date = "Can't be used before: "  . date("m/d/Y",strtotime($Valid_date));
    	}
    	
        //LOOKUP THE TOTAL VACATION TIME THE EMPLOYEE HAS EARNED FOR THIS TIME OFF TYPE
        $GetVac = @mysql_query("SELECT * FROM vf_emp_to_hours WHERE to_id = '$TypeID' " .
        					"AND year = '$summary_year' AND emp_id = '$employee'");
		$Vac_Array = @mysql_fetch_array($GetVac);
        	$Earned = $Vac_Array["hours"];

		 //IF THE EMPLOYEE HAS TIME EARNED GET DETAILS OTHERWISE SKIP THIS TYPE
		 if($Earned != "" || $Valid_date != ""){
            //RESET ALL THE VARIABLES
            $TtlUsed = 0;
            $TtlScheduled = 0;
            $TtlRemaining = 0;
            $TtlToSchedule = 0;
			$TtlUnused = 0;

            //LOOKUP ALL OF THE EMPLOYEES SCHEDULED TIME OFF FOR THE CURENT TYPE
			$Get_Scheduled = @mysql_query("SELECT * FROM vf_vacation WHERE " .
               		"to_id = '$TypeID' AND emp_id = '$employee' AND year = '$summary_year'");
			while($Load_Sched = @mysql_fetch_array($Get_Scheduled)){
            	$Vac = $Load_Sched["hours"];
                $VacDate = $Load_Sched["date"];

                //CONVERT THE FORMAT OF THE VACATION DATE
                $VacDate = strtotime($VacDate);

                //TOTAL HOURS THAT ARE SCHEDULED
				$TtlScheduled = $TtlScheduled + $Vac;

                //IF DATE HAS PASSED. CONSIDER VACATION AS USED
                if($VacDate <= $TodaysDate){
                	$TtlUsed = $TtlUsed + $Vac;
                }

                //IF DATE HAS NOT PASSED. CONSIDER VACATION AS REMAINING
                if($VacDate > $TodaysDate){
                	$TtlRemaining = $TtlRemaining + $Vac;
                }
            }
                //CHECK HOW MANY HOURS ARE LEFT TO SCHEDULE
                $TtlToSchedule = $Earned - $TtlScheduled;

                //CHECK HOW MANY HOURS ARE STILL UNUSED
                $TtlUnused = $Earned - $TtlUsed;

                //IF TYPE IS NOT AN EARNED TYPE. BLANK OUT SOME DISPLAYS
                if($Earned == ""){
	                //$TtlRemaining = "";
	                $TtlUnused = "";
	                $TtlToSchedule = "";
                }
				$time_summary .= '
				  <tr>
				    <td>&nbsp;' . $Descr . '</td>
				    <td align="center">' . $TtlScheduled . '</td>
				    <td align="center">' . $TtlUsed . '</td>
				    <td align="center">' . $TtlRemaining . '</td>
				    <td align="center"> | </td>
				    <td align="center">' . $TtlUnused . '</td>
				    <td align="center">' . $TtlToSchedule . '</td>
				  </tr>';
    	}
	}

	$time_summary .= "</table>
					</td>
				   </tr>
				  </table>";
}

/*******************************************************************************
*** BUILD AND PIECE TOGETHER PARTS OF THE FORM                               ***
*******************************************************************************/

/************ BUILD A DROP DOWN LIST OF SUNDAY DATES ****************/
//CONVERT TODAYS DATE TO A STRING TO CALCULATE BELOW
$today = date("m/d/y",time());
$mydate = getdate(time());
//GET THE NUMBER OF THE DAY OF THE WEEK TO SUBTRACT FROM TODAY AND GET SUNDAYS DATE
$day_num = $mydate [ "wday" ];
//CREATE VARIABLE TO HOLD MINUS DAYS
$sub_days = "-".$day_num."days";
//GET SUNDAYTS DATE IN SECONDS SINCE 1970
$sunday = date(strtotime("$today $sub_days"));
$this_sunday_date = date("m/d/Y", $sunday);

//SET VARIBALE TO DISPLAY THE DATE BESIDE THE WEEKDAY ON THE DISPLAY FORM
if(isset($sunday_choice)){
    $sunday_display = $sunday_choice; //strtotime($sunday_choice);
}else{
    $sunday_display = $this_sunday_date; //$sunday;
}

//GET SUNDAYS DATE FROM ONE YEAR AGO
$last_year = mktime (0,0,0,date("m"),  date("d"),  date("Y")-1);
$mydate = getdate ($last_year);
$day_num = $mydate [ "wday" ];
//CREATE VARIABLE TO HOLD MINUS DAYS
$sub_days = "-".$day_num."days";
$last_year = date("m/d/y",$last_year);
$sunday = date(strtotime("$last_year $sub_days"));
$sunday_date = date("m/d/Y", $sunday);

//START BUILDING THE DROP DOWN
$sunday_box = "<select onChange=\"this.form.submit(sunday_choice)\" size=\"1\" name=\"sunday_choice\">";

//LOAD TWO + YEARS OF DATES STARTING FROM ONE YEAR AGO
for ($i=0; $i<124; $i++) {
	$sunday_date = date("m/d/Y", strtotime ("$sunday_date +1 week"));

	if(isset($sunday_choice)){
		$sunday_choice = date("m/d/Y", strtotime($sunday_choice));
		if($sunday_date == $sunday_choice){
	    	$selected = "selected";
		}
	}elseif($sunday_date == $this_sunday_date){
    	$selected = "selected";//IF THE DATE IS LAST SUNDAYS DATE SELECT IT
	}
    $DateValue = date("Ymd", strtotime ("$sunday_date"));
	$sunday_box .= "<option $selected value=\"$DateValue\">$sunday_date</option>\n";
    //RESET THE VARIABLE
	$selected = "";
}
//CLOSE THE DROP DOWN
$sunday_box .= "</select>";
/************ END OF BUILD A DROP DOWN LIST OF SUNDAY DATES ****************/

/************ BUILD A DROP DOWN LIST OF EMPLOYEES ****************/
//LOOK UP ALL THE EMPLOYEES FOR THE CURRENT DEPARTMENT
$get_employee = @mysql_query("SELECT * FROM vf_employee WHERE Dept_id = '$_SESSION[ses_dept_id]' AND vf_employee.enabled != 'N' " .
		"ORDER BY lname,fname ASC");

$employee_box = "<select onChange=\"this.form.submit(employee)\" size=\"1\" name=\"employee\"><option>Select an employee</option>";
while($emp_array = @mysql_fetch_array($get_employee)){
        $Emp_EmpID = $emp_array["emp_id"];
        $Emp_FName = $emp_array["fname"];
        $Emp_MName = $emp_array["mname"];
        $Emp_LName = $emp_array["lname"];
        $Emp_dept = $emp_array["dept_id"];
        $emp_sub_dept = $emp_array["sub_dept_id"];
        $Eligible = $emp_array["status"];
        $Emp_NAME = $Emp_LName . ", " . $Emp_FName;

        if($Emp_EmpID == $employee){
			$select = "selected";
			//IF AN EMPLOYEE IS SELECTED SET THE FOLLOWING VARIABLES
			$emp_inf = '<input type="hidden" value="'.$Emp_NAME.'" name="ename">
                      <input type="hidden" value="'.$Emp_dept.'" name="edept">
                      <input type="hidden" value="'.$emp_sub_dept.'" name="emp_sub_dept">';
		}
		
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
			$select = "";
		}

        unset($Emp_EmpID);
        unset($Emp_FName);
        unset($Emp_MName);
        unset($Emp_LName);
        unset($Emp_dept);
        unset($emp_sub_dept);
        unset($Eligible);
        unset($Emp_NAME);
}
$employee_box .= "</select>";

//IF AN EMPLOYEE IS SELECTED SET THE FOLLOWING VARIABLES
if(isset($employee)){
	$employee_box .= $emp_inf;
}
/************ END OF BUILD A DROP DOWN LIST OF EMPLOYEES ****************/

/************ BUILD AN ARRAY LIST OF DAY OFF TYPES ****************/
$time_off_type = array();
$array_cnt = 0;

//GET THE TIME OFF TYPES FROM THE DATABASE
$type_sql = @mysql_query("SELECT * from vf_to_type");
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

//CREATE 7 DROP DOWN BOXES FOR VACTION TYPES ( 1 FOR EACH DAY OF THE WEEK)
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

$cur_page_title = "Add Vacation";
$itemcnt = 2; //Count of select boxes on the current page
require(DIR_PATH."includes/header.inc.php");

echo $page_error;	
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-left: 10;">
  <tr>
    <td><b>To add a vacation for an employee:</b></td>
  </tr>
  <tr>
    <td>1. Select the Sunday date of the week you are working with.</td>
  </tr>
  <tr>
    <td>2. Select the employee that you are working with.</td>
  </tr>
  <tr>
    <td>3. Add the hours requested on the correct day of the week.</td>
  </tr>
  <tr>
    <td>4. Select the time off type from the drop down that corresponds
    with the day of the week.</td>
  </tr>
  <tr>
    <td><b>Note:</b> Once an employee is selected the page will reload
    and add a summary for that employee at the bottom of this page. </td>
  </tr>
  <tr>
    <td></td>
  </tr>
</table>

<?PHP echo $feedback; ?>

    <form method="POST" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>">
     <!--MAIN TABLE--->
	  <table border="3" cellpadding="0" cellspacing="0" width="475" style="margin-left: 90; border-color:#00319C">
	    <tr>
	      <td>
            <!--EMPLOYEE TABLE-->
	          <table bgcolor="#C0C0C0" border="0" cellpadding="0" cellspacing="0" bgcolor="#00FFFF" width="100%">
	            <tr>
	              <td>&nbsp;</td>
	              <td>&nbsp;</td>
	            </tr>
	            <tr>
	              <td align="right">Entered by:&nbsp;</td>
	              <td align="left"><b><?PHP echo $user_name; ?></b></td>
	            </tr>
	            <tr>
	              <td>&nbsp;</td>
	              <td>&nbsp;</td>
	            </tr>
	            <tr>
	              <td align="right">Select a Sunday date:&nbsp;</td>
              	  <td align="left">
              	   <div id="HideItem0" style="POSITION:relative">
              	   <?PHP echo $sunday_box; ?>
              	  </div>
              	  </td>
	            </tr>
	            <tr>
	              <td>&nbsp;</td>
	              <td>&nbsp;</td>
	            </tr>
	            <tr>
	              <td align="right">Select an employee:&nbsp;</td>
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
	          </table>
            <!--END OF EMPLOYEE TABLE-->
      </td>
    </tr>
    <tr>
      <td>
        <!--INFORMATION TABLE-->
          <table border="0" cellpadding="0" cellspacing="0" bgcolor="#C0C0C0" width="100%" align="center">
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
              <input type="hidden" value="<?PHP echo  date("Ymd",strtotime($sunday_display)); ?>" name="submit_date[0]">
              &nbsp;</td>
              <td align="right">Sunday-<?PHP echo date("m/d/y",strtotime($sunday_display)); ?>
              &nbsp;</td>
              <td><input style="text-align: Center;" type="text" name="hours[0]" size="8" value="<?PHP echo $hours[0]; ?>"></td>
              <td><select size="1" name="type[0]"><?PHP echo $off_type[0]; ?></select></td>
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">
              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$sunday_display + 1days")); ?>" name="submit_date[1]">
              &nbsp;</td>
              <td align="right">Monday-<?PHP echo date("m/d/y",strtotime("$sunday_display + 1days")); ?>
              &nbsp;</td>
              <td><input style="text-align: Center;" type="text" name="hours[1]" size="8" value="<?PHP echo $hours[1]; ?>"></td>
              <td><select size="1" name="type[1]"><?PHP echo $off_type[1]; ?></select></td>
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">
              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$sunday_display + 2days")); ?>" name="submit_date[2]">
              &nbsp;</td>
              <td align="right">Tuesday-<?PHP echo date("m/d/y",strtotime("$sunday_display + 2days")); ?>
              &nbsp;</td>
              <td><input style="text-align: Center;" type="text" name="hours[2]" size="8" value="<?PHP echo $hours[2]; ?>"></td>
              <td><select size="1" name="type[2]"><?PHP echo $off_type[2]; ?></select></td>
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">
              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$sunday_display + 3days")); ?>" name="submit_date[3]">
              &nbsp;</td>
              <td align="right">Wednesday-<?PHP echo date("m/d/y",strtotime("$sunday_display + 3days")); ?>
              &nbsp;</td>
              <td><input style="text-align: Center;" type="text" name="hours[3]" size="8" value="<?PHP echo $hours[3] ; ?>"></td>
              <td><select size="1" name="type[3]"><?PHP echo $off_type[3]; ?></select></td>
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">
              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$sunday_display + 4days")); ?>" name="submit_date[4]">
              &nbsp;</td>
              <td align="right">Thursday-<?PHP echo date("m/d/y",strtotime("$sunday_display + 4days")); ?>
              &nbsp;</td>
              <td><input style="text-align: Center;" type="text" name="hours[4]" size="8" value="<?PHP echo $hours[4]; ?>"></td>
              <td><select size="1" name="type[4]"><?PHP echo $off_type[4]; ?></select></td>
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">
              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$sunday_display + 5days")); ?>" name="submit_date[5]">
              &nbsp;</td>
              <td align="right">Friday-<?PHP echo date("m/d/y",strtotime("$sunday_display + 5days")); ?>
              &nbsp;</td>
              <td><input style="text-align: Center;" type="text" name="hours[5]" size="8" value="<?PHP echo $hours[5]; ?>"></td>
              <td><select size="1" name="type[5]"><?PHP echo $off_type[5]; ?></select></td>
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">
              <input type="hidden" value="<?PHP echo date("Ymd",strtotime("$sunday_display + 6days")); ?>" name="submit_date[6]">
              &nbsp;</td>
              <td align="right">Saturday-<?PHP echo date("m/d/y",strtotime("$sunday_display + 6days")); ?>
              &nbsp;</td>
              <td><input style="text-align: Center;" type="text" name="hours[6]" size="8" value="<?PHP echo $hours[6]; ?>"></td>
              <td><select size="1" name="type[6]"><?PHP echo $off_type[6]; ?></select></td>
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
              <td width="10">&nbsp;</td>
              <td align="center" colspan="3">
              	<input type="submit" value="Add Time Off" name="add_vacation"></td>
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">&nbsp;</td>
              <td align="left" colspan="3">
                <input type="checkbox" name="check_conf" value="ON">Override number of people off check.
              </td>
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
<!--END OF MAIN TABLE--->
<?PHP echo  $time_summary; ?>
</form>
<?PHP 
require(DIR_PATH."includes/footer.inc.php");
?> 	
