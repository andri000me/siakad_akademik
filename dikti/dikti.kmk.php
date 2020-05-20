<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Kurikulum > Matakuliah");

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Satu' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Satu() {
  echo <<<ESD
  <font size=+1>Master Kurikulum &raquo; Matakuliah</font> <sup>TBKMK</sup><br />
  <table class=box cellspacing=1 width=99%>
  <tr>
      <td>Anda akan mendownload data Kurikulum &raquo; Matakuliah.<br />
      Sistem akan memproses hanya data matakuliah yang aktif saja.<br />
      Tekan tombol proses untuk memulai mengekspor data matakuliah. 
      </td>
      <td width=100 valign=top align=right>
      <input type=button name='Proses' value='Proses'
        onClick="location='../$_SESSION[mnux].kmk.php?gos=Proses'" />
      </td>
      </tr>
  </table>
  <br />
ESD;
}
function Proses() {
  // Buat DBF
  include_once "../$_SESSION[mnux].header.dbf.php";
  include_once "../func/dbf.function.php";
  $NamaFile = "../tmp/TBKMK_$_SESSION[TahunID].DBF";
  $_SESSION['kmk_dbf'] = $NamaFile;
  $_SESSION['kmk_part'] = 0;
  $_SESSION['kmk_counter'] = 0;
  $_SESSION['kmk_total'] = HitungData();
  if (file_exists($NamaFile)) unlink($NamaFile);
  DBFCreate($NamaFile, $HeaderMatakuliah);
  // tampilkan
  $ro = "readonly=true";
  echo <<<ESD
  <font size=+1>Proses Master Kurikulum &raquo; Matakuliah...</font> (<b>$_SESSION[kmk_total]</b> data)<br />
  <table class=box cellspacing=1 width=100%>
  <form name='frmDsn'>
  <tr>
      <td valign=top width=10>
      Counter:<br />
      <input type=text name='Counter' size=4 $ro />
      </td>
      
      <td valign=top width=20>
      Kode MK:<br />
      <input type=text name='MKKode' size=10 $ro />
      </td>
      
      <td valign=top>
      Matakuliah:<br />
      <input type=text name='Matakuliah' size=30 $ro />
      </td>
      
      <td align=right valign=top>
      <input type=button name='btnBatal' value='Batal'
        onClick="location='../$_SESSION[mnux].kmk.php?gos='" />
      </td>
      </tr>
  </form>
  </table>
  <br />
  
  <script>
  function Kembali() {
    window.onLoad=setTimeout("window.location='../$_SESSION[mnux].kmk.php?gos=Selesai'", 0);
  }
  function Prosesnya(cnt, id, nama) {
    frmDsn.Counter.value = cnt;
    frmDsn.MKKode.value = id;
    frmDsn.Matakuliah.value = nama;
  }
  </script>
  <iframe src="../$_SESSION[mnux].kmk.php?gos=ProsesDetails" width=90% height=50 frameborder=0 scrolling=no>
  </iframe>

ESD;
}
function HitungData() {
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and ProdiID='$_SESSION[ProdiID]'";
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? "TahunID='$tahun' " : "or TahunID='$tahun'";
	  $tahunstring = "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  $s = "select count(JadwalID) as JML 
    from jadwal
    where KodeID = '".KodeID."'
      $tahunstring
      $_prodi
    group by ProdiID, MKKode";
  $r = _query($s);
  
  $jml = _num_rows($r);
  return $jml; 
}
function ProsesDetails() {
  $max = $_SESSION['parsial'];
  $tot = $_SESSION['kmk_total'];
  $n = $_SESSION['kmk_part'];
  $_dari = $n * $max;
  $_sampai = (($n + 1) * $max) -1;
  
  // Ambil data // *** Sengaja diambil dari tabel Jadwal atas request STKIP PGRI
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and j.ProdiID = '$_SESSION[ProdiID]' ";
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? "j.TahunID='$tahun' " : "or j.TahunID='$tahun'";
	  $tahunstring = "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  $s = "select j.*,
    p.ProdiDiktiID, p.JenjangID,
    d.Nama as NamaDosen, d.NIDN,
    mk.SKS as MKSKS, mk.Sesi, mk.Wajib,
    mk.SKSTatapMuka, mk.SKSPraktikum, mk.SKSPraktekLap,
    mk.JenisMKID, mk.JenisPilihanID, mk.JenisKurikulumID
    from jadwal j
      left outer join prodi p on p.ProdiID = j.ProdiID and p.KodeID = '".KodeID."'
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join mk mk on mk.MKID = j.MKID
    where j.KodeID = '".KodeID."'
      $tahunstring
      $_prodi
    group by j.ProdiID, j.MKKode
    limit $_dari, $max";
  $r = _query($s);
  $jml = _num_rows($r);
  
  if ($jml > 0) {
    $_p = ($tot > 0)? $_SESSION['kmk_counter']/$tot*100 : 0;
    $__p = number_format($_p);
    $_s = 100 -$_p;
    $h = "height=20";
    echo "<img src='../img/B1.jpg' width=1 $h /><img src='../img/B2.jpg' width=$_p $h /><img src='../img/B3.jpg' width=$_s $h /><img src='../img/B1.jpg' width=1 $h /> <sup>&raquo; $__p%</sup>";

    while ($w = _fetch_array($r)) {
      $_SESSION['kmk_counter']++;
      echo "<script>self.parent.Prosesnya($_SESSION[kmk_counter], '$w[MKKode]', '$w[Nama]');</script>";
      
      // Masukkan data
      include_once "../$_SESSION[mnux].header.dbf.php";
      include_once "../func/dbf.function.php";
      $NamaFile = $_SESSION['kmk_dbf'];
      
      $Kelamin = ($w['KelaminID'] == 'W')? 'P' : 'L';
      $dt = array(
        $w['TahunID'],
        $_SESSION['KodePTI'],
        $w['JenjangID'],
        $w['ProdiDiktiID'],
        $w['MKKode'],
        $w['Nama'],
        $w['SKS'],
        $w['SKSTatapMuka'],
        $w['SKSPraktikum'],
        $w['SKSPraktekLap'],
        $w['Sesi'],
        $w['JenisMKID'],
        'Y',  // kurikulum
        'Y',  // ???
        $w['NIDN'],
        'A',
        'Y',
        'Y',
        'Y',
        ''
      );
      InsertDataDBF($NamaFile, $dt);
    }
    $_SESSION['kmk_part']++;
    // reload
    echo "<script>
    window.onLoad=setTimeout(\"window.location='../$_SESSION[mnux].kmk.php?gos=ProsesDetails'\", $_SESSION[Timer]);
    </script>
    ";

  }
  else { // Selesai
    echo "<script>self.parent.Kembali();</script>";
  }
}
function Selesai() {
  echo <<<ESD
  <font size=+1>Proses Tabel Kurikulum &raquo; Matakuliah Telah Selesai</font><br />
  <table class=box cellspacing=1 width=100%>
  <tr><td>
      Data Kurikulum &raquo; Matakuliah yang berhasil diproses: <b>$_SESSION[kmk_counter]</b>.<br />
      Anda dapat mendownload file hasil proses dengan menekan tombol Download di bawah ini.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Download' value='Download File'
            onClick="location='$_SESSION[kmk_dbf]'" />
            <input type=button name='Kembali' value='Kembali'
            onClick="location='../$_SESSION[mnux].kmk.php?gos='" />
      </td>
      </tr>
  </table>
ESD;
}
?>
</BODY>
</HTML>
