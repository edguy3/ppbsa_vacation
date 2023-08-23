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
if($_SESSION["ses_super_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

//SET DEPARTMENT
$DeptID = $_SESSION["ses_dept_id"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$add_entry = $_POST["add_entry"];
$edit_entry = $_POST["edit_entry"];
$type_id = $_POST["type_id"];
$delete_dept = $_POST["delete_dept"];
$new_desc = $_POST["new_desc"];
$new_sub = $_POST["new_sub"];
$edit_cancel = $_POST["edit_cancel"];
$edit_dept = $_POST["edit_dept"];

$cur_page_title = "Departments";
$hdr_detail = "Add Departments";
$itemcnt = 1; //Count of select boxes on the current page
require(DIR_PATH."includes/header.inc.php");


//ADD A NEW CATEGORY
if(isset($add_entry)){
	$error_ck = 0;
	
	if($new_desc == ""){
		echo '<span style="color:red;"><b>ERROR!<br />Description can not be blank.</b></span><br />';
		$error_ck = 1;		
	}
	if($new_sub == "Select One"){
		echo '<span style="color:red;"><b>ERROR!<br />You must choose to allow a sub-department.</b></span><br />';
		$error_ck = 1;		
	}
	
	$addck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_department` WHERE `descr` = '$new_desc'");
	$addck_result = @mysql_fetch_array($addck_sql);
		$num_rows = $addck_result["TOTAL"];
	
	if($num_rows != 0){
		echo '<span style="color:red;"><b>ERROR!<br />A department exists that contains the same description. Can not add department.</b></span><br />';
		$error_ck = 1;	
	}
		
	if($new_sub == "Y"){
		$select_y = "selected";
		$select_n = "";
	}elseif($new_sub == "N"){
		$select_y = "";
		$select_n = "selected";		
	}else{
		$select_y = "";
		$select_n = "";		
	}	
	
	if($error_ck == 0){		
		$add_sql = @mysql_query("INSERT INTO `vf_department` (`dept_id`,`descr`,`sub_dept`)VALUES('','$new_desc','$new_sub')") or die("Can not add item.");
		$new_desc = "";
		$new_sub = "";
		$select_y = "";
		$select_n = "";			
	}
}


//CANCEL THE EDIT
if(isset($edit_cancel)){
	$new_desc = "";	
	$new_sub = "";
	unset($edit_dept);
	$select_y = "";
	$select_n = "";		
}

//EDIT A DEPARTMENT
if(isset($edit_dept)){
	$edit_sql = @mysql_query("SELECT * FROM `vf_department` WHERE `dept_id` = '$type_id'"); 
	$edit_result = @mysql_fetch_array($edit_sql);
	$new_desc = $edit_result["descr"];
	$new_sub = $edit_result["sub_dept"];
}
//UPDATE THE ITEM AFTER EDITING
if(isset($edit_entry)){
	$error_ck = 0;
echo "EDITT";	
	if($new_desc == ""){
		echo '<span style="color:red;"><b>ERROR!<br />Description can not be blank.</b></span><br />';
		$error_ck = 1;		
	}
	if($new_sub == "Select One"){
		echo '<span style="color:red;"><b>ERROR!<br />You must choose to allow a sub-department.</b></span><br />';
		$error_ck = 1;		
	}	
	$editck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_department` WHERE `descr` = '$new_desc' AND `dept_id` <>'$type_id'");
	$editck_result = @mysql_fetch_array($editck_sql);
		$num_rows = $editck_result["TOTAL"];

	if($num_rows != 0){
		echo '<span style="color:red;"><b>ERROR!<br />A department exists that contains this description. Can not update department.</b></span><br />';
		$error_ck = 1;	
	}
	//Update the department
	if($error_ck == 0){		
		$add_sql = @mysql_query("UPDATE `vf_department` SET `descr` = '$new_desc', `sub_dept` = '$new_sub' WHERE `dept_id` = '$type_id'") or die("Can not update item.");
		$new_desc = "";
		$new_sub = "";
		$select_y = "";
		$select_n = "";	
	}else{
		$edit_dept = "edit_dept";
	}
}


//DELETE A DEPARTMENT
if(isset($delete_dept)){
	if($type_id != ""){
		//Make sure there are no employees in this department
		$deptck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_employee` WHERE `dept_id` = '$type_id'");
		$deptck_result = @mysql_fetch_array($deptck_sql);
		$num_rows = $deptck_result["TOTAL"];

		if($num_rows == 0){
			//Make sure there are no sub_departrments associated with this department
			$deptck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_sub_dept` WHERE `dept_id` = '$type_id'");
			$deptck_result = @mysql_fetch_array($deptck_sql);
			$num_rows = $deptck_result["TOTAL"];			
			if($num_rows == 0){
				$rmv_sql = @mysql_query("DELETE FROM `vf_department` WHERE `dept_id` = '$type_id'") or die("Can not remove item.");	
			}else{
				echo '<span style="color:red;"><b>ERROR!<br />There are sub-departments associated with this department. Please reassociate the sub-department then try again.</b></span><br />';
			}
		}else{
			echo '<span style="color:red;"><b>ERROR!<br />There are employees associated with this department. Please change their department then try again.</b></span><br />';
		}
	}	
}




//GET A LIST OF CURRENT DEPARTMENTS
$dept_cnt = 0;
$dept_sql = @mysql_query("SELECT * FROM `vf_department`");
while($dept_result = @mysql_fetch_array($dept_sql)){
	$dept_id = $dept_result["dept_id"];
	$dept_descr = $dept_result["descr"];
	$sub_dept = $dept_result["sub_dept"];

	if($bgcolor == "#FFFFFF"){
    	$bgcolor = "#C0C0C0";
    }else{
    	$bgcolor = "#FFFFFF";
	}
		
	$dept_display .= '
	  <tr>
	    <td width="50" bgcolor="'.$bgcolor.'" align="center">
	  		<form action="'.$_SERVER["PHP_SELF"] .'" method="post" enctype="multipart/form-data" style="margin-bottom:0;">
  			<input type="hidden" value="'.$dept_id.'" name="type_id">
		    <input name="edit_dept" type="submit" value="Edit" style="background:black;border:none;color:white;">
	  		</form>
		</td>
	    <td width="50" bgcolor="'.$bgcolor.'" align="center">
	  		<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data" style="margin-bottom:0;">
  			<input type="hidden" value="'.$dept_id.'" name="type_id">
		    <input name="delete_dept" type="submit" value="Delete" style="background:black;border:none;color:white;" onclick="javascript:return confirm(\'Are you sure you want to delete: '.$dept_descr.' ?\')">
	  		</form>
		</td>
	    <td width="100" bgcolor="'.$bgcolor.'" align="center">'.$dept_id.'</td>  
	    <td width="350" bgcolor="'.$bgcolor.'">'.$dept_descr.'</td>
	    <td align="center" width="50" bgcolor="'.$bgcolor.'">'.$sub_dept.'</td>	
	  </tr>';	
	$dept_cnt = 1;
}

if(isset($edit_dept)){
	if($new_sub == "Y"){
		$select_y = "selected";
		$select_n = "";
	}elseif($new_sub == "N"){
		$select_y = "";
		$select_n = "selected";		
	}else{
		$select_y = "";
		$select_n = "";		
	}	
}
?>
<p style="margin-left: 10;"><a href="site_setup.php">Return to the Site Setup menu</a></p>
<form action="<?PHP echo $_SERVER["PHP_SELF"]; ?>" method="post">
<table width="600" border="0" cellspacing="2" cellpadding="0" style="margin-left: 10;" id="datatable">
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="600" colspan="2" align="center"><b>Add a new department</b></td>
  </tr>  
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>    
  <tr>
    <td width="200" class="emptable">Department:&nbsp;</td>
    <td width="400">
    <input name="new_desc" type="text" size="30" maxlength="25" value="<?PHP echo $new_desc; ?>">
    </td>
  </tr>
  <tr>
    <td width="200" class="emptable">Allow Sub-Departments:&nbsp;</td>
    <td width="400"><div id="HideItem0" style="POSITION:relative">
	 	 <select name="new_sub" size="1">
		  <option>Select One</option>	 	 
		  <option value="Y" <?PHP echo $select_y; ?>>Yes</option>
		  <option value="N"<?PHP echo $select_n; ?>>No</option>	
		 </select>
		 </div>    
    </td>
  </tr>
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>  
  
  
 
  
<?PHP 
//EDIT A DEPARTMENT
if(isset($edit_dept)){
	echo '	
  <tr>
    <td width="600" colspan="2" align="center"><input name="edit_entry" type="submit" value="Update">&nbsp;<input name="edit_cancel" type="submit" value="Cancel"><input type="hidden" value="'.$type_id.'" name="type_id"></td>
  </tr> 
  <tr>';
}else{
	echo '	
  <tr>
    <td width="600" colspan="2" align="center"><input name="add_entry" type="submit" value="Add"></td>
  </tr> 
  <tr>';
}
?>  
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>      
</table>
</form>

<?PHP 

if($dept_cnt == 1){
?>
	<p style="text-decoration:underline; margin-left: 10;"><b>Configured Departments</b></p>
	<table width="600" border="0" cellspacing="0" cellpadding="1" style="margin-left: 10;" id="datatable">
	  <tr id="tableheader">
	    <th width="50"></th>
	    <th width="50"></th>
	    <th width="100">ID</th>
	    <th width="350">Department</th>
	    <th width="50">Sub-Depts</th>
	  </tr>
	  <?PHP echo $dept_display; ?>
	</table>
	<p>&nbsp;</p>
<?PHP
}else{
echo '<p style="margin-left: 10;"><b>There are currently no departments entered.</b></p>';

}

require(DIR_PATH."includes/footer.inc.php");
?>