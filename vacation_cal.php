<?php
/******************************************************************************
**  File Name: vacation_cal.php
**  Description: Displays a calendar for the user to add vacation.
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

//IF THE EMPLOYEE HASN'T LOGGED IN. STOP THEM
if(!$_SESSION["ses_first_name"]){
	header ("Location: index.php");
}

//CONCATENATE FIRST AND LAST NAME
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];
$emp_full_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];

//SET DEPARTMENT AND SUB DEPT
$DeptID = $_SESSION["ses_dept_id"];
$employee_sub_dept = $_SESSION["ses_sub_dept_id"];
$emp_number = $_SESSION["ses_emp_id"];


require_once(DIR_PATH."includes/db_info.inc.php");
require_once(DIR_PATH."includes/vacation_remove.inc.php");
//SET PAGE VARIABLES
$GoToDay = $_POST["GoToDay"];
require_once(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year        

/*******************************************************************************
** FUNCTIONS SECTION                                                          **
*******************************************************************************/
function track_type(){
     global $WriteType,$OffType,$TotalHrs,$Restriction,$EmployeesOff,$DeptID,$display_restiction;
     global $Type_Date,$Available,$employee_sub_dept,$include_default_dept,$dflt_Available;

	//MAKE SURE THE VARIABLES ARE EMPTY
	$WriteType = "";
	$TotalHrs = 0;
    $display_restiction = "";

    /*#############################################################################
      IF USER IS GROUPED WITH A SUB DEPT AND CONFIG FILE IS SETUP TO CHECK THE
      WHOLE DEPT. RUN THIS WITH THE SQL ABOVE.
      #############################################################################*/
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
            "day = '$Type_Date' AND dept_id = '$DeptID' " .
            "AND sub_dept_id = '0'");
            $dflt_date_restrict = @mysql_fetch_array($dflt_date_allow);
                $dflt_restriction = $dflt_date_restrict["total_off"];
	}
    /*#############################################################################*/

	if($OffType == "P"){
        /*#############################################################################
          IF USER IS GROUPED WITH A SUB DEPT AND CONFIG FILE IS SETUP TO CHECK THE
          WHOLE DEPT. RUN THIS WITH THE SQL ABOVE.
          #############################################################################*/
	    if($include_default_dept == '1'){
            //COUNT THE NUMBER OF PEOPLE OFF
 	        $dflt_sql=("SELECT COUNT(*) AS Total FROM vf_vacation, vf_employee
	        	WHERE vf_vacation.date = '$Type_Date'
	            AND vf_vacation.dept_id = '$DeptID'
	            AND vf_vacation.emp_id = vf_employee.emp_id");

	            $dflt_sql_result = @mysql_query($dflt_sql);
	            $dflt_sql_result_array = @mysql_fetch_array($dflt_sql_result);
	            $dflt_total = $dflt_sql_result_array['Total'];
		}
        /*#############################################################################*/

		//RUN THE QUERY TO GET A TOTAL COUNT OF PEOPLE OFF THE CURRENT DAY
		$SQL=("SELECT COUNT(*) AS Total FROM vf_vacation, vf_employee
        	WHERE vf_vacation.date = '$Type_Date'
            AND vf_vacation.dept_id = '$DeptID'
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
	        	WHERE vf_vacation.date = '$Type_Date'
	            AND vf_vacation.dept_id = '$DeptID'
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
        	WHERE vf_vacation.date = '$Type_Date'
            AND vf_vacation.dept_id = '$DeptID'
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
	            $display_restiction = "Y";
	        }
	    }else{
	        $display_restiction = "Y";
	    }
	}
    /*#############################################################################*/

    //IF THERE ARE RESTRICTIONS FOR THIS DATE LET THE USER KNOW
    if($Restriction != "X"){
        //IF THERE ARE RESTRICTIONS PRESENT THEM. OTHERWISE USE
        //THE DEFAULT VALUE
        if($Restriction != ""){
            $TtlOff = $Restriction;
        }else{
            $TtlOff = $EmployeesOff;
        }

        //CALCULATE THE AMOUNT OF PEOPLE WHO CAN STILL TAKE OFF
        $Available = $TtlOff - $Total;

        if($Available <= 0){
            $display_restiction = "Y";
        }
    }else{
        $display_restiction = "Y";
    }

    //IF THE DATE IS BLOCKED REPLY THAT IT IS
    if($display_restiction == "Y"){
        $WriteType.=
       '<br />
           <table border="0" cellpadding="0" cellspacing="0" width="100%">
             <tr>
               <td align="center">
                 <img border="0" src="images/NA.gif">
               </td>
             </tr>
           </table>';
    }
}//END OF FUNCTION


