<link rel="stylesheet" type="text/css" href="../themes/default/index.css" />
<?php
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../sisfokampus1.php";
  $gos=$_POST['gos'];
  $mnux = GetSetVar('mnux');
  if (empty($gos)) {
  ?>
<body onBlur='javascript:window.close();'>
<?php
$gels = GetFields('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID, Nama");
  $s="select a.PMBFormulirID,a.AplikanID, a.Nama, a.PMBID,p.Jumlah from aplikan a, pmbformjual p where a.PMBID='$_GET[PMBID]' AND a.PMBPeriodID='$gels[PMBPeriodID]' AND p.PMBFormJualID=a.PMBFormJualID ";
  $r=_query($s);
  while ($w=_fetch_array($r)) {
  ?>
  <?php 
  $optForm = "<option value=''>";
  $f = "select PMBFormulirID,Nama,Harga from pmbformulir";
  $j = _query($f);
  while ($ws = _fetch_array($j)) {
  		$harga = $ws['Harga'];
  		if ($w['PMBFormulirID']==$ws['PMBFormulirID']) {
  		$optForm .= "<option value='$ws[PMBFormulirID]' selected>Rp $ws[Harga] - ($ws[Nama])</option>";
		}
		else $optForm .= "<option value='$ws[PMBFormulirID]'>Rp $ws[Harga] - ($ws[Nama])</option>";
	}
    ?>
  <form name='EditMhs' method=POST action='?'>
  <table class="box" width=400>
  <input type="hidden" name="AplikanID" value="<?php echo $w['AplikanID']; ?>">
  <input type="hidden" name="PMBID" value="<?php echo $w['PMBID']; ?>">
  <input type="hidden" name="Harga" value="<?php echo $harga; ?>">
  <input type="hidden" name="gos" value="simpan">
  <input type="hidden" name="mnux" value="$_SESSION[mnux]">
  <tr><td class=inp>Nama</td><td><?php echo $w[Nama]; ?></td></tr>
  <tr><td class=inp>PMBID</td><td><?php echo $w[PMBID]; ?></td></tr>
  <tr><td class=inp>AplikanID</td><td><?php echo $w[AplikanID]; ?></td></tr>
  <tr><td class=inp>Formulir</td><td><select name="Formulir"><?php echo $optForm; ?></select>
  </td></tr>
  <tr><td colspan="2" align="center"><input class='buttons' type="submit" value="Simpan"></td></tr>
  </table></form>
  <?php } 
  }
  elseif ($gos=="simpan") {
		$gels = GetFields('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID, Nama");
		$f = GetFields('pmbformulir',"PMBFormulirID",$_POST[Formulir],"PMBFormulirID,Nama,Harga");
		$r = _query("update aplikan set PMBFormulirID='$f[PMBFormulirID]' where AplikanID='$_POST[AplikanID]' and PMBPeriodID='$gels[PMBPeriodID]'");
		$r = _query("update pmbformjual set PMBFormulirID='$f[PMBFormulirID]',Jumlah='$f[Harga]' where AplikanID='$_POST[AplikanID]' and PMBPeriodID='$gels[PMBPeriodID]'");
		$r = _query("update pmb set PMBFormulirID='$f[PMBFormulirID]' where PMBID='$_POST[PMBID]'");
		TutupScript();
}
  else {

TutupScript();
} 


function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=baa/mhswbaru';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;

}
?>
</body>
  