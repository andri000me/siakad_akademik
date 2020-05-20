<?php

// *** Parameters ***
$_PMBPeriodID = GetSetVar('_PMBPeriodID');
if (empty($_SESSION['_PMBPeriodID'])) {
  $_PMBPeriodID = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
  $_SESSION['_PMBPeriodID'] = $_PMBPeriodID;
}

// *** Main ***
TampilkanJudul("Laporan-laporan PMB");
$gos = (empty($_REQUEST['gos']))? 'DftrLaporan' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function DftrLaporan() {
  $maxperiode = 5;
  $_SESSION['maxperiode'] = $maxperiode;
  $arrLap = array(
    'Laporan Penjualan Formulir~jualformulir',
	'Laporan Penjualan Formulir [XLS]~jualformulir.xls',
	'Laporan Pembayaran Uang Kesehatan [XLS]~uangkesehatanxls',
  'Laporan Calon Mahasiswa Per Asal Kota~asalkota',
  'Laporan Calon Mahasiswa Per Asal Propinsi~asalpropinsi',
  'Laporan Calon Mahasiswa Per Asal Sekolah~asalsekolah',
  'Laporan Calon Mahasiswa Berdasar Nilai Sekolah~nilaiasalsekolah',
  'Sebaran Mahasiswa Per Asal Sekolah~asalsekolah.sebaran',
	'Laporan Calon Mahasiswa Berdasar Program Pendidikan~program',
	'Laporan Data & Fakta PMB~faktapmb',
	'Laporan Ratio Presenter~ratiopresenter',
	'Data Registrasi Sort By Presenter~registrasi',
    "Rekap Jumlah Pendaftar per Periode Reg. (max: $maxperiode periode)~rekapperperiode"
	
  );
  
  // Tampilkan
  LauncherScript();
  $i = 0;
  echo "<p><table class=box cellspacing=1 align=center>";
  echo "<tr>
    <form action='?' method=POST>
    <input type=hidden name='gos' value='' />
    <td class=ul1 colspan=3>
      Gelombang PMB: <input type=text name='_PMBPeriodID' value='$_SESSION[_PMBPeriodID]' size=10 maxlength=10 />
      <input type=submit name='Submit' value='Submit' />
    </td>
    </form>
    </tr>";
  foreach ($arrLap as $arr) {
    $i++;
    $a = explode('~', $arr);
    $_a = "<a href='#' onClick=\"Prints('".$a[1]."')\">";
    echo "<tr>
      <td class=inp width=10>$i</td>
      <td class=ul1>$_a $a[0]</a></td>
      <td class=ul1 align=center width=10>$_a<img src='img/printer2.gif' /></a></td>
      </tr>";
  }
  echo "</table></p>";
}
function LauncherScript() {
  echo <<<SCR
  <script>
  function Prints(mdl) {
    lnk = "$_SESSION[mnux]."+mdl+".php?gel=$_SESSION[_PMBPeriodID]";
    //window.location = lnk;
    win2 = window.open(lnk, "", "width=900, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>
