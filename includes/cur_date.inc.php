<?PHP

/**********************************************************************
** GET THE CURRENT FISCAL YEAR TO BE USED FOR COMPILING INFORMATION  **
**********************************************************************/
//GET THE VALUE IF THE CURRENT CALENDAR DATE
$TodaysDate = strtotime(date("Ymd"));
$Current_date = date("Ymd");
$GetYr = @mysql_query("SELECT * FROM vf_year");
while($YrArray = @mysql_fetch_array($GetYr)){
	$CurYear = $YrArray["year"];
	$YrStart = $YrArray["start"];
    $YrEnd =  $YrArray["end"];

    $YrStart = strtotime($YrStart);
    $YrEnd = strtotime($YrEnd);

    if($TodaysDate >= $YrStart && $TodaysDate <= $YrEnd){
      	$CurrentYear = $CurYear;
    }
}
?>