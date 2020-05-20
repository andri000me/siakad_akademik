<?php

// *** Parameters ***
$gels = GetFields('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "*");
$gelombang = $gels['PMBPeriodID'];

$_pmbNama = GetSetVar('_pmbNama');
$_pmbFrmID = GetSetVar('_pmbFrmID');
$_pmbPrg = GetSetVar('_pmbPrg');
$_pmbNomer = GetSetVar('_pmbNomer');
$_pmbPage = GetSetVar('_pmbPage');
$_pmbUrut = GetSetVar('_pmbUrut', 0);
$arrUrut = array('Nomer PMB~p.PMBID asc, p.Nama', 'Nomer PMB (balik)~p.PMBID desc, p.Nama', 'Nama~p.Nama', 'Nilai Ujian Tertinggi~p.NilaiUjian DESC');

// *** Main ***
TampilkanJudul("Penentuan Kelulusan PMB - $gels[Nama]");
if (empty($gelombang)) {
  echo ErrorMsg("Error",
    "Tidak ada gelombang PMB yang aktif.<br />
    Aktifkan salah satu gelombang terlebih dahulu.<br />
    Untuk mengaktifkan: <a href='?mnux=pmbsetup'>Modul PMB Setup</a>");
}
else {
  $gos = (empty($_REQUEST['gos']))? 'DftrPMB' : $_REQUEST['gos'];
  $gos($gels, $gelombang);
}

// *** Functions ***
function TampilkanHeader($gels, $gel) {
  //IsiFormulirScript($gel);
  //CetakKartuScript();
  EditNilaiScript();
  $optfrm = GetOption2('pmbformulir', 'Nama', 'Nama', $_SESSION['_pmbFrmID'],
    "KodeID='".KodeID."'", 'PMBFormulirID');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_pmbPrg'], "KodeID='".KodeID."'", 'ProgramID');
  $opturut = GetUrutanPMB();
  echo "<table class=box cellspacing=1 align=center>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <input type=hidden name='_pmbPage' value='0' />
  
  <tr>
      <td class=inp>Cari Nama:</td>
      <td class=ul1><input type=text name='_pmbNama' value='$_SESSION[_pmbNama]' size=20 maxlength=30 /></td>
      <td class=inp>Cari No. Formulir:</td>
      <td class=ul1><input type=text name='_pmbNomer' value='$_SESSION[_pmbNomer]' size=20 maxlength=30 /></td>
      <td class=inp width=100>Filter Formulir:</td>
      <td class=ul1>
        <select name='_pmbFrmID'>$optfrm</select>
      </td>
      </tr>
  <tr>
      <td class=inp>Program:</td>
      <td class=ul1><select name='_pmbPrg'>$optprg</select></td>
      <td class=inp>Urutkan:</td>
      <td class=ul1><select name='_pmbUrut'>$opturut</select></td>
      <td class=ul1 colspan=2>
        <input type=submit name='Submit' value='Submit' />
        <input type=button name='Reset' value='Reset'
          onClick=\"location='?mnux=$_SESSION[mnux]&gos=&_pmbPage=0&_pmbNama=&_pmbNomer='\" />
      </td>
      </tr>
  <tr><td bgcolor=silver colspan=7 height=2></td></tr>
  <tr>      
      <td class=ul1 colspan=6 align=center>
      <input type=button name='ExportXL' value='Export ke Excel' 
        onClick=\"window.location='$_SESSION[mnux].XL.php?gel=$gel'\" />
      <!--
	  <input type=button name='ImportXL' value='Upload Data Excel' />
      -->
	  <input type=button name='CetakPengumuman' value='Cetak Pengumuman'
        onClick=\"location='$_SESSION[mnux].pengumuman.php?PMBPeriodID=$gel'\" />
      <input type=button name='CetakSurat' value='Cetak Semua Surat Kelulusan'
        onClick=\"CetakSemuaSuratKelulusan()\" />
      </td>
  </form>
  </table>";
}

function GetUrutanPMB() {
  global $arrUrut;
  $a = ''; $i = 0;
  foreach ($arrUrut as $u) {
    $_u = explode('~', $u);
    $sel = ($i == $_SESSION['_pmbUrut'])? 'selected' : '';
    $a .= "<option value='$i' $sel>". $_u[0] ."</option>";
    $i++;
  }
  return $a;
}

function DftrPMB($gels, $gel) {
  TampilkanHeader($gels, $gel);
  
  global $_maxbaris, $arrUrut;
  include_once "class/dwolister.class.php";
  $hr = "<hr size=1 color=silver />";
  $rs = "rowspan=3";
  $rsm = "rowspan=2";
  // Urutan
  $_urut = $arrUrut[$_SESSION['_pmbUrut']];
  $__urut = explode('~', $_urut);
  $urut = "order by ".$__urut[1];
  // Filter formulir
  $whr = array();
  if (!empty($_SESSION['_pmbFrmID'])) $whr[] = "p.PMBFormulirID='$_SESSION[_pmbFrmID]'";
  if (!empty($_SESSION['_pmbPrg']))   $whr[] = "p.ProgramID = '$_SESSION[_pmbPrg]' ";
  if (!empty($_SESSION['_pmbNama']))  $whr[] = "p.Nama like '%$_SESSION[_pmbNama]%'";
  if (!empty($_SESSION['_pmbNomer'])) $whr[] = "p.PMBID like '%$_SESSION[_pmbNomer]%'";
  
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : 'and '.$_whr;
  
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_pmbPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  $lst = new dwolister;
  $lst->tables = "pmb p 
    left outer join pmbformulir f on p.PMBFormulirID = f.PMBFormulirID
    left outer join program _prg on p.ProgramID = _prg.ProgramID
    left outer join prodi _p1 on p.Pilihan1 = _p1.ProdiID
    left outer join prodi _p2 on p.Pilihan2 = _p2.ProdiID
    left outer join prodi _p3 on p.Pilihan3 = _p3.ProdiID
    left outer join statusawal _sta on p.StatusAwalID = _sta.StatusAwalID
    left outer join wawancara w on p.PMBID = w.PMBID and w.Tanggal = (select max(Tanggal) from wawancara where PMBID=p.PMBID group by PMBID)
	left outer join asalsekolah a on p.AsalSekolah = a.SekolahID
	left outer join perguruantinggi pt on p.AsalSekolah = pt.PerguruanTinggiID
	where p.KodeID = '".KodeID."' 
      and p.PMBPeriodID='$gel'
      $_whr
      $urut";
  $lst->fields = "p.PMBID, p.Nama, p.Kelamin, p.ProdiID, p.Pilihan1, p.Pilihan2, p.Pilihan3, 
    p.DetailNilai, p.NilaiUjian, p.GradeNilai, p.LulusUjian as LU, p.NA, p.NilaiSekolah, 
    p.AsalSekolah, _prg.Nama as PRG, w.WawancaraID,
    if(a.Nama like '_%', a.Nama, 
		if(pt.Nama like '_%', pt.Nama, p.AsalSekolah)) as _NamaSekolah,
	concat('&bull; ', replace(p.PrestasiTambahan, '~', '<br />&bull; ')) as PT,
    f.Nama as FRM, _p1.Nama as P1, _p2.Nama as P2, _p3.Nama as P3,
    _sta.Nama as STA,
    if (p.ProdiID = p.Pilihan1, 'wrn', concat('cna', p.LulusUjian)) as _selP1,
    if (p.ProdiID = p.Pilihan2, 'wrn', concat('cna', p.LulusUjian)) as _selP2,
    if (p.ProdiID = p.Pilihan3, 'wrn', concat('cna', p.LulusUjian)) as _selP3,
    if (p.NilaiUjian+0 = 0, 'Belum Dihitung', concat(p.NilaiUjian, ' ( ', p.GradeNilai, ' ) ')) as _NilaiUjian,
    if (p.MhswID is NULL or p.MhswID = '', '', \"<img src='img/lock.jpg' width=26 title='Sudah menjadi Mahasiswa' />\") as KUNCI,
	if (p.LulusUjian='Y', \"<img src='img/printer2.gif' />\" , '') as PRINT
    ";
  $lst->page = $_SESSION['_pmbPage']+0;
  $lst->pages = $pagefmt;
  $lst->pageactive = $pageoff;
  $lst->maxrow = $_maxbaris;
  $lst->headerfmt = "<p><table class=box cellspacing=1 align=center width=1000>
    
    <tr>
    <th class=ttl>#</th>
    <th class=ttl>PMB #</th>
    <th class=ttl>
      Nama
      $hr
      Asal Sekolah
      </th>
    <th class=ttl>
      Formulir
      $hr
      Program
      $hr
      Status
      </th>
    <th class=ttl colspan=2>
      Pilihan
      <hr size=1 color=silver />
      (Pilihan akhir ditandai<br />dengan warna biru)
      </th>
    <th class=ttl>Prestasi Tambahan</th>
    <th class=ttl>Nilai Ujian
	  <hr size=1 color=silver />
	  Nilai Sekolah
      <hr size=1 color=silver />
      Grade
      </th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10 $rs>=NOMER=</td>
    <td class=ul1 width=84 $rs align=center><b>=PMBID=<br />=KUNCI=</b></td>
    <td class=cna=LU=><b>=Nama=</b></td>
    <td class=cna=LU= width=100>=FRM=</td>
    <td class==_selP1= width=30>=Pilihan1=&nbsp
      </td>
    <td class==_selP1= width=170>=P1=&nbsp;
      </td>
    <td class=cna=LU= width=200 $rs>
      =PT=&nbsp;
      </td>
    <td class=cna=LU= width=70 align=right><b>=_NilaiUjian=</b></td>
    </tr>
    
    <tr>
    <td class=cna=LU=>&raquo; =_NamaSekolah=</td>
    <td class=cna=LU=>=PRG=&nbsp;</td>
    <td class==_selP2=>=Pilihan2=&nbsp;</td>
    <td class==_selP2=>=P2=&nbsp;</td>
    <td class=cna=LU= align=right>&nbsp;<b>=NilaiSekolah=</b></td>
    </tr>
	
    <tr>
    <td class=cna=LU= align=right><img src='img/=Kelamin=.bmp' /></td>
    <td class=cna=LU=>=STA=</td>
    <td class==_selP3=>=Pilihan3=&nbsp;</td>
    <td class==_selP3=>=P3=&nbsp;</td>
    <td class=ul1 align=right>
      Lulus: <a href='#' onClick=\"javascript:EditNilai('=PMBID=')\" /><img src='img/=LU=.gif' /></a>
			 <a href='#' onClick=\"javascript:CetakSuratKelulusan('=PMBID=', '=WawancaraID=')\" title='Cetak Surat Tanda Lulus' />=PRINT=</a> SKTL
			 <a href='#' onClick=\"javascript:CetakKTM('=PMBID=', '=WawancaraID=')\" title='Cetak KTM Sementara' />=PRINT=</a> KTM
	  </td>
    </tr>
    ".$gantibrs;
  $lst->footerfmt = "</table>";
  $hal = $lst->TampilkanHalaman();
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}

function EditNilaiScript() {
  echo <<<SCR
  <script>
  function EditNilai(PMBID) {
    lnk = "$_SESSION[mnux].nilai.php?PMBID="+PMBID;
    win2 = window.open(lnk, "", "width=500, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakSuratKelulusan(PMBID, WID) {
	lnk = "$_SESSION[mnux].suratlulus.php?PMBID="+PMBID+"&WID="+WID;
    win2 = window.open(lnk, "", "width=500, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakKTM(PMBID, WID) {
	lnk = "$_SESSION[mnux].KTM.php?PMBID="+PMBID+"&WID="+WID;
    win2 = window.open(lnk, "", "width=500, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakSemuaSuratKelulusan() {
	lnk = "$_SESSION[mnux].suratlulus.php?PMBID="+0+"&WID="+0;
    win2 = window.open(lnk, "", "width=500, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>
