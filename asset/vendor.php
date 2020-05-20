<?php

// *** Functions ***
function DaftarVendor() {
  global $KodeID;
  $s = "select *
    from vendor where KodeID='".KodeID."'
    order by VendorID";
  $r = _query($s);
  echo "<p><a href='?mnux=asset/vendor&gos=vendorEdt&md=1'>Tambahkan Vendor</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr>
	  <th class=ttl colspan=2>#</th>
      <th class=ttl>Nama</th>
      <th class=ttl>Alamat</th>
      <th class=ttl> Telp. </th>
      <th class=ttl> Fax. </th>
      <th class=ttl>NA</th>
	 </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td class=inp>$w[VendorID] </td>
     <td class=ul><a href='?mnux=asset/vendor&gos=vendorEdt&jid=$w[VendorID]'><img src='img/edit.png'></a></td>
      <td $c>$w[Nama]</td>
      <td $c>$w[Alamat]</td>
      <td $c>$w[Telp]</td>
      <td $c>$w[Fax]</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}

function vendorEdt() {
	global $KodeID;
	$md = $_REQUEST['md']+0;
	if($md==0) {
       $w = GetFields('vendor', 'VendorID', $_REQUEST['jid'], '*');
       $nm = "<input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50>";
       $ro = "readonly=true";
	   $jdl="Edit Data";
	}
	else{
      $w = array();
      $w['VendorID'] = GetaField('vendor', 'KodeID', KodeID, "max(VendorID)+1");
      $nm = "<input type=text name='Nama' value='$w[Nama]' size=70 maxlength=70>";
      $ro = "";
	  $jdl="Tambah Data";
	}
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript("VendorID, Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='asset/vendor'>
  <input type=hidden name='gos' value='vendorSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Ranking/Urutan</td>
    <td class=ul><input type=text name='VendorID' value='$w[VendorID]' size=5 maxlength=5 $ro></td></tr>
  <tr><td class=inp>Nama</td>
    <td class=ul> $nm</td></tr>
  <tr><td class=inp>Alamat</td>
    <td class=ul><input type=text name='Alamat' value='$w[Alamat]' size=70 maxlength=70></td></tr>
  <tr><td class=inp>Telp.</td>
    <td class=ul><input type=text name='Telp' value='$w[Telp]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Fax.</td>
    <td class=ul><input type=text name='Fax' value='$w[Fax]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
    <td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=asset/vendor'\"></td></tr>
  </form></table></p>";
}

function vendorSav() {
  global $DefaultGOS, $KodeID;
  $md = $_REQUEST['md']+0;
  $nm = strtoupper(sqling($_REQUEST['Nama']));
  $vendorid = $_REQUEST['VendorID']+0;
  $alamat =$_REQUEST['Alamat'];
  $telp =$_REQUEST['Telp'];
  $fax =$_REQUEST['Fax'];

  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update vendor set Nama='$nm', Alamat='$alamat', Telp='$telp', Fax='$fax', LoginEdit='$_SESSION[_Nama]', TglEdit='$Actiondate', NA='$NA'
      where VendorID=$vendorid";
    $r = _query($s);
    $DefaultGOS();
  }
  else {
    $ada = GetFields('kelompokasset', "KodeID='".KodeID."' and KelompokID", $KelompokID, '*');
    if (empty($ada)) {
      $s = "insert into vendor (VendorID, Nama, Alamat, Telp, Fax, KodeID, LoginAdd, TglAdd, NA)
        values ('$vendorid', '$nm', '$alamat', '$telp', '$fax', '".KodeID."', '$_SESSION[_Nama]', '$Actiondate', '$NA')";
      $r = _query($s);
      echo "<script>window.location = '?mnux=asset/vendor'; </script>";
    }
    else {
      echo ErrorMsg("Gagal Simpan",
      "Data Vendor <b>$JabatanID</b> sudah ada.<br />
      Anda tidak dapat memasukkan vendor ini lebih dari 1 kali.");
      $DefaultGOS();
    }
  }
}

// *** Parameters ***
$DefaultGOS = "DaftarVendor";
$gos = (empty($_REQUEST['gos']))? $DefaultGOS : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Vendor $arrID[Nama]");
$gos();


?>