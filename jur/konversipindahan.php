<?php
// *** Parameters ***
$MhswID = GetSetVar('MhswID');
$mhsw = GetFields('mhsw', "MhswID = '$MhswID' and KodeID", KodeID, "*");

// *** Main ***
TampilkanJudul("Konversi Matakuliah Mahasiswa Pindahan");
TampilkanAmbilMhswID($MhswID, $mhsw);

if ($MhswID == '') {
  echo Konfirmasi("Masukkan Parameter",
    "Masukkan NPM dari Mahasiswa pindahan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.");
}
// Cek apakah mahasiswanya ketemu atau tidak
elseif (empty($mhsw)) {
  echo ErrorMsg("Error",
    "Mahasiswa dengan NPM: <b>$MhswID</b> tidak ditemukan.<br />
    Masukkan NPM yang sebenarnya.
    <hr size=1 color=silver />
    Hubungi Sysadmin untuk informasi lebih lanjut.");
}
/* sementara ditutup utk proses clustering di BinaInsani
elseif ($mhsw['Keluar'] == 'Y') {
  echo ErrorMsg("Error",
    "Mahasiswa dengan NIM/NPM: <b>$MhswID</b> telah keluar/lulus.<br />
    Anda sudah tidak dapat mengubah konversi.
    <hr size=1 color=silver />
    Hubungi Sysadmin untuk informasi lebih lanjut.");
}
*/
else {
  // Cek apakah punya hak akses terhadap mhsw dari prodi ini?
  if (strpos($_SESSION['_ProdiID'], $mhsw['ProdiID']) === false) {
    echo ErrorMsg("Error",
      "Anda tidak memiliki hak akses terhadap mahasiswa ini.<br />
      Mahasiswa: <b>$MhswID</b>, Prodi: <b>$mhsw[ProdiID]</b>.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.");
  }
  // hak akses oke
  else {
    // Cek apakah mahasiswa pindahan atau bukan
    if ($mhsw['StatusAwalID'] == 'P' || $mhsw['StatusAwalID'] == 'D') {
      $gos = (empty($_REQUEST['gos']))? 'DftrMK' : $_REQUEST['gos'];
      $gos($MhswID, $mhsw);
    }
    // Jika bukan, maka tampilkan pesan error
    else {
      echo ErrorMsg("Error",
        "Mahasiswa ini bukan mahasiswa pindahan/drop-in.<br />
        Anda tidak bisa melakukan konversi pindaha.
        <hr size=1 color=silver />
        Hubungi Sysadmin untuk informasi lebih lanjut.");
    }
  }
}

// *** Functions ***
function GetOptionsFromData($sourceArray, $chosen)
		{	
			$optresult = "";
			if($chosen == '' or empty($chosen))	
			{ 	$optresult .= "<option value='' selected></option>"; }
			else { $optresult .= "<option value=''></option>"; }
			for($i=0; $i < count($sourceArray); $i++)
			{	if($chosen == $sourceArray[$i])
				{	$optresult .= "<option value='$sourceArray[$i]' selected>$sourceArray[$i]</option>"; }
				else
				{ 	$optresult .= "<option value='$sourceArray[$i]'>$sourceArray[$i]</option>"; }
			}
			return $optresult;
		}

function SaveAll($MhswID, $mhsw)
{

	/*$si = "select k.*
    from krs k
      left outer join khs h on h.KHSID = k.KHSID and h.KodeID = '".KodeID."'
    where k.MhswID = '$MhswID'
    order by k.TahunID, k.MKKode";
	$ri = _query($si); 
	$ni = _num_rows($ri);
	
	while($wi=_fetch_array($ri))
	{	$tempNum = substr($wi[MKKode],6,1);
		
		if($tempNum==1)	$theYear = $InputTahun1.$tempNum;
		else if($tempNum==2) $theYear = $InputTahun1.$tempNum;
		else if($tempNum==3) $theYear = ($InputTahun1+1).($tempNum-2);
		else if($tempNum==4) $theYear = ($InputTahun1+1).($tempNum-2);
		else if($tempNum==5) $theYear = ($InputTahun1+2).($tempNum-4);
		else if($tempNum==6) $theYear = ($InputTahun1+2).($tempNum-4);
		else if($tempNum==7) $theYear = ($InputTahun1+3).($tempNum-6);
		else if($tempNum==8) $theYear = ($InputTahun1+3).($tempNum-6);
		else $theYear = '';
		
		//echo "$wi[MKKode]: $theYear<br>";
		
		$ss = "update `krs` set 
					TahunID='$theYear', 
					Setara = 'Y',
					SetaraKode = 'a',
					SetaraNama = 'a',
					SetaraGrade = 'a',
					Sah = 'Y',
					Final = 'Y',
					LoginEdit = '$_SESSION[_Login]',
					TanggalEdit = now()  
				where KRSID = '$wi[KRSID]'";
		$rr = _query($ss);
	}*/
	
	$Siska = array(
				$_REQUEST['Select1'],
				$_REQUEST['Select2'],
				$_REQUEST['Select3'],
				$_REQUEST['Select4'],
				$_REQUEST['Select5'],
				$_REQUEST['Select6'],
				$_REQUEST['Select7'],
				$_REQUEST['Select8'],
				$_REQUEST['Select9'],
				$_REQUEST['Select10'],
				$_REQUEST['Select11'],
				$_REQUEST['Select12'],
				$_REQUEST['Select13'],
				$_REQUEST['Select14'],
				$_REQUEST['Select15'],
				$_REQUEST['Select16'],
				$_REQUEST['Select17'],
				$_REQUEST['Select18'],
				$_REQUEST['Select19'],
				$_REQUEST['Select20'],
				$_REQUEST['Select21'],
				$_REQUEST['Select22'],
				$_REQUEST['Select23'],
				$_REQUEST['Select24'],
				$_REQUEST['Select25'],
				$_REQUEST['Select26'],
				$_REQUEST['Select27'],
				$_REQUEST['Select28'],
				$_REQUEST['Select29'],
				$_REQUEST['Select30'],
				$_REQUEST['Select31'],
				$_REQUEST['Select32'],
				$_REQUEST['Select33'],
				$_REQUEST['Select34'],
				$_REQUEST['Select35'],
				$_REQUEST['Select36'],
				$_REQUEST['Select37'],
				$_REQUEST['Select38'],
				$_REQUEST['Select39'],
				$_REQUEST['Select40'],
				$_REQUEST['Select41'],
				$_REQUEST['Select42'],
				$_REQUEST['Select43'],
				$_REQUEST['Select44'],
				$_REQUEST['Select45'],
				$_REQUEST['Select46'],
				$_REQUEST['Select47'],
				$_REQUEST['Select48'],
				$_REQUEST['Select49'],
				$_REQUEST['Select50'],
				$_REQUEST['Select51'],
				$_REQUEST['Select52'],
				$_REQUEST['Select53'],
				$_REQUEST['Select54'],
				$_REQUEST['Select55'],
				$_REQUEST['Select56'],
				$_REQUEST['Select57'],
				$_REQUEST['Select58'],
				$_REQUEST['Select59'],
				$_REQUEST['Select60'],
				$_REQUEST['Select61'],
				$_REQUEST['Select62'],
				$_REQUEST['Select63'],
				$_REQUEST['Select64'],
				$_REQUEST['Select65'],
				$_REQUEST['Select66'],
				$_REQUEST['Select67'],
				$_REQUEST['Select68'],
				$_REQUEST['Select69'],
				$_REQUEST['Select70']);
	$Siska2 = array(
				$_REQUEST['Hidden1'],
				$_REQUEST['Hidden2'],
				$_REQUEST['Hidden3'],
				$_REQUEST['Hidden4'],
				$_REQUEST['Hidden5'],
				$_REQUEST['Hidden6'],
				$_REQUEST['Hidden7'],
				$_REQUEST['Hidden8'],
				$_REQUEST['Hidden9'],
				$_REQUEST['Hidden10'],
				$_REQUEST['Hidden11'],
				$_REQUEST['Hidden12'],
				$_REQUEST['Hidden13'],
				$_REQUEST['Hidden14'],
				$_REQUEST['Hidden15'],
				$_REQUEST['Hidden16'],
				$_REQUEST['Hidden17'],
				$_REQUEST['Hidden18'],
				$_REQUEST['Hidden19'],
				$_REQUEST['Hidden20'],
				$_REQUEST['Hidden21'],
				$_REQUEST['Hidden22'],
				$_REQUEST['Hidden23'],
				$_REQUEST['Hidden24'],
				$_REQUEST['Hidden25'],
				$_REQUEST['Hidden26'],
				$_REQUEST['Hidden27'],
				$_REQUEST['Hidden28'],
				$_REQUEST['Hidden29'],
				$_REQUEST['Hidden30'],
				$_REQUEST['Hidden31'],
				$_REQUEST['Hidden32'],
				$_REQUEST['Hidden33'],
				$_REQUEST['Hidden34'],
				$_REQUEST['Hidden35'],
				$_REQUEST['Hidden36'],
				$_REQUEST['Hidden37'],
				$_REQUEST['Hidden38'],
				$_REQUEST['Hidden39'],
				$_REQUEST['Hidden40'],
				$_REQUEST['Hidden41'],
				$_REQUEST['Hidden42'],
				$_REQUEST['Hidden43'],
				$_REQUEST['Hidden44'],
				$_REQUEST['Hidden45'],
				$_REQUEST['Hidden46'],
				$_REQUEST['Hidden47'],
				$_REQUEST['Hidden48'],
				$_REQUEST['Hidden49'],
				$_REQUEST['Hidden50'],
				$_REQUEST['Hidden51'],
				$_REQUEST['Hidden52'],
				$_REQUEST['Hidden53'],
				$_REQUEST['Hidden54'],
				$_REQUEST['Hidden55'],
				$_REQUEST['Hidden56'],
				$_REQUEST['Hidden57'],
				$_REQUEST['Hidden58'],
				$_REQUEST['Hidden59'],
				$_REQUEST['Hidden60'],
				$_REQUEST['Hidden61'],
				$_REQUEST['Hidden62'],
				$_REQUEST['Hidden63'],
				$_REQUEST['Hidden64'],
				$_REQUEST['Hidden65'],
				$_REQUEST['Hidden66'],
				$_REQUEST['Hidden67'],
				$_REQUEST['Hidden68'],
				$_REQUEST['Hidden69'],
				$_REQUEST['Hidden70']);
	
	$x=0;
	while(!empty($Siska[$x]))
	{	
		//echo "Select: $Siska[$x], KRSID: $Siska2[$x]<br>";		
		$ss = "update `krs` set 
					TahunID='$Siska[$x]', 
					Setara ='Y', 
					SetaraKode = 'a',
					SetaraNama = 'a',
					SetaraGrade = 'a',
					Sah = 'Y',
					Final = 'Y',
					LoginEdit = '$_SESSION[_Login]',
					TanggalEdit = now()  
					where KRSID='$Siska2[$x]'";
		$rr = _query($ss);
		$x++;
	}
	BerhasilSimpan("?mnux=$_SESSION[mnux]", 1000);
}			
		
function TampilkanAmbilMhswID($MhswID, $mhsw) {
  $stawal = GetaField('statusawal', 'StatusAwalID', $mhsw['StatusAwalID'], 'Nama');
  $status = GetaField('statusmhsw', 'StatusMhswID', $mhsw['StatusMhswID'], 'Nama');
  if (empty($mhsw['PenasehatAkademik'])) {
    $pa = '<sup>Belum diset</sup>';
  }
  else {
    $dosenpa = GetFields('dosen', "Login='$mhsw[PenasehatAkademik]' and KodeID", KodeID, "Nama, Gelar");
    $pa = "$dosenpa[Nama] <sup>$dosenpa[Gelar]</sup>";
  } 
    
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form name='frmMhsw' action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  
  <tr><td class=wrn width=2 rowspan=4></td>
      <td class=inp width=80>NIM/NPM:</td>
      <td class=ul width=200>
        <input type=text name='MhswID' value='$MhswID' size=20 maxlength=50 />
        <input type=submit name='btnCari' value='Cari' />
        </td>
      <td class=inp width=80>Mahasiswa:</td>
      <td class=ul>$mhsw[Nama]&nbsp;</td>
      </tr>
  <tr><td class=inp>Status Mhsw:</td>
      <td class=ul>$status <sup>$stawal</sup></td>
      <td class=inp>Dosen PA:</td>
      <td class=ul>$pa</td>
  </form>
  </table>
ESD;
}
function DftrMK($MhswID, $mhsw) {
  $s = "select k.*
    from krs k
      left outer join khs h on h.KHSID = k.KHSID and h.KodeID = '".KodeID."'
    where k.MhswID = '$MhswID'
    order by k.TahunID, k.MKKode";
  $r = _query($s); $_tahun = 'alksdjfasdf-asdf';
  $n = _num_rows($r);
  echo <<<ESD
  <table class=box cellspacing=1 width=600 align=center>
		<form action='?' method=POST>
		<input type=hidden name='mnux' value='$_SESSION[mnux]'>
		<input type=hidden name='gos' value='SaveAll'>
ESD;

	$arrSemuaTahun = array('2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009');
	$optSemuaTahun = GetOptionsFromData($arrSemuaTahun, '');
	
	 echo "<tr>
        <td class=ul1 colspan=2>
          <input type=button name='btnTambah' value='+ Tambah MK' onClick=\"javascript:fnEditKonversi(1, '$mhsw[MhswID]', '', 0)\" />
        </td>
		</tr>";

  /*echo "<tr>
        <td class=ul1 colspan=2>
          <input type=button name='btnTambah' value='+ Tambah MK' onClick=\"javascript:fnEditKonversi(1, '$mhsw[MhswID]', '', 0)\" />
        </td>
		<td class=inp>Masukkan Tahun Bila Kode -1- adalah Tahun:
		<td class=ul1><select name='InputTahun1'/>$optSemuaTahun</select><td>
		<td class=ul1><input type=button name='InputSemuaTahun' value='Input Semua Tahun' 
			onClick ='this.form.submit()' /></td>
		</tr>";*/
  $hdr = "<tr><th class=ttl width=20>Nmr</th>
    <th class=ttl width=90>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl width=30>SKS</th>
    <th class=ttl width=30>Nilai</th>
	<th class=ttl width=30>Tahun</th>
    <th class=ttl width=30>Edit</th>
	</tr>";

	$m = 0;	
  while ($w = _fetch_array($r)) {
    if ($_tahun != $w['TahunID']) {
      $_tahun = $w['TahunID'];
      echo "<tr>
        <td class=ul1 colspan=10>
          <font size=+1>$_tahun</font>
          <!--<input type=button name='btnTambah' value='+ Tambah MK' onClick=\"javascript:fnEditKonversi(1, '$w[MhswID]', '$_tahun', 0)\" />-->
        </td></tr>";
      echo $hdr;
      $n = 0;
    }
    $n++;
	$m++;
    if ($w['Setara'] == 'Y') {
      $btnEdit = "<input type=button name='btnEdit' value='»'
        onClick=\"fnEditKonversi(0, '$w[MhswID]', '$w[TahunID]', $w[KRSID])\" />";
    }
    else {
      $btnEdit = "<abbr title='Bukan Konversi'><img src='img/flag2.gif' /></abbr>";
    }
	
	$arrTahun = array('20031','20032','20041','20042','20051','20052','20061','20062', '20071', '20072', '20081', '20082', '20091', '20092', '20101', '20102');
	$optTahun = GetOptionsFromData($arrTahun, $w['TahunID']);
	
	//echo "Select: Select$m<br>";
	
	echo <<<ESD
    <tr>
      <td class=inp>$n</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=right>$w[SKS]</td>
      <td class=ul align=center>$w[GradeNilai]
	  <td class=ul align=center><select name='Select$m'>$optTahun</select>
			<input type=hidden name='Hidden$m' value='$w[KRSID]'/></td>
	  <td class=ul align=center>
        $btnEdit
        </td>
    </tr>
ESD;
  }
  RandomStringScript();
 
  echo "<tr>
		<td class=ul1 colspan=6>
			<input type=button name='SaveData' value='Save Data'
			onClick='this.form.submit()' /></td>
	</tr>";
	
  echo <<<ESD
  
	
  </form>
  </table>
  
  <script>
  function fnEditKonversi(md, mhsw, thn, id) {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].edit.php?mhsw="+mhsw+"&md="+md+"&id="+id+"&thn="+thn+"&_rnd="+_rnd+"&ProdiID=$mhsw[ProdiID]";
      win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  
  }

  </script>
ESD;

}
?>
