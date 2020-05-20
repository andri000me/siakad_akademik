function logout(){
	window.location='tlogin';
}
	
		function createRequestObject()
		{
			var ro;
			var browser = navigator.appName;
			if(browser == 'Microsoft Internet Explorer')
			{
				ro = new ActiveXObject('Microsoft.XMLHTTP');
			}
			else
			{
				ro = new XMLHttpRequest();
			}
			return ro;
		}
		var xmlhttp = createRequestObject();
		function modalPopup(lnk,head,a,b,c,d)
		{
			if (!lnk) return;
			xmlhttp.open('get', lnk+'.php?a='+a+'&b='+b+'&c='+c+'&d='+d, true);
			xmlhttp.onreadystatechange = function()
			{
			if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
			$('#modal-isi').html(xmlhttp.responseText);
			$('#modal-header').html(head);
			return false;
			}
			xmlhttp.send(null);
			$('#Modal-Popup').modal('show');
			$('#Modal-Popup').attr('class','modal show fade in');
		}
		function ajaxSave(lnk,field,data,whr1,whr2,whr3,whr4)
		{
			if (!lnk) return;
			xmlhttp.open('get', lnk+'.php?field='+field+'&data='+data.value+'&whr1='+whr1+'&whr2='+whr2+'&whr3='+whr3+'&whr4='+whr4, true);
			xmlhttp.send(null);
			var options = $.parseJSON($("#"+whr3).attr('data-noty-options'));
			noty(options);
		}


