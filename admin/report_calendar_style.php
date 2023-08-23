<?php
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

$GoToDay = $_POST["GoToDay"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year


//SET DEPARTMENT
$DeptID = $_SESSION["ses_dept_id"];
$dept_ses_name = $_SESSION["ses_dept_name"];

function WriteMonth($StartDate,$Border_color,$Title_color){
	global $DeptID,$Available;
	global $WriteType,$TotalHrs,$Restriction,$Type_Date,$content,$dept_ses_name;

	$WriteMonth="";
	$CurrentDate=date("m/1/y", strtotime ("$StartDate"));
	$setMonth=date("m",strtotime ($CurrentDate));
	$BeginWeek=date("m",strtotime ($CurrentDate));
	$EndWeek=date("m",strtotime ($CurrentDate));

   /***************************************************************************
    *** CREATE THE HEADERS FOR THE MONTH AND DAYS OF THE WEEK                ***
    ***************************************************************************/
	$WriteMonth='
			<table border=0 cellspacing=0 cellpadding=0 width="950">
			<tr>
             <td>
				<b>Vacation Report for '.$dept_ses_name.' on '.date("l \- F dS, Y \- g:i a").'</b>
             </td>
			</tr>
			<tr>
             <td>
	            <table border=0 cellspacing=0 cellpadding=0 width="100%">
	             <tr>
	              <td colspan=7 valign=top bgcolor="#FFFFFF" align="center">
	                  <table width="100%">
	                   <tr>
	                    <td valign=top bgcolor="#FFFFFF" align="left">
	                    </td>
	                    <td valign=top bgcolor="#FFFFFF" align="center">&nbsp;&nbsp;&nbsp;</td>
	                    <td valign=top bgcolor="#FFFFFF" align="right">
	                        <form method="POST" action="'.$_SERVER["PHP_SELF"].'">
	                        <input type="hidden" value="'.date("m/1/y", strtotime ("$CurrentDate -1 months")).'" name="GoToDay">
	                        <input type="submit" value="<" name="B1">
	                        </form>
	                    </td>
	                    <td class="text24BkB" valign=top bgcolor="#FFFFFF" align="center" style="color:#000000">
	                        <b>'.date("M",strtotime ($StartDate)).' '.date("Y",strtotime ($StartDate)).'</b>
	                    </td>
	                    <td valign=top bgcolor="#FFFFFF" align="left">
	                        <form method="POST" action="'.$_SERVER["PHP_SELF"].'">
	                        <input type="hidden" value="'.date("m/1/y", strtotime ("$CurrentDate +1 months")).'" name="GoToDay">
	                        <input type="submit" value=">" name="B1">
	                        </form>
	                    </td>
	                    <td valign=top bgcolor="#FFFFFF" align="center">&nbsp;&nbsp;&nbsp;</td>
	                    <td valign=top bgcolor="#FFFFFF" align="right">
	                    </td>
	                   </tr>
	                  </table>
             </td>
            </tr>
			<tr>
				<td class="text14Wt" align="center" bgcolor="#000000"><b>Sun</b></td>
				<td class="text14Wt" align="center" bgcolor="#000000"><b>Mon</b></td>
				<td class="text14Wt" align="center" bgcolor="#000000"><b>Tue</b></td>
				<td class="text14Wt" align="center" bgcolor="#000000"><b>Wed</b></td>
				<td class="text14Wt" align="center" bgcolor="#000000"><b>Thu</b></td>
				<td class="text14Wt" align="center" bgcolor="#000000"><b>Fri</b></td>
				<td class="text14Wt" align="center" bgcolor="#000000"><b>Sat</b></td>
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
			$WriteMonth.="<tr valign=\"top\">";

            /**************************************************
            ** WRITE IN THE WEEKLY DATE BOXES FOR THE MONTH ***
            **************************************************/
			for($i=0;$i<7;$i++){
				$strTemp="";
				$BGcolor="white";
				$FontColor="#000000";
				$Style="";

				//IF NOT THE CURRENT MONTH. MAKE THE BUTTON SMALL AND THE
                //BUTTON TEXT ITALIC AND THE CELL BACKGROUND DARK GRAY
				if(date("m",strtotime ("$CurrentDate $DaysToAd[$i]"))!=$setMonth){
	                $FontColor="#FF00FF";
					$BGcolor="#C0C0C0";
				}

 				$WriteMonth.='
					<td style="border-style: solid; border-width: thin" class="text8Bk" width="95" height="90" valign="top" align="left" bgcolor="'.$BGcolor.'" '.$Style.'><b><span style="text-decoration:underline; color:'.$FontColor.';">'
				        .date("d",strtotime ("$CurrentDate $DaysToAd[$i]")).
	                '</span></b><br />';

                $current_date = date("Ymd",strtotime("$CurrentDate $DaysToAd[$i]"));
                //RETRIEVE ALL VACATIONS FOR THIS DATE
				$get_vacation = @mysql_query("SELECT fname,lname,hours,replacement,to_id,date_entered " .
                	"from vf_vacation LEFT JOIN vf_employee ON vf_vacation.emp_id = " .
                    "vf_employee.emp_id WHERE vf_vacation.date = $current_date AND vf_employee.enabled != 'N' AND ".
                    "vf_vacation.dept_id = $DeptID AND vf_vacation.deny != 'Y' ORDER BY date_entered ASC");

         		while($vacation_result = @mysql_fetch_array($get_vacation)){
                 		$emp_lname = $vacation_result["lname"];
                 		$emp_fname = $vacation_result["fname"];
                        $emp_emp_id = $vacation_result["emp_id"];
                 		$emp_to_id = $vacation_result["to_id"];
                 		$emp_hours = $vacation_result["hours"];
                 		$emp_rplacmnt = $vacation_result["replacement"];

                        // $emp_full_name = $emp_emp_id;
                        $emp_full_name = $emp_fname . " " . $emp_lname;

					$get_vac_type = @mysql_query("SELECT descr FROM vf_to_type WHERE " .
	                		"to_id = '$emp_to_id'");
					$type_array = @mysql_fetch_array($get_vac_type);
						$emp_to_name = $type_array[0];

                    //SEE IF A REPLACEMENT IS FILLED IN
                    if($emp_rplacmnt != ""){
                    	$e_replace = "<br />R-" . $emp_rplacmnt;
                    }else{
                    	$e_replace = "";
                    }

                    if($emp_to_id == 13){
	                    $WriteMonth.= $emp_full_name." | ".$emp_hours." | ".$emp_to_name.$e_replace.
                           "<hr size=\"1\" style=\"color:#FF0000;\">";
                    }else{
		            	$WriteMonth.= $emp_full_name." | ".$emp_hours.$e_replace .
                                "<hr size=\"1\" style=\"color:#FF0000;\">";
                    }
				}

				//ADD THE INFORMATION FOR THE WEEK TO THE CURRENT MONTHLY DISPLAY
	            $WriteMonth.= "</td>";
			}

			$WriteMonth.="</tr>";
			$CurrentDate=date("m/d/y",strtotime("$CurrentDate +1 week"));
			$StartDateofWeek=date("w",strtotime ($CurrentDate));
			$EndofWeek=6 - $StartDateofWeek;
			$BeginWeek=date("m",strtotime ("$CurrentDate -$StartDateofWeek days"));
			$EndWeek=date("m",strtotime ("$CurrentDate +$EndofWeek days"));
		}
	}
	$WriteMonth.="</table></td></tr></table>";
	return $WriteMonth;
}
/*******************************************************************************
** END OF FUNCTIONS SECTION                                                   **
*******************************************************************************/

$FONT ="Arial";
$FONTSIZE="9px";
$FONTCOLOR="#000000";
$BorderColor="#000000";
$BarColor="#000066";

$cur_page_title = "Vacation Calendar";
require(DIR_PATH."includes/header.inc.php");

//SET VARIABLES
if(!empty($GoToDay)){
	$StartDate=date("m/d/y",strtotime ("$GoToDay"));
}else{
	if(empty($StartDate)){
		$StartDate=date("m/d/y");
	}
}

$sdate = urlencode($StartDate);
echo '<table>
		 <tr>
		  <td width="100%"><a href="report_calendar_style_pdf.php?showmonth='.$sdate.'" target="_blank">Printer Friendly (PDF)</a>
		  </td>
		 </tr>
		 <tr>
		  <td width="100%">&nbsp;</td>
		 </tr>
		 <tr>
		  <td width="100%">'
		  . WriteMonth($StartDate,$BorderColor,$BarColor,1) .
		  '</td>
		 </tr>
	    </table>
		  <p>&nbsp;</p>';


require(DIR_PATH."includes/footer.inc.php");
?>