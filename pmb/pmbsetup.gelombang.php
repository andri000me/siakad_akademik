<?php
// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrGelombang' : $_REQUEST['sub'];
$sub();


// *** Functions ***
function GelEdtScript() {
  echo <<<SCR
  <script>
  function GelEdt(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].gelombang.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=440, height=560, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
function DftrGelombang() {
  global $_maxbaris;
  include_once "class/lister.class.php";
  GelEdtScript();
  $_gelombangpage = GetSetVar('_gelombangpage', 0);
  
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=gelombang&sub=&_gelombangpage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=8></td></tr>";
  $lst = new lister;
  $lst->tables = "pmbperiod p where p.KodeID = '".KodeID."' order by p.PMBPeriodID desc";
  $lst->fields = "p.PMBPeriodID, p.Nama,
    p.NA,
    date_format(p.TglMulai, '%d/%m/%y') as _TglMulai,
    date_format(p.TglSelesai, '%d/%m/%y') as _TglSelesai,
    date_format(p.UjianMulai, '%d/%m/%y') as _UjianMulai,
    date_format(p.UjianSelesai, '%d/%m/%y') as _UjianSelesai,
    date_format(p.BayarMulai, '%d/%m/%y') as _BayarMulai,
    date_format(p.BayarSelesai, '%d/%m/%y') as _BayarSelesai,
	date_format(p.WawancaraMulai, '%d/%m/%y') as _WawancaraMulai,
	date_format(p.WawancaraSelesai, '%d/%m/%y') as _WawancaraSelesai";
  $lst->startrow = $_SESSION['_gelombangpage']+0;
  $lst->maxrow = $_maxbaris;
  $lst->headerfmt = "<p><table class=box cellspacing=1 align=center width=800>
    <tr>
    <td class=ul1 colspan=7>
      <input type=button name='Tambah' value='Tambah Gelombang'
        onClick=\"javascript:GelEdt(1, '', '$_SESSION[mnux]')\" />
      <input type=button name='Refresh' value='Refresh'
        onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />
      Catatan: Hanya ada 1 gelombang yang aktif.
    </td>
    </tr>
    
    <tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>Gelombang</th>
    <th class=ttl>Nama Gelombang</th>
    <th class=ttl>Periode</th>
    <th class=ttl>Ujian</th>
    <th class=ttl>Bayar</th>
    <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class=ul1 width=10>
      <a href='#' onClick=\"javascript:GelEdt(0, '=PMBPeriodID=', '$_SESSION[mnux]')\"><img src='img/edit.png' /></a></td>
    <td class=cna=NA= width=100>=PMBPeriodID=</td>
    <td class=cna=NA=>=Nama=</td>
    <td class=cna=NA= width=80 align=center>=_TglMulai=$brs=_TglSelesai=</td>
    <td class=cna=NA= width=80 align=center>=_UjianMulai=$brs=_UjianSelesai=</td>
    <td class=cna=NA= width=80 align=center>=_BayarMulai=$brs=_BayarSelesai=</td>
    <td class=ul1 width=10 align=center><img src='img/book=NA=.gif' /></td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table>";
  $hal = $lst->WritePages ($pagefmt, $pageoff);
  $ttl = $lst->MaxRowCount;
  echo $lst->ListIt();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}

?>
