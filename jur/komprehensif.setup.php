<?php
// Author : Wisnu
// Start  : 14 Agustus 2008
// Email  : -

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Setup Komprehensif");

// *** Parameters ***
$_kompreProdiID = GetSetVar('_kompreProdiID');
//echo "KOMPRE: $_kompreProdiID";

// *** Main ***
TampilkanJudul("Setup Ujian Komprehensif");
$sub = (empty($_REQUEST['sub']))? 'DftrProdi' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function DftrProdi() {
  // container
  $f = GetProdi();
  if (!empty($_SESSION['_kompreProdiID'])){
  	$u = GetKOMPREProdi();
  } else {
  	$u = "";
  }
  echo "<p><table class=bsc cellspacing=1 align=center width=800>
    <tr><td colspan=2 align=center><input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" /></td></tr>
	<tr><td class=ul valign=top width=350>
        $f
        </td>
        <td class=ul1 valign=top>
		$u
        </td>
        </tr>
    </table></p>";
}
function GetProdi() {
  $s = "select p.ProdiID, p.FakultasID, p.Nama, p.JenjangID,
    f.Nama as FAK
    from prodi p
      left outer join fakultas f on p.FakultasID = f.FakultasID
    where p.KodeID = '".KodeID."' and p.NA = 'N'
    order by p.FakultasID, p.ProdiID";
  $r = _query($s);
  
  $a = "<table class=box cellspacing=1 width=100%>
    <tr>
    <th class=ttl colspan=4>Daftar Prodi</th>
    </tr>";
  $f = 'laskdjfairujfa;ldnf;asdf';
  while ($w = _fetch_array($r)) {
    if ($f != $w['FakultasID']) {
      $f = $w['FakultasID'];
      $a .= "<tr>
        <td class=ul1 colspan=4>
        $w[FakultasID] <b>$w[FAK]</b>
        </td></tr>";
    }
    if ($w['ProdiID'] == $_SESSION['_kompreProdiID']) {
      $ki = "&raquo;";
      $ka = "&laquo;";
      $c = "class=inp1";
    }
    else {
      $ki = '&nbsp;';
      $ka = '&nbsp;';
      $c = "class=ul1";
    }
    $a .= "<tr>
      <td $c width=10>$ki</td>
      <td class=inp width=40>$w[ProdiID]</td>
      <td $c><a href='?mnux=$_SESSION[mnux]&sub=&_kompreProdiID=$w[ProdiID]'>$w[Nama]</a></td>
      <td $c width=10>$ka</td>
      </tr>";
  }
  $a .= "</table>";
  return $a;
}

function GetKOMPREProdi() {
  EditKOMPREScript();
  /*$s = "select u.MKID, u.Nama as _mk
	from mk u
      left outer join kurikulum x on u.KurikulumID = x.KurikulumID
    where u.KodeID = '".KodeID."'
	  and u.Komprehensif = 'Y'
      and u.ProdiID = '$_SESSION[_kompreProdiID]'
	  and x.ProdiID = '$_SESSION[_kompreProdiID]'
	  and u.KurikulumID = '$_SESSION[_KurikulumID]'
	  and x.NA = 'N'
    order by u.MKID";*/
  $PilihanKompre = GetaField('prodi', "ProdiID='$_SESSION[_kompreProdiID]' and KodeID", KodeID, "PilihanKompre");
  $checkKompre = ($PilihanKompre == 'Y')? 'checked' : '';
  $s = "select KompreMataUjiID, KodeKompre, Nama, ProdiID
			from komprematauji
			where ProdiID='$_SESSION[_kompreProdiID]'
				and KodeID='".KodeID."'
			order by KodeKompre";
  $r = _query($s);
  $a = "<table class=box cellspacing=1 width=100%>
	<form name='frmCheckKompre' action='../$_SESSION[mnux].setup.php' method=POST>
	<input type=hidden name='sub' value='SimpanPilihanKompre' >
    <tr><td class=inp colspan=7><input type=checkbox name='PilihanKompre' value='Y' $checkKompre onChange=\"this.form.submit();\">
			Cek bila jadwal tiap mata uji dapat dijadwalkan di waktu berbeda-beda</td></tr>	
	<tr><td class=ul1 colspan=2><input type=button name='Tambah' value='+'
          onClick=\"javascript:EditKOMPRE(1,'$_SESSION[_kompreProdiID]', 0)\" /></td>
        <th class=ttl colspan=4>
        Daftar Ujian Komprehensif yg harus ditempuh Cama
        </th>
    </tr>
    <tr><th class=ttl colspan=2>#</th>
        <th class=ttl colspan=2>Komponen Ujian Komprehensif</th>
		<th class=ttl width=50>Del</th>
        </tr>";
  while ($w = _fetch_array($r)) {
    $a .= "<tr>
      <td class=ul1 width=10 align=center>
        <a href=\"javascript:EditKOMPRE(0, '$_SESSION[_kompreProdiID]', $w[KompreMataUjiID])\" /><img src='../img/edit.png' /></a>
        </td>
      <td class=inp width=10>$w[Urutan]</td>
      <td class=ul1 width=30>$w[KodeKompre]</td>
	  <td class=ul1>- $w[Nama]</td>
      <td class=ul1 align=center>
        <a href='#' onClick=\"javascript:DelKOMPRE('$w[KompreMataUjiID]')\"><img src='../img/del.gif' /></a>
        </td>
      </tr>";
  }
  $a .= "</table>";
  return $a;
}

function SimpanPilihanKompre()
{	$PilihanKompre = $_REQUEST['PilihanKompre'];
	
	$s = "update prodi set PilihanKompre='$PilihanKompre' where ProdiID='$_SESSION[_kompreProdiID]' and KodeID='".KodeID."'";
	$r = _query($s);
	
	echo "<script>location='../$_SESSION[mnux].setup.php'</script>";
}
function DelKOMPRE() {
  $kmuid = $_REQUEST['kmuid'];
  $s = "delete from komprematauji where KompreMataUjiID = '$kmuid' and KodeID = '".KodeID."'";
  $r = _query($s);
  echo "<script>window.location='../$_SESSION[mnux].setup.php'</script>";
}
function EditKOMPREScript() {
  echo <<<SCR
  <script>
  function EditKOMPRE(MD,PRD,ID) {
    lnk = "../$_SESSION[mnux].setup.edit.php?md="+MD+"&prd="+PRD+"&id="+ID;
    win2 = window.open(lnk, "", "width=600, height=300, scrollbars, status");
	win2.moveTo(100,300);
    if (win2.opener == null) childWindow.opener = self;
  }
  function FillAllKOMPRE(MD,PRD,ID) {
	lnk = "../$_SESSION[mnux].prodikompre.editall.php?md="+MD+"&prd="+PRD+"&id="+ID;
    win2 = window.open(lnk, "", "width=500, height=200, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function DelKOMPRE(ID) {
    if (confirm('Benar Anda akan menghapus data ini?')) {
      window.location = '../$_SESSION[mnux].setup.php?gos=prodikompre&sub=DelKOMPRE&BypassMenu=1&kmuid='+ID;
    }
  }
  </script>
SCR;
}

?>
