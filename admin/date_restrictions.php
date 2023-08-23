<?php
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
$DeptID = $_SESSION["ses_dept_id"];//set department
$DeptID = $_SESSION["ses_dept_id"];//set department
$OffType = $_SESSION["ses_offtype"];
$GoToDay = $_REQUEST["GoToDay"];
$sub_id = $_REQUEST["sub_id"];


require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$cur_page_title = "Department configuration";
require(DIR_PATH."includes/header.inc.php");

//GET DEPT NAME
$getdept = @mysql_query("SELECT descr FROM vf_department WHERE dept_id = '$DeptID'");
$getdeptarray = @mysql_fetch_array($getdept);
	$dept_name = $getdeptarray["descr"];

//GET SUB DEPT NAME
$getsub = @mysql_query("SELECT descr FROM vf_sub_dept WHERE dept_id = '$DeptID' AND sub_dept_id = '$sub_id'");
$getsubarray = @mysql_fetch_array($getsub);
	$sub_name = $getsubarray["descr"];

//IF THERE ARE NO SUB DEPTS JUST DISPLAY THE DEPT NAME
if($sub_name != ""){
	$sub_name = "/ " . $sub_name;
}

if($sub_id != 0 && $sub_name == ""){
	echo "You are requesting an invalid sub-department.<br />
    	<a href=\"admin.php\">Return to the administration page</a>";
	exit();
}

