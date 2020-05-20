<?php 
session_start(); error_reporting(0);

// ********* Parameter
$gos 	= GetSetVar('gosx');
$PengunjungID 	= GetSetVar('PengunjungID');

// Ready
$_gos 	= (empty($gos) ? "Tampilkan":$gos);
$_gos();

// ===== GO
function Tampilkan(){
TampilkanJudul("Running Text");
$w = GetaField('running_text',"Nama !", '',"Nama");
?>
<link href='themes/ubh/css/jquery.cleditor.css' rel='stylesheet'>
<form class="form-horizontal" method="post" action="?">
<input type="hidden" name="gosx" value="SAV">
						  <fieldset>     
							<div class="control-group">
							  <label class="control-label" for="textarea2">Ketik Text</label>
							  <div class="controls">
								<textarea class="cleditor" id="textarea2" rows="3" name="Nama"><?php echo $w?></textarea>
							  </div>
							</div>
							<div class="form-actions">
							  <button type="submit" class="btn btn-primary">Save changes</button>
							  <button type="reset" class="btn">Cancel</button>
							</div>
						  </fieldset>
						</form>  
	<script src="themes/ubh/js/jquery.cleditor.min.js"></script>
	<script>
	//rich text editor
		$('.cleditor').cleditor();
	</script>
<?php } 


	

function SAV(){
	$text = sqling($_POST['Nama']);
	$s = "UPDATE running_text set Nama='$text'";
	$r = _query($s);
	Tampilkan();
}

