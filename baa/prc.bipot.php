<?php
// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Main ***
TampilkanJudul("Proses Hitung Tagihan Ulang");
$gos = (empty($_REQUEST['gos']))? 'TampilkanPRC' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanPRC() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
  CheckFormScript('TahunID,ProdiID');
  echo "<p>
  <table class=box cellspacing=1 align=center>
  <form action='?' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='AmbilData' />
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <tr><td class=wrn width=2 rowspan=3></td>
      <td class=inp>Tahun Akd:</td>
      <td class=ul><input type=text name='TahunID' 
      value='$_SESSION[TahunID]' size=5 maxlength=5 /></td>
      </tr>
  <tr>
      <td class=inp>Program Studi:</td>
      <td class=ul><select name='ProdiID'>$optprodi</select></td>
      </tr>
  <tr><td class=ul colspan=2>
      <input type=submit name='Submit' value='Proses BIPOT' />
      </td></tr>
  </form>
  </table>
  </p>";
}


function AmbilData() {
  $s = "select k.KHSID, k.MhswID, m.Nama
    from khs k
      left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    where k.KodeID = '".KodeID."'
      and k.TahunID = '$_SESSION[TahunID]'
	  and k.ProdiID = '$_SESSION[ProdiID]'
      and k.NA = 'N'
	  and m.StatusAwalID='M'
	  and m.ProgramID in ('R','J')
	  and k.Bayar = 0
    order by k.MhswID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
  	$_SESSION['PRC_BIPOT_KHSID_'.$n] = $w['KHSID'];
  	$_SESSION['PRC_BIPOT_MhswID_'.$n] = $w['MhswID'];
  	$_SESSION['PRC_BIPOT_Nama_'.$n] = $w['Nama'];
	$n++;
  }
  $_SESSION['PRC_BIPOT_TahunID'] = $_SESSION['TahunID'];
  $_SESSION['PRC_BIPOT_ProdiID'] = $_SESSION['ProdiID'];
  $_SESSION['PRC_BIPOT_JML'] = $n;
  $_SESSION['PRC_BIPOT_PRC'] = 0;
  // Tampilkan konfirmasi
  echo Konfirmasi("Konfirmasi Proses",
    "Anda akan memproses BIPOT dari prodi: <b>$_SESSION[ProdiID]</b> Tahun Akd: <b>$_SESSION[TahunID]</b>.<br />
    Jumlah yg akan diproses: <b>$_SESSION[PRC_BIPOT_JML]</b>.<br />
    Anda yakin akan memprosesnya?
    <hr size=1 color=silver />
    Opsi: <input type=button name='Proses' value='Proses Sekarang'
      onClick=\"window.location='?mnux=$_SESSION[mnux]&gos=Proses'\" />
      <input type=button name='Batal' value='Batal' 
      onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />");
}

function Proses() {
  $jml = $_SESSION['PRC_BIPOT_JML']+0;
  $prc = $_SESSION['PRC_BIPOT_PRC']+0;
  
  $TahunID = $_SESSION['PRC_BIPOT_TahunID'];
  $ProdiID = $_SESSION['PRC_BIPOT_ProdiID'];
  if ($prc < $jml) {
  	// Parameter
  	$KHSID = $_SESSION['PRC_BIPOT_KHSID_'.$prc]+0;
  	$MhswID = $_SESSION['PRC_BIPOT_MhswID_'.$prc];
  	$Nama = $_SESSION['PRC_BIPOT_Nama_'.$prc];
    
	SetBIPOTMhsw($MhswID, $TahunID,$ProdiID);
	// hapus dulu bipot yang sudah ada
	 $delete_bipot = _query('DELETE FROM bipotmhsw WHERE MhswID="'.$MhswID.'" AND Dibayar=0 AND TahunID="'.$TahunID.'" AND BIPOTNamaID not in (14,3,16,12,13,17)');
    $delete_bipot2 = _query('DELETE FROM bipotmhsw2 WHERE MhswID="'.$MhswID.'" AND flag="0" AND BayarMhswID=""  AND TahunID="'.$TahunID.'" AND BIPOTNamaID not in (14,3,16,12,13,17)');
	// Proses :::: Script ini diambil dari library.
	$adabayar = GetaField('bipotmhsw',"MhswID='$MhswID' AND Dibayar=(Jumlah*Besar) AND TahunID", $TahunID, 'Dibayar');
		ProsesBIPOTLIB($MhswID,$_SESSION['PRC_BIPOT_TahunID']);
	$khs = GetaField('khs', "KHSID", $KHSID, "Biaya");
	$khs = number_format($khs);
    // Tampilkan
    $persen = ($jml > 0)? $prc/$jml*100 : 0;
    $sisa = ($jml > 0)? 100-$persen : 0;
    $persen = number_format($persen);
    echo "<p align=center>
    <font size=+1>$persen %</font><br />
    <img src='img/B1.jpg' width=1 height=20 /><img src='img/B2.jpg' width=$persen height=20 /><img src='img/B3.jpg' width=$sisa height=20 /><img src='img/B1.jpg' width=1 height=20 /><br />
    Memproses: #$prc<br />
    <sup>$MhswID</sup><br />
    <b>$Nama</b> : Biaya: Rp $khs<br />
    </p>
    <hr size=1 color=silver />
    <p align=center>
      <input type=button name='Batal' value='Batalkan' 
      onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />
    </p>";

    // Next
    $_SESSION['PRC_BIPOT_PRC']++;
    // Reload
    $tmr = 10;
  echo <<<SCR
    <script>
    window.onload=setTimeout("window.location='?mnux=$_SESSION[mnux]&gos=Proses'", $tmr);
    </script>
SCR;
  }
  else echo Konfirmasi("Proses Selesai",
    "Proses telah selesai.<br />
    Data yang berhasil diproses: <b>$_SESSION[PRC_BIPOT_PRC]</b>.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Kembali' 
    onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />");
}
function SetBIPOTMhsw($mhswid, $thn,$ProdiID) {
$ProgramID 	= GetaField('mhsw', "MhswID", $mhswid, "ProgramID");
$TahunMhsw 	= GetaField('mhsw', "MhswID", $mhswid, "TahunID");
$NamaBipot 	= $thn.'-'.$ProgramID.'-'.substr($TahunMhsw,-2);
$bipot 		= GetaField('bipot', "Tahun = '".$NamaBipot."' AND NA='N' AND ProgramID='".$ProgramID."' AND ProdiID", $ProdiID, 'BIPOTID');
// UPDATE KHS
$s			= "UPDATE khs set BIPOTID='".$bipot."' where MhswID='".$mhswid."' AND TahunID='".$thn."'";
$r			= _query($s);
// UPDATE MHSW
$s			= "UPDATE mhsw set BIPOTID='".$bipot."' where MhswID='".$mhswid."'";
$r			= _query($s);
}



?>
