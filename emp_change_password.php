<?php
/******************************************************************************
**  File Name: emp_change_password.php
**  Description: Allows the employee to change their password
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
session_start();//start the session
define("DIR_PATH", "");//you must change the path for each sub folder

//SET PAGE VARIABLES
$current_pw = $_POST["current_pw"];
$new_pw = $_POST["new_pw"];
$confirm_pw = $_POST["confirm_pw"];
$reset_pw = $_POST["reset_pw"];
//SET DEPARTMENT AND SUB DEPT
$DeptID = $_SESSION["ses_dept_id"];
$employee_sub_dept = $_SESSION["ses_sub_dept_id"];
//CONCATENATE FIRST AND LAST NAME
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];

require(DIR_PATH."includes/db_info.inc.php");
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year          

//IF THE USER SUCCESFULLY LOGGED IN DISPLAY THEIR INFORMATION.
if ($_SESSION["ses_first_name"]) {

    //IF THE USER SUBMITS THE PASSWORD FORM
	if($reset_pw == "Reset Password"){

		//MAKE SURE THE CURRENT PASSWORD IS CORRECT
        $password_sql = "SELECT password FROM vf_employee WHERE emp_id = '$_SESSION[ses_emp_id]'";
		$password_qry = @mysql_query($password_sql);
        $password_result = @mysql_fetch_array($password_qry);
		   $database_pw = $password_result["password"];

		//ENCRYPT THE USER ENTERED PASSWORD
		$cur_pass =  md5(trim($current_pw));

        if($cur_pass != $database_pw){
            $message = urlencode ("The current password is not valid for making this change.");
			header ("Location: emp_change_password.php?error_message=$message");
            exit();
		}

        //CHECK MINIMUM PASSWORD LENGTH
     	if(strlen($new_pw) < 4){
            $message = urlencode ("The new password must be at least 4 characters in length.");
			header ("Location: emp_change_password.php?error_message=$message");
            exit();
		}
        //MAKE SURE NEW PASS AND CONFIRM MATCH.
     	if($new_pw != $confirm_pw){
            $message = urlencode ("Your confirmation password does not match your new password.");
			header ("Location: emp_change_password.php?error_message=$message");
            exit();
		}
        //ENCRYPT THE NEW PASSWORD
        $new_encript_pw = md5(trim($new_pw));
        //IF ALL CHECKS PASS. ENTER THE NEW PASSWORD
        $pw_update = @mysql_query("UPDATE vf_employee SET password = '$new_encript_pw' WHERE
        				emp_id = '$_SESSION[ses_emp_id]'") or die("Can't update the
                        password contact the site administrator");

        //LET THE USER KNOW THE UPDATE WAS SUCCESSFUL
	    $good_message = "Password was sucessfully changed. Please remember your new password.";

	}

$hdr_detail = 'Change Password For<br />'.$user_name;
$cur_page_title = "Change password";
require(DIR_PATH."includes/header.inc.php");	
?>
 	
		<form method="POST" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>">
		  <table border="0" cellpadding="0" cellspacing="0" width="700">
			<tr>
		      <td width="700" colspan="2">&nbsp;</td>
		    </tr>
		    <tr>
		      <td width="700" colspan="2">Enter your current password below. Then enter
		        the new password you wish to use. (Passwords must be between 4 and 16
		        characters in length.)<br /><b>You must log out and then back in for all
                things on the site to work correctly after making the change.</b></td>
		    </tr>
		    <tr>
		      <td width="350">&nbsp;</td>
		      <td width="350"></td>
		    </tr>
		    <tr>
		      <td width="350" align="right">Current password:&nbsp;&nbsp; </td>
			      <td width="350" align="left"><input type="password" name="current_pw" size="27" maxlength="16"></td>
		    </tr>
		    <tr>
		      <td width="350">&nbsp;</td>
		      <td width="350"></td>
		    </tr>
		    <tr>
		      <td width="350" align="right">New password:&nbsp;&nbsp; </td>
		      <td width="350"><input type="password" name="new_pw" size="27" maxlength="16"></td>
		    </tr>
		    <tr>
		      <td width="350" align="right">Confirm password:&nbsp;&nbsp; </td>
		      <td width="350"><input type="password" name="confirm_pw" size="27" maxlength="16"></td>
		    </tr>
		    <tr>
		      <td colspan="2">
		        &nbsp;</td>
		    </tr>
		    <tr>
		      <td colspan="2"align="center"><input type="submit" value="Reset Password" name="reset_pw"></td>
		    </tr>
		  </table>
		</form>
		<p>&nbsp;</p>
	<?PHP require(DIR_PATH."includes/footer.inc.php");
}
else
{
	header("location: index.php");
}	
?>