<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?PHP echo $cur_page_title; ?></title>
<link rel="stylesheet" type="text/css" media="screen" href="<?PHP echo DIR_PATH."css/site.css" ?>">
<link rel="stylesheet" type="text/css" media="print" href="<?PHP echo DIR_PATH."css/print.css" ?>">
<script language="javascript" type="text/javascript" src="<?PHP echo DIR_PATH."includes/site.js"; ?>"></script>
<script language="javascript" type="text/javascript">
<!--
m_21010 = 0;
m_21020 = 0;
<?PHP 
if($_SESSION["ses_super_admin"] == 1 || $_SESSION["ses_is_admin"] == 1){
	echo "m_21030 = 2;\n";
}else{
	echo "m_21030 = 1;\n";	
}
?>

//-->
</script>
<script language="JavaScript" type="text/JavaScript">
function hideitem(selectcnt) {
	if(! selectcnt)
		return;
	
	if(document.all){
		for(i=0; i<selectcnt; i++){
			ObName = "HideItem"+i;
		    document.getElementById(ObName).style.visibility = 'hidden';
		}
	}
}
function showitem(selectcnt) {
	if(! selectcnt)
		return;	
	if(document.all){
		for(i=0; i<selectcnt; i++){
			ObName = "HideItem"+i;
		    document.getElementById(ObName).style.visibility = 'visible';
		}
	}
}
</script>
<?PHP echo $hdr_addin; ?>
</head>
<body style="margin-top:0; margin-left:0;" <?PHP echo $bdy_addin; ?>>
<div id="header">
 <img src="<?PHP echo DIR_PATH."images/company_logo.gif"; ?>" alt="my company" width="165" height="70" border="0">
 <div id="pagetitle"><?PHP echo $hdr_detail; ?></div>
	<?PHP require(DIR_PATH."includes/menu.inc.php");//Menu pages ?>    
</div>
<?PHP
	if(isset($error_message))
	{ 
		echo '<span class="textError">'.$error_message.'</span>';
	}elseif(isset($good_message))
	{
		echo '<span class="textGood">* '.$good_message.'</span>';	
	}
require_once(DIR_PATH."includes/config.inc.php");	
?>