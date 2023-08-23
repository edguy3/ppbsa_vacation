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
$DeptID = $_SESSION["ses_dept_id"];//Set department
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];
$LastDate = $_SESSION["ses_lastdate"];
$sunday_start = $_POST["sunday_start"];
$sunday_end = $_POST["sunday_end"];
$dept_choice = $_POST["dept_choice"];
$date_selection = $_POST["date_selection"];
$approve_request = $_POST["approve_request"];
$total_requests = $_POST["total_requests"];
$vacation_id = $_POST["vacation_id"];
$confirm = $_POST["confirm"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year
require_once(DIR_PATH."includes/config.inc.php");

//IF THE FORM IS SUBMITTED. PROCESS IT
if(isset($approve_request)){
	//GET THE CURRENT TIME AND DATE TO USE AS TIME APPROVED ENTRY
	$cur_date_time = date("YmdHis");

    //APPROVE ALL CHECKED ITEMS
	for($i=0; $i<$total_requests; $i++) {
			
       if($_POST["confirm"][$i] == "on"){
       	
			$vacation_listing = $_POST["vacation_id"][$i];

        	$create_appoval = @mysql_query("UPDATE `vf_vacation` SET
						 apprv_by = '$_SESSION[ses_emp_id]',
                         date_approved = '$cur_date_time'
                         WHERE vacation_id = '$vacation_listing'")or die
                        ("Can't update information. Contact a site administrator.");
                         
            $vacation_date = @mysql_query("SELECT `hours`,`date`,`emp_id` FROM `vf_vacation` 
            			WHERE `vacation_id` = '$vacation_id[$i]'");             
            $vacation_result = @mysql_fetch_array($vacation_date);
            	$app_hours = $vacation_result["hours"];
            	$app_date = $vacation_result["date"];
            	$emp_id = $vacation_result["emp_id"];
            	//CONVERT DATES
            	$now = date("F j, Y, g:i a");
            	$ap_dte = strtotime($app_date);
            	$ap_dte = date("F j, Y", $ap_dte);

            	//RETRIEVE EMAIL ADDRESS
            	$email_sql = @mysql_query("SELECT `email` FROM `vf_employee` WHERE emp_id = '$emp_id'");
            	$email_result = @mysql_fetch_array($email_sql);
            		$email_addr = $email_result["email"];
            	
            	//SEND EMPLOYEE AN EMAIL OF THE APPROVAL IF HE HAS AN EMAIL ADDRESS		
			if($email_addr != ""){	                         
	            $to = "$email_addr";
				$subject = $cfg['company_abbr'] ." Time Off Request - Approval";
				$body = "Your time off request for $app_hours hours on $ap_dte was
            	 approved by $user_name on $now";
			   	mail($to, $subject, $body, "From: ".$cfg['email_reply_addr']);                         
			}                                                  
		}elseif($_POST["confirm"][$i] == "no"){

			$vacation_listing = $_POST["vacation_id"][$i];

        	$create_appoval = @mysql_query("UPDATE `vf_vacation` SET
						 apprv_by = '$_SESSION[ses_emp_id]',
        				 deny = 'Y',
        				 deny_reason = '".$_POST["reason"][$i]."',
        				 hours = '0', 
                         date_approved = '$cur_date_time'
                         WHERE vacation_id = '$vacation_listing'")or die
                        ("Can't update information. Contact a site administrator.");
                        
            $vacation_date = @mysql_query("SELECT `hours`,`date`,`emp_id` FROM `vf_vacation` 
            			WHERE `vacation_id` = '$vacation_id[$i]'");             
            $vacation_result = @mysql_fetch_array($vacation_date);
            	$app_hours = $vacation_result["hours"];
            	$app_date = $vacation_result["date"];
            	$emp_id = $vacation_result["emp_id"];
            	//CONVERT DATES
            	$now = date("F j, Y, g:i a");
            	$ap_dte = strtotime($app_date);
            	$ap_dte = date("F j, Y", $ap_dte);

            	//RETRIEVE EMAIL ADDRESS
            	$email_sql = @mysql_query("SELECT `email` FROM `vf_employee` WHERE emp_id = '$emp_id'");
            	$email_result = @mysql_fetch_array($email_sql);
            		$email_addr = $email_result["email"];
            		      	
            	//SEND EMPLOYEE AN EMAIL OF THE APPROVAL IF HE HAS AN EMAIL ADDRESS		
			if($email_addr != ""){	                         
	            $to = "$email_addr";
				$subject = $cfg['company_abbr'] ." Time Off Request - Denial";
				$body = "Your time off request for $ap_dte was
            	 denied by $user_name on $now. Please contact for more information.";
			   	mail($to, $subject, $body, "From: ".$cfg['email_reply_addr']);   				   	
			}           
                        
		}
	}
}

$cur_page_title = "Review time off request";
if(! isset($date_selection)){
	$itemcnt = 3; //Count of select boxes on the current page
}
require(DIR_PATH."includes/header.inc.php");




/*******************************************************************************
** DISPLAY A LIST OF OPTIONS TO THE USER TO SELECT. IF ALREADY SELECTED        **
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


	//GET SUNDAYS DATE FROM TWO MONTHS AGO
	$last_year = mktime (0,0,0,date("m")-2,  date("d"),  date("Y"));
	$mydate = getdate ($last_year);
	$day_num = $mydate [ "wday" ];
	//CREATE VARIABLE TO HOLD MINUS DAYS
	$sub_days = "-".$day_num."days";
    $two_months = date("m/d/y",$last_year);
	$sunday = date(strtotime("$two_months $sub_days"));
	$sunday_start_date = date("m/d/Y", $sunday);
	$sunday_end_date = date("m/d/Y", $sunday);

	//*************************************
	//START BUILDING THE STARTING DROP DOWN
	$sunday_start = '<select size="1" name="sunday_start">';

	//LOAD ONE AND A HALF YEAR OF DATES STARTING FROM ONE YEAR AGO
	for ($i=0; $i<78; $i++) {
		$sunday_start_date = date("m/d/Y", strtotime ("$sunday_start_date -1 week"));

		if(isset($sunday_choice)){
			$sunday_choice = date("m/d/Y", strtotime($sunday_choice));
			if($sunday_start_date == $sunday_choice){
		    	$selected = "selected";
			}
		}elseif($sunday_date == $this_sunday_date){
	    	$selected = "selected";//IF THE DATE IS LAST SUNDAYS DATE SELECT IT
		}
	    $DateValue = date("Ymd", strtotime ("$sunday_start_date"));
		$sunday_start .= "<option $selected value=\"$DateValue\">$sunday_start_date</option>\n";
	    //RESET THE VARIABLE
		$selected = "";
	}
	//CLOSE THE DROP DOWN
	$sunday_start .= "</select>";

    //***********************************
	//START BUILDING THE ENDING DROP DOWN
	$sunday_end = '<select size="1" name="sunday_end">';

	//LOAD ONE AND A HALF YEAR OF DATES STARTING FROM ONE YEAR AGO
	for ($i=0; $i<78; $i++) {
		$sunday_end_date = date("m/d/Y", strtotime ("$sunday_end_date +4 months"));

		if(isset($sunday_choice)){
			$sunday_choice = date("m/d/Y", strtotime($sunday_choice));
			if($sunday_end_date == $sunday_choice){
		    	$selected = "selected";
			}
		}elseif(date("Y-m-d", strtotime ("$sunday_end_date")) == $LastDate){
	    	$selected = "selected";//IF THE DATE IS LAST SUNDAY OF THE YEAR SELECT IT
		}
	    $DateValue = date("Ymd", strtotime ("$sunday_end_date"));
		$sunday_end .= "<option $selected value=\"$DateValue\">$sunday_end_date</option>\n";
	    //RESET THE VARIABLE
		$selected = "";
	}
	//CLOSE THE DROP DOWN
	$sunday_end .= "</select>";


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
            <input type="hidden" value="set" name="date_selection"><p>&nbsp;</p>
	        <table border="0" cellpadding="0" cellspacing="0" width="600"  style="margin-left: 100;"">
	          <tr>
	            <td></td>
	          </tr>
	          <tr>
	            <td>Review requests from <span id="HideItem0">'.$sunday_start.'</span> through <span id="HideItem1">'.$sunday_end.'</span>
			  </td>
	          </tr>
	          <tr>
	            <td>&nbsp;</td>
	          </tr>
	          <tr>
	            <td>View report for which department: <span id="HideItem2">'.$dept_box.'</span></td>
	          </tr>
	          <tr>
	            <td>&nbsp;</td>
	          </tr>
	          <tr>
	            <td align="center">
                	<input type="submit" value="Review pending requests" name="submit">
	            </td>
	          </tr>
 	        </table>
            </form>';

}else{

	$loop1 = 0;
	echo '<p><a href="apprv_pending_vacations.php">Select another group or time frame</a></p>';

	//FORMAT THE SUNDAY THAT WAS CHOSEN
 	$str_sunday_start = strtotime("$sunday_start");
    $sunday_start = date("Ymd",$str_sunday_start);

	echo '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';

    //INITIALIZE VARIABLE TO INCREMENT THE RADIO BUTTON COUNT
    $apprv_cnt = 0;
	//LOOP THROUGH CHOSEN DATES
    while($sunday_start <= $sunday_end){

        //IF THE WHOLE DEPT IS TO BE DISPLAYED USE THIS OTHERWISE USE THE SECOND PART
		//TO SELECT BY SUB DEPT
        if($dept_choice == "default".$DeptID){
		   	$sql_people = mysql_query("SELECT * FROM vf_vacation WHERE date = '$sunday_start'
               		AND dept_id = '$DeptID' AND apprv_by = '0' ORDER BY date,date_entered");
		}else{
		   	$sql_people = mysql_query("SELECT * FROM vf_vacation,vf_employee WHERE vf_vacation.date = '$sunday_start'
               		AND vf_vacation.dept_id = '$DeptID' AND vf_employee.sub_dept_id = '$dept_choice'
                       AND vf_vacation.emp_id = vf_employee.emp_id AND apprv_by = '0' ORDER BY date,date_entered");
		}

		$vacation_list = "";
        $request_count = 0;
        while($people_array = mysql_fetch_array($sql_people)){
			$vac_id = $people_array["vacation_id"];
			$vac_emp_id = $people_array["emp_id"];
			$vac_hours = $people_array["hours"];
            $vac_to_id = $people_array["to_id"];
            $vac_apprv_by = $people_array["apprv_by"];
			$vac_request_time = $people_array["date_entered"];

            //FORMAT REQUEST TIME
			$vac_request_time = date("F j, Y g:i:sa",strtotime($vac_request_time));

			//MAKE CHECKS TO SEE IF THE USER CAN CHANGE THEIR OWN VACTION
			//IF THE USERS SUPERVISOR IS SET TO N/A THE USER CAN MANAGE THEIR OWN VACATION
			if($_SESSION["ses_is_sup"] == 1){
				$display_ok = 1;			
			}else{
				if($vac_emp_id != $_SESSION["ses_emp_id"]){
					$display_ok = 1;
				}else{
					$display_ok = 0;			
				}				
			}

            if($display_ok == 1){
	            //GET THE VACATION TYPE
	            $get_vacation = @mysql_query("SELECT descr FROM vf_to_type WHERE to_id = '$vac_to_id'");
	            $vacation_name = mysql_fetch_array($get_vacation);
					$vacation_description = $vacation_name["descr"];

				//GET THE EMPLOYEES NAME
	            $get_employee = @mysql_query("SELECT fname,lname FROM vf_employee WHERE
	               		emp_id = '$vac_emp_id'");
	            $emp_name = mysql_fetch_array($get_employee);
	            	$emp_lname = $emp_name["lname"];
	                $emp_fname = $emp_name["fname"];
					$emp_full_name = $emp_fname . " " . $emp_lname;

	            //INCREMENT IF THERE WERE REQUESTS
				$request_count = $request_count + 1;

				 $vacation_list .= '
		         	<tr>

					 <td width="125" align="center">'.$emp_full_name.'</td>
					 <td width="125" align="center">'.$vac_hours.'</td>
					 <td width="125" align="center">'.$vacation_description.'</td>
					 <td width="100" align="left">
	                 	<input type="hidden" value="'.$vac_id.'" name="vacation_id['.$apprv_cnt.']">
	                 	&nbsp;<input type="radio" id="appv'.$apprv_cnt.'" value="on" name="confirm['.$apprv_cnt.']"><label for="appv'.$apprv_cnt.'">Approve</label><br />
	                 	&nbsp;<input type="radio" id="deny'.$apprv_cnt.'" value="no" name="confirm['.$apprv_cnt.']"><label for="deny'.$apprv_cnt.'">Deny</label>
	                 </td>				 
		 		 	 <td width="100" align="center"><textarea rows="2" name="reason['.$apprv_cnt.']" cols="10"></textarea></td>				 
					 <td width="125" align="center">'.$vac_request_time.'</td>				 
					</tr>';
			$apprv_cnt = $apprv_cnt + 1;
			}
		}

		if($request_count != 0){
			if($loop1 == 0){
				echo
						'<input type="submit" value="Approve Selected Requests" name="approve_request">
						<input type="reset" value="Reset" name="B2">
						<br />&nbsp;<br />';			
				$loop1 = 1;
			}
			
			
			echo '
				<table border="0" cellpadding="0" cellspacing="0" width="700">
				  <tr>
	               <td class="textRED"><b>Requested Date: '.date("F j, Y",strtotime($sunday_start)).'</b></td>
				  </tr>
	            </table>
		        <table border="1" cellpadding="0" cellspacing="0" width="700">
				  <tr>
				    <th width="125" style=" color:#FFFFFF; background-color:#000080">Requester</th>
				    <th width="125" style=" color:#FFFFFF; background-color:#000080">Hours<br />Requested</th>
				    <th width="125" style=" color:#FFFFFF; background-color:#000080">Time Off Type</th>
				    <th width="100" style=" color:#FFFFFF; background-color:#000080">Approve</th>
				    <th width="100" style=" color:#FFFFFF; background-color:#000080">Reason<br />for Denial</th>			
				    <th width="125" style=" color:#FFFFFF; background-color:#000080">Submitted</th>			
				  </tr>
				'.$vacation_list.'
	             </table>
                 <br />';
        }

		//INCREMENT THE DATE BY ONE DAY
		$sunday_start = date(strtotime("$sunday_start + 1days"));
	    $sunday_start = date("Ymd",$sunday_start);
	}
	if($apprv_cnt != 0){
		echo '
              	<input type="hidden" value="'.$apprv_cnt.'" name="total_requests">
				<input type="submit" value="Approve Selected Requests" name="approve_request">
                <input type="reset" value="Reset" name="B2">
    			</form>';
	}else{
		echo '
                There are no vacation requests for the time period checked.<br />
    			</form>';
	}
} //END OF ELSE
require(DIR_PATH."includes/footer.inc.php");
?>