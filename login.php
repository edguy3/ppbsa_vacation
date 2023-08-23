<?php
/******************************************************************************
**  File Name: login.php
**  Description: Logs user in and sets session variables.
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
require(DIR_PATH."includes/db_info.inc.php");//database connection information

//SET PAGE VARIABLES
$username = $_POST["username"];
$password = $_POST["password"];


if((ereg ("^[[:alnum:]]+$", $_POST["username"])) AND (eregi  ("^[[:alnum:]]{4,16}$", $_POST["password"])) ) { // Check the submitted info.
	$query = "SELECT * FROM `vf_employee` WHERE username='".$_POST["username"]."'";
	$query_result = @mysql_query ($query, $db_connection) or die ("Can't retieve information.");
	$result = @mysql_fetch_array ($query_result);
	$password =  md5($password);

	if($password == $result[password]) {
		$_SESSION["ses_emp_id"] = $result["emp_id"]; //users employee id number
		$_SESSION["ses_first_name"] = $result["fname"]; //users first name
        $_SESSION["ses_middle_name"] = $result["mname"]; //users middle name
        $_SESSION["ses_last_name"] = $result["lname"]; //users last name
        $_SESSION["ses_dept_id"] = $result["dept_id"]; //id of the users department
        $_SESSION["ses_sub_dept_id"] = $result["sub_dept_id"]; //id of the users subdepartment if used
        $_SESSION["ses_is_admin"] = $result["admin"]; //on if the user can edit the people in the department they oversee
        $_SESSION["ses_super_admin"] = $result["super_admin"]; //on if the user has complete site administration capability
        $_SESSION["ses_sup_id"] = $result["sup_id"]; //the id of the users supervisor or N/A
        $_SESSION["ses_eligible"] = $result["vacation_eligible"]; //on if the user is eligible for vacation time 
        $_SESSION["ses_is_sup"] = $result["supervisor"]; //on if the user is a supervisor
        $_SESSION["ses_email"] = $result["email"]; //employees email that will recieve the approval notice
        $_SESSION["ses_view_vac"] = $result["viewdeptvac"]; //on if the user can view other vacations for their department
		
        //set variable for sucessful login
        //$ses_login_success = 'Y';
		$_SESSION["ses_login_success"] = 'Y';
		
	    //RETRIEVE THE USERS DEPARTMENT NAME
    	$dept_sql = @mysql_query("SELECT descr FROM vf_department WHERE dept_id = '".$result["dept_id"]."'");
    	$dept_array = @mysql_fetch_array($dept_sql);
			$_SESSION["ses_dept_name"] = $dept_array["descr"]; //name of the users department
			
		//RETRIEVE CURRENT VALUES FROM THE CONFIGURATION TABLE FOR THE USER
		$conf_sql = @mysql_query("SELECT * FROM vf_config WHERE dept_id = '".$result["dept_id"]."' AND " .
						" sub_dept_id = '".$result["sub_dept_id"]."'");
		$config_array = @mysql_fetch_array($conf_sql);
			$_SESSION["ses_employeesoff"] = $config_array["emp_off_ttl"];//number of users who can take vacation on the same day
		    $_SESSION["ses_prenotice"] = $config_array["days_notice"];//how many days notice must the employee give to request time off
		    $_SESSION["ses_offtype"] = $config_array["people_off_type"];//how are the peoples vacations tracked
		    $_SESSION["ses_lastdate"] = $config_array["last_vac_date"];//the last date that a user can request off. Used to allow only one year at a time to be scheduled
		    $_SESSION["ses_minimum_hours"] = $config_array["min_hours_per_day"];//what is the least amount of hours per day the user can request
		    $_SESSION["ses_contact_email"] = $config_array["email"];//who should vaction requests be sent to
		    $_SESSION["ses_include_default_dept"] = $config_array["include_default"];//used to determine if vactions checks should be used across the whole department or just the sub department
			    
//        if($ses_is_admin == "1"){
//        	header ("Location: admin/admin.php");//If the user is an administrator show the administrration page.
//		}else{
        	header ("Location: index.php");//If a regular user logs in. Show their home page.
//        }      
		exit;
	}else{
		//send an error message back if the login is incorrect
		$message = urlencode ("The username and password submitted do not match those on file. Please try again.");
	}
} else {
	//if the page was submitted with an emoty field. redisplay with an error
	$message = urlencode ("Please enter your username and password to login.");
}

//resubmit the same page if there was an error and the user was not redirected
header ("Location: index.php?error_message=$message");
exit;
?>