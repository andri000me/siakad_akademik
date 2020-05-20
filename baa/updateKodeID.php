<?php
  session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  
  $KodeID = 'UBH';
  
	$s = "show tables from $db_name";
	$q = _query($s);
	while ($w = _fetch_array($q)){
		$ss = "show columns from $w[0]";
		$qq = _query($ss);
		
		while ($ww = mysql_fetch_assoc($qq)){
			if ($ww[Field] == 'KodeID'){
				$sql = "ALTER TABLE $w[0] CHANGE `KodeID` `KodeID` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '$KodeID'";
				if ($qr = _query($sql)){
					echo "updating table $w[0]... <br>";
				}
				$sql = "Update $w[0] set KodeID = '$KodeID'";
				$qr = _query($sql);
			}
		}		
	}
?>