function WriteMonth($StartDate,$Border_color,$Title_color){
	global $DeptID,$OffType,$Available,$dept_name;
	global $WriteType,$TotalHrs,$Restriction,$Type_Date,$sub_name,$sub_id;

	$WriteMonth="";
	$CurrentDate=date("m/1/y", strtotime ("$StartDate"));
	$setMonth=date("m",strtotime ($CurrentDate));
	$BeginWeek=date("m",strtotime ($CurrentDate));
	$EndWeek=date("m",strtotime ($CurrentDate));
	$PrevYear=date("m/1/y", strtotime ("$StartDate -1 year"));
	$PrevYearWrt=date("Y", strtotime ("$StartDate -1 year"));
	$NextYear=date("m/1/y", strtotime ("$StartDate +1 year"));
	$NextYearWrt=date("Y", strtotime ("$StartDate +1 year"));

   /***************************************************************************
    *** CREATE THE HEADERS FOR THE MONTH AND DAYS OF THE WEEK                ***
    ***************************************************************************/
	$WriteMonth='
			<table border="0" cellpadding="0" cellspacing="0" width="700">
			  <tr>
			    <td><b>To add date restrictions:</b></td>
			  </tr>
			  <tr>
			    <td>1. Select the date you want
                to add restrictions for by clicking the button on the date you wish.
              </td>
			  </tr>
			  <tr>
			    <td>2. You will be directed to another page where you will enter<br />
                	   &nbsp;&nbsp;&nbsp;either the total &quot;Hours&quot; to allow or total &quot;People&quot; to allow<br />
                       &nbsp;&nbsp;&nbsp;off for that day. This will take precidence over the default restrictions.
                </td>
			  </tr>
			  <tr>
			    <td>3. If you wish to completely block
                the day you will need to enter a &quot;X&quot; in the field.
                </td>
			  </tr>
			  <tr>
			    <td>&nbsp;</td>
			  </tr>
			</table>
			<table border="0" cellspacing="0" cellpadding="0" bgcolor="'.$Border_color.'" width="700">
			<tr><td>
			<table border="0" cellspacing="1" cellpadding="2" width="100%" style="border: 1pt solid '.$Border_color.'">
			<tr>
			 <td colspan=7 valign=top BGCOLOR="#FFFFFF" align="center">
			  <table width="100%">
               <tr>
                <td valign=top BGCOLOR="#FFFFFF" align="left">
                 <form method="POST" action="'.$_SERVER["PHP_SELF"].'">
				 <input type="hidden" value="'.$sub_id.'" name="sub_id">
				 <input type="hidden" value="'.$PrevYear.'" name="GoToDay">
				 <input type="submit" value="'.$PrevYearWrt.'" name="B1">
				 </form>
                </td>
                <td valign=top BGCOLOR="#FFFFFF" align="center">&nbsp;&nbsp;&nbsp;</td>
                <td valign=top BGCOLOR="#FFFFFF" align="right">
                	<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
					<input type="hidden" value="'.$sub_id.'" name="sub_id">
                    <input type="hidden" value="'.date("m/1/y", strtotime ("$CurrentDate -1 months")).'" name="GoToDay">
	                <input type="submit" value="<" name="B1">
	                </form>
				</td>
                <td valign=top style="background-color:#FFFFFF; color:#446482; font-size: 14pt;" align="center">
					<b>'.date("M",strtotime ($StartDate)).' '.date("Y",strtotime ($StartDate)).'</b>
				</td>
                <td valign=top BGCOLOR="#FFFFFF" align="left">
					<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
					<input type="hidden" value="'.$sub_id.'" name="sub_id">
                    <input type="hidden" value="'.date("m/1/y", strtotime ("$CurrentDate +1 months")).'" name="GoToDay">
	                <input type="submit" value=">" name="B1">
	                </form>
				</td>
				<td valign=top BGCOLOR="#FFFFFF" align="center">&nbsp;&nbsp;&nbsp;</td>
                <td valign=top BGCOLOR="#FFFFFF" align="right">
                 <form method="POST" action="'.$_SERVER["PHP_SELF"].'">
				<input type="hidden" value="'.$sub_id.'" name="sub_id">
                 <input type="hidden" value="'.$NextYear.'" name="GoToDay">
				 <input type="submit" value="'.$NextYearWrt.'" name="B1">
				 </form>
                </td>
               </tr>
              </table>
             </td>
            </tr>
			<tr>
				<td align="center" style="background-color:#000000; color:#FFFFFF"><B>S</B></td>
				<td align="center" style="background-color:#000000; color:#FFFFFF"><B>M</B></td>
				<td align="center" style="background-color:#000000; color:#FFFFFF"><B>T</B></td>
				<td align="center" style="background-color:#000000; color:#FFFFFF"><B>W</B></td>
				<td align="center" style="background-color:#000000; color:#FFFFFF"><B>T</B></td>
				<td align="center" style="background-color:#000000; color:#FFFFFF"><B>F</B></td>
				<td align="center" style="background-color:#000000; color:#FFFFFF"><B>S</B></td>
			</tr>';
	
	//GET THE VALUE IF THE CURRENT CALENDAR DATE
    $TodaysDate=date("Ymd");
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

				//CHANGE BACKGROUND COLOR OF TODAYS DATE
				if(date("Ymd",strtotime ("$CurrentDate $DaysToAd[$i]")) == $TodaysDate){
					$BGcolor = "#FFFFCC";	
				}				
				
/* 				$WriteMonth.='
					<td width="95" height="40" valign="top" align="left" bgcolor="'.$BGcolor.'" '.$Style.'><u><b><font size="1" color="'.$FontColor.'">'
				        .date("d",strtotime ("$CurrentDate $DaysToAd[$i]")).
	                '</font></b></u><br />';
*/
 				$WriteMonth.='
					<td width="95" height="50" valign="top" align="left" bgcolor="'.$BGcolor.'" '.$Style.'>';

                $current_date = date("Ymd",strtotime("$CurrentDate $DaysToAd[$i]"));

                //RETRIEVE ALL RESTRICTIONS FOR THIS DATE
				$get_restriction = @mysql_query("SELECT * FROM vf_off_perday WHERE " .
                	"dept_id = '$DeptID' AND sub_dept_id = '$sub_id' AND day = '$current_date'");
				$restriction_result = @mysql_fetch_array($get_restriction);
                 		$restrict_day = $restriction_result["day"];
                 		$restrict_amount = $restriction_result["total_off"];

 				$WriteMonth.='
	                <form method="POST" action="date_restrictions_entry.php">
	                 <input type="hidden" value="'.$restrict_amount.'" name="restrict_amount">
	                 <input type="hidden" value="'.$sub_id.'" name="sub_id">
	                 <input type="hidden" value="'.$OffType.'" name="OffType">
	                 <input type="hidden" value="'.date("m/d/y",strtotime ("$CurrentDate $DaysToAd[$i]")).'" name="GoToDay">
	                 <input type="submit" value="'.date("d",strtotime ("$CurrentDate $DaysToAd[$i]")).'" name="B1">
                     </form>';

                if($restrict_amount != ""){
		           	$WriteMonth.= "<font color=\"#FF0000\">Restricted to ".$restrict_amount.
                                "</font>";
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

$FONT ="Verdana, Arial, Helvetica, sans-serif";
$FONTSIZE="8";
$FONTCOLOR="#000000";
$BorderColor="#CCCCFF";
$BarColor="#000066";

//SET VARIABLES
if(!empty($GoToDay)){
	$StartDate=date("m/d/y",strtotime ("$GoToDay"));
}else{
	if(empty($StartDate)){
		$StartDate=date("m/d/y");
	}
}

echo  '<table>
			 <tr>
			  <td width="100%">
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