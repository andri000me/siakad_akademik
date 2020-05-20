<?php
session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Master Dosen");

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Satu' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Satu() {
  echo <<<ESD
  <font size=+1>Master Dosen</font> <sup>MSDOS</sup><br />
  <table class=box cellspacing=1 width=99%>
  <tr>
      <td>Anda akan mendownload data master dosen. 
      Sistem hanya akan memproses data dosen yang aktif saja.<br />
      Tekan tombol proses untuk memulai mengekspor data master dosen. 
      </td>
      <td width=100 valign=top align=right>
      <input type=button name='Proses' value='Proses'
        onClick="location='../$_SESSION[mnux].masterdosen.php?gos=Proses'" />
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
  $NamaFile = "../tmp/MSDOS_$_SESSION[TahunID].DBF";
  $_SESSION['mdsn_dbf'] = $NamaFile;
  $_SESSION['mdsn_part'] = 0;
  $_SESSION['mdsn_counter'] = 0;
  $_SESSION['mdsn_total'] = HitungData();
  if (file_exists($NamaFile)) unlink($NamaFile);
  DBFCreate($NamaFile, $HeaderMasterDosen);
  // tampilkan
  $ro = "readonly=true";
  echo <<<ESD
  <font size=+1>Proses Master Dosen...</font> (<b>$_SESSION[mdsn_total]</b> data)<br />
  <table class=box cellspacing=1 width=100%>
  <form name='frmDsn'>
  <tr>
      <td valign=top width=10>
      Counter:<br />
      <input type=text name='Counter' size=4 $ro />
      </td>
      
      <td valign=top width=20>
      NIP:<br />
      <input type=text name='DosenID' size=10 $ro />
      </td>
      
      <td valign=top>
      Nama Dosen:<br />
      <input type=text name='NamaDosen' size=30 $ro />
      </td>
      </tr>
  </form>
  </table>
  <br />
  
  <script>
  function Kembali() {
    window.onLoad=setTimeout("window.location='../$_SESSION[mnux].masterdosen.php?gos=Selesai'", 0);
  }
  function Prosesnya(cnt, id, nama) {
    frmDsn.Counter.value = cnt;
    frmDsn.DosenID.value = id;
    frmDsn.NamaDosen.value = nama;
  }
  </script>
  <iframe src="../$_SESSION[mnux].masterdosen.php?gos=ProsesDetails" width=90% height=50 frameborder=0 scrolling=no>
  </iframe>

ESD;
}
function HitungData() {
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and d.Homebase='$_SESSION[ProdiID]'";
  $jml = GetaField("dosen d",
    "d.NA='N' $_prodi and KodeID", KodeID, "count(d.Login)")+0;
  return $jml; 
}
function ProsesDetails() {
  $max = $_SESSION['parsial'];
  $tot = $_SESSION['mdsn_total'];
  $n = $_SESSION['mdsn_part'];
  $_dari = $n * $max;
  $_sampai = (($n + 1) * $max) -1;
  
  // Ambil data
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and d.Homebase='$_SESSION[ProdiID]' ";
  $s = "select d.NIDN, d.Nama, d.Gelar,
      d.TempatLahir, date_format(d.TanggalLahir, '%Y%m%d') as TanggalLahir,
      d.KelaminID, d.KTP, d.JenjangID,
      d.JabatanID, d.StatusKerjaID,
      d.TglBekerja, 
      d.NIPPNS, d.Homebase, p.ProdiDiktiID
    from dosen d
      left outer join prodi p on p.ProdiID = d.Homebase and p.KodeID='".KodeID."'
    where d.NA = 'N'
      and d.KodeID = '".KodeID."'
      $_prodi
    order by d.Login
    limit $_dari, $max";
  $r = _query($s);
  $jml = _num_rows($r);
  
  if ($jml > 0) {
    $_p = ($tot > 0)? $_SESSION['mdsn_counter']/$tot*100 : 0;
    $__p = number_format($_p);
    $_s = 100 -$_p;
    $h = "height=20";
    echo "<img src='../img/B1.jpg' width=1 $h /><img src='../img/B2.jpg' width=$_p $h /><img src='../img/B3.jpg' width=$_s $h /><img src='../img/B1.jpg' width=1 $h /> <sup>&raquo; $__p%</sup>";

    while ($w = _fetch_array($r)) {
      $_SESSION['mdsn_counter']++;
      echo "<script>self.parent.Prosesnya($_SESSION[mdsn_counter], '$w[Login]', '$w[Nama]');</script>";
      
      // Masukkan data
      include_once "../$_SESSION[mnux].header.dbf.php";
      include_once "../func/dbf.function.php";
      $NamaFile = $_SESSION['mdsn_dbf'];
      
      $Kelamin = ($w['KelaminID'] == 'W')? 'P' : 'L';
      $dt = array(
        $_SESSION['KodePTI'],
        $w['ProdiDiktiID'],
        $w['JenjangID'],
        $w['KTP'],
        $w['NIDN'],
        $w['Nama'],
        $w['Gelar'],
        $w['TempatLahir'],
        $w['TanggalLahir'],
        $Kelamin,
        $w['JabatanID'],
        $w['JenjangID'],
        $w['StatusKerjaID'],
        $w['NA'],
        $w['TglBekerja'],
        $w['NIPPNS'],
        $w['Homebase']
        );
      InsertDataDBF($NamaFile, $dt);
    }
    $_SESSION['mdsn_part']++;
    // reload
    echo "<script>
    window.onLoad=setTimeout(\"window.location='../$_SESSION[mnux].masterdosen.php?gos=ProsesDetails'\", $_SESSION[Timer]);
    </script>
    ";

  }
  else { // Selesai
    echo "<script>self.parent.Kembali();</script>";
  }
}
function Selesai() {
  echo <<<ESD
  <font size=+1>Proses Master Dosen Telah Selesai</font><br />
  <table class=box cellspacing=1 width=100%>
  <tr><td>
      Data dosen yang berhasil diproses: <b>$_SESSION[mdsn_counter]</b>.<br />
      Anda dapat mendownload file hasil proses dengan menekan tombol Download di bawah ini.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Download' value='Download File'
            onClick="location='$_SESSION[mdsn_dbf]'" />
            <input type=button name='Kembali' value='Kembali'
            onClick="location='../$_SESSION[mnux].masterdosen.php?gos='" />
      </td>
      </tr>
  </table>
ESD;
}
?>
</BODY>
</HTML>
