<?php error_reporting(0);

$JenisTagihan = sqling($_POST['JenisTagihan']);
$ID = sqling($_POST['ID']);
$Nama = sqling($_POST['Nama']);
$Jumlah = sqling($_POST['Jumlah']);
$BIPOTNamaID = sqling($_POST['JenisTagihan']);

if (isset($_POST['JenisTagihan']) && !empty($JenisTagihan) && !empty($ID) && !empty($Nama) && !empty($Jumlah)){
  ProsesBIPOT($ID,$BIPOTNamaID,$Jumlah,$Nama); //parameter blm dibuat
}
TampilkanJudul('Tagihan Non-Portal');
$gos = (empty($_REQUEST['gos']))? 'TampilkanPenagihan' : $_REQUEST['gos'];
$gos();

function TampilkanPenagihan(){ 
$TahunID = GetaField('tahun',"NA='N' AND TahunID not like 'Tran%' AND KodeID",KodeID,"max(TahunID)");
?>
<h4>Perhatian! Tagihan yang sudah dibuat tidak bisa dibatalkan. Jadi, anda diharuskan untuk teliti memilih tagihan yang akan dibayarkan.</h4>
<hr />
<form class="form-horizontal box" action="?" method="post" onsubmit="return konfirmasi()" id="frmTagih">
						  <fieldset>
                            <div class="control-group">
							  <label class="control-label inp" for="JenisTagihan">ID:</label>
							  <div class="controls">
								<input type="text" name="ID" /> *) Boleh KTP/SIM/KTM
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label inp" for="JenisTagihan">Nama:</label>
							  <div class="controls">
								<input type="text" name="Nama" />
							  </div>
							</div> 
              <div class="control-group">
							  <label class="control-label inp" for="JenisTagihan">Jenis tagihan yang ingin dibayar: </label>
							  <div class="controls">
								<select name="JenisTagihan" class="nones" placeholder="Pilih Tagihan" id="Pilihan">
                  					<option value=""></option>
                                	<option value="55">Uang Legalisir</option>
                                    <option value="21">Uang Test Toefl</option>
                                    <option value="54">Uang Kursus Toefl</option>
                                    <option value="56">Uang Pengganti Kertas Ijazah</option>
                                    <option value="57">Uang Pengganti Kertas Transkrip</option>
                                </select>
							  </div>
                </div>
                <div class="control-group">
                <label class="control-label inp" for="JenisTagihan">Jumlah Tagihan:</label>
                <div class="controls">
                <input type="text" name="Jumlah" />
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
	if(window.confirm('Anda yakin ingin membuat tagihan ini?')==false){return false}}
}</script>

<?php 
} 

