<?php

// *** Functions ***
function DaftarKelompokAsset() {
  global $KodeID;
  $s = "select *
    from kelompokasset where KodeID='".KodeID."'
    order by kelompokID";
  $r = _query($s);
  echo "<p><a href='?mnux=asset/kelompok.asset&gos=KelompokAssetEdt&md=1'>Tambahkan Kategori Asset</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr>
	  <th class=ttl colspan=2 rowspan=2>#</th>
      <th class=ttl rowspan=2>Nama Kategori</th>
      <th class=ttl rowspan=2>Kode Jenis</th>
      <th class=ttl colspan=2>Masa Manfaat</th>
      <th class=ttl colspan=2> Prosentase </th>
      <th class=ttl rowspan=2>NA</th>
      </tr>
	  <tr>
		  <th class=ttl> Komersil </th>
		  <th class=ttl> Fiskal </th>
		  <th class=ttl> Komersil </th>
		  <th class=ttl> Fiskal </th>
	 </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td class=inp>$w[KelompokID] </td>
     <td class=ul><a href='?mnux=asset/kelompok.asset&gos=KelompokAssetEdt&jid=$w[KelompokID]'><img src='img/edit.png'></a></td>
      <td $c>$w[Nama]</td>
      <td $c>$w[Kode]</td>
      <td $c>$w[MasaKomersil]</td>
      <td $c>$w[MasaFiskal]</td>
      <td $c>$w[ProsentaseKomersil]</td>
      <td $c>$w[ProsentaseFiskal]</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}

function KelompokAssetEdt() {
	global $KodeID;
	$md = $_REQUEST['md']+0;
	if($md==0) {
       $w = GetFields('kelompokasset', 'KelompokID', $_REQUEST['jid'], '*');
       $nm = "<input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50>";
	   $jdl="Edit Data";
	}
	else{
      $w = array();
      $w['KelompokID'] = GetaField('kelompokasset', 'KodeID', KodeID, "max(KelompokID)+1");
      $nm = "<input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50>";
	  $jdl="Tambah Data";
	}
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript("KelompokID, Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='asset/kelompok.asset'>
  <input type=hidden name='gos' value='KelompokAssetSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Kode Jenis</td>
    <td class=ul><input type=text name='Kode' value='$w[Kode]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Nama</td>
    <td class=ul><b>$nm</b></td></tr>
  <tr><td class=inp>Masa Komersil</td>
    <td class=ul><input type=text name='ManfaatKomersil' value='$w[MasaKomersil]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Masa Fiskal</td>
    <td class=ul><input type=text name='ManfaatFiskal' value='$w[MasaFiskal]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Prosentase Komersil</td>
    <td class=ul><input type=text name='ProsentaseKomersil' value='$w[ProsentaseKomersil]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Prosentase Fiskal</td>
    <td class=ul><input type=text name='ProsentaseFiskal' value='$w[ProsentaseFiskal]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
    <td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=asset/kelompok.asset'\"></td></tr>
  </form></table></p>";
}

function KelompokAssetSav() {
  global $DefaultGOS, $KodeID;
  $md = $_REQUEST['md']+0;
  $nm = strtoupper(sqling($_REQUEST['Nama']));
  $KelompokID = $_REQUEST['KelompokID']+0;
  $manfaatkomersil =$_REQUEST['ManfaatKomersil'];
  $manfaatfiskal =$_REQUEST['ManfaatFiskal'];
  $prosentasekomersil =$_REQUEST['ProsentaseKomersil'];
  $prosentasefiskal =$_REQUEST['ProsentaseFiskal'];

  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update kelompokasset set KelompokID='$KelompokID', MasaKomersil='$manfaatkomersil', MasaFiskal='$manfaatfiskal', ProsentaseKomersil='$prosentasekomersil', ProsentaseFiskal='$prosentasefiskal', LoginEdit='$_SESSION[_Nama]', TanggalEdit='$Actiondate', NA='$NA'
      where KelompokID=$KelompokID";
    $r = _query($s);
    $DefaultGOS();
  }
  else {
    $ada = GetFields('kelompokasset', "KodeID='".KodeID."' and KelompokID", $KelompokID, '*');
    if (empty($ada)) {
      $s = "insert into kelompokasset (Nama, MasaKomersil, MasaFiskal, ProsentaseKomersil, ProsentaseFiskal, KodeID, LoginAdd, TanggalAdd, NA)
        values ('$nm', '$manfaatkomersil', '$manfaatfiskal', '$prosentasekomersil', '$prosentasefiskal', '".KodeID."', '$_SESSION[_Nama]', '$Actiondate', '$NA')";
      $r = _query($s);
      echo "<script>window.location = '?mnux=asset/kelompok.asset'; </script>";
    }
    else {
      echo ErrorMsg("Gagal Simpan",
      "Data kelompok <b>$JabatanID</b> sudah ada.<br />
      Anda tidak dapat memasukkan kelompok ini lebih dari 1 kali.");
      $DefaultGOS();
    }
  }
}

// *** Parameters ***
$DefaultGOS = "DaftarKelompokAsset";
$gos = (empty($_REQUEST['gos']))? $DefaultGOS : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Kategori Asset");
$gos();


?>