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
define("DIR_PATH", "");//you must change the path for each sub folder

//IF THE EMPLOYEE ISN'T AN ADMIN. STOP THE USER
if($_SESSION["ses_view_vac"] != "1"){
	header ("Location: ".DIR_PATH."index.php");
}

$GoToDay = $_POST["GoToDay"];
$is_visible = $_POST["is_visible"];

if($is_visible == "")
{
	$is_visible = "visible";	
}

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year



//Build anarray of time off type text colors
$type_cnt = 0;
$sql = @mysql_query("SELECT `to_id`, `descr`, `text_color` FROM `vf_to_type` ORDER BY `descr`");
while($result = @mysql_fetch_array($sql))
{
	$type_id = $result["to_id"];
	$description = $result["descr"];	
	$text_color = $result["text_color"];
	
	$types[$type_cnt][0] = $type_id;
	$types[$type_cnt][1] = $description;
	$types[$type_cnt][2] = $text_color;
			
	$type_cnt++;
}

//Create a legend for the page
$legend = '
<div id="theLayer" style="position:absolute;display:table-cell;height:200px;left:100;top:100;visibility:'.$is_visible.';">
 <div style="background-color:#000099;">
  <table>
   <tr>
    <th id="titleBar" style="color:white;background-color:#000099;cursor:move;">LEGEND</th>
    <th style="color:white;background-color:#000099;" align="right">
	 <a href="#" onClick="hideMe();return false"><font color=#ffffff size=2 face=arial  style="text-decoration:none">X&nbsp;</font></a>
    </th>
   </tr>
  <tr>
   <td colspan="2">
    <div style="background-color:#FFFFCC; display:table-cell;overflow:auto;height:150px;">
     <table cellspacing="2" cellpadding="2" style="background-color:#FFFFCC;">
';
 
for($i=0;$i<$type_cnt;$i++)
{	
	$legend .= '
	 <tr>
	  <td style="background-color:'.$types[$i][2].'">
	   &nbsp;&nbsp;&nbsp;
	  </td>
	  <td>'.$types[$i][1].'</td>
	 </tr>';	
}
$legend .= '
     </table>
    </div>
   </td>
   </tr>
  </table>
 </div>
</div>';





//SET DEPARTMENT
$DeptID = $_SESSION["ses_dept_id"];
$dept_ses_name = $_SESSION["ses_dept_name"];

