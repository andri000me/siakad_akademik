<?php
	// *** Main ***
	
	session_start();
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	
	$ProdiUSMID = $_REQUEST['ProdiUSMID']+0;
	$gel = $_REQUEST['gel'];
	$PMBID = $_REQUEST['PMBID'];
	$ruangidx = GetSetVar("ruangidx$ProdiUSMID");
	//echo "ProdiUSM: $ProdiUSMID, GEL: $gel, PMBID: $PMBID, RuangID: $ruangidx";
?>
	
<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE><?php echo $_Institution; ?></TITLE>
  <META http-equiv="cache-control" content="max-age=0">
  <META http-equiv="pragma" content="no-cache">
  <META http-equiv="expires" content="0" />
  <META http-equiv="content-type" content="text/html; charset=UTF-8">
  
  <META content="Emanuel Setio Dewo" name="author" />
  <META content="Sisfo Kampus" name="description" />
  
  <link rel="stylesheet" type="text/css" href="../themes/<?=$_Themes;?>/index.css" />
  <link rel="stylesheet" type="text/css" href="../themes/<?=$_Themes;?>/ddcolortabs.css" />
  
  <script type="text/javascript" language="javascript" src="../include/js/dropdowntabs.js"></script>
  <!-- <script type="text/javascript" language="javascript" src="include/js/jquery.js"></script> -->
  <script type="text/javascript" languange="javascript" src="../floatdiv.js"></script>
  
  
  <script src="../fb/jquery.pack.js" type="text/javascript"></script>
  <link href="../fb/facebox.css" media="screen" rel="stylesheet" type="text/css" />
  <script src="../fb/facebox.js" language='javascript' type="text/javascript"></script>
  
  <script type="text/javascript" language="javascript" src="../include/js/boxcenter.js"></script>
  
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('a[rel*=facebox]').facebox() 
    })
  </script>
  </HEAD>
<BODY>
	
