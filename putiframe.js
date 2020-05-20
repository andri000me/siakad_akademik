// JavaScript Document
function putIF(hldr,x,y,w,h,url) 
// description of the arguments below
{
   holder=document.getElementById(hldr); //wrap around div
   holder.style.display='block';  // the div gets styled
   holder.style.left=x;
   holder.style.top=y;
   holder.style.width=w;
   IF=document.createElement('iframe');  // iframe is created in the DOM
   IF.setAttribute('height', h);  // simple DOM methods
   IF.setAttribute('width', w);
   IF.setAttribute('src', url);
   IF.setAttribute('frameborder', 0);
   holder.appendChild(IF); // now we add it to the div
   holder.innerHTML+='<button onclick="closeIF(this.parentNode)">Close</button>';
     // final step is to put in additional controls
}
   function closeIF(obj)
     // the close is simple we just clear out the div
   {
      obj.innerHTML='';
      obj.style.display='none';
   }
function putIFrame(hldr, url) 
// description of the arguments below
{
   holder = document.getElementById(hldr); //wrap around div
   holder.style.display = 'block';  // the div gets styled
   IF=document.createElement('iframe');  // iframe is created in the DOM
   IF.setAttribute('src', url);
   IF.setAttribute('frameborder', 1);
   holder.appendChild(IF); // now we add it to the div
     // final step is to put in additional controls
}



/***********************************************
* IFrame SSI script II- © Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
* Visit DynamicDrive.com for hundreds of original DHTML scripts
* This notice must stay intact for legal use
***********************************************/

//Input the IDs of the IFRAMES you wish to dynamically resize to match its content height:
//Separate each ID with a comma. Examples: ["myframe1", "myframe2"] or ["myframe"] or [] for none:

//var iframeids=["FRAMEMSG","FRAMEDETAIL","FRAMEDETAIL1","FRM_0","FRM_1"]

//Should script hide iframe from browsers that don't support this script (non IE5+/NS6+ browsers. Recommended):
var iframehide="yes"

var getFFVersion = navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1]
var FFextraHeight = parseFloat(getFFVersion) >= 0.1? 5 : 7; //extra height in px to add to iframe in FireFox 1.0+ browsers
var FFextraWidth = parseFloat(getFFVersion) >= 0.1? 5 : 7;

function resizeCaller() {
  var dyniframe=new Array()
  for (i=0; i<iframeids.length; i++){
    if (document.getElementById) resizeIframe(iframeids[i])
    //reveal iframe for lower end browsers? (see var above):
    if ((document.all || document.getElementById) && iframehide=="no"){
      var tempobj=document.all? document.all[iframeids[i]] : document.getElementById(iframeids[i])
      tempobj.style.display="block"
    }
  }
}

function resizeIframe(frameid){
  var currentfr=document.getElementById(frameid)
  if (currentfr && !window.opera){
    currentfr.style.display="block"
    // height
    if (currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight) //ns6 syntax
      currentfr.height = currentfr.contentDocument.body.offsetHeight+FFextraHeight    
    else if (currentfr.Document && currentfr.Document.body.scrollHeight) //ie5+ syntax
      currentfr.height = currentfr.Document.body.scrollHeight;
      
    // width
    if (currentfr.contentDocument && currentfr.contentDocument.body.offsetWidth)
      currentfr.width =  currentfr.contentDocument.body.scrollWidth
    else if (currentfr.Document && currentfr.Document.body.scrollWidth)
      currentfr.width = currentfr.Document.body.scrollWidth;
    //alert(currentfr.contentDocument.body.scrollWidth.toString());
    // listener
    if (currentfr.addEventListener)
      currentfr.addEventListener("load", readjustIframe, false)
    else if (currentfr.attachEvent){
      currentfr.detachEvent("onload", readjustIframe) // Bug fix line
      currentfr.attachEvent("onload", readjustIframe)
    }
  }
}

function readjustIframe(loadevt) {
  var crossevt=(window.event)? event : loadevt
  var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement
  if (iframeroot) resizeIframe(iframeroot.id);
}

function loadintoIframe(iframeid, url){
  if (document.getElementById)
  document.getElementById(iframeid).src=url
}

if (window.addEventListener) window.addEventListener("load", resizeCaller, false)
else if (window.attachEvent) window.attachEvent("onload", resizeCaller)
else window.onload=resizeCaller
