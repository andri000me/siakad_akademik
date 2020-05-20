<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Setup Jadwal Ujian Akhir", 1);

echo <<<SCR
  <script src="../$_SESSION[mnux].ujian.script.js"></script>
SCR;

// *** Parameters ***
$KompreID = $_REQUEST['KompreID'];
$ProdiID = $_REQUEST['ProdiID'];
$md = $_REQUEST['md'];
// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'TampilkanSetupUjianKompre' : $_REQUEST['sub'];
$sub($KompreID, $ProdiID, $md);

// *** Functions ***

function TampilkanSetupUjianKompre($KompreID, $ProdiID, $md) {

  $kom = GetFields("kompre k
    left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID='".KodeID."'
    left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID='".KodeID."'
    ", 
    "k.KompreID", $KompreID, 
    "k.*, m.Nama as NamaMhsw, m.MhswID as MhswID,
    d.Nama as NamaPA, d.Gelar as GelarPA, k.TanggalUjian as _TglUjian");
		
	TampilkanHeaderKompre($kom, $ProdiID, $md);
}
function TampilkanHeaderKompre($kom, $ProdiID, $md) {
  $PA = (empty($kom['NamaPA']))? 'Belum diset' : "$kom[NamaPA] <sup>$kom[GelarPA]</sup>";
    
	if ($md == 0){
		$rd = 'readonly=TRUE';
		$jdl = "Jadwal Ujian Komprehensif";
		$TahunAkd = $kom[TahunID];
		$Mhsw = $kom[NamaMhsw];
		$MhswID = $kom[MhswID];
		$PA = $kom[NamaPA];
	} else if ($md == 1){
		$rd = '';
		$jdl = "Tambah Jadwal Ujian Komprehensif";
		$TahunAkd = '';
		$Mhsw = '';
		$MhswID = '';
		$PA = '';
	}

	TampilkanJudul($jdl);
	
  echo <<<SCR
  <table class=bsc cellspacing=1 width=100%>
  <form name="frmJdwl" action='../$_SESSION[mnux].jadwal.php' method=POST  onsubmit="return cekForm()"/>
  <input type=hidden name='KompreID' value='$kom[KompreID]' />
  <input type=hidden name='ProdiID' value='$ProdiID' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='sub' value='Simpan' />
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul><input type="text" size="5" name="TahunID" value="$kom[TahunID]" $rd/></td>
      <td class=inp>Mahasiswa:</td>
      <td class=ul><input type="text" size="30" name="NamaMhsw" value="$kom[NamaMhsw]" $rd/></td>
      </tr>
  <tr><td class=inp width=160>NPM:</td>
      <td class=ul><input type="text" size="13" name="MhswID" value="$kom[MhswID]" $rd/></td>
	  <td class=inp>Penasehat Akademik:</td>
      <td class=ul><input type="text" size="30" name="PA" value="$PA" $rd/></td>
      </tr>
  <tr><td class=inp colspan=4>
  
  	<table class=box cellspacing=1 cellpadding=4 width=100%>
	<tr>
		<th class=ttl width=20>No</th>
		<th class=ttl>Susunan</th>
		<th class=ttl width=340>Penguji</th>
	</tr>
SCR;
	
	$s = "select *
			from komprematauji
			where KodeID='".KodeID."'
				and ProdiID = '$ProdiID'
				and NA = 'N'";
	$sq = _query($s);
	$jum = mysql_num_rows($sq);
	if ($jum == 0){
		$pr = GetaField('prodi','ProdiID',$ProdiID,'Nama');
		 die(ErrorMsg('Warning',
		"Komponen Ujian Komprehensif Untuk Program Studi ".$pr." Belum di set.
		<hr size=1 color=silver />
		<input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
	}

	$i = 1;
	while ($m = mysql_fetch_array($sq)){
		$pr = GetFields('kompredosen kd left outer join dosen d on kd.DosenID = d.Login',
					"kd.KompreMataUjiID = '$m[KompreMataUjiID]' and kd.KompreID = '$kom[KompreID]' and kd.KodeID", 
					KodeID, 
					'kd.*, d.Nama as _NamaDosen, LEFT(kd.JamMulai, 5) as JM, LEFT(kd.JamSelesai, 5) as JS');
		//echo "KompreID: $kom[KompreID], KompreDosenID: $pr[KompreDosenID], $KompreMataUjiID: $m[KompreMataUjiID]<br>";
		//echo "TANGGAL: $pr[Tanggal], JAM MULAI: $pr[JM], JAM SELESAI: $pr[JS], RUANGID: $pr[RuangID]<br>";
		if($md == 1) // jika mode tambah
		{   $dis = 'disabled = false';
			$pr['Lulus'] = 'N';
			$pr['Tanggal'] = date('Y-m-d');
			$pr['JamMulai'] = '09:00';
			$pr['JamSelesai'] = '10:00';
		}
		else
		{	$pr['Tanggal'] = empty($pr['Tanggal'])? date('Y-m-d') : $pr['Tanggal'];
			$pr['JM'] = empty($pr['JM'])? '09:00' : $pr['JM'];
			$pr['JS'] = empty($pr['JS'])? '10:00' : $pr['JS'];
			$dis = 'disabled = true';
		}
		
		$optTanggal = GetDateOption($pr['Tanggal'], 'Tanggal'.$i);
		$optJamMulai = GetTimeOption($pr['JM'], 'JamMulai'.$i);
		$optJamSelesai = GetTimeOption($pr['JS'], 'JamSelesai'.$i);
		
		echo <<<SCR
		<tr>
		<td class=cna$lls align="center">$i</td>
		<td class=cna$lls>$m[KodeKompre] - $m[Nama]</td>
		<td class=cna$lls>
		<input type="hidden" name="KompreMataUjiID$i" value="$m[KompreMataUjiID]" />
		<input type="hidden" name="DosenID$i" value="$pr[DosenID]" />
		<input type=text id="NamaDosen$i" name="NamaDosen$i" value="$pr[_NamaDosen]" size=30 maxlength=50 
			onKeyUp="javascript:CariDosen('$ProdiID','frmJdwl', '$i')"/>
			<a href="javascript:CariDosen('$ProdiID','frmJdwl', '$i')">Cari...</a>
		  </td>
		<tr>
		<tr><td bgcolor=silver colspan=7 height=1></td></tr>
SCR;

	$i++;
}

if ($kom[Lulus] == 'N'){
	$submit = "<input type=submit name='Simpan' value='Simpan'>
      <input type=button name='Refresh' value='Refresh'
        onClick=\"location='../$_SESSION[mnux].jadwal.php?KompreID=$kom[KompreID]'\" />";
} else {
	$submit = '';
}
echo <<<SCR
	</table>
  <input type="hidden" id="DosenJum" name="DosenJum" value="$jum" />
  </td></tr>
  <tr><td class=ul colspan=4 align=center>
      $submit
      <input type=button name='Tutup' value='Tutup' onClick='javascript:TutupDong()' />
      </td></tr>
  </form>
  </table>
  
<p>
	<div class='box0' id='caridosen'></div>
	<div class='box0' id='cariruang'></div>
  <script>
  <!--
  function cekForm(){
  	var cek1 = '';
	var num = Number(document.getElementById('DosenJum').value);
	for (i=1;i<=num;i++){
		if (document.getElementById('NamaDosen'+i).value == '' || document.getElementById('Ruang'+i).value == ''){
			cek1 = 'kosong';
		}
	}
	if (cek1 == ''){
		return true;
	} else {
		alert ('Masukkan nama penguji dan ruangan ujian pada setiap mata kuliah');
		return false;
	}
  }
  function toggleBox(szDivID, iState) // 1 visible, 0 hidden
  { if(document.layers)	   //NN4+
    {
       document.layers[szDivID].visibility = iState ? "show" : "hide";
    }
    else if(document.getElementById)	  //gecko(NN6) + IE 5+
    {
        var obj = document.getElementById(szDivID);
        obj.style.visibility = iState ? "visible" : "hidden";
    }
    else if(document.all)	// IE 4
    {
        document.all[szDivID].style.visibility = iState ? "visible" : "hidden";
    }
  }
  function CariDosen(ProdiID, frm, count) {
	if (eval(frm + ".NamaDosen"+count+".value != ''")) {
	  eval(frm + ".NamaDosen"+count+".focus()");
	  showDosen(ProdiID, frm, eval(frm +".NamaDosen"+count+".value"), count, 'caridosen');
	  toggleBox('caridosen', 1);
    }
  }
  function CariRuang(ProdiID, frm, count) {
	if (eval(frm + ".RuangID"+count+".value != ''")) {
      eval(frm + ".RuangID"+count+".focus()");
      showRuang(ProdiID, frm, eval(frm +".RuangID"+count+".value"), count, 'cariruang');
      toggleBox('cariruang', 1);
    }
  }
  function TutupDong() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&sub=';
    self.close();
    return false;
  }
  //-->
  </script>
</p>
SCR;
}
function Simpan($KompreID, $ProdiID, $md) {

	TutupScript();

	$JmlDosen = $_REQUEST['DosenJum'];
	
	$DosenID = $_REQUEST['DosenID1'];
	$Tanggal = $_REQUEST['Tanggal1_y'].'-'.$_REQUEST['Tanggal1_m'].'-'.$_REQUEST['Tanggal1_d'];
	$JamMulai = $_REQUEST['JamMulai1_h'].':'.$_REQUEST['JamMulai1_n'].':00';
	$JamSelesai = $_REQUEST['JamSelesai1_h'].':'.$_REQUEST['JamSelesai1_n'].':00';
	$RuangID = $_REQUEST['RuangID1'];
	
	$s = "update kompre set DosenID='$DosenID', TanggalUjian='$Tanggal', JamMulai='$JamMulai', JamSelesai='$JamSelesai', RuangID='$RuangID'
			where KompreID='$_REQUEST[KompreID]' and KodeID='".KodeID."'";
	$r = _query($s);
	
	for($i = 1; $i <= $JmlDosen; $i++)
	{	
		$KompreMataUjiID = $_REQUEST['KompreMataUjiID'.$i];
		$DosenID = $_REQUEST['DosenID'.$i];
		$Tanggal = $_REQUEST['Tanggal'.$i.'_y'].'-'.$_REQUEST['Tanggal'.$i.'_m'].'-'.$_REQUEST['Tanggal'.$i.'_d'];
		$JamMulai = $_REQUEST['JamMulai'.$i.'_h'].':'.$_REQUEST['JamMulai'.$i.'_n'].':00';
		$JamSelesai = $_REQUEST['JamSelesai'.$i.'_h'].':'.$_REQUEST['JamSelesai'.$i.'_n'].':00';
		$RuangID = $_REQUEST['RuangID'.$i];
		
		$ada = GetaField('kompredosen', "KompreID = '$KompreID' and KompreMataUjiID = '$KompreMataUjiID' and KodeID", KodeID, "KompreDosenID");
		if(empty($ada))
		{	$s = "insert into kompredosen 
					(KompreID, KodeID, KompreMataUjiID, DosenID, 
						Tanggal, JamMulai, JamSelesai, RuangID,
						LoginBuat,TanggalBuat) 
				  values
					('$_REQUEST[KompreID]', '".KodeID."', '$KompreMataUjiID', '$DosenID', 
					'$Tanggal', '$JamMulai', '$JamSelesai', '$RuangID',
					'$_SESSION[_Login]',now())";
			$q = _query($s);
		}
		else  
		{	$s = "update kompredosen set DosenID = '$DosenID' ,
			Tanggal = '$Tanggal', JamMulai = '$JamMulai', JamSelesai = '$JamSelesai', 
			RuangID = '$RuangID', LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
			where KompreID = '$_REQUEST[KompreID]' and KompreMataUjiID = '$KompreMataUjiID' and KodeID='".KodeID."'";
			$q = _query($s);	
		}
	}
	echo "<script>ttutup()</script>";
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
