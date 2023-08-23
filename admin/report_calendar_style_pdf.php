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
if($_SESSION["ses_is_admin"] != 1){
	header ("Location: ".DIR_PATH."index.php");
}

$GoToDay = urldecode($_GET["showmonth"]);

//SET VARIABLES
if(!empty($GoToDay)){
	$StartDate=date("m/d/y",strtotime ("$GoToDay"));
}else{
	if(empty($StartDate)){
		$StartDate=date("m/d/y");
	}
}

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year


//SET DEPARTMENT
$DeptID = $_SESSION["ses_dept_id"];
$dept_ses_name = $_SESSION["ses_dept_name"];

$CurrentDate=date("m/1/y", strtotime ("$StartDate"));
$setMonth=date("m",strtotime ($CurrentDate));
$BeginWeek=date("m",strtotime ($CurrentDate));
$EndWeek=date("m",strtotime ($CurrentDate));


require(DIR_PATH."fpdf/fpdf.php");
define("FPDF_FONTPATH",DIR_PATH."fpdf/font/");
require(DIR_PATH."fpdf/mc_table.php");

class PDF extends PDF_MC_Table
{
	
	var $widths;
	var $aligns;
	
	//Page header
	function Header()
	{
		global $current_day,$StartDate;
					
	    $this->SetXY(110,2);
        $this->SetFont('Arial','B',14);
		$this->Cell(100,10,date("F",strtotime($StartDate)).' '.date("Y",strtotime($StartDate))." Vacation Report",0,0,'L');
		$this->SetFont('Arial','',8);		
		$this->SetXY(210,4);
		$this->Cell(60,10,"Print Date: ".date('l F d, Y',strtotime($current_day)),0,0,'L');
		$this->SetXY(5,15);
		
		
		//TABLE HEADERS
		$this->SetFont('Arial','B',12);
		$this->SetFillColor("111,111,111");//Set cell background color
		$this->SetTextColor("255,255,255");
		$this->SetXY(8,15);
		$this->Cell(40,8,"SUN",1,0,'C',1);
		$this->SetXY(48,15);
		$this->Cell(40,8,"MON",1,0,'C',1);
		$this->SetXY(88,15);
		$this->Cell(40,8,"TUE",1,0,'C',1);
		$this->SetXY(128,15);
		$this->Cell(40,8,"WED",1,0,'C',1);
		$this->SetXY(168,15);		
		$this->Cell(40,8,"THU",1,0,'C',1);
		$this->SetXY(208,15);		
		$this->Cell(40,8,"FRI",1,0,'C',1);
		$this->SetXY(248,15);
		$this->Cell(40,8,"SAT",1,0,'C',1);
		$this->SetXY(8,23);				
	}

	
	//Page footer
	function Footer()
	{
		//Position at 1.5 cm from bottom
	    $this->SetY(-15);
	    //Arial italic 8
	    $this->SetFont('Arial','I',8);
	    //Page number
    	$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'C');
	}	
}

$pdf=new PDF("L");
$pdf->AliasNbPages();
$pdf->Open();
$pdf->AddPage();
$pdf->SetTitle("Vacation Calendar"); 
$pdf->SetDisplayMode(100);//Set display to 100%
$pdf->SetMargins(8, 3, 2); 
$pdf->SetWidths(array(40,40,40,40,40,40,40),1);//Set column width
$pdf->SetBorders("1",0);
$pdf->SetFont('Arial','',6);		



