<?PHP
/******************************************************************************
**  File Name: logout.php
**  Description: Clears session variables to log user off program
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

session_start();//start the session

session_unset();//unset all session variables to clear the session

print "<script language='javascript'>window.close();</script>";
exit();

?>