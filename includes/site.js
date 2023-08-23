//---------------------------------------------------------------------------------------------------------
//	DHTML JavaScript (ver 2.3)
//---------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------
//	Show/ Hide menus
//-------------------------------------------------------------------------------------------------------
function show(group,item,imgId,imgBase)
{
	if(document.getElementById)
	{
		menuCount	= eval("m_" + group);
		
		mainMenu	= document.getElementById("m" + group + "0");
		subMenu		= document.getElementById("m" + group + item);

		//	Does object exist?
		if( !subMenu )
		{
			//No, don't process
			return false;
		}
		
		if( imgId != 'NULL' )
		{
			imgObj	= document.getElementById( imgId );
			imgObj.setAttribute( 'base', imgBase );
			imgObj.setAttribute( 'src', imgBase );
		}
		
		mainMenu.style.visibility	= "visible";
		
		if ((mainMenu != subMenu) && (item != 0))
		{
			subMenu.style.visibility	= "visible";
			for (i = 1; i <= menuCount; i++)
			{
				if (i != item)
				{
					document.getElementById("m" + group + i).style.visibility	= "hidden";
				}
			}
		}
	}
}

//-------------------------------------------------------------------------------------------------------
//	Hide menu
//-------------------------------------------------------------------------------------------------------
function hide(group,item,imgId,imgBase)
{
	if(document.getElementById)
	{
		menuCount	= eval("m_" + group);

		//	Does object exist?
		if( !document.getElementById( "m" + group + menuCount ))
		{
			//	No, don't process
			return false;
		}
		
		if( imgId != 'NULL' )
		{
			imgObj	= document.getElementById( imgId );
			imgObj.setAttribute( 'base', imgBase );
			imgObj.setAttribute( 'src', imgBase );
		}
		
		if (item == 0)
		{
			startFrom	= 0;
		}
		else
		{
			startFrom	= 1;
		}
		for (i = startFrom; i <= menuCount; i++)
		{
			document.getElementById("m" + group + i).style.visibility	= "hidden";
		}
	}
}