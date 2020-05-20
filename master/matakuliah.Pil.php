<?php

// *** Functions ***
function DefPil() {
  global $mnux, $pref, $token;
  TampilkanPilihanProdi($mnux, '', $pref, "Pil");
  if (!empty($_SESSION['prodi'])) {
    TampilkanPilihanWajib();
  }
}

function TampilkanPilihanWajib() {
  global $mnux, $pref;
  $s = "select jp.*
    from jenispilihan jp
    where jp.KodeID='$_SESSION[KodeID]' and jp.ProdiID='$_SESSION[prodi]'
    order by jp.Singkatan";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=500>
    <tr>
      <td class=ul1 colspan=5>
      <a href='?mnux=$mnux&$pref=Pil&sub=PilEdt&md=1'>Tambah Jenis Pilihan Wajib</a> 
      <!--|
      <a href='?mnux=$mnux&$pref=Pil&sub=CetakPilihanWajib'>Cetak</a> -->
      </td>
      </tr>
    <tr><th class=ttl>Kode</th><th class=ttl>Nama</th>
    <th class=ttl title='Pilihan Wajib? Tugas Akhir?'>TA?</th>
    <th class=ttl title='Tidak Aktif?'>NA</th></tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
      <td class=ul width=100><a href='?mnux=$mnux&$pref=Pil&sub=PilEdt&md=0&JenisPilihanID=$w[JenisPilihanID]'><img src='img/edit.png'>
      $w[Singkatan]</a></td>
      <td $c>$w[Nama]</td>
      <td $c width=30 align=center><img src='img/$w[TA].gif'></td>
      <td $c width=30 align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function PilEdt() {
  global $mnux, $pref;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $JenisPilihanID = $_REQUEST['JenisPilihanID']+0;
    $w = GetFields('jenispilihan', "JenisPilihanID", $JenisPilihanID, '*');
    $jdl = "Edit Jenis Pilihan Wajib";
  }
  else {
    $w = array();
    $w['JenisPilihanID'] = 0;
    $w['KodeID'] = $_SESSION['KodeID'];
    $w['Singkatan'] = '';
    $w['Nama'] = '';
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['TA'] = 'N';
    $w['NA'] = 'N';
    $jdl = "Tambah Jenis Pilihan Wajib";
  }
  $ta = ($w['TA'] == 'Y')? 'checked' : '';
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  // Tampilkan
  CheckFormScript("Singkatan,Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='Pil'>
  <input type=hidden name='sub' value='PilSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='JenisPilihanID' value='$w[JenisPilihanID]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Kode</td><td class=ul><input type=text name='Singkatan' value='$w[Singkatan]' size=5 maxlength=10></td></tr>
  <tr><td class=inp>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tugas Akhir/Skripsi/Tesis/Disertasi?</td><td class=ul><input type=checkbox name='TA' value='Y' $ta><br />
    Digunakan untuk pengecekan pendaftaran skripsi & wisuda</td></tr>
  <tr><td class=inp>Tidak Aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  
  <tr><td class=ul colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=Pil'\"></td></tr>
  </table></p>";
}
function PilSav() {
  $md = $_REQUEST['md']+0;
  $Singkatan = sqling($_REQUEST['Singkatan']);
  $Nama = sqling($_REQUEST['Nama']);
  $TA = (empty($_REQUEST['TA']))? 'N' : $_REQUEST['TA'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $JenisPilihanID = $_REQUEST['JenisPilihanID'];
    $s = "update jenispilihan set Singkatan='$Singkatan', Nama='$Nama', TA='$TA', NA='$NA'
      where JenisPilihanID='$JenisPilihanID'";
    $r = _query($s);
  }
  else {
    $s = "insert into jenispilihan (ProdiID, KodeID, Singkatan, Nama, TA, NA)
      values ('$_SESSION[prodi]', '$_SESSION[KodeID]', '$Singkatan', '$Nama', '$TA', '$NA')";
    $r = _query($s);
  }

  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=Pil", 100);
}
function CetakPilihanWajib() {
  $kur = GetFields("kurikulum", "NA='N' and ProdiID", $_SESSION['prodi'], '*');
  if (!empty($kur)) CetakPilihanWajib1($kur);
  else Echo ErrorMsg("Tidak Dapat Mencetak",
    "Tidak ada kurikulum yang aktif untuk prodi: <b>$_SESSION[prodi]</b> ini.");
}
function CetakPilihanWajib1($kur) {
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
  $s = "select MKKode, Nama, Sesi, SKS, Wajib, JenisPilihanID
    from mk
    where mk.KurikulumID=$kur[KurikulumID] and mk.NA='N'
    order by mk.JenisPilihanID, mk.Sesi, mk.MKKode";
  $r = _query($s); $n = 0; $brs = 0; $jen = "xhajskhdasiuhdlfkas hdlkfajhsdl faksjdh falksdjhf laksjdhf lkasdhf";
  while ($w = _fetch_array($r)) {
    if ($jen != $w['JenisPilihanID']) {
      $brs += 3;
      $jen = $w['JenisPilihanID'];
      $_k = GetaField('jenispilihan', 'JenisPilihanID', $w['JenisPilihanID'], 'Nama');
      fwrite($f, "\r\nJenis Pilihan: $_k \r\n");
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
