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

//SET PAGE VARIABLES
$Emp = $_GET["Emp"];
$TOType = $_GET["TOType"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$cur_page_title = "Vacation Detail";
require(DIR_PATH."includes/header.inc.php");

//LOADS THE INFORMATION FOR EACH VACATION DATE
function vacation(){
	global $Vac_Date,$Vac,$VacApprv,$vac_deny;

    if($VacApprv == "" || $VacApprv == "0"){
	    $appoved_by = '<span style="color:#FF0000;">Pending</span>';
    }else{
        //GET NAME OF SUPERVISOR WHO APPROVED TIME OFF
		$show_sup = @mysql_query("SELECT fname,lname FROM vf_employee WHERE emp_id = '$VacApprv'");
		$sup_array = @mysql_fetch_array($show_sup);
        	$appv_first = $sup_array["fname"];
        	$appv_last = $sup_array["lname"];

    	//CONCATENAT SUPERVISOR NAME
		$appoved_by = $appv_first . " " . $appv_last;
	}
		
	if($vac_deny == "Y"){
		$appoved_by = '<span style="color:red;">Time off denied</span>';
	}

    echo '
        <tr>
	     <td align="center">' . $Vac_Date . '</td>
	     <td align="center">' . $Vac . '</td>
	     <td align="center">' . $appoved_by . '</td>
	    </tr>';

	//RESET THE VARIABLE
    $VacApprv = "";
}

//IF THE USER IS AN ADMINISTRATOR. DISPALY AN ADMINISTRATORS MENU TAB.
if($_SESSION["ses_is_admin"] == 1){
	$ad_menu = "<td><a href=\"admin.php\"><img border=\"0\" src=\"../images/admin_inact.gif\" width=\"84\" height=\"21\"></a></td>
     <td><img border=\"0\" src=\"../images/TabCtrltBlue_ltBlue.gif\" width=\"31\" height=\"21\"></td>";
}


/**********************************************************************
** GET THE EMPLOYEE INFORMATION                                      **
**********************************************************************/
$SQL = mysql_query("SELECT * FROM `vf_employee` WHERE emp_id = '$Emp' " .
		"AND dept_id = '$_SESSION[ses_dept_id]'");

$Result = mysql_fetch_array($SQL);
	$Emp_EmpID = $Result["emp_id"];
    $Emp_FName = $Result["fname"];
    $Emp_MName = $Result["mname"];
    $Emp_LName = $Result["lname"];
    $Eligible = $Result["status"];

$Emp_NAME = $Emp_LName . ", " . $Emp_FName;

//IF THE EMPLOYEE CAN'T BE FOUND. STOP THE USER
if($Emp_EmpID == ""){
	echo "<b>Can not find employee information. If you feel " .
       "this is an error, please contact your supervisor.</b>";
    exit();
}

//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
echo '
	 <table border="0" cellpadding="0" cellspacing="0" width="700">
	  <tr>
	    <td></td>
	  </tr>
	  <tr>
	    <td style="color:#FF0000"><b>Employee: ' . $Emp_NAME . '</b></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><a href="vacation_summary_step2.php?TOType=' . $TOType.'">Return to the previous view</a></td>
	  </tr>
	  <tr>
	    <td><a href="vacation_summary_step1.php">Select a different time off type</a></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td style="text-decoration: underline;"><b>Below is your current detail information:</b></td>
	  </tr>
	  <tr>
	    <td>&nbsp; </td>
	  </tr>
	  <tr>
	   <td>
	    <!-- MAIN TABLE TO HOLD USED AND UNUSED TABLES -->
	    <table border="0" cellpadding="0" cellspacing="0" width="600">
		 <tr>
	      <td valign="top">';


	//LOOK UP THE TIME OFF TYPE AND DESCRIPTION
	$SQL = @mysql_query("SELECT * from vf_to_type WHERE to_id = '$TOType'");
    $Result = @mysql_fetch_array($SQL);
		$TypeID = $Result["to_id"];
        $Descr = $Result["descr"];
        $Shift = $Result["shift_id"];
        $TOYear = $Result["year"];
        $Valid_date = $Result["type_date"];
        $Dept_Time = $Result["dept_id"];

    //IF THERE IS A VALID DATE FOR THIS VACATION TYPE LET THE USER KNOW
    //VALID DATE DESIGNATES THAT THE VACATION TYPE CANNOT BE SCHEDULED
    //UNTIL THAT DATE OR LATER.
    if($Valid_date != ""){
	     $YR = substr($Valid_date, 0,4);
	     $MO = substr($Valid_date, 4,2);
	     $DY = substr($Valid_date, 6,2);
	     $Type_Date = "Can't be used before: " . $MO . "/" . $DY . "/" . $YR;
    }

    //LOOKUP THE TOTAL VACATION TIME THE EMPLOYEE HAS EARNED FOR THIS TIME OFF TYPE
    $GetVac = @mysql_query("SELECT * FROM vf_emp_to_hours WHERE to_id = '$TypeID' " .
     					"AND emp_id = '$Emp_EmpID' AND year = '$CurrentYear'");
	$Vac_Array = @mysql_fetch_array($GetVac);
      	$Earned = $Vac_Array["hours"];

	//IF THE EMPLOYEE HAS TIME EARNED GET DETAILS OTHERWISE SKIP THIS TYPE
	if($Earned == ""){
         $Earned = 0;
    }

	$TodaysDate = date("Ymd",$TodaysDate);

    //LOOKUP ALL OF THE EMPLOYEES SCHEDULED TIME OFF FOR THE CURENT TYPE
	$Get_Scheduled = @mysql_query("SELECT * FROM vf_vacation WHERE " .
           		"to_id = '$TypeID' AND emp_id = '$Emp_EmpID' AND year = '$CurrentYear' " .
                 "AND date < $TodaysDate ORDER BY date ASC");

    //ADD MORE INFORMATAION TO THE content VARIABLE.
	echo '<!-- TABLE FOR USED VACATION -->
            <table border="0" cellpadding="0" cellspacing="0" width="250">
			 <tr>
			  <td bgcolor="#003366" style="color:#FFFFFF">&nbsp;<b>Used ' . $Descr . ' time:</b></td>
			 </tr>
			 <tr>
			  <td>
		       <!-- TABLE FOR USED VACATION BREAKDOWN -->
			   <table border="1" cellpadding="0" cellspacing="0" width="250">
			    <tr>
			     <th bgcolor="#CCCC99">DATE</th>
		         <th bgcolor="#CCCC99">HOURS</th>
		         <th bgcolor="#CCCC99">APPROVED</th>
		      	</tr>';

			//LOAD THE RESULTS OF THE QUERY ABOVE
			while($Load_Sched = @mysql_fetch_array($Get_Scheduled)){
	           	$Vac = $Load_Sched["hours"];
	            $VacDate = $Load_Sched["date"];
                $VacApprv = $Load_Sched["apprv_by"];
                $vac_deny = $Load_Sched["deny"];

                $Vac_Date = date("m/d/Y",strtotime($VacDate));

	              //CALL THE VACATION FUNCTION
	              vacation();
	        }

        //ADD MORE INFORMATAION TO THE content VARIABLE.
        echo '
			        </table>
			       </td>
			      </tr>
			     </table>
            	 <!-- ADD ANOTHER COLUMN TO THE MAIN TABLE -->
				</td>
				<td valign="top">';

            //GET FUTURE VACTIONS
			$Get_Scheduled = @mysql_query("SELECT * FROM vf_vacation WHERE " .
               		"to_id = '$TypeID' AND emp_id = '$Emp_EmpID' AND year = '$CurrentYear' " .
                    "AND date >= $TodaysDate ORDER BY date ASC");

        //ADD MORE INFORMATAION TO THE content VARIABLE.
        echo '<!-- TABLE FOR SCHEDULED FUTURE VACATION -->
            <table border="0" cellpadding="0" cellspacing="0" width="250">
			 <tr>
			  <td bgcolor="#003366" style="color:#FFFFFF">&nbsp;<b>Scheduled ' . $Descr . ' time:</b></td>
			 </tr>
			 <tr>
			  <td>
		       <!-- TABLE FOR USED VACATION BREAKDOWN -->
			   <table border="1" cellpadding="0" cellspacing="0" width="250">
			    <tr>
			     <th bgcolor="#CCCC99">DATE</th>
		         <th bgcolor="#CCCC99">HOURS</th>
		         <th bgcolor="#CCCC99">APPROVED</th>
		      	</tr>';

				//LOAD THE RESULTS OF THE QUERY ABOVE
				while($Load_Sched = @mysql_fetch_array($Get_Scheduled)){
	            	$Vac = $Load_Sched["hours"];
	                $VacDate = $Load_Sched["date"];
                    $VacApprv = $Load_Sched["apprv_by"];
					$vac_deny = $Load_Sched["deny"];
					
					$Vac_Date = date("m/d/Y",strtotime($VacDate));

	                //CALL THE VACATION FUNCTION
	                vacation();
	            }

	//ADD MORE INFORMATAION TO THE content VARIABLE.
	echo ' <tr>
			       <td></td>
			       <td></td>
			      </tr>
			     </table>
			    </td>
			   </tr>
			  </table>
	 		 </td>
		    </tr>
		   </table>
		  </td>
		 </tr>
		 <tr>
		  <td><b>Earned Hours: ' . $Earned . '</b></td>
		 </tr>
		</table><p>&nbsp;</p>';

require(DIR_PATH."includes/footer.inc.php");
?>