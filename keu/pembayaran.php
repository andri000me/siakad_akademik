<?php error_reporting(0);
$MhswID = ($_SESSION['_LevelID']=='120')? $_SESSION['_Login']: GetSetVar('MhswID');
$JenisTagihan = GetSetVar('JenisTagihan');

if (isset($_POST['JenisTagihan']) && !empty($JenisTagihan)){ProsesBIPOT($MhswID,$JenisTagihan);}
TampilkanJudul('Permintaan Tagihan');
$gos = (empty($_REQUEST['gos']))? 'TampilkanPenagihan' : $_REQUEST['gos'];
$gos($MhswID);

function TampilkanPenagihan($MhswID){ 
$TahunID = GetaField('tahun',"NA='N' AND TahunID not like 'Tran%' AND KodeID",KodeID,"max(TahunID)");
?>
<div class="well"><h6>Kegunaan:</h6><ul class="muted">
<li>Permintaan Tagihan adalah fasilitas dari Universitas bagi mahasiswa yang ingin melakukan pembayaran seperti Uang Wisuda, Skripsi/TA, PL/PLK, KKN-PPM.</li>
<li>Dengan fasilitas ini mahasiswa tidak harus datang ke Kampus untuk meminta dibuatkan tagihan pembayaran ke Bank </li>
<li>Mahasiswa sudah bisa melakukannya secara mandiri lewat akun portal masing-masing.</li>
</ul>
</div>
<h4>Petunjuk Pengajuan Permintaan Tagihan</h4>
<ol>
    <li>Mahasiswa menekan tombol "Ajukan Permintaan Penagihan".</li>
    <li>Mahasiswa memeriksa jumlah tagihan yang tertera.</li>
    <li>Bila jumlah tagihan sudah cocok, mahasiswa bisa langsung mencetak bukti tagihan yang berisi nomor Virtual Account Anda dan Jumlah tagihan yang harus dibayar.</li>
</ol>
<h4>Perhatian! Tagihan yang sudah dibuat tidak bisa dibatalkan. Jadi, anda diharuskan untuk teliti memilih tagihan yang akan dibayarkan.</h4>
<hr />
<form class="form-horizontal" action="?" method="post" onsubmit="return konfirmasi()" id="frmTagih">
						  <fieldset>
							<?php if($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==60){ ?>
                            <div class="control-group">
							  <label class="control-label" for="JenisTagihan">NPM </label>
							  <div class="controls">
								<input type="text" name="MhswID" value="<?php echo $_SESSION['MhswID'];?>" />
							  </div>
							</div>
                            <?php } 
      $st_awal = GetaField('mhsw', "MhswID", $MhswID, "StatusAwalID");
      $ProdiID = GetaField('mhsw', "MhswID", $MhswID, "ProdiID");
      $c_kkn = GetaField('kkn',"MhswID",$MhswID,"MhswID");
      $c_wisuda = GetaField('wisudawan',"Predikat != '' and MhswID",$MhswID,"PrasyaratLengkap");
      $c_UK  = ($st_awal=='M' ? 1 : GetaField('khs',"TahunID = '$TahunID' and MhswID",$MhswID,"Bayar")) + 0;
      $optKKN = (!empty($c_kkn)  ? "<option value='KKN'>Uang KKN-PPM ".date('Y')."</option>" : "");
      //$optPS = ($c_UK > 0 && $ProdiID=='IH' ? "<option value='PS'>Uang Peradilan Semu ".date('Y')."</option>" : "");
      $optPS = (($_SESSION['_LevelID']=='1' || $_SESSION['_LevelID']=='60') && $ProdiID=='IH' ? "<option value='PS'>Uang Peradilan Semu ".date('Y')."</option>" : "");
							/*
							<select name="JenisTagihan" class="nones">
                                	<option value="TA">Uang Skripsi/Tugas Akhir/Makalah</option>
                                    <option value="PL">Uang PL/PLK</option>
                                    <option value="KKN">Uang KKN-PPM</option>
                                    <option value="PS">Uang Peradilan Semu</option>
                                    <option value="WS">Uang Wisuda</option>
                            </select>  */
							?>
                            <div class="control-group">
							  <label class="control-label" for="JenisTagihan">Jenis tagihan yang ingin dibayar: </label>
							  <div class="controls">
								<select name="JenisTagihan" class="nones" placeholder="Pilih Tagihan" id="Pilihan">
                  <option value=""></option>
                  <?php echo $optKKN;
                  echo $optPS;?>
                  <?php echo (($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==60) && $c_wisuda=='Y' && !empty($MhswID) ? "<option value='WS'>Uang Wisuda</option>":"");?>
                                </select>
							  </div>
							</div>
                            <div class="form-actions">
								<button type="submit" class="btn btn-primary">Ajukan Permintaan Penagihan</button>
							  </div>
                           </fieldset>
</form>
<script>function konfirmasi(){ 
	var pilihan = $("#Pilihan").val();
	if (pilihan){
	if(window.confirm('Anda yakin ingin membayar tagihan ini?')==false){return false}}
}</script>

<?php 
TampilkanTagihanMhsw($MhswID);
} 

