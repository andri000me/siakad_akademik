<?php session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Data Mahasiswa Yang Lulus");

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Satu' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Satu() {
  echo <<<ESD
  <font size=+1>Data Mahasiswa Yang Lulus</font> <sup>TRLSM</sup><br />
  <table class=box cellspacing=1 width=99%>
  <tr><td>
      Anda akan mengexport data kelulusan mahasiswa.<br />
      Sistem akan memproses data kelulusan dari tahun akademik yang telah ditentukan di atas.<br />
      Status Mhsw yang akan diproses: <b>L</b>ulus, <b>C</b>uti, <b>N</b>on-aktif.<br />
      Tekan tombol Proses untuk memulai proses export.
      </td>
      <td width=80 align=right valign=top>
      <input type=button name='Proses' value='Proses'
        onClick="location='../$_SESSION[mnux].lulusmhsw.php?gos=Proses'" />
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
  $NamaFile = "../tmp/TRLSM_$_SESSION[TahunID].DBF";
  $_SESSION['lmhsw_dbf'] = $NamaFile;
  $_SESSION['lmhsw_part'] = 0;
  $_SESSION['lmhsw_counter'] = 0;
  $_SESSION['lmhsw_total'] = HitungData();
  if (file_exists($NamaFile)) unlink($NamaFile);
  DBFCreate($NamaFile, $HeaderKelulusanMhsw);
  // tampilkan
  $ro = "readonly=true";
  echo <<<ESD
  <font size=+1>Proses Data Kelulusan Mahasiswa...</font> (<b>$_SESSION[lmhsw_total]</b> data)<br />
  <table class=box cellspacing=1 width=100%>
  <form name='frmMhsw'>
  <tr>
      <td valign=top width=10>
      Counter:<br />
      <input type=text name='Counter' size=4 $ro />
      </td>
      
      <td valign=top width=20>
      NIP:<br />
      <input type=text name='MhswID' size=10 $ro />
      </td>
      
      <td valign=top>
      Nama Mhsw:<br />
      <input type=text name='NamaMhsw' size=30 $ro />
      </td>
      
      <td valign=top align=right width=30>
      <input type=button name='Batal' value='Batal'
        onClick="location='../$_SESSION[mnux].lulusmhsw.php?gos='" />
      </td>
      </tr>
  </form>
  </table>
  <br />
  
  <script>
  function Kembali() {
    window.onLoad=setTimeout("window.location='../$_SESSION[mnux].lulusmhsw.php?gos=Selesai'", 0);
  }
  function Prosesnya(cnt, id, nama) {
    frmMhsw.Counter.value = cnt;
    frmMhsw.MhswID.value = id;
    frmMhsw.NamaMhsw.value = nama;
  }
  </script>
  <iframe src="../$_SESSION[mnux].lulusmhsw.php?gos=ProsesDetails" width=90% height=50 frameborder=0 scrolling=no>
  </iframe>

