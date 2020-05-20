<?php
// Author: Emanuel Setio Dewo, setio_dewo@sisfokampus.net
// 2005-12-27

// *** Functions ***
function DftrRuang() {
  $opt = GetOption2('kampus', "concat(KampusID, ' - ', Nama)", 'KampusID', $_SESSION['kampusid'], '', 'KampusID');
  $colspan = 10;
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=850>
    <tr><form action='?' method=POST>
    <input type=hidden name='mnux' value='$_SESSION[mnux]'>
    <td colspan=$colspan class=ul>
      Kampus : <select name='kampusid' onChange='this.form.submit()'>$opt</select>
      <input type=button name='TambahRuang' value='Tambah Ruang'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=RuangEdt&md=1'\" />
      <input type=button name='MasterKampus' value='Master Kampus'
        onClick=\"location='?mnux=master/kampus'\" />
      </td>
    </form></tr>";
  /*
  echo "<tr>
    <td class=ul colspan=$colspan>
    <a href='cetak/ruang.cetak.php?KampusID=$_SESSION[kampusid]'>Cetak</a>
    </td></tr>";
  */
  echo "<tr><th class=ttl colspan=2>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
	<th class=ttl>Ruang<br />Induk</th>
    <th class=ttl>Rg.<br />Kelas?</th>
    <th class=ttl>Kapasitas</th>
    <th class=ttl>Untuk<br />USM?</th>
    <th class=ttl>Keterangan</th>
    <th class=ttl>NA</th>
    </tr>";
  $s = "select r.RuangID, r.RuangInduk, r.Nama, r.RuangKuliah, r.Kapasitas, r.KapasitasUjian,
    r.UntukUSM, r.NA, r.Keterangan
    from ruang r
    where r.KampusID = '$_SESSION[kampusid]'
    order by RuangID";
  $r = _query($s); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $ket = str_replace(chr(13), '<br />', $w['Keterangan']);
    echo "<tr>
      <td $c width=15>$n</td>
      <td $c width=10>
        <a href='?mnux=$_SESSION[mnux]&gos=RuangEdt&md=0&ruangid=$w[RuangID]'><img src='img/edit.png' border=0>
      <td $c width=80>$w[RuangID]</a></td>
      <td $c width=200>$w[Nama]</td>
	  <td $c>$w[RuangInduk]</td>
      <td $c align=center width=30><img src='img/$w[RuangKuliah].gif'></td>
      <td $c width=60 align=center>$w[KapasitasUjian] - $w[Kapasitas]</td>
      <td $c align=center width=40><img src='img/$w[UntukUSM].gif'></td>
      <td $c>$ket&nbsp;</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function RuangEdt() {
  $md = $_REQUEST['md'] +0;
  if ($md == 0) {
    $w = GetFields('ruang', 'RuangID', $_REQUEST['ruangid'], '*');
    $jdl = "Edit Ruang";
    $strid = "<input type=hidden name='RuangID' value='$w[RuangID]'><b>$w[RuangID]</b>";
  }
  else {
    $w = array();
    $w['RuangID'] = '';
    $w['Nama'] = '';
    $w['ProdiID'] = '';
    $w['KampusID'] = $_SESSION['kampusid'];
    $w['Lantai'] = 1;
	$w['RuangInduk'] = '';
    $w['RuangKuliah'] = 'Y';
    $w['Kapasitas'] = 0;
    $w['KapasitasUjian'] = 0;
    $w['KolomUjian'] = 2;
    $w['UntukUSM'] = 'N';
    $w['Keterangan'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Ruang";
    $strid = "<input type=text name='RuangID' value='' size=40 maxlength=50>";
  }
  $_ruangkuliah = ($w['RuangKuliah'] == 'Y')? 'checked' : '';
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $_usm = ($w['UntukUSM'] == 'Y')? 'checked' : '';
  $_optkampus = GetOption2('kampus', "concat(KampusID, ' - ', Nama)", 'KampusID', $w['KampusID'], '', 'KampusID');
  $optprodi= GetCheckboxes("prodi", "ProdiID",
    "concat(ProdiID, ' - ', Nama) as NM", "NM", $w['ProdiID'], '.');
  CheckFormScript("RuangID,Nama,KampusID,Lantai");
  // Tampilkan
  $c1 = 'class=inp'; $c2 = 'class=ul';
  $snm = session_name(); $sid = session_id();
	$si = "Select RuangID,RuangInduk from ruang where NA='N' order by RuangID";
	$ri = _query($si);
	$optInduk = "<option value=''></option>";
	while ($wi = _fetch_array($ri)) {
	if ($w['RuangInduk']==$wi['RuangInduk']) {
	$optInduk .= "<option value='$wi[RuangID]' selected>$wi[RuangID]</option>";
	}
	else $optInduk .= "<option value='$wi[RuangID]'>$wi[RuangID]</option>";
	}
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='RuangSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td $c1>Kode Ruang</td><td $c2>$strid</td></tr>
  <tr><td $c1>Nama</td><td $c2><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Untuk Prodi</td><td $c2>$optprodi</td></tr>
  <tr><td $c1>Kampus</td><td $c2><select name='KampusID'>$_optkampus</select></td></tr>
  <tr><td $c1>Lantai</td><td $c2><input type=text name='Lantai' value='$w[Lantai]' size=5 maxlength=5></td></tr>
  <tr><td $c1>Ruang Induk</td><td $c2><select name='RuangInduk'>$optInduk</select></td></tr>
  <tr><td $c1>Untuk kuliah?</td><td $c2><input type=checkbox name='RuangKuliah' value='Y' $_ruangkuliah></td></tr>
  <tr><td $c1>Kapasitas</td><td $c2><input type=text name='Kapasitas' value='$w[Kapasitas]' size=5 maxlength=5></td></tr>
  <tr><td $c1>Kapasitas Ujian</td><td $c2><input type=text name='KapasitasUjian' value='$w[KapasitasUjian]' size=5 maxlength=5></td></tr>
  <tr><td $c1>Jumlah Kolom Ujian</td><td $c2><input type=text name='KolomUjian' value='$w[KolomUjian]' size=4 maxlength=3</td></tr>
  <tr><td $c1>Untuk Ujian Saringan Masuk (USM)?</td><td $c2><input type=checkbox name='UntukUSM' value='Y' $_usm></td></tr>
  <tr><td $c1>Keterangan</td><td $c2><textarea name='Keterangan' cols=30 rows=4>$w[Keterangan]</textarea></td></tr>
  <tr><td $c1>NA (tidak aktif)?</td><td $c2><input type=checkbox name='NA' value='Y' $_na></td></tr>

  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&$snm=$sid'\"></td></tr>
  </form></table></p>";
}
function RuangSav() {
  $md = $_REQUEST['md']+0;
  $RuangID = $_REQUEST['RuangID'];
  $RuangInduk = $_REQUEST['RuangInduk'];
  $Nama = sqling($_REQUEST['Nama']);
  $KampusID = $_REQUEST['KampusID'];
  $Lantai = $_REQUEST['Lantai']+0;
  $RuangKuliah = (empty($_REQUEST['RuangKuliah']))? 'N' : $_REQUEST['RuangKuliah'];
  $Kapasitas = $_REQUEST['Kapasitas']+0;
  $KapasitasUjian = $_REQUEST['KapasitasUjian']+0;
  $KolomUjian = $_REQUEST['KolomUjian']+0;
  $UntukUSM = (empty($_REQUEST['UntukUSM']))? 'N' : $_REQUEST['UntukUSM'];
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $prodi = $_REQUEST['ProdiID'];
  $ProdiID = (empty($prodi))? '' : '.'.implode('.', $prodi).'.';
  if ($md == 0) {
    $s = "update ruang set Nama='$Nama', RuangInduk='$RuangInduk', ProdiID='$ProdiID',
      KampusID='$KampusID', Lantai='$Lantai',
      RuangKuliah='$RuangKuliah', Kapasitas='$Kapasitas', KapasitasUjian='$KapasitasUjian', KolomUjian='$KolomUjian',
      UntukUSM='$UntukUSM', Keterangan='$Keterangan', NA='$NA' where RuangID='$RuangID' ";
    $r = _query($s);
    BerhasilSimpan("?mnux=$_SESSION[mnux]", 100);
  }
  else {
    $ada = GetFields('ruang', 'RuangID', $_REQUEST['RuangID'], '*');
    if (empty($ada)) {
      $s = "insert into ruang(KodeID, RuangID, RuangInduk, Nama, ProdiID, KampusID, Lantai, RuangKuliah,
        Kapasitas, KapasitasUjian, KolomUjian, UntukUSM, Keterangan, NA)
        values('".KodeID."', '$RuangID', '$RuangInduk', '$Nama', '$ProdiID', '$KampusID', '$Lantai', '$RuangKuliah',
        '$Kapasitas', '$KapasitasUjian', '$KolomUjian', '$UntukUSM', '$Keterangan', '$NA')";
      $r = _query($s);
      BerhasilSimpan("?mnux=$_SESSION[mnux]", 100);
    }
    else echo ErrorMsg('Terjadi Kesalahan',
      "Kode ruang telah digunakan: <b>$ada[RuangID] - $ada[Nama]</b> di gedung: $ada[KampusID].<br>
      Gunakan kode ruang lain.");
  }
}

// *** Parameters ***
$kampusid = GetSetVar('kampusid');
$gos = (empty($_REQUEST['gos']))? 'DftrRuang' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Ruang");
$gos();
?>
