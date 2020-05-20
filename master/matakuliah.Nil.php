<?php
function DefNil() {
  global $mnux, $pref, $token;
  TampilkanPilihanProdi($mnux, '', $pref, "Nil");
  if (!empty($_SESSION['prodi'])) {
    TampilkanNil();
  }
}
function TampilkanMenuNil() {
  global $mnux, $pref;
  echo "<p> |
  <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=CetakNilai'>Cetak</a></p>";
}

function SKSDefSav() {
  $DefSKS = $_REQUEST['DefSKS']+0;
  $s = "update prodi set DefSKS='$DefSKS' where KodeID = '".KodeID."' and ProdiID='$_REQUEST[prodi]'";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=Nil&sub=", 100);
}
function TampilkanNil() {
  global $mnux, $pref;

  $prd = GetFields('prodi', "KodeID='".KodeID."' and ProdiID", $_SESSION['prodi'], '*');
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>";
  echo "<form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='mk' value='Nil'>
  <input type=hidden name='prodi' value='$_SESSION[prodi]'>
  <input type=hidden name='sub' value='SKSDefSav'>
  <input type=hidden name='BypassMenu' value='1' />";
  echo "<tr>
    <td class=ul1 colspan=6>
    <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&md=1&sub=NilEdt'>Tambah Nilai</a> |
    <a href='?mnux=$_SESSION[mnux]&mk=Nil&sub='>Refresh</a> |
    Default SKS: <input type=text name='DefSKS' value='$prd[DefSKS]' size=3 maxlength=3>
    <input type=submit name='Simpan' value='Simpan'>
    </td></tr>";
  echo "</form>";
  echo "<tr>
    <th class=ttl>Nilai</th>
    <th class=ttl>Bobot</th>
    <th class=ttl>Lulus?</th>
    <th class=ttl>Batas<br>Bawah</th>
    <th class=ttl>Batas<br>Atas</th>
    <th class=ttl>Hitung<br />dlm IPK</th>
    <th class=ttl>Deskripsi</th>
    </tr>";

  // Ambil datanya
  $s = "select *
    from nilai where KodeID='$_SESSION[KodeID]' and ProdiID='$_SESSION[prodi]'
    order by Bobot Desc";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
    <td $c width=50>
    <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&md=0&NID=$w[NilaiID]&sub=NilEdt'><img src='img/edit.png' border=0>
    $w[Nama]</a></td>
    <td $c align=right width=60>$w[Bobot]</td>
    <td $c align=center width=60><img src='img/$w[Lulus].gif'></td>
    <td $c align=right width=60>$w[NilaiMin]</td>
    <td $c align=right width=60>$w[NilaiMax]</td>
    <td $c align=center width=60><img src='img/$w[HitungIPK].gif'></td>
    <td $c>$w[Deskripsi]&nbsp;</td>
    </tr>";
  }
  echo "</table></p>";
}
function NilEdt() {
  global $mnux, $pref;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('nilai', "NilaiID", $_REQUEST['NID'], '*');
    $jdl = "Edit Nilai";
  }
  else {
    $w = array();
    $w['NilaiID'] = 0;
    $w['KodeID'] = $_SESSION['KodeID'];
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['Nama'] = '';
    $w['Bobot'] = 0;
    $w['Lulus'] = 'N';
    $w['NilaiMin'] = 0;
    $w['NilaiMax'] = 0;
    $w['MaxSKS'] = 0;
    $w['HitungIPK'] = 'N';
    $w['Deskripsi'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Nilai";
  }
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $_Lulus = ($w['Lulus'] == 'Y')? 'checked' : '';
  $_HitungIPK = ($w['HitungIPK'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  // Tampilkan formulir
  CheckFormScript("Nama,Bobot,NilaiMin,NilaiMax");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='sub' value='NilSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='NilaiID' value='$w[NilaiID]'>
  <input type=hidden name='KodeID' value='$w[KodeID]'>
  <input type=hidden name='ProdiID' value='$w[ProdiID]'>
  <input type=hidden name='BypassMenu' value='1' />

  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Nilai</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Bobot</td><td class=ul><input type=text name='Bobot' value='$w[Bobot]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Lulus?</td><td class=ul><input type=checkbox name='Lulus' value='Y' $_Lulus></td></tr>
  <tr><td class=inp>Batas Bawah</td><td class=ul><input type=text name='NilaiMin' value='$w[NilaiMin]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Batas Atas</td><td class=ul><input type=text name='NilaiMax' value='$w[NilaiMax]' size=5 maxlength=5></td></tr>
  <!--
  <tr><td class=inp>Max Pengambilan SKS</td><td class=ul><input type=text name='MaxSKS' value='$w[MaxSKS]' size=4 maxlength=3></td></tr>
  -->
  <tr><td class=inp>Hitung dlm IPK?</td><td class=ul><input type=checkbox name='HitungIPK' value='Y' $_HitungIPK></td></tr>
  <tr><td class=inp>Deskripsi</td><td class=ul><input type=text name='Deskripsi' value='$w[Deskripsi]' size=40 maxlength=200></td></tr>
  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]&$snm=$sid'\"></td></tr>

  </form></table></p>";
}
function NilSav() {
  $md = $_REQUEST['md']+0;
  $Nama = strtoupper(sqling($_REQUEST['Nama']));
  $Bobot = $_REQUEST['Bobot'];
  $Lulus = (empty($_REQUEST['Lulus']))? 'N' : $_REQUEST['Lulus'];
  $HitungIPK = (empty($_REQUEST['HitungIPK']))? 'N' : $_REQUEST['HitungIPK'];
  $NilaiMin = $_REQUEST['NilaiMin'];
  $NilaiMax = $_REQUEST['NilaiMax'];
  //$MaxSKS = $_REQUEST['MaxSKS']+0;
  $Deskripsi = sqling($_REQUEST['Deskripsi']);
  if ($md == 0) {
    $s = "update 
      nilai set Nama='$Nama', Bobot='$Bobot', Lulus='$Lulus', NilaiMin='$NilaiMin', NilaiMax='$NilaiMax',
      HitungIPK='$HitungIPK',
      Deskripsi='$Deskripsi', TglEdit=now(), LoginEdit='$_SESSION[_Login]'
      where NilaiID='$_REQUEST[NilaiID]' ";
  }
  else {
    $s = "insert into nilai (KodeID, ProdiID,
      Nama, Bobot, Lulus, NilaiMin, NilaiMax, HitungIPK,
      Deskripsi, TglBuat, LoginBuat)
      values ('$_SESSION[KodeID]', '$_SESSION[prodi]',
      '$Nama', '$Bobot', '$Lulus', '$NilaiMin', '$NilaiMax', '$HitungIPK',
      '$Deskripsi', now(), '$_SESSION[_Login]')";
  }
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=Nil&sub=", 100);
}
function CetakNilai() {
  if (!empty($_SESSION['prodi'])) CetakNilai1();
  else echo ErrorMsg("Tidak Dapat Mencetak",
    "Tidak dapat mencetak karena Program Studi belum diset");
}
function CetakNilai1() {
  global $_lf;
  $mxc = 80;
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $grs = str_pad('-', $mxc, '-').$_lf;
  $prd = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $f = fopen($nmf, 'w');
  $hdr = "Master Nilai \r\nProgram Studi: $prd ($_SESSION[prodi])\r\n\r\n$grs".
    "No.   ".
    str_pad('Nilai', 6).
    str_pad("Bobot", 10, ' ', STR_PAD_LEFT).
    str_pad("Dari", 10, ' ', STR_PAD_LEFT).
    str_pad("Sampai", 10, ' ', STR_PAD_LEFT).
    "   Lulus?  Hitung?".
    $_lf.$grs;
  fwrite($f, $hdr);
  $s = "select *
    from nilai
    where ProdiID='$_SESSION[prodi]' and NA='N'
    order by Bobot desc";
  $r = _query($s); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $l = ($w['Lulus'] == 'Y')? '     *' : '     -';
    $H = ($w['HitungIPK'] == 'Y')? '        *' : '        -';
    fwrite($f, str_pad($n, 6). 
      str_pad($w['Nama'], 6).
      str_pad($w['Bobot'], 10, ' ', STR_PAD_LEFT).
      str_pad($w['NilaiMin'], 10, ' ', STR_PAD_LEFT).
      str_pad($w['NilaiMax'], 10, ' ', STR_PAD_LEFT).
      $l. $H.
      $_lf);
  }
  fwrite($f, $grs);
  fclose($f);
  TampilkanFileDWOPRN($nmf, "matakuliah");
}
?>
