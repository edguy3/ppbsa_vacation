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
$pass = $_POST["pass"];
$password = $_POST["password"];

if(isset($pass)){
	echo md5($password);
}

?>

<html>

<head>
<title>New Page 1</title>
</head>

<body>

<form method="POST" action="">
  <p>&nbsp;</p>
  <p>PASSWORD: <input type="password" name="password" size="20">
  <input type="submit" value="Submit" name="pass"></p>
  <p>&nbsp;</p>
</form>

</body>

</html>
