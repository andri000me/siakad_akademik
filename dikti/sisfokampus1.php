<?php

function HeaderSisfoKampus($title='', $use_facebox=0) {
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

 
  echo "<HTML>
  <HEAD><TITLE>$title</TITLE>
  <META content=\"ismarianto\" name=\"author\">
  <META content=\"Sistem Informasi Manajemen Perguruan Tinggi\" name=\"description\">
  <link rel=\"stylesheet\" type=\"text/css\" href=\"../themes/default/index.css\" />
  <script type=\"text/javascript\" language=\"javascript\" src=\"../include/js/drag.js\"></script>
  <script src='../themes/".$_Themes."/js/jquery-ui-1.8.21.custom.min.js'></script>
  ";
  
  if ($use_facebox == 1) {
?>
  
  <script src="../fb/jquery.pack.js" type="text/javascript"></script>
  <link href="../fb/facebox.css" media="screen" rel="stylesheet" type="text/css" />
  <script src="../fb/facebox.js" type="text/javascript"></script>
  <!-- jQuery -->
	<script src="../themes/<?php echo $_Themes;?>/js/jquery-1.7.2.min.js"></script>
  
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

