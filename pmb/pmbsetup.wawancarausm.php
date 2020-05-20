<?php
// Author : Emanuel Setio Dewo
// Start  : 5 Agustus 2008
// Email  : setio.dewo@gmail.com

// *** Parameters ***
$_usmProdiID = GetSetVar('_usmProdiID');

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrProdi' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function DftrProdi() {
  // container
  $f = GetProdi();
  $u = GetWawanProdi();
  echo "<p><table class=bsc cellspacing=1 align=center width=800>
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
    if ($w['ProdiID'] == $_SESSION['_usmProdiID']) {
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
      <td $c><a href='?mnux=$_SESSION[mnux]&gos=wawancarausm&sub=&_usmProdiID=$w[ProdiID]'>$w[Nama]</a></td>
      <td $c width=10>$ka</td>
      </tr>";
  }
  $a .= "</table>";
  return $a;
}

function GetWawanProdi() {
  EditWawancaraScript();
  $gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
  $s = "select u.*,
    date_format(u.Tanggal, '%d-%m-%Y') as _TanggalWawancara,
    date_format(u.JamMulai, '%H:%i') as _JamWawancara,
	date_format(u.JamSelesai, '%H:%i') as _AkhirJamWawancara
	from wawancarausm u
    where u.KodeID = '".KodeID."'
      and u.ProdiID = '$_SESSION[_usmProdiID]'
	  and u.PMBPeriodID = '$gelombang'
    order by u.Tanggal, u.JamMulai, u.JamSelesai";
  $r = _query($s);
  
  $a = "<table class=box cellspacing=1 width=100%>
		<tr>";
  
  $a .="<td class=ul1><input type=button name='Tambah' value='+'
          onClick=\"javascript:EditWawancara(1,'$_SESSION[_usmProdiID]', 0)\" /></td>";
  $a .="    <th class=ttl colspan=6>
        Wawancara yg harus ditempuh Cama
    </tr>
    <tr><th class=ttl >#</th>
        <th class=ttl width=80>Tgl Wawancara</th>
		<th class=ttl width=80>Jam Wawancara</th>
		<th class=ttl width=100>Ruang</th>
        <th class=ttl width=30>Panjang<br>(menit)</th>
		<th class=ttl width=30>Kuota<br>(orang)</th>
		<th class=ttl width=10>&nbsp;</th>
        </tr>";
  while ($w = _fetch_array($r)) {
    $a .= "<tr>
      <td class=ul1 width=10 align=center>
        <a href='#' onClick=\"javascript:EditWawancara(0, '$_SESSION[_usmProdiID]', $w[WawancaraUSMID])\" /><img src='img/edit.png' /></a>
        </td>
      <td class=ul1>$w[_TanggalWawancara]</td>
	  <td class=ul1>$w[_JamWawancara] - $w[_AkhirJamWawancara]</td>
      <td class=ul1 align=right>$w[RuangID]</td>
      <td class=ul1 align=center>$w[PanjangWaktu]</td>
	  <td class=ul1 align=center>$w[Kapasitas]</td>
	  <td class=ul1 align=center>
        <a href='#' onClick=\"javascript:DelWawancara($w[WawancaraUSMID])\"><img src='img/del.gif' /></a>
        </td>
      </tr>";
  }
  $a .= "</table>";
  return $a;
}
function DelWawancara() {
  $id = $_REQUEST['id'];
  $s = "delete from wawancarausm where WawancaraUSMID = '$id' ";
  $r = _query($s);
  echo "<script>window.location='index.php?mnux=$_SESSION[mnux]&gos=wawancarausm'</script>";
}
function EditWawancaraScript() {
  echo <<<SCR
  <script>
  function EditWawancara(MD,PRD,ID) {
    lnk = "$_SESSION[mnux].wawancarausm.edit.php?md="+MD+"&prd="+PRD+"&id="+ID;
    win2 = window.open(lnk, "", "width=600, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function FillAllWawancara(MD,PRD,ID) {
	lnk = "$_SESSION[mnux].wawancarausm.editall.php?md="+MD+"&prd="+PRD+"&id="+ID;
    win2 = window.open(lnk, "", "width=700, height=200, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function DelWawancara(ID) {
    if (confirm('Benar Anda akan menghapus data ini?')) {
      window.location = 'index.php?mnux=$_SESSION[mnux]&gos=wawancarausm&sub=DelWawancara&BypassMenu=1&id='+ID;
    }
  }
  </script>
SCR;
}

?>
