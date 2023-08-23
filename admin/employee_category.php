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
$delete_cat = $_POST["delete_cat"];
$edit_cat = $_POST["edit_cat"];
$type_id = $_POST["type_id"];
$new_sname = $_POST["new_sname"];
$new_desc = $_POST["new_desc"];
$edit_cancel = $_POST["edit_cancel"];

$cur_page_title = "Employee Categories";
$hdr_detail = "Add/Edit Employee Categories";
require(DIR_PATH."includes/header.inc.php");

//ADD A NEW CATEGORY
if(isset($add_entry)){
	$error_ck = 0;
	
	if($new_sname == ""){
		echo '<span style="color:red;"><b>ERROR!<br />Short name can not be blank.</b></span><br />';
		$error_ck = 1;		
	}
	if($new_desc == ""){
		echo '<span style="color:red;"><b>ERROR!<br />Description can not be blank.</b></span><br />';
		$error_ck = 1;		
	}

	$addck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_category` WHERE `short_name` = '$new_sname' OR descr = '$new_desc'");
	$addck_result = @mysql_fetch_array($addck_sql);
		$num_rows = $addck_result["TOTAL"];
	
	if($num_rows != 0){
		echo '<span style="color:red;"><b>ERROR!<br />A category exists that contains either the short name or description. Can not add category.</b></span><br />';
		$error_ck = 1;	
	}	

	if($error_ck == 0){		
		$add_sql = @mysql_query("INSERT INTO `vf_category` (`cat_id`,`short_name`,`descr`)VALUES('', '$new_sname', '$new_desc')") or die("Can not add item.");
		$new_sname = "";
		$new_desc = "";
	}
}


//CANCEL THE EDIT
if(isset($edit_cancel)){
	$new_sname = "";
	$new_desc = "";	
}

//EDIT A CATEGORY
if(isset($edit_cat)){
	$edit_sql = @mysql_query("SELECT * FROM `vf_category` WHERE `cat_id` = '$type_id'"); 
	$edit_result = @mysql_fetch_array($edit_sql);
	$new_sname = $edit_result["short_name"];
	$new_desc = $edit_result["descr"];
}
//UPDATE THE ITEM AFTER EDITING
if(isset($edit_entry)){
	$error_ck = 0;
	
	if($new_sname == ""){
		echo '<span style="color:red;"><b>ERROR!<br />Short name can not be blank.</b></span><br />';
		$error_ck = 1;		
	}
	if($new_desc == ""){
		echo '<span style="color:red;"><b>ERROR!<br />Description can not be blank.</b></span><br />';
		$error_ck = 1;		
	}
	$editck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_category` WHERE (`short_name` = '$new_sname' OR descr = '$new_desc') AND `cat_id` <>'$type_id'");
	$editck_result = @mysql_fetch_array($editck_sql);
		$num_rows = $editck_result["TOTAL"];

	if($num_rows != 0){
		echo '<span style="color:red;"><b>ERROR!<br />A category exists that contains either the short name or description. Can not update category.</b></span><br />';
		$error_ck = 1;	
	}
	//Update the category
	if($error_ck == 0){		
		$add_sql = @mysql_query("UPDATE `vf_category` SET `short_name` = '$new_sname',`descr` = '$new_desc' WHERE `cat_id` = '$type_id' ") or die("Can not update item.");
		$new_sname = "";
		$new_desc = "";
	}else{
		$edit_cat = "edit_cat";
	}
}


