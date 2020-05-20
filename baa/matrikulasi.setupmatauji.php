<?php

// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrMataUji' : $_REQUEST['sub'];
$sub();

// *** functions ***
function MataUjiEdtScript() {
  echo <<<SCR
  <script>
  function MataUjiEdt(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].setupmatauji.edt.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=440, height=380, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

function DftrMataUji() {
  MataUjiEdtScript();
  $s = "select *
    from matrimatauji
    where KodeID = '".KodeID."'
    order by MatriMataUjiID";
  $r = _query($s);
  $n = 0;
  
  echo "<p><table class=box cellspacing=1 align=center width=500>";
  echo "<tr>
    <td class=ul1 colspan=5>
    <input type=button name='Tambah' value='Tambah Mata Uji'
      onClick=\"javascript:MataUjiEdt(1, '', '$_SESSION[mnux]')\" />
    <input type=button name='Refresh' value='Refresh'
      onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />
    </td>
    </tr>";
  echo "<tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Ujian</th>
    <th class=ttl>Keterangan</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
      <td class=inp width=20>$n</td>
      <td class=ul1 width=10>
        <a href='#' onClick=\"javascript:MataUjiEdt(0, '$w[MatriMataUjiID]', '$_SESSION[mnux]')\"><img src='img/edit.png' /></a>
        </td>
      <td $c width=60>$w[MatriMataUjiID]&nbsp;</td>
      <td $c>$w[Nama]&nbsp;</td>
      <td $c>$w[Keterangan]&nbsp;</td>
      <td class=ul1 width=10><img src='img/book$w[NA].gif' /></td>
      </tr>";
  }
  echo "</table></p>";
}
?>