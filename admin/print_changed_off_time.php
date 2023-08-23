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
require_once(DIR_PATH."includes/config.inc.php");
?>
<html>
<head>
<title><?PHP echo $cfg['company_abbr']; ?> Time off Changes</title>
<link rel="stylesheet" href="../css/site.css" type="text/css">
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="600">
  <tr>
    <td class="text22BkB" style="text-decoration: underline;"><?PHP echo $cfg['company_abbr']; ?> TIME OFF CHANGE FORM</td>
  </tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600">
  <tr>
    <td><form><input type="button" value="Print" onClick="javascript:window.print()"></form>
  </td>
    <td><form><input type=button value="Close This Window" onClick="javascript:window.close();"></form>
  </td>
  </tr>
</table>
<p><b>Office Copy</b></p>

<?PHP
echo '<p class="text10BkB" style="color:#FF0000">' . $_GET["header"] . '</p>';
?>

		<!--SUPERVISORS COPY-->
		<table border="0" cellpadding="0" cellspacing="0" width="600">
            <tr>
             <td class="text14BkB" style="text-decoration: underline;">NAME: <?PHP echo $_GET["ename"]; ?></td>
            </tr>
            <tr>
             <td>&nbsp;</td>
            </tr>
            <tr>
             <td><?PHP echo stripslashes($_GET["content"]); ?></td>
            </tr>
	    </table>
        <p>&nbsp;</p>
           <table border="0" cellpadding="0" cellspacing="0" width="600">
			<tr>
			 <td align="right">Date Signed:&nbsp;</td>
             <td width="100"style="border-bottom: 1 solid black">&nbsp;</td>
		     <td align="right">Employee Signature:&nbsp;</td>
			 <td width="250" style="border-bottom: 1 solid black">&nbsp;</td>
			</tr>
	        <tr>
			 <td align="right">&nbsp;</td>
			 <td align="right">&nbsp;</td>
			 <td align="right">&nbsp;</td>
	         <td align="right">&nbsp;</td>
			</tr>
	        <tr>
			 <td align="right">Date Signed:&nbsp;</td>
	         <td width="100" style="border-bottom: 1 solid black">&nbsp;</td>
	         <td align="right">Supervisor Signature:&nbsp;</td>
	         <td width="250" style="border-bottom: 1 solid black">&nbsp;</td>
			</tr>
           </table>
           <!--SEPERATOR TABLE WITH HORIZONTAL LINE-->
		   <table border="0" cellpadding="0" cellspacing="0" width="600">
			<tr>
			 <td>&nbsp;</td>
			</tr>
			<tr>
			 <td>
			  <hr style="color:black" size="1">
			 </td>
			</tr>
			<tr>
			 <td>&nbsp;</td>
			</tr>
			<tr>
			 <td>&nbsp;</td>
			</tr>
           </table>
           
<p>-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;</p>     
		<!--EMPLOYEES COPY-->
	<table border="0" cellpadding="0" cellspacing="0" width="600">
	  <tr>
	    <td class="text22BkB" style="text-decoration: underline;"><?PHP echo $cfg['company_abbr']; ?> TIME OFF CHANGE FORM</td>
	  </tr>
	</table>	
<p><b>Employee Copy</b></p>		
	<?PHP
	echo '<p class="text10BkB" style="color:#FF0000">' . $_GET["header"] . '</p>';
	?>
		<table border="0" cellpadding="0" cellspacing="0" width="600">
            <tr>
             <td class="text14BkB" style="text-decoration: underline;">NAME: <?PHP echo $_GET["ename"]; ?></td>
            </tr>
            <tr>
             <td>&nbsp;</td>
            </tr>
            <tr>
             <td><?PHP echo stripslashes($_GET["content"]); ?></td>
            </tr>
	       </table>
           		<p>&nbsp;</p>
           <table border="0" cellpadding="0" cellspacing="0" width="600">
			<tr>
			 <td align="right">Date Signed:&nbsp;</td>
             <td width="100"style="border-bottom: 1 solid black">&nbsp;</td>
		     <td align="right">Employee Signature:&nbsp;</td>
			 <td width="250" style="border-bottom: 1 solid black">&nbsp;</td>
			</tr>
	        <tr>
			 <td align="right">&nbsp;</td>
			 <td align="right">&nbsp;</td>
			 <td align="right">&nbsp;</td>
	         <td align="right">&nbsp;</td>
			</tr>
	        <tr>
			 <td align="right">Date Signed:&nbsp;</td>
	         <td width="100" style="border-bottom: 1 solid black">&nbsp;</td>
	         <td align="right">Supervisor Signature:&nbsp;</td>
	         <td width="250" style="border-bottom: 1 solid black">&nbsp;</td>
			</tr>
           </table>
</body>
</html>