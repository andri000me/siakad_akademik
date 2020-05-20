<?php
// Author: Emanuel Setio Dewo
// 17 May 2006
// http://www.sisfokampus.net

// *** Functions ***
function Defpred() {
  global $mnux, $pref, $token;
  TampilkanPilihanProdi($mnux, '', $pref, "pred");
  //TampilkanPilihanProdi($mnux, '', $pref, "MK");
  if (!empty($_SESSION['prodi'])) {
    //TampilkanMenuPred();
    TampilkanPred();
  }
}
function TampilkanPred() {
  global $mnux, $pref, $token;
  PredDelScript();
  // Data
  $s = "select *
    from predikat
    where ProdiID='$_SESSION[prodi]' and KodeID='$_SESSION[KodeID]'
    order by IPKMin desc";
  $r = _query($s);

  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
    <tr><td class=ul1 colspan=7>
        <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&md=1&sub=PredEdt'>Tambah Predikat</a> &#8718;
        <a href='?mnux=$_SESSION[mnux]&mk=pred&sub='>Refresh</a>
        </td></tr>
    <tr>
        <th class=ttl colspan=2 width=20>#</th>
        <th class=ttl width=60>IPK Min</th>
        <th class=ttl width=60>IPK Max</th>
        <th class=ttl width=200>Predikat</th>
        <th class=ttl>Keterangan</th>
        <th class=ttl title='Hapus' width=10>Del</th>
    </tr>";
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
      <td class=inp width=16>$n</td>
      <td class=ul1 align=center width=10>
        <a href='#' onClick='javascript:window.location=\"?mnux=$_SESSION[mnux]&mk=pred&sub=PredEdt&PredikatID=$w[PredikatID]\"'><img src='img/edit.png' /></a>
        </td>
      <td class=ul align=right>$w[IPKMin]</td>
      <td class=ul align=right>$w[IPKMax]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[Keterangan]&nbsp;</td>
      <td class=ul1 align=center>
        <a href='#' onClick='javascript:PredDel($w[PredikatID])'><img src='img/del.gif' /></a>
        </td>
      </tr>";
  }
  echo "</table></p>";
}
function PredDelScript() {
  echo "<script>
  function PredDel(id) {
    if (confirm('Benar Anda mau menghapus data ini?')) {
      window.location = '?mnux=$_SESSION[mnux]&mk=pred&sub=PredDel&BypassMenu=1&PredikatID='+id;
    }
  }
  </script>";
}
function PredEdt() {
  global $mnux, $pref, $token;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $pred = GetFields('predikat', "PredikatID", $_REQUEST['PredikatID'], "*");
    $jdl = "Edit Predikat";
  }
  else {
    $pred = array();
    $pred['PredikatID'] = 0;
    $pred['KodeID'] = $_SESSION['KodeID'];
    $pred['ProdiID'] = $_SESSION['prodi'];
    $pred['Nama'] = '';
    $pred['IPKMin'] = 0;
    $pred['IPKMax'] = 0;
    $pred['Keterangan'] = '';
    $pred['NA'] = 'N';
    $pred['Script'] = '';
    $jdl = "Tambah Predikat";
  }
  $na = ($pred['NA'] == 'Y')? 'checked' : '';
  $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)",
    "ProdiID", $pred['ProdiID'], '', "ProdiID");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='sub' value='PredSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='PredikatID' value='$pred[PredikatID]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Predikat:</td>
      <td class=ul><input type=text name='Nama' value='$pred[Nama]' size=40 maxlength=50></td>
      </tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul><select name='prodi'>$optprd</select></td>
      </tr>
  <tr><td class=inp>Mulai IPK:</td>
      <td class=ul><input type=text name='IPKMin' value='$pred[IPKMin]' size=5 maxlength=5></td>
      </tr>
  <tr><td class=inp>Sampai IPK:</td>
      <td class=ul><input type=text name='IPKMax' value='$pred[IPKMax]' size=5 maxlength=5></td>
      </tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul><textarea name='Keterangan' cols=40 rows=3>$pred[Keterangan]</textarea></td>
      </tr>
  <tr><td class=inp>Tidak Aktif? (NA)</td>
      <td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]'\"></td></tr>
  </form></table></p>";
}
function PredSav() {
  $md = $_REQUEST['md']+0;
  $PredikatID = $_REQUEST['PredikatID'];
  $prodi = $_REQUEST['prodi'];
  $Nama = sqling($_REQUEST['Nama']);
  $IPKMin = $_REQUEST['IPKMin'];
  $IPKMax = $_REQUEST['IPKMax'];
  $Keterangan = sqling($_REQUEST['Keterangan']);
  // Simpan
  if ($md == 0) {
    $s = "update predikat 
      set Nama='$Nama', 
          ProdiID='$prodi',
          IPKMin='$IPKMin', 
          IPKMax='$IPKMax', 
          Keterangan='$Keterangan',
          LoginEdit='$_SESSION[_Login]', TglEdit=now()
      where PredikatID='$PredikatID' ";
    $r = _query($s);
  }
  else {
    $s = "insert into predikat 
      (Nama, ProdiID, KodeID,
      IPKMin, IPKMax, Keterangan,
      LoginBuat, TglBuat)
      values 
      ('$Nama', '$prodi', '$_SESSION[KodeID]',
      '$IPKMin', '$IPKMax', '$Keterangan',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=pred&sub=", 100);
}

function PredDel() {
  $PredikatID = $_REQUEST['PredikatID'];
  $s = "delete from predikat where PredikatID = '$PredikatID' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=pred&sub=", 100);
}
?>
