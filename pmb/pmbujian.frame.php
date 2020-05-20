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
	$_usm_ruang = GetSetVar('_usm_ruang');
	//echo "ProdiUSM: $ProdiUSMID, GEL: $gel, PMBID: $PMBID, RuangID: $_usm_ruang";
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
	$gos($ProdiUSMID, $_usm_ruang, $gel);
	// *** Functions ***
	function ListKelas($ProdiUSMID, $ruangid, $gel)
	{	
		$prodiusm = GetFields("prodiusm pu left outer join pmbusm pu2 on pu.PMBUSMID=pu2.PMBUSMID and pu2.KodeID='".KodeID."'", 'pu.ProdiUSMID', $ProdiUSMID, 'pu.*, pu2.Nama, pu2.CaraPenempatan');
		$arrRuang = explode(',', $prodiusm['RuangID']);
		$_usm_ruang = (empty($ruangid))? $arrRuang[0] : $ruangid;
		
		//Cek apakah sudah terdaftar di 1 kelas
		echo "<table class=bsc cellspacing=1 align=center>
				<form name='USM' action='?mnux=$_SESSION[mnux]&gos=SavData' method=POST>
				<input type=hidden name='ProdiUSMID' value='$ProdiUSMID'>
				<input type=hidden name='gel' value='$gel'>
				<input type=hidden name='_usm_jenisx' value='$_SESSION[_usm_jenisx]'>";
		$ruangcount = 0;
		foreach ($arrRuang as $a) {
			$sel = ($_usm_ruang == $a)? 'class=menuaktif' : 'class=menuitem';
			$NamaRuang = GetaField('ruang', "RuangID='$a' and KodeID", KodeID, 'Nama');
			echo "<td $sel><a href='?mnux=$_SESSION[mnux]&gos=&_usm_ruang=$a&PMBID=$PMBID&ProdiUSMID=$ProdiUSMID&gel=$gel'>$NamaRuang</a></td>";
			$ruangcount++;
		}
		echo "</table>";	
		
		// Gambar tabelnya.
		$ruang = GetFields('ruang', "RuangID='$_usm_ruang' and KodeID", KodeID, 'KapasitasUjian, KolomUjian');
		$BanyakBaris = ceil($ruang['KapasitasUjian']/$ruang['KolomUjian']);
		$arrSiswa = array();
		$s = "select * from ruangusm where ProdiUSMID='$ProdiUSMID' and PMBPeriodID='$gel' and RuangID='$_usm_ruang' and KodeID='".KodeID."'";
		$r = _query($s);
		while($w = _fetch_array($r))
		{	$arrSiswa[$w['UrutanDiRuang']] = $w['PMBID'];
			$arrRuangUSMID[$w['UrutanDiRuang']] = $w['RuangUSMID'];
			$arrKehadiran[$w['UrutanDiRuang']] = $w['Kehadiran'];
			$arrNilaiUSM[$w['UrutanDiRuang']] = $w['NilaiUSM'];
		}
		
		echo "<table class=bsc cellspacing=1 border=1 align=center>";
		$n= 0;
		for($i = 1; $i <= $BanyakBaris; $i++)
		{	echo "<tr>";
			for($j = 1; $j <= $ruang['KolomUjian']; $j++)
			{	$n++;$class='';$entry=''; $tambahan='';
				if(empty($arrSiswa[$n]))
				{	$entry = "<img src='../img/kursi.jpg'>
								<br><sup>Kolom $j, Baris $i</sup>";
					$class = "class=cnaY";
				}
				else
				{	
					$cama = GetFields('pmb', "PMBID='$arrSiswa[$n]' and KodeID", KodeID, '*');
					$dataSiswa = "<sup>$arrSiswa[$n]</sup><br>$cama[Nama]";
					$entry = "<b>$arrSiswa[$n]
							  <br>$cama[Nama]</b>
							  <br><sup>Kolom $j, Baris $i</sup>";
					if($_SESSION['_usm_jenisx'] == 1)
					{	if($arrKehadiran[$n] == 'Y')
							$tambahan = "<br><input type=hidden name='Index$n' value='$arrRuangUSMID[$n]'>
										Nilai: <input type=text name='Nilai$n' value='$arrNilaiUSM[$n]' size=1 maxlength=3 style='text-align: right'>";
						else
							$tambahan = "<br><font color=red>Tidak Hadir</font>";
					}
					else
					{	$sel = ($arrKehadiran[$n] == 'Y')? "checked" : "";
						$tambahan = "<br><b>Hadir?</b>
										 <input type=hidden name='Index$n' value='$arrRuangUSMID[$n]'>
										 <input type=checkbox name='Pilihan$n' value='Y' $sel>";
					}
				}
				echo"<td $class width=140 height=50 align=center valign=center>$entry$tambahan</td>";
			}
			echo "</tr>";
		}
		echo "		<input type=hidden name='Jumlah' value='$n'>
					<tr><td colspan=10 align=center><input type=submit name='Simpan' value='Simpan Data Kelas Ini'></td></tr>
				</form>
			</table>";
	}
	
function SavData($ProdiUSMID, $ruangid, $gel)
{	include_once "statusaplikan.lib.php";

	$Jumlah = $_REQUEST['Jumlah'];
	$ProdiUSMID = $_REQUEST['ProdiUSMID'];
	$gel = $_REQUEST['gel'];
	
	if($_SESSION['_usm_jenisx'] == 1)
	{	for($i = 1; $i <= $Jumlah; $i++)
		{	$_index = $_REQUEST["Index$i"];
			$_nilai = $_REQUEST["Nilai$i"];
			
			$s = "update ruangusm set NilaiUSM='$_nilai' where RuangUSMID='$_index' and KodeID='".KodeID."'";
			$r = _query($s);
		}
	}
	else
	{	for($i = 1; $i <= $Jumlah; $i++)
		{	$_index = $_REQUEST["Index$i"];
			$_pilih = $_REQUEST["Pilihan$i"];

			if(empty($_pilih))
			{	
				$s = "update ruangusm set Kehadiran='N' where RuangUSMID='$_index' and KodeID='".KodeID."'";
				$r = _query($s);
			}
			else
			{	
				$s = "update ruangusm set Kehadiran='Y' where RuangUSMID='$_index' and KodeID='".KodeID."'";
				$r = _query($s);
				//$AplikanID = GetaField('ruangusm ru left outer join pmb p on ru.PMBID=p.PMBID', "ru.RuangUSMID='$_index' and ru.KodeID", KodeID, "p.AplikanID");
				$PMBID = GetaField('ruangusm', "RuangUSMID='$_index' and KodeID", KodeID, "PMBID");
				$AplikanID = GetaField('pmb', "PMBID='$PMBID' and KodeID", KodeID, "AplikanID");
				SetStatusAplikan('USM', $AplikanID, $gel);
			}
			
		}
	}
	echo "<script>location='../$_SESSION[mnux].frame.php?gos=&ProdiUSMID=$ProdiUSMID&gel=$gel'</script>";
}	
?>

  <script>
  JSFX_FloatDiv("divInfo", 0, 100).flt();
  </script>
</BODY>

</HTML>
