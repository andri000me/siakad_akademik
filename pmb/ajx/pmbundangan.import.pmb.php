<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 27 Apr 2014  */
	
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";
$NISN = $_REQUEST['nisn'];
$PMBPeriodID = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
$cekAplikan = GetaField("aplikan", "AplikanID", $NISN, "AplikanID");
if (empty($cekAplikan)) {
$s = "SELECT * From ubh_undangan.camapmdk where NISN = '$NISN' limit 1";
$r = _query($s);
while ($w = _fetch_array($r)) {
	$Pilihan1 = GetaField('ubh_undangan.jurusan', "jurid", $w['Pilihan1'], "jur_singkatan");
	$Pilihan2 = GetaField('ubh_undangan.jurusan', "jurid", $w['Pilihan2'], "jur_singkatan");
	$cek	= GetaField('aplikan', "AplikanID", $w['NISN'],"AplikanID");
	$NilaiRapor = ($w['Nilai1'] + $w['Nilai2'] + $w['Nilai3'] + $w['Nilai4'] + $w['Nilai5']) / 5;
	if (empty($cek)) {
	$s2 = "insert into aplikan
			  (AplikanID, PMBPeriodID, KodeID, StatusAwalID, Nama,
				Password,Kelamin,
				TempatLahir,
				TanggalLahir,
				Kota,
				Handphone,
				Email,
				Foto,
				Propinsi,
				Kabupaten,
				AsalSekolah,
				AlamatSekolah,
				JurusanSekolah,
				Pilihan1,
				Pilihan2,
				NilaiRapor,

			  LoginBuat, TanggalBuat)
			  values
			  ('$w[NISN]', '$PMBPeriodID', '".KodeID."', 'B', '$w[Nama]',
				'$w[Password]',
				'$w[Jenkel]',
				'$w[TempatLahir]',
				'$w[TanggalLahir]',
				'$w[Kota]',
				'$w[Handphone]',
				'$w[Email]',
				'$w[Foto]',
				'$w[PropinsiID]',
				'$w[KabupatenID]',
				'$w[AsalSekolahID]',
				'$w[NamaSekolah]',
				'$w[Jurusan]',
				'$Pilihan1',
				'$Pilihan2',
				'$NilaiRapor',

			  '$_SESSION[_Login]', now())";
			$r2 = _query($s2);
			echo "Import Berhasil";
			if (!empty($w['Foto'])){
				copy("../../../undangan/foto_file/mid_$w[Foto]","../../../spmb/foto_file/mid_$w[Foto]");
				copy("../../../undangan/foto_file/small_$w[Foto]","../../../spmb/foto_file/small_$w[Foto]");
			}
	}

	
	} 
	
} // end cek aplikan
else { 
	echo "Tidak bisa diproses, Ybs. Sudah ada di Aplikan. <br>Ulangi !!<br>";
	echo '<input type="button" onclick="javascript:pindahkePMB(\'9983998213\')" value="Import Data" class="btn btn-small btn-primary">';
	_query("DELETE from aplikan where AplikanID='$NISN'");
}




