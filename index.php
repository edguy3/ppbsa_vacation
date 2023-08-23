<?php
/******************************************************************************
**  File Name: index.php
**  Description: Users home page. Displays vaction information.
**  Written By: Gary Barber
**  Original Date:8/27/04
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

//SET PAGE VARIABLES
$year_to_view = $_POST["year_to_view"];
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];//concatenate first and last name
$year_option = "";

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$error_message = $_REQUEST["error_message"];

$hdr_addin = '
	<script language="javascript" type="text/javascript">
	<!--
	function formload() {
	    var username = document.LoginForm.username;
	    if (username) username.focus();
	}
	//-->
	</script>';


if($_SESSION["ses_login_success"] != 'Y'){
	$bdy_addin = 'onLoad="formload();"';
}

if($_SESSION["ses_login_success"] == 'Y'){
	$hdr_detail = 'Time Off Information For<br />'.$user_name;
}else{
	$hdr_detail = 'Vacation Login';	
}

$cur_page_title = "Employee Summary";
require(DIR_PATH."includes/header.inc.php");

//IF THE USER SUCCESFULLY LOGGED IN DISPLAY THEIR INFORMATION.
if($_SESSION["ses_login_success"] == 'Y'){
	
	//load options for select box of years in the database
	$GetYr = @mysql_query("SELECT * FROM vf_year");
	while($YrArray = @mysql_fetch_array($GetYr)){
		$CurYear = $YrArray["year"];
		
		if($CurrentYear == $CurYear){
			$selected = "selected";
		}else{
			$selected = "";
		}			
		$year_option .= "<option $selected>$CurYear</option>\n";
	}
	
	//Set variable to determine which year to view. If they did not choose a year, use the current fiscal year.	
	if(!isset($year_to_view) || $year_to_view == "--Select a year to view--"){
		$year_to_view = $CurrentYear;		
	}
	
	//If the user has entered an email address display it.
	if($_SESSION["ses_email"] != ""){
		$email_addr = $_SESSION["ses_email"];
	}else{
		$email_addr = "Not available";
	}

	echo '<table border="0" cellpadding="0" cellspacing="0" width="700" id="tablepos">
	  <tr>
	    <td align="right"><b>Contact Email:</b> ' . $email_addr . '</td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>			
	  <tr>
	    <td>
        	<b>To request a vacation:</b><br />
        	1. If you have an email address listed above, when vacation has been approved<br />
			   you will receive an email to let you know.<br /> 	
			2. Click on the Calendar menu option at the top of the page to request vacation time.<br />
            3. Follow the instructions given on the Calendar page.
        </td>
	  </tr>
       <tr> 
     	<td>&nbsp;</td>
	  </tr>
	  <tr>
		<td class="text14BkB" align="center"><u><span class="text14rdb">'.$year_to_view.'</span> <b>summary of time off for ' . $user_name . '</b></u></td>
	  </tr>
	  <tr>
	    <td>&nbsp; </td>
	  </tr>
	  <tr>
	    <td align="center">
			<form method="POST" action="vacation_detail_all.php">
			<input type="hidden" value="' . $year_to_view . '" name="year_to_view">
			<input type="submit" value="View all time off as one grouping sorted by date" name="return">
			</form>	
		</td>
	  </tr>
	  <tr>
	    <td>&nbsp; </td>
	  </tr>
	  <tr>
	    <td>
	      <table border="0" cellpadding="1" cellspacing="0" width="700" id="datatable">
	        <tr id="tableheader">
			  <th>Type</th>
	          <th>Total<br />Hours</th>
	          <th>Hours<br />Scheduled</th>
	          <th>Hours Available<br />to Schedule</th>
	          <th>Hours<br />Used</th>
	          <th>Unused<br />Hours</th>
	          <th></th>
	        </tr>';

					//get all of the time off types that the employee may view
					$SQL = @mysql_query("SELECT * from vf_to_type WHERE emp_viewable = 'Y' AND
				    					(dept_id = '0' OR dept_id = '$_SESSION[ses_dept_id]')");
				    //initialize the cell background color
				    $bgcolor = "#FFFFFF";
				    while($Result = @mysql_fetch_array($SQL)){
						$TypeID = $Result["to_id"];
				        $Descr = $Result["descr"];
				        $Valid_date = $Result["type_date"];
				
						if($bgcolor == "#FFFFFF"){
				        	$bgcolor = "#C0C0C0";
				        }else{
				        	$bgcolor = "#FFFFFF";
						}
				
				        //If there is a valid date for this vacation type let the user know
				        //valid date designates that the vacation type cannot be scheduled
				        //until that date or later.
				/*        if($Valid_date != "0000-00-00"){
					        $Type_Date = "Can't be used before: "  . date("m/d/Y",strtotime($Valid_date));
				    	}
				*/
				        //LOOKUP THE TOTAL VACATION TIME THE EMPLOYEE HAS EARNED FOR THIS TIME OFF TYPE
				        $GetVac = @mysql_query("SELECT * FROM vf_emp_to_hours WHERE to_id = '$TypeID' " .
				        					"AND year = '$year_to_view' AND emp_id = '$_SESSION[ses_emp_id]'");
						$Vac_Array = @mysql_fetch_array($GetVac);
				        	$Earned = $Vac_Array["hours"];
				
						 //IF THE EMPLOYEE HAS TIME EARNED GET DETAILS OTHERWISE SKIP THIS TYPE
						 if($Earned != ""){
				            //RESET ALL THE VARIABLES
				            $TtlUsed = 0;
				            $TtlScheduled = 0;
				            $TtlRemaining = 0;
				            $TtlToSchedule = 0;
							$TtlUnused = 0;
				
				            //LOOKUP ALL OF THE EMPLOYEES SCHEDULED TIME OFF FOR THE CURENT TYPE
							$Get_Scheduled = @mysql_query("SELECT * FROM vf_vacation WHERE " .
				               		"to_id = '$TypeID' AND emp_id = '$_SESSION[ses_emp_id]' " .
				                     "AND year = '$year_to_view'");
							while($Load_Sched = @mysql_fetch_array($Get_Scheduled)){
				            	$Vac = $Load_Sched["hours"];
				                $VacDate = $Load_Sched["date"];
				
				                //FORMAT THE DATE FOR COMPARISON
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
				
				    //AFTER RETRIEVING MORE INFORMATION. CONTINUE BUILDING TO THE INFORMATION
					echo '
		        <tr>
		          <td class="text12BkB" align="center" bgcolor="'.$bgcolor.'">' . $Descr . '<br /><span class="text09Wt">'.$Type_Date.'</span<br /></td>
		          <td class="text12BkB" align="center" bgcolor="'.$bgcolor.'">' . $Earned . '</td>
		          <td class="text12BkB" align="center" bgcolor="'.$bgcolor.'">' . $TtlScheduled . '</td>
        		  <td class="text12BkB" align="center" bgcolor="'.$bgcolor.'">' . $TtlToSchedule . '</td>
		          <td class="text12BkB" align="center" bgcolor="'.$bgcolor.'">' . $TtlUsed . '</td>
		          <td class="text12BkB" align="center" bgcolor="'.$bgcolor.'">' . $TtlUnused . '</td>
		          <td align="right" bgcolor="'.$bgcolor.'">
                   <form method="POST" action="vacation_detail.php" style="margin-bottom:0;">
                    <input type="hidden" value="' . $year_to_view . '" name="hdnYear">	
                    <input type="hidden" value="' . $TypeID . '" name="TOType">
                    <!-- <input border="0" src="images/detail.gif" name="I1" type="image" width="46" height="11"> -->
					<input border="0" name="I1" type="submit" value="Details" style="background:black;border:none;color:white;">
                   </form>
                  </td>
		        </tr>';
   	   	}		     
    }
     echo '</table>
	    </td>
       </tr>
       <tr> 
     	<td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td>By default the current fiscal year is loaded. To view other years change the selection below.</td>
	  </tr>	
	  <tr align="center">
	    <td><form method="POST" action="'.$_SERVER["PHP_SELF"].'">
	  		<select size="1" name="year_to_view">
	    		<option>--Select a year to view--</option>
	    		'.$year_option.'	
	  		</select>&nbsp;<input type="submit" value="Reload Page" name="B1">
			</form>
		</td>
	  </tr>	     
	</table>     
     
     ';
  //If the user isn't logged in display a login screen
}else{
	echo '
	<p align="center"><b>Please login to work with pages on this site!</b></p>
    <form name="LoginForm" action="login.php" method="post">
	<table border="0" cellpadding="0" cellspacing="0" width="250" id="datatable" bgcolor="#C0C0C0" align="center">
	  <tr>
	    <td>
	      &nbsp;
	      <table border="0" cellpadding="0" cellspacing="0" bgcolor="#C0C0C0" width="100%">
	        <tr>
	          <td width="100">&nbsp;</td>
	          <td width="150">&nbsp;</td>
	        </tr>
	        <tr>
	          <td width="100"align="right">&nbsp;Username&nbsp;</td>
	          <td width="150"> <input type="text" name="username" size="16" maxlength="16" >
	          </td>
	        </tr>
	        <tr>
	          <td width="100"align="right">&nbsp;Password&nbsp; </td>
    	      <td width="150"> <input type="password" name="password" size="16" maxlength="16" >
	          </td>
	        </tr>
	        <tr>
	          <td width="100">&nbsp;</td>
	          <td width="150">&nbsp;</td>
	        </tr>
	        <tr>
	          <td width="100">&nbsp;</td>
	          <td width="150"><input type="submit" name="Submit" value="Login" >
	          </td>
	        </tr>
	        <tr>
	          <td width="100">&nbsp;</td>
	    	  <td width="150">&nbsp;</td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	</table>
	&nbsp;
	</form>';
}
?>
<p>&nbsp;</p>
<?PHP require(DIR_PATH."includes/footer.inc.php");?>