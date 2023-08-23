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
if($_SESSION["ses_super_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

//SET PAGE VARIABLES
$selected_emp_id = $_POST["selected_emp_id"];
$new_emp_id = $_POST["new_emp_id"];
if($new_emp_id == "")
	$new_emp_id = $selected_emp_id;

$new_emp = $_POST["new_emp"];
$new_fname = $_POST["new_fname"];
$new_mname = $_POST["new_mname"];
$new_lname = $_POST["new_lname"];
$new_email = $_POST["new_email"];
$new_login = $_POST["new_login"];
$user_pass = $_POST["user_pass"];
$emp_pass = $_POST["emp_pass"];
$emp_pass2 = $_POST["emp_pass2"];
$new_status = $_POST["new_status"];
$new_dept = $_POST["new_dept"];
$new_sup = $_POST["new_sup"];
$new_issup = $_POST["new_issup"];
$new_view_vac =  $_POST["new_view_vac"];
$new_admin = $_POST["new_admin"];
$new_super_admin = $_POST["new_super_admin"];
$update_employee = $_POST["update_employee"];
$old_emp_id = $_POST["old_emp_id"];


//IF THE USER TRYS TO GET DIRECTLY TO THIS PAGE STOP THEM
if($new_emp_id == ""){
   echo "You can't access this page directly.";
}

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

//IF NO EMPLOYEE WAS SELECTED RETURN THEM TO THE SELECTION PAGE
if($new_emp_id == "Select an employee"){
   	$message = urlencode('<span class="Errorhdr">You have not selected an employee:</span><br /> Please try again.<br />');
    header ("Location: employee_edit_step1.php?error_message=$message");
}

//IF FORM IS SUBMITTED CHECK FOR ERRORS AND ADD EMPLOYEE
if(isset($update_employee)){

	//VARIABLE THAT WILL BE USED TO SEE IF THERE ARE ANY ERRORS AFTER THE FORM IS SUBMITTED
	$check_form = 0;

   /****************************************************************************
   **            --     VALIDATE THE FORM      --                             **
   ****************************************************************************/
   //VALIDATE THE EMPLOYEE ID. NUMBERS ONLY.
   $ValidChar = (preg_match("/^[0-9]+$/i",$new_emp_id));
   if($ValidChar == 0){
        $message[] = "The Employee ID can only be numeric values. " .
        	"Only numbers are allowed.";
		$check_form = 1;
	}

   //VALIDATE THE FIRST NAME.
   $ValidChar = (preg_match("/^[a-zA-Z ]+$/i",$new_fname));
   if($ValidChar == 0){
        $message[] = "The Employee First Name can only be alpha characters,<br />
        and can not be empty.";
		$check_form = 1;
	}

   //VALIDATE THE MIDDLE NAME.
   if(strlen($new_mname) != 0){
	   $ValidChar = (preg_match("/^[a-zA-Z ]+$/i",$new_mname));
	   if($ValidChar == 0){
	        $message[] = "The Employee Middle Name can only be alpha characters.";
			$check_form = 1;
		}
   }

   //VALIDATE THE LAST NAME.
   $ValidChar = (preg_match("/^[a-zA-Z ]+$/i",$new_lname));
   if($ValidChar == 0){
        $message[] = "The Employee Last Name can only be alpha characters,<br />
        and can not be empty.";
		$check_form = 1;
	}

   //VALIDATE PART OF EMAIL ADDRESS
   $validate_at = strchr ($new_email, "@");
   if($new_email != ""){
	   if($validate_at == ""){
	        $message[] = "The Employee email address needs an '@' symbol.";
			$check_form = 1;   
	   }
   }
   	
   //ONLY ALLOW ALPHA-NUMERIC NUMBERS IN THE USER LOGIN FIELD
   $ValidChar = (preg_match("/^[a-zA-Z0-9 ]+$/i",$new_login));
   if($ValidChar == 0){
        $message[] = "You must enter a Login Name for the employee to start using the vacation scheduler
        				and must be alpha-numeric values only.";
		$check_form = 1;
	}

   //MAKE SURE THE CONFIRMATION IS THE SAME AS THE PASSWORD
   if($emp_pass != $emp_pass2){
        $message[] = "The password and confirmation do not match.";
		$check_form = 1;
   }

   //PASSWORD MUST BE AT LEAST 4 CHARACTERS LONG
   if(strlen($emp_pass) < 4 || strlen($emp_pass) > 16){
        $message[] = "The password must be at least 4 characters in length and no longer than 16.";
		$check_form = 1;
	}

   //PASSWORD MUST BE AT LEAST 4 CHARACTERS LONG
   if(strlen($emp_pass2) < 4){
        $message[] = "The password confirmation must be at least 4 characters in length.";
		$check_form = 1;
	}

    //THEY MUST SELECT A STATUS
    if($new_status == "Select employee category"){
        $message[] = "You must select a status type.";
		$check_form = 1;
    }

    //THEY MUST SELECT A DEPARTMENT
    if($new_dept == "Select a Department"){
        $message[] = "You must select a department.";
		$check_form = 1;
    }

    //THEY MUST SELECT A SUPERVISOR OR NA
    if($new_sup == "Select a Supervisor"){
        $message[] = "You must select a Supervisor or NA if the employee is a manager.";
		$check_form = 1;
    }

    /***************** END OF FORM VALIDATION *********************************/

    //ECHO MESSAGE BACK TO THE USER IF THERE WERE ANY ERRORS ON THE FORM OTHERWISE
    //INSERT THE INFROMATION TO THE DATABASE
	if($message){
    	$error_message = "<span class=\"Errorhdr\">The following problems occurred:</span><br />\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$error_message .= "$numeric_text. $value <br />\n";
	            $numeric_text = $numeric_text + 1;
            }
		$error_message .= "<br />";
            unset($numeric_text);
            
        //IF THERE WERE ERRORS CHANGE THE USER ID BACK TO THE OLD ONE TO REFRESH
        //THE PAGE
        $new_emp_id = $old_emp_id;

	}else{
    /********* IF NO ERRORS ENTER THE DATA INTO THE EMPLOYEE TABLE ************/
		//CONVERT THE VARIABLE TO A NUMERIC VALUE
		if($new_admin == "ON"){
        	$new_admin = 1;
		}else{
        	$new_admin = 0;
        }

		//CONVERT THE VARIABLE TO A NUMERIC VALUE
  		if($new_super_admin == "ON"){
        	$new_super_admin = 1;
		}else{
        	$new_super_admin = 0;
        }

		//CONVERT THE VARIABLE TO A NUMERIC VALUE
  		if($new_view_vac == "ON"){
        	$new_view_vac = 1;
		}else{
        	$new_view_vac = 0;
        }
                
        //IF NOT APPLICABLE ENTER A ZERO FOR SUPERVISOR
        if($new_sup == "NA"){
        	$new_sup = 0;
        }

		//CONVERT PASSWORD FOR THE PROPER USE
        if($emp_pass == "Password is set" || $emp_pass == "Enter a password" ||$emp_pass == ""){
			$user_pass = $user_pass;
        }else{
	        //ENCRYPT THE PASSWORD
    	    $user_pass =  md5($emp_pass);
		}

        //UPDATE VALUES IN THE EMPLOYEE DATABASE
        $result = @mysql_query("UPDATE vf_employee SET
			emp_id = '$new_emp_id',
            fname = '$new_fname',
            mname = '$new_mname',
            lname = '$new_lname',
            username = '$new_login',
        	email = '$new_email',
            password = '$user_pass',
            status = '$new_status',
            dept_id = '$new_dept',
            admin = '$new_admin',
            super_admin = '$new_super_admin',
            sup_id = '$new_sup',
            supervisor = '$new_issup',
        	viewdeptvac = '$new_view_vac'
			WHERE emp_id = '$old_emp_id'")
            or die ("Unable to update employee");
            
		//LET THE USER KNOW THE EMPLOYEE WAS ADDED
    	$message = urlencode("<b>The following Employee was updated: " .
			"</b><br /> $new_fname $new_lname <br /></font><br />");
		header ("Location: employee_edit_step1.php?error_message=$message");
		exit;

        //UNSET ALL THE VARIABLES
/*		unset($new_emp_id);
        unset($new_fname);
        unset($new_mname);
        unset($new_lname);
        unset($new_email);        
        unset($new_login);
		unset($new_pass);
		unset($new_dept);
        unset($new_sup);
        unset($new_admin);
        unset($new_super_admin);
        unset($new_issup);
*/
	}
}



//GET THE CURRENT INFORMATION FROM THE DATABASE
$get_emp_inf = mysql_query("SELECT * FROM vf_employee WHERE emp_id = '$new_emp_id'")
			or die ("Unable to retrieve employee information.");
$emp_inf_array = mysql_fetch_array($get_emp_inf);
	$new_emp_id = $emp_inf_array["emp_id"];
	if(!isset($new_fname)){
    	$new_fname = $emp_inf_array["fname"];
	}
	if(!isset($new_mname)){	
    	$new_mname = $emp_inf_array["mname"];
	}
	if(!isset($new_lname)){
    	$new_lname = $emp_inf_array["lname"];
	}
	if(!isset($new_email)){
    	$new_email = $emp_inf_array["email"];
	}
	if(!isset($new_login)){
    	$new_login = $emp_inf_array["username"];
	}
	if(!isset($emp_pass)){
		$emp_pass = $emp_inf_array["password"];
		$emp_pass2 = $emp_inf_array["password"];
		$user_pass = $emp_inf_array["password"];
	}
    //IF THERE IS A PASSWORD. LET THE USER KNOW
    if($emp_pass != ""){
    	$emp_pass = "Password is set";
    	$emp_pass2 = "Password is set";
	}
	if(!isset($new_status)){
		$new_status = $emp_inf_array["status"];
	}
	if(!isset($new_dept)){	
		$new_dept = $emp_inf_array["dept_id"];
	}
	if(!isset($new_sup)){	
    	$new_sup = $emp_inf_array["sup_id"];	
	}    	
	if(!isset($new_issup)){		
    	$new_issup = $emp_inf_array["supervisor"];	
	}
	if(!isset($new_view_vac)){		
    	$new_view_vac = $emp_inf_array["viewdeptvac"];	
	}
	if(!isset($new_admin)){			
    	$new_admin = $emp_inf_array["admin"];
	}
	if(!isset($new_super_admin)){			
    	$new_super_admin = $emp_inf_array["super_admin"];
	}
    //SET VARIABLE TO BE USED IN CASE THE ID IS CHANGED
	$old_emp_id = $new_emp_id;
    if($new_admin == 1){
    	$new_admin = "ON";
    }
    if($new_super_admin == 1){
    	$new_super_admin = "ON";
    }
    if($new_view_vac == 1){
    	$new_view_vac = "ON";
    }

//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
if($_SESSION["ses_super_admin"] == 1){

	/************ BUILD A DROP DOWN LIST OF DEPARTMENTS ****************/
	//LOOK UP ALL DEPARTMENTS
	$get_dept = @mysql_query("SELECT * FROM vf_department ORDER BY descr ASC");

	$dept_box = "<select size=\"1\" name=\"new_dept\"><option>Select a Department</option>";
	while($dept_array = @mysql_fetch_array($get_dept)){
	        $new_deptid = $dept_array["dept_id"];
	        $new_descr = $dept_array["descr"];

            if($new_deptid == $new_dept){
            	$select = "selected";
			}

			$dept_box .= "<option $select value=\"$new_deptid\">$new_descr</option>\n";

            $select = "";
	}
	$dept_box .= "</select>";

	/************ END OF BUILD A DROP DOWN LIST OF DEPARTMENTS ****************/

   	/************ BUILD A DROP DOWN LIST OF SUPERVISORS ****************/
	//LOOK UP ALL DEPARTMENTS
	$get_sups = @mysql_query("SELECT * FROM vf_employee WHERE supervisor = 1 OR admin = 1 ORDER  BY lname, fname ASC ");

	$sup_box = "<select size=\"1\" name=\"new_sup\"><option>Select a Supervisor</option> \n";

    if($new_sup == 0){
		$select = "selected";
    }

    $sup_box .= "<option $select >NA</option>";
    $select = "";

	while($sup_array = @mysql_fetch_array($get_sups)){
	        $sup_id = $sup_array["emp_id"];
	        $sup_fname = $sup_array["fname"];
	        $sup_lname = $sup_array["lname"];
            $sup_name = $sup_lname . ", " . $sup_fname;

            if($sup_id == $new_sup){
            	$select = "selected";
			}

			$sup_box .= "<option $select value=\"$sup_id\">$sup_name</option>\n";

            $select = "";
	}
	$sup_box .= "</select>";

	/************ END OF BUILD A DROP DOWN LIST OF SUPERVISORS ****************/

	/************ BUILD A DROP DOWN LIST OF EMPLOYEE STATUS ****************/
	$status_box = '<select size="1" name="new_status">
					<option>Select employee category</option>';
	
	$cat_sql = @mysql_query("SELECT `cat_id`,`descr` FROM `vf_category`");
	while($cat_result = @mysql_fetch_array($cat_sql)){
		$cat_id = $cat_result["cat_id"];
		$cat_desc = $cat_result["descr"];
	
		if($new_status == $cat_id){
			$selected = "selected";
		}else{
			$selected = "";		
		}
		$status_box .='<option value="'.$cat_id.'" '.$selected.'>'.$cat_desc.'</option>';
	}	
	$status_box .= "</select>";	
	/************ END OF BUILD A DROP DOWN LIST OF EMPLOYEE STATUS ****************/

	/************ BUILD A DROP DOWN LIST OF SUPERVISOR TYPES ****************/
	$sup_type = '<select size="1" name="new_issup">';
       if($new_issup == "0"){
			$select = "selected";
	   }
       $sup_type .= '<option value="0" '.$select.'>Not a supervisor</option>';
       $select = "";

       if($new_issup == "1"){
			$select = "selected";
	   }
       $sup_type .= '<option value="1" '.$select.'>Supervisor</option>';
       $select = "";

       if($new_issup == "2"){
			$select = "selected";
	   }
       $sup_type .= '<option value="2" '.$select.'>Supervisor (Self administration)</option>';
       $select = "";

	$sup_type .= "</select>";

	/************ END OF BUILD A DROP DOWN LIST OF SUPERVISOR TYPES ****************/	
	
$hdr_addin ='	
	<SCRIPT LANGUAGE="JavaScript">
	    function switchAll() {
			if(document.new_emp.new_admin.checked == false) {
				document.new_emp.new_admin.checked=true;
			  }
		}
	    function switchback() {
			if(document.new_emp.new_admin.checked == false) {
				document.new_emp.new_super_admin.checked=false;
			  }
		}
	</script>';
	
$cur_page_title = "Edit Employee";
require(DIR_PATH."includes/header.inc.php");
?>	
	
<form name="new_emp" method="POST" action="<?PHP echo $PHP_SELF; ?>">
  <table border="0" cellpadding="0" cellspacing="2" width="700">
    <tr>
		<td width="700" colspan="2"><a href="employee_edit_step1.php">Select a different
          employee</a></td>
    </tr>
    <tr>
	  <td width="300">&nbsp;</td>
      <td width="400"></td>
    </tr>
    <tr>
	  <td width="300" class="emptable">Employee ID Number:</td>
      <td width="400">&nbsp;<input type="text" readonly name="new_emp_id" size="11" value="<?PHP echo $new_emp_id; ?>"></td>
    </tr>
    <tr>
	  <td width="300" class="emptable">First Name:</td>
      <td width="400">&nbsp;<input type="text" name="new_fname" size="20" value="<?PHP echo $new_fname; ?>"></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Middle Name:</td>
	  <td width="400">&nbsp;<input type="text" name="new_mname" size="20" value="<?PHP echo $new_mname; ?>"></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Last Name:</td>
      <td width="400">&nbsp;<input type="text" name="new_lname" size="20" value="<?PHP echo $new_lname; ?>"></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Email Address:</td>
      <td width="400">&nbsp;<input type="text" name="new_email" size="35" value="<?PHP echo $new_email; ?>"></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Login Name:</td>
      <td width="400">&nbsp;<input type="text" name="new_login" size="20" value="<?PHP echo $new_login; ?>"></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Password:</td>
      <td width="400"><input type="hidden" value="<?PHP echo $user_pass;?>" name="user_pass">
      &nbsp;<input type="password" name="emp_pass" size="35" value="<?PHP echo $emp_pass; ?>"></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Password Confirmation:</td>
      <td width="400">&nbsp;<input type="password" name="emp_pass2" size="35" value="<?PHP echo $emp_pass2; ?>"></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Category:</td>
      <td width="400">&nbsp;<?PHP echo $status_box; ?></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Department:</td>
      <td width="400">&nbsp;<?PHP echo $dept_box; ?></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Employee Supervisor:</td>
      <td width="400">&nbsp;<?PHP echo $sup_box; ?></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Select supervisor type:</td>
      <td width="400">&nbsp;<?PHP echo $sup_type; ?></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Check if employee is a department administrator:</td>
      <td width="400">&nbsp;<input type="checkbox" onclick="switchback()"name="new_admin" value="ON"
	  <?PHP
      if($new_admin == "ON"){
      	echo " checked ";
      }
      ?>
      >
      </td>
    </tr>
    <tr>
      <td width="300" class="emptable">Check if employee is a site administrator (must also be a department admin):</td>
      <td width="400">&nbsp;<input type="checkbox" onclick="switchAll()" name="new_super_admin" value="ON" 
	  <?PHP 
      if($new_super_admin == "ON"){
      	echo " checked ";
      }
      ?>
	  >
      </td>
    </tr>
    <tr>
      <td width="300" class="emptable">Check if employee can view department vacations:</td>
      <td width="400">&nbsp;<input type="checkbox" name="new_view_vac" value="ON"
	  <?PHP
      if($new_view_vac == "ON"){
      	echo " checked ";
      }
      ?>
	 >
     </td>
    </tr>		
    <tr>
      <td width="300">&nbsp;</td>
      <td width="400"></td>
    </tr>
    <tr>
      <td width="300">&nbsp;</td>
      <td width="400"></td>
    </tr>
    <tr>
      <td width="300">&nbsp;</td>
      <td width="400">
      	<input type="hidden" value="<?PHP echo $old_emp_id; ?>" name="old_emp_id">
      </td>
    </tr>
    <tr>
      <td width="300">&nbsp;</td>
      <td width="400"><input type="submit" value="Update Employee Information" name="update_employee"></td>
    </tr>
  </table>
</form>
<?PHP
	require(DIR_PATH."includes/footer.inc.php");

}else{
	$content = 'At this time, only Human Resourses can make changes to employees.';
}

?>