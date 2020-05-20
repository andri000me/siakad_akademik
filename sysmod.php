<div class="container">
<?php
// Author: SIAKAD TEAM
// 2005-12-17

// *** Functions ***
function TampilkanMdlGrp() {
//function GetOption2($_table, $_field, $_order='', $_default='', $_where='', $_value='', $not=0) {
  $opt = GetOption2('mdlgrp', "concat(Urutan, '. ', Nama)", 'Urutan', $_SESSION['mdlgrp'], '', 'MdlGrpID');
  echo "<p><table class=box cellspacing=1 cellpadding=4 align=center>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='sysmod'>
  <input type=hidden name='token' value='DftrMdl'>
  <tr><td class=inp1>Group Modul</td><td class=ul><select name='mdlgrp' onChange=\"this.form.submit()\">$opt</select></td></tr>
  </form></table></p>";
}
function TampilkanMenuModul() {
  echo "<p align=center><a href=\"?mnux=sysmod&token=DftrMdl\" class='btn btn-info'>Daftar Modul</a> 
    <a href=\"?mnux=sysmod&token=ModEdt&md=1\" class='btn btn-warning'>Tambah Modul</a> 
    <a href=\"?mnux=sysmod&token=DftrGrp\" class='btn btn-success'>Daftar Group</a> 
    <a href=\"?mnux=sysmod&token=GrpEdt&md=1\" class='btn btn-info'>Tambah Group</a>
    </p>";
}
function DftrMdl() {
  TampilkanMdlGrp();
  $whr = '';
  $whr .= (empty($_SESSION['mdlgrp']))? '' : "and m.MdlGrpID='$_SESSION[mdlgrp]' ";
  $s = "select m.*, mg.Urutan
    from mdl m
    left outer join mdlgrp mg on m.MdlGrpID=mg.MdlGrpID
    where m.MdlID>0 $whr
    order by mg.Urutan, m.Nama";
  $r = mysql_query($s) or die("Gagal: $s<br>".mysql_error());
  $n = 0;
  TampilkanMenuModul();
  echo "<p><table class=box cellspacing=1 cellpadding=4 align=center width=800>
    <tr>
    <th class=ttl>No</th>
    <th class=ttl>Aksi</th><th class=ttl>Module</td>
    <th class=ttl>Level</th>
    <th class=ttl>Script</th>
    <th class=ttl>Web</th>
    <th class=ttl>Group</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = mysql_fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
      <td $c>$n</td>
      <td $c><a href=\"?mnux=$_SESSION[mnux]&token=ModEdt&md=0&mid=$w[MdlID]\" class='btn btn-danger'><i class ='fa fa-edit'></i></a>
      </td>
      <td $c>$w[Nama]</td>
      <td $c>$w[LevelID]</td>
      <td $c>$w[Script]</td>
      <td $c align=center width=5><img src='img/$w[Web].gif'></td>
      <td $c>$w[MdlGrpID]</td>
      <td $c align=center width=5>
        <a href='?mnux=$_SESSION[mnux]&token=ModNA&mid=$w[MdlID]&BypassMenu=1'><img src='img/book$w[NA].gif'></a>
        </td>
      </tr>";
  }
  echo "</table></p>";
}
function ModNA() {
  $mid = $_REQUEST['mid'];
  $m = GetaField('mdl', 'MdlID', $mid, 'NA');
  $NA = ($m['NA'] == 'N')? 'Y' : 'N';
  $s = "update mdl set NA = '$NA' where MdlID = '$mid' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}
function ModEdt() {
  global $_Author, $_AuthorEmail;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('mdl', 'MdlID', $_REQUEST['mid'], '*');
    $jdl = 'Edit Modul';
  }
  else {
    $w = array();
    $w['MdlID'] = '';
    $w['MdlGrpID'] = $_SESSION['mdlgrp'];
    $w['Nama'] = '';
    $w['Script'] = '';
    $w['LevelID'] = '.';
    $w['Web'] = 'Y';
    $w['Author'] = $_Author;
    $w['EmailAuthor'] = $_AuthorEmail;
    $w['Simbol'] = '';
    $w['Help'] = '';
    $w['NA'] = 'N';
    $w['Keterangan'] = '';
    $jdl = "Tambah Modul";
  }
  $optgrp = GetOption2('mdlgrp', "concat(MdlGrpID, ' - ', Nama)", 'Nama', $w['MdlGrpID'], '', 'MdlGrpID');
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $web = ($w['Web'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  $DftrLevel = GetDftrLevel($w['LevelID']);
  // Tampilkan form
  echo "<p><table class=box cellspacing=1 cellpadding=4 align=center>
  <form action='?' name='data' method=POST>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='MdlID' value='$w[MdlID]'>
  <input type=hidden name='mnux' value='sysmod'>
  <input type=hidden name='token' value='ModSav'>
  <input type=hidden name='BypassMenu' value='1' />

  <tr><th colspan=3 class=ttl>$jdl</th></tr>
  <tr><td class=inp>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td>
    <td class=ul rowspan=12 valign=bottom>
<div class='callout callout-warning'><i class='fa fa-key'></i>Hak Akses Modul</div> <br />
    $DftrLevel</td></tr>

  <tr><td class=inp>Group</td><td class=ul><select name='MdlGrpID'>$optgrp</select></td></tr>
  <tr><td class=inp>Script</td><td class=ul><input type=text name='Script' value='$w[Script]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Level</td><td class=ul><input type=text name='LevelID' value='$w[LevelID]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Versi Web</td><td class=ul><input type=checkbox name='Web' value='Y' $web></td></tr>
  <tr><td class=inp>Author</td><td class=ul><input type=text name='Author' value='$w[Author]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Email</td><td class=ul><input type=text name='EmailAuthor' value='$w[EmailAuthor]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Simbol</td><td class=ul><input type=text name='Simbol' value='$w[Simbol]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Help</td><td class=ul><input type=text name='Help' value='$w[Help]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>NA (tdk aktif)</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=inp>Keterangan</td><td class=ul><textarea class='form-control' name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea></td></tr>
  <tr><td colspan=2 align=center><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=sysmod'\"></td></tr>
  </form></table></p>";
}
function ModSav() {
  $md = $_REQUEST['md'];
  $MdlID = $_REQUEST['MdlID'];
  $MdlGrpID = $_REQUEST['MdlGrpID'];
  $Nama = sqling($_REQUEST['Nama']);
  $Script = $_REQUEST['Script'];
  $_levelid = TRIM($_REQUEST['LevelID'], '.');
  if (empty($_levelid)) $LevelID = '';
  else {
    $arrLevelID = explode('.', $_levelid);
    sort($arrLevelID);
    $LevelID = '.'.implode('.', $arrLevelID).'.';
  }
  $Web = (!empty($_REQUEST['Web']))? $_REQUEST['Web'] : 'N';
  $Author = sqling($_REQUEST['Author']);
  $EmailAuthor = sqling($_REQUEST['EmailAuthor']);
  $Simbol = $_REQUEST['Simbol'];
  $Help = $_REQUEST['Help'];
  $NA = (!empty($_REQUEST['NA']))? $_REQUEST['NA'] : 'N';
  $Keterangan = sqling($_REQUEST['Keterangan']);
  // Simpan
  if ($md == 0) {
    $s = "update mdl set Nama='$Nama', MdlGrpID='$MdlGrpID', Script='$Script',
      LevelID='$LevelID', Web='$Web',
      Author='$Author', EmailAuthor='$EmailAuthor', Simbol='$Simbol',
      Help='$Help', NA='$NA', Keterangan='$Keterangan'
      where MdlID='$MdlID'";
  }
  else {
    $s = "insert into mdl (MdlGrpID, Nama, Script, LevelID, Web,
      Author, EmailAuthor, Simbol, Help, NA, Keterangan)
      values ('$MdlGrpID', '$Nama', '$Script', '$LevelID', '$Web',
      '$Author', '$EmailAuthor', '$Simbol', '$Help', '$NA', '$Keterangan')";
  }
  _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 100);
}
function GetDftrLevel($lvl='') {
  TulisScriptUbahLevel();
  $s = "select *
    from level
    order by LevelID";
  $r = _query($s);
  $a = '';

  while ($w = _fetch_array($r)) {
    $ck = (strpos($lvl, ".$w[LevelID].") === false)? '' : 'checked';
    $a .= "<input type=checkbox name='Level$w[LevelID]' value='$w[LevelID]' $ck onChange='javascript:UbahLevel(data.Level$w[LevelID])'> $w[LevelID] - $w[Nama]<br />";
  }
  return $a;
}
function TulisScriptUbahLevel() {
  echo <<<END
  <SCRIPT LANGUAGE="JavaScript1.2">
  function UbahLevel(nm){
    ck = "";
    if (nm.checked == true) {
      var nilai = data.LevelID.value;
      if (nilai.match(nm.value+".") != nm.value+".") data.LevelID.value += nm.value + ".";
    }
    else {
      var nilai = data.LevelID.value;
      data.LevelID.value = nilai.replace(nm.value+".", "");
    }
  }
  //-->
  </script>
END;
}
function DftrGrp() {
  TampilkanMenuModul();
  $s = "select mg.*
    from mdlgrp mg
    order by mg.Urutan";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4 align=center>
  <tr><th class=ttl>#</th>
  <th class=ttl>ID</th>
  <th class=ttl>Group</th>
  <th class=ttl>Nama</th>
  <th class=ttl>NA</th></tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td class=inp1>$w[Urutan]</td>
    <td $c><a href='?mnux=sysmod&token=GrpEdt&md=0&grid=$w[MdlGrpID]'><img src='img/edit.png' border=0>
    $w[MdlGrpID]</a></td>
    <td $c>$w[Nama]</td>
    <td $c align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function GrpEdt() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('mdlgrp', 'MdlGrpID', $_REQUEST['grid'], '*');
    $_grid = "<input type=hidden name='MdlGrpID' value='$w[MdlGrpID]'><b>$w[MdlGrpID]</b>";
    $jdl = "Edit Group";
  }
  else {
    $w = array();
    $w['MdlGrpID'] = '';
    $w['Nama'] = '';
    $w['Urutan'] = 0;
    $w['NA'] = 'N';
    $_grid = "<input type=text name='MdlGrpID' size=10 maxlength=10>";
    $jdl = "Tambah Group";
  }
  $_NA = ($w['NA'] == 'Y')? 'checked' : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='sysmod'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='token' value='GrpSav'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp1>Group ID</td><td class=ul>$_grid</td></tr>
  <tr><td class=inp1>Nama Group</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=20 maxlength=50></td></tr>
  <tr><td class=inp1>Urutan</td><td class=ul><input type=text name='Urutan' value='$w[Urutan]' size=5 maxlength=5></td></tr>
  <tr><td class=inp1>Tidak Aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $_NA></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
  <input type=reset name='Reset' value='Reset'>
  <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=sysmod&token=DftrGrp'\"></td></tr>
  </form></table></p>";
}
function GrpSav() {
  $md = $_REQUEST['md'];
  $MdlGrpID = $_REQUEST['MdlGrpID'];
  if (!empty($MdlGrpID)) {
    $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
    $Nama = sqling($_REQUEST['Nama']);
    $Urutan = $_REQUEST['Urutan']+0;
    if ($md == 0) {
      $s = "update mdlgrp set Nama='$Nama', Urutan='$Urutan', NA='$NA' where MdlGrpID='$MdlGrpID' ";
      $r = _query($s);
      echo "<script>window.location='?mnux=$_SESSION[mnux]&gos='</script>";
    }
    else {
      $ada = GetFields('mdlgrp', 'MdlGrpID', $MdlGrpID, '*');
      if (empty($ada)) {
        $s = "insert into mdlgrp (MdlGrpID, Nama, Urutan, NA)
          values ('$MdlGrpID', '$Nama', '$Urutan', '$NA')";
        $r = _query($s);
        echo "<script>window.location='?mnux=$_SESSION[mnux]&gos='</script>";
      }
      else echo ErrorMsg("Data Tidak Dapat Disimpan",
        "Group dengan ID <b>$MdlGrpID</b> telah ada. Gunakan ID lain.");
    }
  }
  DftrGrp();
}

// *** Parameters ***
$mdlgrp = GetSetVar('mdlgrp');
$token = (empty($_REQUEST['token']))? 'DftrMdl' : $_REQUEST['token'];


// *** Main ***
TampilkanJudul("Modul $_ProductName");
$token();
?>
</div>