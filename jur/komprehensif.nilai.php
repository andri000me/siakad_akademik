<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit Ujian Akhir", 1);

// *** Parameters ***
$KompreID = GetSetVar('KompreID');
$ProdiID = GetSetVar('ProdiID');
$md = GetSetVar('md');

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'TampilkanEditUjianKompre' : $_REQUEST['sub'];
$sub($md);

// *** Functions ***

function TampilkanEditUjianKompre($md) {
  $kom = GetFields("kompre k
    left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID='".KodeID."'
    left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID='".KodeID."'
    ", 
    "k.KompreID", $_SESSION['KompreID'], 
    "k.*, m.Nama as NamaMhsw, m.MhswID as MhswID,
    d.Nama as NamaPA, d.Gelar as GelarPA");
		
	TampilkanHeaderKompre($md,$kom);
}
function TampilkanHeaderKompre($md,$kom) {
  $PA = (empty($kom['NamaPA']))? 'Belum diset' : "$kom[NamaPA] <sup>$kom[GelarPA]</sup>";
  $NamaPenguji = GetaField('dosen', "KodeID='".KodeID."' and Login", $kom['Penguji'], 'Nama');
  
	$lulus = ($kom['Lulus'] == 'Y')? 'checked' : '';
	
	if ($md == 0){
		$jdl = "Edit Nilai Ujian Akhir";
		$TahunAkd = $kom[TahunID];
		$Mhsw = $kom[NamaMhsw];
		$MhswID = $kom[MhswID];
		$PA = $kom[NamaPA];
		$ro = "readonly=TRUE";
		
	} else if ($md == 1){
		$jdl = "Input Nilai Ujian Akhir";
		$TahunAkd = '';
		$Mhsw = '';
		$MhswID = '';
		$PA = '';
		$ro = "";
	}
	TampilkanJudul($jdl);
	
	$s = "select * from kompredosen where KompreID = '".$_SESSION['KompreID']."' and Lulus = 'N'";
	$q = _query($s);
	$simpan = (mysql_num_rows($q) == 0)? '' : "<input type=submit name='Simpan' value='Simpan' />";
	$cekLulus = (mysql_num_rows($q) == 0)? '' : "<input type='checkbox' id='Lulus' name='Lulus' value='Y' $lulus /><font color=red>Beri tanda centang jika Lulus</font>";
  
  echo <<<SCR
  <table class=bsc cellspacing=1 width=100%>
  <form name="frmNilai" action='../$_SESSION[mnux].nilai.php' method=POST onsubmit="return confirmLulus()" />
  <input type=hidden name='KompreID' value='$kom[KompreID]' />
  <input type=hidden name='MhswID' value='$kom[MhswID]' />
  <input type=hidden name='sub' value='Simpan' />
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul><input type="text" size="5" name="TahunID" value="$kom[TahunID]" $ro/></td>
	  </tr>
  <tr><td class=inp width=160>NPM:</td>
      <td class=ul><input type="text" size="13" name="MhswID" value="$kom[MhswID]" $ro/></td> 
	  </tr>
  <tr>	  
      <td class=inp>Mahasiswa:</td>
      <td class=ul><input type="text" size="30" name="NamaMhsw" value="$kom[NamaMhsw]" $ro/></td>
      </tr>
  <tr><td class=inp>Penasehat Akademik:</td>
      <td class=ul><input type="text" size="30" name="PA" value="$PA" $ro/></td>
      </tr>
  <tr><td class=inp>Nilai Rata-rata:</td>
      <td class=ul>
	  <input type="text" size="5" id="NilRata" name="NilaiRata" value="$kom[NilaiRata]" $ro/>
	  $cekLulus
	  </td>
      </tr>  
  <tr><td class=inp colspan=2>
  
  	<table class=box cellspacing=1 cellpadding=4 width=100%>
	<tr>
		<th class=ttl width="20" align=center>No</th>
		<th class=ttl align=center>Tim Penguji</th>
		<th class=ttl width="80" align=center>Nilai</th>
	</tr>
SCR;

	$s = "select kd.KompreMataUjiID, kmu.KodeKompre as _KodeKompreMataUji, kmu.Nama as _NamaKompreMataUji, kd.Nilai as _Nilai, kd.Lulus as _Lulus
			from kompredosen kd
				left outer join komprematauji kmu on kmu.KompreMataUjiID=kd.KompreMataUjiID
			where kd.KompreID = '".$_SESSION['KompreID']."'
			and kd.NA = 'N'";
	$sq = _query($s);
	$i = 1;
	
	$MKJum = mysql_num_rows($sq);
	
	while ($m = mysql_fetch_array($sq)){
	
	$ro = ($m[_Lulus] == 'Y')? 'readonly = true' : '';
	echo <<<SCR
	<tr>
	<td class=cna$m[_Lulus] align="center">$i</td>
	<td class=cna$m[_Lulus] align=center>$m[_KodeKompreMataUji] - $m[_NamaKompreMataUji]</td>
	<td class=cna$m[_Lulus] align=center>
	<input type=hidden name='KompreMataUjiID$i' value='$m[KompreMataUjiID]' />
	<input type="text" size="5" id="Nil$i" name="Nilai$i" value="$m[_Nilai]" onkeyup="HitungNilai('$MKJum')" onblur="HitungNilai('$MKJum')" $ro/></td>
	<tr>
	<tr><td bgcolor=silver colspan=4 height=1></td></tr>
SCR;
$i ++;
}
echo <<<SCR
	</table>
  </td></tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=hidden name='MKJum' value='$MKJum' />
	  $simpan
      <input type=button name='Refresh' value='Refresh'
        onClick="location='../$_SESSION[mnux].nilai.php?KompreID=$kom[KompreID]'" />
      <input type=button name='Tutup' value='Tutup' onClick='javascript:TutupDong()' />
      </td></tr>
  </form>
  </table>
  
  <script>
  <!--
  function confirmLulus(){
  	if (document.getElementById("Lulus").checked == true){
		if (confirm("Anda yakin akan meluluskan mahasiswa ini?"+String.fromCharCode(10)+"Nilai ujian tidak akan dapat dirubah lagi.")){
			return true;
		} else {
			return false;
		}
	}
  }
  
  function HitungNilai(jum) {
  	var nil = 0;
  	for (i=1;i<=jum;i++){
		nil = nil + Number(document.getElementById("Nil"+i).value);
	}
	rata2 = Math.floor((nil/jum)*100)/100;
	document.getElementById("NilRata").value = rata2;
  }
  
  function TutupDong() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&sub=';
    self.close();
    return false;
  }
  //-->
  </script>

SCR;
}
function Simpan() {

	TutupScript();
	if ($_REQUEST[Lulus] == '') $_REQUEST[Lulus] = 'N';
	
	$s = "update kompre set NilaiRata = '".$_REQUEST[NilaiRata]."', Lulus = '".$_REQUEST[Lulus]."',
		 LoginEdit = '".$_SESSION[_Login]."', TanggalEdit = now() where KompreID = '".$_REQUEST[KompreID]."'";
	$q = _query($s);
	
	for($i=1;$i<=$_REQUEST[MKJum];$i++){
		$s = "update kompredosen set Nilai = '".$_REQUEST[Nilai.$i]."',
		LoginEdit = '".$_SESSION[_Login]."', TanggalEdit = now()
		where KompreID = '".$_REQUEST[KompreID]."' and KompreMataUjiID = '".$_REQUEST[KompreMataUjiID.$i]."'";
		$q = _query($s);
	}
	
	if ($_REQUEST[Lulus] == 'Y'){
		$s = "update kompredosen set Lulus = '".$_REQUEST[Lulus]."'
			where KompreID = '".$_REQUEST[KompreID]."' and Lulus = 'N' and NA = 'N'";
		$q = _query($s);
		
	} 
	
	echo "<script>
	opener.location='../index.php?mnux=$_SESSION[mnux]';
	window.close();
	//location='../$_SESSION[mnux].nilai.php?KompreID=$_REQUEST[KompreID]&ProdiID=$_REQUEST[ProdiID]';
	</script>";
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}

?>

</BODY>
</HTML>
