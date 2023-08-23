<div id="colorWheel" style="background-color:#C0C0C0; width:165px;">
<style>
img.colorwheelImg {
	border:0;
	height: 5px; 
	width: 5px;
}
#colorchg {
	width: 100%;
	height: 10px;
	background-color: "#000000";
}
</style>  
<script language="javascript" type="text/javascript">
	var lock = 0;

	function changeClr(hval)
	{ 		
		var hval;
	
		if(lock == 0)
		{
			document.getElementById('colorchg').style.backgroundColor = hval;
			//document.getElementById('text_color').value = hval;
		}
	}
	
	function selectClr(hval)
	{
		var hval;
	
		if(lock == 0)
		{
			lock = 1;
			document.getElementById('text_color').value = hval;
			document.getElementById('colorchg').style.backgroundColor = hval;
		}
		else
		{
			lock = 0;
			document.getElementById('text_color').value = '';			
		}			
	}
</script>



<table>
 <tr>
  <td colspan="18"><div id="colorchg"></div></td>
 </tr>
 <tr>
  <td bgcolor="#000000"><a href="JavaScript:selectClr('#000000')" onMouseOver="changeClr('#000000'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#000033"><a href="JavaScript:selectClr('#000033')" onMouseOver="changeClr('#000033'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#000066"><a href="JavaScript:selectClr('#000066')" onMouseOver="changeClr('#000066'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#000099"><a href="JavaScript:selectClr('#000099')" onMouseOver="changeClr('#000099'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#0000cc"><a href="JavaScript:selectClr('#0000cc')" onMouseOver="changeClr('#0000cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#0000ff"><a href="JavaScript:selectClr('#0000ff')" onMouseOver="changeClr('#0000ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#006600"><a href="JavaScript:selectClr('#006600')" onMouseOver="changeClr('#006600'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#006633"><a href="JavaScript:selectClr('#006633')" onMouseOver="changeClr('#006633'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#006666"><a href="JavaScript:selectClr('#006666')" onMouseOver="changeClr('#006666'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#006699"><a href="JavaScript:selectClr('#006699')" onMouseOver="changeClr('#006699'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#0066cc"><a href="JavaScript:selectClr('#0066cc')" onMouseOver="changeClr('#0066cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#0066ff"><a href="JavaScript:selectClr('#0066ff')" onMouseOver="changeClr('#0066ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00cc00"><a href="JavaScript:selectClr('#00cc00')" onMouseOver="changeClr('#00cc00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00cc33"><a href="JavaScript:selectClr('#00cc33')" onMouseOver="changeClr('#00cc33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00cc66"><a href="JavaScript:selectClr('#00cc66')" onMouseOver="changeClr('#00cc66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00cc99"><a href="JavaScript:selectClr('#00cc99')" onMouseOver="changeClr('#00cc99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00cccc"><a href="JavaScript:selectClr('#00cccc')" onMouseOver="changeClr('#00cccc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00ccff"><a href="JavaScript:selectClr('#00ccff')" onMouseOver="changeClr('#00ccff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#003300"><a href="JavaScript:selectClr('#003300')" onMouseOver="changeClr('#003300'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#003333"><a href="JavaScript:selectClr('#003333')" onMouseOver="changeClr('#003333'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#003366"><a href="JavaScript:selectClr('#003366')" onMouseOver="changeClr('#003366'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#003399"><a href="JavaScript:selectClr('#003399')" onMouseOver="changeClr('#003399'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#0033cc"><a href="JavaScript:selectClr('#0033cc')" onMouseOver="changeClr('#0033cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#0033ff"><a href="JavaScript:selectClr('#0033ff')" onMouseOver="changeClr('#0033ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#009900"><a href="JavaScript:selectClr('#009900')" onMouseOver="changeClr('#009900'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#009933"><a href="JavaScript:selectClr('#009933')" onMouseOver="changeClr('#009933'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#009966"><a href="JavaScript:selectClr('#009966')" onMouseOver="changeClr('#009966'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#009999"><a href="JavaScript:selectClr('#009999')" onMouseOver="changeClr('#009999'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#0099cc"><a href="JavaScript:selectClr('#0099cc')" onMouseOver="changeClr('#0099cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#0099ff"><a href="JavaScript:selectClr('#0099ff')" onMouseOver="changeClr('#0099ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00ff00"><a href="JavaScript:selectClr('#00ff00')" onMouseOver="changeClr('#00ff00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00ff33"><a href="JavaScript:selectClr('#00ff33')" onMouseOver="changeClr('#00ff33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00ff66"><a href="JavaScript:selectClr('#00ff66')" onMouseOver="changeClr('#00ff66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00ff99"><a href="JavaScript:selectClr('#00ff99')" onMouseOver="changeClr('#00ff99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00ffcc"><a href="JavaScript:selectClr('#00ffcc')" onMouseOver="changeClr('#00ffcc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#00ffff"><a href="JavaScript:selectClr('#00ffff')" onMouseOver="changeClr('#00ffff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#330000"><a href="JavaScript:selectClr('#330000')" onMouseOver="changeClr('#330000'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#330033"><a href="JavaScript:selectClr('#330033')" onMouseOver="changeClr('#330033'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#330066"><a href="JavaScript:selectClr('#330066')" onMouseOver="changeClr('#330066'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#330099"><a href="JavaScript:selectClr('#330099')" onMouseOver="changeClr('#330099'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#3300cc"><a href="JavaScript:selectClr('#3300cc')" onMouseOver="changeClr('#3300cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#3300ff"><a href="JavaScript:selectClr('#3300ff')" onMouseOver="changeClr('#3300ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#336600"><a href="JavaScript:selectClr('#336600')" onMouseOver="changeClr('#336600'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#336633"><a href="JavaScript:selectClr('#336633')" onMouseOver="changeClr('#336633'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#336666"><a href="JavaScript:selectClr('#336666')" onMouseOver="changeClr('#336666'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#336699"><a href="JavaScript:selectClr('#336699')" onMouseOver="changeClr('#336699'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#3366cc"><a href="JavaScript:selectClr('#3366cc')" onMouseOver="changeClr('#3366cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#3366ff"><a href="JavaScript:selectClr('#3366ff')" onMouseOver="changeClr('#3366ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33cc00"><a href="JavaScript:selectClr('#33cc00')" onMouseOver="changeClr('#33cc00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33cc33"><a href="JavaScript:selectClr('#33cc33')" onMouseOver="changeClr('#33cc33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33cc66"><a href="JavaScript:selectClr('#33cc66')" onMouseOver="changeClr('#33cc66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33cc99"><a href="JavaScript:selectClr('#33cc99')" onMouseOver="changeClr('#33cc99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33cccc"><a href="JavaScript:selectClr('#33cccc')" onMouseOver="changeClr('#33cccc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33ccff"><a href="JavaScript:selectClr('#33ccff')" onMouseOver="changeClr('#33ccff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#333300"><a href="JavaScript:selectClr('#333300')" onMouseOver="changeClr('#333300'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#333333"><a href="JavaScript:selectClr('#333333')" onMouseOver="changeClr('#333333'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#333366"><a href="JavaScript:selectClr('#333366')" onMouseOver="changeClr('#333366'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#333399"><a href="JavaScript:selectClr('#333399')" onMouseOver="changeClr('#333399'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#3333cc"><a href="JavaScript:selectClr('#3333cc')" onMouseOver="changeClr('#3333cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#3333ff"><a href="JavaScript:selectClr('#3333ff')" onMouseOver="changeClr('#3333ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#339900"><a href="JavaScript:selectClr('#339900')" onMouseOver="changeClr('#339900'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#339933"><a href="JavaScript:selectClr('#339933')" onMouseOver="changeClr('#339933'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#339966"><a href="JavaScript:selectClr('#339966')" onMouseOver="changeClr('#339966'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#339999"><a href="JavaScript:selectClr('#339999')" onMouseOver="changeClr('#339999'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#3399cc"><a href="JavaScript:selectClr('#3399cc')" onMouseOver="changeClr('#3399cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#3399ff"><a href="JavaScript:selectClr('#3399ff')" onMouseOver="changeClr('#3399ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33ff00"><a href="JavaScript:selectClr('#33ff00')" onMouseOver="changeClr('#33ff00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33ff33"><a href="JavaScript:selectClr('#33ff33')" onMouseOver="changeClr('#33ff33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33ff66"><a href="JavaScript:selectClr('#33ff66')" onMouseOver="changeClr('#33ff66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33ff99"><a href="JavaScript:selectClr('#33ff99')" onMouseOver="changeClr('#33ff99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33ffcc"><a href="JavaScript:selectClr('#33ffcc')" onMouseOver="changeClr('#33ffcc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#33ffff"><a href="JavaScript:selectClr('#33ffff')" onMouseOver="changeClr('#33ffff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#660000"><a href="JavaScript:selectClr('#660000')" onMouseOver="changeClr('#660000'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#660033"><a href="JavaScript:selectClr('#660033')" onMouseOver="changeClr('#660033'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#660066"><a href="JavaScript:selectClr('#660066')" onMouseOver="changeClr('#660066'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#660099"><a href="JavaScript:selectClr('#660099')" onMouseOver="changeClr('#660099'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#6600cc"><a href="JavaScript:selectClr('#6600cc')" onMouseOver="changeClr('#6600cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#6600ff"><a href="JavaScript:selectClr('#6600ff')" onMouseOver="changeClr('#6600ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#666600"><a href="JavaScript:selectClr('#666600')" onMouseOver="changeClr('#666600'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#666633"><a href="JavaScript:selectClr('#666633')" onMouseOver="changeClr('#666633'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#666666"><a href="JavaScript:selectClr('#666666')" onMouseOver="changeClr('#666666'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#666699"><a href="JavaScript:selectClr('#666699')" onMouseOver="changeClr('#666699'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#6666cc"><a href="JavaScript:selectClr('#6666cc')" onMouseOver="changeClr('#6666cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#6666ff"><a href="JavaScript:selectClr('#6666ff')" onMouseOver="changeClr('#6666ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66cc00"><a href="JavaScript:selectClr('#66cc00')" onMouseOver="changeClr('#66cc00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66cc33"><a href="JavaScript:selectClr('#66cc33')" onMouseOver="changeClr('#66cc33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66cc66"><a href="JavaScript:selectClr('#66cc66')" onMouseOver="changeClr('#66cc66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66cc99"><a href="JavaScript:selectClr('#66cc99')" onMouseOver="changeClr('#66cc99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66cccc"><a href="JavaScript:selectClr('#66cccc')" onMouseOver="changeClr('#66cccc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66ccff"><a href="JavaScript:selectClr('#66ccff')" onMouseOver="changeClr('#66ccff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#663300"><a href="JavaScript:selectClr('#663300')" onMouseOver="changeClr('#663300'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#663333"><a href="JavaScript:selectClr('#663333')" onMouseOver="changeClr('#663333'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#663366"><a href="JavaScript:selectClr('#663366')" onMouseOver="changeClr('#663366'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#663399"><a href="JavaScript:selectClr('#663399')" onMouseOver="changeClr('#663399'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#6633cc"><a href="JavaScript:selectClr('#6633cc')" onMouseOver="changeClr('#6633cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#6633ff"><a href="JavaScript:selectClr('#6633ff')" onMouseOver="changeClr('#6633ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#669900"><a href="JavaScript:selectClr('#669900')" onMouseOver="changeClr('#669900'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#669933"><a href="JavaScript:selectClr('#669933')" onMouseOver="changeClr('#669933'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#669966"><a href="JavaScript:selectClr('#669966')" onMouseOver="changeClr('#669966'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#669999"><a href="JavaScript:selectClr('#669999')" onMouseOver="changeClr('#669999'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#6699cc"><a href="JavaScript:selectClr('#6699cc')" onMouseOver="changeClr('#6699cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#6699ff"><a href="JavaScript:selectClr('#6699ff')" onMouseOver="changeClr('#6699ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66ff00"><a href="JavaScript:selectClr('#66ff00')" onMouseOver="changeClr('#66ff00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66ff33"><a href="JavaScript:selectClr('#66ff33')" onMouseOver="changeClr('#66ff33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66ff66"><a href="JavaScript:selectClr('#66ff66')" onMouseOver="changeClr('#66ff66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66ff99"><a href="JavaScript:selectClr('#66ff99')" onMouseOver="changeClr('#66ff99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66ffcc"><a href="JavaScript:selectClr('#66ffcc')" onMouseOver="changeClr('#66ffcc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#66ffff"><a href="JavaScript:selectClr('#66ffff')" onMouseOver="changeClr('#66ffff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#990000"><a href="JavaScript:selectClr('#990000')" onMouseOver="changeClr('#990000'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#990033"><a href="JavaScript:selectClr('#990033')" onMouseOver="changeClr('#990033'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#990066"><a href="JavaScript:selectClr('#990066')" onMouseOver="changeClr('#990066'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#990099"><a href="JavaScript:selectClr('#990099')" onMouseOver="changeClr('#990099'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#9900cc"><a href="JavaScript:selectClr('#9900cc')" onMouseOver="changeClr('#9900cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#9900ff"><a href="JavaScript:selectClr('#9900ff')" onMouseOver="changeClr('#9900ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#996600"><a href="JavaScript:selectClr('#996600')" onMouseOver="changeClr('#996600'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#996633"><a href="JavaScript:selectClr('#996633')" onMouseOver="changeClr('#996633'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#996666"><a href="JavaScript:selectClr('#996666')" onMouseOver="changeClr('#996666'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#996699"><a href="JavaScript:selectClr('#996699')" onMouseOver="changeClr('#996699'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#9966cc"><a href="JavaScript:selectClr('#9966cc')" onMouseOver="changeClr('#9966cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#9966ff"><a href="JavaScript:selectClr('#9966ff')" onMouseOver="changeClr('#9966ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99cc00"><a href="JavaScript:selectClr('#99cc00')" onMouseOver="changeClr('#99cc00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99cc33"><a href="JavaScript:selectClr('#99cc33')" onMouseOver="changeClr('#99cc33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99cc66"><a href="JavaScript:selectClr('#99cc66')" onMouseOver="changeClr('#99cc66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99cc99"><a href="JavaScript:selectClr('#99cc99')" onMouseOver="changeClr('#99cc99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99cccc"><a href="JavaScript:selectClr('#99cccc')" onMouseOver="changeClr('#99cccc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99ccff"><a href="JavaScript:selectClr('#99ccff')" onMouseOver="changeClr('#99ccff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#993300"><a href="JavaScript:selectClr('#993300')" onMouseOver="changeClr('#993300'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#993333"><a href="JavaScript:selectClr('#993333')" onMouseOver="changeClr('#993333'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#993366"><a href="JavaScript:selectClr('#993366')" onMouseOver="changeClr('#993366'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#993399"><a href="JavaScript:selectClr('#993399')" onMouseOver="changeClr('#993399'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#9933cc"><a href="JavaScript:selectClr('#9933cc')" onMouseOver="changeClr('#9933cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#9933ff"><a href="JavaScript:selectClr('#9933ff')" onMouseOver="changeClr('#9933ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#999900"><a href="JavaScript:selectClr('#999900')" onMouseOver="changeClr('#999900'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#999933"><a href="JavaScript:selectClr('#999933')" onMouseOver="changeClr('#999933'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#999966"><a href="JavaScript:selectClr('#999966')" onMouseOver="changeClr('#999966'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#999999"><a href="JavaScript:selectClr('#999999')" onMouseOver="changeClr('#999999'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#9999cc"><a href="JavaScript:selectClr('#9999cc')" onMouseOver="changeClr('#9999cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#9999ff"><a href="JavaScript:selectClr('#9999ff')" onMouseOver="changeClr('#9999ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99ff00"><a href="JavaScript:selectClr('#99ff00')" onMouseOver="changeClr('#99ff00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99ff33"><a href="JavaScript:selectClr('#99ff33')" onMouseOver="changeClr('#99ff33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99ff66"><a href="JavaScript:selectClr('#99ff66')" onMouseOver="changeClr('#99ff66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99ff99"><a href="JavaScript:selectClr('#99ff99')" onMouseOver="changeClr('#99ff99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99ffcc"><a href="JavaScript:selectClr('#99ffcc')" onMouseOver="changeClr('#99ffcc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#99ffff"><a href="JavaScript:selectClr('#99ffff')" onMouseOver="changeClr('#99ffff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#cc0000"><a href="JavaScript:selectClr('#cc0000')" onMouseOver="changeClr('#cc0000'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc0033"><a href="JavaScript:selectClr('#cc0033')" onMouseOver="changeClr('#cc0033'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc0066"><a href="JavaScript:selectClr('#cc0066')" onMouseOver="changeClr('#cc0066'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc0099"><a href="JavaScript:selectClr('#cc0099')" onMouseOver="changeClr('#cc0099'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc00cc"><a href="JavaScript:selectClr('#cc00cc')" onMouseOver="changeClr('#cc00cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc00ff"><a href="JavaScript:selectClr('#cc00ff')" onMouseOver="changeClr('#cc00ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc6600"><a href="JavaScript:selectClr('#cc6600')" onMouseOver="changeClr('#cc6600'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc6633"><a href="JavaScript:selectClr('#cc6633')" onMouseOver="changeClr('#cc6633'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc6666"><a href="JavaScript:selectClr('#cc6666')" onMouseOver="changeClr('#cc6666'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc6699"><a href="JavaScript:selectClr('#cc6699')" onMouseOver="changeClr('#cc6699'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc66cc"><a href="JavaScript:selectClr('#cc66cc')" onMouseOver="changeClr('#cc66cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc66ff"><a href="JavaScript:selectClr('#cc66ff')" onMouseOver="changeClr('#cc66ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cccc00"><a href="JavaScript:selectClr('#cccc00')" onMouseOver="changeClr('#cccc00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cccc33"><a href="JavaScript:selectClr('#cccc33')" onMouseOver="changeClr('#cccc33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cccc66"><a href="JavaScript:selectClr('#cccc66')" onMouseOver="changeClr('#cccc66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cccc99"><a href="JavaScript:selectClr('#cccc99')" onMouseOver="changeClr('#cccc99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cccccc"><a href="JavaScript:selectClr('#cccccc')" onMouseOver="changeClr('#cccccc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ccccff"><a href="JavaScript:selectClr('#ccccff')" onMouseOver="changeClr('#ccccff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#cc3300"><a href="JavaScript:selectClr('#cc3300')" onMouseOver="changeClr('#cc3300'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc3333"><a href="JavaScript:selectClr('#cc3333')" onMouseOver="changeClr('#cc3333'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc3366"><a href="JavaScript:selectClr('#cc3366')" onMouseOver="changeClr('#cc3366'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc3399"><a href="JavaScript:selectClr('#cc3399')" onMouseOver="changeClr('#cc3399'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc33cc"><a href="JavaScript:selectClr('#cc33cc')" onMouseOver="changeClr('#cc33cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc33ff"><a href="JavaScript:selectClr('#cc33ff')" onMouseOver="changeClr('#cc33ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc9900"><a href="JavaScript:selectClr('#cc9900')" onMouseOver="changeClr('#cc9900'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc9933"><a href="JavaScript:selectClr('#cc9933')" onMouseOver="changeClr('#cc9933'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc9966"><a href="JavaScript:selectClr('#cc9966')" onMouseOver="changeClr('#cc9966'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc9999"><a href="JavaScript:selectClr('#cc9999')" onMouseOver="changeClr('#cc9999'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc99cc"><a href="JavaScript:selectClr('#cc99cc')" onMouseOver="changeClr('#cc99cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#cc99ff"><a href="JavaScript:selectClr('#cc99ff')" onMouseOver="changeClr('#cc99ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ccff00"><a href="JavaScript:selectClr('#ccff00')" onMouseOver="changeClr('#ccff00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ccff33"><a href="JavaScript:selectClr('#ccff33')" onMouseOver="changeClr('#ccff33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ccff66"><a href="JavaScript:selectClr('#ccff66')" onMouseOver="changeClr('#ccff66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ccff99"><a href="JavaScript:selectClr('#ccff99')" onMouseOver="changeClr('#ccff99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ccffcc"><a href="JavaScript:selectClr('#ccffcc')" onMouseOver="changeClr('#ccffcc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ccffff"><a href="JavaScript:selectClr('#ccffff')" onMouseOver="changeClr('#ccffff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#ff0000"><a href="JavaScript:selectClr('#ff0000')" onMouseOver="changeClr('#ff0000'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff0033"><a href="JavaScript:selectClr('#ff0033')" onMouseOver="changeClr('#ff0033'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff0066"><a href="JavaScript:selectClr('#ff0066')" onMouseOver="changeClr('#ff0066'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff0099"><a href="JavaScript:selectClr('#ff0099')" onMouseOver="changeClr('#ff0099'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff00cc"><a href="JavaScript:selectClr('#ff00cc')" onMouseOver="changeClr('#ff00cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff00ff"><a href="JavaScript:selectClr('#ff00ff')" onMouseOver="changeClr('#ff00ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff6600"><a href="JavaScript:selectClr('#ff6600')" onMouseOver="changeClr('#ff6600'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff6633"><a href="JavaScript:selectClr('#ff6633')" onMouseOver="changeClr('#ff6633'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff6666"><a href="JavaScript:selectClr('#ff6666')" onMouseOver="changeClr('#ff6666'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff6699"><a href="JavaScript:selectClr('#ff6699')" onMouseOver="changeClr('#ff6699'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff66cc"><a href="JavaScript:selectClr('#ff66cc')" onMouseOver="changeClr('#ff66cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff66ff"><a href="JavaScript:selectClr('#ff66ff')" onMouseOver="changeClr('#ff66ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffcc00"><a href="JavaScript:selectClr('#ffcc00')" onMouseOver="changeClr('#ffcc00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffcc33"><a href="JavaScript:selectClr('#ffcc33')" onMouseOver="changeClr('#ffcc33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffcc66"><a href="JavaScript:selectClr('#ffcc66')" onMouseOver="changeClr('#ffcc66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffcc99"><a href="JavaScript:selectClr('#ffcc99')" onMouseOver="changeClr('#ffcc99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffcccc"><a href="JavaScript:selectClr('#ffcccc')" onMouseOver="changeClr('#ffcccc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffccff"><a href="JavaScript:selectClr('#ffccff')" onMouseOver="changeClr('#ffccff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td bgcolor="#ff3300"><a href="JavaScript:selectClr('#ff3300')" onMouseOver="changeClr('#ff3300'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff3333"><a href="JavaScript:selectClr('#ff3333')" onMouseOver="changeClr('#ff3333'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff3366"><a href="JavaScript:selectClr('#ff3366')" onMouseOver="changeClr('#ff3366'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff3399"><a href="JavaScript:selectClr('#ff3399')" onMouseOver="changeClr('#ff3399'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff33cc"><a href="JavaScript:selectClr('#ff33cc')" onMouseOver="changeClr('#ff33cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff33ff"><a href="JavaScript:selectClr('#ff33ff')" onMouseOver="changeClr('#ff33ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff9900"><a href="JavaScript:selectClr('#ff9900')" onMouseOver="changeClr('#ff9900'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff9933"><a href="JavaScript:selectClr('#ff9933')" onMouseOver="changeClr('#ff9933'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff9966"><a href="JavaScript:selectClr('#ff9966')" onMouseOver="changeClr('#ff9966'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff9999"><a href="JavaScript:selectClr('#ff9999')" onMouseOver="changeClr('#ff9999'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff99cc"><a href="JavaScript:selectClr('#ff99cc')" onMouseOver="changeClr('#ff99cc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ff99ff"><a href="JavaScript:selectClr('#ff99ff')" onMouseOver="changeClr('#ff99ff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffff00"><a href="JavaScript:selectClr('#ffff00')" onMouseOver="changeClr('#ffff00'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffff33"><a href="JavaScript:selectClr('#ffff33')" onMouseOver="changeClr('#ffff33'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffff66"><a href="JavaScript:selectClr('#ffff66')" onMouseOver="changeClr('#ffff66'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffff99"><a href="JavaScript:selectClr('#ffff99')" onMouseOver="changeClr('#ffff99'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffffcc"><a href="JavaScript:selectClr('#ffffcc')" onMouseOver="changeClr('#ffffcc'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
  <td bgcolor="#ffffff"><a href="JavaScript:selectClr('#ffffff')" onMouseOver="changeClr('#ffffff'); return true"><img src="../images/blank.gif" class="colorwheelImg"></a></td>
 </tr>
 <tr>
  <td colspan="18">Click a color to select.<br /> Click again to deselect<br /> and choose another</td>
 </tr>
</table>
</div>