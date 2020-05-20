<?php
// *** Parameters ***
$MhswID = GetSetVar('MhswID');
$mhsw = GetFields("mhsw m
      left outer join dosen d on m.PenasehatAkademik = d.Login and d.KodeID='".KodeID."'
      left outer join prodi prd on prd.ProdiID = m.ProdiID and prd.KodeID='".KodeID."'
      left outer join program prg on prg.ProgramID = m.ProgramID and prg.KodeID='".KodeID."'
      ",
      "m.KodeID='".KodeID."' and m.MhswID", $MhswID,
      "m.*, prd.Nama as _PRD, prg.Nama as _PRG,
      d.Nama as DSN, d.Gelar");

// *** Main ***
TampilkanJudul("Koreksi Nilai Mahasiswa");
TampilkanHeaderMhsw($MhswID, $mhsw);
$gos = (empty($_REQUEST['gos']))? "EditNilaiMhsw" : $_REQUEST['gos'];
if (!empty($mhsw)) $gos($MhswID, $mhsw);


// *** Functions ***
function TampilkanHeaderMhsw($MhswID, $w) {
  echo <<<ESD
  <table class=box cellspacing=1 width=700>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <tr><td class=inp width=100>NPM:</td>
      <td class=ul width=210>
        <input type=text name='MhswID' value='$_SESSION[MhswID]' size=12 maxlength=20 />
        <input type=submit name='Ambil' value='Get Data' />
        </td>
      <td class=inp width=100>Mahasiswa:</td>
      <td class=ul><b>$w[Nama]</b>&nbsp;</td>
      </tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul>$w[_PRD] <sup>$w[ProdiID]</sup>&nbsp;</td>
      <td class=inp>Prg. Pendidikan:</td>
      <td class=ul>$w[_PRG] <sup>$w[ProgramID]</sup>&nbsp;</td>
      </tr>
  <tr><td class=inp>Penasehat Akd:</td>
      <td class=ul>$w[DSN] <sup>$w[Gelar]</sup>&nbsp;</td>
      <td class=inp>Masa Studi:</td>
      <td class=ul>$w[TahunID] &#8594; $w[BatasStudi]</td>
      </tr>
  </form>
  </table>
ESD;
}
function EditNilaiMhsw($MhswID, $mhsw) {
  $whr = '';
  $s = "select k.*,
      @KOR := (select count(kn.KoreksiNilaiID)
      from koreksinilai kn
      where kn.KRSID = k.KRSID),
      if (@KOR = 0, '&nbsp;', concat(@KOR, '&times;')) as JML
    from krs k
    where k.KodeID = '".KodeID."'
      and k.MhswID = '$MhswID'
	  and k.NA='N'
	  $whr
    order by k.TahunID, k.MKKode";
  $r = _query($s);
  $n = 0; $_thn = 'laskdfj'; $sks = 0;
  $hdr = "<tr>
    <th class=ttl width=30>#</th>
    <th class=ttl width=80>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl width=20>SKS</th>
    <th class=ttl width=20>Nilai</th>
    <th class=ttl width=30>Koreksi</th>
    </tr>";
  echo "<table class=box cellspacing=1 width=700 align=center>";
  while ($w = _fetch_array($r)) {
    if ($_thn != $w['TahunID']) {
      $_thn = $w['TahunID'];
      echo "<tr>
        <td class=ul1 colspan=10>Thn Akd: <font size=+1>$w[TahunID]</font></td>
        </tr>";
      echo $hdr;
      $n = 0;
    }
    $n++;
    // Detail
      $c = 'class=ul';
      $sks += $w['SKS'];
    if ($w['BobotNilai'] == 0) {
      $Nilai = '&times;';
    }
    else {
      $Nilai = "$w[GradeNilai] <sup>$w[BobotNilai]</sup>";
    }
	$koreksi = _query("SELECT concat(GradeLama,' ke ',GradeNilai,' oleh ', d.Nama, ' melalui modul ',k.Modul,'. Keterangan: ', k.Keterangan,', ',k.Perihal) as Koreksi
						from koreksinilai k 
            left outer join dosen d on k.LoginBuat=d.Login
            left outer join karyawan kr on k.LoginBuat=kr.Login
						where k.KRSID='".$w['KRSID']."'");
	$textKoreksin = '';
	while ($_koreksi = _fetch_array($koreksi)) {
		$textKoreksin .= $_koreksi['Koreksi']."<hr>";
	}		 
	$textKoreksi = $textKoreksin ? "data-rel='popover' title='Perubahan Nilai' data-content='$textKoreksin'":'';
    echo <<<ESD
    <tr><td class=inp>$n</td>
        <td $c>$w[MKKode]</td>
        <td $c>$w[Nama]</td>
        <td $c align=right>$w[SKS]</td>
        <td $c align=center>$Nilai</td>
        <td $c align=right>
          $w[JML]&nbsp;
          <a href='#' onClick="javascript:Edit($w[KRSID])" $textKoreksi><img src='img/edit.png' /></a>
          </td>
        </tr>
ESD;
  }
  echo <<<ESD
  <tr>
    <td class=ul colspan=3 align=right>Total SKS:</td>
    <td class=ul align=right><font size=+1>$sks</font></td>
    </tr>
  </table>
  
  <script>
  <!--
  function Edit(krsid) {
    lnk = "$_SESSION[mnux].edit.php?KRSID="+krsid;
    win2 = window.open(lnk, "", "width=500, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
ESD;
}

?>
