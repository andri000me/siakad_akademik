<?php
 
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	
function HeaderSisfoKampus($title='', $use_facebox=0) {
  global $_Themes;
	include_once "../parameter.php";
	include_once "../cekparam.php";
	
  echo "<HTML xmlns=\"http://www.w3.org/1999/xhtml\">
  <HEAD><TITLE>$title</TITLE>
  <META content=\"Sistem Informasi akademik \" name=\"author\">
  <META content=\"\" name=\"description\">
  <link rel=\"stylesheet\" type=\"text/css\" href=\"../tpl/nahyan/dist/css/index.css\" />
  <link rel=\"stylesheet\" type=\"text/css\" href=\"../themes/$_Themes/drag.css\" />
  <script type=\"text/javascript\" language=\"javascript\" src=\"../include/js/drag.js\"></script>
  <link rel='stylesheet' href='../tpl/nahyan/dist/css/global.css'> 
  <script src='../putiframe.js' language='javascript' type='text/javascript'></script>";
  if ($use_facebox == 1) {
?>
  
  <script src="../fb/jquery.pack.js" type="text/javascript"></script>
  <link href="../fb/facebox.css" media="screen" rel="stylesheet" type="text/css" />
  <script src="../fb/facebox.js" type="text/javascript"></script>
  
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('a[rel*=facebox]').facebox() 
    })
	
	function getDateTime(ob){
		var curDate = document.getElementById('alt'+ob).value;
		curDate = curDate.replace('-','/');
		curDate = curDate.replace('-','/');
		var period = Date.parse(curDate);
		
		return period;
	}
  </script>

<?php
  }
  echo "</HEAD>
  <BODY>";
}
?>

