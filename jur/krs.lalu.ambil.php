<?php

// Kostumisasi untuk KRS Menyusul oleh Arisal Yanuarafi
// Desember  2011

session_start();
include_once "../sisfokampus1.php";
include_once "../$_SESSION[mnux].lib.php";

HeaderSisfoKampus("KRS Mahasiswa", 1);

// *** Parameters ***

$mhswid = GetSetVar('mhswid');
$tahunid = GetSetVar('tahunid');
$_krsKelasID = GetSetVar('_krsKelasID');
$_krsSemester  = GetSetVar('_krsSemester');
$_KurikulumID = GetSetVar('_KurikulumID');

// *** Main ***
TampilkanJudul("Daftar Matakuliah Yang Tersedia");
$gos = (empty($_REQUEST['gos']))? 'DftrJadwal' : $_REQUEST['gos'];
$gos($mhswid, $tahunid);

// *** Functions ***
function TampilkanFilterProgram($khs) {
  $_SESSION['_krsProgramID'] = $khs['ProgramID'];
   $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_krsProgramID, "KodeID='".KodeID."'", 'ProgramID');
   $s6 = "select KurikulumID,KurikulumKode,Nama
    from kurikulum
    where ProdiID = '$khs[ProdiID]' order by Nama";
	
  echo "<table class=bsc cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].ambil.php' method=POST name='frmFilterProgram'>
  <input type=hidden name='gos' value='' />
  <input type=hidden name='mhswid' value='$khs[MhswID]' />
  <input type=hidden name='tahunid' value='$khs[TahunID]' />
  
  <tr><td class=inp width=10>Filter:</td>
      <td class=ul1 nowrap>
      Program Pendidikan:
      <input type=text name='_prog' value='$_SESSION[_krsProgramID]' disabled size=3>
	  <input type=hidden name='_krsProgramID' value='$_SESSION[_krsProgramID]'> &nbsp;
      </td><td class=inp>Kurikulum: </td><td>";
	  $r6 = _query($s6);
	  $optkurikulum = "<option value=''></option>";
	  while($w6 = _fetch_array($r6))
		{  $ck = ($w6['KurikulumID'] == $_SESSION['_KurikulumID'])? "selected" : '';
		   $optkurikulum .=  "<option value='$w6[KurikulumID]' $ck>$w6[Nama]</option>";
		}
	  $_inputKurikulum = "<select name='_KurikulumID' onChange='this.form.submit()'>$optkurikulum</select>";    
   echo"$_inputKurikulum</td> <td class=inp>Semester:</td><td>
      <input type=text name='_krsSemester' value='$_SESSION[_krsSemester]'
        size=2 maxlength=2 /></td><td>
      <input type=submit name='Filter' value='Filter' />
      <input type=button name='Tutup' value='Tutup' onClick='window.close()' />
      </td>
      </tr>
  
  </form>
  </table>";
}
function TampilkanWarning($psn) {
  echo "<table class=box cellspacing=1 width=100%>
  <tr><th class=wrn>$psn</th></tr>
  </table>";
}
function AmbilDaftarKRS($mhswid, $tahunid) {
  $s = "select JadwalID
    from krs
    where TahunID = '$tahunid'
	AND MhswID = '$mhswid'
    order by JadwalID";
  $r = _query($s);
  $j = array();
  while ($w = _fetch_array($r)) {
    $j[] = $w['JadwalID'];
  }
  $jid = implode(',', $j);
  $jid = (empty($jid))? '' : "and not (j.JadwalID in ($jid))";
  return $jid;
}
function AmbilJenisJadwal()
{ $s = "select * from jenisjadwal where Tambahan='N' and NA='N'";
  $r = _query($s);
  $jj = array();
  while($w = _fetch_array($r))
  {	$jj[] = "'".$w['JenisJadwalID']."'";
  }
  $jjid = implode(',', $jj);
  $jjid = (empty($jjid))? '' : "and j.JenisJadwalID in ($jjid)";
  return $jjid;
}
function DftrJadwal($mhswid, $tahunid) {
  $khs = GetFields('khs', 'MhswID='.$mhswid.' and TahunID', $tahunid, '*');
  TampilkanFilterProgram($khs);
  PilihLabKRSScript();
  
  // filtering the listing
  $whr_prg = ($_SESSION['_krsProgramID'] == '')? '' : "and ProgramID = '$_SESSION[_krsProgramID]' ";
  $whr_kls = ($_SESSION['_krsKelasID'] == '')? '' : "and j.NamaKelas like '$_SESSION[_krsKelasID]%' ";
  $whr_smt = ($_SESSION['_krsSemester'] == '')?  '' : "and mk.Sesi = '$_SESSION[_krsSemester]' ";
  $whr_jenisjadwal = AmbilJenisJadwal();
  $whr_krs = AmbilDaftarKRS($mhswid, $tahunid);
  
  $s7 = "select distinct(Sesi)
    from mk
    where KodeID = '".KodeID."'
      and ProdiID = '$khs[ProdiID]'
	  $whr_smt
      and NA = 'N'
    order by sesi";
  $r7 = _query($s7); $n = 0;
  // Jika tidak ada yg ditawarkan:
  if (_num_rows($r7) == 0) die(TampilkanWarning("Tidak ada matakuliah yang dijadwalkan.
    <hr size=1 color=white />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // Tampilkan
  
  echo "<table class=bsc cellspacing=1 width=100%>";
  echo "<form action='../$_SESSION[mnux].ambil.php' method=POST>
    <input type=hidden name='gos' value='Ambil' />
    <input type=hidden name='mhswid' value='$mhswid' />
    <input type=hidden name='tahunid' value='$khs[TahunID]' />";
  $hdr = "<tr>
    <th class=ttl colspan=2>Ambil</th>
    <th class=ttl width=80>Kode <sup title='Semester'>Smt</sup></th>
    <th class=ttl>Matakuliah <sup title='SKS MK'>SKS</sup></th>
	<th class=ttl>Alokasi Nilai</sup></th>
	</tr>";

  $hr = -32;
  $btn = "<input type=submit name='Simpan' value='Ambil Yg Dicentang' />
    <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />";
	while ($w7 = _fetch_array($r7)) {
	$s = "select  m.ProdiID, m.MKID, m.MKKode, m.Nama as Matakuliah, m.SKS, m.Sesi
    from mk m
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$khs[ProdiID]'
      and m.NA = 'N'
      and m.Sesi='$w7[Sesi]'
	  and m.KurikulumID='$_SESSION[_KurikulumID]'
    order by m.Nama";
  $r = _query($s);
    while ($w = _fetch_array($r)) {

$mkMhsw = GetFields('krs', "MhswID='$khs[MhswID]' and MKKode='$w[MKKode]' and KodeID", KodeID, 'MKKode, BobotNilai, GradeNilai, TahunID');
$mkKHS = GetFields('khs', "MhswID='$khs[MhswID]' and TahunID='$mkMhsw[TahunID]' and KodeID", KodeID, 'Sesi');
$mkSesi = GetFields('mk', "ProdiID='$khs[ProdiID]' and MKKode='$w[MKKode]' and KodeID", KodeID, 'Sesi');
$mkNilai = $mkMhsw[BobotNilai]+0;
    if ($hr != $w['Sesi']) {
      $hr = $w['Sesi'];
      $_hr = "Semester $w[Sesi]";
      $btn1 = ($hr > 1)? $btn : '';
      echo "<tr>
        <td class=ul1 colspan=3>
        <b>$_hr</b> 
        </td>
        <td class=ul1 colspan=5>$btn1</td>
        </tr>";
      echo $hdr;
    }
    
	$checkboxjadwal = "<input type=checkbox id='JdwlRes$w[JadwalID]' name='jid[]' value='$w[MKID]' onChange=\"ChooseJadwal('$w[JadwalID]')\"/>" ;
		//if ($mkNilai<3) {
		$n++;
		$wmk=$w['MKID'];
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul1 width=5>
        $checkboxjadwal
        </td>
      
      <td class=ul1>$w[MKKode]<sup>$w[Sesi]</sup></td>
      <td class=ul1>$w[Matakuliah] <sup>$w[SKS] </sup></td>
	  <td class=ul1 align=center><select name='Nilai_$wmk'>
		 <option value=''>---</option>";
		 $s12 = "select Nama, Bobot, NilaiID from nilai where ProdiID='$khs[ProdiID]'";
		 $r12 = _query($s12);
		 while ($w12 = _fetch_array($r12)) {
		 echo "<option value='$w12[NilaiID]'>$w12[Nama] - $w12[Bobot]</option>";
		 }
		 echo "</select></td>
	  </tr>";
	//} 
	/*else {
		$n++;
		$wmk=$w['MKID'];
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul1 width=5>
        $checkboxjadwal
        </td>
      
      <td class=ul1>$w[MKKode]<sup>$w[Sesi]</sup></td>
      <td class=ul1>$w[Matakuliah] <sup>$w[SKS] ".(empty($mkMhsw['GradeNilai'])) ? '':"____diambil disesi $mkKHS[Sesi]. nilai: $mkMhsw[GradeNilai]"."</sup></td>
	  <td class=ul1 align=center><select name='Nilai_$wmk'>
		 <option value=''>---</option>";
		 $s12 = "select Nama, Bobot, NilaiID from nilai where ProdiID='$khs[ProdiID]'";
		 $r12 = _query($s12);
		 while ($w12 = _fetch_array($r12)) {
		 echo "<option value='$w12[NilaiID]'>$w12[Nama] - $w12[Bobot]</option>";
		 }
		 echo "</select></td>
	  </tr>";
	} */
  }
  }

  echo "<tr><td class=ul1 colspan=3>&nbsp</td><td class=ul1 colspan=5>$btn</td></tr>";
  echo "</form></table>";
  echo "<p align=center>Mata kuliah yang sudah diambil tidak ditampilkan lagi di sini.</p>";
}

function Ambil($mhswid, $tahunid) {
  $jid = array();
  $jid = $_REQUEST['jid'];
  $jresid = $_REQUEST['jresid'];
  $khs = GetFields('khs', 'MhswID='.$mhswid.' and TahunID', $tahunid, '*');
  $cekprasyarat = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $khs['ProdiID'], 'CekPrasyarat');
  if (empty($jid)) {
    echo ErrorMsg('Error',
      "Anda belum mencentang matakuliah yang akan diambil.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Kembali' value='Kembali'
        onClick=\"location='../$_SESSION[mnux].ambil.php?mhswid=$mhswid&khsid=$khsid'\" />
        <input type=button name='Tutup' value='Tutup'
        onClick=\"window.close()\" />");
  }
  else {
    TutupScript($mhswid, $tahunid);

    // Buat array pesan	
    $arrPesan = array();
    $_psn = '';
    foreach ($jid as $j) {
	$khs = GetFields('khs', 'MhswID='.$mhswid.' and TahunID', $tahunid, '*');
		$Nilai9 = $_REQUEST['Nilai_'.$j]+0;
  	 	if ($Nilai9>0){
  		$nil2 = GetFields('nilai', 'NilaiID', $Nilai9, '*');
		}
	  $oke = true;
      $jdwl = GetFields('mk', 'MKID', $j, '*');
      // Cek prasyarat
      // Cek apakah ada bentrok?
      	  /*if($jdwl['AdaResponsi'] == 'Y')
	  {	$jdwlresponsi = GetFields("jadwal jr left outer join jadwal j on j.JadwalRefID=jr.JadwalID and j.KodeID='".KodeID."'" , 'JadwalID', $arrResponsi[$j], 'jr.*, j.Nama, j.MKKode, j.SKS');
		if ($oke) $oke = CheckResponsiMhsw($khs, $jdwlresponsi, $_psn); 
	  }*/
      if ($oke) SimpanKRSMhsw($khs, $jdwl,$nil2);
      else $arrPesan[] = $_psn;
    }
    HitungUlangKRS($mhswid, $tahunid);
    echo "<script>
      opener.location='../index.php?mnux=$_SESSION[mnux]&gos=&mhswid=$mhswid&tahunid=$tahunid';
      </script>";

    // Jika ada Error, tampilkan pesan errornya
    if (!empty($arrPesan)) {
     $p = implode(' ', $arrPesan);
     echo ErrorMsg('Error',
       "Ada KRS yang gagal diambil. Berikut adalah pesan kesalahannya:
       <ol>$p</ol>
       <hr size=1 color=silver />
       Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"javascript:ttutup()\" />
         <input type=button name='Kembali' value='Kembali' onClick=\"location='../$_SESSION[mnux].ambil.php'\" />");
    }
    else
	{	
		// Sampai sini, penyimpanan data krs telah selesai. 
	    //echo "KRS untuk Jadwal Kuliah Utama Telah berhasil disimpan.<br>";
		
			// Sekarang, cek dan simpan data kelas tambahan (responsi/lab/tutorial)
			// Buat array yang memuat semua jadwal responsi
			$arrPesan = array();
			// Bila ada jadwal kelas tambahan yang dipilih....
			if(!empty($jresid))
			{	
				foreach($jresid as $j)
				{	$a = explode('~', $j);
					
					$oke = true;
					  $jdwl = GetFields('jadwal', 'JadwalID', $a[1], '*');
					  // Tidak usah Cek prasyarat karena sudah dicek sebelumnya
					  // Cek apakah ada bentrok?
					  if ($oke) $oke = CheckKRSMhsw($khs, $jdwl, $_psn);
					  if ($oke) $oke = CheckKapasitas($jdwl, $_psn);
					  if ($oke) SimpanKRSMhsw($khs, $jdwl);
					  else $arrPesan[] = $_psn;
				}
			}
			
			if (!empty($arrPesan)) {
			 $p = implode(' ', $arrPesan);
			 echo ErrorMsg('Error',
			   "Ada KRS Tambahan yang gagal diambil. Berikut adalah pesan kesalahannya:
			   <ol>$p</ol>
			   <hr size=1 color=silver />
			   Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"javascript:ttutup()\" />
				 <input type=button name='Kembali' value='Kembali' onClick=\"location='../$_SESSION[mnux].ambil.php'\" />");
			}
		echo "<script>ttutup()</script>";
	}
  }
}

function SimpanKRSMhsw($khs, $jdwl, $nil2) {
	$_SKS = $jdwl['SKS'];
  $jumambil=$khs[SKS]+$_SKS;
  if($jumambil > 1020)
  {
  echo"
  <script>
  alert('Batas Pengambilan Sks Anda Tidak Mencukupi')
  </script>";
  }else{
  $s = "insert into krs
    (KodeID, KHSID, MhswID, TahunID, 
    MKID, MKKode, Nama, SKS,
    LoginEdit, TanggalEdit,GradeNilai,BobotNilai,NilaiAkhir,EvaluasiDosen,Final,Tinggi)
    values
    ('".KodeID."', '$khs[KHSID]', '$khs[MhswID]', '$khs[TahunID]', 
    '$jdwl[MKID]', '$jdwl[MKKode]', '$jdwl[Nama]', '$_SKS',
    '$_SESSION[_Login]-MKRS', now(),'$nil2[Nama]','$nil2[Bobot]','$nil2[NilaiMin]','Y','Y','*')";
  $r = _query($s);
  
  HitungUlangKRS($khs[MhswID],$khs[TahunID]);
  }
}
function PilihLabKRSScript()
{	echo <<< SCR
		<script>
			function ChooseLab(mkid, type, target)
			{	count = document.getElementById('JdwlResCount'+mkid+'of'+type).value;
				if(document.getElementById('JdwlRes'+mkid).checked)
				{
					for(i = 1; i <= count; i++)
					{	document.getElementById('JdwlRes'+mkid+'of'+type+'of'+i).checked = false;
					}
					document.getElementById('JdwlRes'+mkid+'of'+type+'of'+target).checked = true;
				}
				else
				{	for(i = 1; i <= count; i++)
					{	document.getElementById('JdwlRes'+mkid+'of'+type+'of'+i).checked = false;
					}
				}
			}
			function ChooseJadwal(mkid)
			{	countType = document.getElementById('JdwlResCountType'+mkid).value;
				for(t = 1; t <= countType; t++)
				{	oneType = document.getElementById('JdwlResType'+mkid+'of'+t).value;
					
					count = document.getElementById('JdwlResCount'+mkid+'of'+oneType).value;
					if(document.getElementById('JdwlRes'+mkid).checked)
					{	if(count > 0)
						{	
							for(i = 1; i <= count; i++)
							{	document.getElementById('JdwlRes'+mkid+'of'+oneType+'of'+i).checked = false;	
							}
						}
						document.getElementById('JdwlRes'+mkid+'of'+oneType+'of'+1).checked = true;
					}
					else
					{	for(i = 1; i <= count; i++)
							document.getElementById('JdwlRes'+mkid+'of'+oneType+'of'+i).checked = false;	
					}
				}
			}
		</script>
SCR;
}
function TutupScript($mhswid, $tahunid) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=&mhswid=$mhswid&khsid=$tahunid';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}

?>