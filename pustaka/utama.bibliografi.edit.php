<?php

session_start();
include_once "../sisfokampus2.php";


HeaderSisfoKampus("Edit Bibliografi");

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0; // Jika edit, maka gunakan id ini
$bck = $_REQUEST['bck'];

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $bck);

// *** Functions ***
function Edit($md, $id, $bck) {
  if ($md == 0) {
    $jdl = "Edit Bibliografi";
    $w = GetFields('app_pustaka1.biblio b
					left outer join app_pustaka1.biblio_attachment a on  a.biblio_id=b.biblio_id
					left outer join app_pustaka1.files f on f.file_id=a.file_id', 'b.biblio_id', $id, 'b.*,f.file_desc');
    $ro = "readonly=true disabled=true";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Bibliografi";
    $w = array();
    $ro = '';
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih detail.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

  $optpenerbit = GetOption3('app_pustaka1.mst_publisher', "publisher_name", 
    '', $w['publisher_id'], "", 'publisher_id');
  $optkota = GetOption3('app_pustaka1.mst_place', "place_name", 
    '', $w['publish_place_id'], "", 'place_id');
  $optlokasi = GetOption3('app_pustaka1.mst_location', "location_name", 
    '', $w['location_id'], "", 'location_id');

// Pengarang
$s1 = "SELECT a.*, b.author_id as AI from app_pustaka1.mst_author a left outer join app_pustaka1.biblio_author b on b.author_id=a.author_id and b.biblio_id='$w[biblio_id]'";
	$r1 = _query($s1);
	while ($w1 = _fetch_array($r1)) {
		$optpengarang .= "<option value='$w1[author_id]' ".($w1['AI']==$w1['author_id']? "Selected='selected'":"").">$w1[author_name]</option>";
	}
// Subject
$s1 = "SELECT a.*, b.topic_id as AI from app_pustaka1.mst_topic a left outer join app_pustaka1.biblio_topic b on b.topic_id=a.topic_id where b.biblio_id='$w[biblio_id]'";
  $r1 = _query($s1);
  while ($w1 = _fetch_array($r1)) {
    $optsubject = $w1['topic'];
  }  

// Tipe
$s1 = "SELECT * from app_pustaka1.mst_gmd";
$r1 = _query($s1);
$opttipe = "<option></option>";
while ($w1 = _fetch_array($r1)) { 
	$opttipe .= "<option value='$w1[gmd_id]' ".($w['gmd_id']==$w1['gmd_id']? "Selected='selected'":"").">$w1[gmd_code]-$w1[gmd_name]</option>";
}

// Koleksi
$s1 = "SELECT * from app_pustaka1.item where biblio_id='$w[biblio_id]'";
$r1 = _query($s1);
$koleksi = "<table border=0 id='item_0' style='display:block;max-height: 100px;min-height: 20px;overflow-x: hidden;overflow-y: scroll;width:100%'>";
while ($w1 = _fetch_array($r1)) { 
	$koleksi .= "<tr id='item_$w1[item_id]'><td><input type='button' value='Hapus' onclick=\"HapusItem('$w1[item_id]')\"> <b>$w1[item_code]</b></td></tr>";
}
$koleksi .= "</table>";
	?><script>
    function HapusItem(item_id,biblio_id){
				if (confirm('Yakin menghapus koleksi ini?')) { 
				 $.ajax({
						url: 'ajx.hapuskoleksi.php?item_id='+item_id+'&biblio_id='+biblio_id,
						type: 'GET',
						
						mimeType:"multipart/form-data",
						contentType: false,
						cache: false,
						processData:false,
						success: function(data, textStatus, jqXHR)
						{
								$("#item_"+item_id).remove();
							
						},
						error: function(jqXHR, textStatus, errorThrown) 
						{
							alert('Tidak berhasil menghapus!');
						} 	        
				   });
				}
			}
      function MuatUlang(){
          $("#btn-refresh").html("Harap tunggu...");
          $.ajax({
                type:"GET",
                url:'ajx.loadselect.php?v=<?php echo $w['publisher_id']?>&md=1',
                success:function(data, textStatus, jqXHR) {
                  $("#Penerbit").html(data);
                  $("#Penerbit").trigger("liszt:updated");
                }
            });
          $.ajax({
                type:"GET",
                url:'ajx.loadselect.php?v=<?php echo $w[biblio_id]?>&md=2',
                success:function(data, textStatus, jqXHR) {
                  $("#author_id").html(data);
                  $("#author_id").trigger("liszt:updated");
                }
            });
          $("#btn-refresh").html("Load Ok!");
      }
    </script>
    <?php
	$hapusitemALL =  ($id > 0 ? "<input type='button' value='(x) Hapus Semua Koleksi' onclick=\"HapusItem(0,'$w[biblio_id]')\">":"");
	$format = explode("~", $w['FormatEksemplar']);
	$file = (empty($w['file_att'])? "":"<a class='btn btn-primary btn-small' href='http://lib.bunghatta.ac.id/app/repository/$w[file_att]'>Lihat File</a>");
  echo "
  <form action='../$_SESSION[mnux].bibliografi.edit.php' method=POST  enctype='multipart/form-data'>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='bck' value='$bck' />
  <table class=box cellspacing=1 width=100% >
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Judul:</td>
      <td class=ul1><input type=text name='title' value='$w[title]' size=60 /></td>
      </tr>
  <tr><td class=inp>Judul Seri:</td>
      <td class=ul1>
	  <input type=text name='series_title' value='$w[series_title]' size=60 />
      </td></tr>
  <tr><td class=inp>Informasi Detail:</td>
      <td class=ul1>
      <textarea name='spec_detail_info' cols=30 rows=4>$w[spec_detail_info]</textarea>
      </td></tr>
  <tr><td class=inp>Pernyataan Tanggungjawab:</td>
      <td class=ul1>
	  <input type=text name='sor' value='$w[sor]' size=60 maxlength='200' />
      </td></tr>
  <tr><td class=inp>Pengarang:</td>
      <td class=ul1><select multiple name='author_id[]' id='author_id' place-holder='Pilih Pengarang' class='chosen-select'>$optpengarang</select><br />
      <a class='btn btn-small' href=# onclick=\"javascript:MuatUlang();\" ><i class='icon-refresh'></i> Reload Pengarang & Penerbit</a> 
      <span id='btn-refresh'><sup>*) Sisipkan pengarang yang baru ditambahkan ke halaman ini, tanpa harus memuat ulang seluruh halaman.</sup></span></td>
      </tr>
  <tr><td class=inp>Subjek:</td>
      <td class=ul1><input type=text name='topic_id' value='$optsubject' size=60 />
      </td>
      </tr>
  <tr><td class=inp>Edisi:</td>
      <td class=ul1><input type=text name='edition' value='$w[edition]' size=20 maxlength=20 /></td>
      </tr>
  <tr><td class=inp>GMD:</td>
      <td class=ul1><select name='gmd_id' data-rel='chosen'>$opttipe</select></td>
      </tr>
  <tr><td class=inp>Bentuk Fisik:</td>
      <td class=ul1><input type=text name='collation' value='$w[collation]' size=40 maxlength=60 /></td>
      </tr>
  <tr><td class=inp>Klasifikasi:</td>
      <td class=ul1><input type=text id='classification' name='classification' value='$w[classification]' onkeyup=\"javascript:copytocn(this.value);\" size=40 maxlength=40 /></td>
      </tr>
  <tr><td class=inp>Nomor Panggil:</td>
      <td class=ul1><input type=text id='call_number' name='call_number' value='$w[call_number]' size=20 maxlength=30 /></td>
      </tr>
  <tr><td class=inp>Eksemplar:</td>
      <td class=ul1>Pola <input type=text name='pola' value='B00000' size=6 maxlength=10 /> 
	  Dari <input type=text name='dari' value='0' size=6 maxlength=6 />
	  Ke <input type=text name='ke' value='0' size=6 maxlength=6 /> 
    Lokasi: 
      <select name='location_id'>$optlokasi</select> $hapusitemALL </td>
      </tr>
  <tr><td class=inp>Data Koleksi:</td>
      <td class=ul1>$koleksi</td>
      </tr>
  <tr><td class=inp>ISSN/ISBN:</td>
      <td class=ul1><input type=text name='isbn_issn' value='$w[isbn_issn]' size=20 maxlength=40 /></td>
      </tr>
  <tr><td class=inp>Penerbit:</td>
      <td class=ul1><select name='publisher_id' data-rel='chosen' id='Penerbit'>$optpenerbit</select></td>
      </tr>
  <tr><td class=inp>Kota Penerbit:</td>
      <td class=ul1><select name='publish_place_id'>$optkota</select></td>
      </tr>    
  <tr><td class=inp>Tahun Terbit:</td>
      <td class=ul1><input type=text name='publish_year' value='$w[publish_year]' size=5 maxlength=4 /></td>
      </tr>
  <tr><td class=inp>Bahasa:</td>
      <td class=ul1><input type='radio' name='language_id' value='id' ".($w['language_id']=='id' ? "checked":"")." /> Indonesia <br />
	  				<input type='radio' name='language_id' value='en' ".($w['language_id']=='en' ? "checked":"")." /> English <br />
					<input type='radio' name='language_id' value='jp' ".($w['language_id']=='jp' ? "checked":"")." /> Jepang<br />
					<input type='radio' name='language_id' value='dll' ".($w['language_id']=='dll' ? "checked":"")." /> Lain-lain</td>
      </tr>
  <tr><td class=inp>Gambar Sampul:</td>
      <td class=ul1>".($w['image'] != '' ? "<img src='http://lib.bunghatta.ac.id/app/images/docs/$w[image]'><br />":"")."<input type=file name='GambarSampul' /></td>
      </tr>
  <tr><td class=inp>File:</td>
   <td class=ul1><input type=file name='File' /> &raquo; hanya <b>.pdf</b> $file</td>
      </tr>
  <tr><td class=inp>Deskripsi File:</td>
      <td class=ul1><input type=text name='file_desc' value='$w[file_desc]' size=60 maxlength=100 /></td>
      </tr>
  <tr><td class=inp>Sumber Dana:</td>
      <td class=ul1><input type=text name='sumber_dana' value='$w[sumber_dana]' size=20 maxlength=100 /></td>
      </tr>
  <tr><td class=inp>Sembunyikan:</td>
      <td class=ul1><input type='radio' name='opac_hide' value='1' ".($w['opac_hide']=='1' ? "checked":"")." /> Ya
	  				<input type='radio' name='opac_hide' value='0' ".($w['opac_hide']=='0' ? "checked":"")." /> Tidak</td>
      </tr>
  <tr><td class=inp>Promosikan:</td>
      <td class=ul1><input type='radio' name='promoted' value='1' ".($w['promoted']=='1' ? "checked":"")." /> Ya
	  				<input type='radio' name='promoted' value='0' ".($w['promoted']=='0' ? "checked":"")." /> Tidak</td>
      </tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td></tr>
  </table></form>";
  ?><script>
  function copytocn(dval){
		  $('#call_number').val(dval);
	  }
</script><?php
}
function Simpan($md, $id, $bck) {
  $BibliografiID = $_REQUEST['id']+0;
  $title = sqling($_REQUEST['title']);
  $author_id = $_REQUEST['author_id'];
  $topic_id = $_REQUEST['topic_id'];
  $series_title = sqling($_REQUEST['series_title']);
  $edition = sqling($_REQUEST['edition']);
  $collation = sqling($_REQUEST['collation']);
  $call_number = sqling($_REQUEST['call_number']);
  $classification = sqling($_REQUEST['classification']);
  $KodeEksemplar = sqling($_REQUEST['KodeEksemplar']);
  $isbn_issn = sqling($_REQUEST['isbn_issn']);
  $publisher_id = sqling($_REQUEST['publisher_id']);
  $publish_place_id = sqling($_REQUEST['publish_place_id']);
  $publish_year = sqling($_REQUEST['publish_year']);
  $language_id = sqling($_REQUEST['language_id']);
  $opac_hide = sqling($_REQUEST['opac_hide']);
  $promoted = sqling($_REQUEST['promoted']);
  $file_desc = sqling($_REQUEST['file_desc']);
  $spec_detail_info = sqling($_REQUEST['spec_detail_info']);
  $sumber_dana = sqling($_REQUEST['sumber_dana']);
  $sor = sqling($_REQUEST['sor']);
  $gmd_id = sqling($_REQUEST['gmd_id']);
  $subject = sqling($_REQUEST['subject']);
  $location_id = sqling($_REQUEST['location_id']);
  
  $pola = sqling($_REQUEST['pola']);
  $dari = sqling($_REQUEST['dari']);
  $ke = sqling($_REQUEST['ke']);
  
  $bID = GetaField('app_pustaka1.biblio','biblio_id >', 0, "COUNT(biblio_id)")+1;

  // Proses Gambar Sampul
  if (!empty($_FILES['GambarSampul'])){
	  $upf = $_FILES['GambarSampul']['tmp_name'];
	  $arrNama = explode('.', $_FILES['GambarSampul']['name']);
	  $tipe = $_FILES['GambarSampul']['type'];
	  $arrtipe = explode('/', $tipe);
	  $extensi = $arrtipe[1];
	  $dest = "../../pustaka/app/images/docs/" . $bID . '-' . strtolower(str_replace(" ", "_",$title)). '.' . $extensi;
	  $PicSampul = "../../pustaka/app/images/docs/" . $bID . '2-' . strtolower(str_replace(" ", "_",$title)). '.' . $extensi;
	  $PicSampul2 = $bID . '2-' . strtolower(str_replace(" ", "_",$title)). '.' . $extensi;
	   
	  //echo $dest;
	  if ((move_uploaded_file($upf, $dest)) && ($extensi == 'jpeg')) {
	  
	  //identitas file asli
	  $im_src = imagecreatefromjpeg($dest);
	  $src_width = imageSX($im_src);
	  $src_height = imageSY($im_src);
	  
		//Simpan dalam versi small 110 pixel
	  //Set ukuran gambar hasil perubahan
	  $dst_width = 150;
	  $dst_height = ($dst_width/$src_width)*$src_height;
	
	  //proses perubahan ukuran
	  $im = imagecreatetruecolor($dst_width,$dst_height);
	  imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
	
	  //Simpan gambar
	  imagejpeg($im,$PicSampul);
	  $scUPDATE = "image='$PicSampul2',";
	  $scINSERT1 = "image,";
	  $scINSERT2 = "'$PicSampul2',";
	  unlink($dest);
	  }
  }
  
  
  // Proses File
  if (!empty($_FILES['File'])){
	  $upf = $_FILES['File']['tmp_name'];
	  $arrNama = explode('.', $_FILES['File']['name']);
	  $tipe = $_FILES['File']['type'];
	  $arrtipe = explode('/', $tipe);
	  $extensi = strtolower($arrtipe[1]);
	  $Filex = "../../pustaka/app/repository/" . $bID . '-' . strtolower(str_replace(" ", "_",$title)). '.' . $extensi;
	  $Filex2 = $bID . '-' . strtolower(str_replace(" ", "_",$title)). '.' . $extensi;
	   
	  //echo $dest;
	  if ((move_uploaded_file($upf, $Filex)) && ($extensi == 'pdf')) {
		$scrUPDATE = "file_att='$Filex2',";
	  	$scrINSERT1 = "file_att,";
	  	$scrINSERT2 = "'$Filex2',";
	  }
  }
  // Simpan
  if ($md == 0) {
    $s = "update app_pustaka1.biblio
      set title = '$title',
          series_title  = '$series_title',
		  spec_detail_info  = '$spec_detail_info',
          edition = '$edition',
          collation = '$collation',
		  sumber_dana = '$sumber_dana',
		  sor = '$sor',
		  gmd_id = '$gmd_id',
		  call_number = '$call_number',
		  classification = '$classification',
		  isbn_issn = '$isbn_issn',
		  publisher_id = '$publisher_id',
		  publish_place_id = '$publish_place_id',
		  publish_year = '$publish_year',
		  language_id = '$language_id',
		  opac_hide = '$opac_hide',
		  promoted = '$promoted',
		  $scUPDATE $scrUPDATE
          last_update = now()
      where biblio_id = '$id' ";
    $r = _query($s);
	if (!empty($scrUPDATE)){
		$cekFile = GetaField('app_pustaka1.biblio_attachment',"biblio_id", $id,"file_id");
		if (!empty($cekFile)){
			$s = "UPDATE app_pustaka1.files f,app_pustaka1.biblio_attachment a set f.file_desc='$file_desc' where a.biblio_id='$id' and f.file_id=a.file_id";
			$r = _query($s);
		}else{
			$s = "INSERT into app_pustaka1.files (file_title, file_name, file_url,mime_type,file_desc,input_date) values
					('$title', '$Filex2', 'repository/$Filex2','aplication/pdf','$file_desc',now())";
			$r = _query($s);
			$file_id = mysql_insert_id();
			$s = "INSERT into app_pustaka1.biblio_attachment (biblio_id, file_id, access_type) values
					('$id','$file_id','public')";
			$r = _query($s);
		}
	}
  }
  elseif ($md == 1) {
    $s = "insert into app_pustaka1.biblio
      (title, series_title, sumber_dana, sor, gmd_id, spec_detail_info, edition, collation, call_number, classification, isbn_issn,
      publisher_id, publish_place_id, publish_year, language_id, opac_hide,
      promoted, $scINSERT1 $scrINSERT1 input_date)
      values
      ('$title', '$series_title', '$sumber_dana', '$sor', '$gmd_id', '$spec_detail_info', '$edition', '$collation', '$call_number', '$classification', '$isbn_issn',
      '$publisher_id', '$publish_place_id', '$publish_year', '$language_id', '$opac_hide', '$promoted',
      $scINSERT2 $scrINSERT2 now())";
    $r = _query($s);
	$biblio_id = mysql_insert_id();
	$id = $biblio_id;
  $BibliografiID = $biblio_id;
	if (!empty($scrINSERT1)){
	$s = "insert into app_pustaka1.`files`(file_title,file_name,file_desc,input_date)
			values ('$title', '$Filex2', '$file_desc',now()) ";
	$r = _query($s);	
	$file_id = mysql_insert_id();
	$s = "insert into app_pustaka1.`biblio_attachment`(biblio_id,file_id,access_type)
			values ('$biblio_id','$file_id','public') ";
	$r = _query($s);
	}
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
	
  // Pengarang
  $insAuthor = _query("DELETE from app_pustaka1.biblio_author where biblio_id ='$id'");
  foreach ($author_id as $key => $value) {
    
      $insAuthor = _query("INSERT into app_pustaka1.biblio_author(biblio_id,author_id) value ('$id','$value')");
    
  }
  // Subjek
  if (!empty($topic_id)){
	  $cektopic = GetaField('app_pustaka1.mst_topic',"topic", $topic_id,"topic_id");
		  if (empty($cektopic)){
		      $insTopic = _query("INSERT into app_pustaka1.mst_topic(topic,input_date) value ('$topic_id',now())");
		      $_topic_id = mysql_insert_id();
		      $cektopic = GetaField('app_pustaka1.biblio_topic',"biblio_id", $id,"topic_id");
		  	if (empty($cektopic)){
		      $insTopic = _query("INSERT IGNORE into app_pustaka1.biblio_topic(biblio_id,topic_id) value ('$id','$_topic_id')");
		  	}else{
			  		$insTopic = _query("UPDATE IGNORE app_pustaka1.biblio_topic set topic_id='$cektopic' where biblio_id='$id'");
			  	}
		  }else{
		  	if (!empty($cektopic)){
		       $cektopic2 = GetaField('app_pustaka1.biblio_topic',"biblio_id", $id,"topic_id");
			  	if (empty($cektopic2)){
			      $insTopic = _query("INSERT IGNORE into app_pustaka1.biblio_topic(biblio_id,topic_id) value ('$id','$cektopic')");
			  	}else{
			  		$insTopic = _query("UPDATE IGNORE app_pustaka1.biblio_topic set topic_id='$cektopic2' where biblio_id='$id'");
			  	}
		  	}
		  }
  }
	// item batch insert
    if (trim($pola) != '' && $dari > 0 && $ke > 0) {
      $hasil = array();
      $pattern = trim($pola);
      // get last zero chars
      //preg_match('@0+$@i', $pattern, $hasil);
      //$zeros = strlen($hasil[0]);
      $start = (integer)$dari;
      $end = (integer)$ke;
      for ($b = $start; $b <= $end; $b++) {
      $len = strlen($b);
      if ($zeros > 0) {
        $itemcode = preg_replace('@0{'.$len.'}$@i', $b, '/'.$pattern);
      } else { $itemcode = $b.'/'.$pattern; }
      $s = "INSERT IGNORE INTO app_pustaka1.item (biblio_id, item_code, call_number, coll_type_id,location_id)
        VALUES ($id, '$itemcode', '$call_number', 2, '$location_id')";
      $r = _query($s);
      }
    }
	TutupScript($bck);
  
}
function TutupScript($BCK) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../?mnux=$BCK';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
