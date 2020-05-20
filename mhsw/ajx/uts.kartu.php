<?php
session_start();

  include_once "../../dwo.lib.php";
  include_once "../../db.mysql.php";
  include_once "../../connectdb.php";
  include_once "../../parameter.php";
  include_once "../../cekparam.php";
  
  $MhswID = $_SESSION['_Login'];
  $w	= GetFields('mhsw', "MhswID", $MhswID,'*');
  $TahunID = GetaField('tahun', "ProdiID='$w[ProdiID]' and NA", N, "TahunID");
?>
<form method=post action='mhsw/uts.kartu.cetak.php' target='_blank' id='modal-form'>
<input type="hidden" name='MhswID' value="<?php echo $MhswID?>" />
<input type="hidden" name='TahunID' value="<?php echo $TahunID?>" />
<input type="hidden" name='ProdiID' value="<?php echo $w['ProdiID']?>" />
<input type="hidden" name='_khsProgramID' value="<?php echo $w['ProgramID']?>" />
<input type="submit" value="Cetak Jadwal UTS" />
</form>
<form method=post action='mhsw/uas.kartu.cetak.php' target='_blank' id='modal-form'>
<input type="hidden" name='MhswID' value="<?php echo $MhswID?>" />
<input type="hidden" name='TahunID' value="<?php echo $TahunID?>" />
<input type="hidden" name='ProdiID' value="<?php echo $w['ProdiID']?>" />
<input type="hidden" name='_khsProgramID' value="<?php echo $w['ProgramID']?>" />
<input type="submit" value="Cetak Jadwal UAS" />
</form>