function WriteMonth($StartDate,$Border_color,$Title_color){
	global $DeptID,$EmployeesOff,$PreNotice,$OffType,$LastDate,$Available,$dflt_Available,$display_restiction;
	global $include_default_dept,$WriteType,$TotalHrs,$Restriction,$Type_Date,$content,$employee_sub_dept;
	global $emp_number;
	
	$WriteMonth="";
	$CurrentDate=date("m/1/y", strtotime ("$StartDate"));
	$setMonth=date("m",strtotime ($CurrentDate));
	$BeginWeek=date("m",strtotime ($CurrentDate));
	$EndWeek=date("m",strtotime ($CurrentDate));
	$PrevYear=date("m/1/y", strtotime ("$StartDate -1 year"));
	$PrevYearWrt=date("Y", strtotime ("$StartDate -1 year"));
	$NextYear=date("m/1/y", strtotime ("$StartDate +1 year"));
	$NextYearWrt=date("Y", strtotime ("$StartDate +1 year"));

	//GET THE VALUE IF THE CURRENT CALENDAR DATE
    $TodaysDate=date("m/d/y");
    //ADD THE PRENOTICE VALUE OF DAYS TO TODAYS DATE. NO VACATIONS WILL BE ALLOWED
    //UNTIL TODAYS DATE PLUS PRENOTICE DAYS
    $TodaysDate = date("Ymd", strtotime ("$TodaysDate +" . $PreNotice . " days"));

   /***************************************************************************
    *** CREATE THE HEADERS FOR THE MONTH AND DAYS OF THE WEEK                ***
    ***************************************************************************/
	$WriteMonth='
			<table border="0" cellspacing="0" cellpadding="0" bgcolor="'.$Border_color.'" width="700">
			<tr><td>
			<table border="0" cellspacing="1" cellpadding="2" width="100%" style="border: 1pt solid '.$Border_color.'">
			<tr>
			 <td colspan="7" valign="top" BGCOLOR="#FFFFFF" align="center">
			  <table width="100%">
               <tr>
                <td valign="top" BGCOLOR="#FFFFFF" align="left">
                 <form method="POST" action="'.$_SERVER["PHP_SELF"].'">
                 <input type="hidden" value="'.$PrevYear.'" name="GoToDay">
				 <input type="submit" value="'.$PrevYearWrt.'" name="B1">
				 </form>
                </td>
                <td valign="top" BGCOLOR="#FFFFFF" align="center">&nbsp;&nbsp;&nbsp;</td>
                <td valign=top BGCOLOR="#FFFFFF" align="right">
                	<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
                    <input type="hidden" value="'.date("m/1/y", strtotime ("$CurrentDate -1 months")).'" name="GoToDay">
	                <input type="submit" value="<" name="B1">
	                </form>
				</td>
                <td class="text24BkB" valign="top" BGCOLOR="#FFFFFF" align="center">
					<b><span style="color:#446482;">'
					.date("M",strtotime ($StartDate)).' '.date("Y",strtotime ($StartDate)).
					'</span></b>
				</td>
                <td valign="top" BGCOLOR="#FFFFFF" align="left">
					<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
                    <input type="hidden" value="'.date("m/1/y", strtotime ("$CurrentDate +1 months")).'" name="GoToDay">
	                <input type="submit" value=">" name="B1">
	                </form>
				</td>
				<td valign="top" BGCOLOR="#FFFFFF" align="center">&nbsp;&nbsp;&nbsp;</td>
                <td valign="top" BGCOLOR="#FFFFFF" align="right">
                 <form method="POST" action="'.$_SERVER["PHP_SELF"].'">
                 <input type="hidden" value="'.$NextYear.'" name="GoToDay">
				 <input type="submit" value="'.$NextYearWrt.'" name="B1">
				 </form>
                </td>
               </tr>
              </table>
             </td>
            </tr>
			<tr>
			 <th>S</th>
			 <th>M</th>
			 <th>T</th>
			 <th>W</th>
			 <th>T</th>
			 <th>F</th>
			 <th>S</th>
			</tr>
	';

	/***************************************************************************
    *** CREATE THE HEADERS FOR THE ACTUAL CALENDAR                           ***
    ***************************************************************************/
	for($j=0;$j<6;$j++){
		if($BeginWeek==$setMonth||$EndWeek==$setMonth){
			switch(date("w",strtotime($CurrentDate))){
			case 0:
				$DaysToAd=array("","+1 days","+2 days","+3 days","+4 days","+5 days","+6 days");
				break;
			case 1:
				$DaysToAd=array("-1 days","","+1 days","+2 days","+3 days","+4 days","+5 days");
				break;
			case 2:
				$DaysToAd=array("-2 days","-1 days","","+1 days","+2 days","+3 days","+4 days");
				break;
			case 3:
				$DaysToAd=array("-3 days","-2 days","-1 days","","+1 days","+2 days","+3 days");
				break;
			case 4:
				$DaysToAd=array("-4 days","-3 days","-2 days","-1 days","","+1 days","+2 days");
				break;
			case 5:
				$DaysToAd=array("-5 days","-4 days","-3 days","-2 days","-1 days","","+1 days");
				break;
			case 6:
				$DaysToAd=array("-6 days","-5 days","-4 days","-3 days","-2 days","-1 days","");
				break;
			}
			$WriteMonth.='<tr valign="top">';

            /**************************************************
            ** WRITE IN THE WEEKLY DATE BOXES FOR THE MONTH ***
            **************************************************/
			for($i=0;$i<7;$i++){
				$strTemp="";
				$BGcolor="white";
				$FontColor="#000000";
				$Style="";

                //FORMAT THE DATE YYYYMMDD TO CHECK IF THE DATE IS > THAN TODAY'S DATE
				//AND LESS THAN THE LAST DATE ALLOWED
                $AllowDate = date("Ymd",strtotime ("$CurrentDate $DaysToAd[$i]"));

                //GET THE DATE FORMATTED TO RETREIVE RECORDS FROM THE VACATION FILE
 				$Type_Date = date("Ymd",strtotime ("$CurrentDate $DaysToAd[$i]"));

                //CHECK IF THERE ARE SPECIAL DATES THAT THE DEPARTMENT HAS RESTRICTED
                //IF NO INFORMATION FOR THE CURRENT DATE IS IN THE TABLE. THE ALLOWED
                //TIME OFF DEFAULTS TO THE DEFAULT config TABLE people_off_per_day VALUE
                $DATE_ALLOW = @mysql_query("SELECT * FROM vf_off_perday WHERE " .
                  			"day = '$Type_Date' AND dept_id = '$DeptID' " .
                            "AND sub_dept_id = '$employee_sub_dept'");
                $DATE_RESTRICT = @mysql_fetch_array($DATE_ALLOW);
                   	$Restriction = $DATE_RESTRICT["total_off"];

                /********************************
                ** CALL FUNCTION TO LOAD HOURS **
                ********************************/
                if($AllowDate <= $LastDate && $AllowDate >= $TodaysDate){
	                track_type();
					//AFTER THE FUNCTION IS RAN. IF THERE IS THE ALLOTED AMOUNT
                    //OF PEOPLE ALREADY OFF. SET VARIABLE SO A BUTTON WON'T BE ADDED
					if($Available <= 0){
                        $display_restiction = "Y";

                        //$Restriction = "X";
                    }
				}else{
                	$WriteType = "";
                }

 				//SET BLANK AND ADD DATA IF THE DAY IS A SUNDAY AND WITHIN THE
                //ALLOWABLE BEGIN AND END DATES
                $weekly_schedule = "";
				if($AllowDate >= $TodaysDate && $AllowDate < $LastDate){
                    if($i == 0){
   	                //ADD A WEEKLY BUTTON FOR EACH SUNDAY OF THE MONTH
					$weekly_schedule ='
						<div style="position:absolute; top:0; left:30;">
					 	 <form method="POST" action="add_weekly_vac.php">
		                 <input type="hidden" value="'.date("m/d/y",strtotime ("$CurrentDate $DaysToAd[$i]")).'" name="GoToDay">
		                 <input type="submit" value="Week" name="B1">
	                     </form>
						</div>';
                    }
				}

				//CHANGE BACKGROUND COLOR OF TODAYS DATE
				if(date("Ymd",strtotime ("$CurrentDate $DaysToAd[$i]")) == $TodaysDate){
					$BGcolor = "#FFFFCC";	
				}
				
                /***************************************************************
                ** IF DATE FALLS IN THE RANGE AFTER TODAYS DATE AND BEFORE THE**
                ** END DATE. CREATE BUTTONS OTHERWISE CREATE NUMBERS FOR THE  **
                ** DAYS OF THE MONTH                                          **
                ***************************************************************/
                if($AllowDate >= $TodaysDate && $AllowDate < $LastDate && $display_restiction != "Y"){ //$Restriction != "X"){

					//IF NOT THE CURRENT MONTH. MAKE THE BUTTON SMALL AND THE
	                //BUTTON TEXT ITALIC AND THE CELL BACKGROUND DARK GRAY
					if(date("m",strtotime ("$CurrentDate $DaysToAd[$i]"))!=$setMonth){
	                    $FontColor = 'style="font-style: italic; font-size: 10px"';
						$BGcolor="#C0C0C0";
					}

 	                //ADD A BUTTON FOR EACH DAY OF THE MONTH
					$WriteMonth.='
					 	<td width="95" height="100" align="left" bgcolor="'.$BGcolor.'" '.$Style.' >'.$ck.' 
						 <div style="position:relative; height:100; width:95;">
						  <div style="position:absolute; top:0; left:0;">
		                   <form method="POST" action="add_daily_vac.php">
			                <input type="hidden" value="'.date("m/d/y",strtotime ("$CurrentDate $DaysToAd[$i]")).'" name="GoToDay">
			                <input type="submit" value="'.date("d",strtotime ("$CurrentDate $DaysToAd[$i]")).'" name="B1">
		                   </form>
						  </div>';
                }else{

					//IF NOT THE CURRENT MONTH. MAKE THE BUTTON SMALL AND THE
                    //BUTTON TEXT ITALIC AND THE CELL BACKGROUND DARK GRAY
					if(date("m",strtotime ("$CurrentDate $DaysToAd[$i]"))!=$setMonth){
	                    $FontColor="#FF00FF";
						$BGcolor="#C0C0C0";
					}

 					$WriteMonth.='
					 	<td width="95" height="100" valign="top" align="left" bgcolor="'.$BGcolor.'" '.$Style.'>
 						<div style="position:relative; height:100; width:95;">
 							<u><b><span style="color:'.$FontColor.'">'
				          .date("d",strtotime ("$CurrentDate $DaysToAd[$i]")).
	                     '</span></b></u><br />';
                }
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                //DISPLAY ANY TIME OFF FOR THE CURRENT EMPLOYEE
            	$to_sql = @mysql_query("SELECT `vacation_id`,`hours`,`to_id`,`apprv_by`,`deny`,`deny_reason` FROM vf_vacation WHERE " .
              			"date = '$Type_Date' AND emp_id = '$emp_number'");		
				$to_result = @mysql_fetch_array($to_sql);
					$vacation_id = $to_result["vacation_id"];
                	$hours_off = $to_result["hours"];
                	$off_type = $to_result["to_id"];
                	$apprv_by = $to_result["apprv_by"];
                	$vac_deny = $to_result["deny"];
                	$deny_reason = $to_result["deny_reason"];
          
            	if($hours_off != "" && $off_type != ""){
            		//RETRIEVE OFF TYPE DESCRIPTION
            		$type_sql = @mysql_query("SELECT `descr` FROM `vf_to_type` " .
            					"WHERE `to_id` = '$off_type'");
            		$type_result = @mysql_fetch_array($type_sql);
            			$to_descr = $type_result["descr"];
            		if($vac_deny == "Y"){
            			$to_display = '<div style="position:absolute; top:30; left:0;" title="'.$deny_reason.'"><b>Denied: </b>
            				<span style="color:#FF0000; text-decoration: line-through;">'.$hours_off.'-'.$to_descr.'</span>
            				</div>';                  			            			
            		}else{       
	            		if($apprv_by == "0" || $apprv_by == ""){
	            			$to_display = '<div style="position:absolute; top:30; left:0;">';
	            				            			
							if(date("Ymd",strtotime ("$CurrentDate $DaysToAd[$i]")) > $TodaysDate)
							{
								$to_display .= '<a href="javascript:remove_vac(\'rem\',\''.$vacation_id.'\',\''.$CurrentDate.'\')" title="Remove" onclick="javascript:return confirm(\'Are you sure you want to remove '.$hours_off.'-'.$to_descr.' ?\')"><img border="0" src="images/remove.gif" width="7" height="7"></a>';
							}
								            			
	            			$to_display .= '   			
	            				<span style="color:#FF0000">'.$hours_off.'-'.$to_descr.'</span>
	            				</div>';            		
	            		}else{
	            			$to_display = '<div style="position:absolute; top:30; left:0;">';
							if(date("Ymd",strtotime ("$CurrentDate $DaysToAd[$i]")) > $TodaysDate)
							{	            			
								$to_display .= '<a href="javascript:remove_vac(\'req\',\''.$vacation_id.'\',\''.$CurrentDate.'\')" title="Submit request to remove" onclick="javascript:return confirm(\'Are you sure you want to request removal of '.$hours_off.'-'.$to_descr.' ?\')"><img border="0" src="images/remove.gif" width="7" height="7"></a>';
							}
	            			$to_display .= '   										
	            				<span style="color:#0000FF">'.$hours_off.'-'.$to_descr.'</span>
	            				</div>';            		
	            		}
            		}            			
            	}else{
            		$to_display = "";
            	}

            	//EMPTY VARS
            	$holiday = "";
            	$holiday_display = "";
            	$loop1 = 0;
            	
                //CHECK FOR HOLIDAYS OR NOTABLE DAYS
            	$hol_sql = @mysql_query("SELECT * FROM vf_notable_dates WHERE date = '$Type_Date'");		
              	while($hol_result = @mysql_fetch_array($hol_sql)){		
					$holiday = $hol_result["descr"];                  			
					if($holiday != ""){
						if($loop1 == 0){
							$holiday_display .= '<div style="position:absolute; bottom:0; left:0;">';
							$loop1 = 1;
						}
						$holiday_display .= '<br /><span style="color:#0000FF">'.$holiday.'</span>';
					}						
	          	}
	          	if($loop1 == 1){
	          		$holiday_display .= "</div>";
	          	}
                  			
	            //RESET THE VARIABLES
				$TotalHrs = 0;
				$Total = 0;
				//ADD THE INFORMATION FOR THE WEEK TO THE CURRENT MONTHLY DISPLAY
	            $WriteMonth.= $WriteType . $weekly_schedule .$to_display . $holiday_display ."
	            </div> 	
	            </td>";
			}

			$WriteMonth.="</tr>";
			$CurrentDate=date("m/d/y",strtotime("$CurrentDate +1 week"));
			$StartDateofWeek=date("w",strtotime ($CurrentDate));
			$EndofWeek=6 - $StartDateofWeek;
			$BeginWeek=date("m",strtotime ("$CurrentDate -$StartDateofWeek days"));
			$EndWeek=date("m",strtotime ("$CurrentDate +$EndofWeek days"));
		}
	}
	$WriteMonth.="</table></td></tr></table>
			<!-- End Calendar table -->";
//	return $WriteMonth;
echo $WriteMonth;
}
/*******************************************************************************
** END OF FUNCTIONS SECTION                                                   **
*******************************************************************************/

