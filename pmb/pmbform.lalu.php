<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 13/10/2008
// Desc   : Ambil pendaftar dari periode yg lalu

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Data PMB Gelombang Lalu");

// *** Parameters ***
$gel = GetSetVar('gel');
$gel0 = GetSetVar('gel0');
$_pmbNama = GetSetVar('_pmbNama');
$_pmbNomer = GetSetVar('_pmbNomer');

// *** Main ***
TampilkanJudul("Ambil Data Pendaftar Dari Gelombang Lalu");
$gos = (empty($_REQUEST['gos']))? 'DftrPMB' : $_REQUEST['gos'];
$gos($gel, $gel0);

// *** Functions ***
function DftrPMB($gel, $gel0) {
  if (empty($gel0)) {
    $gel0 = GetaField('pmbperiod',
      "PMBPeriodID < '$gel' and KodeID", KodeID,
      "PMBPeriodID");
    $_SESSION['gel0'] = $gel0;
  }
  if (empty($gel0))
    die(ErrorMsg('Error',
      "Tidak ditemukan data gelombang sebelum gel <b>$gel</b>.<br />
      Hubungi Kepala PMB atau Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));
  
  TampilkanHeadernya($gel, $gel0);
  TampilkanDaftarnya($gel, $gel0);
}
function TampilkanHeadernya($gel, $gel0) {
  echo <<<ESD
  <table class=box cellspacing=1 width=100% align=center>
  <form name='frmLalu' action='../$_SESSION[mnux].lalu.php' method=POST>
  <input type=hidden name='gel' value='$gel' />
  
  <tr>
      <td class=inp>Gelombang Lalu:</td>
      <td class=ul1>
        <input type=text name='gel0' value='$gel0' size=10 maxlength=10 />
      </td>
      
      <td class=inp>Nama:</td>
      <td class=ul1>
        <input type=text name='_pmbNama' value='$_SESSION[_pmbNama]'
          size=10 maxlength=50 />
      </td>
      
      <td class=inp>Nomer PMB:</td>
      <td class=ul1>
        <input type=text name='_pmbNomer' value='$_SESSION[_pmbNomer]'
          size=10 maxlength=50 />
      </td>
      
      <td class=ul1 nowrap align=right>
        <input type=submit name='Cari' value='Cari' />
        <input type=button name='Reset' value='Reset'
          onClick="location='../$_SESSION[mnux].lalu.php?gos=&_pmbNama=&_pmbNomer='" />
        <input type=button name='Tutup' value='Tutup'
          onClick="window.close()" />
      </td>
      </tr>
  
  </form>
  </table>
ESD;
}
function TampilkanDaftarnya($gel, $gel0) {
$_maxbaris = 10;
  include_once "../class/dwolister.class.php";

  // Filter formulir
  $whr = array();
  if (!empty($_SESSION['_pmbNama']))  $whr[] = "p.Nama like '%$_SESSION[_pmbNama]%'";
  if (!empty($_SESSION['_pmbNomer'])) $whr[] = "p.PMBID like '%$_SESSION[_pmbNomer]%'";
  if (!empty($gel0)) $whr[] = "p.PMBPeriodID = '$gel0' ";
  
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : 'and '.$_whr;
  
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_pmbPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  $lst = new dwolister;
  $lst->tables = "pmb p 
    left outer join pmbformulir f on p.PMBFormulirID = f.PMBFormulirID
    left outer join prodi _p1 on p.Pilihan1 = _p1.ProdiID
    left outer join prodi _p2 on p.Pilihan2 = _p2.ProdiID
    left outer join prodi _p3 on p.Pilihan3 = _p3.ProdiID
    left outer join program _prg on p.ProgramID = _prg.ProgramID
    left outer join statusawal _sta on p.StatusAwalID = _sta.StatusAwalID
    where p.KodeID = '".KodeID."' 
      and p.PMBPeriodID < '$gel'
      and p.NA = 'N'
      $_whr
    order by p.Nama";
  $lst->fields = "p.PMBID, p.Nama, p.Kelamin, p.ProdiID, p.Pilihan1, p.Pilihan2, p.Pilihan3,
    f.Nama as FRM, _p1.Nama as P1, _p2.Nama as P2, _p3.Nama as P3,
    _sta.Nama as STA, _prg.Nama as PRG";
  //$lst->startrow = $_SESSION['_pmbPage']+0;
  $lst->maxrow = $_maxbaris;
  $lst->pages = $pagefmt;
  $lst->pageactive = $pageoff;
  $lst->page = $_SESSION['_pmbPage']+0;
  $lst->headerfmt = "<table class=box cellspacing=1 align=center width=100%>
    
    <tr>
    <th class=ttl>#</th>
    <th class=ttl>PMB #</th>
    <th class=ttl colspan=2>Nama</th>
    <th class=ttl>Status</th>
    <th class=ttl>Formulir<hr size=1 color=silver />Program</th>
    <th class=ttl>Pilihan1</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class=ul1 width=80>
      <b>=PMBID=</b><br />
      <input type=button name='Ambil' value='Ambil'
        onClick=\"location='../$_SESSION[mnux].lalu.php?gos=AmbilSaja&id==PMBID='\" />
      </td>
    <td class=cna=NA=>=Nama=</td>
    <td class=cna=NA= width=10 align=center><img src='../img/=Kelamin=.bmp' /></td>
    <td class=cna=NA= width=70>=STA=</td>
    <td class=cna=NA= width=120>
      =FRM=&nbsp;
      <hr size=1 color=silver />
      =PRG=&nbsp;
      </td>
    <td class=cna=NA= width=180>
      =P1=&nbsp;
      <hr size=1 color=silver />
      =P2=&nbsp;
      <hr size=1 color=silver />
      =P3=&nbsp;</td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table>";

  $hal = $lst->TampilkanHalaman($pagefmt, $pageoff);
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}
function AmbilSaja($gel, $gel0) {
  $id = $_REQUEST['id'];
  $p = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $id, '*');
  if (empty($p))
    die(ErrorMsg('Error',
      "Data PMB untuk nomer: <b>$id</b> tidak ditemukan. <br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Tutup' value='Tutup' onClick='window.close()' />
      "));
  // Jika ketemu
  $pmbid = GetNextPMBID($gel);
  $s = "insert into pmb
    (PMBID, PMBRef, PMBFormulirID, PMBPeriodID, PMBFormJualID,
    BuktiSetoran, KodeID, BIPOTID,
    Nama, StatusAwalID, ProgramID, ProdiID,
    Kelamin, GolonganDarah,
    WargaNegara, Kebangsaan,
    TempatLahir, TanggalLahir,
    Agama, StatusSipil, TinggiBadan, BeratBadan,
    Alamat, Kota, RT, RW, KodePos, Propinsi, Negara,
    Telepon, Handphone, Email,
    AlamatAsal, KotaAsal, RTAsal, RWAsal, KodePosAsal, PropinsiAsal, NegaraAsal, TeleponAsal,
    NamaAyah, AgamaAyah, PendidikanAyah, PekerjaanAyah, HidupAyah,
    NamaIbu, AgamaIbu, PendidikanIbu, PekerjaanIbu, HidupIbu,
    AlamatOrtu, KotaOrtu, RTOrtu, RWOrtu, KodePosOrtu, PropinsiOrtu, NegaraOrtu, 
    TeleponOrtu, HandphoneOrtu, EmailOrtu,
    PendidikanTerakhir, AsalSekolah, JenisSekolahID,
    AlamatSekolah, KotaSekolah, JurusanSekolah, NilaiSekolah, TahunLulus,
    AsalPT, ProdiAsalPT, LulusAsalPT, TglLulusAsalPT,
    Pilihan1, Pilihan2, Pilihan3,
    NA, NilaiUjian, DetailNilai, GradeNilai, Catatan,
    Syarat, SyaratLengkap, 
    BuktiSetoranMhsw, TanggalSetoranMhsw,
    LoginBuat, TanggalBuat)
    values
    ('$pmbid', '$id', '$p[PMBFormulirID]', '$gel', '$p[PMBFormJualID]',
    '$p[BuktiSetoran]', '".KodeID."', '$p[BIPOTID]',
    '$p[Nama]', '$p[StatusAwalID]', '$p[ProgramID]', '$p[ProdiID]',
    '$p[Kelamin]', '$p[GolonganDarah]',
    '$p[WargaNegara]', '$p[Kebangsaan]',
    '$p[TempatLahir]', '$p[TanggalLahir]',
    '$p[Agama]', '$p[StatusSipil]', '$p[TinggiBadan]', '$p[BeratBadan]',
    '$p[Alamat]', '$p[Kota]', '$p[RT]', '$p[RW]', '$p[KodePos]', '$p[Propinsi]', '$p[Negara]',
    '$p[Telepon]', '$p[Handphone]', '$p[Email]',
    '$p[AlamatAsal]', '$p[KotaAsal]', '$p[RTAsal]', '$p[RWAsal]', '$p[KodePosAsal]', '$p[PropinsiAsal]', '$p[NegaraAsal]', '$p[TeleponAsal]',
    '$p[NamaAyah]', '$p[AgamaAyah]', '$p[PendidikanAyah]', '$p[PekerjaanAyah]', '$p[HidupAyah]',
    '$p[NamaIbu]', '$p[AgamaIbu]', '$p[PendidikanIbu]', '$p[PekerjaanIbu]', '$p[HidupIbu]',
    '$p[AlamatOrtu]', '$p[KotaOrtu]', '$p[RTOrtu]', '$p[RWOrtu]', '$p[KodePosOrtu]', '$p[PropinsiOrtu]', '$p[NegaraOrtu]',
    '$p[TeleponOrtu]', '$p[HandphoneOrtu]', '$p[EmailOrtu]',
    '$p[PendidikanTerakhir]', '$p[AsalSekolah]', '$p[JenisSekolahID]',
    '$p[AlamatSekolah]', '$p[KotaSekolah]', '$p[JurusanSekolah]', '$p[NilaiSekolah]', '$p[TahunLulus]',
    '$p[AsalPT]', '$p[ProdiAsalPT]', '$p[LulusAsalPT]', '$p[TglLulusAsalPT]',
    '$p[Pilihan1]', '$p[Pilihan2]', '$p[Pilihan3]',
    'N', '$p[NilaiUjian]', '$p[DetailNilai]', '$p[GradeNilai]', '$p[Catatan]',
    '$p[Syarat]', '$p[SyaratLengkap]',
    '$p[BuktiSetoranMhsw]', '$p[TanggalSetoranMhsw]',
    '$_SESSION[_Login]', now())";
  $r = _query($s);
  
  echo <<<ESD
  <script>
  opener.location = '../index.php?mnux=$_SESSION[mnux]&gos=';
  self.close();
  </script>
ESD;
}
?>
