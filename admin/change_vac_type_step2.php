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
$DeptID = $_SESSION["ses_dept_id"];
$selected_year = $_REQUEST["selected_year"];
$selected_emp_id = $_REQUEST["selected_emp_id"];
$elements = $_REQUEST["elements"];
$feedback = $_GET["feedback"];
$Remove_TO = $_GET["Remove_TO"];
$date_off = $_GET["date_off"];
$cur_to_type = $_GET["cur_to_type"];
$cur_to_hours = $_GET["cur_to_hours"];
$vac_id = $_REQUEST["vac_id"];
$update_emp_to = $_POST["update_emp_to"];
$date_chosen = $_POST["date"];    	
$selected_hours_off = $_POST["hours_off"];
$old_hours_off = $_POST["old_hours"];
$selected_off_type = $_POST["off_type"];
$old_type_off = $_POST["old_type"];   

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

if($selected_emp_id == "Select an employee"){
	$message = urlencode("Please select an employee.");
	header ("Location: change_vac_type_step1.php?error_message=$message");
	exit();
}

//CREATE A DROPDOWN OF TIMEOFF TYPES AND ADD TO AN ARRAY VARIABLE
$types_dropdown = array();
$type_count = 0;
$SQL = @mysql_query("SELECT * from vf_to_type WHERE dept_id = '0' OR dept_id = '$DeptID'");
while($Result = @mysql_fetch_array($SQL)){
	$TypeID = $Result["to_id"];
    $Descr = $Result["descr"];
    $WhoEligible = $Result["all_eligible"];
    $TOYear = $Result["year"];
    $Valid_date = $Result["type_date"];
    $Dept_Time = $Result["dept_id"];

    $types_dropdown[$type_count][0] = $TypeID;
    $types_dropdown[$type_count][1] = $Descr;

    $type_count = $type_count + 1;
}

