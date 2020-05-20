<link rel="stylesheet" type="text/css" href="../themes/default/index.css" />
<?php
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../sisfokampus1.php";
  $gos=$_POST['gos'];
  if (empty($gos)) {
  ?>
<body onBlur='javascript:window.close();'>
<?php

  $s="select MhswID,Nama,TempatLahir,TanggalLahir,NamaAyah from mhsw where MhswID='$_GET[MhswID]'";
  $r=_query($s);
  
  while ($w=_fetch_array($r)) {
  ?>
  <?php echo "<form name='EditMhs' method=POST action='?'>"; ?>
  <table class="box">
  <input type="hidden" name="MhswID" value="<?php echo $_GET[MhswID]; ?>">
  <input type="hidden" name="gos" value="simpan">
  <tr><td class=inp>Nama</td><td><input type="text" name='Nama' size="30" maxlength="200" value='<?php echo $w[Nama]; ?>' /></td></tr>
  <tr><td class=inp>Tempat Lahir</td><td><input type="text" name="TempatLahir" size="30" maxlength="200" value="<?php echo $w[TempatLahir]; ?>" /></td></tr>
  <tr><td class=inp>Tanggal Lahir</td><td><input name="TanggalLahir" type="text" size="15" maxlength="15" value="<?php echo $w[TanggalLahir]; ?>" /></td></tr>
  <tr><td class=inp>Nama Ayah</td><td><input name="NamaAyah" type="text" size="30" maxlength="200" value="<?php echo $w[NamaAyah]; ?>" /></td></tr>
  <tr><td colspan="2" align="center"><input class='buttons' type="submit" value="Simpan"></td></tr>
  </table></form>
  <?php } 
  }
  else {
  $s="update mhsw set Nama='$_POST[Nama]',
  						TempatLahir='$_POST[TempatLahir]',
						TanggalLahir='$_POST[TanggalLahir]',
						NamaAyah='$_POST[NamaAyah]'
				where MhswID='$_POST[MhswID]'";
	$r=_query($s);
	
	  ?> 
  <body onFocus='javascript:window.close();'> <?php
} 
?>

</body>
  