<?php

// *** Functions ***
function DaftarLokasiAsset() {
  global $KodeID;
  $s = "select *
    from lokasiasset where KodeID='".KodeID."'
    order by lokasiID";
  $r = _query($s);
  echo "<p><a href='?mnux=asset/lokasi.asset&gos=LokasiAssetEdt&md=1'>Tambahkan Lokasi Asset</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr>
	  <th class=ttl colspan=2>#</th>
      <th class=ttl >Nama Lokasi</th>
      <th class=ttl >NA</th>
	 </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td class=inp>$w[LokasiID] </td>
     <td class=ul><a href='?mnux=asset/lokasi.asset&gos=LokasiAssetEdt&jid=$w[LokasiID]'><img src='img/edit.png'></a></td>
      <td $c>$w[Nama]</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}

function LokasiAssetEdt() {
	global $KodeID;
	$md = $_REQUEST['md']+0;
	if($md==0) {
       $w = GetFields('lokasiasset', 'LokasiID', $_REQUEST['jid'], '*');
       $nm = "<input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50>";
	   $jdl="Edit Data";
	   $lokasi = "<input type=text name='LokasiID' value='$w[LokasiID]' size=5 maxlength=5 readonly>";
	}
	else{
      $w = array();
      $w['LokasiID'] = GetaField('lokasiasset', 'KodeID', KodeID, "max(LokasiID)+1");
      $nm = "<input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50>";
	  $jdl="Tambah Data";
	  $lokasi = "<input type=text name='LokasiID' value='$w[LokasiID]' size=5 maxlength=5>";
	}
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript("LokasiID, Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='asset/lokasi.asset'>
  <input type=hidden name='gos' value='LokasiAssetSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Ranking/Urutan</td>
    <td class=ul>$lokasi</td></tr>
  <tr><td class=inp>Nama</td>
    <td class=ul><b>$nm</b></td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
    <td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=asset/lokasi.asset'\"></td></tr>
  </form></table></p>";
}

function LokasiAssetSav() {
  global $DefaultGOS, $KodeID;
  $md = $_REQUEST['md']+0;
  $Nama = strtoupper(sqling($_REQUEST['Nama']));
  $LokasiID = $_REQUEST['LokasiID']+0;

  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update lokasiasset set LokasiID='$LokasiID', Nama='$Nama',KodeID='".KodeID."', LoginEdit='$_SESSION[_Nama]', TanggalEdit=now(), NA='$NA'
      where LokasiID=$LokasiID";
    $r = _query($s);
    $DefaultGOS();
  }
  else {
    $ada = GetFields('lokasiasset', "KodeID='$KodeID' and LokasiID", $LokasiID, '*');
    if (empty($ada)) {
      $s = "insert into lokasiasset (LokasiID, Nama, KodeID, LoginAdd, TanggalAdd, NA)
        values ('$LokasiID', '$Nama', '".KodeID."', '$_SESSION[_Nama]', now(), '$NA')";
      $r = _query($s);
      echo "<script>window.location = '?mnux=asset/lokasi.asset'; </script>";
    }
    else {
      echo ErrorMsg("Gagal Simpan",
      "Data Lokasi <b>$LokasiID</b> sudah ada.<br />
      Anda tidak dapat memasukkan lokasi ini lebih dari 1 kali.");
      $DefaultGOS();
    }
  }
}

// *** Parameters ***
$DefaultGOS = "DaftarLokasiAsset";
$gos = (empty($_REQUEST['gos']))? $DefaultGOS : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Lokasi $arrID[Nama]");
$gos();


?>