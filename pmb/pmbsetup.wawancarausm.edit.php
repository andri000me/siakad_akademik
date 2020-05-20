<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 04 Agustus 2008

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Wawancara - USM");

// *** Parameters ***
$prd = sqling($_REQUEST['prd']);
$id = sqling($_REQUEST['id']);
$md = $_REQUEST['md']+0;

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $prd, $id);

// *** Functions ***
function Edit($md, $prd, $id) {
  $gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
  if ($md == 0) {
    $jdl = "Edit Wawancara USM";
    $w = GetFields('wawancarausm', 'WawancaraUSMID', $id, '*');
  }
  elseif ($md == 1) {
    
	$jdl = "Tambah Wawancara";
    $w = array();
    $w['Tanggal'] = GetaField('pmbperiod', "KodeID='".KodeID."' and PMBPeriodID", $gelombang, 'UjianMulai');
	$w['JamMulai'] = '09:00';
	$w['JamSelesai'] = '09:50';
	$w['PanjangWaktu'] = 0;
	$w['Kapasitas'] = 0;
  }
  else die(ErrorMsg('Error',
    "Terjadi kesalahan.<br />
    Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // Tampilkan
  TampilkanJudul($jdl);
  
  $s1 = "select r.RuangID, r.KapasitasUjian, k.Nama as NamaKampus 
			from ruang r left outer join kampus k on r.KampusID=k.KampusID
			where r.UntukUSM = 'Y' 
			order by r.KampusID, r.RuangID";
  $r1 = _query($s1);
  
  if(!empty($_SESSION['prodi']))
  
  $gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
  $jml = GetaField('pmb', "PMBPeriodID='$gelombang' and Pilihan1", $_SESSION['prodi'], "count(PMBID)");
  
  $ruangopt = GetOption2('ruang', 'RuangID', 'RuangID', $w['RuangID'], "KodeID='".KodeID."'", 'RuangID');
  $Tanggal = GetDateOption($w['Tanggal'], 'Tanggal');
  $JamMulai = GetTimeOption($w['JamMulai'], 'JamMulai');
  $JamSelesai = GetTimeOption($w['JamSelesai'], 'JamSelesai');
  
  CheckFormScript('Ruang,Kapasitas,PanjangWaktu');
  echo "<p><table class=bsc cellspacing=1 width=100%>
  <form name='wawancaraedit' action='../$_SESSION[mnux].wawancarausm.edit.php' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='prd' value='$prd' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><td class=inp>Mata Uji:</td>
      <td class=ul1 colspan=3><input type=text name='xMataUji' value='Wawancara' disabled>
							  <input type=hidden name='MataUji' value='Wawancara'></td>
      </tr>
  <tr><td class=inp>Tanggal Wawancara:</td>
      <td class=ul1 colspan=3>$Tanggal</td>
      </tr>
  <tr><td class=inp>Jam Wawancara:</td>
      <td class=ul1 colspan=3>$JamMulai s/d $JamSelesai</td>
	  </tr>
  <tr><td class=inp>Panjang Waktu Wawancara:<br>(Per Cama)</td>
	  <td class=ul1 colspan=3><input type=text name='PanjangWaktu' value='$w[PanjangWaktu]' size=2 maxlength=3 >  menit 
			<font color=red>*) Masukkan 0 untuk tidak ada pembatasan waktu</font></td>
	  </tr>
  <tr><td class=inp>Kapasitas Cama:</td>
      <td class=ul1 colspan=3><input type=text name='Kapasitas' value='$w[Kapasitas]' size=2 maxlength=3 > orang 
			<font color=red>*) Masukkan 0 untuk tidak ada pembatasan kuota</font></td>
	  </tr>
  <tr><td class=inp>Ruang Wawancara:</td>
	  <td class=ul1 colspan=3><select name='Ruang'>$ruangopt</select></td></tr>
  <tr><td class=ul1 colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' onClick=\"return (CekJam() && CekPanjangWaktu()) \"/>
      <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />
      </td></tr>
  </form>
  </table>
  <script>
	function CekJam()
	{	cek = true;
		jammulai = wawancaraedit.JamMulai_h.value;
		menitmulai = wawancaraedit.JamMulai_n.value;
		jamselesai = wawancaraedit.JamSelesai_h.value;
		menitselesai = wawancaraedit.JamSelesai_n.value;
		if(jammulai > jamselesai)
		{	alert('Jam Mulai tidak boleh lebih telat dari atau sama dengan Jam Selesai');
			cek = false;
		}
		else if(jammulai == jamselesai)
		{	if(menitmulai >= menitselesai)
			{	alert('Jam Mulai tidak boleh lebih telat dari atau sama dengan Jam Selesai');
				cek = false;
			}
		}
		return cek;
	}
	function CekPanjangWaktu()
	{	cek = true;
		jammulai = wawancaraedit.JamMulai_h.value;
		menitmulai = wawancaraedit.JamMulai_n.value;
		jamselesai = wawancaraedit.JamSelesai_h.value;
		menitselesai = wawancaraedit.JamSelesai_n.value;
		panjangwaktu = wawancaraedit.PanjangWaktu.value;
		
		rentangmenit = ((jamselesai - jammulai)*60)+(menitselesai-menitmulai);
		if(panjangwaktu > rentangmenit)
		{	alert('Panjang Waktu Wawancara tidak boleh lebih kecil dari selisih Jam Selesai dan Jam Mulai');
			cek = false;
		}
		
		return cek;
	}
  </script>";
}

