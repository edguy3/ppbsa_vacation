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

//SET PAGE VARIBALES
$DeptID = $_SESSION["ses_dept_id"];
$weeks_to_display = $_POST["weeks_to_display"];
$sunday_choice = $_POST["sunday_choice"];
$date_selection = $_POST["date_selection"];
$submit = $_POST["submit"];
$dept_choice = $_POST["dept_choice"];
$selected_date = $sunday_choice;

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$cur_page_title = "Weekly Vacation Report";
if(!isset($date_selection)){
	$itemcnt = 2; //Count of select boxes on the current page
}
require(DIR_PATH."includes/header.inc.php");

/*******************************************************************************
** DISPLAY A LIST OF OPTIONS TO THE USER TO SELECT IF ALREADY SELECTED        **
** DISPLAY THE INFORMATION                                                    **
*******************************************************************************/
if(!$date_selection){

	/************ BUILD A DROP DOWN LIST OF SUNDAY DATES ****************/
	//CONVERT TODAYS DATE TO A STRING TO CALCULATE BELOW
	$today = date("m/d/y",time());
	$mydate = getdate(time());
	//GET THE NUMBER OF THE DAY OF THE WEEK TO SUBTRACT FROM TODAY AND GET SUNDAYS DATE
	$day_num = $mydate [ "wday" ];
	//CREATE VARIABLE TO HOLD MINUS DAYS
	$sub_days = "-".$day_num."days";
	//GET SUNDAYTS DATE IN SECONDS SINCE 1970
	$sunday = date(strtotime("$today $sub_days"));
	$this_sunday_date = date("m/d/Y", $sunday);

	//SET VARIBALE TO DISPLAY THE DATE BESIDE THE WEEKDAY ON THE DISPLAY FORM
	if(isset($sunday_choice)){
		$sunday_display = strtotime($sunday_choice);
	}else{
		$sunday_display = $sunday;
	}

	//GET SUNDAYS DATE FROM ONE YEAR AGO
	$last_year = mktime (0,0,0,date("m"),  date("d"),  date("Y")-1);
	$mydate = getdate ($last_year);
	$day_num = $mydate [ "wday" ];
	//CREATE VARIABLE TO HOLD MINUS DAYS
	$sub_days = "-".$day_num."days";
    $last_year = date("m/d/y",$last_year);
	$sunday = date(strtotime("$last_year $sub_days"));
    $sunday_date = date("m/d/Y", $sunday);

	//START BUILDING THE DROP DOWN
	$sunday_box = '<select size="1" name="sunday_choice">';

	//LOAD TWO YEARS OF DATES STARTING FROM ONE YEAR AGO
	for ($i=0; $i<204; $i++) {
		$sunday_date = date("m/d/Y", strtotime ("$sunday_date +1 week"));

		if(isset($sunday_choice)){
			$sunday_choice = date("m/d/Y", strtotime($sunday_choice));
			if($sunday_date == $sunday_choice){
		    	$selected = "selected";
			}
		}elseif($sunday_date == $this_sunday_date){
	    	$selected = "selected";//IF THE DATE IS LAST SUNDAYS DATE SELECT IT
		}
	    $DateValue = date("Ymd", strtotime ("$sunday_date"));
		$sunday_box .= "<option $selected value=\"$DateValue\">$sunday_date</option>\n";
	    //RESET THE VARIABLE
		$selected = "";
	}
	//CLOSE THE DROP DOWN
	$sunday_box .= "</select>";
	/************ END OF BUILD A DROP DOWN LIST OF SUNDAY DATES ****************/

	/************ BUILD A DROP DOWN LIST OF SUB DEPTS ****************/

	//START BUILDING THE DROP DOWN
	$dept_box = '<select size="1" name="dept_choice">
                <option value="default'.$DeptID.'">All of the '.$_SESSION["ses_dept_name"].' Department</option>';
	//GET SUB DEPTS FOR THE CURRENT DEPT.
	$ck_sub = @mysql_query("SELECT * FROM vf_sub_dept WHERE dept_id = '$DeptID'");
	while($sub_array = @mysql_fetch_array($ck_sub)){
		$sub_dept_id = $sub_array["sub_dept_id"];
		$sub_dept_code = $sub_array["code"];

	    $dept_box .= '<option value="'.$sub_dept_id.'">'.$sub_dept_code.'</option>';
	}

    $dept_box .= '</select>';
	/************ END OF BUILD A DROP DOWN LIST OF SUB DEPTS ****************/

    //DISPLAY SELECTION OPTIONS
	echo '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
            <input type="hidden" value="set" name="date_selection">
	        &nbsp;
	        <table border="0" cellpadding="0" cellspacing="0" width="700">
	          <tr>
	            <td></td>
	          </tr>
	          <tr>
	            <td>Select the starting Sunday for your report: <span id="HideItem0" style="POSITION:relative">'.$sunday_box.'</span>
			  </td>
	          </tr>
	          <tr>
	            <td>&nbsp;</td>
	          </tr>
	          <tr>
	            <td>Enter the number of&nbsp; weeks to display:
                <input type="text" value="1" name="weeks_to_display" size="5"></td>
	          </tr>
	          <tr>
	            <td>&nbsp;</td>
	          </tr>
	          <tr>
	            <td>View report for which department: <span id="HideItem1" style="POSITION:relative">'.$dept_box.'</span></td>
	          </tr>
	          <tr>
	            <td>&nbsp;
                </td>
	          </tr>
	          <tr>
	            <td><input type="submit" value="View Report" name="submit">
	            </td>
	          </tr>
 	        </table>
            </form>';

}else{

    //ONCE THE USER HAS MADE SELECTIONS. DISPLAY THE INFORMATION.
	$error_on_page = 0;

    //CHECK THAT ONLY NUMBERS ARE ENTERED IN THE HOURS FIELD
	$ValidChar = (preg_match("/^[0-9]+$/i",$weeks_to_display));
	if($ValidChar == 0){
    	//SET ERROR VARIABLE TO ON
		$error_on_page = 1;
	    $message[] = "You must enter a numeric value in the weeks to display field.<br />";
	}

	//IF THERE WERE ANY ERRORS DON'T UPDATE THE TABLE AND LET THE USER KNOW
	if($message){
    	$error_mssage = "<div align=\"left\"><font size=\"5\" color=\"red\"><b>The following problems
        	occurred:</b></font><br /><font color=\"red\">\n";
            $numeric_text = 1;
            foreach($message as $key => $value){
            	$error_mssage .= "$numeric_text. $value ";
	            $numeric_text = $numeric_text + 1;
            }
		$error_mssage .= "</font><br />\n";
        unset($numeric_text);
    }

	echo  '<p><a href="report_weekly.php">Select another group or time frame</a><br />
				<a href="javascript:printFriendly()">Printer Friendly</a><br />
				<a href="javascript:printpdf()">Printer Friendly (PDF)</a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>
                '.date("l \- F dS, Y \- g:i a").'</b>
		   </p>';

	//FORMAT THE SUNDAY THAT WAS CHOSEN
	$sunday_display = strtotime("$sunday_choice");
    $sunday_choice = date("m/d/y",$sunday_display);

    //DO THIS FOR THE NUMBER OF WEEKS TO DISPLAY
    for ($a=0; $a<$weeks_to_display; $a++) {

        //DO THIS FOR THE 7 DAYS OF THE WEEK
	    for ($i=0; $i<7; $i++) {
            //USE TO CALCULATE EACH OF THE & DAYS
            $add_days = "+".$i."days";
            $current_day = date("Ymd",strtotime("$sunday_choice $add_days"));

            

            
            

            echo '
            <table border="1" cellpadding="0" cellspacing="0" width="650" style="border-color:#000000;">
			  <tr>
			    <td>
			      <table border="0" cellpadding="0" cellspacing="0" width="650">
			        <tr>
			          <td><b>&nbsp;'.date("l F d, Y",strtotime($current_day)).'</b></td>
			        </tr>
			        <tr>
			          <td>
			            &nbsp;
			          </td>
			        </tr>
			 	  </table>
			      <table border="0" cellpadding="0" cellspacing="0" width="650">
			        <tr>
			          <td align="center" width="10" style="text-decoration:underline;"></td>
			          <td align="center" width="140" style="text-decoration:underline;"><b>NAME</b></td>
			          <td align="center" width="100" style="text-decoration:underline;"><b>TYPE</b></td>
			          <td align="center" width="50" style="text-decoration:underline;"><b>HOURS</b></td>
			          <td align="center" width="140" style="text-decoration:underline;"><b>RELIEF</b></td>
			          <td align="center" width="200" style="text-decoration:underline;"><b>NOTES</b></td>
			          <td align="center" width="10" style="text-decoration:underline;"></td>
			        </tr>
              ';

            //IF THE WHOLE DEPT IS TO BE DISPLAYED USE THIS OTHERWISE USE THE SECOND PART
			//TO SELECT BY SUB DEPT
            if($dept_choice == "default".$DeptID){
		    	$sql_people = mysql_query("SELECT * FROM vf_vacation WHERE date = '$current_day'
                		AND dept_id = '$DeptID' ORDER BY date_entered ASC");
			}else{
		    	$sql_people = mysql_query("SELECT * FROM vf_vacation,vf_employee WHERE vf_vacation.date = '$current_day'
                		AND vf_vacation.dept_id = '$DeptID' AND vf_employee.sub_dept_id = '$dept_choice'
                        AND vf_vacation.emp_id = vf_employee.emp_id ORDER BY vf_vacation.date_entered ASC");
			}

            while($people_array = mysql_fetch_array($sql_people)){
				$vac_emp_id = $people_array["emp_id"];
				$vac_hours = $people_array["hours"];
                $vac_to_id = $people_array["to_id"];
                $vac_apprv_by = $people_array["apprv_by"];
                $vac_replacement = $people_array["replacement"];
                $vac_note = $people_array["note"];
                $vac_deny = $people_array["deny"];

                //GET THE VACATION TYPE
                $get_vacation = @mysql_query("SELECT descr FROM vf_to_type WHERE to_id = '$vac_to_id'");
                $vacation_name = mysql_fetch_array($get_vacation);
					$vacation_description = $vacation_name["descr"];

				if($vac_replacement == ""){
					$vac_replacement = "&nbsp;";
				}

				if($vac_note == ""){
					$vac_note = "&nbsp;";
				}

                //GET THE EMPLOYEES NAME
                $get_employee = @mysql_query("SELECT fname,lname,enabled FROM vf_employee WHERE
                		emp_id = '$vac_emp_id'");
                $emp_name = mysql_fetch_array($get_employee);
                	$emp_lname = $emp_name["lname"];
                	$emp_fname = $emp_name["fname"];
                	$emp_enabled = $emp_name["enabled"];
					$emp_full_name = $emp_fname . " " . $emp_lname;
					
				//ONLY DISPLAY DATA IF THE USER IS ENABLED	
				if($emp_enabled != 'N'){
	            	echo '
			        <tr>
			          <td class="text9Bk" align="center" width="10"></td>
			          <td class="text9Bk" align="center" width="140" style="border-bottom: 1px solid black">'.$emp_full_name.'</td>
			          <td class="text9Bk" align="center" width="100" style="border-bottom: 1px solid black">'.$vacation_description.'</td>
			          <td class="text9Bk" align="center" width="50" style="border-bottom: 1px solid black">'.$vac_hours.'</td>
	            	  <td class="text9Bk" align="center" width="140" style="border-bottom: 1px solid black">'.$vac_replacement.'</td>';            		
	            	if($vac_deny == "Y"){
	            		echo '<td class="text9Bk" align="center" width="200" style="border-bottom: 1px solid black"><span style="color: red">Time off denied</span></td>';
	            	}else{
	            		echo '<td class="text9Bk" align="center" width="200" style="border-bottom: 1px solid black">'.$vac_note.'</td>';            		
	            	}
	            	echo '
			          <td class="text9Bk" align="center" width="10"></td>
			        </tr>';
				}

            }
			echo '
		        <tr>
		          <td colspan="6">&nbsp;</td>
		        </tr>
	   		   </table>
			 </td>
			</tr>
	        </table><br />';
    	}
      $sunday_choice = date("m/d/y",strtotime("$sunday_choice +7days"));
	}

} //END OF ELSE
 echo '
 <form method="POST" action="report_weekly_pf.php" target="_blank" name="printer">
	<input type="hidden" name="weeks_to_display" value="'.$weeks_to_display.'">
	<input type="hidden" name="sunday_choice" value="'.$selected_date.'"> 
	<input type="hidden" name="date_selection" value="'.$date_selection.'"> 
	<input type="hidden" name="dept_choice" value="'.$dept_choice.'"> 
 </form>
 
 <script language="JavaScript" type="text/javascript">
   function printFriendly(){ 
    document.printer.submit()
   }
 </script> 
 

 <form method="POST" action="report_weekly_pdf.php" target="_blank" name="prtpdf">
	<input type="hidden" name="weeks_to_display" value="'.$weeks_to_display.'">
	<input type="hidden" name="sunday_choice" value="'.$selected_date.'"> 
	<input type="hidden" name="date_selection" value="'.$date_selection.'"> 
	<input type="hidden" name="dept_choice" value="'.$dept_choice.'"> 
 </form> 
 
 <script language="JavaScript" type="text/javascript">
   function printpdf(){ 
    document.prtpdf.submit()
   }
 </script>  
     
 ';
require(DIR_PATH."includes/footer.inc.php");

?>