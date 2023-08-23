<?php

class PDF_MC_Table extends FPDF
{
var $widths;
var $height;
var $bordercolors;
var $borders;
var $aligns;
var $bgcolors;
var $textcolors;
var $textfonts;


function SetWidths($w,$arry)
{
	//Format to call this function for multiple columns (4 columns in this case)
	//$pdf->SetWidths(array(10,7,7,17),1);
	//or
	//$pdf->SetWidths(10,0);
	if($arry == 1){		
		//Set the array of column widths
		$this->widths=$w;
	}else{
		$this->width=$w;		
	}	
}

function SetHeight($h)
{
	//Format to call this function
	//$pdf->SetHeight(4);
	
	//Set the array of column widths
	$this->height=$h;
}

function SetBorderColors($bc,$arry)
{
	
	//$pdf->SetBorderColor(array("R*G*B","R*G*B","R*G*B","R*G*B"),1);
	//or
	//$pdf->SetBorderColor("R*G*B",0);
	//Set the array of column background colors
	if($arry == 1){		
		$this->bordercolors=$bc;
	}else{
		$this->bordercolor=$bc;		
	}	
}

function SetBorders($b,$arry)
{
	//$pdf->SetBorders(array("LRTB","LRTB","LRTB","1"),1);
	//or
	//$pdf->SetBorders("LRTB",0);
	if($arry == 1){			
		//Set the array of column borders
		$this->borders=$b;
	}else{
		$this->border=$b;
	}		
}

function SetAligns($a,$arry)
{
	//Format to call this function for multiple columns (4 columns in this case)
	//$pdf->SetAligns(array("C","R","L","C"),1);//2-Center align 1-Right and 1-Left
	//or
	//$pdf->SetAligns("C",0);
	if($arry == 1){			
		//Set the array of column alignments
		$this->aligns=$a;
	}else{
		$this->align=$a;
	}
}

function SetBGColor($bc,$arry)
{
	//Format to call this function. If it is an array append a 1 to the call
	//$pdf->SetBGColor(array("R*G*B","R*G*B","R*G*B","R*G*B"),1);
	//or
	//$pdf->SetBGColor("R*G*B",0);
	
	if($arry == 1){		
		//Set the array of column background colors
		$this->bgcolors=$bc;
	}else{
		$this->bgcolor=$bc;		
	}
}

function SetTxtColor($tc,$arry)
{	
	//Format to call this function. If it is an array append a 1 to the call
	//$pdf->SetTxtColor(array("R*G*B","R*G*B","R*G*B","R*G*B"),1);
	//or
	//$pdf->SetTxtColor("R*G*B",0);
	
	if($arry == 1){	
		//Set the array of column text colors
		$this->textcolors=$tc;
	}else{
		$this->textcolor=$tc;		
	}	
}

function SetTxtFont($fnt,$arry)
{
	//Format to call this function. If it is an array. append a 1 to the call
	//$pdf->SetTxtFont(array("family*style*size","family*style*size","family*style*size","family*style*size"),1);
	//or
	//$pdf->SetTxtFont("family*style*size",0);
	
	if($arry == 1){
		//Set the array of column text font style
		$this->textfonts=$fnt;	
	}else{
		//Set the default text style
		$this->textfont=$fnt;			
	}
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
    	
		//Set column widths
		if(isset($this->widths[$i])){
			$w=$this->widths[$i];
		}elseif(isset($this->width)){	
			$w=$this->width;
		}else{
			$w = 20;
		}
		    
		//If there are border parameters, set them otherwise set to no borders
		if(isset($this->borders[$i])){
			$b=$this->borders[$i];	
		}elseif(isset($this->border)){		
			$b=$this->border;
		}else{
			'0';
		}
					
		//Set the border color
		if(isset($this->bordercolors[$i])){
			$brd_pieces = explode("*",  $this->bordercolors[$i]);
			$this->SetDrawColor($brd_pieces[0],$brd_pieces[1],$brd_pieces[2]);
		}elseif(isset($this->bordercolor)){ 
			$brd_pieces = explode("*",  $this->bordercolor);
			$this->SetDrawColor($brd_pieces[0],$brd_pieces[1],$brd_pieces[2]);
		}else{
			$this->SetDrawColor(0,0,0);
		}			
			
		//Set background color if not set to transparent
		if(isset($this->bgcolors[$i])){
			$bg_pieces = explode("*",  $this->bgcolors[$i]);
			$this->SetFillColor($bg_pieces[0],$bg_pieces[1],$bg_pieces[2]);
		}elseif(isset($this->bgcolor)){
			$bg_pieces = explode("*",  $this->bgcolor);
			$this->SetFillColor($bg_pieces[0],$bg_pieces[1],$bg_pieces[2]);
		}else{ 
			$this->SetFillColor(255,255,255); 
		}
				
		//If there are align parameters, set them otherwise set to Left justified
		if(isset($this->aligns[$i])){
			$a=$this->aligns[$i];
		}elseif(isset($this->align)){			
			$a=$this->align;
		}else{
			'L';
		}
		
		//Set the text color
		if(isset($this->textcolors[$i])){
			$txt_pieces = explode("*",  $this->textcolors[$i]);
			$this->SetTextColor($txt_pieces[0],$txt_pieces[1],$txt_pieces[2]);
		}elseif(isset($this->textcolor)){ 
			$txt_pieces = explode("*",  $this->textcolor);
			$this->SetTextColor($txt_pieces[0],$txt_pieces[1],$txt_pieces[2]);						
		}else{ 
			$this->SetTextColor(0,0,0); 
		}		
		
		
		
        //$w=$this->widths[$i];
        //$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h,"DF");
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

/*
function Row($data)
{

		//Set the text color
		if(isset($this->textcolors[$i])){
			$txt_pieces = explode("*",  $this->textcolors[$i]);
			$this->SetTextColor($txt_pieces[0],$txt_pieces[1],$txt_pieces[2]);
		}elseif(isset($this->textcolor)){ 
			$txt_pieces = explode("*",  $this->textcolor);
			$this->SetTextColor($txt_pieces[0],$txt_pieces[1],$txt_pieces[2]);						
		}else{ 
			$this->SetTextColor(0,0,0); 
		}
	
		//Set the font style
		if(isset($this->textfonts[$i])){
			$txt_pieces = explode("*",  $this->textfonts[$i]);
			$this->SetFont("$txt_pieces[0]","$txt_pieces[1]","$txt_pieces[2]");//Set font for each cell
		}elseif(isset($this->textfont)){ 
			$txt_pieces = explode("*",  $this->textfont);//Set font for the whole row
			$this->SetFont("$txt_pieces[0]","$txt_pieces[1]","$txt_pieces[2]");
		}else{
			$this->SetFont('Arial','',8);//Set as default if no text is defined 
		}
		
		//Save the current position
		$x=$this->GetX();
		$y=$this->GetY();
		
		//Draw the border
		$this->Rect($x,$y,$w,$h);
		
		//Print the text
		$this->MultiCell($w,$h,$data[$i],$b,$a,1);
				
		//Put the position to the right of the cell
		$this->SetXY($x+$w,$y);
	}
	//Go to the next line
	$this->Ln($h);
}
*/
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
}
?>
