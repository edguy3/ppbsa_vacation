<?PHP
/******************************************************************************
**  Description:
**  Written By: Gary Barber
**  
*******************************************************************************/

session_start();
define("DIR_PATH", "../");//you must change the path for each sub folder
require(DIR_PATH."includes/db_info.inc.php");//database connection information

//IF THE EMPLOYEE ISN'T AN ADMIN. STOP THE USER
if($_SESSION["ses_super_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

$cur_page_title = "Change Departments";
$hdr_detail = "Change department to work with";
require(DIR_PATH."includes/header.inc.php");


$chg_dept = $_POST['chg_dept'];
//Change the department
if($chg_dept == "Y")
{
	$dept = $_POST['dept'];
	if($dept != "none")
	{
		$_SESSION["ses_dept_id"] = $dept; //Set users department.
	}
}

function get_dept_name($dept_num)
{
	$sql = @mysql_query("SELECT `descr` FROM `vf_department` WHERE `dept_id` = '$dept_num'");
	if(! $sql)
	{
		$cur_dept_name = "Error getting name.";
	}
	else
	{
		$result = @mysql_fetch_array($sql);
		 $cur_dept_name = $result["descr"];		
	}

	return $cur_dept_name;
}

//Get department name
$cur_dept_name = get_dept_name($_SESSION["ses_dept_id"]);
echo '<div style="width:300px">Current department: '.$cur_dept_name.'.</div>';  

$dept_options = "";
$dept_sql = mysql_query("SELECT `dept_id`,`descr` FROM `vf_department` ORDER BY `descr`");
while($dept_result = mysql_fetch_array($dept_sql))
{
	$dept_id = $dept_result["dept_id"];
	$dept_name = $dept_result["descr"];
	
	$dept_options .= '<option value="'.$dept_id.'">'.$dept_name.'</option>'."\n";
}	

echo'
<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data" name="change_dep">
 <table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>  
	 <b>Departments:</b>&nbsp;
     <select size="1" name="dept">
	  <option value="none">Select One</option>
	  '.$dept_options.'
     </select>
   </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
	 <input name="chg_dept" type="hidden" value="Y">
     <input name="change" type="submit" value="Change Department"> 
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>  
 </table>
</form>';
require(DIR_PATH."includes/footer.inc.php");
?>