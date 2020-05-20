<?php
// Author : Emanuel Setio Dewo
// Start  : 4 Agustus 2008, Ultah Sisi
// Email  : setio.dewo@gmail.com

// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrFormulir' : $_REQUEST['sub'];
$sub();

// *** functions ***
function DftrFormulirScript() {
  echo <<<SCR
  <script>
  function FrmEdt(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].formulir.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=440, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

function DftrFormulir() {
  DftrFormulirScript();
  $s = "select f.PMBFormulirID, f.Nama, f.JumlahPilihan, f.Harga, f.NA, f.WebDef,  
    format(f.Harga, 2) as HRG, f.USM
    from pmbformulir f
    where f.KodeID = '".KodeID."'
    order by f.Nama";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 align=center width=500>";
  echo "<tr>
    <td class=ul1 colspan=6>
      <input type=button name='TambahFrm' value='Tambah Formulir'
        onClick=\"javascript:FrmEdt(1, 0, '$_SESSION[mnux]')\" />
      <input type=button name='Refresh' value='Refresh'
        onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />
    </td>
    </tr>";
  echo "<tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>Formulir</th>
    <th class=ttl>Pil.</th>
    <th class=ttl>Harga</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $WebDef = ($w['WebDef'] == 'N')? '' : "<img src='img/web.jpg' />"; 
	echo "<tr>
      <td class=inp width=28>$n</td>
      <td class=ul1 width=10>
        <a href='#' onClick=\"javascript:FrmEdt(0, $w[PMBFormulirID], '$_SESSION[mnux]')\"><img src='img/edit.png' /></a>
        </td>
      <td $c>$w[Nama]&nbsp;$WebDef</td>
	  <td $c align=right width=40>$w[JumlahPilihan]</td>
      <td $c align=right width=100>$w[HRG]</td>
      <td class=ul1 align=center width=10><img src='img/book$w[NA].gif' /></td>
      </tr>";
  }
  echo "</table>";
}
