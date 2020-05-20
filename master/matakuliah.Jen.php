<?php

function TampilkanMenuJen() {
  global $mnux, $pref;
  echo "<p></p>";
}
function DefJen() {
  global $mnux, $pref, $token;
  TampilkanPilihanProdi($mnux, '', $pref, "Jen");
  if (!empty($_SESSION['prodi'])) {
    TampilkanMenuJen();
    TampilkanJenisMK();
  }
}
function TampilkanJenisMK() {
  global $mnux, $pref, $arrID;
  $s = "select *
    from jenismk
    where ProdiID='$_SESSION[prodi]' and KodeID='$arrID[Kode]'
    order by Urutan";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>";
  echo "<tr>
    <td class=ul1 colspan=4>
    <a href='?mnux=$mnux&$pref=Jen&sub=JenAdd&md=1'>Tambah Jenis Mata Kuliah</a>
    <!-- |
    <a href='?mnux=$mnux&$pref=Jen&sub=CetakJenisMK'>Cetak</a> -->
    </td></tr>";
  echo "<tr>
    <th class=ttl>Urutan</th>
    <th class=ttl>Singkatan</th>
    <th class=ttl>Jenis Matakuliah</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
    <td $c width=40 align=right>$w[Urutan]</td>
    <td $c width=100>
      <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=JenAdd&md=0&jmkid=$w[JenisMKID]'><img src='img/edit.png' border=0>
      $w[Singkatan]</a></td>
    <td $c>$w[Nama]</td>
    <td $c align=center width=10><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function JenAdd() {
  global $arrID, $mnux, $pref;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('jenismk', "JenisMKID", $_REQUEST['jmkid'], '*');
    $jdl = "Edit Jenis Mata Kuliah";
  }
  else {
    $w = array();
    $w['JenisMKID'] = 0;
    $w['KodeID'] = $arrID['Kode'];
    $w['Urutan'] = 0;
    $w['Singkatan'] = '';
    $w['Nama'] = '';
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['NA'] = 'N';
    $jdl = "Tambah Jenis Mata Kuliah";
  }
  $snm = session_name(); $sid = session_id();
  $_NA = ($w['NA'] == 'Y')? 'checked' : '';
  $_prd = GetProdiUser($_SESSION['_Login'], $_SESSION['prodi']);
  // tampilkan formulir
  CheckFormScript("Nama,prodi");
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='sub' value='JenSav'>
  <input type=hidden name='KodeID' value='$arrID[Kode]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='JenisMKID' value='$w[JenisMKID]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Urutan Tampilan</td><td class=ul><input type=text name='Urutan' value='$w[Urutan]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Singkatan</td><td class=ul><input type=text name='Singkatan' value='$w[Singkatan]' size=30 maxlength=20></td></tr>
  <tr><td class=inp>Jenis Matakuliah</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi'>$_prd</select></td></tr>
  <tr><td class=inp>Tidak aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $_NA></td></tr>
  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]'\"></td></tr>
  </form></table>";
}
function JenSav() {
  $md = $_REQUEST['md']+0;
  $Urutan = $_REQUEST['Urutan']+0;
  $JenisMKID = $_REQUEST['JenisMKID'];
  $Singkatan = $_REQUEST['Singkatan'];
  $Nama = sqling($_REQUEST['Nama']);
  $KodeID = $_REQUEST['KodeID'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  // Simpan
  if ($md == 0) {
    $s = "update jenismk set Urutan='$Urutan', Singkatan='$Singkatan', Nama='$Nama', ProdiID='$_REQUEST[prodi]', NA='$NA' where JenisMKID='$JenisMKID' ";
  }
  else {
    $s = "insert into jenismk (Urutan, Singkatan, Nama, KodeID, ProdiID, NA)
      values('$Urutan', '$Singkatan', '$Nama', '$KodeID', '$_REQUEST[prodi]', '$NA')";
  }
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=Jen", 100);
}
function CetakJenisMK() {
  $kur = GetFields("kurikulum", "NA='N' and ProdiID", $_SESSION['prodi'], '*');
  if (!empty($kur)) CetakJenisMK1($kur);
  else Echo ErrorMsg("Tidak Dapat Mencetak",
    "Tidak ada kurikulum yang aktif untuk prodi: <b>$_SESSION[prodi]</b> ini.");
}
function CetakJenisMK1($kur) {
  global $_lf;
  $mxc = 80;
  $mxb = 50;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $prd = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $hdr = str_pad("Daftar Matakuliah $prd ($_SESSION[prodi])", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Kurikulum: $kur[KurikulumKode], $kur[Nama]", $mxc, ' ', STR_PAD_BOTH).$_lf;
  $hdr1 = $grs.
    str_pad('No.', 6). str_pad('Kode', 10). str_pad('Nama', 45).
    str_pad('SKS', 5). str_pad('Wajib', 5).$_lf.$grs;
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, $hdr);
  $s = "select MKKode, Nama, Sesi, SKS, Wajib, JenisMKID
    from mk
    where mk.KurikulumID=$kur[KurikulumID] and mk.NA='N'
    order by mk.JenisMKID, mk.Sesi, mk.MKKode";
  $r = _query($s); $n = 0; $brs = 0; $jen = "xhajskhdasiuhdlfkas hdlkfajhsdl faksjdh falksdjhf laksjdhf lkasdhf";
  while ($w = _fetch_array($r)) {
    if ($jen != $w['JenisMKID']) {
      $brs += 3;
      $jen = $w['JenisMKID'];
      $_k = GetaField('jenismk', 'JenisMKID', $w['JenisMKID'], 'Nama');
      fwrite($f, "\r\nJenis Matakuliah: $_k \r\n$hdr1");
    }
    if ($brs >= $mxb) {
      $brs = 0;
      fwrite($f, chr(12));
      fwrite($f, $hdr.$hdr1);
    }
    $brs++;
    $n++;
    $wjb = ($w['Wajib'] == 'Y')? '*' : '';
    fwrite($f, str_pad($n, 6).
      str_pad($w['MKKode'], 10).
      str_pad($w['Nama'], 45).
      str_pad($w['SKS'], 5).
      str_pad($wjb, 5).
      $_lf); 
  }
  fwrite($f, $grs);
  fclose($f);
  TampilkanFileDWOPRN($nmf, "matakuliah");
}
?>
