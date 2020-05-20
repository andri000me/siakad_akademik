<?php
// *** Parameters ***
$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");

// *** Main ***
TampilkanJudul("Penjualan Formulir - $gelombang");
if (empty($gelombang)) {
  echo ErrorMsg("Error",
    "Tidak ada gelombang PMB yang aktif.<br />
    Hubungi Kepala PMB untuk mengaktifkan gelombang.");
}
else {
  $gos = (empty($_REQUEST['gos']))? 'DftrPenjualan' : $_REQUEST['gos'];
  $gos($gelombang);
}

// *** Functions ***
function DftrPenjualan($g) {
  JualFormulirScript();
  $s = "select f.PMBFormulirID, f.Nama, f.JumlahPilihan, format(f.Harga, 0) as HRG,
    (select count(PMBFormJualID) 
      from pmbformjual 
      where KodeID='".KodeID."' 
        and PMBFormulirID=f.PMBFormulirID
        and PMBPeriodID='".$g."'
		and Batal='N') as JML
    from pmbformulir f
    where f.KodeID = '".KodeID."' and f.NA = 'N'
    order by f.Nama";
  $r = _query($s); $n = 0;
  
  echo "<p><table class=box cellspacing=1 align=center width=500>";
  echo "<tr>
    <td class=ul1 colspan=6>
    <input type=button name='Refresh' value='Refresh'
      onClick=\"location='?mnux=$_SESSION[mnux]'\" />
    <!--
    <input type=button name='CetakUlangKwitansi' value='Cetak Ulang Kwitansi'
      onClick=\"\" />
    -->
    </td>
    </tr>";
  echo "<tr>
    <th class=ttl>#</th>
    <th class=ttl>Formulir</th>
    <th class=ttl>&sum; Pil.</th>
    <th class=ttl>Harga</th>
    <th class=ttl>&sum;<br />Terjual</th>
    <th class=ttl>Jual</th>
	<th class=ttl>Cetak</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $tot += $w['JML'];
    echo "<tr>
      <td class=inp width=20>$n</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul width=40 align=center>$w[JumlahPilihan]</td>
      <td class=ul width=80 align=right>$w[HRG]</td>
      <td class=ul width=40 align=right>&nbsp;$w[JML]</td>
      <td class=ul width=10 align=center>
        <a href='#' onClick=\"javascript:JualFormulir('$g', $w[PMBFormulirID])\"><img src='img/fileshare.gif' /></a>
        </td>
	  <td class=ul width=20 align=center><a href='#' onClick=\"CetakFormulir('$g', '$w[PMBFormulirID]')\"><img src='img/printer2.gif'></a></td>
      </tr>";
  }
  $_tot = number_format($tot);
  echo "<tr>
    <td class=ul1 colspan=4 align=right>Total Terjual:</td>
    <td class=ul1 align=right><font size=+1>$_tot</font></td>
    </tr>";
  echo "</table></p>";
}

function JualFormulirScript() {
  echo <<<SCR
  <script>
  function JualFormulir(gel, id) {
    lnk = "$_SESSION[mnux].jual.php?id="+id+"&gel="+gel;
    win2 = window.open(lnk, "", "width=440, height=550, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakFormulir(gel, id) {
	lnk = "$_SESSION[mnux].cetak.php?id="+id+"&gel="+gel;
    win2 = window.open(lnk, "", "width=1000, height=600, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>
