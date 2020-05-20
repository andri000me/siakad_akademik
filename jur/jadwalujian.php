<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 26/11/2008

// *** Parameters ***
$_jdwlProdi = GetSetVar('_jdwlProdi');
$_jdwlProg  = GetSetVar('_jdwlProg');
$_jdwlTahun = GetSetVar('_jdwlTahun');
$_jdwlHari  = GetSetVar('_jdwlHari');
$_jdwlUjian = GetSetVar('_jdwlUjian');

$arrUjian = array(1=>'UTS', 2=>'UAS');
$_SESSION['_jdwlU'] = $arrUjian[$_jdwlUjian];

// *** Main ***
TampilkanJudul("Jadwal $_SESSION[_jdwlU] &minus; $_SESSION[_jdwlTahun]");
$gos = (empty($_REQUEST['gos']))? "fnJadwalUjian" : $_REQUEST['gos'];
$gos();


// *** Functions ***
function fnJadwalUjian() {
  echo <<<ESD
  <iframe name='frmJDWL' src='$_SESSION[mnux].jdwl.php' width=500 height=500 frameborder=0 align=center>
  </iframe>
  <iframe name='frmUJIAN' src='$_SESSION[mnux].ruang.php' width=500 height=500 frameborder=0 align=center>
  </iframe>
  
  <script>
  function RefreshAll() {
    window.location="../index.php?mnux=$_SESSION[mnux]";
  }
  </script>
ESD;
}

?>
