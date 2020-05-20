<?php error_reporting(0);
$MhswID = ($_SESSION['_LevelID']=='120')? $_SESSION['_Login']: GetSetVar('MhswID');
$JenisPengajuan = GetSetVar('JenisPengajuan');

if (isset($_POST['JenisPengajuan']) && !empty($JenisPengajuan)){ProsesPengajuan($MhswID,$JenisPengajuan);}
TampilkanJudul('Permintaan Perubahan Status Mahasiswa');
$gos = (empty($_REQUEST['gos']))? 'Tampilkan' : $_REQUEST['gos'];
$gos($MhswID);

function Tampilkan($MhswID){ 
$TahunID = GetaField('tahun',"NA='N' AND TahunID not like 'Tran%' AND KodeID",KodeID,"max(TahunID)");
?>
<div class="well"><h6>Kegunaan:</h6><ul class="muted">
<li>Permintaan Perubahan Status Mahasiswa adalah fasilitas dari Universitas bagi mahasiswa yang ingin Berhenti Studi Sementara (BSS) atau Pindah Kuliah.</li>
</ul>
</div>
<h4>Petunjuk Pengajuan</h4>
<ol>
    <li>Mahasiswa mengisi formulir perubahan.</li>
    <li>Mahasiswa menekan tombol "Ajukan Permintaan Perubahan Status".</li>
    <li>Mahasiswa mencetak bukti pengajuan.</li>
    <li>Mahasiswa menunggu bagian SPP menginput tagihan yang harus dibayarkan. Proses ini hanya bisa dilakukan pada hari dan jam kerja.</li>
    <li>Mahasiswa membayar tagihan sesuai yang ditetapkan oleh bagian SPP. Pembayaran dapat dilakukan melalui bank/ATM terdekat ke nomor rekening virtual yang tercantum pada halaman pengajuan.</li>
    <li>Status mahasiswa akan berubah otomatis setelah pembayaran dilakukan.</li>
</ol>
<hr />
<form class="form-horizontal" action="?" method="post" onsubmit="return konfirmasi()" id="frmTagih">
						  <fieldset>
							<?php if($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==60){ ?>
              <div class="control-group">
							  <label class="control-label" for="NPM">NPM </label>
							  <div class="controls">
								<input type="text" name="MhswID" value="<?php echo $_SESSION['MhswID'];?>" />
							  </div>
							</div>
                            <?php } 
    					?>
              <div class="control-group">
							  <label class="control-label" for="JenisTagihan">Jenis Pengajuan: </label>
							  <div class="controls">
								<select name="JenisPengajuan" class="nones" placeholder="Pilih Pengajuan">
                  <option value=""></option>
                  <option value="C">Berhenti Studi Sementara (BSS)</option>
                  <option value="K">Pindah Kuliah</option>
                </select> 
							  </div>
							</div>
                            <div class="form-actions">
								<button type="submit" class="btn btn-primary">Ajukan Permintaan Perubahan Status</button>
							  </div>
                           </fieldset>
</form>
<script>function konfirmasi(){ if(window.confirm('Anda yakin ?')==false){return false}}</script>
<?php 
} 
function ProsesPengajuan($MhswID,$JenisPengajuan){
  $cek = GetaField('mhsw', "MhswID", $MhswID,"StatusMhswID");
  if ($cek != $JenisPengajuan && ($JenisPengajuan=='C' || $JenisPengajuan=='K')){
    $insert = "INSERT INTO prosesstatusmhsw ("
  }
  
}
?>