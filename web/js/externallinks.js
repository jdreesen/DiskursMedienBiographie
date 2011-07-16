/**
 * \brief   Workaround for opening links in a new window because 'target'-attribute is not allowed since HTML 4.01 Strict and XHTML 1.0 Strict
 * \author	werk85 - David Zacharias <zacharias@werk85.de>
 * \author	werk85 - Jacob Dreesen <dreesen@werk85.de>
 * \date 	Created:	01.06.2006
 * \date	Last mod: 	20.02.2007
 */
 
// -------------------------------------------------------------------------------------------------------------
// functions
// -------------------------------------------------------------------------------------------------------------

function addLoadEvent(fn) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = fn;
	} else {
		window.onload = function() {
		  oldonload();
		  fn();
		}
	}
}

function newwin(url, width, height, name)
{
    var top  = 100;
    var left = 100;
    var time = new Date();
    
    if(document.all || navigator.userAgent.toLowerCase().indexOf('mozilla') != -1)
    {
        top  = Math.round(document.body.offsetHeight/2);
        left = Math.round(document.body.offsetWidth/2);
    }
    else
    {
        top  = Math.round(document.innerHeight/2);
        left = Math.round(document.innerWidth/2);
    }
		
    top  -= Math.round(height/2);
    left -= Math.round(width/2);
		
		if(name == '')
    	name = time.getTime()
		
     handle = window.open(url, name, 'width='+width+',height='+height+',top='+top+',left='+left+',resizable=yes,location=no,menubar=yes,menustatus=no,scrollbars=yes');
}

// Note:
// You have to replace 'target="_blank"' with 'rel="external"' to open links in new windows.
// If you want to use this function, you have to put it in the windows onLoad event handler.

function externalLinks() 
{
	if (!document.getElementsByTagName) return;
 
	var anchors = document.getElementsByTagName("a");
	
	for (var i=0; i<anchors.length; i++) 
	{
		if(anchors[i].getAttribute("href"))
		{
			if(anchors[i].getAttribute("rel") == "external")
			{
				anchors[i].target = "_blank";
			}
			else if(anchors[i].getAttribute("rel") == "newwin") 
			{
				anchors[i].target = "pop";
				anchors[i].onclick = new Function('defaultWin', 'newwin("", 550, 500, "pop");'); 				
			}
 		}
	}
}

addLoadEvent(externalLinks);