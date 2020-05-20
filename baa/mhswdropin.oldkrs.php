<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 12/12/2008

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Mhsw Pindahan");

// *** Parameters ***
$MhswID = GetSetVar('MhswID');
$mhsw = GetFields('mhsw', "MhswID='$MhswID' and KodeID", KodeID, '*');
if (empty($mhsw))
  die(ErrorMsg('Error',
  "Data mahasiswa lama tidak ditemukan.<br />
  Hubungi Sysadmin untuk informasi lebih lanjut"));

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'DftrKRSLama' : $_REQUEST['gos'];
$gos($MhswID, $mhsw);

// *** Functions ***
function DftrKRSLama($MhswID, $mhsw) {
  $s = "select k.*
    from krs k
    where k.MhswID = '$mhsw[PMBID]'
    order by k.TahunID";
  $r = _query($s);
  
  RandomStringScript();
  echo "<table class=bsc cellspacing=1 width=100%>
    <tr>
        <th class=ttl width=20><abbr title='Konversikan'>Konv.</abbr></th>
        <th class=ttl width=20>#</th>
        <th class=ttl width=80>Kode &rsaquo; SKS</th>
        <th class=ttl>Matakuliah</th>
        </tr>";
  $thn = 'alksdjflasdjfhasd';
  while ($w = _fetch_array($r)) {
    if ($thn != $w['TahunID']) {
      $thn = $w['TahunID'];
      echo "<tr>
        <td class=ul1 colspan=5><font size=+1>$thn</td>
        </tr>";
    }
    $n++;
    if ($w['StatusKRSID'] == 'K') {
      $c = "class=nac";
      $konv = "<abbr title='Sudah dikonversikan'>&times;</a>";
    }
    else {
      $c = "class=ul1";
      $konv = "<input type=button name='btnKonversi' value='<' onClick=\"javascript:Konversikan($w[KRSID], '$MhswID')\" />";
    }
    echo "<tr>
      <td class=ul align=center>$konv</td>
      <td class=inp>$n</td>
      <td $c>$w[MKKode]<sup>$w[SKS]</sup></td>
      <td $c>$w[Nama]</td>
      </tr>";
  }
  echo <<<ESD
  </table>
  
  <script>
  function Konversikan(krsid, mhswid) {
    _rnd = randomString();
    lnk = "../$_SESSION[mnux].konversikan.php?KRSID="+krsid+"&MhswID="+mhswid+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function RefreshParent() {
    parent.location="../index.php?mnux=$_SESSION[mnux]&gos=fnKonversi&MhswID=$MhswID";
  }
  </script>
  
ESD;
}

?>

</BODY>
</HTML>
