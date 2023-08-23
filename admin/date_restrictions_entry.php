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
$DeptID = $_SESSION["ses_dept_id"];
$GoToDay = $_POST["GoToDay"];
$sub_id = $_POST["sub_id"];
$OffType = $_POST["OffType"];
$restrict_amount = $_POST["restrict_amount"];
$hours = $_POST["hours"];
$add_restriction = $_POST["add_restriction"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

//IF THE USER TRIES TO GO DIRECTLY TO THIS PAGE STOP THEM
if($GoToDay == ""){
	echo "You can't access this page directly.";
    exit();
}

//IF THE USER IS ADDING A NEW DATE RESTRICTION. RUN THIS.
if(isset($add_restriction)){

	//VARIABLE THAT WILL BE USED TO SEE IF THERE ARE ANY ERRORS AFTER THE FORM IS SUBMITTED
	$check_form = 0;

   /****************************************************************************
   **            --     VALIDATE THE FORM      --                             **
   ****************************************************************************/
   //VALIDATE THE HOURS FIELD. NUMBERS OR X ONLY.
   $ValidChar = (preg_match("/^[xX0-9]+$/i",$hours));
   if($ValidChar == 0){
        $message[] = "The \"Allowed\" field can only contain numbers or an X.";
		$check_form = 1;
	}

    //ECHO MESSAGE BACK TO THE USER IF THERE WERE ANY ERRORS ON THE FORM OTHERWISE
    //INSERT THE INFORMATION TO THE DATABASE
	if($message){
    	$error_message = "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$error_message .= "$numeric_text. $value <br />\n";
	            $numeric_text = $numeric_text + 1;
            }
		$error_message .= "</font><br />";
            unset($numeric_text);
	}else{
        //IF LOWERCASE CHANGE TO UPPERCASE
		if($hours == "x"){
        	$hours = "X";
        }

        //INSERT THE DATA
        $result = @mysql_query("INSERT INTO vf_off_perday SET
			day = '".date("Ymd",strtotime($GoToDay))."',
            dept_id = '$DeptID',
            sub_dept_id = '$sub_id',
            total_off = '$hours'")or die("Unable to add restriction.");

		//LET THE USER KNOW THE RESTRICTION WAS ADDED
		if($hours != X){
	    	$message = urlencode("<b>The following restriction was added: " .
				"</b><br />".date("l F j, Y",strtotime($GoToDay))." is restricted to $hours <br /></font><br />");
		}else{
	    	$message = urlencode("<b>The following restriction was added: " .
				"</b><br />".date("l F j, Y",strtotime($GoToDay))." is restricted and no one can take this date off.<br /></font><br />");
        }

        //REDIRECT THEM BACK TO THE RESTRICTION CALENDAR
		header("Location: date_restrictions.php?error_message=$message&GoToDay=$GoToDay&sub_id=$sub_id");
		exit();
    }
}

//IF THE USER IS CHANGING A DATE RESTRICTION. RUN THIS.
if(isset($update_restriction)){

	//VARIABLE THAT WILL BE USED TO SEE IF THERE ARE ANY ERRORS AFTER THE FORM IS SUBMITTED
	$check_form = 0;

   /****************************************************************************
   **            --     VALIDATE THE FORM      --                             **
   ****************************************************************************/
   //VALIDATE THE HOURS FIELD. NUMBERS OR X ONLY.
   $ValidChar = (preg_match("/^[xX0-9]+$/i",$hours));
   if($ValidChar == 0){
        $message[] = "The \"Allowed\" field can only contain numbers or an X.";
		$check_form = 1;
	}

    //ECHO MESSAGE BACK TO THE USER IF THERE WERE ANY ERRORS ON THE FORM OTHERWISE
    //INSERT THE INFORMATION TO THE DATABASE
	if($message){
    	$error_message .= "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$error_message .= "$numeric_text. $value <br />\n";
	            $numeric_text = $numeric_text + 1;
            }
		$error_message .= "</font><br />";
            unset($numeric_text);
	}else{
        //IF LOWERCASE CHANGE TO UPPERCASE
		if($hours == "x"){
        	$hours = "X";
        }

        //INSERT THE DATA
        $result = @mysql_query("UPDATE vf_off_perday SET
            total_off = '$hours' WHERE
			day = '".date("Ymd",strtotime($GoToDay))."' AND
            dept_id = '$DeptID' AND
            sub_dept_id = '$sub_id'")or die("Unable to add restrictions.");

		//LET THE USER KNOW THE RESTRICTION WAS ADDED
		if($hours != X){
	    	$message = urlencode("<b>The following restriction was added: " .
				"</b><br />".date("l F j, Y",strtotime($GoToDay))." is restricted to $hours <br /></font><br />");
		}else{
	    	$message = urlencode("<b>The following restriction was added: " .
				"</b><br />".date("l F j, Y",strtotime($GoToDay))." is restricted and no one can take this date off.<br /></font><br />");
        }

        //REDIRECT THEM BACK TO THE RESTRICTION CALENDAR
		header("Location: date_restrictions.php?error_message=$message&GoToDay=$GoToDay&sub_id=$sub_id");
		exit();
    }
}

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

//ADD DATE VARIABLE FOR THE FOLLOWING QUERY
$current_date = date("Ymd", strtotime($GoToDay));

//RETRIEVE ALL RESTRICTIONS FOR THIS DATE
$get_restriction = @mysql_query("SELECT * FROM vf_off_perday WHERE " .
       	"dept_id = $DeptID AND sub_dept_id = '$sub_id' AND day = '$current_date'");
$restriction_result = @mysql_fetch_array($get_restriction);
	$restrict_day = $restriction_result["day"];
    $hours = $restriction_result["total_off"];


/**********************************************************************
** GET THE CURRENT DEPARTMENT CONFIGURATION RULES                    **
**********************************************************************/
//RETRIEVE CURRENT VALUES FROM THE CONFIGURATION TABLE
$conf_sql = @mysql_query("SELECT * FROM vf_config WHERE dept_id = '$DeptID' AND " .
				" sub_dept_id = '$sub_id'");
$config_array = @mysql_fetch_array($conf_sql);
	$EmployeesOff = $config_array["emp_off_ttl"];
    $PreNotice = $config_array["days_notice"];
    $OffType = $config_array["people_off_type"];
    $LastDate = $config_array["last_vac_date"];
    $minimum_hours = $config_array["min_hours_per_day"];

$cur_page_title = "Date Restrictions";
require(DIR_PATH."includes/header.inc.php");    
    
//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
echo '
       <table border="0" cellpadding="0" cellspacing="0" width="500" style="margin-left:10;">
		  <tr>
		    <td><a href="date_restrictions.php?GoToDay='.$GoToDay.'&sub_id='.$sub_id.'">
				Select a different date</a>
            </td>
		  </tr>
		  <tr>
		    <td>&nbsp;
            </td>
		  </tr>
		  <tr>
		    <td><b>To add date restrictions to the date listed below:</b></td>
		  </tr>
		  <tr>
		    <td>
            	1. Add the hours/people allowed during the date listed.
            </td>
		  </tr>
		  <tr>
		    <td>
            	2. Update the information.
            </td>
		  </tr>
		  <tr>
		    <td>
            	3. To completely block the day. Enter an <b>X</b> in the field.
            </td>
		  </tr>
		  <tr>
		    <td></td>
		  </tr>
		</table>

		<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
  <table border="3" cellpadding="0" cellspacing="0" width="375" style="margin-left:10; border-color:#00319C;">
    <tr>
      <td>
        <!--INFORMATION TABLE-->
          <table border="0" cellpadding="0" cellspacing="0" bgcolor="#C0C0C0" width="100%" align="center">
            <tr>
              <td width="10">&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">&nbsp;</td>
              <th align="center" valign="bottom">Date</th>';

	if($OffType == "H"){
             echo '<th>Total<br />Hours<br />Allowed</th>';
	}else{
             echo '<th>Total<br />People<br />Allowed</th>';
	}

	echo '
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">&nbsp;</td>
              <td align="center">' . date("l",strtotime($GoToDay)) . '-' . $GoToDay . '
              &nbsp;</td>
              <td><input style="text-align: Center;" type="text" name="hours" size="8" value="' . $hours . '"></td>
              <td width="10">&nbsp;</td>
            </tr>';

			if($hours != ""){
				echo '
                	<tr>
			         <td width="10">&nbsp;</td>
                	 <td align="right"><a href="date_restrictions_remove.php?sub_id='.$sub_id.'&hours='.$hours.'&GoToDay='.$GoToDay.'">
                                Remove this restriction</a></td>
		             <td>&nbsp;</td>
		             <td width="10">&nbsp;</td>
		            </tr>
               		<tr>
		              <td width="10">&nbsp;</td>
                      <td align="right"></td>
		              <td>&nbsp;</td>
		              <td width="10">&nbsp;</td>
		            </tr>
                    ';

			}else{
				echo '
                		<tr>
			              <td width="10">&nbsp;</td>
                          <td align="right"></td>
			              <td>&nbsp;</td>
			              <td width="10">&nbsp;</td>
			            </tr>';
			}

            echo '
            <tr>
              <td width="10">
              <input type="hidden" value="' . $GoToDay . '" name="GoToDay">
              <input type="hidden" value="' . $sub_id . '" name="sub_id">
              &nbsp;</td>
              <td align="center" colspan="2">';

            if($hours == ""){
			    echo '
	              <input type="submit" value="Add Date Restriction" name="add_restriction"></td>';
			}else{
			    echo '
	              <input type="submit" value="Update Date Restriction" name="update_restriction"></td>';
            }

    echo '
              <td width="10">&nbsp;</td>
            </tr>
            <tr>
              <td width="10">&nbsp;</td>
              <td align="right"></td>
              <td></td>
              <td width="10">&nbsp;</td>
            </tr>
          </table>
        <!--END OF INFORMATION TABLE-->
      </td>
    </tr>
  </table>
<!--END OF MAIN TABLE--->
  ' . $time_summary . '
</form>';

require(DIR_PATH."includes/footer.inc.php");
?>