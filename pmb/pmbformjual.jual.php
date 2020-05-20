<?php
session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Penjualan Formulir");

// *** Parameters ***
$gel = sqling($_REQUEST['gel']);
$id = $_REQUEST['id']+0;
$tn = $_REQUEST['tn'];
$tnpmb = $_REQUEST['tnpmb'];
// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Jualan' : $_REQUEST['gos'];
$gos($gel, $id, $tn, $tnpmb);

// *** Functions ***


function Jualan($gel, $id, $tn, $tnpmb) {
  TampilkanJudul("Jual Formulir - $gel");
  $frm = GetFields('pmbformulir', 'PMBFormulirID', $id, '*');
  $hrg = number_format($frm['Harga']);
  if(!empty($tn))
	{	
		$grey = "D3D3D3";
		$ss = "select * from `aplikan` where AplikanID='$tn'";
		$rr = _query($ss);
		$ww = _fetch_array($rr);
		$prodinya = $ww['Pilihan1'];
		$prodinya = (empty($prodinya) or ($prodinya == ''))?
						((empty($ww['Pilihan2']))? 'TI' : $ww['Pilihan2']) 
					 :  ((empty($ww['Pilihan2']))? $prodinya : $prodinya.','.$ww['Pilihan2']);
		$namafield = "<tr bgcolor=$grey>
				<td class=inp>No. Aplikan: </td>
				<td class=ul1><input type=hidden name='AplikanID' value='$ww[AplikanID]'>$ww[AplikanID]</td>
			</tr>
			<tr bgcolor=$grey>
				<td class=inp>Nama:</td>
				<td class=ul1><input type=hidden name='Nama' value='$ww[Nama]'>$ww[Nama]</td>
			</tr>
			<tr bgcolor=$grey>
				<td class=inp>Alamat:</td>
				<td class=ul1><input type=hidden name='Alamat' value='$ww[Alamat]'>$ww[Alamat], $ww[Kota] $ww[KodePos]</td>
			</tr>
			<tr bgcolor=$grey>
				<td class=inp>Tgl. Lahir: </td>
				<td class=ul1><input type=hidden name='TanggalLahir' value='$ww[TanggalLahir]' >
							  <input type=hidden name='ProdiID' value='$prodinya' >
							  $ww[TanggalLahir]</td>
			</tr>
			";
	}
	else if(!empty($tnpmb) and $tnpmb!='' and $tnpmb!=0)
	{	
		$grey = "D3D3D3";
		$ss = "select Nama, Alamat, Kota, KodePos, TanggalLahir  from `pmb` where PMBID='$tnpmb'";
		$rr = _query($ss);
		$ww = _fetch_array($rr);
		$prodinya = $ww['Pilihan1'];
		$prodinya = (!empty($prodinya))?
						((!empty($ww['Pilihan2']))? $prodinya.','.$ww['Pilihan2'] : $prodinya) : 'TI' ;
		$namafield = "<tr bgcolor=$grey>
				<td class=inp>No. PMB Lama: </td>
				<td class=ul1><input type=hidden name='PMBID' value='$tnpmb'>$tnpmb</td>
			</tr>
			<tr bgcolor=$grey>
				<td class=inp>Nama:</td>
				<td class=ul1><input type=hidden name='Nama' value='$ww[Nama]'>$ww[Nama]</td>
			</tr>
			<tr bgcolor=$grey>
				<td class=inp>Alamat:</td>
				<td class=ul1><input type=hidden name='Alamat' value='$ww[Alamat]'>$ww[Alamat], $ww[Kota] $ww[KodePos]</td>
			</tr>
			<tr bgcolor=$grey>
				<td class=inp>Tgl. Lahir: </td>
				<td class=ul1><input type=hidden name='TanggalLahir' value='$ww[TanggalLahir]' >
							  <input type=hidden name='ProdiID' value='$prodinya' >
							  $ww[TanggalLahir]</td>
			</tr>
			";
	}
	else
	{	$namafield = "<tr>
				<td class=inp>No. Aplikan: </td>
				<td class ul1>( Lakukan 'Cari Nama' dahulu )</td>
				</tr>";
	}
  
  CheckFormScript("NomorAplikan");
  
  $arrSyarat = array(); // Syarat yang tidak memiliki checkbox untk dicek
  $arrSyarat2 = array(); // Syarat yang memiliki checkbox untuk dicek
  if($frm['USM'] == 'Y') $arrSyarat[] = "USM";
  if($frm['Wawancara'] == 'Y') $arrSyarat[] = "Wawancara";
  if($frm['Prasyarat'] == 'Y')
  {	$arrPrasyarat = explode('|', $frm['PrasyaratExtra']);
	$n = 0; $n1 = 0;
	foreach($arrPrasyarat as $persyarat)
	{	$n++;
		$arr = explode('~', $persyarat);
		// $arr[0] adalah PMBFormSyaratID, $arr[1] adalah 'Y' atau 'N' digunakan, $arr[2] adalah Tambahan input untuk prasyarat
		
		$pmbformsyarat = GetFields('pmbformsyarat', "PMBFormSyaratID='$arr[0]' and KodeID", KodeID, "*");
		if($arr[1] == 'Y')
		{   if($pmbformsyarat['AdaScript']=='Y')
			{
				$FormSyarat = "$pmbformsyarat[Nama]";
				$pos = strpos($pmbformsyarat['Script'], '=INPUT=');
				if($pos > 0) 
				{	$FormSyarat .= ": $arr[2]";
				}
				$arrSyarat[] = $FormSyarat;
			}
			else
			{	$n1++;
				$arrSyarat2[] = "<input type=checkbox name='CekSyarat[]' value='$pmbformsyarat[PMBFormSyaratID]'> $pmbformsyarat[Nama]";
			}
		}
	}
  }
  if(!empty($arrSyarat)) $Syarat = '&bull; '.implode('<br>&bull; ', $arrSyarat);
  if(!empty($arrSyarat2)) $Syarat2 = implode('<br>', $arrSyarat2);
  
  // Tampilkan formulir
  echo "<table class=bsc cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].jual.php' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='gel' value='$gel' />
  <input type=hidden id='id' name='id' value='$id' />
  <input type=hidden id='gel' name='gel' value='$gel' />
  <input type=hidden id='tn' name='tn' value='$tn' />
  <input type=hidden id='NomorAplikan' name='NomorAplikan' value='$tn' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><td class=inp nowrap>Jenis Formulir:</td>
      <td class=ul1><b>$frm[Nama]</b></td>
      </tr>
  <tr><td class=inp nowrap>Jumlah Pilihan:</td>
      <td class=ul1>$frm[JumlahPilihan]</td>
      </tr>
  <tr><td class=inp>Harga:</td>
      <td class=ul1>$hrg</td>
      </tr>
  <tr><td class=inp>Nama Pembeli:</td>
      <td class=ul1>
        <input id='Namanya' type=text name='Nama' size=20 maxlength=50 />
		<input type=button name='SearchName' value='Cari Nama' 
		onClick=\"CariNama()\" />
      </td></tr>
	$namafield
  <tr><td class=inp>Syarat:</td>
	  <td class=ul1>$Syarat
				<br>$Syarat2</td>
	  </tr>
  <tr><td class=inp nowrap>Bukti Setoran:</td>
      <td class=ul1><input type=text name='BuktiSetoran'
        size=20 maxlength=50 /> <br />(Kosongkan jika tunai)</td>
      </tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul1>
      <textarea name='Keterangan' cols=30 rows=3></textarea>
      </td></tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td>
      </tr>
  </form>
  </table>
  
  <script>
	function CariNama()
	{	
		temp = document.getElementById('Namanya').value;
		
		if(temp!='')
		{
			id = document.getElementById('id').value;
			gel = document.getElementById('gel').value;
			lnk = '../$_SESSION[mnux].cari.php?gel='+gel+'&id='+id+'&n='+temp;
			//alert(lnk);
			win2 = window.open(lnk, '', 'width=1000, height=600, scrollbars, status, resizable');
			if (win2.opener == null) childWindow.opener = self;
		}
		else
		{	alert('Masukkan nama pembeli terlebih dahulu');
		}
	}
  </script>
  
  ";
}

