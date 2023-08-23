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
$edit_year = $_POST["edit_year"];
$year_id = $_POST["year_id"];
$old_year = $_POST["old_year"];
$new_year = $_POST["new_year"];
$new_begin = $_POST["new_begin"];
$new_end = $_POST["new_end"];
$edit_cancel = $_POST["edit_cancel"];

$cur_page_title = "Fiscal year";
$hdr_detail = "Add/Edit Fiscal Year";
require(DIR_PATH."includes/header.inc.php");

//ADD A NEW YEAR
if(isset($add_entry)){
	$error_ck = 0;
	
	if($new_year == ""){
		echo '<span style="color:red;"><b>ERROR!<br />The Year can not be blank.</b></span><br />';
		$error_ck = 1;		
	}else{
		if(strlen($new_year) != 4){
			echo '<span style="color:red;"><b>ERROR!<br />The Year must be 4 digits.</b></span><br />';
			$error_ck = 1;							
		}else{	
			if($new_begin == ""){
				$new_begin = $new_year."-01-01";		
			}
			
			if($new_end == ""){
				$new_end = $new_year."-12-31";		
			}
		}	
	}
		
	$yearck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_year` WHERE `year` = '$new_year'");
	$yearck_result = @mysql_fetch_array($yearck_sql);
		$num_rows = $yearck_result["TOTAL"];
	
	if($num_rows != 0){
		echo '<span style="color:red;"><b>ERROR!<br />The year you entered already exists. Can not add year.</b></span><br />';
		$error_ck = 1;	
	}	

	if($error_ck == 0){		
		$add_sql = @mysql_query("INSERT INTO `vf_year` (`year`,`start`,`end`)VALUES('$new_year', '$new_begin', '$new_end')") or die("Can not add item.");
		$new_year = "";
		$new_begin = "";
		$new_end = "";
	}
}


//CANCEL THE EDIT
if(isset($edit_cancel)){
		$new_year = "";
		$new_begin = "";
		$new_end = "";
}

//EDIT A YEAR
if(isset($edit_year)){
	$edit_sql = @mysql_query("SELECT * FROM `vf_year` WHERE `year` = '$year_id'"); 
	$edit_result = @mysql_fetch_array($edit_sql);
		$new_year = $edit_result["year"];
		$new_begin = $edit_result["start"];
		$new_end = $edit_result["end"];
}
//UPDATE THE ITEM AFTER EDITING
if(isset($edit_entry)){
	$error_ck = 0;
	if($old_year == ""){
		echo '<span style="color:red;"><b>ERROR!<br />The Year can not be updated.</b></span><br />';
		$error_ck = 1;				
	} 
	
	if($new_year == ""){
		echo '<span style="color:red;"><b>ERROR!<br />The Year can not be blank.</b></span><br />';
		$error_ck = 1;		
	}else{
		if(strlen($new_year) != 4){
			echo '<span style="color:red;"><b>ERROR!<br />The Year must be 4 digits.</b></span><br />';
			$error_ck = 1;							
		}else{	
			if($new_begin == ""){
				$new_begin = $new_year."-01-01";		
			}
			
			if($new_end == ""){
				$new_end = $new_year."-12-31";		
			}
		}	
	}
	
	if($new_year != $old_year){
		$editck_sql = @mysql_query("SELECT COUNT(*) AS TOTAL FROM `vf_year` WHERE `year` = '$new_year'");
		$editck_result = @mysql_fetch_array($editck_sql);
			$num_rows = $editck_result["TOTAL"];
	
		if($num_rows != 0 ){
			echo '<span style="color:red;"><b>ERROR!<br />A year exists that matched your entry. Can not update year.</b></span><br />';
			$error_ck = 1;	
		}
	}

	//Update the year
	if($error_ck == 0){				
		$add_sql = @mysql_query("UPDATE `vf_year` SET `year` = '$new_year',`start` = '$new_begin',`end` = '$new_end' WHERE `year` = '$old_year' ") or die("Can not update item.");
		$new_year = "";
		$new_begin = "";
		$new_end = "";
		$old_year = "";
	}else{
		$edit_year = "edit_year";
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

//CHECK ALL YEARS IN THE TABLE
$year_cnt = 0;
$year_sql = @mysql_query("SELECT * FROM `vf_year` ORDER BY `year`");
while($year_result = @mysql_fetch_array($year_sql)){
	$year = $year_result["year"];
	$start = $year_result["start"];
	$end = $year_result["end"];

	$timestamp = strtotime($start);
	$display_start = date('D M d, Y ', $timestamp);
	
	$timestamp = strtotime($end);
	$display_end = date('D M d, Y ', $timestamp);
	
	if($bgcolor == "#FFFFFF"){
    	$bgcolor = "#C0C0C0";
    }else{
    	$bgcolor = "#FFFFFF";
	}
	
  $year_display .= '
	  <tr bgcolor="'.$bgcolor.'">	
	    <td width="50" align="center" title="Edit">
	  		<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data" style="margin-bottom:0;" >
  			<input type="hidden" value="'.$year.'" name="year_id">
		    <input name="edit_year" type="submit" value="Edit" style="background:black;border:none;color:white;">
	  		</form>
  		</td>
	    <td align="center" width="100" align="center">'.$year.'</td>  
	    <td align="center" width="200">'.$display_start.'</td>
  		<td align="center" width="200">'.$display_end.'</td>
	  </tr>';
  $year_cnt = 1;
}


?>
<p style="margin-left: 10;"><a href="site_setup.php">Return to the Site Setup menu</a></p>
<form name="fiscal" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>" method="post">
<table width="600" border="0" cellspacing="2" cellpadding="0" style="margin-left: 10;" id="datatable">
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="600" colspan="2" align="center"><b>Configure fiscal year</b></td>
  </tr>
  <tr>
    <td width="600" colspan="2" align="center">(Leaving date blank will set dates to Jan. 1 - Dec. 31)</td>
  </tr>    
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>    
  <tr>
    <td width="200" class="emptable">Year (4 digit):&nbsp;</td>
    <td width="400"><input name="new_year" type="text" size="10" maxlength="4" value="<?PHP echo $new_year; ?>"></td>
  </tr>
  <tr>
    <td width="200" class="emptable">Beginning Date:&nbsp;</td>
    <td width="400">
    	<input name="new_begin" type="text" size="15" maxlength="8" value="<?PHP echo $new_begin; ?>" readonly>
		<a href="javascript:void(0)" onclick="gfPop.fPopCalendar(document.fiscal.new_begin);return false;" HIDEFOCUS>
        <img title="Open Calendar" name="popcal" align="absmiddle" src="popCal/calbtn.gif" width="34" height="22" border="0" alt=""></a>     	
    </td>
  </tr>
  <tr>
    <td width="200" class="emptable">Ending Date:&nbsp;</td>
    <td width="400">
    	<input name="new_end" type="text" size="15" maxlength="8" value="<?PHP echo $new_end; ?>" readonly>
		<a href="javascript:void(0)" onclick="gfPop.fPopCalendar(document.fiscal.new_end);return false;" HIDEFOCUS>
        <img title="Open Calendar" name="popcal" align="absmiddle" src="popCal/calbtn.gif" width="34" height="22" border="0" alt=""></a>       	
    </td>
  </tr>
  <tr>
    <td width="600" colspan="2">&nbsp;</td>
  </tr>  
<?PHP 
//EDIT A YEAR
if(isset($edit_year)){
	echo '	
  <tr>
    <td width="600" colspan="2" align="center">
		<input name="edit_entry" type="submit" value="Update">
		&nbsp;<input name="edit_cancel" type="submit" value="Cancel">
		<input type="hidden" value="'.$year_id.'" name="year_id">
		<input type="hidden" value="'.$year_id.'" name="old_year">
	</td>
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

if($year_cnt == 1){
?>
	<p style="text-decoration:underline; margin-left: 10;"><b>Configured Years in the tables</b></p>
	<table width="600" border="0" cellspacing="0" cellpadding="1" style="margin-left: 10;" id="datatable">
	  <tr id="tableheader">
	    <th width="50">&nbsp;</th>
	    <th width="100">Year</th>
	    <th width="200">Start</th>
	    <th width="200">End</th>	    
	  </tr>
	  <?PHP echo $year_display; ?>
	</table>
	<p>&nbsp;</p>
		<!--  PopCalendar(tag name and id must match) Tags should sit at the page bottom -->
		<iframe width=174 height=189 name="gToday:company_cal2:agenda.js" id="gToday:company_cal2:agenda.js" src="popCal/cal_ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
		</iframe>
		
			
<?PHP
}else{
echo '<p style="margin-left: 10;"><b>There are currently no years entered.</b></p>';

}

require(DIR_PATH."includes/footer.inc.php");
?>