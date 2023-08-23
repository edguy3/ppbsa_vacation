<?PHP
/******************************************************************************
**  File Name: selected_year.inc.php
**  Description: Include file to determine the fiscal year of the date chosen.
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
function selected_date($selected_year){
	
	$GetYr = @mysql_query("SELECT * FROM vf_year");
	while($YrArray = @mysql_fetch_array($GetYr)){
		$CurYear = $YrArray["year"];
		$YrStart = $YrArray["start"];
	    $YrEnd =  $YrArray["end"];
	
	    $YrStart = strtotime($YrStart);
	    $YrEnd = strtotime($YrEnd);
	    
	    if($selected_year >= $YrStart && $selected_year <= $YrEnd){
	      	$year_select = $CurYear;
	    }
	}
	
	return $year_select;
}
?>