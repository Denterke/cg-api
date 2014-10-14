// function XmlHttp()
// {
// 	var xmlhttp;
// 	try{xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");}
// 	catch(e)
// 	{
// 	 try {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");} 
// 	 catch (E) {xmlhttp = false;}
// 	}
// 	if (!xmlhttp && typeof XMLHttpRequest!='undefined')
// 	{
// 	 xmlhttp = new XMLHttpRequest();
// 	}
// 	  return xmlhttp;
// }
 
// function ajax(param)
// {
// 	if (window.XMLHttpRequest) req = new XmlHttp();
// 	send="";				
// 	for (var i in param.data) 
// 		send+= i+"="+param.data[i]+"&";      
// 	req.open("POST", param.url, true);              
// 	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
// 	req.send(send);
// 	req.onreadystatechange = function()
// 	{
// 	   if (req.readyState == 4 && req.status == 200) //если ответ положительный
// 	   {
// 		 if(param.success)param.success(req.responseText);
// 	   }
// 	}
// }