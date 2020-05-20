<?php
// Author: Emanuel Setio Dewo
// 14 April 2006
include "../sisfokampus.php";
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  include "parameter.php";
  include "cekparam.php";
  include_once "mhswkeu.lib.php";
?>
<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE><?php echo $_Institution; ?></TITLE>
  <META content="Emanuel Setio Dewo" name="author">
  <META content="Sisfo Kampus" name="description">
  <link href="index.css" rel="stylesheet" type="text/css">
  </HEAD>
<BODY>
<?php
	$KHSID = $_REQUEST['KHSID'];
	$khs = GetFields('khs', "KHSID", $KHSID, '*');
	$mhsw = GetFields('mhsw', "MhswID", $khs['MhswID'], '*');
	TampilkanJudul("Detail Biaya Mahasiswa");
  echo "<p>";
	echo TampilkanBiayaPotongan($mhsw, $khs);
	echo DaftarPembayaran($mhsw, $khs);
	TampilkanSummaryKeuMhsw($mhsw, $khs);
	echo "</p>";
?>
