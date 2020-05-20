<?php
// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrUSM' : $_REQUEST['sub'];
$sub();

// *** functions ***
function USMEdtScript() {
  echo <<<SCR
  <script>
  function USMEdt(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].usm.edit.php?md="+MD+"&Kode="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=440, height=380, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

function DftrUSM() {
  USMEdtScript();
  $s = "select *
    from pmbusm
    where KodeID = '".KodeID."'
    order by PMBUSMID";
  $r = _query($s);
  $n = 0;
  
  echo "<p><table class=box cellspacing=1 align=center width=500>";
  echo "<tr>
    <td class=ul1 colspan=5>
    <input type=button name='Tambah' value='Tambah Komponen'
      onClick=\"javascript:USMEdt(1, '', '$_SESSION[mnux]')\" />
    <input type=button name='Refresh' value='Refresh'
      onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />
    </td>
    </tr>";
  echo "<tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Ujian</th>
	<th class=ttl>Cara<br>Penempatan</th>
    <th class=ttl>Keterangan</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
      <td class=inp width=20>$n</td>
      <td class=ul1 width=10>
        <a href='#' onClick=\"javascript:USMEdt(0, '$w[PMBUSMID]', '$_SESSION[mnux]')\"><img src='img/edit.png' /></a>
        </td>
      <td $c width=60>$w[PMBUSMID]&nbsp;</td>
      <td $c>$w[Nama]&nbsp;</td>
	  <td $c>$w[CaraPenempatan]&nbsp;</td>
      <td $c>$w[Keterangan]&nbsp;</td>
      <td class=ul1 width=10><img src='img/book$w[NA].gif' /></td>
      </tr>";
  }
  echo "</table></p>";
}
