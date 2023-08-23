<?PHP
/******************************************************************************
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
//$Page_css ='<link rel="stylesheet" href="'.DIR_PATH.'css/main.css" type="text/css">';
$Page_css ='<link rel="stylesheet" type="text/css" href="'.DIR_PATH.'css/site.css">';

$content .= '
<html>
<head>
<title>Employee Paid Time Off Hourly Request Form</title>
'.$Page_css.'
</head>
<body>
<table border="1" cellpadding="0" cellspacing="2" width="670" style="border-color:#000000">
  <tr>
    <td>
      <table border="1" cellspacing="2" width="100%" style="border-color:#000000">
        <tr>
          <td>&nbsp;
            <div align="center">
              <table border="1" cellpadding="0" cellspacing="0" style="border-color:#000000">
                <tr>
                  <td>&nbsp;&nbsp;&nbsp;&nbsp;<b><span style="font-family: Verdana, Arial, sans-serif; font-size: 12pt;">Employee Paid Time Off Hourly Request Form</span></b>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
              </table>
            </div>
            <div align="center">
              <table border="0" cellpadding="0" cellspacing="0" width="90%">
                <tr>
                  <td width="50%">&nbsp;</td>
                  <td width="50%"></td>
                </tr>
                <tr>
                  <td width="50%"><b>'.$first_name.' '.$last_name.'</b></td>
                  <td width="50%" align="center">
                    <b>'.$week_ending.'</b></td>
                </tr>
                <tr>
                  <td width="50%" height="2"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="250" height="1"></td>
                  <td width="50%" height="2" align="center">
                    <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="100" height="1"></td>
                </tr>
                <tr>
                  <td width="50%">Name</td>
                  <td width="50%" align="center">
                    Week Ending</td>
                </tr>
                <tr>
                  <td width="600" colspan="2">
                    <hr noshade style="color:#000000" align="left">
                  </td>
                </tr>
              </table>
            </div>
            <div align="center">
              <table border="0" cellpadding="0" cellspacing="0" width="645">
                <tr>
                  <td width="220">&nbsp;&nbsp;</td>
                  <td width="150">&nbsp;</td>
                  <td width="25" align="center">&nbsp;</td>
                  <td width="250">&nbsp;</td>
                </tr>
                <tr>
                  <td align="center" width="220" height="30" style="text-decoration: underline;">Date</td>
                  <td align="center" width="150" height="30" style="text-decoration: underline;">No. Of Hours</td>
                  <td align="center" width="25" height="30" style="text-decoration: underline;"></td>
	              <td align="left" width="250" height="30" style="text-decoration: underline;">Type of Leave</td>
              </tr>
              <tr>
                <td width="220"></td>
                <td width="150"></td>
                <td width="25" align="center"></td>
                <td width="250" style="font-size: 8pt;">(Please mark one)</td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$vacation.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$vacation_hrs.'</td>
              <td width="25" align="center" height="30" valign="bottom">';

        if($vacation != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'
                </td>
              <td width="250" height="30" valign="bottom">Vacation</td>
              </tr>
              <tr>
                <td width="220" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$personal.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$personal_hrs.'</td>
                <td width="25" align="center" height="30" valign="bottom">';

        if($personal != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'</td>
                <td width="250" height="30" valign="bottom">Personal</td>
              </tr>
              <tr>
                <td width="220" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$eom.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$eom_hrs.'</td>
                <td width="25" align="center" height="30" valign="bottom">';

        if($eom != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'</td>
                <td width="250" height="30" valign="bottom">EE of the Month</td>
              </tr>
              <tr>
                <td width="220" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$funeral.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$funeral_hrs.'</td>
                <td width="25" align="center" height="30" valign="bottom">';

        if($funeral != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'</td>
                <td width="250" height="30" valign="bottom">Funeral</td>
              </tr>
              <tr>
                <td width="220" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$jury.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$jury_hrs.'</td>
                <td width="25" align="center" height="30" valign="bottom">';

        if($jury != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'</td>
                <td width="250" height="30" valign="bottom">Jury</td>
              </tr>
              <tr>
                <td width="220" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$new_year.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$new_year_hrs.'</td>
                <td width="25" align="center" height="30" valign="bottom">';

        if($new_year != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'</td>
                <td width="250" height="30" valign="bottom">New Year’s Holiday</td>
              </tr>
              <tr>
                <td width="220" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$labor_day.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$labor_day_hrs.'</td>
                <td width="25" align="center" height="30" valign="bottom">';

        if($labor_day != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'</td>
                <td width="250" height="30" valign="bottom">Labor Day Holiday</td>
              </tr>
              <tr>
                <td width="220" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$july4.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$july4_hrs.'</td>
                <td width="25" align="center" height="30" valign="bottom">';

        if($july4 != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'</td>
                <td width="250" height="30" valign="bottom">4th of July Holiday</td>
              </tr>
              <tr>
                <td width="220" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$mem_day.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$mem_day_hrs.'</td>
                <td width="25" align="center" height="30" valign="bottom">';

        if($mem_day != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'</td>
                <td width="250" height="30" valign="bottom">Memorial Day Holiday</td>
              </tr>
              <tr>
                <td width="220" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" align="center">
                  <img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              <tr>
                <td width="220" height="30" align="center" valign="bottom">
                '.$safety.'</td>
                <td width="150" height="30" align="center" valign="bottom">
                '.$safety_hrs.'</td>
                <td width="25" align="center" height="30" valign="bottom">';

        if($safety != ""){
			$mark = "X";
        }else{
			$mark = "";
		}
                $content .=
                $mark .'</td>
                <td width="250" height="30" valign="bottom">Safety</td>
              </tr>
              <tr>
                <td width="220" height="1" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="200" height="1"></td>
                <td width="150" height="1" align="center"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="110" height="1"></td>
                <td width="25" align="center" height="1"><img border="0" src="'.DIR_PATH.'images/blk_line.gif" width="20" height="1"></td>
                <td width="250" height="1"><img border="0" src="'.DIR_PATH.'images/spacer.gif"></td>
              </tr>
              </table>
            </div>
            <div align="center">
              <table border="0" cellpadding="0" cellspacing="0" width="95%">
                <tr>
                  <td width="50%">&nbsp;&nbsp;</td>
                  <td width="50%"></td>
                </tr>
                <tr>
                  <td width="50%"></td>
                  <td width="50%"></td>
                </tr>
                <tr>
                  <td width="50%">____________________________</td>
                  <td width="50%">______________________________________</td>
                </tr>
                <tr>
                  <td width="50%">Date Signed</td>
                  <td width="50%">Supervisor\'s Signature&nbsp;</td>
                </tr>
              </table>
            </div>
            <div align="center">
              <table border="0" cellpadding="0" cellspacing="0" width="95%">
                <tr>
                  <td width="50%">&nbsp;</td>
                  <td width="50%"></td>
                </tr>
                <tr>
                  <td width="100%" colspan="2">
                    <hr noshade style="color:#000000" align="left">
                  </td>
                </tr>
                <tr>
                  <td width="100%" colspan="2">&nbsp;</td>
                </tr>
                <tr>
                  <td width="100%" colspan="2" height="25"><b>For Funeral Leave:</b>_________________________/__________________________________</td>
                </tr>
                <tr>
                  <td width="50%" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name of deceased</td>
                  <td width="50%" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Relationship to employee</td>
                </tr>
                <tr>
                  <td width="50%" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    _________________</td>
                  <td width="50%" height="25">__________________</td>
                </tr>
                <tr>
                  <td width="50%" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Date of death</td>
                  <td width="50%" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date of Funeral</td>
                </tr>
                <tr>
                  <td width="100%" colspan="2" height="25">&nbsp;&nbsp;&nbsp;
                    _________________________________________________________________________</td>
                </tr>
                <tr>
                  <td width="100%" colspan="2" height="25">&nbsp;&nbsp;&nbsp; Location of
                    Funeral</td>
                </tr>
                <tr>
                  <td width="100%" colspan="2" height="25">&nbsp;&nbsp;&nbsp;
                    _________________________________________________________________________</td>
                </tr>
                <tr>
                  <td width="100%" colspan="2" height="25">&nbsp;&nbsp;&nbsp; Address where flowers/memorial may be sent to</td>
                </tr>
                <tr>
                  <td width="50%" height="25">&nbsp;</td>
                  <td width="50%" height="25"></td>
                </tr>
                <tr>
                  <td width="100%" colspan="2" height="25">&nbsp;&nbsp;&nbsp; CC (funeral notice only): 	Nancy Throckmorton, Human Resources</td>
                </tr>
                <tr>
                  <td width="50%" height="25"></td>
                  <td width="50%" height="25"></td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>


</body>

</html>
';

echo $content;
?>