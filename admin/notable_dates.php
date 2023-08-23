<?php
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
if($_SESSION["ses_super_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

//SET PAGE VARIABLES
$entry_error = 	"";
$Add_date = $_POST["Add_date"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information

$entry_error = 	"";
$add_date = $_POST["add_date"];
$rmv_date = $_GET["rmv_date"];

if($add_date == "Add Date"){
	
	$holiday = $_POST["holiday"];
	$description = $_POST["description"];

	if($holiday != "" && $description != ""){
		$add_new_date = mysql_query("INSERT INTO vf_notable_dates (date,descr)VALUES('$holiday','$description')");
	}else{
		$entry_error = 	'<font color="#FF0000"><b>Error: Some of your data is incorrect or missing.</b></font>';
	}
}		
		
//REMOVE A DATE IF SELECTED FOR REMOVAL
if(isset($rmv_date)){
	$clear_date = @mysql_query("DELETE FROM vf_notable_dates WHERE date = '$rmv_date'");
}		
		
	//DISPLAY ALL DATES IN THE TABLE AND ADD A REMOVE LINK
	$no_dates = 0;
	$first_loop = 0;
	$bgcolor = "";
	
	$current_dates = "<b><u>Current dates that are in the table</u></b><br /><br />";
	$show_dates = @mysql_query("SELECT date,descr FROM vf_notable_dates ORDER BY date ASC");
	while($show_dates2 = @mysql_fetch_array($show_dates)){
		$curr_descr = $show_dates2["descr"];			
		$curr_date = $show_dates2["date"];
		$make_date = strtotime($curr_date);
		$fmt_date = date("D - F d, Y",$make_date);
		if($bgcolor == "#C0C0C0"){
			$bgcolor = "#FFFFFF";
		}else{
			$bgcolor = "#C0C0C0";				
		}
			
			
		if($first_loop == 0){
			$current_dates .= '<table border="0" cellpadding="3" cellspacing="0" width="450">';
			$first_loop = 1;
		}	
		//ADD DATE TO VARIABLE
		$current_dates .= '
		  <tr>
		    <td width="50" align="right" bgcolor="'.$bgcolor.'"><a href="'.$_SERVER["PHP_SELF"].'?rmv_date='.$curr_date.'" onclick="javascript:return confirm(\'Are you sure you want to delete: '.$curr_descr.' on '.$fmt_date.'?\')">Remove</a>&nbsp;</td>
		    <td width="200" align="left" bgcolor="'.$bgcolor.'">'.$curr_descr.'</td>
		    <td width="200" align="left" bgcolor="'.$bgcolor.'">'.$fmt_date.'</td>
		  </tr>';	
		$no_dates = 1;
	}
	if($first_loop == 1){
		$current_dates .= '</table>';
	}
	
	if($no_dates == 0){
		$current_dates .= "No Dates are currently in the table.";	
	}

	
$cur_page_title = "Notable Dates";
$hdr_detail = "Add Notable Dates";
require(DIR_PATH."includes/header.inc.php");


 $entry_error
?>
<p style="margin-left: 10;"><a href="site_setup.php">Return to the Site Setup menu</a></p>
<form name="dates" method="post" action="<?PHP echo $_SERVER["PHP_SELF"]; ?>">
    <table border="0" cellpadding="0" cellspacing="0" width="750">
       <tr>
        <td>
            &nbsp;
        </td>
       </tr>
       <tr>
        <td align="center">
            Select a date and submit it to add a notable date such as Christmas or a company holiday.
        </td>
       </tr>
       <tr>
        <td>&nbsp;</td>
       </tr>
      <tr>
        <td></td>
      </tr>
	  <tr>			
        <td valign="top">
		  <!-- *** DELIVERY DATES TABLE *** -->
		   <table border="0" cellpadding="0" cellspacing="0" align="center">
            <tr>                      
              <td class="text12BkB" align="center">Description</td>
              <td class="text12BkB" align="center">Date</td>
            </tr>
            <tr>                      
              <td class="text12BkB" align="center">&nbsp;</td>
              <td class="text12BkB" align="center">&nbsp; </td>
            </tr>	
            <tr>
              <td><input type="text" name="description" size="30">&nbsp;&nbsp;</td>
              <td>
                <input name="holiday" value="" size="12" readonly>
                <a href="javascript:void(0)" onclick="gfPop.fPopCalendar(document.dates.holiday);return false;" HIDEFOCUS>
                <img name="popcal" align="absmiddle" src="popCal/calbtn.gif" width="34" height="22" border="0" alt=""></a>                            	                     
              </td>
            </tr>
           </table>
	      <!-- *** END OF DELIVERY DATES TABLE *** -->
         </td>
       </tr>
       <tr>
        <td>
            &nbsp;
        </td>
       </tr>
       <tr>
        <td align="center">
            <input type="submit" value="Add Date" name="add_date">
        </td>
       </tr>	
       <tr>
        <td>
            &nbsp;
        </td>
       </tr>
       <tr>
        <td>
            &nbsp;
        </td>
       </tr>
       <tr>
        <td>	
			<table border="0" cellpadding="0" cellspacing="0" width="80%" align="center">	
			 <tr>
			  <td>	
				<?PHP echo $current_dates; ?>
			  </td>	
			 </tr>
			</table>	
        </td>
       </tr>
       <tr>
        <td>
            &nbsp;
        </td>
       </tr>	
    </table>
   </form>
<!--  PopCalendar(tag name and id must match) Tags should sit at the page bottom -->
<iframe width=174 height=189 name="gToday:company_cal2:agenda.js" id="gToday:company_cal2:agenda.js" src="popCal/cal_ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
<?PHP 
require(DIR_PATH."includes/footer.inc.php");
?> 

	