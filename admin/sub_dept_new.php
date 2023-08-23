<?PHP
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
define("DIR_PATH", "../");//you must change the path for each sub folder

//IF THE EMPLOYEE ISN'T AN ADMIN. STOP THE USER
if($_SESSION["ses_is_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

//SET PAGE VARIABLES
$DeptID = $_SESSION["ses_dept_id"];
$add_sub_dept = $_POST["add_sub_dept"];
$update_sub_dept = $_POST["update_sub_dept"];
$cancel = $_POST["cancel"];
$sub_dept_code = $_POST["sub_dept_code"];
$sub_dept_name = $_POST["sub_dept_name"];
$page_id = $_GET["page_id"];
$rmv_sub = $_GET["rmv_sub"];
$sub_dept_edit_descr = $_GET["sub_dept_edit_descr"];
$sub_edit = $_GET["sub_edit"];
$sub_editID = $_POST["sub_editID"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$cur_page_title = "Add Sub-Department";
require(DIR_PATH."includes/header.inc.php");


if(isset($cancel)){
	unset($page_id);	
}
//IF THE USER IS EDITING A CURRENT DESCRIPTION RUN THIS
if(isset($update_sub_dept)){
    $error_on_page = 0;

	if($sub_dept_code == ""){
	   	//SET ERROR VARIABLE TO ON
		$error_on_page = 1;
		$message[] = "You must enter a code for the Sub-Deprtment.";
	}

	if($sub_dept_name == ""){
	   	//SET ERROR VARIABLE TO ON
		$error_on_page = 1;
		$message[] = "You must enter a name for the Sub-Deprtment.";
	}

	//IF THERE WERE ANY ERRORS DON'T UPDATE THE TABLE AND LET THE USER KNOW
	if($message){
    	echo "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	echo "$numeric_text. $value ";
	            $numeric_text = $numeric_text + 1;
            }
		echo "</font><br />\n";
            unset($numeric_text);
    }

	/************************************************************************
	** START ENTERING INFORMATION INTO THE DATABASE IT THERE WERE NO ERRORS**
	************************************************************************/
	 if($error_on_page == 0){
		$run_update = @mysql_query("UPDATE `vf_sub_dept` SET
	  				`descr` = '$sub_dept_name', `code` = '$sub_dept_code'
                    WHERE `sub_dept_id` = '$sub_editID' AND `dept_id` = '$DeptID'
	    			")or die ("Can't update changes.");

		//LET THE USER KNOW THE EMPLOYEE WAS ADDED
    	echo "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The Sub-Department was changed to: " .
			"</b></font><br /><font color=\"red\">\n $sub_dept_name and the code was changed to $sub_dept_code<br />\n";
		echo "</font><br />";

        //UNSET ALL THE VARIABLES
		$sub_dept_name = "";
		unset($sub_dept_edit_code);
		unset($sub_dept_edit_descr);
        unset($page_id);
        unset($sub_edit);
	 }
}

//IF THE USER WANTS TO DELETE A SUB DEPARTMENT RUN THIS
if(isset($rmv_sub)){
   //IF THE USER TRIES TO DELETE A SUB_DEPT. MAKE SURE IT EVEN EXIST (PREVENT HACKING)
   $check_for_sub_dept = @mysql_query("SELECT * FROM vf_sub_dept WHERE
        	sub_dept_id = '$rmv_sub' AND dept_id = '$DeptID'");
   $field_count = @mysql_num_rows($check_for_sub_dept);
   //IF THE SUB_DEPT EXITS RUN THIS CODE
   if($field_count != 0){
       //MAKE SURE ALL REFERENCES TO THE SUB_DEPT HAVE BEEN REMOVED. IF NOT DON"T
       //DELETE THE SUB_DEPT
	   $check_quantity = @mysql_query("SELECT fname,lname FROM
    			 vf_employee WHERE sub_dept_id = '$rmv_sub' AND dept_id = '$DeptID'");
	    while($user_entry = @mysql_fetch_array($check_quantity)){
	    	$entry_name = $user_entry["fname"]." ".$user_entry["lname"];
	        $entered_users .= $entry_name."<br />";
	    }
	  	if($entered_users == ""){
			$delete_the_sub_dept = @mysql_query("DELETE FROM vf_sub_dept WHERE
	        	sub_dept_id = '$rmv_sub' AND dept_id = '$DeptID'");
	    	$current_message = "<font color=\"#FF0000\"><b>The sub_department has been removed.</b></font><br />";
		}else{
	    	$current_message = "<font color=\"#FF0000\"><b>Cannot remove the sub_department the following names are associated
	        	with it.</b><br />$entered_users <b>Move the users to another department
	            and try again.</b></font><br />";
		}
		unset($rmv_sub);
	    unset($field_count);
    }
}

//IF THE USER WANTS TO EDIT A DESCRIPTION RUN THIS
if(isset($page_id)){
    $sub_edit = $page_id;
	$ck_sub_edit = @mysql_query("SELECT * FROM vf_sub_dept WHERE sub_dept_id = '$page_id' AND dept_id = '$DeptID'")or die("Selected Sub-Department is not valid");
	$sub_array_edit = @mysql_fetch_array($ck_sub_edit);
		$sub_dept_edit_id = $sub_array_edit["sub_dept_id"];
		$sub_dept_edit_code = $sub_array_edit["code"];
		$sub_dept_edit_descr = $sub_array_edit["descr"];
}

//IF FORM CHANGES ARE SUBMITTED RUN THIS
if(isset($add_sub_dept)){

    $error_on_page = 0;

	if($sub_dept_code == ""){
	   	//SET ERROR VARIABLE TO ON
		$error_on_page = 1;
		$message[] = "You must enter a code for the Sub-Deprtment.";
	}

	if($sub_dept_name == ""){
	   	//SET ERROR VARIABLE TO ON
		$error_on_page = 1;
		$message[] = "You must enter a name for the Sub-Deprtment.";
	}

	//IF THERE WERE ANY ERRORS DON'T UPDATE THE TABLE AND LET THE USER KNOW
	if($message){
    	$current_message = "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$current_message .= "$numeric_text. $value <br />";
	            $numeric_text = $numeric_text + 1;
            }
		$current_message .= "</font><br />\n";
            unset($numeric_text);
    }

	/************************************************************************
	** START ENTERING INFORMATION INTO THE DATABASE IF THERE WERE NO ERRORS**
	************************************************************************/
	 if($error_on_page == 0){
        //INSERT VALUES INTO THE EMPLOYEE DATABASE
        $result = @mysql_query("INSERT INTO vf_sub_dept
        	(code,descr,dept_id)values('$sub_dept_code','$sub_dept_name','$DeptID')")or die ("Unable to add Sub_Department");

		//IF THEY ADD A SUB DEPT MAKE SURE THE DEPARTMENT TABLE IS EDITED TO SAY Y ON SUB DEPT
        $use_subs = @mysql_query("UPDATE `vf_department` SET sub_dept = 'Y' WHERE dept_id = '$DeptID'");


		//LET THE USER KNOW THE EMPLOYEE WAS ADDED
    	echo "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following Sub-Department was added: " .
			"</b></font><br /><font color=\"red\">\n $sub_dept_name <br />\n";
		echo "</font><br />";

        //UNSET ALL THE VARIABLES
		$sub_dept_name = "";
        unset($page_id);
		unset($sub_dept_edit_descr);
	}
}

//IF THERE ARE SUB DEPARTMENTS DISPLAY THEM
$current_subs = '<p align="center">';
$ck_sub = @mysql_query("SELECT * FROM vf_sub_dept WHERE dept_id = '$DeptID'");
while($sub_array = @mysql_fetch_array($ck_sub)){
	$sub_dept_id = $sub_array["sub_dept_id"];
	$sub_dept_code_selection = $sub_array["code"];
	$sub_dept_selection = $sub_array["descr"];

    $page_id = "?page_id=".$sub_dept_id;
    $remove_sub =  "?rmv_sub=".$sub_dept_id;

	$current_subs .= $sub_dept_code_selection.' - '.$sub_dept_selection.'&nbsp;
    <a href="'.$_SERVER["PHP_SELF"].$page_id.'">Edit</a>
    &nbsp;&nbsp;<a href="'.$_SERVER["PHP_SELF"].$remove_sub.'" onclick="javascript:return confirm(\'Are you sure you want to delete the <<'.$sub_dept_selection.'>> sub_department?\')">Delete</a><br />';

}
$current_subs .= '</p>';


//$current_message IS USED WHEN DELETING A SUB_DEPARTMENT
echo $current_message.'
		 <form method="POST" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data">
		  <table border="0" cellpadding="0" cellspacing="0" width="500">
		   <tr>
			<td colspan="2"><a href="sub_dept_menu.php">Return to the work with sub-departments menu</a></td>
		   </tr>
		   <tr>
			<td colspan="2">&nbsp;</td>
		   </tr>
		   <tr>
			<td colspan="2"><b>1.</b> Enter a new name and code then click the Add button.<br />
             <b>2.</B> The code will be used for referring to this item, so try to make it meaningful.
            </td>
		   </tr>
		   <tr>
			<td colspan="2">&nbsp;</td>
		   </tr>
		   <tr>
			<td>New sub-department code (4 characters):&nbsp; </td>
			<td><input type="text" name="sub_dept_code" size="10" maxlength="4" value="'.$sub_dept_edit_code.'"></td>
		   </tr>
		   <tr>
			<td colspan="2">&nbsp;</td>
		   </tr>
		   <tr>
			<td>New sub-department description (name):&nbsp; </td>
			<td><input type="text" name="sub_dept_name" size="52" value="'.$sub_dept_edit_descr.'"></td>
		   </tr>
		   <tr>
			<td colspan="2">&nbsp;</td>
		   </tr>
		  </table>';

if(isset($sub_edit)){
	$url_addition = "?sub_dept_edit_descr=&sub_edit=";

	echo '<table border="0" cellpadding="0" cellspacing="0" width="500">
		   <tr>
			<td align="center">
		         <input type="hidden" value="' . $DeptID . '" name="DeptID">
		         <input type="hidden" value="' . $sub_dept_edit_id . '" name="sub_editID">
			     <input type="submit" value="Update Description" name="update_sub_dept">
				 <input type="submit" value="Cancel" name="cancel">
		    </td>
		   </tr>
		  </table>';
}else{
	echo '<table border="0" cellpadding="0" cellspacing="0" width="500">
		   <tr>
			<td align="center">
	         <input type="hidden" value="' . $DeptID . '" name="DeptID">
		     <input type="submit" value="Add" name="add_sub_dept">		    
		    </td>
		   </tr>
		  </table>';
}
echo '</form>';
//IF THERE ARE SUB DEPARTMENTS. DISPLAY THEM
if($current_subs != ""){
	echo '<table border="0" cellpadding="0" cellspacing="0" width="500">
		   <tr>
			<td align="center"><p class="text14BkB"align="center"><u>Current Sub-Departments</u></p>
			'.$current_subs.'
		    </td>
		   </tr>
		  </table>
			<p>&nbsp;</p>';       
}

require(DIR_PATH."includes/footer.inc.php");
?>