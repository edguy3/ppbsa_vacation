<?php
/******************************************************************************
**  File Name: 
**  
**  Developed By: Gary Barber
**  Created: 10/27/05
**  Last modified: 
**
**  Description: 
**
*******************************************************************************/

//Clear cache
clearstatcache();
session_start();
define("DIR_PATH", "../");//you must change the path for each sub folder

//IF THE EMPLOYEE ISN'T AN ADMIN. STOP THE USER
if($_SESSION["ses_is_admin"] != 1)
{
	header ("Location: ".DIR_PATH."index.php");
}

//SET PAGE VARIBALES
$DeptID = $_SESSION["ses_dept_id"];
$weeks_to_display = $_POST["weeks_to_display"];
$sunday_choice = $_POST["sunday_choice"];
$date_selection = $_POST["date_selection"];
$submit = $_POST["submit"];
$dept_choice = $_POST["dept_choice"];

require(DIR_PATH."includes/db_info.inc.php");//database connection information
require(DIR_PATH."includes/cur_date.inc.php");//get the current fiscal year

//FORMAT THE SUNDAY THAT WAS CHOSEN
$sunday_display = strtotime("$sunday_choice");
$sunday_choice = date("m/d/y",$sunday_display);
			
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
		global $current_day;
					
	    $this->SetXY(70,2);
        $this->SetFont('Arial','B',14);
		$this->Cell(100,10,"Weekly Vacation Report",0,0,'L');
		$this->SetFont('Arial','',8);		
		$this->SetXY(135,4);
		$this->Cell(60,10,"Print Date: ".date('l F d, Y',strtotime($current_day)),0,0,'L');
		$this->SetXY(10,20);			
		$this->Line(10,12,200,12);
		$this->SetXY(5,20);
	}
	
	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}
	
	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}
	
	function Row($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++)
	        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	    $h=5*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        $this->Rect($x,$y,$w,$h);
	        //Print the text
	        $this->MultiCell($w,5,$data[$i],0,$a);
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->Ln($h);
	}
	
	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}
	
	function NbLines($w,$txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
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

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->Open();
$pdf->AddPage();
$pdf->SetDisplayMode(100);//Set display to 100%
$pdf->SetMargins(5, 3, 2); 
$pdf->SetWidths(array(40,30,15,40,70),1);//Set column width
$pdf->SetBorders("1",0);
		

		
//DO THIS FOR THE NUMBER OF WEEKS TO DISPLAY
for ($a=0; $a<$weeks_to_display; $a++) 
{

    //DO THIS FOR THE 7 DAYS OF THE WEEK
    for ($i=0; $i<7; $i++) 
    {
        //USE TO CALCULATE EACH OF THE & DAYS
        $add_days = "+".$i."days";
        $current_day = date("Ymd",strtotime("$sunday_choice $add_days"));		
		        
		//TABLE HEADERS		
		$y=$pdf->GetY();		
		$y = $y + 2;
		$pdf->SetXY(5,$y);
		$pdf->SetFont('Arial','B',12);
	    $pdf->CheckPageBreak(20); //Make sure the page breaks if needed   
		$pdf->Cell(100,8,date("l F d, Y",strtotime($current_day)),0,0,'L');		
		$y=$pdf->GetY();
		$y = $y + 6;
		$pdf->SetXY(5,$y);
		$pdf->SetFillColor("224,224,224");//Set cell background color
		$pdf->SetFont('Arial','B',8);		
		$pdf->Cell(40,6,"NAME",1,0,'C',1);
		$pdf->SetXY(45,$y);
		$pdf->Cell(30,6,"TYPE",1,0,'C',1);
		$pdf->SetXY(75,$y);
		$pdf->Cell(15,6,"HOURS",1,0,'C',1);
		$pdf->SetXY(90,$y);
		$pdf->Cell(40,6,"RELIEF",1,0,'C',1);
		$pdf->SetXY(130,$y);
		$pdf->Cell(70,6,"NOTES",1,0,'C',1);
		$y = $y + 6;		
		$pdf->SetXY(5,$y);
		$pdf->SetFont('Arial','',8);		        

        //IF THE WHOLE DEPT IS TO BE DISPLAYED USE THIS OTHERWISE USE THE SECOND PART
		//TO SELECT BY SUB DEPT
        if($dept_choice == "default".$DeptID)
        {
	    	$sql_people = mysql_query("SELECT * FROM `vf_vacation` WHERE `date` = '$current_day'
            		AND `dept_id` = '$DeptID' ORDER BY `date_entered` ASC");
		}
		else
		{
	    	$sql_people = mysql_query("SELECT * FROM vf_vacation,vf_employee WHERE vf_vacation.date = '$current_day'
            		AND vf_vacation.dept_id = '$DeptID' AND vf_employee.sub_dept_id = '$dept_choice'
                    AND vf_vacation.emp_id = vf_employee.emp_id ORDER BY vf_vacation.date_entered ASC");
		}

        while($people_array = mysql_fetch_array($sql_people))
        {
			$vac_emp_id = $people_array["emp_id"];
			$vac_hours = $people_array["hours"];
            $vac_to_id = $people_array["to_id"];
            $vac_apprv_by = $people_array["apprv_by"];
            $vac_replacement = $people_array["replacement"];
            $vac_note = $people_array["note"];
            $vac_deny = $people_array["deny"];

            //GET THE VACATION TYPE
            $get_vacation = @mysql_query("SELECT descr FROM vf_to_type WHERE to_id = '$vac_to_id'");
            $vacation_name = mysql_fetch_array($get_vacation);
				$vacation_description = $vacation_name["descr"];
			

            //GET THE EMPLOYEES NAME
            $get_employee = @mysql_query("SELECT fname,lname,enabled FROM vf_employee WHERE
            		emp_id = '$vac_emp_id'");
            $emp_name = mysql_fetch_array($get_employee);
            	$emp_lname = $emp_name["lname"];
            	$emp_fname = $emp_name["fname"];
            	$emp_enabled = $emp_name["enabled"];
				$emp_full_name = $emp_fname . " " . $emp_lname;		
			
			//ONLY DISPLAY DATA IF THE USER IS ENABLED	
			if($emp_enabled != 'N')
			{								
			    $pdf->SetAligns(array("L","C","C","L","L"),1);//Set the alignment of each column
				$pdf->Row(array($emp_full_name,$vacation_description,$vac_hours,$vac_replacement,$vac_note),1,1);//Set value of each column, and 1 for border, 1 background fill						
			}
    	}
	}		
	$sunday_choice = date("m/d/y",strtotime("$sunday_choice +7days"));
}	 
	$pdf->Output();			
		
?>