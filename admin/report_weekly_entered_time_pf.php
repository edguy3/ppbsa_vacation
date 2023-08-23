<?PHP
/******************************************************************************
**  File Name:
**  Description:
**  Written By: Gary Barber
**  Original Date: 10/27/05
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
$sunday_choice = $_POST["sunday_choice"];
$dept_choice = $_POST["dept_choice"];
$date_selection = $_POST["date_selection"];
$submit = $_POST["submit"];
$weeks_to_display = $_POST["weeks_to_display"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Weekly Vacation Report</title>
<link rel="stylesheet" type="text/css" href="../css/site.css">
	
</head>
<body style="margin-top:0; margin-left:0;">	
<div style="position:relative; left:200px; top:10px; font-size:14px"><b>Submitted Time Report</b></div>
<div style="position:relative;top:15;width:650px;text-align:right">Print Date: <?PHP echo date('l F d, Y'); ?></div>
<?PHP 	

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


            echo $error_mssage . '
            <table border="1" cellpadding="0" cellspacing="0" width="650" style="border-color:#000000;position:relative;top:25px;">
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
			          <td align="center" width="120" style="text-decoration:underline;"><b>NAME</b></td>
			          <td align="center" width="80" style="text-decoration:underline;"><b>TYPE</b></td>
			          <td align="center" width="50" style="text-decoration:underline;"><b>HOURS</b></td>
			          <td align="center" width="120" style="text-decoration:underline;"><b>RELIEF</b></td>
			          <td align="center" width="160" style="text-decoration:underline;"><b>NOTES</b></td>
			          <td align="center" width="100" style="text-decoration:underline;"><b>SUBMITTED</b></td>            
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
                $date_entered = $people_array["date_entered"];
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
                $get_employee = @mysql_query("SELECT fname,lname, enabled FROM vf_employee WHERE emp_id = '$vac_emp_id'");
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
			          <td class="text9Bk" align="center" width="120" style="border-bottom: 1px solid black">'.$emp_full_name.'</td>
			          <td class="text9Bk" align="center" width="80" style="border-bottom: 1px solid black">'.$vacation_description.'</td>
			          <td class="text9Bk" align="center" width="50" style="border-bottom: 1px solid black">'.$vac_hours.'</td>
	            	  <td class="text9Bk" align="center" width="120" style="border-bottom: 1px solid black">'.$vac_replacement.'</td>';
	            	if($vac_deny == "Y"){
			          echo '<td class="text9Bk" align="center" width="160" style="border-bottom: 1px solid black"><span style="color: red">Time off denied</span></td>';            		
	            	}else{
			          echo '<td class="text9Bk" align="center" width="160" style="border-bottom: 1px solid black">'.$vac_note.'</td>';
	            	}
	            	echo '
	                  <td class="text9Bk" align="center" width="100" style="border-bottom: 1px solid black">'.$date_entered.'</td>
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
?>
 </body>
</html>	