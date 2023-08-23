<?php
/******************************************************************************
**  File Name: emp_change_email.php
**  Description: Allows the user to change their email address
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

//SET DEPARTMENT AND SUB DEPT
$DeptID = $_SESSION["ses_dept_id"];
$employee_sub_dept = $_SESSION[ses_sub_dept_id];
//CONCATENATE FIRST AND LAST NAME
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];
//SET PAGE VARIABLES
$email_add = $_POST["email_add"];
$reset_email = $_POST["reset_email"];

require(DIR_PATH."includes/db_info.inc.php");
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year        

//IF THE USER SUCCESFULLY LOGGED IN DISPLAY THEIR INFORMATION.
if ($_SESSION["ses_first_name"]) {

    //IF THE USER SUBMITS THE PASSWORD FORM
	if($reset_email == "Update Address" || $email_add != ""){

		//VALIDATE PART OF EMAIL ADDRESS
		$validate_at = strchr ($email_add, "@");
		if($email_add != ""){
		   if($validate_at == ""){
		        $message = urlencode ("The Employee email address needs an @ symbol.");
				header ("Location: emp_change_email.php?error_message=$message");
            	exit();
		   }
		}
		
        //IF ALL CHECKS PASS. ENTER THE NEW PASSWORD
        $mail_update = @mysql_query("UPDATE `vf_employee` SET `email` = '$email_add' WHERE
        				emp_id = '$_SESSION[ses_emp_id]'") or die("Can't update the
                        email address. Contact the site administrator");

        //LET THE USER KNOW THE UPDATE WAS SUCCESSFUL
	    $good_message = "Your email address was sucessfully changed.<br />
	    			NOTICE: You will not see the change until your next login.";
	}

	
	//GET THE CURRENT EMAIL LISTING
    $email_sql = "SELECT `email` FROM `vf_employee` WHERE `emp_id` = '$_SESSION[ses_emp_id]'";
	$email_qry = @mysql_query($email_sql);
    $email_result = @mysql_fetch_array($email_qry);
	   $email_descr = $email_result["email"];

$hdr_detail = 'Change Email Address For<br />'.$user_name;
$cur_page_title = "Change email address";
require(DIR_PATH."includes/header.inc.php");	   
?>

		<form method="POST" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>">
		  <table border="0" cellpadding="0" cellspacing="0" width="700">
		    <tr>
		      <td width="700">Enter the email address you want replies to be sent when vacation is approved or denied. If there is 
		      no address the program will not send messages to you.</td>
		    </tr>
		    <tr>
		      <td width="700">&nbsp;</td>
		    </tr>		  
		    <tr>
		      <td width="700">&nbsp;</td>
		    </tr>
		    <tr>
		      <td  width="700" colspan="2" align="center">
		        <b>Email:&nbsp;&nbsp;</b><input type="text" name="email_add" value="<?PHP echo $email_descr; ?>" size="40">
		      </td>
		    </tr>
		    <tr>
		      <td width="700">&nbsp;</td>
		    </tr>
		    <tr>
		      <td width="700">&nbsp;</td>
		    </tr>
		    <tr>
		      <td width="700" align="center">
		        <input type="submit" value="Update Address" name="reset_email">
		      </td>
		    </tr>
		  </table>
		</form>
		<p>&nbsp;</p>
		<?PHP require(DIR_PATH."includes/footer.inc.php");?> 	
		
		
<?PHP 		
}
else
{
	header("location: index.php");
}	

?>