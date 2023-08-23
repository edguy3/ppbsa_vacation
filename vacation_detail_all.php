<?PHP
/******************************************************************************
**  File Name: vacation_detail_all.php
**  Description: Displays all of the users vaction types on one page
**  with a display of dates requested.
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
session_start();//start the session
define("DIR_PATH", "");//you must change the path for each sub folder

//IF THE EMPLOYEE HASN'T LOGGED IN. STOP THEM
if(!$_SESSION["ses_first_name"]){
	header ("Location: index.php");
}

//SET PAGE VARIABLES
$year_to_view = $_POST["year_to_view"];
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];//Concatenate the user name

require_once(DIR_PATH."includes/db_info.inc.php");
require_once(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year        
require_once(DIR_PATH."includes/vacation_remove.inc.php");

if($year_to_view == ""){
	echo 'You can not access this page directly. <a href="index.php">Return to the home page</a>.';
	exit;
}

//LOADS THE INFORMATION FOR EACH VACATION DATE
function vacation(&$last_view,&$vacation_id){
	global $Vac_Date,$Vac,$VacApprv,$content,$VacApprvDate,$to_descr,$VacEnterDate,$year_to_view,$vac_deny,$year_to_view;

    if($VacApprv == "" || $VacApprv == "0"){
	    $appoved_by = '<span style="color:#FF0000;">Pending</span>';
    }else{
        //GET NAME OF SUPERVISOR WHO APPROVED TIME OFF
		$show_sup = @mysql_query("SELECT fname,lname FROM vf_employee WHERE emp_id = '$VacApprv'");
		$sup_array = @mysql_fetch_array($show_sup);
        	$appv_first = $sup_array["fname"];
        	$appv_last = $sup_array["lname"];

    	//CONCATENAT SUPERVISOR NAME
		$appoved_by = $appv_first . " " . $appv_last;
	}

	if($vac_deny == "Y"){
		$VacApprvDate = '<span style="color:red;">Time off denied.</span>';
	}
	
	//Add icon for the employee to remove a tine off date
	$ckdate = date("Ymd",strtotime ($Vac_Date));
	$ckdate2 = date("Ymd");
	if($ckdate2 < $ckdate)
	{
		if($VacApprv == "" || $VacApprv == "0")
		{
			$allow_removal = '<a href="javascript:remove_vac(\'rem\',\''.$vacation_id.'\',\''.$year_to_view.'\')" title="Remove" onclick="javascript:return confirm(\'Are you sure you want to remove '.$Vac.' hours on '.$Vac_Date.'?\')"><img border="0" src="images/remove.gif" width="7" height="7"></a>&nbsp;';
		}
		else
		{
			$allow_removal = '<a href="javascript:remove_vac(\'req\',\''.$vacation_id.'\',\''.$year_to_view.'\')" title="Submit request to remove" onclick="javascript:return confirm(\'Are you sure you want to request removal of '.$Vac.' hours on '.$Vac_Date.'?\')"><img border="0" src="images/remove.gif" width="7" height="7"></a>&nbsp;';			
		}
	}
	else 
	{
		$allow_removal = '';				
	}	
	
    echo'
        <tr>
	     <td align="center">' . $allow_removal.$Vac_Date . '</td>
	     <td align="center">' . $to_descr . '</td>
	     <td align="center">' . $Vac . '</td>
	     <td align="center">' . $VacEnterDate . '</td>
	     <td align="center">' . $appoved_by . '</td>
	     <td align="center">' . $VacApprvDate . '</td>
	     <td align="center">';
        //CHECK IF Pending IS IN THE APPROVE FIELD. IF NOT APPROVED DON'T  DISPLAY A LINK
         $check_apprv = strstr($appoved_by, 'Pending');
         $check_apprv = substr($check_apprv, 0, 7);
         if($vac_deny == "Y"){
         	echo '&nbsp;';
         	$last_view = '&nbsp;';
         }else{         
	  		 if($check_apprv != "Pending"){
	             echo '<a href="emp_timeoff_compile.php?vacation_date='.urlencode($Vac_Date).'" target="_blank">Create Request form</a>';
	         }else{
	         	echo '&nbsp;';
	         }
         }
         
         if($last_view == ""){
         	$last_view = "&nbsp;";	
         }
                  
         echo'
                </td>
              	<td align="center">'.$last_view.'</td>
		    </tr>';


	//RESET THE VARIABLE
    $VacApprv = "";
}


//TOTAL ALL HOURS EARNED
$ttl_earned = 0;
$earned = @mysql_query("SELECT * FROM vf_emp_to_hours WHERE emp_id = '$_SESSION[ses_emp_id]' " .
				"AND year = '$year_to_view'");

while($earned_array = @mysql_fetch_array($earned)){
	$current_earned = $earned_array["hours"];

	$ttl_earned = $ttl_earned + $current_earned;
}

$hdr_detail = 'Combined Detail For<br />'.$user_name;
$cur_page_title = "Vacation - Combined detail";
require_once(DIR_PATH."includes/header.inc.php");
?>
    
	<table border="0" cellpadding="0" cellspacing="0" width="750">
	  <tr>
    	<td><span style="font-size:14px;color:red;font-weight:bold;"><?php echo $feedback; ?></span></td>
	  </tr>		
	  <tr>
	    <td>&nbsp;</td>
	  </tr>	
	  <tr>
	    <td width="600">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			  <tr>
			    <td width="50%">
					<form method="POST" action="index.php">
						<input type="hidden" value="<?PHP echo $year_to_view; ?>" name="year_to_view">
						<input type="submit" value="Return to the previous view" name="return">
					</form>
				</td>
			    <td width="50%" align="right"><form><input type="button" value="Print Information" onClick="javascript:window.print()"></form></td>
			  </tr>
			</table>
		</td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><b><u>Below is your current detail information:</u></b></td>
	  </tr>
	  <tr>
	    <td>&nbsp; </td>
	  </tr>
	  <tr>
	    <td><b>Total Hours Earned (including holidays) = <?PHP echo $ttl_earned; ?></b></td>
	  </tr>
	  <tr>
	    <td>&nbsp; </td>
	  </tr>
	  <tr>
	   <td>
	    <!-- MAIN TABLE TO HOLD USED AND UNUSED TABLES -->
	    <table border="0" cellpadding="0" cellspacing="0" width="750">
		 <tr>
	      <td valign="top">
	      
			<?PHP	      
		        //LOOKUP ALL OF THE EMPLOYEES SCHEDULED TIME OFF FOR THE CURENT TYPE
				$Get_time_off = @mysql_query("SELECT * FROM vf_vacation WHERE " .
		             		"emp_id = '$_SESSION[ses_emp_id]' AND year = '$year_to_view' " .
		                    "ORDER BY date ASC");
			?>
			<!-- TABLE FOR USED VACATION -->
            <table border="0" cellpadding="0" cellspacing="0" width="750">
			  <tr>
			    <td bgcolor="#003366">
                	&nbsp;<b><span style="color:#FFFFFF;">Time Off for <?PHP echo $user_name; ?></span></b>
			    </td>
			  </tr>
			  <tr>
			    <td>
		          <!-- TABLE FOR USED VACATION BREAKDOWN -->
			      <table border="1" cellpadding="0" cellspacing="0" width="750">
			        <tr>
			          <th bgcolor="#CCCC99">DATE</th>
			          <th bgcolor="#CCCC99">TIME OFF TYPE</th>
		          	  <th bgcolor="#CCCC99">HOURS</th>
		          	  <th bgcolor="#CCCC99">DATE OF REQUEST</th>
		          	  <th bgcolor="#CCCC99">APPROVED BY</th>
		          	  <th bgcolor="#CCCC99">DATE<br />APPROVED</th>
		          	  <th bgcolor="#CCCC99">&nbsp;</th>
		          	  <th bgcolor="#CCCC99">LAST PRINTED</th>        
		      		</tr>
					<?PHP		      		

		                //VARIABLE TO COUNT HOURS
		                $total_hours = 0;
						//LOAD THE RESULTS OF THE QUERY ABOVE
						while($Load_Sched = @mysql_fetch_array($Get_time_off)){
							$vacation_id = $Load_Sched["vacation_id"];
			            	$Vac = $Load_Sched["hours"];
			                $VacDate = $Load_Sched["date"];
		                    $VacApprv = $Load_Sched["apprv_by"];
		                    $VacEnterDate = $Load_Sched["date_entered"];
		                    $VacApprvDate = $Load_Sched["date_approved"];
		                    $to_type = $Load_Sched["to_id"];
							$last_view =  $Load_Sched["form_request"];
							$vac_deny = $Load_Sched["deny"];
		                    
		                    $Vac_Date = date("m/d/Y",strtotime($VacDate));
		
		                    $total_hours = $total_hours + $Vac;
		
		                    if($VacApprvDate == "0000-00-00 00:00:00"){
								$VacApprvDate = "&nbsp;";
		                    }else{
			                    $VacApprvDate = date("m/d/Y",strtotime($VacApprvDate));
							}
		
		                    if($VacEnterDate == "0000-00-00 00:00:00"){
								$VacEnterDate = "&nbsp;";
		                    }else{
			                    $VacEnterDate = date("m/d/Y g:i:sa",strtotime($VacEnterDate));
							}

							//LOOK UP THE TIME OFF TYPE AND DESCRIPTION
							$SQL = @mysql_query("SELECT * from vf_to_type WHERE to_id = '$to_type'");
						    $Result = @mysql_fetch_array($SQL);
								$TypeID = $Result["to_id"];
						        $to_descr = $Result["descr"];
						        $Shift = $Result["shift_id"];
						        $TOYear = $Result["year"];
						        $Valid_date = $Result["type_date"];
						        $Dept_Time = $Result["dept_id"];
				
					                //CALL THE VACATION FUNCTION
					                vacation($last_view,$vacation_id);
					    }
				?>					    
					
				    <tr>
				      <td bgcolor="#CCCC99"><b>TOTALS:</b></td>
				      <td bgcolor="#CCCC99">&nbsp;</td>
				   	  <td bgcolor="#CCCC99" align="center"><b><?PHP echo $total_hours; ?></b></td>
				   	  <td bgcolor="#CCCC99">&nbsp;</td>
				   	  <td bgcolor="#CCCC99">&nbsp;</td>
				   	  <td bgcolor="#CCCC99">&nbsp;</td>
				   	  <td bgcolor="#CCCC99">&nbsp;</td>
				   	  <td bgcolor="#CCCC99">&nbsp;</td>    
				    </tr>
	   			  </table>
			  </td>    
			 </tr>
	   		</table>	   			  
          </td>
         </tr>
        </table>	   		
      </td>
     </tr>
    </table>
   <p>&nbsp;</p>
<!-- Form to redirect the input -->
<form method="post" name="remove_vacation" action="<?PHP echo $PHP_SELF; ?>">
<input type=hidden name="req_type" value="">
<input type=hidden name="vac_id" value="">
<input type=hidden name="year_to_view" value="">
</form>
<script language="JavaScript" type="text/javascript">
  function remove_vac(type,vid,year){ 
  	document.remove_vacation.req_type.value = type;
  	document.remove_vacation.vac_id.value = vid;  	
  	document.remove_vacation.year_to_view.value = year;  	  	
    document.remove_vacation.submit()
  }
</script>       
<?PHP require_once(DIR_PATH."includes/footer.inc.php");?>