/***************************************************************************
*** CREATE THE HEADERS FOR THE ACTUAL CALENDAR                           ***
***************************************************************************/
for($j=0;$j<6;$j++)
{
	if($BeginWeek==$setMonth||$EndWeek==$setMonth)
	{
		switch(date("w",strtotime($CurrentDate)))
		{
			case 0:
				$DaysToAd=array("","+1 days","+2 days","+3 days","+4 days","+5 days","+6 days");
				break;
			case 1:
				$DaysToAd=array("-1 days","","+1 days","+2 days","+3 days","+4 days","+5 days");
				break;
			case 2:
				$DaysToAd=array("-2 days","-1 days","","+1 days","+2 days","+3 days","+4 days");
				break;
			case 3:
				$DaysToAd=array("-3 days","-2 days","-1 days","","+1 days","+2 days","+3 days");
				break;
			case 4:
				$DaysToAd=array("-4 days","-3 days","-2 days","-1 days","","+1 days","+2 days");
				break;
			case 5:
				$DaysToAd=array("-5 days","-4 days","-3 days","-2 days","-1 days","","+1 days");
				break;
			case 6:
				$DaysToAd=array("-6 days","-5 days","-4 days","-3 days","-2 days","-1 days","");
				break;
		}


        /**************************************************
        ** WRITE IN THE WEEKLY DATE BOXES FOR THE MONTH ***
        **************************************************/
      
		for($i=0;$i<7;$i++)
		{
		
			//IF NOT THE CURRENT MONTH. MAKE THE BUTTON SMALL AND THE
            //BUTTON TEXT ITALIC AND THE CELL BACKGROUND DARK GRAY
			if(date("m",strtotime ("$CurrentDate $DaysToAd[$i]"))!=$setMonth)
			{
                $FontColor[$i]="255*0*0";
				$BGcolor[$i]="225*223*223";
				
			}
			else 
			{
                $FontColor[$i]="0*0*0";
				$BGcolor[$i]="255*255*255";							
			}


			$WriteMonth[$i]= date("d",strtotime ("$CurrentDate $DaysToAd[$i]"))."\n";
            $current_date = date("Ymd",strtotime("$CurrentDate $DaysToAd[$i]"));
            //RETRIEVE ALL VACATIONS FOR THIS DATE
			$get_vacation = @mysql_query("SELECT fname,lname,hours,replacement,to_id,date_entered " .
            	"from vf_vacation LEFT JOIN vf_employee ON vf_vacation.emp_id = " .
                "vf_employee.emp_id WHERE vf_vacation.date = $current_date AND vf_employee.enabled != 'N' AND ".
                "vf_vacation.dept_id = $DeptID AND vf_vacation.deny != 'Y' ORDER BY date_entered ASC");

     		while($vacation_result = @mysql_fetch_array($get_vacation))
     		{
             		$emp_lname = $vacation_result["lname"];
             		$emp_fname = $vacation_result["fname"];
                    $emp_emp_id = $vacation_result["emp_id"];
             		$emp_to_id = $vacation_result["to_id"];
             		$emp_hours = $vacation_result["hours"];
             		$emp_rplacmnt = $vacation_result["replacement"];

             		$emp_hours = substr($emp_hours, 0, 4); 
             		
                    // $emp_full_name = $emp_emp_id;
                    $emp_full_name = $emp_fname . " " . $emp_lname;

				$get_vac_type = @mysql_query("SELECT descr FROM vf_to_type WHERE " .
                		"to_id = '$emp_to_id'");
				$type_array = @mysql_fetch_array($get_vac_type);
					$emp_to_name = $type_array[0];

                //SEE IF A REPLACEMENT IS FILLED IN
                if($emp_rplacmnt != "")
                {
                	$e_replace = "R-" . $emp_rplacmnt;
                }
                else
                {
                	$e_replace = "";
                }

                if($emp_to_id == 13)
                {
                    $WriteMonth[$i].= $emp_full_name." | ".$emp_hours." | ".$emp_to_name.$e_replace."\n";
                }
                else
                {
	            	$WriteMonth[$i].= $emp_full_name." | ".$emp_hours.$e_replace."\n";
                }
			}

		}
	
		$pdf->SetBGColor(array($BGcolor[0],$BGcolor[1],$BGcolor[2],$BGcolor[3],$BGcolor[4],$BGcolor[5],$BGcolor[6]),1);
		$pdf->SetTxtColor(array($FontColor[0],$FontColor[1],$FontColor[2],$FontColor[3],$FontColor[4],$FontColor[5],$FontColor[6]),1);
	    $pdf->SetAligns(array("L","L","L","L","L","L","L"),1);//Set the alignment of each column
		$pdf->Row(array($WriteMonth[0],$WriteMonth[1],$WriteMonth[2],$WriteMonth[3],$WriteMonth[4],$WriteMonth[5],$WriteMonth[6]),1,1);//Set value of each column, and 1 for border, 1 background fill						


		$CurrentDate=date("m/d/y",strtotime("$CurrentDate +1 week"));
		$StartDateofWeek=date("w",strtotime ($CurrentDate));
		$EndofWeek=6 - $StartDateofWeek;
		$BeginWeek=date("m",strtotime ("$CurrentDate -$StartDateofWeek days"));
		$EndWeek=date("m",strtotime ("$CurrentDate +$EndofWeek days"));
	}
}

$pdf->Output();

?>