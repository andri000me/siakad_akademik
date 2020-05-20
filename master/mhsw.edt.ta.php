<?php
// Author: Arisal Yanuarafi
// 06 Maret 2012

// *** Main ***
if ($_SESSION['_LevelID']==120) {
	$KP = GetaField('krs k,mk m', "k.MKID=m.MKID and m.PraktekKerja='Y' and m.Komprehensif='Y' and MhswID",'$_SESSION[_Login]', 			'm.Komprehensif');
	if ($KP='Y') {
	$sub = (empty($_REQUEST['sub']))? 'frmTA' : $_REQUEST['sub'];
	$sub();
	}
	else {
	include_once "mhsw.edt.pri.php";
	$sub = 'frmPribadi';
  	$sub();
	}
  }
  else {
	$sub = (empty($_REQUEST['sub']))? 'frmTA' : $_REQUEST['sub'];
	$sub();
}

// *** Functions ***
function frmTA() {
  global $dataTA, $datamhsw, $mnux, $pref;
  	// *** cek ke dalam tabel mhsw ***
	$msg = array();
	$cek = array('MhswID','Nama','TempatLahir','TanggalLahir','IPK','Alamat','NamaAyah','NamaIbu','Handphone','Foto');
		foreach ($cek as $a) {
		if (empty($datamhsw[$a])) $msg[] = "$a";
		}
		
	// *** cek ke dalam tabel ta ***
	$cek = array('Judul','Pembimbing','TglUjian');
	$ta = GetFields('ta',"MhswID",$datamhsw['MhswID'],'*');
		foreach ($cek as $a) {
		if (empty($ta[$a])) $msg[] = "$a";
		}
	
	// *** Buat Validasi *** (Hasilnya dapat dilihat di Mhsw.Edt.Ta)
		$ValidasiWisuda='';
		foreach ($msg as $a) {	
		$ValidasiWisuda .= "$a - ";
		}	
  // *** Jika sudah lengkap tampilkan Tombol cetak Permohonan Wisuda	
	if (empty($msg)) {
	?><form name='CetakPermohonanWisuda' target="_blank" action="master/permohonan.wisuda.php" method="post">
    <input type="hidden" name="MhswID" value="<?php echo $datamhsw['MhswID']; ?>" />
    <input type=submit  value='Cetak Permohonan Wisuda'> 
    <input type="button" onclick="location.href='../../doc/Prosedur_Wisuda_57.pdf'" target="_blank" value="Cetak Form Bebas Administrasi" />
    </form>
    <sup>&radic; Silakan klik tombol diatas untuk mencetak Permohonan Wisuda dan Form Bebas Administrasi.</sup>
	<?php
	}
   else { 
   echo "<p align=center>
   		<br>
		<table class=box cellspacing=1 cellpadding=4 width=600>
		<tr><td><b>Harap melengkapi data profil termasuk Foto Mahasiswa. Berikut adalah data yang belum Anda lengkapi:</b></td></tr>
		<tr><td>$ValidasiWisuda</td></tr>
		<tr><td><b>Tombol Cetak Permohonan Wisuda akan muncul setelah data diatas dilengkapi.</b></td></tr>
		</table>
		</p>";
	}
   echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]'>
  <input type=hidden name='sub' value='TASav' />
  <input type=hidden name='submodul' value='ta' />
  <input type=hidden name='BypassMenu' value='1' />

  <tr><td colspan=2 class=ul><b>Data Tugas Akhir/Skripsi</b></td></tr>
  <tr><td class=inp width=150>Judul Bahasa Indonesia</td>
      <td class=ul>
	  <TEXTAREA NAME='bind' COLS=47 ROWS=6>$dataTA[Judul]</TEXTAREA></td></tr>
  <tr><td class=inp>Judul Bahasa Inggris</td>
      <td class=ul>
	  <TEXTAREA NAME='bing' COLS=47 ROWS=6>$dataTA[Deskripsi]</TEXTAREA>
      </tr>
  <tr><td class=inp>Nama Pembimbing</td>
      <td class=ul>";
	   $strProdiID = '.'.$datamhsw[ProdiID].'.';
	  	echo "<select name='DosenPembimbing'>
		 <option value='$dataTA[Pembimbing]'>$dataTA[NamaPembimbing], $dataTA[Gelar]</option>";
		 $s8 = "select distinct(NIDN),Nama, Gelar from dosen where Homebase != '' and Login not like '%-%' order by Nama";
		 $r8 = _query($s8);
		 while ($w8 = _fetch_array($r8)) {
		 echo "<option value='$w8[NIDN]'>$w8[Nama], $w8[Gelar]</option>";
		 }
		 echo "</select>";
	  echo "</td>
      </tr>";
	  $TanggalLulus = GetDateOption4(date('Y')-1,date('Y'),$datamhsw['TanggalLulus'], 'tglLulus');
 echo " <tr><td class=inp>Tanggal Lulus Sidang</td>
      <td class=ul>$TanggalLulus</td>
      </tr>";
    echo "<tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";

}

function TASav() {
  $jBindo = sqling($_REQUEST['bind']);
  $jBing = sqling($_REQUEST['bing']);
  $NmPembimbing = sqling($_REQUEST['DosenPembimbing']);
  $tglLulus = sqling("$_REQUEST[tglLulus_y]-$_REQUEST[tglLulus_m]-$_REQUEST[tglLulus_d]");

$cek= GetaField('ta', "MhswID",$_REQUEST[mhswid], 'count(MhswID)');
$khs= GetaField('khs', "MhswID",$_REQUEST[mhswid], 'TahunID');

  $s = "update mhsw set TanggalLulus='$tglLulus'
   where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
if ($cek >0) {
  // Simpan
  $s = "update ta set Judul='$jBindo',
    Deskripsi='$jBing',
    Pembimbing='$NmPembimbing', TglUjian='$tglLulus'
   where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&submodul=ta&gos=", 100);
  }
  else {
  $thnWisuda= GetaField('wisuda',"NA",N,'TahunID');
  $s = "insert into ta (MhswID,TahunID,Judul,Deskripsi,Pembimbing,TglUjian,LoginBuat,TanggalBuat) values ('$_REQUEST[mhswid]','$thnWisuda','$jBindo','$jBing','$NmPembimbing', '$tglLulus','$_SESSION[_Login]',now())";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&submodul=ta&gos=", 100);
  }
  
}

?>