ESD;
}
function HitungData() {
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and m.ProdiID = '$_SESSION[ProdiID]'";
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? "m.TahunGantiStatus='$tahun' " : "or m.TahunGantiStatus='$tahun'";
	  $tahunstring = "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  $jml = GetaField("mhsw m",
    "m.StatusMhswID in ('P', 'C', 'L', 'D')  $_prodi $tahunstring and KodeID",
    KodeID, "count(MhswID)")+0;
  /*$jml = GetaField("wisudawan w left outer join mhsw m on w.MhswID=m.MhswID",
    "w.TahunID='$_SESSION[TahunID]' and m.StatusMhswID='L' $_prodi and w.KodeID",
    KodeID, "count(w.MhswID)")+0;*/
  return $jml;
}
function ProsesDetails() {
  $max = $_SESSION['parsial'];
  $tot = $_SESSION['lmhsw_total'];
  $n   = $_SESSION['lmhsw_part'];
  $_dari = $n * $max;
  $_sampai = (($n + 1) * $max) -1;
  
  // Ambil data
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and m.ProdiID = '$_SESSION[ProdiID]'";
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? "w.TahunID='$tahun' " : "or w.TahunID='$tahun'";
	  $tahunstring = "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  $s = "select m.MhswID, 
      left(m.Nama, 50) as NamaMahasiswa, 
      m.StatusMhswID, m.StatusAwalID,
      IF(m.StatusMhswID='A' and m.StatusMhswID='L',p.TotalSKS,m.TotalSKS) as _TotalSKS, m.IPK, m.SKKeluar, w.NomerSeri as NoIjazah,
      p.ProdiDiktiID, p.JenjangID,
      w.Judul, w.TglSidang,
      date_format(w.TglSelesai, '%Y-%m-%d') as _TglSelesai,
      date_format(w.TglMulai, '%Y-%m-%d') as _TglMulai,
      date_format(w.TglSidang, '%Y-%m-%d') as _TglSidang,
      date_format(m.TanggalLulus, '%Y%m%d') as _TanggalLulus,
      date_format(m.TglSKKeluar, '%Y%m%d') as _TglSKKeluar
    from mhsw m
      left outer join prodi p on p.ProdiID=m.ProdiID and p.KodeID='".KodeID."'
      left outer join wisudawan w on w.MhswID=m.MhswID
    where m.KodeID = '".KodeID."'
      and m.StatusMhswID in ('C', 'L', 'P', 'D','A')
      $_prodi
	  $tahunstring
    order by m.MhswID
    limit $_dari, $max";
	
  /*$s = "select m.MhswID, left(m.Nama, 50) as NamaMahasiswa, 
      m.StatusMhswID, m.StatusAwalID,
      m.TotalSKS, m.IPK, m.SKKeluar, m.NoIjazah,
      p.ProdiDiktiID, p.JenjangID,
      date_format(m.TanggalLulus, '%Y%m%d') as _TanggalLulus,
      date_format(m.TglSKKeluar, '%Y%m%d') as _TglSKKeluar
	from wisudawan w 
	  left outer join mhsw m on w.MhswID=m.MhswID and m.KodeID='".KodeID."'
	  left outer join prodi p on p.ProdiID=m.ProdiID and p.KodeID='".KodeID."'
	where w.KodeID='".KodeID."'
		and w.TahunID='$_SESSION[TahunID]'
		and m.StatusMhswID='L'
		$_prodi
	order by m.MhswID
	limit $_dari, $max";*/
  $r = _query($s);
  $jml = _num_rows($r);
  
  if ($jml > 0) {
    $n = 0; $h = "height=20";
    $_p = ($tot > 0)? $_SESSION['lmhsw_counter']/$tot*100 : 0;
    $__p = number_format($_p);
    $_s = 100 - $_p;

    echo "<img src='../img/B1.jpg' width=1 $h /><img src='../img/B2.jpg' width=$_p $h /><img src='../img/B3.jpg' width=$_s $h /><img src='../img/B1.jpg' width=1 $h /> <sup>&raquo; $__p%</sup>";
    while ($w = _fetch_array($r)) {
      $_SESSION['lmhsw_counter']++;
      $_counter = $_SESSION['lmhsw_counter'];
      echo "<script>self.parent.Prosesnya($_counter, '$w[MhswID]', '$w[Nama]', '$w[NamaMK]');</script>";
      // Masukkan ke DBF
      include_once "../$_SESSION[mnux].header.dbf.php";
      include_once "../func/dbf.function.php";
      $NamaFile = $_SESSION['lmhsw_dbf'];
      $StatusMhsw = ($w['StatusMhswID'] == 'P')? 'N' : $w['StatusMhswID'];
      $TanggalLulus = GetaField('wisudawan', "MhswID", $w[MhswID], "date_format(TglSidang, '%Y-%m-%d')");
      $dt = array(
        $_SESSION['TahunID'],
        $_SESSION['KodePTI'],
        $w['JenjangID'],
        $w['ProdiDiktiID'],
        $w['MhswID'],
        $w['NamaMahasiswa'],
        $StatusMhsw,
        $TanggalLulus,
        $w['_TotalSKS'],
        $w['IPK'],
        $w['SKKeluar'],
        $w['_TglSKKeluar'],
        str_replace("T", "I", $w['NoIjazah']),
        $StatusMhsw,
        $w['Judul'],
        $w['_TglSidang'],
        $w['_TglMulai'],
        $w['_TglSelesai'],
        '',
        '',
        '',
        ''
      );
      InsertDataDBF($NamaFile, $dt);
    }
    $_SESSION['lmhsw_part']++;
    // Reload
    echo <<<SCR
    <script>
    window.onLoad=setTimeout("window.location='../$_SESSION[mnux].lulusmhsw.php?gos=ProsesDetails'", $_SESSION[Timer]);
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
  $NamaFile = $_SESSION['lmhsw_dbf'];
  echo <<<ESD
  <font size=+1>Pemrosesan Data Kelulusan Mahasiswa Telah Selesai</font><br />
  <table class=box cellspacing=1 width=100%>
  <tr><td>
      Proses telah selesai. Anda dapat mendownload file hasil proses dengan menekan tombol download di bawah ini.<br />
      Data yang berhasil diproses: <b>$_SESSION[lmhsw_counter]</b>
      <hr size=1 color=silver />
      Opsi: <input type=button name='Download' value='Download File'
        onClick="location='$NamaFile'" />
        <input type=button name='Kembali' value='Kembali'
        onClick="location='../$_SESSION[mnux].lulusmhsw.php?gos='" />
  </td></tr>
  </table>
ESD;
}
?>
