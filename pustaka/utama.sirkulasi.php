<?php
// *** Parameters ***
$_srcmhswkey = GetSetVar('Keyword');
$_srcmhswval = GetSetVar('Val');
$prodi = GetSetVar('prodi');
if ($_REQUEST['Val'] == 'Reset') {
  $_SESSION['Keyword'] = '';
  $_SESSION['Val'] = '';
  $_SESSION['prodi'] = '';
}
$_SESSION['_TextBuku']='';
$_SESSION['_IDBuku']='';
$_SESSION['_JumlahBuku']='';
$_SESSION['_pustakaAnggotaID']='';

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'Dftar' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function EdtScript() {
  echo <<<SCR
  <script>
  function Edt(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].sirkulasi.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=540, height=360, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function pengembalianBuku(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].pinjaman.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=540, height=360, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function cekAnggota(ID) {
	$.ajax({
        	url: "pustaka/ajx.cekanggota.php?AnggotaID="+ID,
	        type: 'GET',
			
			mimeType:"multipart/form-data",
			contentType: false,
    	    cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					$("#deteksiKey").html(data);
				
		    },
		  	error: function(jqXHR, textStatus, errorThrown) 
	    	{
				alert('Ada kesalahan koneksi!');
	    	} 	        
	   });
  }
    function cekBuku(ID) {
		$('#daftarBibliografi').load('$_SESSION[mnux].ajx.cekbuku.php?item_code='+ID);
  	}
  function deteksiEnterAgt(ID, event){
  	if (event.which == 13 || event.keyCode == 13) {
            cekAnggota(ID);
            return false;
        }
        return true;
  }
  function deteksiEnterBuku(ID, event){
  	if (event.which == 13 || event.keyCode == 13) {
            cekBuku(ID);
            return false;
        }
        return true;
  }
  $('#AnggotaID').focus();
  </script>
SCR;
}
function Dftar() {
  EdtScript();
  
echo "<p><form name='frmAnggota' method='post' action=?>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <div id='deteksiKey'></div>
  <div id='deteksiKey2'></div>
  <table class=box cellspacing=1 align=center width=800>
    <tr>
    <td class=inp colspan=3>
    	ID Anggota:
    </td>
    <td class=ul1 colspan=5>
    <input type='text' id='AnggotaID' name='AnggotaID' size=30 style='margin-bottom:4px' value='' onkeypress=\"deteksiEnterAgt(this.value,event)\" />
        </td>
        </tr>
	<tr>
    <td class=inp colspan=3>
    	Nama:
    </td>
    <td class=ul1 colspan=5>
    <span id='Namax'></span>
        </td>
        </tr>
	<tr>
    <td class=inp colspan=3>
    	Status Keanggotan:
    </td>
    <td class=ul1 colspan=5>
    <span id='Statusx' style='font-weight:bold'></span>
        </td>
        </tr>
     <tr>
     <tr>
    <td class=inp colspan=3>
    	Jumlah buku belum dikembalikan:
    </td>
    <td class=ul1 colspan=5>
    <span id='Pinjamx'></span>
        </td>
        </tr>
     <tr>
    <td colspan=8 align='right'> <b>ID Buku: <b>
    <input type='text' name='BibliografiID2' id='BibliografiID2' size=30 style='margin-bottom:4px' value='' onkeypress=\"deteksiEnterBuku(this.value,event)\" />
    </td>
    </tr>
    </table>
    <div id='daftarBibliografi'></div>";
  
}

?>