function WriteMonth($StartDate,$Border_color,$Title_color){
	global $DeptID,$Available,$type_cnt,$types,$is_visible;
	global $WriteType,$TotalHrs,$Restriction,$Type_Date,$content,$dept_ses_name;

	$WriteMonth="";
	$CurrentDate=date("m/1/y", strtotime ("$StartDate"));
	$setMonth=date("m",strtotime ($CurrentDate));
	$BeginWeek=date("m",strtotime ($CurrentDate));
	$EndWeek=date("m",strtotime ($CurrentDate));

   /***************************************************************************
    *** CREATE THE HEADERS FOR THE MONTH AND DAYS OF THE WEEK                ***
    ***************************************************************************/
	$WriteMonth="
			<table border=0 cellspacing=0 cellpadding=0 width=\"950\">
			<tr>
             <td>
				<b>Vacation Report for ".$dept_ses_name." on ".date("l \- F dS, Y \- g:i a")."</b>
             </td>
			</tr>
			<tr>
             <td align=\"right\">
				<a href=\"javascript:showMe();\"><b>Display calendar legend</b></a>
             </td>
			</tr>	
			<tr>
             <td>
	            <table border=0 cellspacing=0 cellpadding=0 width=\"100%\">
	             <tr>
	              <td colspan=7 valign=top bgcolor=\"#FFFFFF\" align=\"center\">
	                  <table width=\"100%\">
	                   <tr>
	                    <td valign=top bgcolor=\"#FFFFFF\" align=\"left\">
	                    </td>
	                    <td valign=top bgcolor=\"#FFFFFF\" align=\"center\">&nbsp;&nbsp;&nbsp;</td>
	                    <td valign=top bgcolor=\"#FFFFFF\" align=\"right\">
	                        <form method=\"POST\" action=\"\">
	                        <input type=\"hidden\" value=\"".date("m/1/y", strtotime ("$CurrentDate -1 months"))."\" name=\"GoToDay\">
	                        <input type=\"submit\" value=\"<\" name=\"B1\">
							<input type=\"hidden\" value=\"".$is_visible."\" name=\"is_visible\" id=\"vis1\">
	                        </form>
	                    </td>
	                    <td class=\"text24BkB\" valign=top bgcolor=\"#FFFFFF\" align=\"center\">
	                        <b><span style=\"color:#000000;\">"
	                        .date("M",strtotime ($StartDate))." ".date("Y",strtotime ($StartDate)).
	                        "</span></b>
	                    </td>
	                    <td valign=top bgcolor=\"#FFFFFF\" align=\"left\">
	                        <form method=\"POST\" action=\"\">
	                        <input type=\"hidden\" value=\"".date("m/1/y", strtotime ("$CurrentDate +1 months"))."\" name=\"GoToDay\">
	                        <input type=\"submit\" value=\">\" name=\"B1\">
							<input type=\"hidden\" value=\"".$is_visible."\" name=\"is_visible\" id=\"vis2\">	                        
	                        </form>
	                    </td>
	                    <td valign=top bgcolor=\"#FFFFFF\" align=\"center\">&nbsp;&nbsp;&nbsp;</td>
	                    <td valign=top bgcolor=\"#FFFFFF\" align=\"right\">
	                    </td>
	                   </tr>
	                  </table>
             </td>
            </tr>
			<tr>
				<td class=\"text14Wt\" align=\"center\" bgcolor=\"#000000\"><b>Sun</b></td>
				<td class=\"text14Wt\" align=\"center\" bgcolor=\"#000000\"><b>Mon</b></td>
				<td class=\"text14Wt\" align=\"center\" bgcolor=\"#000000\"><b>Tue</b></td>
				<td class=\"text14Wt\" align=\"center\" bgcolor=\"#000000\"><b>Wed</b></td>
				<td class=\"text14Wt\" align=\"center\" bgcolor=\"#000000\"><b>Thu</b></td>
				<td class=\"text14Wt\" align=\"center\" bgcolor=\"#000000\"><b>Fri</b></td>
				<td class=\"text14Wt\" align=\"center\" bgcolor=\"#000000\"><b>Sat</b></td>
			</tr>
	";

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

				//Display notable dates
            	//EMPTY VARS
            	$holiday = "";
            	$holiday_display = "";
            	$h_date = date("Y-m-d",strtotime ("$CurrentDate $DaysToAd[$i]"));
                //CHECK FOR HOLIDAYS OR NOTABLE DAYS
            	$hol_sql = @mysql_query("SELECT * FROM vf_notable_dates WHERE date = '$h_date'");		
              	while($hol_result = @mysql_fetch_array($hol_sql)){		
					$holiday = $hol_result["descr"];                  			
					if($holiday != ""){
						$holiday_display .= '<span style="color:#0000FF">'.$holiday.'</span><br />';
					}						
	          	}

 				$WriteMonth.='
					<td style="border-style: solid; border-width: thin" class="text9Bk" width="95" height="90" valign="top" align="left" bgcolor="'.$BGcolor.'" '.$Style.'><u><b><span style="color:'.$FontColor.'">'
				        .date("d",strtotime ("$CurrentDate $DaysToAd[$i]")).
	                '</span></b></u><br />'.$holiday_display;

                $current_date = date("Ymd",strtotime("$CurrentDate $DaysToAd[$i]"));
                //RETRIEVE ALL VACATIONS FOR THIS DATE
				$get_vacation = @mysql_query("SELECT fname,lname,hours,replacement,to_id,date_entered " .
                	"from vf_vacation LEFT JOIN vf_employee ON vf_vacation.emp_id = " .
                    "vf_employee.emp_id Where vf_vacation.date = $current_date AND vf_employee.enabled != 'N' AND ".
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
                    
                    //Get the text color for the timeoff type
                    $text_clr = "";
					for($t=0;$t<$type_cnt;$t++)
					{
						if($emp_to_id == $types[$t][0])
						{							
							$text_clr = $types[$t][2];
						}						
					}
					
					if($text_clr == "")
					{					
						$text_clr = "#000000";
					}
					
                    if($emp_to_id == 13){
	                    $WriteMonth.= '<span style="color:'.$text_clr.'">'.$emp_full_name.' | '.$emp_hours.' | '.$emp_to_name.$e_replace.'</span><hr size="1" style="color:#FF0000">';
                    }else{
		            	$WriteMonth.= '<span style="color:'.$text_clr.'">'.$emp_full_name.' | '.$emp_hours.$e_replace .'</span><hr size="1" style="color:#FF0000">';		            
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
	echo $WriteMonth;
}
/*******************************************************************************
** END OF FUNCTIONS SECTION                                                   **
*******************************************************************************/

$FONT ="Arial";
$FONTSIZE="12px";
$FONTCOLOR="#000000";
$BorderColor="#000000";
$BarColor="#000066";


//SET VARIABLES
if(!empty($GoToDay)){
	$StartDate=date("m/d/y",strtotime ("$GoToDay"));
}else{
	if(empty($StartDate)){
		$StartDate=date("m/d/y");
	}
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Vacation Calendar</title>
<link rel="stylesheet" type="text/css" media="screen" href="<?PHP echo DIR_PATH."css/site.css" ?>">
<script language="JavaScript1.2">

// Script Source: CodeLifter.com
// Copyright 2003
// Do not remove this header

isIE=document.all;
isNN=!document.all&&document.getElementById;
isN4=document.layers;
isHot=false;

function ddInit(e){
  topDog=isIE ? "BODY" : "HTML";
  whichDog=isIE ? document.all.theLayer : document.getElementById("theLayer");  
  hotDog=isIE ? event.srcElement : e.target;  
  while (hotDog.id!="titleBar"&&hotDog.tagName!=topDog){
    hotDog=isIE ? hotDog.parentElement : hotDog.parentNode;
  }  
  if (hotDog.id=="titleBar"){
    offsetx=isIE ? event.clientX : e.clientX;
    offsety=isIE ? event.clientY : e.clientY;
    nowX=parseInt(whichDog.style.left);
    nowY=parseInt(whichDog.style.top);
    ddEnabled=true;
    document.onmousemove=dd;
  }
}

function dd(e){
  if (!ddEnabled) return;
  whichDog.style.left=isIE ? nowX+event.clientX-offsetx : nowX+e.clientX-offsetx; 
  whichDog.style.top=isIE ? nowY+event.clientY-offsety : nowY+e.clientY-offsety;
  return false;  
}

function ddN4(whatDog){
  if (!isN4) return;
  N4=eval(whatDog);
  N4.captureEvents(Event.MOUSEDOWN|Event.MOUSEUP);
  N4.onmousedown=function(e){
    N4.captureEvents(Event.MOUSEMOVE);
    N4x=e.x;
    N4y=e.y;
  }
  N4.onmousemove=function(e){
    if (isHot){
      N4.moveBy(e.x-N4x,e.y-N4y);
      return false;
    }
  }
  N4.onmouseup=function(){
    N4.releaseEvents(Event.MOUSEMOVE);
  }
}

function hideMe(){
  if (isIE||isNN)
  {
  	whichDog.style.visibility="hidden";
  	document.getElementById('vis1').value = "hidden";
  	document.getElementById('vis2').value = "hidden";
  }	
  else if (isN4) 
  {
  	document.theLayer.visibility="hide";
  	document.getElementById('vis1').value = "hidden";
  	document.getElementById('vis2').value = "hidden";  	
  }
}

function showMe(){
  if (isIE||isNN)
  {
 	whichDog.style.visibility="visible";
    document.getElementById('vis1').value = "visible";
  	document.getElementById('vis2').value = "visible"; 
  }
  else if (isN4)
  {
  	document.theLayer.visibility="show";
    document.getElementById('vis1').value = "visible";
  	document.getElementById('vis2').value = "visible"; 
  }
}

document.onmousedown=ddInit;
document.onmouseup=Function("ddEnabled=false");

</script>


</head>

<body style="margin-top:0; margin-left:0;">
	<table>
	 <tr>
	  <td width="100%">
	  </td>
	 </tr>
	 <tr>
	  <td width="100%">&nbsp;</td>
	 </tr>
	 <tr>
	  <td width="100%">
	  <?PHP WriteMonth($StartDate,$BorderColor,$BarColor,1) ?>
	  </td>
	 </tr>
    </table>
    <?PHP echo $legend; ?>
 </body>
</html>

