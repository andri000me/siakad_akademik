<?php session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
      $ID = sqling($_REQUEST['AssetID']);
      $w = GetFields('asset', 'AssetID', $ID, '*');
	    $AssetID = "$w[InventarisID]";
      $lks = GetOption2('lokasiasset', "concat(LokasiID, ' - ', Nama)", 'LokasiID', $w['LokasiID'], '', 	 'LokasiID');
      $klp = GetOption2('kelompokasset', "concat(KelompokID, ' - ', Nama)", 'KelompokID', $w['KelompokID'], '', 'KelompokID');
      $vdr = GetOption2('vendor', "concat(VendorID, ' - ', Nama)", 'VendorID', $w['VendorID'], '', 'VendorID');
 	    $jdl="<h1>Data Asset <small>#$ID</small></h1>";
    $tglbeli=TanggalFormat($w['TglBeli']);
    $tglsusut=TanggalFormat($w['TglSusut']);

  echo "<html>
  <head><title>Cetak Asset</title>
  <link href='../themes/ubh/css/arisal.css' rel='stylesheet'>
  </head>
  <body>
  <p style='margin:0 auto'><table class=box cellspacing=1 cellpadding=4 border=1>
  <tr><th class=ttl colspan=4>$jdl</th></tr>
  <tr><td class=inp>Inventaris ID</td>
  <td class=ul colspan=4>$AssetID</td></tr>
  <tr><td class=inp>Nama</td>
  <td class=ul colspan=4>$w[Nama]</td></tr>
  <tr><td class=inp>Tanggal Perolehan</td>
	  <td class=ul colspan=4>$tglbeli</td></tr>
  <tr><td class=inp>Tangal Disusutkan</td>
	  <td class=ul colspan=4>$tglsusut</td></tr>
  <tr><td class=inp>Q t y</td>
	  <td class=ul colspan=4>$w[Jumlah]</td></tr>
  <tr><td class=inp>Satuan</td>
	  <td class=ul colspan=4>$w[Satuan]</td></tr>
  <tr><td class=inp>Harga Beli Satuan</td>
	  <td class=ul>".number_format($w['HargaBeli'])."</td>
    <td class=inp>Harga Total</td>
    <td class=ul>".number_format($w['HargaBeli']*$w['Jumlah'])."</td></tr> 
  <tr><td class=inp>Lokasi </td>
	  <td class=ul colspan=4><select name='LokasiID'>$lks</select></td></tr>
  <tr><td class=inp>Manfaat Komersil</td>
	  <td class=ul>$w[ManfaatKomersil] Tahun</td>
  <td class=inp>Manfaat Fiskal</td>
	  <td class=ul>$w[ManfaatFiskal] Tahun</td></tr>
  <tr><td class=inp>Prosentase Komersil</td>
	  <td class=ul>$w[ProsentaseKomersil] %</td>
      <td class=inp>Prosentase Fiskal</td>
	  <td class=ul>$w[ProsentaseFiskal] %</td> </tr>
  <tr><td class=inp>Kondisi</td>
	  <td class=ul colspan=4>$w[Kondisi]</td></tr>
  <tr><td class=inp>Pemakai</td>
	  <td class=ul colspan=4>$w[Pemakai]</td></tr>
  <tr><td class=inp>No. Purchase Order(PO)</td>
	  <td class=ul colspan=4>$w[PurchaseOrder]</td></tr>
  <tr><td class=inp>Vendor</td>
	  <td class=ul colspan=4><select name='VendorID' disabled>$vdr</select></td></tr>
  </table></p>

  </body></html>";
