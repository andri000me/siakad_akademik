<?php

session_start();
error_reporting(0);
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Aktivitas Mahasiswa");

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$DariMhsw = GetSetVar('DariMhsw');
$SampaiMhsw = GetSetVar('SampaiMhsw');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Satu' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Satu() {
  echo <<<ESD
  <font size=+1>Aktivitas Mahasiswa</font> <sup>TRAKM</sup><br />
  <table class=box cellspacing=1 width=99%>
  <form action='../$_SESSION[mnux].mhsw.php' method=POST>
  <tr>
      <td width=200 valign=top>
      Dari NIM:<br /> 
      <input type=text name='DariMhsw' value='$_SESSION[DariMhsw]' size=20 maxlength=50 />
      <font color=red>*)</font>
      </td>
      
      <td width=200 valign=top>
      Sampai NIM:<br />
      <input type=text name='SampaiMhsw' value='$_SESSION[SampaiMhsw]' size=20 maxlength=50 />
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
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and j.ProdiID = '$_SESSION[ProdiID]' ";
  // Buat DBF
  
  include_once "../$_SESSION[mnux].header.dbf.php";
  include_once "../func/dbf.function.php";
  $NamaFile = "../tmp/TRAKM_$_SESSION[TahunID].DBF";

  $_SESSION['amhsw_dbf'] = $NamaFile;
  $_SESSION['amhsw_part'] = 0;
  $_SESSION['amhsw_counter'] = 0;
  $_SESSION['amhsw_total'] = HitungData();
  //die($NamaFile);
  if (file_exists($NamaFile)) unlink($NamaFile);
  DBFCreate($NamaFile, $HeaderAktivitasMhsw);
  echo <<<ESD
  <font size=+1>Proses Aktivitas Mahasiswa...</font> (<b>$_SESSION[amhsw_total]</b> data)<br />
  <table class=box cellspacing=1 width=100%>
  <form name='frmMhsw' >
  <tr>
      <td valign=top width=10>
      Counter:<br />
      <input type=text name='Counter' value='0' size=3 maxlength=3 readonlye=true />
      <td valign=top width=50>
      NPM:<br />
      <input type=text name='MhswID' value='' size=10 maxlength=50 readonly=true />
      </td>
      <td valign=top>
      Nama Mahasiswa:<br />
      <input type=text name='NamaMhsw' value='' size=30 maxlength=100 readonly=true />
      </td>
      </tr>
  </form>
  </table>
  <br />
  <script>
  function Kembali() {
    window.onLoad=setTimeout("window.location='../$_SESSION[mnux].mhsw.php?gos=Selesai'", 0);
  }
  function Prosesnya(cnt, nim, nama) {
    frmMhsw.Counter.value = cnt;
    frmMhsw.MhswID.value = nim;
    frmMhsw.NamaMhsw.value = nama;
  }
  </script>
  <iframe src="../$_SESSION[mnux].mhsw.php?gos=ProsesDetails" width=90% height=50 frameborder=0 scrolling=no>
  </iframe>
