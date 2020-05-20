<script language=JavaScript>
  function opentab(idx) {
	var f = form;
	f.action = 'index.php';
	f.submit();
  }
</script>
<?php
  // Author: E. Setio Dewo, setio_dewo@telkom.net, April 2003

  include "class/tab.class.css";
  
  function DisplayTab($arrt, $aidx=0, $content='', $action='', $extra='') {
    $jml = count($arrt);
	$jml1 = $jml+1;
	$sid = session_id();
	$snm = session_name();
	if (empty($action)) $action = $_SERVER["SCRIPT_NAME"];
	settype($aidx, 'int');
    echo "<table width=100% cellspacing=1 cellpadding=4 style='border-collapse: collapse;' >";
	echo "<tr>";
	// gambarkan tab
	for ($i=0; $i < $jml; $i++) {
	  $jdl = $arrt[$i];
	  if ($i === $aidx) echo "<td class='taba'>$jdl</td>";
	  else echo "<td class='tabn' onClick='location=\"$action?tab=$i&$extra&$snm=$sid\"'>$jdl</td>";
	}
	echo "<td class='tabe' width=*>&nbsp;</td></tr>";
	echo "</table><table width=100% cellspacing=0 cellpadding=4>";
	echo "<tr><td colspan=$jml1 class='tabc'>$content</td></tr>";	
	echo "</table>";
  }
?>