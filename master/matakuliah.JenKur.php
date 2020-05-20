<?php
// Author: Emanuel Setio Dewo, 27 Jan 2006

// *** Functions ***
function DefJenKur() {
  global $mnux, $pref, $token;
  TampilkanPilihanProdi($mnux, '', $pref, "Pil");
  if (!empty($_SESSION['prodi'])) {
    TampilkanJenisKurikulum();
  }
}

function TampilkanJenisKurikulum() {
  global $mnux, $pref;
  $s = "select jp.*
    from jeniskurikulum jp
    where jp.KodeID='$_SESSION[KodeID]' and jp.ProdiID='$_SESSION[prodi]'
    order by jp.Singkatan";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=500>
    <tr>
      <td class=ul1 colspan=5>
      <a href='?mnux=$mnux&$pref=JenKur&sub=JenKurEdt&md=1'>Tambah Jenis Kurikulum</a> <!-- |
      <a href='?mnux=$mnux&$pref=JenKur&sub=CetakJenKur'>Cetak</a> -->
      </td>
      </tr>
    <tr><th class=ttl>Kode</th><th class=ttl>Nama</th><th class=ttl title='Tidak Aktif?'>NA</th></tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
      <td class=ul width=100>
        <a href='?mnux=$mnux&$pref=JenKur&sub=JenKurEdt&md=0&JenisKurikulumID=$w[JenisKurikulumID]'><img src='img/edit.png'>
        $w[Singkatan]</a></td>
      <td $c>$w[Nama]</td>
      <td $c width=30 align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function JenKurEdt() {
  global $mnux, $pref;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $JenisKurikulumID = $_REQUEST['JenisKurikulumID']+0;
    $w = GetFields('jeniskurikulum', "JenisKurikulumID", $JenisKurikulumID, '*');
    $jdl = "Edit Jenis Kurikulum";
  }
  else {
    $w = array();
    $w['JenisKurikulumID'] = 0;
    $w['KodeID'] = $_SESSION['KodeID'];
    $w['Singkatan'] = '';
    $w['Nama'] = '';
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['NA'] = 'N';
    $jdl = "Tambah Jenis Kurikulum";
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  // Tampilkan
  CheckFormScript("Singkatan,Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='mk' value='JenKur'>
  <input type=hidden name='sub' value='JenKurSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='JenisKurikulumID' value='$w[JenisKurikulumID]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Kode</td><td class=ul><input type=text name='Singkatan' value='$w[Singkatan]' size=5 maxlength=10></td></tr>
  <tr><td class=inp>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tidak Aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  
  <tr><td class=ul colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=JenKur'\"></td>
    </tr>
  </table></p>";
}
function JenKurSav() {
  $md = $_REQUEST['md']+0;
  $Singkatan = sqling($_REQUEST['Singkatan']);
  $Nama = sqling($_REQUEST['Nama']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $JenisKurikulumID = $_REQUEST['JenisKurikulumID'];
    $s = "update jeniskurikulum set Singkatan='$Singkatan', Nama='$Nama', NA='$NA'
      where JenisKurikulumID='$JenisKurikulumID'";
    $r = _query($s);
  }
  else {
    $s = "insert into jeniskurikulum (ProdiID, KodeID, Singkatan, Nama, NA)
      values ('$_SESSION[prodi]', '$_SESSION[KodeID]', '$Singkatan', '$Nama', '$NA')";
    $r = _query($s);
  }

  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=JenKur", 100);
}
function CetakJenKur() {
  $kur = GetFields("kurikulum", "NA='N' and ProdiID", $_SESSION['prodi'], '*');
  if (!empty($kur)) CetakJenKur1($kur);
  else Echo ErrorMsg("Tidak Dapat Mencetak",
    "Tidak ada kurikulum yang aktif untuk prodi: <b>$_SESSION[prodi]</b> ini.");
}
function CetakJenKur1($kur) {
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
  fwrite($f, $hdr.$hdr1);
  $s = "select MKKode, Nama, Sesi, SKS, Wajib, JenisKurikulumID
    from mk
    where mk.KurikulumID=$kur[KurikulumID] and mk.NA='N'
    order by mk.JenisKurikulumID, mk.Sesi, mk.MKKode";
  $r = _query($s); $n = 0; $brs = 0; $jen = "xhajskhdasiuhdlfkas hdlkfajhsdl faksjdh falksdjhf laksjdhf lkasdhf";
  while ($w = _fetch_array($r)) {
    if ($jen != $w['JenisKurikulumID']) {
      $brs += 3;
      $jen = $w['JenisKurikulumID'];
      $_k = GetaField('jeniskurikulum', 'JenisKurikulumID', $w['JenisKurikulumID'], 'Nama');
      fwrite($f, "\r\nJenis Kurikulum: $_k \r\n");
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
