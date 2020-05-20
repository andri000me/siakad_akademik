<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE><?php echo 'Pendaftaran Online'; ?></TITLE>
  <META content="Sugeng S" name="author">
  <META content="Sisfo Kampus" name="description">
  <link href="index.css" rel="stylesheet" type="text/css">
</HEAD><BODY>
<?php
// Created By Sugeng
// September 2006

include "dwo.lib.php";
include "db.mysql.php";
include "connectdb.php";
include "parameter.php";
include_once "pmbform.edt.php";

$pmbaktif = GetaField('pmbperiod','NA','N','PMBPeriodID');
//$pmbaktif = GetSetVar('pmbaktif');
$_SESSION['pmbaktif'] = $pmbaktif;

function GetNextPMBID2($prodi='') {
  $_PMBDigit = 3;
  $pmbaktif = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
  $pmbmx = GetaField('pmbweb', "PMBID like '$pmbaktif%' and NA", 'N', "max(PMBID)");
  //$pmbcnt = ltrim($pmbmx, $pmbaktif);
  $pmbcnt = str_replace($pmbaktif, '', $pmbmx)+1;
  $pmbcnt = $pmbaktif."- w".str_pad($pmbcnt, $_PMBDigit, '0', STR_PAD_LEFT);
  //echo $pmbcnt;
  return $pmbcnt;
  //echo $pmbmx.' : '.$pmbaktif.' : '.$pmbcnt;
}

function PilihanForm(){
$opt = GetOption2("pmbformulir", "concat(Nama, ' (', JumlahPilihan, ' pilihan) : Rp. ', format(Harga, 0))",
    'PMBFormulirID', $_SESSION['pmbfid'], "KodeID='UKRIDA'", 'PMBFormulirID');
$optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prgid'], '', 'ProgramID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='pmbform'>
  <input type=hidden name='gos' value='PMBEdt'>
  <input type=hidden name='md' value=1>
  <tr><td class=ul colspan=2><strong>UKRIDA</strong></td></tr>
  <tr><td class=inp1>Periode Aktif</td><td class=ul>: <b>$_SESSION[pmbaktif]</td></tr>
  <tr><td class=inp1>Jenis formulir</td><td class=ul>: <select name='pmbfid'>$opt</select></td></tr>
  <tr><td class=inp1>Program</td><td class=ul>: <select name='prgid'>$optprg</select></td></tr>
  <tr><td class=ul colspan=2><input type=submit value=Kirim name=kirim></td></tr>
  </form></table></p>";
}


