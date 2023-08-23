<?PHP
/******************************************************************************
**  File Name: time_summary.inc.php
**  Description: Include file for files add_weekly_vac.php and add_daily.php
**  Written By: Gary Barber
**  Original Date: 9/7/04
**  
*******************************************************************************
**********************  LAST MODIFIED  ****************************************
**
**  Date:
**  Programmer:
**  Notes:
**
******************************************************************************/

function time_checks(){
	global $time_off_used,$time_array_cnt,$time_summary,$TypeID,$Descr,$Valid_date;
	global $TtlScheduled,$TtlUsed,$TtlRemaining,$TtlUnused,$TtlToSchedule,$Earned;
    global $employee,$CurrentYear,$employee_sub_dept,$GoToDay,$DeptID;

    //DETERMINE TODAYS DATE AND CONVERT TO SECONDS
    $current_date = strtotime(date("Ymd"));

	//DETERMINE THE YEAR OF THE SELECTED DATE
	//CALL FUNCTION TO RETURN THE VALUE
    $selected_year = strtotime($GoToDay);
	$year_select = selected_date($selected_year);     
    
    //CREATE ARRAY TO HOLD TIME OFF INFORMATION
    $time_off_used = array();
    $time_array_cnt = 0;

	$time_summary = '
	    <p><b><u>SUMMARY INFORMATION</u></b></p>
	    <table border="3" cellpadding="0" cellspacing="0" style="background-color:#C0C0C0; border-color:#00319C;" width="720">
	      <tr>
	        <td>
				<table border="0" cellpadding="0" cellspacing="0" width="720" bgcolor="#C0C0C0">
                  <tr>
                    <th bgcolor="#00319C" width="161">&nbsp;</th>
				    <th bgcolor="#00319C" colspan="3" width="247" style="border-bottom-style: solid; border-bottom-width: 1px"><span style="color:#FFFFFF;">Already
                      Scheduled time</span></th>
				    <th bgcolor="#00319C"><b><span style="color:#FFFFFF;">|</span></b></th>
				    <th bgcolor="#00319C" colspan="2" width="276" style="border-bottom-style: solid; border-bottom-width: 1px"><span style="color:#FFFFFF;">Total time</span></th>
                  </tr>
				  <tr>
				    <th bgcolor="#00319C"><span style="color:#FFFFFF;">Description</span></th>
				    <th bgcolor="#00319C"><span style="color:#FFFFFF;">Scheduled</span></th>
				    <th bgcolor="#00319C"><span style="color:#FFFFFF;">Used</span></th>
			    	<th bgcolor="#00319C"><span style="color:#FFFFFF;">Remaining</span></th>
                    <th bgcolor="#00319C"><span style="color:#FFFFFF;"> | </span></th>
				    <th bgcolor="#00319C"><span style="color:#FFFFFF;">Total Unused</span></th>
				    <th bgcolor="#00319C"><span style="color:#FFFFFF;">Left to Schedule</span></th>
				  </tr>';


	//GET ALL OF THE TIME OFF TYPES
	$SQL = @mysql_query("SELECT * from vf_to_type WHERE emp_viewable = 'Y' AND
    					(dept_id = '0' OR dept_id = '$DeptID')");
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
        					"AND year = '$year_select' AND emp_id = '$employee'")
                            or die("Can't retrieve employee summary");
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
               		"to_id = '$TypeID' AND emp_id = '$employee' AND year = '$year_select'");
			while($Load_Sched = @mysql_fetch_array($Get_Scheduled)){
            	$Vac = $Load_Sched["hours"];
                $VacDate = $Load_Sched["date"];

                //CONVERT THE FORMAT OF THE VACATION DATE
                $VacDate = strtotime($VacDate);

                //TOTAL HOURS THAT ARE SCHEDULED
				$TtlScheduled = $TtlScheduled + $Vac;

                //IF DATE HAS PASSED. CONSIDER VACATION AS USED
                if($VacDate <= $current_date){
                	$TtlUsed = $TtlUsed + $Vac;
                }

                //IF DATE HAS NOT PASSED. CONSIDER VACATION AS REMAINING
                if($VacDate > $current_date){
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

            //LOAD THE ARRAY WITH THE VALUES
            $time_off_used[$time_array_cnt][0] = $TypeID;
            $time_off_used[$time_array_cnt][1] = $TtlToSchedule;
            $time_off_used[$time_array_cnt][2] = $Descr;
            $time_array_cnt = $time_array_cnt + 1;
    	}
	}

	$time_summary .= "
				  </table>	
				 </td>
				</tr>
			  </table>";
}//END OF FUNCTION
?>