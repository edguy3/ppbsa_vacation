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
$delete_type = $_POST["delete_type"];
$new_code = $_POST["new_code"];
$new_desc = $_POST["new_desc"];
$edit_cancel = $_POST["edit_cancel"];
$new_var = $_POST["new_var"];
$new_earn = $_POST["new_earn"];
$new_dept = $_POST["new_dept"];
$new_view = $_POST["new_view"];
$edit_type = $_POST["edit_type"];
$new_text_color = $_POST["text_color"];
//Force a text color if one is not selected
if($new_text_color == "")
{
	$new_text_color = "#000000";
}


$cur_page_title = "Time off types";
$hdr_detail = "Add/Edit Time Off Types";
$itemcnt = 5; //Count of select boxes on the current page
require(DIR_PATH."includes/header.inc.php");

//ADD A NEW CATEGORY
if(isset($add_entry)){
	$error_ck = 0;
	
	if($new_desc == ""){
		echo '<span style="color:red;"><b>ERROR!<br />Description can not be blank.</b></span><br />';
		$error_ck = 1;		
	}
	if($new_var == "Select One"){
		echo '<span style="color:red;"><b>ERROR!<br />You must choose whether to allow variable hours.</b></span><br />';
		$error_ck = 1;		
	}	
	if($new_earn == "Select One"){
		echo '<span style="color:red;"><b>ERROR!<br />You must choose whether time off must be earned before it can be used.</b></span><br />';
		$error_ck = 1;		
	}	
	if($new_dept == "all"){
		$new_dept = 0;
	}
	if($new_shift == "all"){
		$new_shift = 0;
	}
	if($new_view == "Select One"){
		echo '<span style="color:red;"><b>ERROR!<br />You must choose whether to allow employees to view this type.</b></span><br />';
		$error_ck = 1;		
	}	
	if($new_code != ""){
		$addck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_to_type` WHERE `descr` = '$new_desc' OR `code` = '$new_code'");
	}else{
		$addck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_to_type` WHERE `descr` = '$new_desc'");		
	}
	$addck_result = @mysql_fetch_array($addck_sql);
		$num_rows = $addck_result["TOTAL"];
	
	if($num_rows != 0){
		echo '<span style="color:red;"><b>ERROR!<br />A time off type exists that contains either the code or description. Can not add type.</b></span><br />';
		$error_ck = 1;	
	}
	
	if($error_ck == 0){		
		$add_sql = @mysql_query("INSERT INTO `vf_to_type` (`to_id`,`emp_viewable`, `code`, `descr`, `variable_hours`,`earned`, `dept_id`, `shift_id`, `text_color`)
								VALUES
								('','$new_view','$new_code','$new_desc','$new_var','$new_earn','$new_dept','$new_shift','$new_text_color')") or die("Can not add item.");
		$new_to_id = "";
		$new_view = "";
		$new_code = "";
		$new_desc = "";
		$new_earn = "";
		$new_var = "";
		$new_dept = "";
		$new_shift = "";
		$new_text_color = "";
	}
}


//CANCEL THE EDIT
if(isset($edit_cancel)){
	$new_to_id  = "";
	$new_view = "";
	$new_code = "";
	$new_desc = "";
	$new_earn = "";
	$new_var = "";
	$new_dept_id = "";
	$new_shift_id = "";
	unset($edit_type);
	$new_text_color = "";
}

//EDIT A TYPE
if(isset($edit_type)){
	$edit_sql = @mysql_query("SELECT * FROM `vf_to_type` WHERE `to_id` = '$type_id'"); 
	$edit_result = @mysql_fetch_array($edit_sql);
	$new_to_id  = $edit_result["to_id "];
	$new_view = $edit_result["emp_viewable"];
	$new_code = $edit_result["code"];
	$new_desc = $edit_result["descr"];
	$new_earn = $edit_result["earned"];
	$new_var = $edit_result["variable_hours"];
	$new_dept = $edit_result["dept_id"];	
	$new_shift = $edit_result["shift_id"];
	$new_text_color = $edit_result["text_color"];
}  
//UPDATE THE ITEM AFTER EDITING
if(isset($edit_entry)){
	$error_ck = 0;
	
	if($new_desc == ""){
		echo '<span style="color:red;"><b>ERROR!<br />Description can not be blank.</b></span><br />';
		$error_ck = 1;		
	}
	if($new_var == "Select One"){
		echo '<span style="color:red;"><b>ERROR!<br />You must choose whether to allow variable hours.</b></span><br />';
		$error_ck = 1;		
	}	
	if($new_earn == "Select One"){
		echo '<span style="color:red;"><b>ERROR!<br />You must choose whether time off must be earned before it can be used.</b></span><br />';
		$error_ck = 1;		
	}	
	if($new_dept == "all"){
		$new_dept = 0;
	}
	if($new_shift == "all"){
		$new_shift = 0;
	}
	if($new_view == "Select One"){
		echo '<span style="color:red;"><b>ERROR!<br />You must choose whether to allow employees to view this type.</b></span><br />';
		$error_ck = 1;		
	}	
	
	if($new_code != ""){
		$editck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_to_type` WHERE (`descr` = '$new_desc' OR `code` = '$new_code') AND `to_id` <>'$type_id'");
	}else{
		$editck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_to_type` WHERE `descr` = '$new_desc' AND `to_id` <>'$type_id'");		
	}
	$editck_result = @mysql_fetch_array($editck_sql);
		$num_rows = $editck_result["TOTAL"];

	if($num_rows != 0){
		echo '<span style="color:red;"><b>ERROR!<br />A time off type exists that contains either the code or description. Can not update type.</b></span><br />';
		$error_ck = 1;	
	}
	//Update the department
	if($error_ck == 0){		
		$add_sql = @mysql_query("UPDATE `vf_to_type` 
								SET `emp_viewable` = '$new_view', 
								`code` = '$new_code', 
								`descr` = '$new_desc', 
								`earned` = '$new_earn', 
								`variable_hours` = '$new_var', 
								`dept_id` = '$new_dept', 
								`shift_id` = '$new_shift',
		 						`text_color` = '$new_text_color'
								WHERE `to_id` = '$type_id'") or die("Can not update item.");

		$new_to_id = "";
		$new_view = "";
		$new_code = "";
		$new_desc = "";
		$new_earn = "";
		$new_var = "";
		$new_dept = "";
		$new_shift = "";
		$new_text_color = "";				
	}else{
		$edit_type = "edit_type";
	}
}

//DELETE A TYPE
if(isset($delete_type)){	
	if($type_id != ""){
		
		//Make sure there are no employees in this department
		$typeck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_emp_to_hours` WHERE `to_id` = '$type_id'");
		$typeck_result = @mysql_fetch_array($typeck_sql);
		$num_rows = $typeck_result["TOTAL"];

		if($num_rows == 0){
				$rmv_sql = @mysql_query("DELETE FROM `vf_to_type` WHERE `to_id` = '$type_id'") or die("Can not remove item.");	
		}else{
			echo '<span style="color:red;"><b>ERROR!<br />There is time off associated with this type. Remove these entries then try again.</b></span><br />';
		}
	}	
}

//CHECK ALL TIME OFF TYPES IN THE TABLE
$to_cnt = 0;
$to_sql = @mysql_query("SELECT * FROM `vf_to_type`");
while($to_result = @mysql_fetch_array($to_sql)){
	$to_id = $to_result["to_id"];
	$to_code = $to_result["code"];
	$to_descr = $to_result["descr"];
	$to_earned = $to_result["earned"];
	$variable_hours = $to_result["variable_hours"];
	$to_dept = $to_result["dept_id"];
	$to_shift = $to_result["shift_id"];
	$to_view = $to_result["emp_viewable"];
	$text_color = $to_result["text_color"];
	
	if($bgcolor == "#FFFFFF"){
    	$bgcolor = "#C0C0C0";
    }else{
    	$bgcolor = "#FFFFFF";
	}
	
  $to_display .= '
	  <tr>
	    <td bgcolor="'.$bgcolor.'" align="center">
	  		<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data" style="margin-bottom:0;">
  			<input type="hidden" value="'.$to_id.'" name="type_id">
		    <input name="edit_type" type="submit" value="Edit" style="background:black;border:none;color:white;">
	  		</form>  
 		</td>
	    <td bgcolor="'.$bgcolor.'" align="center">
	  		<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data" style="margin-bottom:0;">
  			<input type="hidden" value="'.$to_id.'" name="type_id">
		    <input name="delete_type" type="submit" value="Delete" style="background:black;border:none;color:white;" onclick="javascript:return confirm(\'Are you sure you want to delete: '.$to_descr.' ?\')">
	  		</form>
  		</td>
  		<td bgcolor="'.$bgcolor.'" align="center"><span style="background-color:'.$text_color.';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	    <td bgcolor="'.$bgcolor.'" align="center">'.$to_code.'</td>  
	    <td bgcolor="'.$bgcolor.'">'.$to_descr.'</td>
	    <td bgcolor="'.$bgcolor.'" align="center">'.$variable_hours.'</td>
	    <td bgcolor="'.$bgcolor.'">'.$to_dept.'</td>
  		<td bgcolor="'.$bgcolor.'" align="center">'.$to_earned.'</td>
	    <td bgcolor="'.$bgcolor.'" align="center">'.$to_shift.'</td>
	    <td bgcolor="'.$bgcolor.'" align="center">'.$to_view.'</td>
	  </tr>';
  $to_cnt = 1;
}


//GET A LIST OF CURRENT DEPARTMENTS
$dept_cnt = 0;
$dept_sql = @mysql_query("SELECT * FROM `vf_department`");
while($dept_result = @mysql_fetch_array($dept_sql)){
	$dept_id = $dept_result["dept_id"];
	$dept_descr = $dept_result["descr"];

	if($new_dept == $dept_id){
		$selected = "selected";
	}
	
	$dept_display .= '<option value="'.$dept_id.'" '.$selected.'>'.$dept_descr.'</option>';	
	$selected = "";
	$dept_cnt = 1;
}


//GET A LIST OF CURRENT SHIFTS
$shift_cnt = 0;
$shift_sql = @mysql_query("SELECT * FROM `vf_shift`");
while($shift_result = @mysql_fetch_array($shift_sql)){
	$shift_id = $shift_result["shift_id "];
	$shift_descr = $shift_result["descr"];

	$shift_display .= '<option value="'.$shift_id.'">'.$shift_descr.'</option>';
	
	$shift_cnt = 1;
}
?>
<p style="margin-left: 10;"><a href="site_setup.php">Return to the Site Setup menu</a></p>
<form action="<?PHP echo $_SERVER["PHP_SELF"]; ?>" method="post">
<table width="600" border="0" cellspacing="2" cellpadding="0" style="margin-left: 10;" id="datatable">
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="600" colspan="2" align="center"><b>Add a new Time Off type</b></td>
  </tr>  
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>  
  <tr>
    <td width="200" class="emptable">Code:&nbsp;</td>
    <td width="400"><input name="new_code" type="text" size="15" maxlength="15" value="<?PHP echo $new_code; ?>"></td>
  </tr>
  <tr>
    <td width="200" class="emptable">Description:&nbsp;</td>
    <td width="400"><input name="new_desc" type="text" size="30" maxlength="25" value="<?PHP echo $new_desc; ?>"></td>
  </tr>
  <tr>
    <td width="200" class="emptable">Variable Hours:&nbsp;</td>
    <td width="400">
    <div id="HideItem0" style="POSITION:relative">
	 <select name="new_var" size="1">
		  <option>Select One</option>	 	 
		  <option value="Y" <?PHP if($new_var == "Y"){echo "selected"; }?>>Yes</option>
		  <option value="N" <?PHP if($new_var == "N"){echo "selected"; }?>>No</option>	
	 </select>
	 </div>
	</td>
  </tr> 
  <tr>
    <td width="200" class="emptable" title="Yes, if hours are credited before they can be used">Earned Hours:&nbsp;</td>
    <td width="400">
    <div id="HideItem1" style="POSITION:relative">    
	 <select name="new_earn" size="1">
		  <option>Select One</option>	 	 
		  <option value="Y" <?PHP if($new_earn == "Y"){echo "selected"; }?>>Yes</option>
		  <option value="N" <?PHP if($new_earn == "N"){echo "selected"; }?>>No</option>	
	 </select>
	 </div>
	</td>
  </tr>    
  <tr>
    <td width="200" class="emptable">Department:&nbsp;</td>
    <td width="400">
     <div id="HideItem2" style="POSITION:relative">
	 <select name="new_dept" size="1">
	  <option value="all">All</option>
	  <?PHP 
	  if($dept_cnt == 1){
	  	echo $dept_display;
	  }
	  ?>
	 </select>	
	 </div>
	</td>
  </tr>
  <tr>
    <td width="200" class="emptable">Shift:&nbsp;</td>
    <td width="400">
     <div id="HideItem3" style="POSITION:relative">
	 <select name="new_shift" size="1">
	  <option value="all">All</option>
	  <?PHP 
	  if($shift_cnt == 1){
	  	echo $shift_display;
	  }
	  ?>	
	 </select>
	 </div>	    
    </td>
  </tr>
  <tr>
    <td width="200" class="emptable">Employee Viewable:&nbsp;</td>
    <td width="400">
    <div id="HideItem4" style="POSITION:relative">
	 <select name="new_view" size="1">
		  <option>Select One</option>	 	 
		  <option value="Y" <?PHP if($new_view == "Y"){echo "selected"; }?>>Yes</option>
		  <option value="N" <?PHP if($new_view == "N"){echo "selected"; }?>>No</option>	
	 </select>
	 </div>
	</td>
  </tr>
  <tr>
    <td width="600" colspan="2">  
     <table cellpadding="0" cellspacing="0">
      <tr>
       <td valign="top" width="272"> 
        <table cellpadding="0" cellspacing="0">
         <tr>
          <td width="200" class="emptable">Text Display Color:&nbsp;</td>
          <td width="72" style="padding-left:2px;"><input name="text_color" type="text" size="7" maxlength="7" value="<?PHP echo $new_text_color; ?>"></td>
         </tr>  
        </table>
       </td>
       <td width="328">
       <?PHP include_once(DIR_PATH."includes/colorwheel.inc.php"); ?>
       </td>
      </tr>
     </table> 
    </td>
  </tr>      
  <tr>
    <td width="600" colspan="2"></td>
  </tr>
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>
<?PHP
//EDIT A DEPARTMENT
if(isset($edit_type)){
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

if($to_cnt == 1){
?>
	<p style="text-decoration:underline; margin-left: 10;"><b>Configured Time Off Types</b></p>
	<table width="600" border="0" cellspacing="0" cellpadding="1" style="margin-left: 10;" id="datatable">
	  <tr id="tableheader">
	    <td valign="bottom"></td>
	    <td valign="bottom"></td>
	    <td valign="bottom">Text<br />color</td>
	    <td valign="bottom">Code</td>
	    <td valign="bottom">Description</td>
	    <td valign="bottom">Variable<br />Hrs</td>
	    <td valign="bottom">Dept</td>
	    <td valign="bottom">Earned</td>
	    <td valign="bottom">Shift</td>
	    <td valign="bottom">Employee<br />Viewable</td>
	  </tr>
	  <?PHP echo $to_display; ?>
	</table>
	<p>&nbsp;</p>
<?PHP
}else{
echo '<p><b>There are currently no Time Off types entered.</b></p>';

}

require(DIR_PATH."includes/footer.inc.php");
?>
