<?php

session_start();

include_once "../sisfokampus1.php";

HeaderSisfoKampus("Aktivitas Dosen");

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$DariDosen = GetSetVar('DariDosen');
$SampaiDosen = GetSetVar('SampaiDosen');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Satu' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Satu() {
  echo <<<ESD
  <font size=+1>Aktivitas Dosen</font> <sup>TRAKD</sup><br />
  <table class=box cellspacing=1 width=99%>
  <form action='../$_SESSION[mnux].dosen.php' method=POST>
  <tr>
      <td width=200 valign=top>
      Dari dosen:<br />
      <input type=text name='DariDosen' value='$_SESSION[DariDosen]' size=20 maxlength=50 />
      <font color=red>*)</font>
      </td>
      
      <td width=200 valign=top>
      Sampai dosen:<br />
      <input type=text name='SampaiDosen' value='$_SESSION[SampaiDosen]' size=20 maxlength=50 />
      </td>
      
      <td align=right valign=top>
      <input type=submit name='gos' value='Proses' />
      </td>
      </tr>
  </form>
  </table>
  <div align=right>
  <font color=red>*) Kosongkan jika akan diproses semua</font>
  </div>
ESD;
}
function Proses() {
  // Buat DBF
  include_once "../$_SESSION[mnux].header.dbf.php";
  include_once "../func/dbf.function.php";
  $NamaFile = "../tmp/TRAKD_$_SESSION[TahunID].DBF";
  $_SESSION['adsn_dbf'] = $NamaFile;
  if (file_exists($NamaFile)) unlink($NamaFile);
  DBFCreate($NamaFile, $HeaderAktivitasDosen);
  // Parameters
  $_SESSION['adsn_part'] = 0;
  $_SESSION['adsn_counter'] = 0;
  $_SESSION['adsn_total'] = HitungData();
  // Tampilkan
  $ro = "readonly=true";
  echo <<<ESD
  <font size=+1>Proses Aktivitas Dosen...</font> (<b>$_SESSION[adsn_total]</b> data)<br />
  <table class=box cellspacing=1 width=100%>
  <form name='frmDsn'>
  <tr>
      <td valign=top width=10>
      Counter:<br />
      <input type=text name='Counter' value='0' size=4 maxlength=4 $ro />
      </td>
      
      <td valign=top width=50>
      NIP:<br />
      <input type=text name='DosenID' size=10 maxlength=50 $ro />
      </td>
      
      <td valign=top>
      Nama Dosen:<br />
      <input type=text name='NamaDosen' size=30 maxlength=100 $ro />
      </td>
  </form>
  </table>
  <br />
  <script>
  function Kembali() {
    window.onLoad=setTimeout("window.location='../$_SESSION[mnux].dosen.php?gos=Selesai'", 0);
  }
  function Prosesnya(cnt, nip, nama) {
    frmDsn.Counter.value = cnt;
    frmDsn.DosenID.value = nip;
    frmDsn.NamaDosen.value = nama;
  }
  </script>
  <iframe src="../$_SESSION[mnux].dosen.php?gos=ProsesDetails" width=90% height=50 frameborder=0 scrolling=no>
  </iframe>
ESD;
}
function HitungData() {
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and j.ProdiID = '$_SESSION[ProdiID]' ";
  if (empty($_SESSION['DariDosen'])) {
    $_dosen = '';
  }
  else {
    $sampai = (empty($_SESSION['SampaiDosen']))? 'ZZZZZZZZZZZZZZZZZZZZZZZZZZZZ' : $_SESSION['SampaiDosen'];
    $_dosen = "and '$_SESSION[DariDosen]' <= j.DosenID and j.DosenID <= '$sampai' ";
  }
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? "j.TahunID='$tahun' " : "or j.TahunID='$tahun'";
	  $tahunstring = "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  $jml = GetaField("jadwal j",
    "j.NA='N' $_prodi $_dosen $tahunstring and KodeID", KodeID, "count(j.JadwalID)")+0;
  return $jml;
}
function ProsesDetails() {
  $max = $_SESSION['parsial'];
  $tot = $_SESSION['adsn_total'];
  $n = $_SESSION['adsn_part'];
  $_dari = $n * $max;
  $_sampai = (($n + 1) * $max) -1;
  
  // Ambil data
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and j.ProdiID = '$_SESSION[ProdiID]' ";
  if (empty($_SESSION['DariDosen'])) {
    $_dosen = '';
  }
  else {
    $sampai = (empty($_SESSION['SampaiDosen']))? 'ZZZZZZZZZZZZZZZZZZZZZZZZZZZZ' : $_SESSION['SampaiDosen'];
    $_dosen = "and '$_SESSION[DariDosen]' <= j.DosenID and j.DosenID <= '$sampai' ";
  }
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? "j.TahunID='$tahun' " : "or j.TahunID='$tahun'";
	  $tahunstring = "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  $s = "select j.DosenID, j.TahunID, j.NamaKelas, j.MKKode, 
        j.HariID, j.JamMulai, j.JamSelesai,
        j.ProgramID, j.ProdiID, 
        j.RencanaKehadiran, j.Kehadiran,
        p.ProdiDiktiID, p.JenjangID,
        d.NIDN, 
        LEFT(d.Nama, 50) as NamaDosen,
        LEFT(j.Nama, 50) as NamaMatakuliah,
        LEFT(p.Nama, 50) as NamaProdi
      from jadwal j
        left outer join prodi p on p.ProdiID = j.ProdiID and p.KodeID = '".KodeID."'
        left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      where j.KodeID = '".KodeID."'
        $tahunstring
		and j.NA = 'N'
        $_prodi $_dosen
      order by j.DosenID, j.HariID, j.JamMulai
      limit $_dari, $_SESSION[parsial]";
  $r = _query($s);
 
  $jml = _num_rows($r);
  
  /*
  $fn = '../tmp/log.txt';
  $f = fopen($fn, 'a');
  fwrite($f, "$s\r\n");
  fwrite($f, "$jml\r\n");
  fwrite($f, "W: $w\r\n");
  fclose($f);
  */
  
  // *** Proses DBF
  if ($jml > 0) {
    $_p = ($tot > 0)? $_SESSION['adsn_counter']/$tot*100 : 0;
    $__p = number_format($_p);
    $_s = 100 -$_p;
    $h = "height=20";
    echo "<img src='../img/B1.jpg' width=1 $h /><img src='../img/B2.jpg' width=$_p $h /><img src='../img/B3.jpg' width=$_s $h /><img src='../img/B1.jpg' width=1 $h /> <sup>&raquo; $__p%</sup>";
    while ($w = _fetch_array($r)) {
      $_SESSION['adsn_counter']++;      
      echo "<script>self.parent.Prosesnya($_SESSION[adsn_counter], '$w[DosenID]', '$w[NamaDosen]');</script>";
      
      // Masukkan data
      include_once "../$_SESSION[mnux].header.dbf.php";
      include_once "../func/dbf.function.php";
      $NamaFile = $_SESSION['adsn_dbf'];
      $dt = array(
        $_SESSION['TahunID'],
        $_SESSION['KodePTI'],
        $w['JenjangID'],
        $w['ProdiDiktiID'],
        $w['NamaProdi'],
        $w['NIDN'],
        $w['NamaMatakuliah'],
        $w['MKKode'],
        $w['NamaKelas'],
        $w['RencanaKehadiran'],
        $w['Kehadiran'], 
        ucwords(strtolower($w['NamaDosen']))
        );
      InsertDataDBF($NamaFile, $dt);
    }    
    
    $_SESSION['adsn_part']++;
    echo "<script>
    window.onLoad=setTimeout(\"window.location='../$_SESSION[mnux].dosen.php?gos=ProsesDetails'\", $_SESSION[Timer]);
    </script>
    ";
  }
  else { // *** Selesai Proses
    echo "
    <script>
    self.parent.Kembali();
    </script>";
  }
}
function Selesai() {
  echo <<<ESD
  <font size=+1>Pemrosesan Aktivitas Dosen Telah Selesai</font><br />
  <table class=box cellspacing=1 width=100%>
  <tr><td>
      Data yang berhasil diproses: <b>$_SESSION[adsn_counter]</b>.<br />
      Anda dapat mendownload data dengan menekan tombol Download di bawah ini.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Download' value='Download File'
            onClick="location='$_SESSION[adsn_dbf]'" />
            <input type=button name='Kembali' value='Kembali'
            onClick="location='../$_SESSION[mnux].dosen.php?gos='" />
      </td></tr>
  </table>
ESD;
}

?>
</BODY>
</HTML>
