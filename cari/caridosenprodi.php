<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 21 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Cari Dosen");

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$Nama = GetSetVar('Nama');

echo "$ProdiID &raquo; $Nama";

// *** Main ***

?>


<p>
<a href='#' onClick="javascript:frmJadwal.DosenID.value='Test';frmJadwal.Dosen.value='Nama';jQuery.facebox.close()">Close</a>
</p>
