<?PHP
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
$DeptID = $_SESSION["ses_dept_id"];
$employee_sub_dept = $_SESSION["ses_sub_dept_id"];
$sub_dept_drpdown = $_REQUEST["sub_dept_drpdown"];
$sub_dept_selection = $_REQUEST["sub_dept_selection"];
$update_submission = $_POST["update_submission"];
$edate = $_POST["edate"];
$notice = $_POST["notice"];
$track = $_POST["track"];
$trackwithdefault = $_POST["trackwithdefault"];
$restrict = $_POST["restrict"];
$minimum_hours = $_POST["minimum_hours"];
$contact_email = $_POST["contact_email"];
$submit_button = $_POST["submit_button"];
$Update_Configuration = $_POST["UpdateConfiguration"];
$Add_Configuration = $_POST["AddConfiguration"];
$error_on_page = 0;

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year


//IF THE DEPARTMENT USES SUB DEPARTMENTS MAKE THEM SELECT THE SUB
$ck_sub = mysql_query("SELECT * FROM vf_department WHERE dept_id = '$DeptID'");
$use_sub = mysql_fetch_array($ck_sub);
	$dept_subs = $use_sub["sub_dept"];


//INCLUDE UPDATE FILE IF THE FORM WAS SUBMITTED
if(isset($update_submission)){
	include("config_update.php");
}
	
if($dept_subs != "Y"){
	$sub_dept_drpdown = "0";
}

$cur_page_title = "Configure department restrictions";
$hdr_addin = '<script language="JavaScript" SRC="popCal/calendar.js" type="text/javascript"></script>';
if($dept_subs == "Y" && ! $sub_dept_selection){
	$itemcnt = 1; //Count of select boxes on the current page	
}
require(DIR_PATH."includes/header.inc.php");