$FONT ="Verdana, Arial, Helvetica, sans-serif";
$FONTSIZE="8";
$FONTCOLOR="#000000";
$BorderColor="#3D6699";
$BarColor="#000066";

//echo "SELECT * FROM vf_config WHERE dept_id = '$DeptID' AND " .
//				" sub_dept_id = '$employee_sub_dept'";
/**********************************************
*** SELECT ALL THE CONFIGURATION ATTRIBUTES ***
**********************************************/
$SQL = @mysql_query("SELECT * FROM vf_config WHERE dept_id = '$DeptID' AND " .
				" sub_dept_id = '$employee_sub_dept'");
$RESULT_ARRAY = @mysql_fetch_array($SQL);
	$EmployeesOff = $RESULT_ARRAY["emp_off_ttl"];
    $PreNotice = $RESULT_ARRAY["days_notice"];
    $OffType = $RESULT_ARRAY["people_off_type"];
    $LastDate = $RESULT_ARRAY["last_vac_date"];
    $include_default_dept = $RESULT_ARRAY["include_default"];

    $LastDate = date("Ymd",strtotime($LastDate));

    

$hdr_detail = 'Vacation Calendar For<br />'.$user_name;
$cur_page_title = "Vacation Calendar";
require(DIR_PATH."includes/header.inc.php");   

