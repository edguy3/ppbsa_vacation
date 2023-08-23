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

//SET PAGE VARIABLES
$DeptID = $_SESSION["ses_dept_id"];
$weeks_to_display = $_POST["weeks_to_display"];
$sunday_choice = $_POST["sunday_choice"];
$date_selection = $_POST["date_selection"];
$submit = $_POST["submit"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$cur_page_title = "Weekly Vacation Report- All";
if(!isset($date_selection)){
	$itemcnt = 1; //Count of select boxes on the current page
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



    //DISPLAY SELECTION OPTIONS
	echo '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
            <input type="hidden" value="set" name="date_selection">
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

	echo '<p><a href="report_weekly_all.php">Select another time frame</a></p>';

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
              ';

            //SELECT ALL DEPARTMENTS
            $sql_dept = @mysql_query("SELECT dept_id,descr FROM vf_department ORDER BY dept_id");

            //GRAB THE DEPARTMENT INFORMATION
            while($dept_array = @mysql_fetch_array($sql_dept)){
            	$my_dept_id = $dept_array["dept_id"];
            	$my_dept_name = $dept_array["descr"];

	            //SELECT ALL VACATIONS FOR A DATE AND THE EMPLOYEES NAME AND VACATION
                //TYPE WITH A JOIN TABLE
		    	$sql_people = @mysql_query("SELECT vf_employee.lname,vf_employee.fname,
                	vf_to_type.descr,vf_vacation.hours,vf_vacation.replacement,vf_vacation.note
                    FROM vf_vacation,vf_employee,vf_to_type WHERE date = '$current_day' AND
                    vf_vacation.dept_id = '$my_dept_id' AND vf_vacation.emp_id = vf_employee.emp_id
                    AND vf_to_type.to_id = vf_vacation.to_id AND vf_vacation.deny != 'Y'
                    ORDER BY vf_vacation.dept_id,vf_employee.lname,vf_employee.fname");

				$dept_header ='
			        <tr>
			          <td align="center" width="10"></td>
			          <td  colspan="5">&nbsp;</td>
			          <td align="center" width="10"></td>
			        </tr>
			        <tr>
			          <td align="center" width="10"></td>
			          <td  colspan="5"><b>'.$my_dept_name.'</b></td>
			          <td align="center" width="10"></td>
			        </tr>
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

                //RESET THE VALUE
                $vacation_detail ="";
                //IF THIS DOESN'T CHANGE. THERE IS NO DATA TO DISPLAY
				$is_empty = 0;
	            while($people_array = @mysql_fetch_array($sql_people)){
                    $emp_lname = $people_array["lname"];
	                $emp_fname = $people_array["fname"];
	                $vacation_description = $people_array["descr"];
					$vac_hours = $people_array["hours"];
	                $vac_replacement = $people_array["replacement"];
	                $vac_note = $people_array["note"];
                    $emp_full_name = $emp_fname . " " . $emp_lname;

	                //IF THERE IS NO DATA ADD A SPACE SO THE TABLE BORDER WILL DIPSLAY CORRECTLY
					if($vac_replacement == ""){
						$vac_replacement = "&nbsp;";
					}

	                //IF THERE IS NO DATA ADD A SPACE SO THE TABLE BORDER WILL DIPSLAY CORRECTLY
					if($vac_note == ""){
						$vac_note = "&nbsp;";
					}

	            	$vacation_detail .='
			        <tr>
			          <td class="text9Bk" align="center" width="10"></td>
			          <td class="text9Bk" align="center" width="140" style="border-bottom: 1px solid black">'.$emp_full_name.'</td>
			          <td class="text9Bk" align="center" width="100" style="border-bottom: 1px solid black">'.$vacation_description.'</td>
			          <td class="text9Bk" align="center" width="50" style="border-bottom: 1px solid black">'.$vac_hours.'</td>
			          <td class="text9Bk" align="center" width="140" style="border-bottom: 1px solid black">'.$vac_replacement.'</td>
			          <td class="text9Bk" align="center" width="200" style="border-bottom: 1px solid black">'.$vac_note.'</td>
			          <td class="text9Bk" align="center" width="10"></td>
			        </tr>
					';
                    //SET TO 1 TO SHOW THERE WAS DATA
                    $is_empty = 1;

	            }
				if($is_empty == 1){
	                echo  $dept_header .$vacation_detail;
				}

			}//END OF DEPARTMENT INFORMATION WHILE LOOP
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

require(DIR_PATH."includes/footer.inc.php");
?>