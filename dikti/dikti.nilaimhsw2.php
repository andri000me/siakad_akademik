<?php
session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Nilai Mahasiswa");

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Satu' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Satu() {
  echo <<<ESD
  <font size=+1>Nilai Mahasiswa</font> <sup>TRNLM</sup><br />
  <table class=box cellspacing=1 width=99%>
  <tr><td valign=top>
      Anda akan memproses data nilai mahasiswa.<br />
      Sistem hanya akan memproses nilai mahasiswa yang telah difinalisasi.
      Tekan tombol proses untuk memulai memproses nilai mahasiswa.
      </td>
      
      <td valign=top align=right>
      <input type=button name='Proses' value='Proses'
        onClick="location='../$_SESSION[mnux].nilaimhsw.php?gos=Proses'" />
      </td>
      </tr>
  </table>
ESD;
}
function Proses() {
  // Buat DBF
  include_once "../$_SESSION[mnux].header.dbf.php";
  include_once "../func/dbf.function.php";
  $NamaFile = "../tmp/TRNLM_$_SESSION[TahunID].DBF";
  $_SESSION['nmhsw_dbf'] = $NamaFile;
  $_SESSION['nmhsw_part'] = 0;
  $_SESSION['nmhsw_counter'] = 0;
  $_SESSION['nmhsw_total'] = HitungData();
  if (file_exists($NamaFile)) unlink($NamaFile);
  DBFCreate($NamaFile, $HeaderNilaiMhsw);
  $ro = "readonly=true";
  echo <<<ESD
  <font size=+1>Proses Nilai Mahasiswa...</font> (<b>$_SESSION[nmhsw_total]</b> data)<br />
  <table class=box cellspacing=1 width=100%>
  <form name='frmNilai'>
  <tr><td width=10>
      Counter:<br />
      <input type=text name='Counter' size=4 $ro />
      </td>
      
      <td width=20>
      NIM:<br />
      <input type=text name='MhswID' size=10 $ro />
      </td>
      
      <td width=20>
      Mahasiswa:<br />
      <input type=text name='NamaMhsw' size=30 $ro />
      </td>
      
      <td>
      Matakuliah:<br />
      <input type=text name='NamaMK' size=30 $ro />
      </td>
      </tr>
  </form>
  </table>
  <br />
  
  <script>
  function Kembali() {
    window.onLoad=setTimeout("window.location='../$_SESSION[mnux].nilaimhsw.php?gos=Selesai'", 0);
  }
  function Prosesnya(cnt, nim, nama, mk) {
    frmNilai.Counter.value = cnt;
    frmNilai.MhswID.value = nim;
    frmNilai.NamaMhsw.value = nama;
    frmNilai.NamaMK.value = mk;
  }
  </script>
  <iframe src="../$_SESSION[mnux].nilaimhsw.php?gos=ProsesDetails" width=90% height=50 frameborder=0 scrolling=no>
  </iframe>
ESD;
}
function HitungData() {
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and h.ProdiID = '$_SESSION[ProdiID]' ";
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? "k.TahunID='$tahun' " : "or k.TahunID='$tahun'";
	  $tahunstring = "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  $jml = GetaField("krs k
    left outer join khs h on h.KHSID = k.KHSID",
    "h.KodeID = '".KodeID."' $tahunstring $_prodi and k.NA", 'N', "count(KRSID)")+0;
  return $jml;
}
function ProsesDetails() {
  $max = $_SESSION['parsial'];
  $tot = $_SESSION['nmhsw_total'];
  $n = $_SESSION['nmhsw_part'];
  $_dari = $n * $max;
  $_sampai = (($n + 1) * $max) -1;
  
  // Ambil data
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and h.ProdiID = '$_SESSION[ProdiID]' ";
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? "k.TahunID='$tahun' " : "or k.TahunID='$tahun'";
	  $tahunstring = "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  $s = "select k.TahunID, k.MhswID, right(k.MhswID,3) as _Blkg, k.MKKode,
      left(k.Nama, 50) as NamaMK, 
      k.GradeNilai, k.BobotNilai,
      p.ProdiDiktiID, p.JenjangID, LEFT(m.Nama, 50) as NamaMhsw,
      j.DosenID, LEFT(d.Nama, 50) as DSN
    from krs k
      left outer join khs h on h.KHSID = k.KHSID and h.KodeID = '".KodeID."'
      left outer join prodi p on p.ProdiID = h.ProdiID and p.KodeID = '".KodeID."'
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
      left outer join jadwal j on k.JadwalID = j.JadwalID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    where k.NA = 'N'
      $tahunstring
      $_prodi
    order by k.MhswID
    limit $_dari, $max";
  $r = _query($s);
  $jml = _num_rows($r);
  
  if ($jml > 0) {
    $n = 0; $h = "height=20";
    $_p = ($tot > 0)? $_SESSION['nmhsw_counter']/$tot*100 : 0;
    $__p = number_format($_p);
    $_s = 100 - $_p;

    echo "<img src='../img/B1.jpg' width=1 $h /><img src='../img/B2.jpg' width=$_p $h /><img src='../img/B3.jpg' width=$_s $h /><img src='../img/B1.jpg' width=1 $h /> <sup>&raquo; $__p%</sup>";
    while ($w = _fetch_array($r)) {
    	
      $_SESSION['nmhsw_counter']++;
      $_counter = $_SESSION['nmhsw_counter'];
      $Kelas = ceil($w['_Blkg']/40);
      $Kelas = ($Kelas < 10)? "0".$Kelas : $Kelas;
      echo "<script>self.parent.Prosesnya($_counter, '$w[MhswID]', '$w[NamaMhsw]', '$w[NamaMK]');</script>";
      // Masukkan ke DBF
      // Masukkan data
      include_once "../$_SESSION[mnux].header.dbf.php";
      include_once "../func/dbf.function.php";
      $NamaFile = $_SESSION['nmhsw_dbf'];
      $dt = array(
        $w['TahunID'],
        $_SESSION['KodePTI'],
        $w['JenjangID'],
        $w['ProdiDiktiID'],
        $w['MhswID'],
        $w['MKKode'],
        $w['GradeNilai'],
        $w['BobotNilai'],
		$Kelas
        );
      InsertDataDBF($NamaFile, $dt);
    }
    $_SESSION['nmhsw_part']++;
    // Reload
    echo <<<SCR
    <script>
    window.onLoad=setTimeout("window.location='../$_SESSION[mnux].nilaimhsw.php?gos=ProsesDetails'", $_SESSION[Timer]);
    </script>
SCR;
  }
  else { // *** Selesai proses
    echo <<<SCR
    <script>
    self.parent.Kembali();
    </script>
SCR;
  }
}
function Selesai() {
  $NamaFile = $_SESSION['nmhsw_dbf'];
  echo <<<ESD
  <font size=+1>Pemrosesan Nilai Mahasiswa Telah Selesai</font><br />
  <table class=box cellspacing=1 width=100%>
  <tr><td>
      Proses telah selesai. 
      Anda dapat mendownload file hasil proses dengan menekan tombol Download di bawah ini.
      Data yang berhasil diproses: <b>$_SESSION[nmhsw_counter]</b>.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Download' value='Download File'
        onClick="location='$NamaFile'" />
        <input type=button name='Kembali' value='Kembali'
        onClick="location='../$_SESSION[mnux].nilaimhsw.php?gos='" />
      </td></tr>
  </table>
ESD;
}
?>

</BODY>
</HTML>