//LOOK UP THE EMPLOYEE
$get_employee = @mysql_query("SELECT fname,lname,dept_id FROM vf_employee WHERE dept_id =
			'$DeptID' AND emp_id = '$selected_emp_id'")or die("Can't retrieve employee information") ;
$emp_array = @mysql_fetch_array($get_employee);
        $emp_fname = $emp_array["fname"];
        $emp_lname = $emp_array["lname"];
        $emp_dept = $emp_array["dept_id"];
        $emp_name = $emp_fname . " " . $emp_lname;


//IF A VACATION IS DELETED RUN THIS
if(isset($Remove_TO)){

	$run = @mysql_query("DELETE FROM vf_vacation WHERE emp_id='$selected_emp_id' AND
    			 date='$date_off' AND vacation_id='$vac_id'")or die ("Can't remove vacation.");

	$new_win_header = "The following was removed by " . $_SESSION["ses_first_name"] . " " .
		$_SESSION["ses_last_name"] . " on " . date("l F d, Y");
	$new_win_content = $cur_to_hours." hours ".$cur_to_desc." removed for $emp_name for " . date("l F d, Y",strtotime($date_off));

	$hdr_addin = "<script language=\"javascript\">
		window.open('print_changed_off_time.php?content=".urlencode($new_win_content)."&header=".urlencode($new_win_header)."&ename=".urlencode($emp_name)."','newWind', \"width=350,height=400,toolbar=no,scrollbars=yes,resizable=no\")
	    </script>";
}

//IF FORM CHANGES ARE SUBMITTED RUN THIS
if(isset($update_emp_to)){

    $error_on_page = 0;
    for ($i=0; $i<$elements; $i++) {
 
		//CHECK THAT ONLY NUMBERS ARE ENTERED IN THE HOURS FIELDS
		$ValidChar = (preg_match("/^[0-9.]+$/i",$selected_hours_off[$i]));
		if($ValidChar == 0){
	    	//SET ERROR VARIABLE TO ON
			$error_on_page = 1;
		    $message[] = "No data was updated. Incorrect hours entry in the <b>" .
            		date("l F d, Y",strtotime($date_chosen[$i])) . "</b> Hours field.<br /> " .
				"<b>You entered \"".$selected_hours_off[$i]."\"</b><br />".
		       	"Only numbers are allowed. If you don't want a value enter 0.<br />".
                "You must reenter all of your changes.";
		}
	}

	//IF THERE WERE ANY ERRORS DON'T UPDATE THE TABLE AND LET THE USER KNOW
	if($message){
    	$page_error = "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$page_error .= "$numeric_text. $value ";
	            $numeric_text = $numeric_text + 1;
            }
		$page_error .= "</font><br />\n";
            unset($numeric_text);
    }else{
		$good_message =  "Information Updated For: " .$emp_name . "<br />";
    }		
	/************************************************************************
	** START ENTERING INFORMATION INTO THE DATABASE IT THERE WERE NO ERRORS**
	************************************************************************/
	 if($error_on_page == 0){

	    $are_there_changes = 0;
	    $changes_count = 1;
		$new_win_header = "The following information was updated by " . $_SESSION["ses_first_name"] . " " .
			$_SESSION["ses_last_name"] . " on " . date("l F d, Y");

		$new_win_content = "";

	    for ($i=0; $i<$elements; $i++) {
	       	if($selected_hours_off[$i] != $old_hours_off[$i] || $selected_off_type[$i] != $old_type_off[$i]){
	       		       		
			   	$run_update = @mysql_query("UPDATE `vf_vacation` SET
	        				`hours` = '".$selected_hours_off[$i]."',
	                        `to_id` = '".$selected_off_type[$i]."'
		                    WHERE `vacation_id` = '".$vac_id[$i]."'
	    					")or die ("Can't update changes.");
			}

			$new_descr = "";
	        $old_descr = "";
	        for ($b=0; $b<$type_count; $b++) {
	        	if($types_dropdown[$b][0] == $old_type_off[$i]){
					$old_descr = $types_dropdown[$b][1];
				}
	        	if($types_dropdown[$b][0] == $selected_off_type[$i]){
					$new_descr = $types_dropdown[$b][1];
				}
			}

			if($selected_hours_off[$i] != $old_hours_off[$i]){
				$new_win_content .= "<b>".$changes_count.".</b> The ".$old_descr." hours were updated from ".$old_hours_off[$i]." hours to ".$selected_hours_off[$i]." hours for $emp_name for ". date("l F d, Y",strtotime($date[$i])).".<br />";
			    $changes_count = $changes_count + 1;
			    $are_there_changes = 1;
			}

			if($selected_off_type[$i] != $old_type_off[$i]){
				$new_win_content .= "<b>".$changes_count.". ".$old_descr."</b> was changed to <b>".$new_descr."</b> for $emp_name for ". date("l F d, Y",strtotime($date[$i])).".<br />";
			    $changes_count = $changes_count + 1;
			    $are_there_changes = 1;
			}
    	}

		if($are_there_changes == 1){
			$hdr_addin = "<script language=\"javascript\">
			window.open('print_changed_off_time.php?content=".urlencode($new_win_content)."&header=".urlencode($new_win_header)."&ename=".urlencode($emp_name)."','newWind', \"width=700,height=700,toolbar=no,scrollbars=yes,resizable=no\")
		    </script>";
		}else{
			$new_win_content = "<b>No changes were made.</b>";
	   		$hdr_addin = "<script language=\"javascript\">
			window.open('print_changed_off_time.php?content=".urlencode($new_win_content)."&header=".urlencode($new_win_header)."','newWind', \"width=700,height=700,toolbar=no,scrollbars=yes,resizable=no\")
		    </script>";
	    }
	}
}


$get_inf = @mysql_query("SELECT * FROM vf_vacation WHERE emp_id = '$selected_emp_id' " .
	"AND year = $selected_year ORDER BY date ASC");
$num_rows = mysql_num_rows($get_inf);
	
if($num_rows == 0){
	$itemcnt = 0; //Count of select boxes on the current page
}else{
	$itemcnt = $num_rows; //Count of select boxes on the current page	
}

$cur_page_title = "Edit Vacation";
require(DIR_PATH."includes/header.inc.php");
	
$feedback = str_replace("\\","",$feedback);	
echo urldecode($feedback);
echo $page_error;
?>	

<p class="text18BkB">Update Information For: <?PHP echo $emp_name; ?></p><br />
<br /><a href="change_vac_type_step1.php">Select a different employee</a><br />
<form method="POST" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>"><br />
 <table border="2" cellpadding="0" cellspacing="0" width="700">
 <tr>
  <td bgcolor="#808080" style="color:#FFFFFF"><b>&nbsp;TYPE&nbsp;</b></td>
  <td bgcolor="#808080" style="color:#FFFFFF"><b>&nbsp;DATE&nbsp;</b></td>
  <td align="center" bgcolor="#808080" style="color:#FFFFFF"><b>HOURS<br />&nbsp;REQUESTED&nbsp;</b></td>
  <td bgcolor="#808080" style="color:#FFFFFF"><b>&nbsp;DELETE&nbsp;</b></td>
 </tr>
 
 
<?PHP
	$count_elements = 0;
//    $get_inf = @mysql_query("SELECT * FROM vf_vacation WHERE emp_id = '$selected_emp_id' " .
//    	"AND year = $selected_year ORDER BY date ASC");
//	$num_rows = mysql_num_rows($get_inf);
    if($num_rows == 0){
    	echo '</table><p class="textError" style="margin-left:15;">No information matches your criteria. <a href="change_vac_type_step1.php">Select a different employee or year.</a></p>';
	}else{
		while($inf_array = @mysql_fetch_array($get_inf)){
	        $vac_id = $inf_array["vacation_id"];
			$date_off = $inf_array["date"];
			$hours_off = $inf_array["hours"];
			$time_off_type = $inf_array["to_id"];
			$vac_deny = $inf_array["deny"];
			
			$date_off_fmt = date("l F d, Y",strtotime($date_off));

	        $to_type_box[$count_elements] = "";
			$to_type_box[$count_elements] .= '<div id="HideItem'.$count_elements.'" style="POSITION:relative"><select size="1" name="off_type['. $count_elements . ']">';

			//CREATE A DROP DOWN OF TIME OFF TYPES AND SELECTED THE CURRENT TYPE
	        for ($i=0; $i<$type_count; $i++) {
    	    	if($types_dropdown[$i][0] == $time_off_type){
					$select_type = "selected";
	                //FILL VARIALES TO PASS WHEN SUBMITTED
					$cur_type_descr[$count_elements] = $types_dropdown[$i][1];
					$old_type_descr[$count_elements] = $types_dropdown[$i][1];
					$old_type[$count_elements] = $types_dropdown[$i][0];
				}
	            $to_type_box[$count_elements] .= '<option value="' . $types_dropdown[$i][0] .'" '
	             	. $select_type .'>' . $types_dropdown[$i][1] . '</option>';
				$select_type = "";
	        }
	        $to_type_box[$count_elements] .= '</select></div>';

	        $feedback = urlencode('<font size="4" color="#FF0000">Time off removed for '.$emp_name.' on '.$date_off_fmt.'</font><br /><br />');

			echo '<tr>
    			  <td>
                    <input type="hidden" value="' . $vac_id . '" name="vac_id[' . $count_elements . ']">
                    <input type="hidden" value="' . $old_type[$count_elements] . '" name="old_type[' . $count_elements . ']">
                    <input type="hidden" value="' . $date_off . '" name="date[' . $count_elements . ']">
                  	' . $to_type_box[$count_elements] . '
                   </td>
                  <td>
                  	' . $date_off_fmt;
			if($vac_deny == "Y"){
				echo '<span style="color: red;">&nbsp;Denied</span>';
			}
             echo '
                  </td>
                  <td align="center">
                    <input type="hidden" value="' . $hours_off . '" name="old_hours[' . $count_elements . ']">
                  	<input style="text-align: Center;" type="text" value="' . $hours_off . '"
                     name="hours_off['. $count_elements . ']" size="6">
                  </td>
                  <td align="center">
                    <a href="' . $_SERVER["PHP_SELF"].'?feedback='.$feedback.'&Remove_TO=yes&selected_emp_id='.$selected_emp_id.
					'&elements='.$count_elements.'&date_off='. $date_off.'&cur_to_type='.$time_off_type.
                    '&cur_to_hours='.$hours_off.'&vac_id='.$vac_id. '&selected_year='.$selected_year.
					'" onclick="javascript:return confirm(\'Are you sure you want to delete '.$date_off.' ?\')">
                    Delete</a>
                  </td>
				 </tr>';

	    $count_elements = $count_elements + 1;
	    }

		echo '</table><p>
	        <input type="hidden" value="' . $selected_emp_id . '" name="selected_emp_id">
	        <input type="hidden" value="' . $count_elements . '" name="elements">
	        <input type="hidden" value="' . $selected_year . '" name="selected_year">		
	        <input type="submit" value="Update Information" name="update_emp_to"></p>
	        </form>';
	}//END OF num_rows IF STATEMENT

require(DIR_PATH."includes/footer.inc.php");
?> 