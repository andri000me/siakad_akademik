<?php
// Author: Emanuel Setio Dewo, 28 Jan 2006

function DefKons() {
  global $mnux, $pref, $token;
  TampilkanPilihanProdi($mnux, '', $pref, "Kons");
  if (!empty($_SESSION['prodi'])) {
    TampilkanKons();
  }
}

function TampilkanKons() {
  global $mnux, $pref, $token, $arrID;
  $s = "select * from konsentrasi
    where KodeID='$arrID[Kode]' and ProdiID='$_SESSION[prodi]'
    order by KonsentrasiKode";
  $r = _query($s);
  $n = 0;
  echo "<table class=box cellspacing=1 cellpadding=4 width=600>";
  echo "<tr>
    <td class=ul1 colspan=5>
    <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=KonsEdt&md=1'>Tambah Konsentrasi</a>
    <!-- 
    |
    <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=CetakKons'>Cetak</a>
    -->
    </td>
    </tr>";
  echo "<tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Keterangan</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td $c>$n</td>
    <td $c nowrap>
    <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=KonsEdt&konsid=$w[KonsentrasiID]&md=0'><img src='img/edit.png' border=0>
      $w[KonsentrasiKode]</td>
    <td $c nowrap>$w[Nama]&nbsp;</td>
    <td $c>$w[Keterangan]&nbsp;</td>
    <td $c align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table>";
}
function KonsEdt() {
  global $mnux, $pref, $arrID;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('konsentrasi', "KonsentrasiID", $_REQUEST['konsid'], '*');
    $jdl = "Edit Konsentrasi";
  }
  else {
    $w = array();
    $w['KonsentrasiID'] = 0;
    $w['KonsentrasiKode'] = '';
    $w['Nama'] = '';
    $w['KodeID'] = $arrID['Kode'];
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['Keterangan'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Konsentrasi";
  }
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $snm = session_name();
  $sid = session_id();
  // Tampilkan form
  CheckFormScript("KonsentrasiKode,Nama");
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='sub' value='KonsSav'>
  <input type=hidden name='BypassMenu' value='1' />
  <input type=hidden name='KodeID' value='$w[KodeID]'>
  <input type=hidden name='prodi' value='$w[ProdiID]'>
  <input type=hidden name='KonsentrasiID' value='$w[KonsentrasiID]'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Kode Konsentrasi</td><td class=ul><input type=text name='KonsentrasiKode' value='$w[KonsentrasiKode]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Keterangan</td><td class=ul><textarea name='Keterangan' cols=30 rows=4>$w[Keterangan]</textarea></td></tr>
  <tr><td class=inp>Tidak Aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $_na></td></tr>
  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]&$snm=$sid'\"></td></tr>
  </form></table>";
}
function KonsSav() {
  $md = $_REQUEST['md'];
  $KonsentrasiKode = strtoupper(sqling($_REQUEST['KonsentrasiKode']));
  $Nama = sqling($_REQUEST['Nama']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update konsentrasi set KonsentrasiKode='$KonsentrasiKode',
      Nama='$Nama', Keterangan='$Keterangan', NA='$NA'
      where KonsentrasiID='$_REQUEST[KonsentrasiID]' ";
  }
  else {
    $s = "insert into konsentrasi (KonsentrasiKode, KodeID, ProdiID, Nama, Keterangan, NA)
      values('$KonsentrasiKode', '$_REQUEST[KodeID]', '$_REQUEST[prodi]',
      '$Nama', '$Keterangan', '$NA')";
  }
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=Kons", 100);
}
function CetakKons() {
  $kur = GetFields("kurikulum", "NA='N' and ProdiID", $_SESSION['prodi'], '*');
  if (!empty($kur)) CetakKons1($kur);
  else Echo ErrorMsg("Tidak Dapat Mencetak",
    "Tidak ada kurikulum yang aktif untuk prodi: <b>$_SESSION[prodi]</b> ini.");
}
function CetakKons1($kur) {
  global $_lf;
  $mxc = 80;
  $mxb = 50;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $prd = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $hdr = str_pad("Daftar Matakuliah $prd ($_SESSION[prodi])", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Kurikulum: $kur[KurikulumKode], $kur[Nama]", $mxc, ' ', STR_PAD_BOTH).$_lf.$grs.
    str_pad('No.', 6). str_pad('Kode', 10). str_pad('Nama', 45).
    str_pad('SKS', 5). str_pad('Wajib', 5).$_lf.$grs;
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, $hdr);
  $s = "select MKKode, Nama, Sesi, SKS, Wajib, KonsentrasiID
    from mk
    where mk.KurikulumID=$kur[KurikulumID] and mk.NA='N'
    order by mk.KonsentrasiID, mk.Sesi, mk.MKKode";
  $r = _query($s); $n = 0; $brs = 0; $kon = "xhajskhdasiuhdlfkas hdlkfajhsdl faksjdh falksdjhf laksjdhf lkasdhf";
  while ($w = _fetch_array($r)) {
    if ($kons != $w['KonsentrasiID']) {
      $brs += 3;
      $kons = $w['KonsentrasiID'];
      $_k = GetaField('konsentrasi', 'KonsentrasiID', $w['KonsentrasiID'], 'Nama');
      fwrite($f, "\r\nKonsentrasi: $_k \r\n$grs");
    }
    if ($brs >= $mxb) {
      $brs = 0;
      fwrite($f, chr(12));
      fwrite($f, $hdr);
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
