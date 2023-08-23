<?PHP
/******************************************************************************
**  File Name:
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

//SET PAGE VARIBALES
$TOType = $_GET["TOType"];

$tab_selection = 1;//sets the color of the tab that corresponds to this page
require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$cur_page_title = "Time Off Summary";
require(DIR_PATH."includes/header.inc.php");

//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
echo '<table border="0" cellpadding="0" cellspacing="0" width="700">
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="vacation_summary_step1.php">Select a different time off type</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td class="text14BkB"><b>Summary Information</b></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>';


	//GET THE TIME OFF TYPE
	$SQL = @mysql_query("SELECT * from vf_to_type WHERE to_id = '$TOType'");
    $Result = @mysql_fetch_array($SQL);
		$TypeID = $Result["to_id"];
        $Descr = $Result["descr"];
        $Shift = $Result["shift_id"];
        $TOYear = $Result["year"];
        $Valid_date = $Result["type_date"];
        $Dept_Time = $Result["dept_id"];


        //MAKE SURE THE USER CAN'T CHANGE THE URL AND GET INTO ANOTHER DEPARTMENTS
        //INFORMATION
        if($Dept_Time != 0){
        	if($Dept_Time != $_SESSION["ses_dept_id"]){
	        	print "You can't view this time off type.".$Dept_Time . $_SESSION["ses_dept_id"];
				exit();
			}
        }

        //IF THERE IS A VALID DATE FOR THIS VACATION TYPE LET THE USER KNOW
        //VALID DATE DESIGNATES THAT THE VACATION TYPE CANNOT BE SCHEDULED
        //UNTIL THAT DATE OR LATER.
/*        if($Valid_date != "0000-00-00"){
	        $Type_Date = "Can't be used before: "  . date("m/d/Y",strtotime($Valid_date));
    	}
*/
//ADD MORE INFORMATAION TO THE content VARIABLE.
echo '<tr>
		      <td bgcolor="#00319C">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				  <tr>
				    <td align="left" style="color:#FFFFFF;"><b>&nbsp;' . $Descr . '</b></td>
				    <td align="left" style="color:#FFFFFF;">' . $Type_Date . '</td>
				    <td align="right" valign="middle">
                    </td>
				  </tr>
				</table>
			   </td>
			  </tr>
		  <tr>
		    <td>
		      <table border="0" cellpadding="0" cellspacing="0" width="700">
		        <tr>
		          <th bgcolor="#CCCC99">Name</th>
		          <th bgcolor="#CCCC99">Earned</th>
		          <th bgcolor="#CCCC99">Scheduled</th>
		          <th bgcolor="#CCCC99">Remaining to Schedule</th>
		          <th bgcolor="#CCCC99">Used</th>
		          <th bgcolor="#CCCC99">Unused</th>
		          <th bgcolor="#CCCC99"></th>
		        </tr>';


		//SET VARIABLE THAT CHANGES CELL COLORS
        $cell_bg_color = 0;
		//LOOK UP ALL THE EMPLOYEES FOR THE CURRENT DEPARTMENT
        $GetEmp = @mysql_query("SELECT * FROM vf_employee WHERE Dept_id = '$_SESSION[ses_dept_id]' AND vf_employee.enabled != 'N'" .
        		"ORDER BY lname,fname ASC");
        //INITIALIZE HEADER REPEAT VARIABLE
		$repeat_header = 1;
        while($EmpArray = @mysql_fetch_array($GetEmp)){
	        $Emp_EmpID = $EmpArray["emp_id"];
	        $Emp_FName = $EmpArray["fname"];
	        $Emp_MName = $EmpArray["mname"];
	        $Emp_LName = $EmpArray["lname"];
	        $Eligible = $EmpArray["status"];

	        $Emp_NAME = $Emp_LName . ", " . $Emp_FName;

	        //LOOKUP THE TOTAL VACATION TIME THE EMPLOYEE HAS EARNED FOR THIS TIME OFF TYPE
	        $GetVac = @mysql_query("SELECT * FROM vf_emp_to_hours WHERE to_id = '$TypeID' " .
	        					"AND year = '$CurrentYear' AND emp_id = '$Emp_EmpID'");
			$Vac_Array = @mysql_fetch_array($GetVac);
	        	$Earned = $Vac_Array["hours"];

			//IF THE EMPLOYEE HAS TIME EARNED GET DETAILS OTHERWISE SKIP THIS TYPE
			if($Earned == ""){
               $Earned = 0;
            }

             //RESET ALL THE VARIABLES
            $TtlUsed = 0;
            $TtlScheduled = 0;
            $TtlRemaining = 0;
            $TtlToSchedule = 0;
			$TtlUnused = 0;

            //LOOKUP ALL OF THE EMPLOYEES SCHEDULED TIME OFF FOR THE CURENT TYPE
			$Get_Scheduled = @mysql_query("SELECT * FROM vf_vacation WHERE " .
               		"to_id = '$TypeID' AND emp_id = '$Emp_EmpID' AND year = '$CurrentYear'");
			while($Load_Sched = @mysql_fetch_array($Get_Scheduled)){
            	$Vac = $Load_Sched["hours"];
                $VacDate = $Load_Sched["date"];

                //FORMAT THE DATE FOR COMPARISON
                $VacDate = date("Ymd",strtotime($VacDate));

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
             if($cell_bg_color == 0){
                $cell_color = "";
                $cell_bg_color = 1;
             }elseif($cell_bg_color == 1){
                $cell_color = 'bgcolor="#E4E4E4"';
                $cell_bg_color = 0;
			 }

			//ADD MORE INFORMATAION TO THE content VARIABLE.
			echo '<tr>
		      <td align="left" '.$cell_color.'>' . $Emp_NAME . '</td>
		      <td align="center" '.$cell_color.'>' . $Earned . '</td>
		      <td align="center" '.$cell_color.'>' . $TtlScheduled . '</td>
	       	  <td align="center" '.$cell_color.'>' . $TtlToSchedule . '</td>
		      <td align="center" '.$cell_color.'>' . $TtlUsed . '</td>
		      <td align="center" '.$cell_color.'>' . $TtlUnused . '</td>
		      <td align="center" '.$cell_color.'><a href="vacation_detail.php?Emp=' . $Emp_EmpID . '&TOType=' . $TOType . '">Details</a></td>
		     </tr>';
        //END WHILE LOOK UP EMPLOYEES

        	 if($repeat_header == 20){
				echo '
			        <tr>
			          <th bgcolor="#CCCC99">Name</th>
			          <th bgcolor="#CCCC99">Earned</th>
			          <th bgcolor="#CCCC99">Scheduled</th>
			          <th bgcolor="#CCCC99">Remaining to Schedule</th>
			          <th bgcolor="#CCCC99">Used</th>
			          <th bgcolor="#CCCC99">Unused</th>
			          <th bgcolor="#CCCC99"></th>
			        </tr>';
				$repeat_header = 0;
             }
        //INCREMENT THE HEADER REPEAT VARIABLE
		$repeat_header = $repeat_header + 1;
	   	}

        //ADD MORE INFORMATAION TO THE content VARIABLE.
		echo '</table>
		    		  </td>
		  	   		   </tr>
				  	   <tr>
				    	<td>&nbsp;</td>
				  	   </tr>
                    </table>';

require(DIR_PATH."includes/footer.inc.php");
?>