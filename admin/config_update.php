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


//IF THE EMPLOYEE ISN'T AN ADMIN. STOP THE USER
if($_SESSION["ses_is_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

//THE  END DATE CAN NOT BE EMPTY
if($edate == ""){
	$message[] = "The End Date cannot be empty. If you want to allow vacations to be added
	well into the future select a date that is a long way into the future.";
     $error_on_page = 1;
}	
	
//VALIDATE THE ADVANCED NOTICE FIELD
$ValidChar = (preg_match("/^[0-9]+$/i",$notice));
if($ValidChar == 0){
	$message[] = "Incorrect entry in the <b>Advanced Notice</b> field. You entered \"$notice\" ".
        	"Only numbers are allowed. If you do not want a value enter 0. Please reenter all " .
            "configuration changes you tryed to make to this page.";
     $error_on_page = 1;
}

//VALIDATE THE DEFAULT RESTRICTIONS FIELD
$ValidChar = (preg_match("/^[0-9]+$/i",$restrict));
if($ValidChar == 0){
	$message[] = "Incorrect entry in the <b>Default Restrictions</b> field. You entered \"$restrict\" " .
        	"Only numbers are allowed. If you do not want to restrict vacations enter a high value. Please reenter all " .
            "configuration changes you tryed to make to this page.";
     $error_on_page = 1;            
}

//VALIDATE THE DEFAULT RESTRICTIONS FIELD
$ValidChar = (preg_match("/^[0-9]+$/i",$minimum_hours));
if($ValidChar == 0){
	$message[] = "Incorrect entry in the <b>Minimum Hours</b> field. You entered \"$minimum_hours\" " .
        	"Only numbers are allowed. If you do not want to restrict vacations enter a zero Please reenter all " .
            "configuration changes you tryed to make to this page.";
     $error_on_page = 1;            
}

if($message){
	$error_msg = "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
    	occurred:</b></font><br /><font color=\"red\">\n";
        $numeric_text = 1;
        foreach($message as $key => $value){
        	$error_msg .= "$numeric_text. $value <br />\n";
            $numeric_text = $numeric_text + 1;
        }
	$error_msg .= "</font><br />";
        unset($numeric_text);
}else{
	//SEND A MESSAGE THROUGH THE URL TO LET THE USER KNOW THE CONFIGURATION WAS UPDATED
	$good_message = "Configuration updated!<br />&nbsp;<br />Some changes will not be seen until your next login.<br />It is recommended that you log out and back in now.";	
}

if($error_on_page == 0){
	//CONVERT THE VARIABLE TO SOMETHING MEANINGFUL TO ADD TO THE TABLE
	if($trackwithdefault == "ON"){
		$trackwithdefault = 1;
	}else{
		$trackwithdefault = 0;
	}

	if($Update_Configuration){
	    //UPDATE THE INFORMATION
		$db_update = @mysql_query("UPDATE vf_config SET " .
	 		   "people_off_type = '$track'," .
			   "last_vac_date = '$edate'," .
	           "days_notice = '$notice'," .
			   "min_hours_per_day = '$minimum_hours',".
	           "emp_off_ttl = '$restrict',".
	           "include_default = '$trackwithdefault',".
	           "email = '$contact_email' WHERE dept_id = '$_SESSION[ses_dept_id]'" .
	           "AND sub_dept_id = '$sub_dept_drpdown'")
	           or die("Can't Update information");
	}
	
	if($Add_Configuration){
	    //ADD THE INFORMATION
		$db_add = @mysql_query("INSERT INTO vf_config SET " .
	 		   "people_off_type = '$track'," .
			   "last_vac_date = '$edate'," .
	           "days_notice = '$notice'," .
	           "emp_off_ttl = '$restrict',".
			   "min_hours_per_day = '$minimum_hours',".
	           "sub_dept_id = '$sub_dept_drpdown',".
				"email = '$contact_email',".
	           "dept_id = '$_SESSION[ses_dept_id]'")
	           or die("Can't Update information");
	}
}

?>