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

//IF THE EMPLOYEE HASN'T LOGGED IN. STOP THEM
if(!$_SESSION["ses_first_name"]){
	header ("Location: ".DIR_PATH."index.php");
}

//SET PAGE VARIABLES
$dept = $_SESSION["ses_dept_id"];
$vacation_date = urldecode($_GET["vacation_date"]);
$eid = urldecode($_GET["eid"]);

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

//GET THE EMPLOYEES NAME
$emp_sql = @mysql_query("SELECT fname,lname FROM vf_employee WHERE emp_id = '$eid' AND
			dept_id = '$dept'");
$emp_arry = @mysql_fetch_array($emp_sql);
	$first_name = $emp_arry["fname"];
	$last_name = $emp_arry["lname"];

if($last_name == ""){
	echo "There is an error retrieving the employee. Please check with a programmer to repair the
    	problem.<br />";
    exit();
}

$vacation_req_date = str_replace("-","",$vacation_date);
$vacation_date = $vacation_req_date;
$check_date = checkdate(substr($vacation_req_date,4,2), substr($vacation_req_date,6,2), substr($vacation_req_date,0,4));
if($check_date != 1){
	echo "The date requested is not a valid date. Close this window and check again.<br />";
}

//CONCATENATE THE USER NAME
$user_name = $_SESSION["ses_first_name"] . " " . $_SESSION["ses_last_name"];


//GET THE SUNDAY DATE PRIOR TO THE DATE SELECTED
$convert_date = date("m/d/y",strtotime($vacation_date));
$day_num = date("w",strtotime($vacation_date));
$minus_days = "-".$day_num."days";
$sunday = date("Y-m-d" ,strtotime("$convert_date $minus_days"));

//LOOKUP ALL OF THE EMPLOYEES SCHEDULED TIME OFF FOR THE CURRENT TYPE
$Get_time_off = @mysql_query("SELECT * FROM vf_vacation WHERE " .
	"emp_id = '$eid' AND year = '$CurrentYear' " .
    "AND apprv_by != '0' AND deny != 'Y' ORDER BY date ASC");

    //INITIALIZE THE VARIABLE
    $x = 0;
	while($Load_Sched = @mysql_fetch_array($Get_time_off)){
		$Vac = $Load_Sched["hours"];
	    $VacDate = $Load_Sched["date"];
        $VacApprv = $Load_Sched["apprv_by"];
        $VacEnterDate = $Load_Sched["date_entered"];
        $VacApprvDate = $Load_Sched["date_approved"];
        $to_type = $Load_Sched["to_id"];
		$last_view =  $Load_Sched["form_request"];
        
		switch ($VacDate) {
		  case $sunday:
		    $day_of_wk = date("m/d",strtotime("$sunday"));
		    break;
		  case date("Y-m-d",strtotime("$sunday +1 days")):
		    $day_of_wk = date("m/d",strtotime("$sunday +1days"));
		    break;
		  case date("Y-m-d",strtotime("$sunday +2days")):
		    $day_of_wk = date("m/d",strtotime("$sunday +2days"));
		    break;
		  case date("Y-m-d",strtotime("$sunday +3days")):
		    $day_of_wk = date("m/d",strtotime("$sunday +3days"));
		    break;
		  case date("Y-m-d",strtotime("$sunday +4days")):
		    $day_of_wk = date("m/d",strtotime("$sunday +4days"));
		    break;
		  case date("Y-m-d",strtotime("$sunday +5days")):
		    $day_of_wk = date("m/d",strtotime("$sunday +5days"));
		    break;
		  case date("Y-m-d",strtotime("$sunday +6days")):
		    $day_of_wk = date("m/d",strtotime("$sunday +6days"));
		    break;
		}


        if($day_of_wk != ""){
        	$sup_name = "By ".$_SESSION["ses_first_name"] . " " . substr($_SESSION["ses_last_name"], 0, 1).".";
			$entry_date = $sup_name." on ".date("m/d/y",$TodaysDate);
			//UPDATE DATABASE TO SHOW THE LATEST VIEW OF THE REQUEST FORM
			$set_date = mysql_query("UPDATE `vf_vacation` 
					SET `form_request` = '$entry_date' 
					WHERE `emp_id` = '$eid' 
					AND `year` = '$CurrentYear'
					AND `date` = '$VacDate'");
			//IF THE DATE OF THE REQUEST HAS NOT BEEN PRINTED IN THE PAST
			//DISPLAY A BLUE / BETEEN THE DAY AND MONTH
        	if($last_view == ""){
        		$day_of_wk = str_replace('/', '<font color="#0000FF">/</font>', $day_of_wk);        		
        	}
			
	        $time_off_data[$x][0] = $day_of_wk;
	        $time_off_data[$x][1] = $to_type;
            $time_off_data[$x][2] = $Vac;
            //INCREMENT THE COUNTER
            $x = $x + 1;
        }

    	$day_of_wk = "";
        $to_type = "";
    }

if($x == 0){
	echo "The employee has either requested no time or you have not approved
    	 the time off request. Close this window and correct any data.";
    exit();
}

for ($i=0; $i<$x; $i++) {

    switch ($time_off_data[$i][1]) {
      case 1:
        $vacation .= $time_off_data[$i][0]." ";
        $vacation_hrs = $vacation_hrs + $time_off_data[$i][2];
        break;
      case 2:
        $personal .= $time_off_data[$i][0]." ";
        $personal_hrs = $personal_hrs + $time_off_data[$i][2];
        break;
      case 4:
        $mem_day .= $time_off_data[$i][0]." ";
        $mem_day_hrs = $mem_day_hrs + $time_off_data[$i][2];
        break;
      case 6:
        $july4 .= $time_off_data[$i][0]." ";
        $july4_hrs = $july4_hrs + $time_off_data[$i][2];
        break;
      case 7:
        $labor_day .= $time_off_data[$i][0]." ";
        $labor_day_hrs = $labor_day_hrs + $time_off_data[$i][2];
        break;
      case 8:
        $new_year .= $time_off_data[$i][0]." ";
        $new_year_hrs = $new_year_hrs + $time_off_data[$i][2];
        break;
      case 10:
        $eom .= $time_off_data[$i][0]." ";
        $eom_hrs = $eom_hrs + $time_off_data[$i][2];
        break;
      case 12:
        $funeral .= $time_off_data[$i][0]." ";
        $funeral_hrs = $funeral_hrs + $time_off_data[$i][2];
        break;
      case 17:
        $jury .= $time_off_data[$i][0]." ";
        $jury_hrs = $jury_hrs + $time_off_data[$i][2];
        break;
      case 23:
        $safety .= $time_off_data[$i][0]." ";
        $safety_hrs = $safety_hrs + $time_off_data[$i][2];
        break;
    }

};

$week_ending = date("M. j, Y",strtotime("$sunday +6days"));
include("admin_emp_timeoff_form.php");


?>