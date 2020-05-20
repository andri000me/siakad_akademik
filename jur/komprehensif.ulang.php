<?php
// Author : wisnu
// Email  : -
// Start  : 17 maret 2009

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Jadwal Ulang Ujian Komprehensif", 1);

echo <<<SCR
  <script src="../$_SESSION[mnux].ta.ujian.script.js"></script>
SCR;

// *** Parameters ***
$KompreID = GetSetVar('KompreID');
$ProdiID = GetSetVar('ProdiID');
$md = GetSetVar('md');

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'TampilkanSetupUjianKompre' : $_REQUEST['sub'];
$sub($md);

// *** Functions ***

function TampilkanSetupUjianKompre($md) {
  $kom = GetFields("kompre k
    left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID='".KodeID."'
    left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID='".KodeID."'
    ", 
    "k.NA = 'N' and k.KompreID", $_SESSION['KompreID'], 
    "k.*, m.Nama as NamaMhsw, m.MhswID as MhswID,
    d.Nama as NamaPA, d.Gelar as GelarPA");
		
	TampilkanHeaderKompre($md,$kom);
}
function TampilkanHeaderKompre($md,$kom) {
  $PA = (empty($kom['NamaPA']))? 'Belum diset' : "$kom[NamaPA] <sup>$kom[GelarPA]</sup>";
  $_TglUjian = ($kom['TglUjian'] == '0000-00-00')? date('Y-m-d') : $kom['TglUjian'];
  $_TglAkhirUjian = ($kom['TglAkhirUjian'] == '0000-00-00')? date('Y-m-d') : $kom['TglAkhirUjian'];
  $TglUjian = GetDateOption($_TglUjian, 'TglUjian');
  $TglAkhirUjian = GetDateOption($_TglAkhirUjian, 'TglAkhirUjian');
  $NamaPenguji = GetaField('dosen', "KodeID='".KodeID."' and Login", $kom['Penguji'], 'Nama');
  
	$jamUjian = substr($_TglUjian, 11, 5);
	$optjamUjian = GetTimeOption($jamUjian, 'TanggalUjian');
	$akhirjamUjian = substr($_TglAkhirUjian, 11, 5);
	$optakhirjamUjian = GetTimeOption($akhirjamUjian, 'AkhirTanggalUjian');

	if ($md == 0){
		$jdl = "Jadwal Ulang Ujian Komprehensif";
		$TahunAkd = $kom[TahunID];
		$Mhsw = $kom[NamaMhsw];
		$MhswID = $kom[MhswID];
		$PA = $kom[NamaPA];
		$TglUjian = $TglUjian;
		$optjamUjian = $optjamUjian;
		$optakhirjamUjian = $optakhirjamUjian;
		$ro = "readonly=TRUE";
		
	} else if ($md == 1){
		$jdl = "Setup Jadwal Ujian Komprehensif";
		$TahunAkd = '';
		$Mhsw = '';
		$MhswID = '';
		$PA = '';
		$TglUjian = GetDateOption(date('Y-m-d'), 'TglUjian');
		$optjamUjian = GetTimeOption('00:00', 'TanggalUjian');;
		$optakhirjamUjian = GetTimeOption('00:00', 'AkhirTanggalUjian');
		$ro = "";
		
	}
	TampilkanJudul($jdl);
	$opttglbayar = GetDateOption(date('Y-m-d'),'TglBayar');
	$ss = "select * from denda where NA = 'N'";
	$qq = _query($ss);
	$opt = '<select name="Denda">';
	while($ww = _fetch_array($qq)){
		$sel = ($ww[Denda] == $w[Denda])? "selected=selected" : "";
		$opt .= "<option value=$ww[Denda] $sel>$ww[Nama]</option>";
	}
	$opt .= "</select>";

	
  echo <<<SCR
  <table class=bsc cellspacing=1 width=100%>
  <form name="frmUlang" action='../$_SESSION[mnux].kompre.ulang.php' method=POST onsubmit="return confirmUlang()" />
  <input type=hidden name='KompreID' value='$kom[KompreID]' />
  <input type=hidden name='md' value='$_SESSION[md]' />
  <input type=hidden name='ProdiID' value='$_SESSION[ProdiID]' />
  <input type=hidden name='sub' value='Simpan' />
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul><input type="text" size="5" name="TahunID" value="$kom[TahunID]" $ro/></td>
      <td class=inp>Mahasiswa:</td>
      <td class=ul><input type="text" size="30" name="NamaMhsw" value="$kom[NamaMhsw]" $ro/></td>
      </tr>
  <tr><td class=inp width=160>NIM:</td>
      <td class=ul><input type="text" size="10" name="MhswID" value="$kom[MhswID]" $ro/></td>
	  <td class=inp>Penasehat Akademik:</td>
      <td class=ul><input type="text" size="30" name="PA" value="$PA" $ro/></td>
      </tr>
  <tr><td class=inp>Tgl Ujian Ulang Komprehensif:</td>
      <td class=ul colspan=3>
      $TglUjian
      </td></tr>
  <tr><td class=inp>Waktu Ujian</td>
      <td class=ul colspan=3>
      $optjamUjian - $optakhirjamUjian
      </td></tr>
	<tr><th class=ttl colspan=4>Biaya Ujian</th></tr>
  <tr><td class=inp>Biaya Ujian Ulang :</td>
      <td class=ul colspan=3><input type=text id="Biaya" name='Biaya'
        size=20 maxlength=20 /> <font color="#FF0000">* Kosongkan jika tidak ingin menambah biaya</font></td>
      </tr>
  <tr><td class=inp>Denda :</td>
      <td class=ul colspan=3>$opt&nbsp;<input type="text" size="4" name="DendaPersen" />%</td>
      </tr>
  <tr><td class=inp>Tanggal Pembayaran :</td>
      <td class=ul colspan=3>$opttglbayar</td>
      </tr>
  <tr><td class=inp>Catatan :</td>
      <td class=ul colspan=3>
      <textarea name='Catatan' cols=40 rows=3 ></textarea>
      </td></tr>

  <tr><td class=inp colspan=4>&nbsp;</td></tr>
  <tr><td class=ttl colspan=4>Pilih Mata Kuliah yang akan diulang :</td></tr>
  <tr><td class=inp colspan=4>
  	<table class=box cellspacing=1 cellpadding=4 width=100%>
	<tr>
		<th class=ttl width="20">&nbsp;</th>
		<th class=ttl>Mata Kuliah</th>
		<th class=ttl width="300">Penguji</th>
		<th class=ttl width="80">Ruang</th>
		<th class=ttl width="80">Nilai</th>
	</tr>
SCR;

    $ks = "select KurikulumKode as _Kurikulum
			from kurikulum where ProdiID = '".$_SESSION[ProdiID]."' and NA = 'N' order by KurikulumKode";
	
	$ksq = _query($ks);
	while ($kur = mysql_fetch_array($ksq)){
		if (substr($kom[MhswID],0,4) >= $kur[_Kurikulum]){
			$kuri = $kur[_Kurikulum];
		} else {
			break;
		}
	}
	
	$s = "select mk.Nama as _NamaMK, mk.MKID as _MKID
			from mk mk
			left outer join kurikulum k on mk.KurikulumID = k.KurikulumID
			where k.KurikulumKode = '$kuri'
			and mk.ProdiID = '".$_SESSION[ProdiID]."'
			and mk.Komprehensif = 'Y'
			and mk.NA = 'N'";
	$sq = _query($s);
	$jum = mysql_num_rows($sq);
	$i = 1;
	while ($m = mysql_fetch_array($sq)){
		
		$p = GetFields("kompredosen kd left outer join dosen d on kd.LoginDosen = d.Login", "kd.MKID = '".$m[_MKID]."'
				and kd.NA", "N", "kd.RuangID as _Ruang, d.Nama as _NamaDosen, d.Login as _LoginDosen, kd.Nilai as _Nilai");
		
	echo <<<SCR
	<tr>
	<td class=cna=Lulus= align="center"><input type="checkbox" id="Ulang$i" name="Ulang$i" value="Y" /></td>
	<td class=cna=Lulus=>$m[_NamaMK]</td>
	<td class=cna=Lulus=>
	<input type="hidden" name="MKID$i" value="$m[_MKID]" />
	<input type="hidden" name="LoginDosen$i" value="$p[_LoginDosen]" />
	<input type=text name="NamaDosen$i" value="$p[_NamaDosen]" size=40 maxlength=50 onKeyUp='javascript:SearchDosens("$_SESSION[ProdiID]","frmJdwl","$i",frmJdwl.NamaDosen$i.value)' $ro/>
	  </td>
	<td class=cna=Lulus= align="center"><input type="text" size="5" name="Ruang$i" value="$p[_Ruang]" $ro/></td>
	<td class=cna=Lulus= align="center"><input type="text" size="5" name="Nilai$i" value="$p[_Nilai]" $ro/></td>
	<tr>
	<tr><td bgcolor=silver colspan=5 height=1></td></tr>
SCR;
$i ++;
}
echo <<<SCR
	</table>
  <input type="hidden" id="jumDsn" name="DosenJum" value="$jum" />
  </td></tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Refresh' value='Refresh'
        onClick="location='../$_SESSION[mnux].kompre.ulang.php?KompreID=$kom[KompreID]'" />
      <input type=button name='Tutup' value='Tutup' onClick="javascript:TutupDong($_SESSION[md],'$kom[KompreID]','$_SESSION[ProdiID]')" />
      </td></tr>
  </form>
  </table>
  
  <script>
  <!--
  function confirmUlang(){
  	var cek = '';
  	num = Number(document.getElementById('jumDsn').value);
	for (i=1;i<=num;i++){
		if (document.getElementById('Ulang'+i).checked == true){
			cek = cek + 'cek ';
		}
	}
	if (cek != ''){
		if (confirm("Anda yakin akan mengulang mata kuliah ini?")){
			return true;
		} else {
			return false;
		}
	} else {
		alert("Pilih Mata kuliah yang akan diulang!");
		return false;
	}
  }
    
  function TutupDong(md,id,prodi) {
    opener.location='../$_SESSION[mnux].kompre.detail.php?md='+md+'&KompreID='+id+'&ProdiID='+prodi+'&ref=1';
    self.close();
    return false;
  }
  //-->
  </script>

SCR;
}

function Simpan() {

	TutupScript();

	$TanggalUjian = "$_REQUEST[TglUjian_y]-$_REQUEST[TglUjian_m]-$_REQUEST[TglUjian_d]  $_REQUEST[TanggalUjian_h]:$_REQUEST[TanggalUjian_n]";
	$TanggalAkhirUjian = "$_REQUEST[TglUjian_y]-$_REQUEST[TglUjian_m]-$_REQUEST[TglUjian_d]  $_REQUEST[AkhirTanggalUjian_h]:$_REQUEST[AkhirTanggalUjian_n]";
	$TglBayar = "$_REQUEST[TglBayar_y]-$_REQUEST[TglBayar_m]-$_REQUEST[TglBayar_d]";

	$s = "update kompre set NA = 'Y' where KompreID = '".$_REQUEST[KompreID]."'";
	$q = _query($s);
	$s = "update kompredosen set NA = 'Y' where KompreID = '".$_REQUEST[KompreID]."'";
	$q = _query($s);
	

	
	if ($_REQUEST[Biaya] != ''){
		$MaxTahun = GetaField('biaya_statusbayarmhsw', 'MhswID', $_REQUEST[MhswID], 'max(TahunBayarID)'); 
	   $s = "insert into biaya_mhsw
      (KodeID, MhswID, TahunID, PMBMhswID,
      JadwalBayarID, Nama, TrxID,
      Jumlah, AngsurKuliah, TanggalBayar, Denda, BesarDenda, Absolute,
	  Catatan, LoginBuat, TanggalBuat)
      values
      ('".KodeID."', '$_REQUEST[MhswID]', '$MaxTahun', 1, 
	  0, 'Ujian Ulang Komprehensif', 1, 
      1, '".$_REQUEST[Biaya]."', '".$TglBayar."', '".$_REQUEST[Denda]."', '".$_REQUEST[DendaPersen]."', 'Y',
	  '".$_REQUEST[Catatan]."', '$_SESSION[_Login]', now())";
	  
	  $q = _query($s);
		$biayaID = GetaField('biaya_mhsw', 'NA', 'N', 'max(MhswBiayaID)'); 
	}
	
	$s = "insert into kompre 
			(TahunID,MhswID,KodeID,TglUjian,TglAkhirUjian,LoginBuat,TanggalBuat,BiayaUlangID)
			values
			('".$_SESSION[TahunID]."','".$_REQUEST[MhswID]."','".KodeID."','$TanggalUjian',
			'$TanggalAkhirUjian','".$_SESSION[_Login]."',now(),'$biayaID')";
	$q = _query($s);
	
	$s = "select MAX(KompreID) as _KompreID from kompre where MhswID = '".$_REQUEST[MhswID]."'"; 
	$q = _query($s);
	$k = mysql_fetch_array($q);
	
	for($i=1;$i<=$_REQUEST[DosenJum];$i++){
		$s = "insert into kompredosen (KompreID,KodeID,MKID,LoginDosen,RuangID,Nilai,Lulus,LoginBuat,TanggalBuat) values
		('".$k[_KompreID]."','".KodeID."','".$_REQUEST[MKID.$i]."','".$_REQUEST[LoginDosen.$i]."','".$_REQUEST[Ruang.$i]."',
		'".$_REQUEST[Nilai.$i]."','Y','".$_SESSION[_Login]."',now())";
		$q = _query($s);
		
		if ($_REQUEST[Ulang.$i] == 'Y'){
			$s = "update kompredosen set Nilai = '0.00', Lulus = 'N' where MKID = '".$_REQUEST[MKID.$i]."' and KompreID = '".$k[_KompreID]."'";
			$q = _query($s);
		}
	}
	
	echo "<script>ttutup()</script>";
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../$_SESSION[mnux].kompre.detail.php?md=$_REQUEST[md]&KompreID=$_REQUEST[KompreID]&ProdiID=$_REQUEST[ProdiID]&ref=1';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}

?>

</BODY>
</HTML>