function Simpan($md, $prd, $id) {
  TutupScript();
  $Urutan = $_REQUEST['Urutan']+0;
  $PMBUSMID = sqling($_REQUEST['PMBUSMID']);
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $JamMulai = "$_REQUEST[JamMulai_h]:$_REQUEST[JamMulai_n]";
  $JamSelesai = "$_REQUEST[JamSelesai_h]:$_REQUEST[JamSelesai_n]";
  $PanjangWaktu = $_REQUEST['PanjangWaktu']+0;
  $Ruang = $_REQUEST['Ruang'];
  $Kapasitas = $_REQUEST['Kapasitas']+0;
  
  // Simpan
  $gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
  
  $oke = '';
  $w['TanggalUjian'] = $Tanggal;
  $w['JamMulai'] = $JamMulai;
  $w['JamSelesai'] = $JamSelesai;
  $w['RuangID'] = $Ruang;
  $w['PMBPeriodID'] = $gelombang;
  
   $cekada = GetFields('pmbperiod', "KodeID='".KodeID."' and 
            left(WawancaraMulai,10)<= '$Tanggal' and 
            left(WawancaraSelesai,10)>= '$Tanggal' and NA", "N", "*");
  
  $cek = GetFields('pmbperiod', "KodeID='".KodeID."' and NA","N",
            "date_format(WawancaraMulai,'%d %M %Y') as Mulai, 
            date_format(WawancaraSelesai,'%d %M %Y') as Selesai");
  if(empty($cekada)){
    die(ErrorMsg('Kesalahan Tanggal', 
      "Tanggal yang anda setting tidak sesuai dengan Tanggal Wawancara,<br/>
       yaitu dari Tanggal : <b>$cek[Mulai]</b> sampai dengan <b>$cek[Selesai]</b>.
      <hr size=1 color=silver />
      <p align=center>
      <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </p>"));
  }
  
  //$oke .= CekTanggal($w, $id, $prd);
  if (!empty($w['RuangID'])) $oke .= CekRuang($w, $id, $prd, $md);
  
  if(empty($oke))
  {
	  if ($md == 0) {
		$s = "update wawancarausm
		  set Tanggal = '$Tanggal',
			  JamMulai = '$JamMulai', JamSelesai = '$JamSelesai', PanjangWaktu = '$PanjangWaktu',
			  RuangID = '$Ruang', Kapasitas = '$Kapasitas',
			  LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
		  where KodeID = '".KodeID."' and WawancaraUSMID = '$id' ";
		$r = _query($s);
		echo "<script>ttutup()</script>";
	  }
	  elseif ($md == 1) {
			$smax = "select MAX(Urutan) as _maxurutan from `wawancarausm` where PMBPeriodID='$gelombang' and KodeID='".KodeID."'
					group by PMBPeriodID";
			$rmax = _query($smax);
			$wmax = _fetch_array($rmax);
			$maxurutan = $wmax['_maxurutan']+1;	
				
			$s = "insert into wawancarausm
			  (KodeID, ProdiID, PMBPeriodID, Kapasitas, Urutan, 
			  Tanggal, JamMulai, JamSelesai, RuangID, PanjangWaktu,
			  LoginBuat, TanggalBuat)
			  values
			  ('".KodeID."', '$prd', '$gelombang', '$Kapasitas', '$maxurutan',
			  '$Tanggal', '$JamMulai', '$JamSelesai', '$Ruang', '$PanjangWaktu', 
			  '$_SESSION[_Login]', now())";
			$r = _query($s);
			echo "<script>ttutup()</script>";
	  }
	  else {
	  }
   }
   else {
    die(ErrorMsg('Jadwal Bentrok', 
      "Berikut adalah list jadwal yang bentrok: 
      <ol>$oke</ol>
      <div align=center><input type=button name='Tutup' value='Tutup' onClick=\"window.close();\"></div>
	  </p>"));
  }
}

function CekTanggal($w, $id, $prd)
{	$pmbperiod = GetFields('pmbperiod', "PMBPeriodID='$w[PMBPeriodID]' and KodeID", KodeID, "*");
	$a = '';
	
	include_once "../util.lib.php";
	
	// Cek Tanggal Ujian	
	echo "Bandingkan: $w[TanggalUjian] dan $pmbperiod[WawancaraMulai]<br>";
	if(strtotime($w['TanggalUjian']) < strtotime($pmbperiod['WawancaraMulai']) or strtotime($pmbperiod['WawancaraSelesai']) < strtotime($w['TanggalUjian']))
	{	$a .= "<li>
				<b> Tanggal Ujian berada di luar rentang waktu yang disediakan untuk penjadwalan Wawancara </b><br>
				<table class=bsc width=400>
					<tr><td class=inp width=150>Tanggal Jadwal Wawancara Gagal:</td>
					<td class=ul1>".GetDateInWords($w['TanggalUjian'])."</td>
					</tr>
					<tr><td class=inp width=150>Rentang Waktu Wawancara:</td>
					<td class=ul1>".GetDateInWords($pmbperiod['UjianMulai'])." - ".GetDateInWords($pmbperiod['UjianSelesai'])."</td>
					</tr>
				</table>
			   </li>";
	}
	return $a;
}

function CekRuang($w, $id, $prd, $md)
{	$ruangcheck = '';
	$arrRuang = array($w['RuangID']);
	foreach($arrRuang as $ruang)
		$ruangcheck .= (empty($ruangcheck))? "INSTR(concat(',', pu.RuangID, ','), concat(',', '$ruang', ',')) != 0" :
												" or INSTR(concat(',', pu.RuangID, ','), concat(',', '$ruang', ',')) != 0";
	$ruangcheck = "and (".$ruangcheck.")";
	
	$s1 = "select pu.*, pu2.Nama as _NamaUjian
			from prodiusm pu 
				left outer join pmbusm pu2 on pu.PMBUSMID=pu2.PMBUSMID and pu2.KodeID='".KodeID."'
			where pu.PMBPeriodID='$w[PMBPeriodID]'
				$ruangcheck
				and pu.TanggalUjian = '$w[TanggalUjian]'
				and (('$w[JamMulai]:00' <= pu.JamMulai and pu.JamMulai <= '$w[JamSelesai]:59')
					or  ('$w[JamMulai]:00' <= pu.JamSelesai and pu.JamSelesai <= '$w[JamSelesai]:59'))
				and pu.KodeID='".KodeID."'
				";
	$r1 = _query($s1);
	while($w1 = _fetch_array($r1))
	{	$namaprodistring = GetProdiString('|', $w1['ProdiID']);
		$prodiidstring = implode(',', explode('|', $w1['ProdiID']));
		$a .= "<li>
			<b>Jadwal USM bentrok dengan</b>:<br />
			<table class=bsc width=400>
			<tr><td class=inp width=100>Mata Ujian:</td>
			  <td class=ul1>$w1[PMBUSMID] - $w1[_NamaUjian]</td>
			  </tr>
			<tr><td class=inp>Ruang:</td>
			  <td class=ul1>$w1[RuangID]</td>
			  </tr>
			<tr><td class=inp>Tanggal Ujian:</td>
			  <td class=ul1>$w1[TanggalUjian]&nbsp;</td>
			  </tr>
			<tr><td class=inp>Jam Ujian:</td>
			  <td class=ul1>$w1[JamMulai] &minus; $w1[JamSelesai]</td>
			  </tr>
			<tr><td class=inp>Program Studi:</td>
			  <td class=ul1>$namaprodistring <sup>($prodiidstring)</sup></td>
			  </tr>";
		
		$a .= "<tr><td>&nbsp;</td></tr>
			  </table>
			</li>";
	}
	
	$ruangcheck = '';
	$arrRuang = array($w['RuangID']);
	foreach($arrRuang as $ruang)
		$ruangcheck .= (empty($ruangcheck))? "INSTR(concat(',', wu.RuangID, ','), concat(',', '$ruang', ',')) != 0" :
												" or INSTR(concat(',', wu.RuangID, ','), concat(',', '$ruang', ',')) != 0";
	$ruangcheck = "and (".$ruangcheck.")";
	$s1 = "select wu.*
			from wawancarausm wu 
			where wu.PMBPeriodID='$w[PMBPeriodID]'
				$ruangcheck
				and wu.Tanggal = '$w[TanggalUjian]'
				and (('$w[JamMulai]:00' <= wu.JamMulai and wu.JamMulai <= '$w[JamSelesai]:59')
					or  ('$w[JamMulai]:00' <= wu.JamSelesai and wu.JamSelesai <= '$w[JamSelesai]:59'))
				and wu.KodeID='".KodeID."'
				and wu.WawancaraUSMID <> '$id'
				";
	$r1 = _query($s1);
	while($w1 = _fetch_array($r1))
	{	$namaprodistring = GetProdiString('|', $w1['ProdiID']);
		$prodiidstring = implode(',', explode('|', $w1['ProdiID']));
		$a .= "<li>
			<b>Jadwal USM bentrok dengan</b>:<br />
			<table class=bsc width=400>
			<tr><td class=inp width=100>Mata Ujian:</td>
			  <td class=ul1>Wawancara</td>
			  </tr>
			<tr><td class=inp>Ruang:</td>
			  <td class=ul1>$w1[RuangID]</td>
			  </tr>
			<tr><td class=inp>Tanggal Wawancara:</td>
			  <td class=ul1>$w1[Tanggal]&nbsp;</td>
			  </tr>
			<tr><td class=inp>Jam Wawancara:</td>
			  <td class=ul1>$w1[JamMulai] &minus; $w1[JamSelesai]</td>
			  </tr>
			<tr><td class=inp>Program Studi:</td>
			  <td class=ul1>$namaprodistring <sup>($prodiidstring)</sup></td>
			  </tr>";
		$a .= "<tr><td>&nbsp;</td></tr>
			  </table>
			</li>";
	}
	
	return $a;
}

function GetProdiString($divider, $string)
{	$arrProdi = explode($divider, $string);
	$a = '';
	foreach($arrProdi as $perprodi)
	{	$namaprodi = GetaField('prodi', "ProdiID='$perprodi' and KodeID", KodeID, "Nama");
		$a .= (empty($a))? $namaprodi : ', '.$namaprodi;
	}
	return $a;
}

function GabungkanProdi($md, $prd, $id)
{	$idtujuan = $_REQUEST['idtujuan'];
	if($md == 0)
	{	$ProdiID = GetaField('prodiusm', "ProdiUSMID='$id' and KodeID", KodeID, 'ProdiID');
		$arrtempProdiID = explode('|', $ProdiID);
		if(count($arrTempProdiID) == 1)
		{	$s = "delete from prodiusm where ProdiUSMID='$id' and KodeID='".KodeID."'";
			$r = _query($s);
		}
		else
		{	foreach($arrtempProdiID as $key => $tempProdiID)
			{	if($tempProdiID == $prd) unset($arrTempProdiID[$key]);
			}
			$newProdiID=implode('|', $arrtempProdiID);
			$s = "update prodiusm set ProdiID = '$NewProdiID' where ProdiUSMID='$id' and KodeID='".KodeID."'";
			$r = _query($s);
		}
	}
	else if($md == 1)
	{	// Do Nothing, Just update Below
	}
	$arrProdi = explode('|', GetaField('prodiusm', "ProdiUSMID='$idtujuan' and KodeID", KodeID, 'ProdiID'));
	$arrProdi[] = $prd;
	$newProdiID = implode('|', $arrProdi);
	$s = "update prodiusm set ProdiID='$newProdiID'
			where ProdiUSMID='$idtujuan'";
	$r = _query($s);
	TutupScript();
	echo "<script>ttutup()</script>";
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=wawancarausm';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}
?>
