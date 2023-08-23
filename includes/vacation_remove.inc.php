<?PHP 

$req_type = $_POST["req_type"];
$vac_id = $_POST["vac_id"];
$employee = $_SESSION["ses_emp_id"];

if(isset($req_type))
{
	if($req_type == "rem")
	{
		//Make sure the request was not sent after it was approved
		$sql = @mysql_query("SELECT `apprv_by` FROM `vf_vacation` WHERE `vacation_id` = '$vac_id'");
		$result = @mysql_fetch_array($sql);
		 	$is_approved = $result["apprv_by"];
		if($is_approved == '0')
	 	{		
			$rmv_entry = @mysql_query("DELETE FROM `vf_vacation` WHERE `vacation_id` = '$vac_id'")
				or die("Unable to remove time off. ". mysql_error()); 
	 		$feedback = "Time off removed";
	 	}
	 	else 
	 	{
			$feedback = "This vacation has been approved since you first displayed this page. The page is now refresh. Resubmit";  		
	 	}
		
	}
	elseif ($req_type == "req")
	{
		
		$contact_email = $_SESSION["ses_contact_email"];
		//If the contact email is not configured. Get the supervisors email if it is configured.
        if ($contact_email == "")
        {		
			//Get the supervisor id
			$sup_id_SQLstr="SELECT `sup_id` FROM `vf_employee` WHERE `emp_id` =' $_SESSION[ses_emp_id]'";
				$sup_id_qry = @mysql_query($sup_id_SQLstr);
			if (!$sup_id_qry)
			{
				$message = 'Error getting supervisor email : ' . mysql_error() . "\n";
				die($message);
			}
			$sup_id_result = @mysql_fetch_array($sup_id_qry);
			$sup_id_value = $sup_id_result["sup_id"];

			//GET THE SUPERVISOR EMAIL FROM THE SUPERVISOR ID
            $sup_email_SQLstr="SELECT email FROM vf_employee WHERE emp_id ='$sup_id_value'";
			$sup_email_qry = @mysql_query($sup_email_SQLstr);
      		if (!$sup_email_qry){
				$message = 'Request invalid : ' . mysql_error() . "\n";
	     		$message .= 'Request complete : ' . $sup_email_SQLstr;
			die($message);
			}
    			$sup_email_result = @mysql_fetch_array($sup_email_qry);
	   		$contact_email = $sup_email_result["email"];
        }
		
		
        if($contact_email != "")
        {
        	$to = $contact_email;
        	
			//Get information to email to the supervisor
			$sql = @mysql_query("SELECT `vf_vacation`.`date`,`vf_vacation`.`hours`,`vf_to_type`.`descr` 
								FROM `vf_vacation`,`vf_to_type`
								WHERE `vacation_id` = '$vac_id' 
								AND `vf_vacation`.`to_id` = `vf_to_type`.`to_id`");
			if (!$sql)
			{
				$message = 'Error getting time off information : ' . mysql_error() . "\n";
				die($message);
			}			
			$result = @mysql_fetch_array($sql);
	        	
			$subject = "$user_name time off removal request!";
			$body = $user_name . ' has requested removal of time off that is scheduled for:' . "\n" .
	            	$result["hours"] . ' hours ' . $result["descr"] . ' on ' .
            		date("F j, Y",strtotime($result["date"])) .
                    "\n\nPlease remove time off.\n";

		   	@mail($to, $subject, $body, "From: $user_name time off removal Request");			
		
			$feedback = "The request was emailed to your supervisor. Check back later to see if the time has been removed";	
        }
        else 
        {
			$feedback = "Your supervisor has not configured an email address and since the time has already been approved you must 
						ask you supervisor to remove the time.";	                		
        }
	}	
}
?>