function Simpan($gel, $id, $tn) {
  include_once "statusaplikan.lib.php";
  
  $_PMBDigit = 4;
  $frm = GetFields('pmbformulir', 'PMBFormulirID', $id, '*');
  $BuktiSetoran = sqling($_REQUEST['BuktiSetoran']);
  $AplikanID = $_REQUEST['AplikanID'];
  $ProdiID = $_REQUEST['ProdiID'];
  $Nama = sqling($_REQUEST['Nama']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $CekSyarat = $_REQUEST['CekSyarat'];
 
  // Cek prasyarat pembelian formulir
  $MsgList = array();
  if($frm['Prasyarat'] == 'Y')
  {	
	$arrPrasyarat = explode('|', $frm['PrasyaratExtra']);
	$n = 0;
	foreach($arrPrasyarat as $persyarat)
	{	$n++;
		$arr = explode('~', $persyarat);
		// $arr[0] adalah PMBFormSyaratID, $arr[1] adalah 'Y' atau 'N' digunakan, $arr[2] adalah Tambahan input untuk prasyarat
		
		$pmbformsyarat = GetFields('pmbformsyarat', "PMBFormSyaratID='$arr[0]' and KodeID", KodeID, "*");
		if($arr[1] == 'Y')
		{   if($pmbformsyarat['AdaScript'] == 'Y')
			{
				$pos = strpos($pmbformsyarat['Script'], '=INPUT=');
				if($pos > 0) 
				{	$_Script = str_replace('=INPUT=', "'$arr[2]'", $pmbformsyarat['Script']);
					$cari = GetaField('aplikan', "AplikanID='$AplikanID' and $_Script and KodeID", KodeID, "AplikanID");
					if(empty($cari)) $MsgList[] = "Syarat $pmbformsyarat[Nama] tidak terpenuhi.";
				}
			}
			else
			{	if(!empty($CekSyarat))
				{	if(!in_array($pmbformsyarat['PMBFormSyaratID'], $CekSyarat))
					{	$MsgList[] = "Syarat $pmbformsyarat[Nama] tidak terpenuhi.";
					}
				}
				else $MsgList[] = "Syarat $pmbformsyarat[Nama] tidak terpenuhi.";
			}
		}
	}	
  }
  if(!empty($MsgList))
  {	  echo "<table class=box cellspacing=1 width=100%>";
	  echo "<tr><td class=inpx>Terdapat syarat-syarat pembelian formulir yang tidak terpenuhi:</td></tr>";
	  foreach($MsgList as $msg)
	  {	 echo "<tr><td>&bull; <b>$msg</b></td></tr>";
	  }
	  echo "<tr><td class=ul1 align=center><input type=button name='Tutup' value='Tutup' onClick=\"window.close()\"></td></tr>
	  </table>";
  }
  
  else
  {	// Buat nomer baru
	  $nomer = str_pad('', $_PMBDigit, '_', STR_PAD_LEFT);
	  $nomer = $gel . $nomer;
	  $akhir = GetaField('pmbformjual',
		"PMBFormJualID like '$nomer' and KodeID", KodeID, "max(PMBFormJualID)");
	  $nmr = str_replace($gel, '', $akhir);
	  $nmr++;
	  $baru = str_pad($nmr, $_PMBDigit, '0', STR_PAD_LEFT);
	  $baru = $gel.$baru;
	
	  // Simpan
	  $s = "insert into pmbformjual
		(PMBFormJualID, PMBFormulirID, KodeID, AplikanID,
		Tanggal, PMBPeriodID, BuktiSetoran, ProdiID,
		Nama, LoginBuat, TanggalBuat,
		Keterangan, Jumlah)
		values
		('$baru', '$id', '".KodeID."', '$AplikanID', 
		now(), '$gel', '$BuktiSetoran', '$ProdiID', 
		upper('$Nama'), '$_SESSION[_Login]', now(),
		'$Keterangan', '$frm[Harga]')";
	  $r = _query($s);
	  $s1 = "update aplikan set PMBFormJualID='$baru', PMBFormulirID='$id', VA_Bayar='Y'
				where AplikanID = '$AplikanID' ";
	  $r1 = _query($s1);
	  
	  // Set Status Peminat menjadi BLI
	  SetStatusAplikan('BLI', $AplikanID, $gel);
	  
	  echo "<script>opener.location='../index.php?mnux=$_SESSION[mnux]';</script>";
	  
	  // Tampilkan pesan
	  $hrg = number_format($frm['Harga']);
	  TutupScript();
	
	  // Tampilkan kwitansi
	  TampilkanJudul("Cetak Kwitansi");
	  
	  echo "<table class=box cellspacing=1 align=center width=90%>
	  <tr><td class=inp>No. Formulir:</td>
		  <td class=ul1><font size=+1>$baru</font></td>
		  </tr>
	  <tr><td class=inp>Gelombang:</td>
		  <td class=ul1>$gel</td>
		  </tr>
	  <tr><td class=inp>Formulir:</td>
		  <td class=ul1>$frm[Nama]</td>
		  </tr>
	  <tr><td class=inp>Jumlah Pilihan:</td>
		  <td class=ul1>$frm[JumlahPilihan]</td>
		  </tr>
	  <tr><td class=inp>Harga:</td>
		  <td class=ul1>$hrg</td>
		  </tr>
	  <tr><td class=inp>Nama:</td>
		  <td class=ul1><font size=+1>$Nama</font></td>
		  </tr>
	  <tr><td class=ul1 colspan=2 align=center>
		  <input type=button name='Tutup' value='Tutup' onClick=\"javascript:ttutup('$_SESSION[mnux]')\" />
		  <input type=button name='CetakKwitansi' value='Cetak Kwitansi'
			onClick=\"location='../$_SESSION[mnux].kwitansi.php?id=$baru' \" />
		  </td>
		  </tr>
	  </table>";
   }
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}

?>