function ProsesBIPOT($MhswID,$JenisTagihan){
	// GANTI LAGI SETELAH WISUDA 65
  $TahunID = GetaField('tahun',"NA='N' AND TahunID not like 'Tran%' AND KodeID",KodeID,"max(TahunID)");
  $TahunID = ($JenisTagihan=='WS' ? "20161" :$TahunID);

  if ($_SESSION['_LevelID']=='120' && ($JenisTagihan != 'PS' && $JenisTagihan != 'KKN')){ die('Anda tidak berhak meminta tagihan ini.');}

   $BIPOTNamaID = GetaField('bipotnama',"Singkatan",$JenisTagihan,"BIPOTNamaID");
  $CEK = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and BIPOTNamaID='$BIPOTNamaID' and TahunID",$TahunID, "MhswID");
  if (!empty($CEK)){ TampilkanTagihanMhsw($MhswID);die("Maaf, tagihan ini sudah ada dalam akun anda!"); }
	$m = GetFields("mhsw", "MhswID", $MhswID, "ProdiID,ProgramID,Nama,Blokir");
	$Prodi = GetFields("prodi","ProdiID",$m['ProdiID'],"Nama,FakultasID");
	$Fakultas = GetaField("fakultas","FakultasID",$Prodi['FakultasID'],"Nama");
	$Semester = GetFields("khs", "TahunID='$TahunID' and MhswID", $MhswID, "Sesi");
	$s1 = "select b2.*,b.Nama as _Nama 
	   from bipot2 b2 
	   left outer join bipotnama b on b2.BIPOTNamaID=b.BIPOTNamaID
	   left outer join bipot bp on bp.BIPOTID=b2.BIPOTID
		where b.Singkatan='$JenisTagihan'
	      and b2.Otomatis = 'N'
		  and b2.NA = 'N'
		  and bp.ProdiID = '$m[ProdiID]'
		  and bp.ProgramID = '$m[ProgramID]'
		order by b2.TrxID, b2.Prioritas limit 1";
	  $r1 = _query($s1);
	  while ($w1 = _fetch_array($r1)) 
	  {	
		$MsgList[] = '-----------------------------------------------------------------';
		$MsgList[] = "Memproses $w1[BIPOT2ID], Rp. $w1[Jumlah]";
	    
		$oke = true;
	
		// Simpan data
		if ($oke) {
			$ada = GetaField('bipotmhsw',
				"KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
				and NA = 'N'
				and TahunID='$TahunID'
				and BIPOTNamaID = '$w1[BIPOTNamaID]'
				and BIPOT2ID",
				$w1['BIPOT2ID'], "BIPOTMhswID") +0;
			
			if ($ada == 0 && $m['Blokir']=='N') {
			  // Simpan
			  $Nama = $w1['_Nama'];
              $Jumlah = 1;

			  $Besar = $w1['Jumlah'];
              // jika bipot untuk mk praktek pakai query ini
                 $s2 = "insert into bipotmhsw
                    (KodeID, COAID, PMBMhswID, MhswID, TahunID,
                    BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
                    Jumlah, Besar, Dibayar,
                    Catatan, NA,
                    LoginBuat, TanggalBuat, Prodi, Fakultas, NamaMhs, Sesi)
                    values
                    ('".KodeID."', '$w1[COAID]', 1, '$MhswID', '$TahunID',
                    '$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', '', '$Nama', '$w1[TrxID]', 
                    '$Jumlah', '$Besar', 0,
                    'Auto', 'N',
                    '$_SESSION[_Login]', now(), '$Prodi[Nama]', '$Fakultas', '$m[Nama]', '$Semester')";
                  $r2 = _query($s2);
              HitungUlangBIPOTMhsw($MhswID, $TahunID);
              BuatTagihanBank($MhswID,$TahunID,$Semester);
		    }
	     }
	  }
  $_SESSION['JenisTagihan']='';
}

function HitungUlangBIPOTMhsw($MhswID, $TahunID){
  // Hitung Total BIPOT & Pembayaran
  $biaya = GetaField("bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID = b2.BIPOT2ID",
      "bm.PMBMhswID = 1 and bm.KodeID = '".KodeID."'
      and bm.NA = 'N'
      and bm.TrxID = 1
      and bm.TahunID = '$TahunID' and bm.MhswID", $MhswID,
      "sum(bm.Jumlah * bm.Besar)")+0;
	if (!empty($biaya)) { $up = _query("update khs set KonfirmasiAktif='Y' where KodeID = '".KodeID."'
      and MhswID = '$MhswID' 
      and TahunID = '$TahunID'
    ");
	}
  $potongan = GetaField("bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID = b2.BIPOT2ID",
      "bm.PMBMhswID = 1 and bm.KodeID = '".KodeID."'
      and bm.NA = 'N'
      and bm.TrxID = -1
      and bm.TahunID = '$TahunID' and bm.MhswID", $MhswID,
      "sum(bm.Jumlah * bm.Besar)")+0;
  $bayar = GetaField('bayarmhsw',
      "PMBMhswID = 1 and KodeID = '".KodeID."'
      and NA = 'N'
      and TrxID = 1
      and TahunID = '$TahunID' and MhswID", $MhswID,
      "sum(Jumlah)")+0;
  $tarik = GetaField('bayarmhsw',
      "PMBMhswID = 1 and KodeID = '".KodeID."'
      and NA = 'N'
      and TrxID = -1
      and TahunID = '$TahunID' and MhswID", $MhswID,
      "sum(Jumlah)")+0;
  // Update data PMB
  $s = "update khs
    set Biaya = $biaya, Potongan = $potongan,
        Bayar = $bayar, Tarik = $tarik
    where KodeID = '".KodeID."'
      and MhswID = '$MhswID' 
      and TahunID = '$TahunID'
    ";
  $r = _query($s);
  $jml = $biaya - $bayar + $tarik - $potongan;
  return $jml;

}?>