/*
function TampilkanOpsiForm() {
  global $arrID, $_PMBAdminJalurKhusus;
  // Daftar status awal yg boleh diakses:
  $JalurKhusus = (strpos($_PMBAdminJalurKhusus, ".$_SESSION[_LevelID].") === false)? 'N' : 'Y';
  $s = "select StatusAwalID, Nama from statusawal where JalurKhusus='$JalurKhusus' order by StatusAwalID";
  $r = _query($s);
  $arrJK = array();
  while ($w = _fetch_array($r)) {
    $arrJK[] = '<b>'.$w['Nama'].'</b>';
  }
  $strJK = implode(', ', $arrJK);
  $strJK = "Anda hanya dapat mengakses calon dengan status: $strJK.";
  
  // opsi2
  $opt = GetOption2("pmbformulir", "concat(Nama, ' (', JumlahPilihan, ' pilihan) : Rp. ', format(Harga, 0))",
    'PMBFormulirID', $_SESSION['pmbfid'], "KodeID='$_SESSION[KodeID]'", 'PMBFormulirID');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prgid'], '', 'ProgramID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='pmbform'>
  <tr><td class=ul colspan=2><strong>$arrID[Nama]</strong></td></tr>
  <tr><td class=ul>Periode Aktif</td><td class=ul>: <b>$_SESSION[pmbaktif]</td></tr>
  <tr><td class=ul>Jenis formulir</td><td class=ul>: <select name='pmbfid' onChange='this.form.submit()'>$opt</select></td></tr>
  <tr><td class=ul>Program</td><td class=ul>: <select name='prgid' onChange='this.form.submit()'>$optprg</select></td></tr>
  <tr><td class=ul>Cari calon</td><td class=ul>: <input type=text name='pmbcari' value='$_SESSION[pmbcari]' size=20 maxlength=20>
    <input type=submit name='Cari' value='PMBID'>
    <input type=submit name='Cari' value='Nama'>
    <input type=submit name='Cari' value='All'>
  </td></tr>
  <tr><td class=ul colspan=2><a href='?mnux=pmbform&gos=PMBEdt0&md=1'>Input Formulir</a> |
  $strJK
  </td></tr>
  </form></table></p>";
}
*/
function DftrPMB() {
  if (!empty($_SESSION['pmbfid'])) DftrPMB1();
}
function DftrPMB1() {
  global $_defmaxrow, $_FKartuUSM, $_PMBAdminJalurKhusus;
  include_once "class/lister.class.php";
  if ($_SESSION['Cari'] != 'All') {
    $_cari2 = (!empty($_SESSION['pmbcari']))? " and p.$_SESSION[Cari] like '%$_SESSION[pmbcari]%' " : '';
  } 
  else $_cari2 = '';

  $whr = '';
  $whr .= (empty($_SESSION['prgid']))? '' : "and p.ProgramID='$_SESSION[prgid]'";
  $JalurKhusus = (strpos($_PMBAdminJalurKhusus, ".$_SESSION[_LevelID].") === false)? 'N' : 'Y';
  //echo 'Jalur Khusus: '.$JalurKhusus;

  $pagefmt = "<a href='?mnux=pmbform&gos=DftrPMB&SR==STARTROW='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $lister = new lister;
  $lister->tables = "pmb p
    left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
    left outer join program prg on p.ProgramID=prg.ProgramID
    left outer join prodi p1 on p.Pilihan1=p1.ProdiID
    left outer join prodi p2 on p.Pilihan2=p2.ProdiID
    left outer join prodi p3 on p.Pilihan3=p3.ProdiID
    where PMBFormulirID='$_SESSION[pmbfid]' and sa.JalurKhusus='$JalurKhusus'
      and PMBID like '$_SESSION[pmbaktif]%' $_cari2 $whr
    order by p.PMBID desc";
	//echo $lister->tables;
    $lister->fields = "p.PMBID, p.PMBRef, p.Nama, p.NA, p.JenisSekolahID, prg.Nama as PRG,
      p.SyaratLengkap, p.Syarat,
      p1.Nama as Pil1, p2.Nama as Pil2, p3.Nama as Pil3, format(Harga, 2) as HRG ";
    $lister->startrow = $_REQUEST['SR']+0;
    $lister->maxrow = $_defmaxrow;
    $lister->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
      <tr>
	  <th class=ttl>No.</th><th class=ttl>No. PMB</th>
	  <th class=ttl>No. Ujian</th>
	  <th class=ttl>Nama</th>
	  <th class=ttl>Program</th>
	  <th class=ttl>Pilihan 1</th>
	  <th class=ttl>Pilihan 2</th>
	  <th class=ttl>Pilihan 3</th>
      <th class=ttl>Asal</th>
	  <th class=ttl colspan=2>Harga</th>
      <th class=ttl>Syarat</th>
	  <th class=ttl>Kartu</th>
      </tr>";
    $lister->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cna=NA=><a href=\"?mnux=pmbform&gos=PMBEdt0&md=0&pmbid==PMBID=\"><img src='img/edit.png' border=0>
      =PMBID=</a></td>
      <td class=cna=NA=>=PMBRef=&nbsp;</td>
	  <td class=cna=NA=>=Nama=</a></td>
	  <td class=cna=NA=>=PRG=</td>
	  <td class=cna=NA=>=Pil1=&nbsp;</td>
	  <td class=cna=NA=>=Pil2=&nbsp;</td>
      <td class=cna=NA=>=Pil3=&nbsp;</td>
      <td class=cna=NA=>=JenisSekolahID=</td>
	  <td class=cna=NA=><center><img src='img/=NA=.gif' border=0></td>
	  <td class=cna=NA= align=right>=HRG=</td>
      <td class=cna=NA= align=center><a href='?mnux=pmbform&gos=SyaratEdt&pmbid==PMBID='><img src='img/=SyaratLengkap=.gif'></a></td>
	  <td class=cna=NA=><a href='pmbform.kartu.php?pmbid==PMBID='>Cetak</a></td></tr>";
    $lister->footerfmt = "</table></p>";
    $halaman = $lister->WritePages ($pagefmt, $pageoff);
    $TotalNews = $lister->MaxRowCount;
    $usrlist = $lister->ListIt () .
	  "<br>Halaman: $halaman<br>
	  Total: $TotalNews";
    echo $usrlist;
}
function PMBEdt0() {
  PMBEdt('pmbform', 'PMBSav0');
}
function PMBSav0() {
  PMBSav();
  DftrPMB();
}
function SyaratEdt() {
  $w = GetFields("pmb p
    left outer join prodi prd on p.ProdiID=prd.ProdiID
    left outer join program prg on p.ProgramID=prg.ProgramID
    left outer join statusawal sta on p.StatusAwalID=sta.StatusAwalID",
    'p.PMBID', $_REQUEST['pmbid'], 'p.*,
    prd.Nama as PRD, prg.Nama as PRG, sta.Nama as STA');
  TampilkanHeaderPMB($w, 'pmbform&gos=');
  $a = TampilkanPMBSyarat($w);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbform'>
  <input type=hidden name='gos' value='SyaratSav'>
  <input type=hidden name='pmbid' value='$w[PMBID]'>
  <tr><th class=ttl>Syarat-syarat</th></tr>
  <tr><td class=ul>Berikut adalah syarat-syarat yg harus dilengkapi:</td></tr>
  <tr><td class=ul>$a</td></tr>
  <tr><td class=ul><input type=submit name='Simpan' value='Simpan'></td></tr>
  </form></table>";
}
function SyaratSav() {
  $pmbid = $_REQUEST['pmbid'];
  $_syarat = array();
  $_syarat = $_REQUEST['PMBSyaratID'];
  $syarat = (empty($_syarat))? '' : '.' . implode('.', $_syarat) .'.';
  // Cek Kelengkapan
  $mhsw = GetFields('pmb', 'PMBID', $pmbid, 'StatusAwalID, ProdiID, Syarat, SyaratLengkap');
  $s = "select PMBSyaratID, Nama
    from pmbsyarat
    where NA='N' and KodeID='$_SESSION[KodeID]'
      and INSTR(StatusAwalID, '.$mhsw[StatusAwalID].') >0
      and INSTR(ProdiID, '.$mhsw[ProdiID].') >0
    order by PMBSyaratID";
  $r = _query($s);
  $lkp = True;
  while ($w = _fetch_array($r)) {
    if (array_search($w['PMBSyaratID'], $_syarat) === false)
      $lkp = false;
  }
  $Lengkap = ($lkp == true)? 'Y' : 'N';
  // Simpan
  $s = "update pmb set Syarat='$syarat', SyaratLengkap='$Lengkap' where PMBID='$pmbid' ";
  $r = _query($s);
  DftrPMB();
}


// *** Parameters ***
$prgid = GetSetVar('prgid');
$pmbfid = GetSetVar('pmbfid');

// *** Main ***
include "header.php";
TampilkanJudul("Pendaftaran Mahasiswa Online");
if (!empty($pmbaktif)) {
  $gos = (empty($_REQUEST['gos']))? 'PilihanForm' : $_REQUEST['gos'];
  //TampilkanOpsiForm();
  $gos();
}
else echo ErrorMsg("Gagal",
  "Tidak ada periode PMB yang aktif. Hubungi Kepala Admisi untuk mengaktifkan periode/gelombang PMB.");
?>
<div class='footer'>
  <hr size=1 color=silver>
  <center>Powered by <a href="http://www.sisfokampus.net" title="PT Sisfo Sukses Mandiri">Sisfo Kampus 2006</a></center>
  </div>
</BODY>

</HTML>
