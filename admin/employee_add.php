<?PHP
/******************************************************************************
**  Description:
**  Written By: Gary Barber
**  
*******************************************************************************
**********************  LAST MODIFIED  ****************************************
**
**  Date: 3/25/05
**  Programmer: Gary Barber
**  Notes: Add a chackbox to determine if the employee has rights to view vacations
**  for their department.
**  ====================
**
**
**
**
**
**
******************************************************************************/
session_start();
define("DIR_PATH", "../");//you must change the path for each sub folder

//IF THE EMPLOYEE ISN'T AN ADMIN. STOP THE USER
if($_SESSION["ses_super_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

//SET PAGE VARIABLES
$new_emp_id = $_POST["new_emp_id"];
$new_fname = $_POST["new_fname"];
$new_mname = $_POST["new_mname"];
$new_lname = $_POST["new_lname"];
$new_email = $_POST["new_email"];
$new_login = $_POST["new_login"];
$new_pass = $_POST["new_pass"];
$new_pass2 = $_POST["new_pass2"];
$new_status = $_POST["new_status"];
$new_dept = $_POST["new_dept"];
$new_sup = $_POST["new_sup"];
$new_admin = $_POST["new_admin"];
$new_super_admin = $_POST["new_super_admin"];
$new_issup = $_POST["new_issup"];
$view_vac =  $_POST["view_vac"];
$add_employee = $_POST["add_employee"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year


//IF FORM IS SUBMITTED CHECK FOR ERRORS AND ADD EMPLOYEE
if(isset($add_employee)){

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
   
   //VALIDATE PART OF EMAIL ADDRESS
   $validate_at = strchr ($new_email, "@");
   if($new_email != ""){
	   if($validate_at == ""){
	        $message[] = "The Employee email address needs an '@' symbol.";
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

   //ONLY ALLOW ALPHA-NUMERIC NUMBERS IN THE USER LOGIN FIELD
   $ValidChar = (preg_match("/^[a-zA-Z0-9 ]+$/i",$new_login));
   if($ValidChar == 0){
        $message[] = "You must enter a Login Name for the employee to start using the vacation scheduler
        				and must be alpha-numeric values only.";
		$check_form = 1;
	}

   //ONLY ALLOW ALPHA-NUMERIC NUMBERS IN THE PASSWORD FIELD
   $ValidChar = (preg_match("/^[a-zA-Z0-9 ]+$/i",$new_pass));
   if($ValidChar == 0){
        $message[] = "You must enter a password for the employee to start using the vacation scheduler
        				and must be alpha-numeric values only.";
		$check_form = 1;
	}

    //PASSWORD MUST BE BETWEEN 4 AND 16 CHARACTERS
	if(strlen($new_pass) < 4 || strlen($new_pass) > 16){
        $message[] = "Please enter a password that consists only of letters and numbers,<br />
        between 4 and 16 characters long.";
		$check_form = 1;
	}

	if($new_pass != $new_pass2){
        $message[] = "The Password and Confirmation do not match.";
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

    //MAKE SURE THE EMPLOYEE NUMBER IS NOT ALREADY BEING USED
	$ck_val_sql = "SELECT * FROM vf_employee WHERE
           emp_id = '$new_emp_id'";
	// Execute the query and put results in $result
	$ck_val_result = mysql_query($ck_val_sql)
	    or die ( 'Unable to check employee number.' );
	// Get number of rows in $result.
	$num = mysql_numrows($ck_val_result);
	if ( $num != 0 ) {
      	$message[] = "The employee number is already being used. You may not
           	use the employee number.";
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
  		if($view_vac == "ON"){
        	$view_vac = 1;
		}else{
        	$view_vac = 0;
        }
                
        //ENCRYPT THE PASSWORD
        $new_pass =  md5($new_pass);

        //INSERT VALUES INTO THE EMPLOYEE DATABASE
        $result = @mysql_query("INSERT INTO vf_employee
        	(emp_id,fname,mname,lname,email,username,password,status,dept_id,admin,super_admin,
            sup_id,supervisor,viewdeptvac,year)
        	values
        	('$new_emp_id','$new_fname','$new_mname','$new_lname','$new_email','$new_login','$new_pass','$new_status','$new_dept',
            $new_admin,$new_super_admin,'$new_sup',$new_issup,$view_vac,'')")or die ("Unable to add employee");

		//LET THE USER KNOW THE EMPLOYEE WAS ADDED
    	$good_message = "The following Employee was added: \n $new_fname $new_lname <br />";

        //UNSET ALL THE VARIABLES
		unset($new_emp_id);
        unset($new_fname);
        unset($new_mname);
        unset($new_lname);
        unset($new_email);        
        unset($new_login);
		unset($new_pass);
        unset($new_pass2);
        unset($new_status);
		unset($new_dept);
        unset($new_sup);
        unset($new_admin);
        unset($new_super_admin);
        unset($new_issup);
        unset($view_vac);
	}
}



//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
if($_SESSION["ses_super_admin"] == 1){


	/************ BUILD A DROP DOWN LIST OF DEPARTMENTS ****************/
	//LOOK UP ALL DEPARTMENTS
	$get_dept = @mysql_query("SELECT * FROM vf_department ORDER BY descr ASC");

	$dept_box = '<select size="1" name="new_dept">
				<option>Select a Department</option>';
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

	$sup_box = '<select size="1" name="new_sup">';
			
		if($new_sup == "NA")
					$select = "selected";
					
					
		$sup_box .= "<option>Select a Supervisor</option>
					 \n<option $select>NA</option>";
		 $select = '';
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
				if($new_issup == "0"){$sup_select = "selected";}	
    $sup_type .= '<option value="0" '.$sup_select.'>Not a supervisor</option>';
    			$sup_select = "";
				if($new_issup == "1"){$sup_select = "selected";}	    
    $sup_type .= '<option value="1" '.$sup_select.'>Supervisor</option>';
    			$sup_select = "";    
				if($new_issup == "2"){$sup_select = "selected";}	    
    $sup_type .= '<option value="2" '.$sup_select.'>Supervisor (Self administration)</option>';
    			$sup_select = "";    
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

$cur_page_title = "Add Employee";
$itemcnt = 4; //Count of select boxes on the current page
require(DIR_PATH."includes/header.inc.php");
?>
<form name="new_emp" method="POST" action="<?PHP echo $PHP_SELF; ?>">
  <table border="0" cellpadding="0" cellspacing="2" width="700">
    <tr>
	  <td width="300" class="emptable">Employee ID Number:</td>
      <td width="400">&nbsp;<input type="text" name="new_emp_id" size="11" value="<?PHP echo $new_emp_id; ?>"></td>
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
	  <td width="300" class="emptable">Login Name:</td>
      <td width="400">&nbsp;<input type="text" name="new_login" size="20" value="<?PHP echo $new_login; ?>"></td>
    </tr>
    <tr>
	  <td width="300" class="emptable">Email Address:</td>
      <td width="400">&nbsp;<input type="text" name="new_email" size="35" value="<?PHP echo $new_email; ?>"></td>
    </tr>
    <tr>
      <td width="300" class="emptable">Initial Password:</td>
      <td width="400">&nbsp;<input type="password" name="new_pass" size="20" value="<?PHP echo $new_pass; ?>"></td>
    </tr>
    <tr>
	  <td width="300" class="emptable">Password Confirmation:</td>
      <td width="400">&nbsp;<input type="password" name="new_pass2" size="20" value="<?PHP echo $new_pass2; ?>"></td>
    </tr>
    <tr>
	  <td width="300" class="emptable">Category:&nbsp;</td>
      <td width="400">
      	<div id="HideItem0" style="POSITION:relative">
       	 <?PHP echo $status_box; ?>
        </div> 	 
      </td>
    </tr>
    <tr>
	  <td width="300" class="emptable">Department:&nbsp;</td>
      <td width="400">
      	<div id="HideItem1" style="POSITION:relative">     
         <?PHP echo $dept_box; ?>
       </div>   
      </td>
    </tr>
    <tr>
	  <td width="300" class="emptable">Employee Supervisor:&nbsp;</td>
      <td width="400">
      	<div id="HideItem2" style="POSITION:relative">
      	<?PHP echo $sup_box; ?>
      	</div>
      </td>
    </tr>
    <tr>
	  <td width="300" class="emptable">Select supervisor type:&nbsp;</td>
      <td width="400">
      	<div id="HideItem3" style="POSITION:relative">
      	 <?PHP echo $sup_type; ?>
      	</div> 
      </td>
    </tr>
    <tr>
	  <td width="300" class="emptable">Check if employee is a department administrator:</td>
      <td width="400">&nbsp;<input type="checkbox" onclick="switchback()" name="new_admin" value="ON"
      <?PHP
      if($new_admin == "ON"){
      	echo " checked ";
      }
      ?>	
      ></td>
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
      <td width="400">&nbsp;<input type="checkbox" name="view_vac" value="ON"
		<?PHP 
      if($view_vac == "ON"){
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
      <td width="400"></td>
    </tr>
    <tr>
      <td width="300">&nbsp;</td>
      <td width="400"><input type="submit" value="Add Employee" name="add_employee"></td>
    </tr>
  </table>
</form>
<?PHP
	require(DIR_PATH."includes/footer.inc.php");
}else{
	echo 'At this time, only Human Resourses can make changes to employees.';
}
?>