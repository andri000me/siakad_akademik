<?php
// Author 	: Arisal Yanuarafi
// Start	: 19 Maret 2012
// Kliring Nilai

$mhswid = GetSetVar('MhswID');
$_KurikulumID = GetSetVar('_KurikulumID');
$mhsw = GetFields("mhsw m
      left outer join dosen d on m.PenasehatAkademik = d.Login and d.KodeID='".KodeID."'
      left outer join prodi prd on prd.ProdiID = m.ProdiID and prd.KodeID='".KodeID."'
      left outer join program prg on prg.ProgramID = m.ProgramID and prg.KodeID='".KodeID."'",
	  "m.KodeID='".KodeID."' and m.MhswID", $mhswid,
      "m.*, prd.Nama as _PRD, prg.Nama as _PRG,
      d.Nama as DSN, d.Gelar");

// *** Main ***
TampilkanJudul("Kliring Nilai Mahasiswa");
TampilkanHeaderMhsw($mhswid, $mhsw);
$gos = (empty($_REQUEST['gos']))? "EditNilaiMhsw" : $_REQUEST['gos'];
if (!empty($mhsw)) $gos($mhswid, $mhsw);

// *** Functions ***
function TampilkanHeaderMhsw($mhswid, $w) {
  echo <<<ESD
  <table class=box cellspacing=1 width=800>
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
function EditNilaiMhsw($mhswid, $mhsw) {
	$n=0;
	$mhs = GetFields('mhsw', "MhswID", $mhswid, '*');
	$mhstgl = GetFields('mhsw', "MhswID", $mhswid, "date_format(TanggalLahir, '%d') as Tgl, date_format(TanggalLahir, '%m') as _BlnLahir, date_format(TanggalLahir, '%Y') as Thn");

	$program = GetaField ('mhsw',"MhswID", $mhswid, 'ProgramID');
	$prog = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $mhs['ProdiID'], 'j.Nama as NMProg');
echo "<form name='ProdiID' action='?' method=POST><table width=800 class=box><tr><td class=inp>Kurikulum</td><td>";
   $s6 = "select KurikulumID,KurikulumKode,Nama
    from kurikulum
    where ProdiID = '$mhs[ProdiID]' order by Nama";
$r6 = _query($s6);
	  $optkurikulum = "<option value=''></option>";
	  while($w6 = _fetch_array($r6))
		{  $ck = ($w6['KurikulumID'] == $_SESSION['_KurikulumID'])? "selected" : '';
		   $optkurikulum .=  "<option value='$w6[KurikulumID]' $ck>$w6[Nama]</option>";
		}
	  $_inputKurikulum = "<select name='_KurikulumID' onChange='this.form.submit()'>$optkurikulum</select>";
   echo"$_inputKurikulum <font color=red> *) Tentukan Kurikulum</font></td></tr></table></form>";    	
echo "<center><font size=4 id=header><strong>DAFTAR NILAI</strong></font></center>";
echo "<table border=0 width=800 align=center><tr><td>";
echo "<form action='?' method=POST>
 <input type=hidden name='gos' value='NilaiMhswSimpan' />";
 	if (!empty($_SESSION['_KurikulumID'])) $whrKurikulum="And m.KurikulumID='$_SESSION[_KurikulumID]'";
	else $whrKurikulum=='';
//tabel sisi kiri
echo "<table border=0 width=395 align=left><tr class=ttl>";
echo "<td class=ttl>Kode</strong></td>";
echo "<td class=ttl>Matakuliah</strong></td>";
echo "<td class=ttl>SKS</strong></td>";
echo "<td class=ttl>Nilai</strong></td>";

$totalmutu=0;
$totalsks=0;
if ($program=='R' || $program=='P' || $program=='J' || $program=='M') {
	if ($prog['NMProg']=='S1') {	$SesiKiri=4;	$SesiKanan=8;	}
	else {						$SesiKiri=4;	$SesiKanan=8; 	}
}
else {
	if ($prog['NMProg']=='S1') {	$SesiKiri=2; 	$SesiKanan=3;	}
	else {						$SesiKiri=4;	$SesiKanan=8;	}
}

while ($sesi<$SesiKiri) {
$sesi++;
if ($sesi==1) { $_sesi='I'; }
elseif ($sesi==2) { $_sesi='II'; }
elseif ($sesi==3) { $_sesi='III'; }
elseif ($sesi==4) { $_sesi='IV'; }
elseif ($sesi==5) { $_sesi='V'; }
if ($program=='R' || $program=='P' || $program=='J' || $program=='M' || $prog['NMProg']=='D3') {
$s = "select m.MKKode,m.Nama,m.SKS,m.MKID
    from mk m
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$mhs[ProdiID]'
	  And m.Sesi=$sesi
      and m.NA = 'N'
	  $whrKurikulum
    order by m.MKKode";
}
$r=_query($s);
$jmlsks=0;
$jmlmutu=0;
$lg=0;
echo "<tr><td colspan=5><strong>Semester $_sesi</font></td></tr>";
	while ($w=_fetch_array($r)) {
	$lg++;
	if ($lg==1) {
		echo "<tr bgcolor=lightgrey>";
		$lg=$lg-2;
	}
	else {
	echo "<tr>";
	}
	
		echo "<td>$w[MKKode]</td>";
		echo "<td>$w[Nama]</td>";
		echo "<td align=center>$w[SKS]</td>";
		
		$krs=GetFields('krs',"MKKode='$w[MKKode]' and Tinggi='*' and MhswID",$mhs['MhswID'],"*,Min(GradeNilai) as GradeNilai");
		if (!empty($krs['KRSID'])) {
		$id=$krs['KRSID']+0;
		$selectall="SelectAll('Nilai_".$id."')";
		$optnilai = ($krs['NA']=='Y')? "<option></option>":GetOption2('nilai', "Nama", 'Bobot desc',
    $krs['BobotNilai'], "KodeID='".KodeID."' and ProdiID='$mhs[ProdiID]'", 'Bobot');
		$optnilai .= ($krs['NA']=='Y')? "<option value='X' selected>Non Aktif</option><option value='N'>Aktifkan</option>" : "<option value='X'>Non Aktif</option>";
		echo "<input type=hidden name='krsid[]' value='$krs[KRSID]' />
      	<input type=hidden name='KRS_$id' value='".$krs['KRSID']."' />";
		
		echo "<td align=center><select name='Nilai_$id' class='nones' id='Nilai_$id' data-noty-options='{\"text\":\"Data telah disimpan\",\"layout\":\"topCenter\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}' onChange=\"javascript:ajaxSave('master/ajx/ajxsave.kliringnilai','UPDATE',this,'$krs[KRSID]','$mhs[MhswID]', 'Nilai_$id')\">$optnilai</select></td></tr>";
		$mutu=$w['SKS']*$krs['BobotNilai']+0;
		$totalmutu=($totalmutu + $mutu)+0;
		$totalsks=($totalsks + $w['SKS'])+0;
		$jmlsks=($jmlsks+$w['SKS'])+0;
		$jmlmutu=($jmlmutu+$mutu)+0;
		}
		else {
		$n++;
		$id=$n+0;
		$selectall="SelectAll('Nilai_".$id."')";
		echo "<input type=hidden name='n[]' value='$n' />";
		echo "<input type=hidden name='MKID_$n' value='$w[MKID]' />";
		echo "<input type=hidden name='MKKode_$n' value='$w[MKKode]' />";
		echo "<input type=hidden name='SKS_$n' value='$w[SKS]' />";
		echo "<input type=hidden name='Nama_$n' value='$w[Nama]' />";
		$optnilai = GetOption2('nilai', "Nama", 'Bobot desc',
    $krs['BobotNilai'], "KodeID='".KodeID."' and ProdiID='$mhs[ProdiID]'", 'Bobot');
		
		echo "<td align=center><select name='Nilai_$id' class='nones' id='Nilai_$id' data-noty-options='{\"text\":\"Data telah disimpan\",\"layout\":\"topCenter\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}' onChange=\"javascript:ajaxSave('master/ajx/ajxsave.kliringnilai','INSERT',this,'$w[MKID]','$mhs[MhswID]','Nilai_$id')\">$optnilai</select></td></tr>";
		}

	}
	echo "<tr><td colspan=2 align=right><b>Jumlah :</td><td align=center><b>$jmlsks</b></td><td align=center>
	<b>$jmlmutu</td></tr>";
}
echo "</table>";

//tabel sisi kanan
echo "<table border=0 width=395 align=right><tr class=ttl>";
echo "<td class=ttl>Kode</strong></td>";
echo "<td class=ttl>Matakuliah</strong></td>";
echo "<td class=ttl>SKS</strong></td>";
echo "<td class=ttl>Nilai</strong></td>";

while ($sesi<$SesiKanan) {
$sesi++;
if ($sesi==2) { $_sesi='II'; }
elseif ($sesi==3) { $_sesi='III'; }
elseif ($sesi==4) { $_sesi='IV'; }
elseif ($sesi==5) { $_sesi='V'; }
elseif ($sesi==6) { $_sesi='VI'; }
elseif ($sesi==7) { $_sesi='VII'; }
elseif ($sesi==8) { $_sesi='VIII'; }
if ($program=='R' || $program=='P' || $program=='J' || $program=='M' || $prog['NMProg']=='D3') {
$s = "select m.MKKode,m.Nama,m.SKS,m.MKID
    from mk m
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$mhs[ProdiID]'
	  And m.Sesi=$sesi
      and m.NA = 'N'
	  $whrKurikulum
    order by m.MKKode";
}
$r=_query($s);
echo "<tr><td colspan=5><strong>Semester $_sesi</font></td></tr>";
$jmlsks=0;
$jmlmutu=0;
$lg=0;
	while ($w=_fetch_array($r)) {
		$lg++;
		if ($lg==1) {
			echo "<tr bgcolor=darkgrey>";
		$lg=$lg-2;
		}
		else {
			echo "<tr>";
		}
		echo "<td>$w[MKKode]</td>";
		echo "<td>$w[Nama]</td>";
		echo "<td align=center>$w[SKS]</td>";
	
		$krs=GetFields('krs',"MKKode='$w[MKKode]' and Tinggi='*' and MhswID",$mhs['MhswID'],"*,min(GradeNilai) as GradeNilai");
		if (!empty($krs['KRSID'])) {
		$id=$krs['KRSID']+0;
		$selectall="SelectAll('Nilai_".$id."')";
		$optnilai = ($krs['NA']=='Y')? "<option></option>":GetOption2('nilai', "Nama", 'Bobot desc',
    $krs['BobotNilai'], "KodeID='".KodeID."' and ProdiID='$mhs[ProdiID]'", 'Bobot');
		$optnilai .= ($krs['NA']=='Y')? "<option value='X' selected>Non Aktif</option><option value='N'>Aktifkan</option>" : "<option value='X'>Non Aktif</option>";
		echo "<input type=hidden name='krsid[]' value='$krs[KRSID]' />
      	<input type=hidden name='KRS_$id' value='$krs[KRSID]' />";
		
		echo "<td align=center><select name='Nilai_$id' class='nones' id='Nilai_$id' data-noty-options='{\"text\":\"Data telah disimpan\",\"layout\":\"topCenter\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}' onChange=\"javascript:ajaxSave('master/ajx/ajxsave.kliringnilai','UPDATE',this,'$krs[KRSID]','$mhs[MhswID]','Nilai_$id')\">$optnilai</select></td></tr>";
		$mutu=$w['SKS']*$krs['BobotNilai']+0;
		$totalmutu=($totalmutu + $mutu)+0;
		$totalsks=($totalsks + $w['SKS'])+0;
		$jmlsks=($jmlsks+$w['SKS'])+0;
		$jmlmutu=($jmlmutu+$mutu)+0;
		}
		else {
		$n++;
		$id=$n+0;
		$selectall="SelectAll('Nilai_".$id."')";
		echo "<input type=hidden name='n[]' value='$n' />";
		echo "<input type=hidden name='MKID_$n' value='$w[MKID]' />";
		echo "<input type=hidden name='MKKode_$n' value='$w[MKKode]' />";
		echo "<input type=hidden name='SKS_$n' value='$w[SKS]' />";
		echo "<input type=hidden name='Nama_$n' value='$w[Nama]' />";
		$optnilai = GetOption2('nilai', "Nama", 'Bobot desc',
    $krs['BobotNilai'], "KodeID='".KodeID."' and ProdiID='$mhs[ProdiID]'", 'Bobot');
		
		echo "<td align=center><select name='Nilai_$id' class='nones' id='Nilai_$id' data-noty-options='{\"text\":\"Data telah disimpan\",\"layout\":\"topCenter\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}' onChange=\"javascript:ajaxSave('master/ajx/ajxsave.kliringnilai','INSERT',this,'$w[MKID]','$mhs[MhswID]','Nilai_$id')\">$optnilai</select></td></tr>";
		}
	}
	echo "<tr><td colspan=2 align=right><b>Jumlah :</td><td align=center><b>$jmlsks</b></td><td align=center><b>$jmlmutu</td></tr>";
}
$ipk=($totalmutu/$totalsks)+0;
echo "</table></td></table><br />";

echo "<table width=600 align=center border=0>";
//$prog2 = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $mhs[ProdiID], 'KAPITAL(j.Nama) as NMProg');
echo	"<tr><td align=center>
		<input type=submit name='SimpanSemua' value='Simpan Semua' />
		<input type=reset name='ResetSemua' value='Batalkan Semua' /></td></tr>
		<tr><td width=200>Total Angka Mutu </td><td width=3>:</td><td width=200><b>$totalmutu</td></tr>
		<tr><td>Total SKS </td><td>:</td><td><b>$totalsks</td></tr>
		<tr><td>Indeks Prestasi Komulatif (IPK) </td><td>:</td><td><b>";
		printf ("%01.2f", $ipk);
		$Yudisium= GetaField('wisudawan', "MhswID", $mhswid, 'Predikat');
		echo "
		
		</form></table>";
	}
function NilaiMhswSimpan() {
global $mhsw;
  /*$krsid = array();
  $krsid = $_REQUEST['krsid'];
  foreach ($krsid as $id) {
   $Nilai9 = $_REQUEST['Nilai_'.$id];
    if ($Nilai9 !='') {
   	  $arrgrade = GetFields('nilai', 
      		"Nama='$Nilai9' and ProdiID",
      		$mhsw[ProdiID], "Nama, Bobot,NilaiMin");
    // Simpan
   $s1 = "update krs set NilaiAkhir='$arrgrade[NilaiMin]', GradeNilai='$arrgrade[Nama]', BobotNilai='$arrgrade[Bobot]',
	LoginEdit = '$_SESSION[_Login]-KLN',
    TanggalEdit = now()
    where KRSID='$id' ";
   $r1 = _query($s1); //echo $s1;


	}
  }
  
// Nilai yang ditambah
  $krsid = array();
  $krsid = $_REQUEST['n'];
  foreach ($krsid as $id) {
  $MKID = $_REQUEST['MKID_'.$id];
   $MKKode = $_REQUEST['MKKode_'.$id];
   $SKS = $_REQUEST['SKS_'.$id];
   $Nama = $_REQUEST['Nama_'.$id];
   $Nilai9 = $_REQUEST['Nilai_'.$id];
   if ($Nilai9 !='X' && $Nilai9 !='') {
   	  $arrgrade = GetFields('nilai', 
      		"Nama='$Nilai9' and ProdiID",
      		$mhsw[ProdiID], "Nama, Bobot,NilaiMin");
    // Simpan
	
   $s2 = "insert into krs (KodeID,MhswID,MKID,MKKode,Nama,SKS,Tinggi,Final,NilaiAkhir,GradeNilai,BobotNilai,LoginBuat,TanggalBuat) value
	('".KodeID."','$mhsw[MhswID]','$MKID','$MKKode','$Nama','$SKS','*','Y','$arrgrade[NilaiMin]','$arrgrade[Nama]','$arrgrade[Bobot]','$_SESSION[_Login]-KLN',now())";
   $r2 = _query($s2); //echo $s2; 
	}
  }*/
 BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1000);
}
?>