<?php
// Author: Emanuel Setio Dewo
// 23 Feb 2006

$Cari = $_REQUEST['Cari'];
if (empty($Cari)) {
  $_REQUEST['Pesan'] = "Tidak ada yang harus dicari.<hr size=1 />
    Masukkan Nama & Kota dari perguruan tinggi yg dicari dalam format: [<font color=maroon>NamaPT/SingkatanPT, KotaPT</font>]";
  include "pesan.html.php";
}
else {
include "../db.mysql.php";
include_once "../connectdb.php";
include_once "../dwo.lib.php";
include_once "../parameter.php";

echo "<HTML>
  <HEAD>
  <TITLE>Cari Perguruan Tinggi</TITLE>";
echo "<link rel='stylesheet' type='text/css' href='../themes/$_Themes/index.css' />";
echo "</HEAD>
  <BODY>";
TampilkanKembalikanScript();
TampilkanJudul("Daftar Perguruan Tinggi");
TampilkanDaftarPerguruanTinggi();

include_once "disconnectdb.php";
echo "</BODY>
</HTML>";
}

function TampilkanKembalikanScript() {
echo <<<END
  <script>
  <!--
  function kembalikan(PerguruanTinggiID, Nama, Kota){
    creator.AsalSekolah.value = PerguruanTinggiID;
    creator.NamaSekolah.value = Nama + ", " + Kota;
	creator.SavAsalSekolah.value = PerguruanTinggiID;
    window.close();
  }
  -->
  </script>
END;
}

function RemoveBannedWords(&$arr)
{	$arrBannedWords = array('UNIVERSITAS', 'UNI', 'UNIVERSITY');
	
	foreach($arr as $key=>$element)
	{	if(in_array($element, $arrBannedWords))
		{	unset($arr[$key]);
		}
	}
}

function TampilkanDaftarPerguruanTinggi() {
  global $Cari;
  $Max = 50;
 
  $arrcr = explode(',', $Cari);
  $arrcrmixed = array(); $arrcrnama = array(); $arrcrsingkatan = array(); $arrwhrnamaangks = array(); $arrwhrnama = array(); $arrwhrkota = array(); 
  
  if(!empty($arrcr))
  {	
	if(count($arrcr) == 1)
	{	$arrcrmixed = explode(' ', TRIM($arrcr[0]));
		RemoveBannedWords($arrcrmixed);
		foreach($arrcrmixed as $cr)
		{	if($cr+0 > 0) $arrwhrnamaangka[] = "Nama like ' ".TRIM($cr)." ' ";
			else
			{	$arrwhrnama[] = "Nama like '%".TRIM($cr)."%' ";
				$arrcrsingkatan[] = "SingkatanNama like '%".TRIM($cr)."%' ";
				$arrwhrkota[] = "Kota like '%".TRIM($cr)."%' ";
			}
		}
		$divider = "or";
	}
	else
	{	$arrcrnama = explode(' ', TRIM($arrcr[0]));
		
		RemoveBannedWords($arrcrnama);
		foreach($arrcrnama as $cr) 
		{	if($cr+0 > 0) $arrwhrnamaangka[] = "Nama like '% ".TRIM($cr)." %' ";
			else 
			{	$arrwhrsingkatan[] = "SingkatanNama like '%".TRIM($cr)."%' ";
				$arrwhrnama[] = "Nama like '%".TRIM($cr)."%' ";
			}
		}
	    $arrwhrkota[] = "Kota like '%".TRIM($arrcr[1])."%' ";
		$divider = "and";
	}
  }
  
  $whr = '('.((!empty($arrwhrnama))? implode(' or ', $arrwhrnama) : 1).') '.
		((!empty($arrwhrsingkatan))? $divider.' ('.implode(' or ', $arrwhrsingkatan) : ' and (1').') '.
		((!empty($arrwhrnamaangka))? $divider.' ('.implode(' or ', $arrwhrnamaangka) : ' and (1').') '.
		((!empty($arrwhrkota))? $divider.' ('.implode(' or ', $arrwhrkota) : ' and (1').')';
  // Hitung jumlah baris
  $Jml = GetaField('perguruantinggi', "$whr and NA", 'N', "count(PerguruanTinggiID)");
  if ($Jml > $Max) {
    $_Jml = number_format($Jml);
    echo "<p><b>Catatan:</b> Jumlah perguruan tinggi yang Anda cari mencapai: <b>$_Jml</b>, tetapi sistem membatasi
      jumlah perguruan tinggi yang ditampilkan dan hanya menampilkan: <b>$Max</b>.
      Gunakan Nama perguruan tinggi dan Kota Sekolah dengan lebih spesifik untuk membatasi
      jumlah perguruan tinggi yang ditampilkan.</p>

      <p><b>Format Pencarian:</b> NamaPerguruanTinggi/Singkatan, KotaSekolah</p>";
  }
  // Tampilkan
  $s = "select PerguruanTinggiID, SingkatanNama, Nama, Kota
    from perguruantinggi
    where $whr and NA='N'
    order by Nama limit $Max";
  $r = _query($s);
  $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4 align=center>
    <form name='datapt' method=POST>
	<tr><th class=ttl>#</th>
    <th class=ttl>Kode Sekolah</th>
    <th class=ttl>Singkatan</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Kota</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=ul>$n</td>
    <td class=ul><a href='javascript:kembalikan(\"$w[PerguruanTinggiID]\", \"$w[Nama]\", \"$w[Kota]\")'>$w[PerguruanTinggiID]</a></td>
    <td class=ul>$w[SingkatanNama]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[Kota]</td>
    </tr>";
  }
  echo "
	<tr><td colspan=10 align=center><input type=button name='Tutup' value='Tutup' onClick=\"window.close()\"></td></tr>
	</form>
  </table></p>";
}
?>
