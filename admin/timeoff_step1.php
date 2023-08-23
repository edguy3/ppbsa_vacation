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
if($_SESSION["ses_is_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

//SET PAGE VARIABLES
$category = $_GET["category"];
$to_choice = $_GET["to_choice"];
$selected_year = $_GET["selected_year"];
$error_message = $_REQUEST["error_message"];


require(DIR_PATH."includes/db_info.inc.php");//database connection information

$cur_page_title = "Update time off hours";
$hdr_detail = "Update Time Off Earned Hours";
$itemcnt = 4; //Count of select boxes on the current page
require(DIR_PATH."includes/header.inc.php");


/* BUILD A DROP DOWN OF FISCAL YEARS IN THE DATABASE */
$TodaysDate=date("Ymd");

$year_box = "<option>Select fiscal year</option>\n";
$GetYr = @mysql_query("SELECT * FROM vf_year ORDER BY year ASC");
while($YrArray = @mysql_fetch_array($GetYr)){
	$CurYear = $YrArray["year"];
	$YrStart = $YrArray["start"];
    $YrEnd =  $YrArray["end"];

    $YrStart = date("Ymd",strtotime($YrStart));
    $YrEnd = date("Ymd",strtotime($YrEnd));

	if($this_year == $CurYear){
	 	$select = "selected";
	}

    //IF TODAY IS NOT PAST THE END DATE DISPLAY THE YEAR
    if($YrEnd >= $TodaysDate){
		$year_box .= "<option $select >$CurYear</option>\n";
    }

 	$select = "";
}

/* BUILD A DROP DOWN OF TIME OFF TYPES */
$to_box = "";
$to_box = '<option>Select a time off type</option>';
$get_time_off = @mysql_query("SELECT * FROM vf_to_type WHERE `earned` = 'Y' ORDER BY descr ASC ");
while($to_array = @mysql_fetch_array($get_time_off)){
	$current_to_id = $to_array["to_id"];
	$current_to_descr = $to_array["descr"];

	if($to_choice == $current_to_id){
	 	$select = "selected";
	}
	$to_box .= "<option $select value=\"$current_to_id\">$current_to_descr</option>\n";

	$select = "";
}

/* BUILD A DROP DOWN OF JOB CATEGORIES */
$category_box = "";
$category_box .= "<option>Select employee category</option>";

$cat_sql = @mysql_query("SELECT `cat_id`,`descr` FROM `vf_category`");
while($cat_result = @mysql_fetch_array($cat_sql)){
	$cat_id = $cat_result["cat_id"];
	$cat_desc = $cat_result["descr"];

	if($category == $cat_id){
		$selected = "selected";
	}else{
		$selected = "";		
	}
	$category_box .= '<option value="'.$cat_id.'" '.$selected.'>'.$cat_desc.'</option>';
}

//BUILD A DROP DOWN OF DEPARTMENTS
$department_box = "";
$department_box .= '<option value="all">All</option>';
$dept_sql = @mysql_query("SELECT `dept_id`, `descr` FROM `vf_department`");
while($dept_result = @mysql_fetch_array($dept_sql)){
	$dept_id = $dept_result["dept_id"];
	$dept_descr = $dept_result["descr"];
	
	if($department == $dept_id){
		$selected = "selected";
	}else{
		$selected = "";		
	}
	
	$department_box .= '<option value="'.$dept_id.'" '.$selected.'>'.$dept_descr.'</option>';


}

//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
if($_SESSION["ses_super_admin"] == 1){
    //CREATE A VARIABLE WITH ALL THE PAGE DISPLAY INFORMATION
	echo '
		<form name="timeoff" method="POST" action="timeoff_step2.php">
		  <table border="0" cellpadding="0" cellspacing="2" width="700" style="margin-left: 10;" id="datatable">
		    <tr>
		      <td colspan="2">&nbsp;</td>
		    </tr>	
		    <tr>
		      <td class="emptable">Select the fiscal year:&nbsp;</td> 
				<td><div id="HideItem0" style="POSITION:relative">	 
				<select size="1" name="selected_year">
				'.$year_box.'
		       </select></div>
              </td>
		    </tr>
		    <tr>
		      <td class="emptable">Select employee category to update:&nbsp;</td>
			  <td>
	   			<div id="HideItem1" style="POSITION:relative">
                <select size="1" name="category">
				'.$category_box.'
		       </select>
			  </div>
             </td>
		    </tr>
		    <tr>
		      <td class="emptable">Select type to update:&nbsp;</td>
			  <td>
				<div id="HideItem2" style="POSITION:relative">
                <select size="1" name="to_choice">
				'.$to_box.'
		        </select>
				</div>
			  </td>
		    </tr>
		    <tr>
		      <td class="emptable">Select a department:&nbsp;</td>
			  <td>
				<div id="HideItem3" style="POSITION:relative">
                <select size="1" name="department">
				'.$department_box.'
		        </select>
				</div>
			  </td>
		    </tr>				
		    <tr>
		      <td colspan="2">&nbsp;</td>
		    </tr>
		    <tr>
		      <td colspan="2" align="center"><input type="submit" value="Go To Step 2..." name="update_timeoff"></td>
		    </tr>
		    <tr>
		      <td colspan="2">&nbsp;</td>
		    </tr>					
		  </table>
		  <p>&nbsp;</p>
		</form>';

}else{
	echo 'At this time, only Human Resourses can make changes to employees.';
}


require(DIR_PATH."includes/footer.inc.php");
?>