//IF THE CONFIGURATION FILE IS NOT SET UP STOP USER
if($OffType == ""){
	echo "ERROR! Your supervisor still needs to setup the configuration file for your
    	department. Please report the error to your supervisor.
	 	 </body>
		</html>";
	exit();
}

//SET VARIABLES
if(!empty($GoToDay)){
	$StartDate=date("m/d/y",strtotime ("$GoToDay"));
}else{
	if(empty($StartDate)){
		$StartDate=date("m/d/y");
	}
}
?>

 <table width="690" style="margin-left:10;">
  <tr>
   <td width="100%"><span style="font-size:14px;color:red;font-weight:bold;"><?php echo $feedback; ?></span></td>
  </tr> 
  <tr>
   <td width="100%">
    <b>To request a vacation:</b><br />
     1. For a complete week click on the Weekly button on any Sunday.<br />
	  &nbsp;&nbsp;&nbsp;&nbsp;Follow the directions given on that page.<br />
     2. For a single day click on the button for that day.<br />
	  &nbsp;&nbsp;&nbsp;&nbsp;Follow the directions given on that page.<br />
     <b>Note:</b> Some days may be unavailable. Each manager has configured
     the calendar to their specific needs. If there are no buttons to select
     a day, it is probably because the manager has configured it to do so.
   </td>
  </tr>
  <tr>
   <td width="100%">&nbsp;</td>
  </tr>
  <tr>
   <td width="100%">
	<div id="calendar">
	<?PHP   
	WriteMonth($StartDate,$BorderColor,$BarColor,1);
	?>

	</div>
   </td>
  </tr>
</table>
<p>&nbsp;</p>
<!-- Form to redirect the input -->
<form method="post" name="remove_vacation" action="<?PHP echo $PHP_SELF; ?>">
<input type=hidden name="req_type" value="">
<input type=hidden name="vac_id" value="">
<input type=hidden name="GoToDay" value="">
</form>
<script language="JavaScript" type="text/javascript">
  function remove_vac(type,vid,GoToDay){ 
  	document.remove_vacation.req_type.value = type;
  	document.remove_vacation.vac_id.value = vid;  	
  	document.remove_vacation.GoToDay.value = GoToDay;
    document.remove_vacation.submit()
  }
</script>  


<?PHP require(DIR_PATH."includes/footer.inc.php");?>