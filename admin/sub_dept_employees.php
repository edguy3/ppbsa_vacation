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
$update = $_POST["update"];
$sub_type = $_POST["sub_type"];
$employee_count = $_POST["employee_count"];
$cur_emp_id = $_POST["cur_emp_id"];
$cur_emp_name = $_POST["cur_emp_name"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

$cur_page_title = "Sub-Department options";
require(DIR_PATH."includes/header.inc.php");

//IF THE USER IS EDITING A CURRENT DESCRIPTION RUN THIS
if(isset($update)){

	/************************************************************************
	** START ENTERING INFORMATION INTO THE DATABASE IT THERE WERE NO ERRORS**
	************************************************************************/
    for ($i=0; $i<$employee_count; $i++) {
		if($sub_type[$i] == ""){
            $sub_type[$i] = 0;
		}

	   	$run_update = @mysql_query("UPDATE `vf_employee` SET
			`sub_dept_id` = '".$sub_type[$i]."'
		    WHERE `emp_id` = '".$cur_emp_id[$i]."'
            AND `dept_id` = '".$DeptID."'
	    	")or die ("Can't update changes for cur_emp_id[$i].");
	}
	echo '<span class="textGood">Update complete</span>';
}

/*IF THERE ARE SUB DEPARTMENTS DISPLAY THEM */
//SWITCH TO SEE IF SUB_DEPTS EXIST FOR THIS DEPT.
$ck_for_subs = 0;
//HOLDS ALL SUB DEPT ID'S
$sub_depts = array();
//KEEPS A COUNT OF SUB DEPTS
$sub_dept_count = 0;
//START BUILDING A STING TO ADD LATER IN THE SCRIPT
$current_subs = '<th style="color:#FFFFFF;">Employee Name</th>';
//GET A SUB DEPTS FOR THE CURRENT DEPT.
$ck_sub = @mysql_query("SELECT * FROM vf_sub_dept WHERE dept_id = '$DeptID'");
while($sub_array = @mysql_fetch_array($ck_sub)){
	$sub_dept_id = $sub_array["sub_dept_id"];
	$sub_dept_code = $sub_array["code"];
	$sub_dept_descr = $sub_array["descr"];

    //CREATE AN ARRAY OF SUB DEPTS
    $sub_depts[$sub_dept_count]= $sub_dept_id;

    //ADD THE CURRENT SUB DEPT
    $current_subs .= '<th width="50" style="color:#FFFFFF;">'.$sub_dept_code.'</th>';

    //IF THERE ARE ANY SUB DEPTS. SWITCH TRIGGER TO ON.
	$ck_for_subs = 1;
	$sub_dept_count = $sub_dept_count + 1;
}

//ADD THE NONE RADIO BUTTON IF THE USER IS NOT IN A SUB DEPT
$current_subs .= '<th width="50" style="color:#FFFFFF">NONE</th>';


if($ck_for_subs == 1){
	//ADD INFORMATAION TO THE content VARIABLE. (THE BULK OF THE DATA)
	echo '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
 		   <p><a href="sub_dept_menu.php">Return to the work with sub-departments menu</a></p>
		   <p><input type="submit" value="Save Changes" name="update"></p>
          <table border="0" cellpadding="0" cellspacing="0">
		   <tr bgcolor="red">
		    '.$current_subs.'
		   </tr>';

    //SET THE CELL BACKGROUND
	$cellbg ='#C0C0C0';
    //WHEN THIS GETS TO 15 REPEAT THE HEADER ROW
	$emp_display = 0;
    //COUNT HOW MANY EMPLOYEES ARE IN THE DEPARTMENT
	$emp_count = 0;
    //SELECT ALL EMPLOYEES FOR THE DEPRTMENT AND DISPLY THEM.
	$emp_sql = @mysql_query("SELECT emp_id,fname,lname,dept_id,sub_dept_id FROM `vf_employee` WHERE dept_id = '$DeptID' ORDER BY lname ASC");
	while($emp_array = @mysql_fetch_array($emp_sql)){
		$emp_id = $emp_array["emp_id"];
		$emp_fname = $emp_array["fname"];
		$emp_lname = $emp_array["lname"];
		$emp_dept = $emp_array["dept_id"];
		$emp_sub = $emp_array["sub_dept_id"];


		echo '<tr>
        			 <td bgcolor="'.$cellbg.'">
                         <input type="hidden" value="'.$emp_lname.', '.$emp_fname.'" name="cur_emp_name['.$emp_count.']">
	                     <input type="hidden" value="'.$emp_id.'" name="cur_emp_id['.$emp_count.']">
                     '.$emp_lname.', '.$emp_fname.'</td>';
        //DISPLAY THE RADIO BUTTONS
		//SWITCH TO SEE IF THE THE USER BELONGS TO A SUB DEPT
		$not_in_sub = 0;

        for ($i=0; $i<$sub_dept_count; $i++) {
			if($emp_sub == $sub_depts[$i]){
	           	$emp_sub_id = "checked";
				$not_in_sub = 1;
	        }

			echo '<td width="50" align="center" bgcolor="'.$cellbg.'"><input type="radio" value="'.$sub_depts[$i].'" '.$emp_sub_id.' name="sub_type['.$emp_count.']"></td>';
			$emp_sub_id = "";
        }

        //IF NO SUB DEPT WAS SELECTED SELECT NONE
        if($not_in_sub == 0){
        	$checked = "checked";
        }else{
        	$checked = "";
        }
        echo '<td width="50" align="center" bgcolor="'.$cellbg.'"><input type="radio" value="0" '.$checked.' name="sub_type['.$emp_count.']"></td>';

		echo '</tr>';

        //CHANGE THE CELL BACKGOUD FOR EACH ROW
        if($cellbg == "#C0C0C0"){
        	$cellbg = "#FFFFFF";
		}else{
			$cellbg = "#C0C0C0";
		}

        //REPEAT THE HEADER ROW IF COUNT IS 15
		if($emp_display == 15){
        	$emp_display = 0;
	        echo '
			   <tr bgcolor="red">
			    '.$current_subs.'
			   </tr>';
		}

       	$emp_display = $emp_display + 1;
		$emp_count = $emp_count + 1;
	}


	//CLOSE THE TABLE AND FORM
	echo '</table>
       <input type="hidden" value="'.$emp_count.'" name="employee_count">
	   <p><input type="submit" value="Save Changes" name="update"></p>
       </form>';
}else{
    //IF THERE ARE NO SUB DEPARTMENTS DON'T LET THE USER DO ANYTHING
	echo '<b>You currently are not using Sub-Departments.</b><br />
    	To add Sub-Departments go here - <a href="sub_dept_new.php">Add a
        Sub-Department</a>.';
}

require(DIR_PATH."includes/footer.inc.php");
?>