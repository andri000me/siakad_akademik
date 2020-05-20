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
      <input type=submit name='Submit' value='Proses KRS' />
      </td></tr>
  </form>
  </table>
  </p>";
}


function AmbilData() {
  $s = "select k.KRSID, k.MhswID, m.Nama
    from krs k
      left outer join mk m on k.MKID = m.MKID and m.KodeID = '".KodeID."'
	  left outer join mhsw mh on mh.MhswID=k.MhswID
	  left outer join krs2 kr on kr.KRSID=k.KRSID
    where k.KodeID = '".KodeID."'
      and k.TahunID = '$_SESSION[TahunID]'
      and mh.ProdiID = '$_SESSION[ProdiID]'
	  and m.PerSKS = 'N'
	  and k.GradeNilai='-'
	  and kr.GradeNilai != 'B'
    order by k.MhswID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
  	$_SESSION['PRC_BIPOT_KRSID_'.$n] = $w['KRSID'];
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
    "Anda akan memproses KRS dari prodi: <b>$_SESSION[ProdiID]</b> Tahun Akd: <b>$_SESSION[TahunID]</b>.<br />
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
  	$KRSID = $_SESSION['PRC_BIPOT_KRSID_'.$prc]+0;
  	$MhswID = $_SESSION['PRC_BIPOT_MhswID_'.$prc];
  	$Nama = $_SESSION['PRC_BIPOT_Nama_'.$prc];
    $pros = 'No';
	$krs = GetFields('krs',"KRSID", $KRSID, "BobotNilai, GradeNilai, NilaiAkhir");
	if ($krs['GradeNilai']=='-'){
		$krs2 = GetFields('krs2',"KRSID", $KRSID, "BobotNilai, GradeNilai, NilaiAkhir");
			if ($krs2['NilaiAkhir']!='70.0'){
			$update = _query("UPDATE krs set NilaiAkhir='$krs2[NilaiAkhir]',BobotNilai='$krs2[BobotNilai]',GradeNilai='$krs2[GradeNilai]'
								where KRSID='$KRSID' and MhswID='$MhswID'");
			$pros = 'Ya';
			}
	}
			
	
    // Tampilkan
    $persen = ($jml > 0)? $prc/$jml*100 : 0;
    $sisa = ($jml > 0)? 100-$persen : 0;
    $persen = number_format($persen);
    echo "<p align=center>
    <font size=+1>$persen %</font><br />
    <img src='img/B1.jpg' width=1 height=20 /><img src='img/B2.jpg' width=$persen height=20 /><img src='img/B3.jpg' width=$sisa height=20 /><img src='img/B1.jpg' width=1 height=20 /><br />
    Memproses: #$prc<br />
    <sup>$MhswID</sup><br />
    <b>$Nama : $krs[GradeNilai] ke $krs2[GradeNilai] (Proses:$pros)</b><br />
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




?>