<?php	
	$gos = (empty($_REQUEST['gos']))? 'ListKelas' : $_REQUEST['gos'];
	$gos($PMBID, $ProdiUSMID, $ruangidx, $gel);
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
	
	function ListKelas($PMBID, $ProdiUSMID, $ruangid, $gel)
	{	
		$prodiusm = GetFields("prodiusm pu left outer join pmbusm pu2 on pu.PMBUSMID=pu2.PMBUSMID and pu2.KodeID='".KodeID."'", 'pu.ProdiUSMID', $ProdiUSMID, 'pu.*, pu2.Nama, pu2.CaraPenempatan');
		
		$arrRuang = explode(',', $prodiusm['RuangID']);
		$ruangidx = (empty($ruangid))? $arrRuang[0] : $ruangid;
		
		//Cek apakah sudah terdaftar di 1 kelas
		$ada = GetaField('ruangusm', "ProdiUSMID='$ProdiUSMID' and PMBPeriodID='$gel' and PMBID='$PMBID' and KodeID", KodeID, 'RuangUSMID');
		$KursiKosong = 'Y';
		if(empty($ada))
		{	$KursiKosong = 'N';
			if($prodiusm['CaraPenempatan'] == 'Urut')
			{	$ruangkosong = ''; $urutankosong = '';
				foreach($arrRuang as $a)
				{	$countCama = GetaField('ruangusm', "ProdiUSMID='$ProdiUSMID' and PMBPeriodID='$gel' and RuangID='$ruangidx' and KodeID", KodeID, 'count(PMBID)');
					if($countCama < GetaField('ruang', "RuangID='$ruangidx' and KodeID", KodeID, 'KapasitasUjian')) 
					{	$ruangkosong = $a;
						$urutankosong = $countCama+1;
						break;
					}
				}
				if(empty($ruangkosong))
				{	die(ErrorMsg("Peringatan", "Tidak ada tempat di semua ruangan yang telah dipersiapkan untuk USM.<br>
												Harap menghubungi Kepala PMB untuk menambahkan jumlah ruangan yang tersedia"));
				}
				$ruangidx = $ruangkosong;
			}
			else if($prodiusm['CaraPenempatan'] == 'Acak')
			{	$ruangkosong = ''; $urutankosong = '';
				$arrRuangKosong = array();
				foreach($arrRuang as $a)
				{	$s = "select DISTINCT(ru.RuangID), count(ru.RuangUSMID) as _countCama
							from ruangusm ru
							where ru.ProdiUSMID='$ProdiUSMID' and ru.RuangID='$a' and ru.PMBPeriodID='$gel' and ru.KodeID='".KodeID."'
							group by ru.RuangID";
					$r = _query($s);
					$KapasitasUjian = GetaField('ruang', "RuangID='$a' and KodeID", KodeID, "KapasitasUjian");
					if(_num_rows($r) == 0) $arrRuangKosong[] = $a;
					else
					{	$w = _fetch_array($r);
						if($w['_countCama'] < $KapasitasUjian) $arrRuangKosong[] = $a; 
					}
				}
				
				if(empty($arrRuangKosong)) die(ErrorMsg("Peringatan", "Tidak ada tempat di semua ruangan yang telah dipersiapkan untuk USM.<br>
												Harap menghubungi Kepala PMB untuk menambahkan jumlah ruangan yang tersedia"));
				$ruangkosong = $arrRuangKosong[rand(0, count($arrRuangKosong)-1)];
				$ruangidx = $ruangkosong;
				$KapasitasMaksimum = GetaField("ruang", "RuangID='$ruangkosong' and KodeID", KodeID, 'KapasitasUjian');
				
				$arrKursiKosong = array();
				$s = "select UrutanDiRuang from ruangusm where ProdiUSMID='$ProdiUSMID' and PMBPeriodID='$gel' and RuangID='$ruangidx' and KodeID='".KodeID."' order by UrutanDiRuang";
				$r = _query($s);
				$n = 0;
				while($w = _fetch_array($r))
				{	$n++;
					if($n < $w['UrutanDiRuang'])
					{	while($n < $w['UrutanDiRuang']) 
						{	$arrKursiKosong[] = $n;
							$n++;
						}
					}
				}
				if($n < $KapasitasMaksimum)
				{	while($n < $KapasitasMaksimum)
					{	$n++;
						$arrKursiKosong[] = $n;
					}
				}
				$urutankosong = $arrKursiKosong[rand(0, count($arrKursiKosong)-1)];				
			}
			else
			{	// Manual, maka tidak melakukan apa2
				$KursiKosong = 'Y';
			}
			
			// Masukkan secara otomatis ke dalam database
			$s = "insert into ruangusm set PMBID='$PMBID', 
							ProdiUSMID='$ProdiUSMID',
							PMBPeriodID='$gel',
							RuangID='$ruangkosong',
							UrutanDiRuang='$urutankosong',
							KodeID='".KodeID."',
							TanggalBuat=now(),
							LoginBuat='$_SESSION[_Login]'";
			$r = _query($s);
			$RuangSiswa = $ruangkosong;
			$UrutanSiswa = $urutankosong;
		}
		else 
		{	$ruangusm = GetFields('ruangusm', "RuangUSMID='$ada' and KodeID", KodeID, 'UrutanDiRuang, RuangID');
			if(empty($ruangusm['RuangID'])) $KursiKosong = 'Y';
			else $KursiKosong = 'N';
			$RuangSiswa = $ruangusm['RuangID'];
			$UrutanSiswa = $ruangusm['UrutanDiRuang'];
		}
		
		// Buat Header
		$ruang = GetFields('ruang', "RuangID='$RuangSiswa' and KodeID", KodeID, 'KapasitasUjian, KolomUjian');
		//echo "Urutan: $UrutanSiswa, Kolom: $ruang[KolomUjian], Baris: $BanyakBaris";
		$kolombarisstring = ($ruang['KolomUjian']+0 == 0)? "" : 
			"Ruang: ".$RuangSiswa.", Kolom: ".(($UrutanSiswa-1)%$ruang['KolomUjian']+1).", Baris: ".((floor(($UrutanSiswa-1)/$ruang['KolomUjian']))+1);
		echo  "<p><table class=bsc width=100%><tr width=100%>
					<td class=ul1><font size=5>Mata Uji: $prodiusm[Nama]</font>
				  <input type=button name='Tutup' value='Tutup' onClick=\"ttutup()\">
				  <br><sup>Cara Penempatan: $prodiusm[CaraPenempatan]</sup></td>
				  <td class=inp>
				  <img src='../img/kursi$KursiKosong.jpg'><sup>$kolombarisstring</sup></td></tr>
				  </table></p>";
		echo "<table class=bsc cellspacing=1 align=center>";
		$ruangcount = 0;
		foreach ($arrRuang as $a) {
			$sel = ($ruangidx == $a)? 'class=menuaktif' : 'class=menuitem';
			$NamaRuang = GetaField('ruang', "RuangID='$a' and KodeID", KodeID, 'Nama');
			echo "<td $sel><a href='?mnux=$_SESSION[mnux]&gos=&ruangidx$ProdiUSMID=$a&PMBID=$PMBID&ProdiUSMID=$ProdiUSMID&gel=$gel'>$NamaRuang</a></td>";
			$ruangcount++;
		}
		echo "</table>";	
		
		// Gambar tabelnya.
		$ruang = GetFields('ruang', "RuangID='$ruangidx' and KodeID", KodeID, 'KapasitasUjian, KolomUjian');
		$BanyakBaris = ceil($ruang['KapasitasUjian']/$ruang['KolomUjian']);
		$arrSiswa = array();
		$s = "select * from ruangusm where ProdiUSMID='$ProdiUSMID' and PMBPeriodID='$gel' and RuangID='$ruangidx' and KodeID='".KodeID."'";
		$r = _query($s);
		while($w = _fetch_array($r))
		{	$arrSiswa[$w['UrutanDiRuang']] = $w['PMBID'];
		}
		
		echo "<table class=bsc cellspacing=1 border=1 align=center>";
		$n= 0;
		for($i = 1; $i <= $BanyakBaris; $i++)
		{	echo "<tr>";
			for($j = 1; $j <= $ruang['KolomUjian']; $j++)
			{	$n++;$class='';
			
				if(empty($arrSiswa[$n]))
				{	
					if ($prodiusm['CaraPenempatan'] == 'Urut'){
						$ahref = "";
					} else {
						$ahref ="<a href='#' onClick=\"PilihKursiIni('$PMBID', $n, '$ProdiUSMID', '$gel', '$ruangidx')\">";
					}
					$entry = $ahref."
								<img src='../img/kursi.jpg'>
								<br><sup>Kolom $j, Baris $i</sup></a>";
				}
				else
				{	if($arrSiswa[$n] == $PMBID) $class='class=wrn';
					$cama = GetFields('pmb', "PMBID='$arrSiswa[$n]' and KodeID", KodeID, '*');
					$dataSiswa = "<sup>$arrSiswa[$n]</sup><br>$cama[Nama]";
					$entry = "<b>$arrSiswa[$n]
							  <br>$cama[Nama]</b>
							  <br><sup>Kolom $j, Baris $i</sup>";
				}
				echo"<td $class width=140 height=40 align=center valign=center>$entry</td>";
			}
			echo "</tr>";
		}
		echo "</table>
			  <script>
				function PilihKursiIni(pmbid, urutan, prodiusmid, gel, ruangid)
				{	lnk = '../$_SESSION[mnux].frame.save.php?pmbid='+pmbid+'&urutan='+urutan+'&prodiusmid='+prodiusmid+'&gel='+gel+'&ruangid='+ruangid;
					win2 = window.open(lnk, '', 'width=0, height=0, scrollbars, status');
					if (win2.opener == null) childWindow.opener = self;
					win2.creator = self;
				}
				function ttutup() {
					top.opener.location='../index.php?mnux=$_SESSION[mnux]&_pmbPage=0';
					top.close();
					return false;
				}
			  </script>";
	}
?>

  <script>
  JSFX_FloatDiv("divInfo", 0, 100).flt();
  </script>
</BODY>

</HTML>