function ProsesBIPOT($ID,$BIPOTNamaID,$Jumlah,$Nama){
	// GANTI LAGI SETELAH WISUDA 65
  $TahunID = GetaField('tahun',"NA='N' AND TahunID not like 'Tran%' AND KodeID",KodeID,"max(TahunID)");
  //$TahunID = ($JenisTagihan=='WS' ? "20161" :$TahunID);

  $CEK = GetaField('bipotnonportal', "ID='$ID' and NA='N' and BIPOTNamaID='$BIPOTNamaID' and NamaMhs",$Nama, "Nama");
  if (!empty($CEK)){ TampilkanTagihanNonPortal($ID,$BIPOTNamaID,$Jumlah,$Nama);die("Maaf, tagihan ini sudah ada dalam akun anda!"); }
			$BN = GetaField('bipotnama', "BIPOTNamaID",$BIPOTNamaID, "Nama");
                 $s2 = "insert into bipotnonportal
                    (KodeID, COAID, PMBMhswID, ID, TahunID,
                    BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
                    Jumlah, Besar, Dibayar,
                    Catatan, NA,
                    LoginBuat, TanggalBuat, Prodi, Fakultas, NamaMhs, Sesi)
                    values
                    ('".KodeID."', '', 1, '$ID', '$TahunID',
                    '', '$BIPOTNamaID', '', '$BN', '1', 
                    '1', '$Jumlah', 0,
                    'Auto', 'N',
                    '$_SESSION[_Login]', now(), '', '', '$Nama', '1')";
                  $r2 = _query($s2);
  BuatTagihanBankNonPortal($ID,$BIPOTNamaID,$Jumlah,$Nama,$TahunID);
  TampilkanTagihanNonPortal($ID,$BIPOTNamaID,$Jumlah,$Nama);
}
function TampilkanTagihanNonPortal($ID,$BIPOTNamaID,$Jumlah,$Nama){
  $s = "SELECT * from bipotnonportal2 where ID='$ID' and flag in (0,2) group by VirtualAccount";
  $r = _query($s);
  $num = _num_rows($r);
  
  if ($num>0){
    echo "
    <h1 align=center>Tagihan Non Portal</h1>
    <table width=700 style='margin:0 auto' align=center border=0>
    <tr><td>Identitas : $ID</td></tr>
    <tr><td>Nama : $Nama</td></tr>
    </table>
    <table class='box' width=700 style='margin:0 auto' align=center border=1>
    <tr><th class='ttl'>No.</th><th class='ttl'>Untuk Pembayaran</th><th class='ttl'>Kirim ke Rekening<br>(Virtual Account)</th><th class='ttl'>Jumlah yang harus ditransfer</th></tr>";
    $n=0;
    while ($w = _fetch_array($r)) {
      $n++;
      echo "<tr><td class=inp>$n.</td><td>$w[NamaBIPOT]</td><td><h3>99 0003 $w[VirtualAccount]</h3><br>Atas Nama: $w[NamaMhsw]</td><td align=right>Rp ".number_format($w['Besar'],0,",",".")."</td></tr>";
    }
    echo "</table><br>
    <h4 align=center>Harap dicetak halaman ini, karena informasi di atas dibutuhkan pada saat pembayaran di ATM atau dihadapan Teller Bank.</h4>
    ";
    echo "<p align=center><a class='btn btn-small' href='//portal.bunghatta.ac.id/'>Kembali</a></p>";
    die();
  }
}
function BuatTagihanBankNonPortal($ID,$BIPOTNamaID,$Jumlah,$Nama,$TahunID){
  $s = "SELECT b.BIPOTNamaID,bn.KodeTagihan,bn.KodeVirtualAccount,b.Nama,sum(b.Jumlah*b.Besar*b.TrxID) as Besar,sum(Dibayar) as Dibayar FROM `bipotnonportal` b left outer join bipotnama bn on bn.BIPOTNamaID=b.BIPOTNamaID where b.ID='$ID' and b.NA='N' and b.BIPOTNamaID='$BIPOTNamaID' and b.NamaMhs='$Nama'
       group by b.Nama";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $Ditagih = $w['Besar'] - $w['Dibayar'];
    if ($Ditagih > 0 || $Ditagih < 0) {

      // Kode Virtual Account + NPM
      $VirtualAccount = GetNextVANonPortal();
      $s1 = _query("DELETE FROM bipotnonportal2 where BIPOTNamaID='$w[BIPOTNamaID]' and ID='$ID' and NamaMhsw='$Nama' and flag in (0,2)");
      $s1 = _query("INSERT INTO bipotnonportal2 (VirtualAccount,ID, Prodi, Fakultas,KodeProdi,KodeFakultas, NamaMhsw, Sesi, TahunID, KodeForm, KodeTagihan, Besar, flag, BIPOTNamaID, NamaBIPOT) 
          values
          ('$VirtualAccount', '$ID', '', '','','', '$Nama', '1', '$TahunID', '0001', '0022', '$Ditagih', 0, '$w[BIPOTNamaID]', '$w[Nama]')");
          
    }
  } 
}
function GetNextVANonPortal() {
  // Ambil Setup NIM
  $check = '791'.date('y').'17';
  // check dulu
  $s = "select max(VirtualAccount) as LAST from bipotnonportal2 where VirtualAccount like '$check%' ";
  $r = _query($s);
  $w = _fetch_array($r);
  //die($w['LAST']);
  
  if (empty($w['LAST'])) {
    $Last = $check.'000001';
  }
  else {
    $Last = $w['LAST']+1;
  }
  return $Last;
}
?>