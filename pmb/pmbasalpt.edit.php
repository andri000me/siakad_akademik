<?php
// Author: Emanuel Setio Dewo, setio_dewo@sisfokampus.net
// 2005-12-28

function PTEdt() {
  $md = $_REQUEST['md']+0;
  $SRPT = $_REQUEST['SRPT']+0;
  if ($md == 0) {
    $w = GetFields('perguruantinggi', 'PerguruanTinggiID', $_REQUEST['PTid'], '*');
    $jdl = "Edit Perguruan Tinggi";
    $strid = "<input type=hidden name='PerguruanTinggiID' value='$w[PerguruanTinggiID]'><b>$w[PerguruanTinggiID]</b>";
  }
  else {
    $w = array();
    $w['PerguruanTinggiID'] = '';
    $w['Nama'] = '';
    $w['Alamat1'] = '';
    $w['Alamat2'] = '';
    $w['Kota'] = '';
    $w['KodePos'] = '';
    $w['JenisPerguruanTinggiID'] = '';
    $w['Telephone'] = '';
    $w['Fax'] = '';
    $w['Website'] = '';
    $w['Email'] = '';
    $w['Kontak'] = '';
    $w['JabatanKontak'] = '';
    $w['HandphoneKontak'] = '';
    $w['EmailKontak'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Perguruan Tinggi";
    $strid = "<input type=text name='PerguruanTinggiID' size=5 maxlength=8>";
  }
  $snm = session_name(); $sid = session_id();
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  //$_JenisPerguruanTinggi = GetOption2('jenisperguruantinggi', "Nama", 'Nama', $w['JenisPerguruanTinggiID'], '', 'JenisPerguruanTinggiID');
  $c1 = 'class=inp'; $c2 = 'class=ul';
  // Tampilkan
  CheckFormScript("PerguruanTinggiID,Nama,Kota");
  echo "<table class=box cellspacing=1 cellpadding=4 align=center>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='PTSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td $c1>Kode Perguruan Tinggi</td><td $c2>$strid</td></tr>
  <tr><td $c1>Nama Perguruan Tinggi</td><td $c2><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Jenis Perguruan Tinggi</td><td $c2><select name='JenisPerguruanTinggiID'>$_JenisPerguruanTinggi</select></td></tr>
  <tr><td $c1>Alamat</td><td $c2><input type=text name='Alamat1' value='$w[Alamat1]' size=50 maxlength=100><br>
    <input type=text name='Alamat2' value='$w[Alamat2]' size=50 maxlength=100></td></tr>
  <tr><td $c1>Kota</td><td $c2><input type=text name='Kota' value='$w[Kota]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Kode Pos</td><td $c2><input type=text name='KodePos' value='$w[KodePos]' size=30 maxlength=20></td></tr>
  <tr><td $c1>Telephone</td><td $c2><input type=text name='Telephone' value='$w[Telephone]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Facsimile</td><td $c2><input type=text name='Fax' value='$w[Fax]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Website</td><td $c2><input type=text name='Website' value='$w[Website]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Email</td><td $c2><input type=text name='Email' value='$w[Email]' size=50 maxlength=50></td></tr>
  <tr><td colspan=2 class=ul align=center><b>Kontak Utama</td></tr>
  <tr><td $c1>Nama Kontak</td><td $c2><input type=text name='Kontak' value='$w[Kontak]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Jabatan</td><td $c2><input type=text name='JabatanKontak' value='$w[JabatanKontak]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Handphone</td><td $c2><input type=text name='HandphoneKontak' value='$w[HandphoneKontak]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Email</td><td $c2><input type=text name='EmailKontak' value='$w[EmailKontak]' size=50 maxlength=50></td></tr>
  <tr><td $c1>NA (tidak aktif)?</td><td $c2><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"window.close()\"></td></tr>
  </form></table>";
}
function PTSav() {
  $md = $_REQUEST['md'] +0;
  $PerguruanTinggiID = $_REQUEST['PerguruanTinggiID'];
  $Nama = sqling($_REQUEST['Nama']);
  $JenisPerguruanTinggiID = $_REQUEST['JenisPerguruanTinggiID'];
  $Alamat1 = sqling($_REQUEST['Alamat1']);
  $Alamat2 = sqling($_REQUEST['Alamat2']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = $_REQUEST['KodePos'];
  $Telephone = sqling($_REQUEST['Telephone']);
  $Fax = sqling($_REQUEST['Fax']);
  $Website = sqling($_REQUEST['Website']);
  $Email = sqling($_REQUEST['Email']);
  $Kontak = sqling($_REQUEST['Kontak']);
  $JabatanKontak = sqling($_REQUEST['JabatanKontak']);
  $HandphoneKontak = sqling($_REQUEST['HandphoneKontak']);
  $EmailKontak = sqling($_REQUEST['EmailKontak']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update perguruantinggi set Nama='$Nama', Alamat1='$Alamat1', Alamat2='$Alamat2',
      JenisPerguruanTinggiID='$JenisPerguruanTinggiID', Kota='$Kota', KodePos='$KodePos', NA='$NA',
      Website='$Website', Email='$Email', Telephone='$Telephone', Fax='$Fax',
      Kontak='$Kontak', JabatanKontak='$JabatanKontak',
      HandphoneKontak='$HandphoneKontak', EmailKontak='$EmailKontak'
      where PerguruanTinggiID='$PerguruanTinggiID'";
    $r = _query($s);
  }
  else {
    $ada = GetFields('perguruantinggi', 'PerguruanTinggiID', $PerguruanTinggiID, '*');
    if (!empty($ada)) echo ErrorMsg("Data tidak dapat disimpan",
      "Data tidak dapat disimpan karena kode sekolah <b>$PerguruanTinggiID</b> telah digunakan oleh
      sekolah <b>$ada[Nama]</b>.<br>
      Gunakan kode sekolah yg lain.");
    else {
      $s = "insert into perguruantinggi (PerguruanTinggiID, Nama, JenisPerguruanTinggiID, Alamat1, Alamat2, Kota, KodePos, NA,
        Telephone, Fax, Website, Email, 
        Kontak, JabatanKontak, HandphoneKontak, EmailKontak)
        values('$PerguruanTinggiID', '$Nama', '$JenisPerguruanTinggiID', '$Alamat1', '$Alamat2', '$Kota', '$KodePos', '$NA',
        '$Telephone', '$Fax', '$Website', '$Email',
        '$Kontak', '$JabatanKontak', '$HandphoneKontak', '$EmailKontak')";
      $r = _query($s);
    }
  }
  TutupScript();
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}

// *** Parameters ***

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Daftar Asal Perguruan Tinggi", 1);

$_asalsekPage = GetSetVar('_asalsekPage');
$perguruantinggi = GetSetVar("NamaPerguruanTinggi");
$kotasekolah = GetSetVar("KotaPerguruanTinggi");
$gos = (empty($_REQUEST['gos']))? "PTEdt" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Asal Perguruan Tinggi");
$gos();
?>
