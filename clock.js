// JavaScript file for Dynamic Server Time in PHP
var montharray=new Array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
var hariarray=new Array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
var currentTime = '';  

function setClock(time)
{ currentTime = new Date(time);
}

function updateClock()
{ 
  currentTime.setSeconds(currentTime.getSeconds()+1);
  var currentMinutes = currentTime.getMinutes();
  var currentSeconds = currentTime.getSeconds();
  
  // Pad the minutes and seconds with leading zeros, if required
  currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
  currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

  // Update the time display
  document.getElementById("clock").firstChild.nodeValue = 
	hariarray[currentTime.getDay()] + ", " + 
	currentTime.getDate() + " " + 
	montharray[currentTime.getMonth()] + " " + 
	currentTime.getFullYear() +  "  " + 
	currentTime.getHours() + ":" + 
	currentMinutes + ":" + currentSeconds;
}