if($dept_subs == "Y" && ! $sub_dept_selection){
?>
		<p><b>This department has sub-departments. Please select the sub-department you want to configure.</b></p>
		<div id="HideItem0" style="POSITION:relative; margin-left:100;">
			<form method="POST" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>">
			<select size="1" name="sub_dept_drpdown">
			<option value="default<?PHP echo $DeptID; ?>">Default Department</option>
		<?PHP
		$sql_subs = @mysql_query("SELECT sub_dept_id,descr,code FROM vf_sub_dept
	    		WHERE dept_id = '$DeptID'");
		while($sql_sub_array = @mysql_fetch_array($sql_subs)){
	    	$fetch_sub_id = $sql_sub_array["sub_dept_id"];
	    	$fetch_descr = $sql_sub_array["descr"];
	    	$fetch_code = $sql_sub_array["code"];

	       	echo '<option value="'.$fetch_sub_id.'">'.$fetch_code.'</option>';

		}
		?>
		    </select>
            <input type="submit" value="Submit" name="sub_dept_selection">
           </form>
		</div>
<?PHP

}else{

	//CHECK IF THEY CHOOSE THE DEFAULT DEPT OR A SUB DEPT
	if($sub_dept_drpdown == "default".$DeptID || $sub_dept_drpdown == "0"){
		$sub_dept_drpdown = 0;
		$sub_attachment = "";
	}else{
			$sql_get_sub = @mysql_query("SELECT descr FROM vf_sub_dept
		    		WHERE dept_id = '$DeptID' AND sub_dept_id = '$sub_dept_drpdown'");
			$get_sub_array = @mysql_fetch_array($sql_get_sub);
	        	$current_sub_name = $get_sub_array["descr"];
			$sub_attachment = " - " . $current_sub_name;
	}
	
	//RETRIEVE CURRENT VALUES FROM THE CONFIGURATION TABLE
	$SQL = @mysql_query("SELECT * FROM vf_config WHERE dept_id = '$DeptID'
						AND sub_dept_id = '$sub_dept_drpdown'");
	$RESULT_ARRAY = @mysql_fetch_array($SQL);

    $OffType = $RESULT_ARRAY["people_off_type"];
    if(isset($edate)){
    	$LastDate = $edate;
    }else{
    	$LastDate = $RESULT_ARRAY["last_vac_date"];
    }
	if(isset($notice)){
		$PreNotice = $notice;    
	}else{
    	$PreNotice = $RESULT_ARRAY["days_notice"];    
	}
	if(isset($restrict)){
		$EmployeesOff = $restrict;		
	}else{
		$EmployeesOff = $RESULT_ARRAY["emp_off_ttl"];
	}
	if(isset($minimum_hours)){
		$minimum_hours = $minimum_hours;    
	}else{
		$minimum_hours = $RESULT_ARRAY["min_hours_per_day"];
	}
	if(isset($contact_email)){
		$contact_email = $contact_email;
	}else{
		$contact_email = $RESULT_ARRAY["email"];
	}
    $include_default_dept = $RESULT_ARRAY["include_default"];

    
	if($OffType == ""){
		$submit_button_name = "AddConfiguration";
		$submit_button_val = "Add Configuration";
    }else{
		$submit_button_name = "UpdateConfiguration";
		$submit_button_val = "Update Configuration";		
    }

	//MAKE SURE VARIABLES ARE EMPTY
	$SelHrs = "";
	$SelPeop = "";
	
    if(isset($track)){
	    if($track == "H"){
    		$SelHrs = "selected";
		}elseif($track == "P"){
			$SelPeop = "selected";
		}    
    		
	}else{
	    if($OffType == "H"){
    		$SelHrs = "selected";
		}elseif($OffType == "P"){
			$SelPeop = "selected";
		}
	}	
	
	//DISPLAY ERROR MESSAGE IF THERE ARE ERRORS 
	echo $error_msg;
	
	//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
	if($sub_dept_drpdown != 0){
		echo '<a href="config.php">Select a different sub-department</a><br />';
	}
	?>
	
	<form name="config" method="post" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>">
	  <table border="0" cellpadding="0" cellspacing="0" width="690" style="height:296px;margin-left=10px;">
	    <tr>
	      <td height="21"></td>
	    </tr>
	    <tr>
	      <td height="21"><b>Department:&nbsp;<span style="color:#FF0000"><?PHP echo $_SESSION["ses_dept_name"].$sub_attachment; ?></span></b></td>
	    </tr>
	    <tr>
	      <td height="21">&nbsp;</td>
	    </tr>
	    <tr>
	      <td height="21"><b><u>If you are going to add an ending vacation date limit. Enter the date below.</u></b></td>
	    </tr>
	    <tr>
	      <td height="62">
	      	Example: Transportation only allows people to schedule vacations during the current<br />
	        fiscal year. To be fair they don't allow people to start the next year until a given date.<br />
	        At that date, it is first come first serve. They would not change this date until the <br />
	        predetermined date arrives. <b>Note:</b> This should always end on a Sunday.
	      </td>
	    </tr>
	    <tr>
	      <td height="25"><b>End Date: </b>
	      <input type="text" value="<?PHP echo $LastDate; ?>" readonly name="edate" size="11">
			<!-- <a href="javascript:doNothing()" onClick="setDateField(document.config.edate);top.newWin=window.open('popCal/calendar.html','cal','dependent=yes,width=210,height=230,screenX=800,screenY=1200,titlebar=yes,left=150,top=150')">
			<img src="popCal/calendar.gif" border="0"></a> -->
				<a href="javascript:void(0)" onclick="gfPop.fPopCalendar(document.config.edate);return false;" HIDEFOCUS>
                <img name="popcal" align="absmiddle" src="popCal/calbtn.gif" width="34" height="22" border="0" alt=""></a>  			
	      </td>
	    </tr>
	    <tr>
	      <td height="21">&nbsp;</td>
	    </tr>
	    <tr>
	      <td height="20"><b><u>Advanced vacation notice.</u></b></td>
	    </tr>
	    <tr>
	      <td height="21">
	      	Enter the number of days advanced notice that you require an employee<br />
	        to give before he may take a vacation. (By entering a number here<br />
	        the calendar displayed to the employee will not display a button<br />
	        to add vacation until the date is the current date plus the value <br />
	        you enter.)
	      </td>
	    </tr>
	    <tr>
	      <td height="21"><b>Advanced Notice: </b>
	      <input type="text" value="<?PHP echo $PreNotice; ?>" name="notice" size="4"></td>
	    </tr>
	    <tr>
	      <td height="21">&nbsp;</td>
	    </tr>
	    <tr>
	      <td height="21"><b><u>How do you want to track vacations?&nbsp;</u></b></td>
	    </tr>
	    <tr>
	      <td height="21">
	      	You can restrict vacations by <b> hours</b> allowed off per day or <b> people</b><br />
	        allowed off per day. This will be used for reference when checking if there is<br />
	        available time for a person to take a vacation on a given day
	      </td>
	    </tr>
	    <tr>
	      <td height="21"><b>Tracking Type: </b><select size="1" name="track">
	          <option <?PHP echo $SelHrs; ?> value="H">Hours</option>
	          <option <?PHP echo $SelPeop; ?> value="P">People</option>
	        </select></td>
	    </tr>
	<?PHP     
	//IF THIS IS A SUB DEPARTMENT ADD A CHECK BOX TO INCLUDE DEFAULT CHECKS
	if($sub_attachment != ""){
		if(isset($trackwithdefault) && $trackwithdefault != $include_default_dept){
			$include_default_dept = $trackwithdefault;
		}
		if($include_default_dept == 1){
	     	$select_default = "checked";
	    }else{
	     	$select_default = "";
	    }
	?>
	
	    <tr>
		  <td height="21">&nbsp;</td>
		</tr>
	    <tr>
	      <td height="21">
	      <input type="checkbox" name="trackwithdefault" <?PHP echo $select_default; ?> value="ON">
	      &nbsp; If you want to combine the checks with this sub_department and
	      the default department check this box. If checked the users in this
	      sub_department will have to meet the &quot;Default restrictions&quot; for this
	      sub_department and the restrictions imposed on the default department.
	      </td>
	    </tr>
	<?PHP 
	}
	?>
	
	     <tr>
		      <td height="21">&nbsp;</td>
		    </tr>
		    <tr>
		      <td height="21"><b><u>Default restrictions.</u></b></td>
	    	</tr>
		    <tr>
		      <td height="21">
	          	This value will be the default value for either the number of hours<br />
		        or the number of people allowed off per day. If you do not want<br />
		        any restrictions just enter a value higher than the number of employees<br />
				who could possibly be off on a given date.
		      </td>
		    </tr>
		    <tr>
		      <td height="21"><b>Default Value: </b>
		      <input type="text" value="<?PHP echo $EmployeesOff; ?>" name="restrict" size="4"></td>
		    </tr>
		    <tr>
		      <td height="21">&nbsp;</td>
		    </tr>
		    <tr>
		      <td height="21"><b><u>Minimum Hours Request.</u></b></td>
	    	</tr>
		    <tr>
		      <td height="21">
	          	This value will restrict the minimum number of hours an employee may request per day.<br />
		        This is to prevent a user from purposly selecting a large number of daysoff and only<br />
	            entering one hour to try to reserve the date until they make a final decision of the<br />
	            exact one they wish to use. If you do not want restrcitions enter 0.
		      </td>
		    </tr>
		    <tr>
		      <td height="21"><b>Minimum hours: </b>
		      <input type="text" value="<?PHP echo $minimum_hours; ?>" name="minimum_hours" size="4"></td>
		    </tr>
		    <tr>
		      <td height="21">&nbsp;</td>
		    </tr>
		    <tr>
		      <td height="21"><b><u>Email for Requests.</u></b></td>
	    	</tr>
		    <tr>
		      <td height="21">
				Enter the email address that all vacation request should be emailed to.<br />
	            If it should be directed to a group and you need to have one created contact<br />
	            Trent Maring or Gary Barber.
		      </td>
		    </tr>
		    <tr>
		      <td height="21"><b>Email: </b>
		      <input type="text" value="<?PHP echo $contact_email; ?>" name="contact_email" size="25"></td>
		    </tr>
		    <tr>
		      <td height="21"></td>
		    </tr>
		  </table>
	       <input type="hidden" value="1" name="sub_dept_selection">
	       <input type="hidden" value="1" name="update_submission">
	       <input type="hidden" value="<?PHP echo $sub_dept_drpdown; ?>" name="sub_dept_drpdown">
		   <input type="submit" value="<?PHP echo $submit_button_val; ?>" name="<?PHP echo $submit_button_name; ?>">
		</form>
		<!--  PopCalendar(tag name and id must match) Tags should sit at the page bottom -->
		<iframe width=174 height=189 name="gToday:company_cal2:agenda.js" id="gToday:company_cal2:agenda.js" src="popCal/cal_ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
		</iframe>
<?PHP	
}
require(DIR_PATH."includes/footer.inc.php");
?>