include "../statusaplikan.lib.php";
$UID  = $NISN;
$cama = GetFields("aplikan","AplikanID",$UID,"Foto,Nama,Kelamin,Pilihan1,Pilihan2,AplikanID");
if (!empty($cama['AplikanID'])) {
$p1 = GetaField("prodi","ProdiID",$cama['Pilihan1'],"Nama");
$p2 = GetaField("prodi","ProdiID",$cama['Pilihan2'],"Nama");
$w = GetFields("aplikan c left outer join asalsekolah a on a.SekolahID=c.AsalSekolah
								left outer join jurusansekolah j on j.JurusanSekolahID=c.Jurusan", "AplikanID", $NISN,
					"a.PropinsiID as Prop, a.KabupatenID as Kab, a.SekolahID,c.*,j.NamaJurusan, a.Nama as Sekolah, a.NamaPropinsi"); 
					if (!empty($w['Nama']) 
                                    && (!empty($w['Pilihan1'])) 
                                    && (!empty($w['Pilihan2'])) )
					{
$cekPMB = GetaField('pmb','AplikanID',$UID,"AplikanID");
if (empty($cekPMB)){
	$cekPMBFormJual = GetaField('pmbformjual','AplikanID',$UID,"PMBFormJualID");
	$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
	if (empty($cekFormJual)) {
	$sFJ = "INSERT IGNORE INTO pmbformjual (PMBFormJualID, AplikanID, PMBFormulirID, PMBPeriodID, Tanggal, BuktiSetoran, OK, LoginBuat, TanggalBuat, Jumlah)
	values
	('$UID','$UID', '13', '$gelombang', now(), '$UID', 'Y', 'Arisal', now(), 0)";
	$rFJ = _query($sFJ);
	}
	$PMBFormJualID = $w['PMBFormJualID'];
	$PMBPeriodID = $w['PMBPeriodID'];
	$frm = GetFields('pmbformulir', 'PMBFormulirID', $PMBFormulirID, '*');
	  $pil = array();
	  $vpil = array();
	  $epil = array();
	  for ($i = 1; $i <= $frm['JumlahPilihan']; $i++) {
		$pil[] = 'Pilihan'.$i;
		$vpil[] = "'".sqling($w['Pilihan'.$i])."'";
	  }
	  $_pil = implode(', ', $pil);
	  $_vpil = implode(', ', $vpil);
	$id = GetNextPMBIDFromGel($PMBPeriodID);
	if (empty($id)) die ("Terjadi kesalahan. code: id | $PMBPeriodID");
			$s = "insert into pmb
			  (PMBID, AplikanID, PMBPeriodID, KodeID, StatusAwalID, Nama, 
			  TempatLahir, TanggalLahir, Kelamin, GolonganDarah,
			  Agama, StatusSipil, TinggiBadan, BeratBadan,
			  WargaNegara, Kebangsaan,
			  TempatTinggal, Alamat, RT, RW, KodePos, Kota, Propinsi, 
			  Telepon, Handphone, Email,
			  PendidikanTerakhir, AsalSekolah, AlamatSekolah, JurusanSekolah,
			  TahunLulus, NilaiSekolah, PrestasiTambahan, 
			  NamaAyah, AgamaAyah, PendidikanAyah, PekerjaanAyah, HidupAyah, PenghasilanAyah,
			  NamaIbu, AgamaIbu, PendidikanIbu, PekerjaanIbu, HidupIbu, PenghasilanIbu, BiayaStudi, 
			  AlamatOrtu, RTOrtu, RWOrtu, KodePosOrtu, KotaOrtu, PropinsiOrtu,
			  TeleponOrtu, HandphoneOrtu, EmailOrtu,
			  NamaPerusahaan, AlamatPerusahaan, TeleponPerusahaan, JabatanPerusahaan,
			  PMBFormulirID, ProgramID, ProdiID, Pilihan1,Pilihan2,		
			  Foto,  Hobi,
			  LoginBuat, TanggalBuat)
			  values
			  ('$id', '$w[AplikanID]', '$PMBPeriodID', 'UBH', 'B', '".sqling($w[Nama])."', 
			  '$w[TempatLahir]', '$w[TanggalLahir]', '$w[Kelamin]', '$w[GolonganDarah]',
			  '$w[Agama]', '$w[StatusSipil]', '$w[TinggiBadan]', '$w[BeratBadan]',
			  '$w[WargaNegara]', '$w[Kebangsaan]',
			  '$w[TempatTinggal]', '$w[Alamat]', '$w[RT]', '$w[RW]', '$w[KodePos]', '$w[Kota]', '$w[Propinsi]', 
			  '$w[Telepon]', '$w[Handphone]', '$w[Email]',
			  '$w[PendidikanTerakhir]', '$w[AsalSekolah]', '$w[AlamatSekolah]', '$w[JurusanSekolah]',
			  '$w[TahunLulus]', '$w[NilaiSekolah]', '$w[PrestasiTambahan]', 
			  '$w[NamaAyah]', '$w[AgamaAyah]', '$w[PendidikanAyah]', '$w[PekerjaanAyah]', '$w[HidupAyah]', '$w[PenghasilanAyah]', 
			  '$w[NamaIbu]', '$w[AgamaIbu]', '$w[PendidikanIbu]', '$w[PekerjaanIbu]', '$w[HidupIbu]', '$w[PenghasilanIbu]', '$w[BiayaStudi]',
			  '$w[AlamatOrtu]', '$w[RTOrtu]', '$w[RWOrtu]', '$w[KodePosOrtu]', '$w[KotaOrtu]', '$w[PropinsiOrtu]',
			  '$w[TeleponOrtu]', '$w[HandphoneOrtu]', '$w[EmailOrtu]',
			  '$w[NamaPerusahaan]', '$w[AlamatPerusahaan]', '$w[TeleponPerusahaan]', '$w[JabatanPerusahaan]',
			  '$w[PMBFormulirID]', 'R', '$w[ProdiID]', '$w[Pilihan1]','$w[Pilihan2]',
			  '$w[Foto]', '$w[Hobi]',
			  'Jalur Undangan', now())";
			$r = _query($s);
			
			
			$s = "update aplikan set PMBID='$id' where AplikanID='$UID'";
			$r = _query($s);
			
			SetStatusAplikan('DFT', $UID, $PMBPeriodID);
		}
					}
}
function GetNextPMBIDFromGel($gel) {
  $gelombang = GetFields('pmbperiod', "PMBPeriodID='$gel' and KodeID", KodeID, "FormatNoPMB, DigitNoPMB");
  // Buat nomer baru
  $nomer = str_pad('', $gelombang['DigitNoPMB'], '_', STR_PAD_LEFT);
  $nomer = $gelombang['FormatNoPMB'].$nomer;
  $akhir = GetaField('pmb',
    "PMBID like '$nomer' and KodeID", KodeID, "max(PMBID)");
  $nmr = str_replace($gelombang['FormatNoPMB'], '', $akhir);
  $nmr++;
  $baru = str_pad($nmr, $gelombang['DigitNoPMB'], '0', STR_PAD_LEFT);
  $baru = $gelombang['FormatNoPMB'].$baru;
  return $baru;
}