//DELETE A CATEGORY
if(isset($delete_cat)){
	if($type_id != ""){
		//Make sure there are no employees in this category
		$catck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_employee` WHERE `status` = '$type_id'");
		$catck_result = @mysql_fetch_array($catck_sql);
		$num_rows = $catck_result["TOTAL"];
	
		if($num_rows == 0){
			$rmv_sql = @mysql_query("DELETE FROM `vf_category` WHERE `cat_id` = '$type_id'") or die("Can not remove item.");	
		}else{
			echo '<span style="color:red;"><b>ERROR!<br />There are employees associated with this category. Please change their category then try again.</b></span><br />';
		}
	}	
}


//GET A LIST OF CURRENT DEPARTMENTS
$dept_cnt = 0;
$dept_sql = @mysql_query("SELECT * FROM `vf_department`");
while($dept_result = @mysql_fetch_array($dept_sql)){
	$dept_id = $dept_result["dept_id"];
	$dept_descr = $dept_result["descr"];

	$dept_display .= '
	  <tr>
	    <td bgcolor="'.$bgcolor.'" align="center" title="Edit"><a href="#">E</a></td>
	    <td bgcolor="'.$bgcolor.'" align="center" title="Delete"><a href="#">D</a></td>
	    <td bgcolor="'.$bgcolor.'" align="center">'.$dept_id.'</td>  
	    <td bgcolor="'.$bgcolor.'">'.$dept_descr.'</td>
	  </tr>';	
	$dept_cnt = 1;
}

//CHECK ALL CATEGORIES IN THE TABLE
$cat_cnt = 0;
$cat_sql = @mysql_query("SELECT * FROM `vf_category`");
while($cat_result = @mysql_fetch_array($cat_sql)){
	$cat_id = $cat_result["cat_id"];
	$cat_shrtnme = $cat_result["short_name"];
	$cat_descr = $cat_result["descr"];
	
	if($bgcolor == "#FFFFFF"){
    	$bgcolor = "#C0C0C0";
    }else{
    	$bgcolor = "#FFFFFF";
	}
	
  $cat_display .= '
	  <tr bgcolor="'.$bgcolor.'">	
	    <td width="50" align="center" title="Edit">
	  		<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data" style="margin-bottom:0;" >
  			<input type="hidden" value="'.$cat_id.'" name="type_id">
		    <input name="edit_cat" type="submit" value="Edit" style="background:black;border:none;color:white;">
	  		</form>
  		</td>
	    <td width="50" align="center" title="Delete">
	  		<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data" style="margin-bottom:0;">
  			<input type="hidden" value="'.$cat_id.'" name="type_id">
		    <input name="delete_cat" type="submit" value="Delete" style="background:black;border:none;color:white;" onclick="javascript:return confirm(\'Are you sure you want to delete category: '.$cat_descr.' ?\')">
	  		</form> 
		  </td> 
	    <td width="100" align="center">'.$cat_shrtnme.'</td>  
	    <td width="400">'.$cat_descr.'</td>
	  </tr>';
  $cat_cnt = 1;
}


?>
<p style="margin-left: 10;"><a href="site_setup.php">Return to the Site Setup menu</a></p>
<form action="<?PHP echo $_SERVER["PHP_SELF"]; ?>" method="post">
<table width="600" border="0" cellspacing="2" cellpadding="0" style="margin-left: 10;" id="datatable">
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="600" colspan="2" align="center"><b>Add a new category</b></td>
  </tr>  
  <tr>
    <td width="600" colspan="2" align="center">(Full time , part time, etc...)</td>
  </tr>
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>    
  <tr>
    <td width="200" class="emptable">Short Name:&nbsp;</td>
    <td width="400"><input name="new_sname" type="text" size="15" maxlength="15" value="<?PHP echo $new_sname; ?>"></td>
  </tr>
  <tr>
    <td width="200" class="emptable">Description:&nbsp;</td>
    <td width="400"><input name="new_desc" type="text" size="30" maxlength="25" value="<?PHP echo $new_desc; ?>"></td>
  </tr>
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>
<?PHP 
//EDIT A CATEGORY
if(isset($edit_cat)){
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
    <td width="600" colspan="2">&nbsp;</td>
  </tr>      
</table>
</form>

<?PHP 

if($cat_cnt == 1){
?>
	<p style="text-decoration:underline; margin-left: 10;"><b>Configured Time Off Types</b></p>
	<table width="600" border="0" cellspacing="0" cellpadding="1" style="margin-left: 10;" id="datatable">
	  <tr id="tableheader">
	    <th width="50" title="Edit">&nbsp;</th>
	    <th width="50" title="Delete">&nbsp;</th>
	    <th width="100">Short Name</th>
	    <th width="400">Description</th>
	  </tr>
	  <?PHP echo $cat_display; ?>
	</table>
	<p>&nbsp;</p>
<?PHP
}else{
echo '<p style="margin-left: 10;"><b>There are currently no categories entered.</b></p>';

}

require(DIR_PATH."includes/footer.inc.php");
?>