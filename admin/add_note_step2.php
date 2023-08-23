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
$selected_year = $_POST["selected_year"];
$selected_emp_id = $_POST["selected_emp_id"];
$DeptID = $_SESSION["ses_dept_id"];//Set department
$update_note = $_POST["update_note"];
$selected_emp_id = $_POST["selected_emp_id"];
$elements = $_POST["elements"];
$note = $_POST["note"];
$relief = $_POST["relief"];
$date = $_POST["date"];


require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

if($selected_emp_id == "Select an employee"){
	$message = urlencode ("Please select an employee.");
	header ("Location: add_note_step1.php?error_message=$message");
	exit();
}


//LOOK UP THE EMPLOYEE
$get_employee = @mysql_query("SELECT fname,lname FROM vf_employee WHERE dept_id = '$DeptID' AND emp_id = '$selected_emp_id'");
while($emp_array = @mysql_fetch_array($get_employee)){
        $emp_fname = $emp_array["fname"];
        $emp_lname = $emp_array["lname"];
        $emp_name = $emp_fname . ", " . $emp_lname;
}

//IF FORM IS SUBMITTED. UPDATE.
if(isset($update_note)){

    for ($i=1; $i<$elements; $i++) {
    	//if($note[$i] != "" || $relief[$i] != ""){
   	        //INSERT VALUES INTO THE EMPLOYEE DATABASE
	        $result = @mysql_query("UPDATE vf_vacation SET
				note = '$note[$i]',
	            replacement = '$relief[$i]'
				WHERE emp_id = '$selected_emp_id' AND date = '$date[$i]'")
	            or die ("Unable to update employee");
    	//}            
	}
    $good_message =  "Information Updated For: " .$emp_name . "<br />";
}

$cur_page_title = "Add notes";
require(DIR_PATH."includes/header.inc.php");
?>

<p class="text18BkB">Update Information For: <?PHP echo $emp_name; ?></p>
<p><a href="add_note_step1.php">Select a different employee</a></p>
<form method="POST" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>">
<table border="2" cellpadding="0" cellspacing="0" width="700">
 <tr>
  <td style="background-color:#808080; color:#FFFFFF;"><b>&nbsp;TYPE&nbsp;</b></td>
  <td style="background-color:#808080; color:#FFFFFF;"><b>&nbsp;DATE&nbsp;</b></td>
  <td align="center"  style="background-color:#808080; color:#FFFFFF;"><b>&nbsp;HOURS&nbsp;</b></td>
  <td style="background-color:#808080; color:#FFFFFF;"><b>&nbsp;NOTES&nbsp;</b></td>
  <td style="background-color:#808080; color:#FFFFFF;"><b>&nbsp;REPLACEMENT&nbsp;</b></td>
 </tr>
<?PHP 
$count_elements = 1;
//LOOK UP THE TIME OFF TYPE AND DESCRIPTION
$SQL = @mysql_query("SELECT * from vf_to_type");
while($Result = @mysql_fetch_array($SQL)){
	$TypeID = $Result["to_id"];
    $Descr = $Result["descr"];
    $WhoEligible = $Result["all_eligible"];
    $TOYear = $Result["year"];
    $Valid_date = $Result["type_date"];
    $Dept_Time = $Result["dept_id"];

   $get_inf = @mysql_query("SELECT * FROM vf_vacation WHERE emp_id = '$selected_emp_id' " .
      			"AND to_id = '$TypeID' AND year = '$selected_year' ORDER BY date DESC");
	while($inf_array = @mysql_fetch_array($get_inf)){
		$date_off = $inf_array["date"];
		$hours_off = $inf_array["hours"];
        $note = $inf_array["note"];
        $relief = $inf_array["replacement"];
        $vac_deny = $inf_array["deny"];

		$date_off_fmt = date("l F d, Y",strtotime($date_off));

		echo '<tr>
			  <td>
                <input type="hidden" value="' . $date_off . '" name="date[' . $count_elements . ']">
              	' . $Descr . '
               </td>
              <td>
              	' . $date_off_fmt . '
              </td>
              <td align="center">
              	' . $hours_off . '
              </td>
              <td>
              	<textarea rows="2" name="note[' . $count_elements . ']" cols="30">' . $note . '</textarea>
              </td>';
		if($vac_deny == "Y"){
			echo '
			  <td><span style="color: red;">Time off denied</span>
            	&nbsp;<input type="hidden" value="' . $relief . '" name="relief[' . $count_elements . ']" size="20">&nbsp;
              </td>';			
		}else{
			echo'
			  <td>
            	&nbsp;<input type="text" value="' . $relief . '" name="relief[' . $count_elements . ']" size="20">&nbsp;
              </td>';
		}
		
		echo '</tr>';
    	$count_elements = $count_elements + 1;
    }
}
?>

	</table>
<?PHP	
if($count_elements == 1){
?>
	<p class="textError" style="margin-left:15;">No information matches your criteria. <a href="add_note_step1.php">Select a different employee or year.</a></p>
<?PHP 	
}else{	
?>	
	 <p>
        <input type="hidden" value="<?PHP echo $selected_emp_id; ?>" name="selected_emp_id">
        <input type="hidden" value="<?PHP echo $count_elements; ?>" name="elements">
        <input type="hidden" value="<?PHP echo $selected_year; ?>" name="selected_year">
        <input type="submit" value="Update Information" name="update_note">
     </p>
<?PHP 
}
?>
  </form>
    
<?PHP     
require(DIR_PATH."includes/footer.inc.php");
?>