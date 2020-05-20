<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Daftar Bimbingan Skripsi/TA", 1);

if ($_SESSION['_LevelID']==100) {
	$cek = GetFields('ta',"TAID", $_REQUEST['TAID'],"Pembimbing,Pembimbing2");
	if ($cek['Pembimbing']!=$_SESSION['_Login'] && $cek['Pembimbing2']!=$_SESSION['_Login']) {
		die(ErrorMsg('Error',
      "Anda tidak berhak mengakses modul ini.<br />
      Hubungi SysAdmin untuk informasi lebih lanjut."));
	}
}
// *** Parameters ***

// *** Main ***
TampilkanJudul("Daftar Bimbingan Skripsi/TA");
$sub = (empty($_REQUEST['sub']))? 'TampilkanDaftarBimbinganTA' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function TampilkanDaftarBimbinganTA() {
  $ta = GetFields("ta t
    left outer join mhsw m on t.MhswID = m.MhswID and m.KodeID='".KodeID."'
    left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID='".KodeID."'
    left outer join dosen d1 on d1.Login = t.Pembimbing and d1.KodeID='".KodeID."'
	left outer join dosen d2 on d2.Login = t.Pembimbing2 and d2.KodeID='".KodeID."'
    ", 
    "t.TAID", $_REQUEST['TAID'], 
    "t.*, m.Nama as NamaMhsw,
    date_format(t.TglMulai, '%d-%m-%Y') as _TglMulai,
    date_format(t.TglSelesai, '%d-%m-%Y') as _TglSelesai,
    d.Nama as NamaPA, d.Gelar as GelarPA,
    d1.Nama as NamaPembimbing, d1.Gelar as GelarPembimbing, d1.Gelar1 as GelarPembimbingDepan,
	d2.Nama as NamaPembimbing2, d2.Gelar as GelarPembimbing2, d2.Gelar1 as GelarPembimbingDepan2");
  TampilkanHeaderBimbingan($ta);
}
function TampilkanHeaderBimbingan($ta) {
	$PA = (empty($ta['NamaPA']))? 'Belum diset' : "$ta[NamaPA] <sup>$ta[GelarPA]</sup>";	
	$ss = "select td.* from tadosen td
			left outer join ta t on td.TAID = t.TAID
			where t.TAID = '".$ta[TAID]."'
			and t.NA = 'N'";
	$qss = _query($ss);
	
	$rd = 'readonly=TRUE';
	
	if (_num_rows($qss) != 0) {
		$s = "select t.BobotNilai from tadosen td
				left outer join ta t on td.TAID = t.TAID
				where t.TAID = '".$ta[TAID]."'
				and t.BobotNilai != '0.00'
				and t.NA = 'N'";
		$qs = _query($s);
		
		$TglUjian = (_num_rows($qs) == 0)? $TglUjian : substr($ta[_TglUjian],0,10);
		$JamUjian = (_num_rows($qs) == 0)? $JamUjian : substr($ta[_JamUjian],0,5);
		
		$rd = (_num_rows($qs) == 0)? '' : 'readonly=true';
		$cari = "";
		
	} else {
		$TglUjian = GetDateOption(date('Y-m-d'), 'TglUjian');
		$JamUjian = GetTimeOption(date('h:i'), 'JamUjian');
		//$cari = "<a href='javascript:SearchDosen(\"$_SESSION[ProdiID]\",\"frmJdwl\",\"$i\")'>Cari...</a>";
		$cari = "";
	}
	
	$BobotNilai = GetaField('ta',"TAID",$_REQUEST['TAID'],"BobotNilai");
	
	$tambah = ($BobotNilai != '0.00')? '' : "<input type=button name='Tambah' value='Tambah Bimbingan' onClick=\"javascript:EditBimbingan($_REQUEST[TAID],1,'')\" />";

echo <<<SCR
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmJdwl' action='../$_SESSION[mnux].ta.ujian.php' method=POST />
  <input type=hidden name='TAID' value='$ta[TAID]' />
   <input type=hidden name='MhswID' value='$ta[MhswID]' />
  <input type=hidden name='sub' value='Simpan' />
  <tr><td class=inp width=160>NIM:</td>
      <td class=ul>$ta[MhswID]</td>
      <td class=inp>Mahasiswa:</td>
      <td class=ul>$ta[NamaMhsw]</td>
      </tr>
  <tr><td class=inp>Penasehat Akademik:</td>
      <td class=ul>$PA</td>
      <td class=inp>Pembimbing Skripsi/TA:</td>
      <td class=ul>1. $ta[GelarPembimbingDepan] $ta[NamaPembimbing] <sup>$ta[GelarPembimbing]</sup>
	  				2. $ta[GelarPembimbingDepan2] $ta[NamaPembimbing2] <sup>$ta[GelarPembimbing2]</sup></td>
      </tr>
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul>$ta[TahunID]</td>
      <td class=inp>Batas Waktu:</td>
      <td class=ul><sup>$ta[_TglMulai]</sup> &#8883; <sub>$ta[_TglSelesai]</sub></td>
      </tr>
  <tr><td class=inp>Judul:</td>
      <td class=ul colspan=3>$ta[Judul]</td>
      </tr>
  <tr><td class=inp>Deskripsi/Abstrak:</td>
      <td class=ul colspan=3>$ta[Deskripsi]</td>
      </tr>
  <tr><td class=ul colspan=4 align=center>
  $tambah
      <input type=button name='Cetak' value='Cetak' onClick="CetakBimbingan('$ta[TAID]')" />
	  <input type=button name='Refresh' value='Refresh'
        onClick="location='../$_SESSION[mnux].bimbingan.php?TAID=$ta[TAID]'" />
      <input type=button name='Tutup' value='Tutup' onClick='javascript:TutupDong()' />
  </td>
      </tr>
	

  <tr><td colspan="4">
  	<table class=box cellspacing=1 cellpadding=4 width=100%>
	<tr>
		<th class=ttl colspan="4">Kegiatan Bimbingan</th>
	</tr>
	<tr>
		<th class=ttl align=center width=20>No.</th>
		<th class=ttl align=center width=120>Tanggal</th>
		<th class=ttl align=center>Catatan</th>
		<th class=ttl align=center width=80>Edit / Delete</th>
	</tr>
	
SCR;

$bim = "select date_format(tb.TglBimbingan,'%d %M %Y') as _TglBimbingan, tb.Catatan, tb.BimbinganID, tb.Tipe from tabimbingan tb
		left outer join ta t on t.TAID = tb.TAID
		where tb.TAID = '".$_REQUEST['TAID']."'
		and tb.NA = 'N' order by TglBimbingan";
$qb = _query($bim);

$jum = _num_rows($qb);
$x=1;
while ($b = _fetch_array($qb)){

$edit = ($BobotNilai != '0.00')? '' : "<a href=\"javascript:EditBimbingan($_REQUEST[TAID],0,$b[BimbinganID])\" title='Edit Kegiatan Bimbingan'><img src='../img/edit.png' /></a>";
$del = ($BobotNilai != '0.00')? '' : "<a href=\"javascript:DelBimbingan($_REQUEST[TAID],0,$b[BimbinganID])\" title='Hapus Kegiatan Bimbingan'><img src='../img/del.gif' /></a>";

echo <<<SCR
	<tr>
	<td class=inp align=center>$x</td>
	<td class=cna$Acc align=center><sup>$b[_TglBimbingan]</sup></td>
	<td class=cna$Acc><b>Pembimbing $b[Tipe]:</b> <br />$b[Catatan]</td>
	<td class=cna$Acc align=center>
	$edit
	&nbsp;&nbsp;
	$del
	</td>
	<tr>
	<tr><td bgcolor=silver colspan=4 height=1></td></tr>
SCR;
$x++;
}

$x3 = $jum+1;
for ($i=$x3;$i<=16;$i++){
echo <<<SCR
	<tr>
	<td class=inp align=center>$i</td>
	<td class=cna$Acc align=center>&nbsp;</td>
	<td class=cna$Acc>&nbsp;</td>
	<td class=cna$Acc align=center>&nbsp;</td>
	<tr>
	<tr><td bgcolor=silver colspan=4 height=1></td></tr>
SCR;
}

echo <<<SCR
	</table>
	</td>
	</tr>
  <tr><td class=ul colspan=4 align=center>&nbsp;
      </td></tr>
  </form>
  </table>
    
  <script>
  <!--
  function TutupDong() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&sub=';
    self.close();
    return false;
  }
  function EditBimbingan(TAID,md,id) {
	lnk = "../$_SESSION[mnux].bimbingan.edit.php?TAID="+TAID+"&md="+md+"&id="+id;
	win2 = window.open(lnk, "", "width=500, height=300, scrollbars, status");
	win2.moveTo(100,100);
	if (win2.opener == null) childWindow.opener = self;
  }
  function DelBimbingan(TAID,md,id){
  	if (confirm("Anda yakin akan menghapus kegiatan bimbingan ini?")){
		window.location = "../$_SESSION[mnux].bimbingan.php?TAID="+TAID+"&md="+md+"&sub=DelBimbingan&id="+id;
	} 
  }
  function CetakBimbingan(TAID) {
	lnk = "../$_SESSION[mnux].bimbingan.cetak.php?TAID="+TAID;
	win2 = window.open(lnk, "", "width=600, height=800, scrollbars, status");
	win2.moveTo(100,100);
	if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>

SCR;
}

function DelBimbingan(){
	$s = "delete from tabimbingan where BimbinganID = '".$_REQUEST[id]."'";
	$q = _query($s);
	echo "<script>window.location='../$_SESSION[mnux].bimbingan.php?TAID=$_REQUEST[TAID]&md=$_REQUEST[md]&sub='</script>";
}

if ($_REQUEST[ref] == 1){
echo <<<SCR
	<script>
		opener.location='../index.php?mnux=$_SESSION[mnux]&sub=';
	</script>
SCR;
}
?>

</BODY>
</HTML>
