<?php
// Author : Emanuel Setio Dewo
// Start  : 4 Agustus 2008, Ultah Sisi
// Email  : setio.dewo@gmail.com

// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrPras' : $_REQUEST['sub'];
$sub();

// *** functions ***
function SyaEdtScript() {
  echo <<<SCR
  <script>
  function PrasEdt(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].pras.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=600, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

function DftrPras() {
  SyaEdtScript();
  $s = "select *
    from pmbsyarat
    where KodeID = '".KodeID."'
    order by PMBSyaratID";
  $r = _query($s);
  $n = 0;
  
  echo "<p><table class=box cellspacing=1 align=center width=600>";
  echo "<tr>
    <td class=ul1 colspan=6>
    <input type=button name='Tambah' value='Tambah Komponen'
      onClick=\"javascript:PrasEdt(1, '', '$_SESSION[mnux]')\" />
    </td>
    </tr>";
  echo "<tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Prodi</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
      <td class=inp width=20>$n</td>
      <td class=ul1 width=10>
        <a href='#' onClick=\"javascript:PrasEdt(0, '$w[PMBSyaratID]', '$_SESSION[mnux]')\"><img src='img/edit.png' /></a>
        </td>
      <td $c width=60>$w[PMBSyaratID]&nbsp;</td>
      <td $c>$w[Nama]&nbsp;</td>
      <td $c>$w[ProdiID]&nbsp;</td>
      <td class=ul1 width=10><img src='img/book$w[NA].gif' /></td>
      </tr>";
  }
  echo "</table></p>";
}
