<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Formulir");

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0;
$max = sqling($_REQUEST['max']);

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $max);

// *** Functions ***
function Edit($md, $id, $max) {
  if ($md == 0) {
    $jdl = "Edit Quota Beasiswa";
    $w = GetFields('quotabeasiswa', "KodeID='".KodeID."' and MaxQuotaID", $id, "*");
  }
  elseif ($md == 1) {
    $jdl = "Tambah Quota Beasiswa";
    $w = array();
    $w['NA'] = 'N';
  }
  else die(ErrorMsg('Error', "Mode edit tidak dikenali."));
  
  TampilkanJudul($jdl);
  // Parameters
  $gel = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', 'PMBPeriodID');
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $usmchecked = ($w['USM'] == 'USM')? 'checked' : '' ;
  //$TestMasuk = GetTestMasuk($w['USM']);
  echo "<p><table class=bsc cellspacing=1 align=center width=100%>
  <form action='../$_SESSION[mnux].edt.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='max' value='$max' />
  
  <tr><td class=inp>Gelombang:</td>
      <td class=ul1><input type=hidden name='gel' value='$gel'><b>$gel</b></td></tr>
  <tr><td class=inp>Prodi:</td>
      <td class=ul1><input type=hidden name='prodi' value='$_SESSION[ProdiID]'><b>$_SESSION[ProdiID]</b></td></tr>
  <tr><td class=inp>Nilai:</td>
      <td class=ul1><input type=text name='DariNilai' value='$w[DariNilai]' size=3 maxlength=6 /> s/d <b>$max</b></td>
      </tr>
  <tr><td class=inp>Diskon:</td>
      <td class=ul1><input type=text name='Diskon' value='$w[Diskon]' size=3 maxlength=6 /> %</td>
      </tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul1>
      <textarea name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea>
      </td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
      <td class=ul1>
      <input type=checkbox name='NA' value='Y' $na /> *) Beri centang jika tidak aktif
      </td>
      </tr>
  
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td>
      </tr>
  </form>
  </table></p>";
}

function Simpan($md, $id, $max) {
  TutupScript();
  $gel = $_REQUEST['gel'];
  $prodi = $_REQUEST['prodi'];
  $DariNilai = $_REQUEST['DariNilai']+0;
  $Diskon = $_REQUEST['Diskon']+0;
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : 'Y';
  
  if ($md == 0) {
    $DariNilaiLama = GetaField('quotabeasiswa', "KodeID = '".KodeID."' and MaxQuotaID", $id, 'DariNilai');
	$cari = GetaField('quotabeasiswa', "PMBPeriodID='$gel' and ProdiID='$prodi' and SampaiNilai='$DariNilaiLama' and KodeID", KodeID, 'MaxQuotaID');
	if(!empty($cari))
	{	$s = "update quotabeasiswa set SampaiNilai='$DariNilai' where MaxQuotaID='$cari'";
		$r = _query($s);
	}
	
	$s = "update quotabeasiswa
      set DariNilai = '$DariNilai',
		  Diskon = '$Diskon',
          Keterangan = '$Keterangan',
          NA = '$NA',
          LoginEdit = '$_SESSION[_Login]',
          TanggalEdit = now()
      where KodeID = '".KodeID."' and MaxQuotaID = $id ";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux]');</script>";
  }
  elseif ($md == 1) {
	$cari = GetaField('quotabeasiswa', "PMBPeriodID='$gel' and ProdiID='$prodi' and SampaiNilai='$max' and KodeID", KodeID, 'MaxQuotaID');
	if(!empty($cari))
	{	$s = "update quotabeasiswa set SampaiNilai='$DariNilai' where MaxQuotaID='$cari'";
		$r = _query($s);
	}
	
	$s = "insert into quotabeasiswa
      (PMBPeriodID, ProdiID, KodeID, DariNilai, SampaiNilai, Diskon,
      Keterangan, LoginBuat, TanggalBuat, NA)
      values
      ('$gel', '$prodi', '".KodeID."', '$DariNilai', '$max', '$Diskon',
      '$Keterangan', '$_SESSION[_Login]', now(), '$NA')";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux]');</script>";
  }
  else die(ErrorMsg('Error', "Mode edit tidak ditemukan."));
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup(bck) {
    opener.location='../index.php?mnux=$_SESSION[mnux]';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}
?>
