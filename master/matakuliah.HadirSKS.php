<?php

function DefHadirSKS() {
  global $mnux, $pref, $token;
  TampilkanPilihanProdi($mnux, '', $pref, "HadirSKS");
  if (!empty($_SESSION['prodi'])) {
    TampilkanHadirSKS();
  }
}
function TampilkanmenuHadirSKS() {
  global $mnux, $pref, $token;
  echo "<p> |
	</p>";
  //<a href='?mnux=$mnux&$pref=$token&sub=CetakHadirSKS'>Cetak</a></p>";
}
function TampilkanHadirSKS() {
  global $mnux, $pref, $token;
  HadirSKSDelScript();
  $s = "select *
    from hadirsks
    where ProdiID = '$_SESSION[prodi]'
      and KodeID = '".KodeID."'
      and NA = 'N'
    order by SKS ASC";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1>
    <tr><td class=ul1 colspan=5>
        <a href='?mnux=$mnux&$pref=$token&sub=HadirSKSEdt&md=1'>Tambah Batas SKS</a> |
        <a href='?mnux=$mnux&mk=HadirSKS&sub='>Refresh</a>
        </td></tr>
    <tr><th class=ttl colspan=2>#</th>
	<th class=ttl width=80>SKS</th>
	<th class=ttl width=80>Default Kehadiran</th>
	<th class=ttl width=80>Default Max Absen</th>
	<th class=ttl width=20 title='Delete'>Del</th>
	</tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
      <td class=inp width=10>$n</td>
      <td class=ul align=center width=10><a href='?mnux=$mnux&$pref=$token&sub=HadirSKSEdt&md=0&HadirSKSID=$w[HadirSKSID]'><img src='img/edit.png'></a></td>
	  <td class=ul align=right>$w[SKS]</td>
	  <td class=ul align=right>$w[DefKehadiran]</td>
	  <td class=ul align=right>$w[DefMaxAbsen]</td>
	  <td class=ul1 align=center>
	    <a href='#' onClick='javascript:HadirSKSDel($w[HadirSKSID])'><img src='img/del.gif' /></a>
	  </td>
	</tr>";
  }
  echo "</table></p>";
}
function HadirSKSDelScript() {
  echo <<<ED
  <script>
  function HadirSKSDel(id) {
    if (confirm('Benar Anda akan menghapus data ini?')) {
      window.location = '?mnux=$_SESSION[mnux]&mk=HadirSKS&sub=HadirSKSDel&hadirsksid='+id;
    }
  }
  </script>
ED;
}
function HadirSKSDel() {
  $hadirsksid = $_REQUEST['hadirsksid'];
  $s = "delete from hadirsks where HadirSKSID = '$hadirsksid' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=HadirSKS&sub=", 10);
}
function HadirSKSEdt() {
  global $mnux, $pref, $token;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $HadirSKSID = $_REQUEST['HadirSKSID'];
	$w = GetFields('hadirsks', 'HadirSKSID', $HadirSKSID, '*');
	$jdl = "Edit Default Kehadiran SKS";
  }
  else {
    $w['HadirSKSID'] = 0;
	$w['DefKehadiran'] = 0;
	$w['DefMaxAbsen'] = 0;
	$w['SKS'] = 0;
	$jdl = "Tambah Default Kehadiran SKS";
  }
  $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  // Tampilkan
  CheckFormScript("prodi,DefKehadiran,DefMaxAbsen,SKS");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='HadirSKS' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$token'>
  <input type=hidden name='sub' value='HadirSKSSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='HadirSKSID' value='$w[HadirSKSID]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Program Studi</td>
      <td class=ul><select name='prodi'>$optprd</select></td></tr>
  <tr><td class=inp>SKS</td>
      <td class=ul><input type=text name='SKS' value='$w[SKS]' size=3 maxlength=3></td></tr>
  <tr><td class=inp>Default Kehadiran</td<br>
      <td class=ul><input type=text name='DefKehadiran' value='$w[DefKehadiran]' size=5 maxlength=5></td></tr<br>
  <tr><td class=inp>Default Maksimum Absen</td<br>
      <td class=ul><input type=text name='DefMaxAbsen' value='$w[DefMaxAbsen]' size=5 maxlength=5></td></tr<br>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=$token'\"></td>
      </tr>
  </form>
  </table></p>";
}
function HadirSKSSav() {
  $md = $_REQUEST['md']+0;
  $HadirSKSID = $_REQUEST['HadirSKSID']+0;
  $DefKehadiran = $_REQUEST['DefKehadiran']+0;
  $DefMaxAbsen = $_REQUEST['DefMaxAbsen']+0;
  $SKS = $_REQUEST['SKS']+0;
  $ProdiID = $_REQUEST['prodi'];
  if ($md == 0) {
    $s = "update hadirsks 
      set DefKehadiran='$DefKehadiran', 
		  DefMaxAbsen='$DefMaxAbsen', 
          SKS='$SKS',
          LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
      where HadirSKSID = $HadirSKSID ";
    $r = _query($s);
  }
  else {
    $s = "insert into hadirsks 
      (KodeID, ProdiID, DefKehadiran, DefMaxAbsen, SKS,
      LoginBuat, TanggalBuat)
      values 
      ('".KodeID."', '$ProdiID', '$DefKehadiran', '$DefMaxAbsen', '$SKS',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=HadirSKS&sub=", 100);
}
function CetakHadirSKS() {
  if (!empty($_SESSION['prodi'])) CetakHadirSKS1();
  else echo ErrorMsg("Tidak Dapat Mencetak",
    "Tidak dapat mencetak karena Program Studi belum diset");
}
function CetakHadirSKS1() {
  global $_lf;
  $mxc = 80;
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $grs = str_pad('-', $mxc, '-').$_lf;
  $prd = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $f = fopen($nmf, 'w');
  $hdr = "Master Default Kehadiran SKS \r\nProgram Studi: $prd ($_SESSION[prodi])\r\n\r\n$grs".
    "No.   ".
    str_pad('Dari IP', 10, ' ', STR_PAD_LEFT).
    str_pad("Smp IP", 10, ' ', STR_PAD_LEFT).
    str_pad("HadirSKS", 10, ' ', STR_PAD_LEFT).
    $_lf.$grs;
  fwrite($f, $hdr);
  $s = "select *
    from hadirsks
    where ProdiID='$_SESSION[prodi]' and NA='N'
    order by DariIP desc";
  $r = _query($s); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    fwrite($f, str_pad($n, 6). 
      str_pad($w['DariIP'], 10, ' ', STR_PAD_LEFT).
      str_pad($w['SampaiIP'], 10, ' ', STR_PAD_LEFT).
      str_pad($w['SKS'], 10, ' ', STR_PAD_LEFT).
      $_lf);
  }
  fwrite($f, $grs);
  fclose($f);
  TampilkanFileDWOPRN($nmf, "matakuliah");
}

?>