ESD;
}
function HitungData() {
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? "h.TahunID='$tahun' " : "or h.TahunID='$tahun'";
	  $tahunstring = "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and h.ProdiID = '$_SESSION[ProdiID]' ";
  $jml = GetaField("khs h",
    "h.NA='N' $_prodi $tahunstring and KodeID", KodeID, "count(h.KHSID)")+0;
  return $jml;
}
function ProsesDetails() {
  $max = $_SESSION['parsial'];
  $tot = $_SESSION['amhsw_total'];
  $n = $_SESSION['amhsw_part'];
  $_dari = $n * $max;
  $_sampai = (($n + 1) * $max) -1;
  
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
	  $arrTahun = explode('~', $_SESSION['_DiktiTahunProses']);
	  foreach($arrTahun as $tahun) $tahunstring .= (empty($tahunstring))? " h.TahunID='$tahun' " : "or h.TahunID='$tahun'";
	  $tahunstring .= "and (".$tahunstring.")";  
  }
  else $tahunstring = '';
  
  // Ambil data
  $_prodi = (empty($_SESSION['ProdiID']))? '' : "and h.ProdiID = '$_SESSION[ProdiID]' ";
  $s = "select h.MhswID, h.TahunID, h.ProdiID, p.ProdiDiktiID, p.Nama as NamaProdi,
      p.JenjangID, h.IPS, h.TotalSKS, h.IP, h.SKS, h.StatusMhswID, m.Nama as NamaMhsw
    from khs h
      left outer join prodi p on p.ProdiID = h.ProdiID and p.KodeID = '".KodeID."'
      left outer join mhsw m on m.MhswID = h.MhswID and m.KodeID = '".KodeID."'
    where h.NA = 'N' and $tahunstring
      $_prodi
    order by h.MhswID
    limit $_dari, $_SESSION[parsial]";
  //echo "<pre>$s</pre>";
  $r = _query($s);
  $jml = _num_rows($r);
  
  if ($jml > 0) {
    $n = 0; $h = "height=20";
    $_p = ($tot > 0)? $_SESSION['amhsw_counter']/$tot*100 : 0;
    $__p = number_format($_p);
    $_s = 100 - $_p;

    echo "<img src='../img/B1.jpg' width=1 $h /><img src='../img/B2.jpg' width=$_p $h /><img src='../img/B3.jpg' width=$_s $h /><img src='../img/B1.jpg' width=1 $h /> <sup>&raquo; $__p%</sup>";
    while ($w = _fetch_array($r)) {
      $_SESSION['amhsw_counter']++;
      $_counter = $_SESSION['amhsw_counter'];
      echo "
      <script>self.parent.Prosesnya($_counter, '$w[MhswID]', '');</script>";
      // Masukkan data
      include_once "../$_SESSION[mnux].header.dbf.php";
      include_once "../func/dbf.function.php";
      $NamaFile = $_SESSION['amhsw_dbf'];
      $TahunID = $_SESSION['TahunID'];
      
      //$IPS = HitungIPS2($w['MhswID'],$TahunID);
		       
      //$SKS = GetaField('krs', "TahunID='$_SESSION[TahunID]' AND MhswID", $w['MhswID'], 'sum(SKS)')+0;    
      //$IPK = ($w[ProdiID]=='PGSD')? HitungIPKPGSD($MhswID):HitungIPK2($w['MhswID']);
      $TotSKS = HitungSKS($w['MhswID']);
      $dt = array(
        $_SESSION['TahunID'],
        $_SESSION['KodePTI'],
         $w['JenjangID'],
        $w['ProdiDiktiID'],
        $w['NamaProdi'],
        $w['MhswID'],
        $w['NamaMhsw'],
        $w['SKS'],
        $w['IPS'],
       $w['TotalSKS'],
        $w['IP'],
        $w['StatusMhswID']
        );
      InsertDataDBF($NamaFile, $dt);
    }
    $_SESSION['amhsw_part']++;
    // Reload
	echo <<<SCR
    <script>
    window.onLoad=setTimeout("window.location='../$_SESSION[mnux].mhsw.php?gos=ProsesDetails'", $_SESSION[Timer]);
    </script>
SCR;
  }
  else { // *** Selesai Proses
    echo <<<SCR
    <script>
    self.parent.Kembali();
    </script>
SCR;
  }
}
function Selesai() {
  $NamaFile = $_SESSION['amhsw_dbf'];
  echo <<<ESD
  <font size=+1>Pemrosesan Aktivitas Mahasiswa Semester Telah Selesai</font><br />
  <table class=box cellspacing=1 width=100%>
  <tr><td>
      Proses telah selesai. Anda dapat mendownload file hasil proses dengan menekan tombol download di bawah ini.<br />
      Data yang berhasil diproses: <b>$_SESSION[amhsw_counter]</b>
      <hr size=1 color=silver />
      Opsi: <input type=button name='Download' value='Download File'
        onClick="location='$NamaFile'" />
        <input type=button name='Kembali' value='Kembali'
        onClick="location='../$_SESSION[mnux].mhsw.php?gos='" />
  </td></tr>
  </table>
ESD;
}

?>
</BODY>
</HTML>
