<?php
	//Author: Irvandy Goutama
	// Start: 8 Januari 2009
	// Email: irvandygoutama@gmail.com
	
	// *** Parameters ***
	$md = $_REQUEST['md'];
	$matri_kelompok = GetSetVar('matri_kelompok', '');	
	$prodi = GetSetVar('prodi', '');
	$kelamin_by = GetSetVar('kelamin_by', '');
	$nilai_dari = GetSetVar('nilai_dari', '0');
	$nilai_sampai = GetSetVar('nilai_sampai', '100');
	loadJavaScripts();
		
	// *** Main ***
	$sub = (empty($_REQUEST['sub']))? 'HalamanUtamaMatrikulasi' : $_REQUEST['sub'];
	$sub();
	
	// *** Helper Functions ***
	
	function GetOptionsFromData($sourceArray, $chosen)
	{	
			$optresult = "";
			if($chosen == '' or empty($chosen))	
			{ 	$optresult .= "<option value='' selected></option>"; }
			else { $optresult .= "<option value=''></option>"; }
			for($i=0; $i < count($sourceArray); $i++)
			{	if($chosen == $sourceArray[$i])
				{	$optresult .= "<option value='$sourceArray[$i]' selected>$sourceArray[$i]</option>"; }
				else
				{ 	$optresult .= "<option value='$sourceArray[$i]'>$sourceArray[$i]</option>"; }
			}
			return $optresult;
	}
	
	function loadJavaScripts()
	{	echo "
					<SCRIPT LANGUAGE='JavaScript'>

					function CheckAll(chk)
					{	
						total = (document.getElementById('Jumlah'+chk)).value;
						for (i = 1; i <= total; i++)
						{	
							(document.getElementById(chk+i)).checked = true;
						}
					}
					
					function UnCheckAll(chk)
					{
						total = (document.getElementById('Jumlah'+chk)).value;
						
						for (i = 1; i <= total; i++)
						{
							(document.getElementById(chk+i)).checked = false;
						}
					}
					
					function fnEditKelompok(md, id) {
						lnk = '$_SESSION[mnux].entry.edt.php?md='+md+'&kid='+id;
						//alert(lnk);
						win2 = window.open(lnk, '', 'width=620, height=300, scrollbars, status');
						if (win2.opener == null) childWindow.opener = self;
						}
					function fnTampilKelompok() {
						lnk = '$_SESSION[mnux].entry.list.php'
						win2 = window.open(lnk, '', 'width=1000, height=600, scrollbars, status');
						if(win32.opener == null) childWindow.opener=self;
						}
					function fnOtomatisKelompok() {
						lnk = '$_SESSION[mnux].entry.otomatis.php'
						win2 = window.open(lnk, '', 'width=800, height=600, scrollbars, status');
						if(win32.opener == null) childWindow.opener=self;
						}
					</script>
		";		
	}

	// *** Functions ***
		
	function HalamanUtamaMatrikulasi()
	{	TampilHeaderMatrikulasi();
		
		echo  "<Iframe name='frame1' src='$_SESSION[mnux].frame1.php' align=left width=39.5% height=1500 frameborder=0></Iframe>";

		echo "<Iframe name='frame2' src='$_SESSION[mnux].frame2.php' align=right width=59.5% height=1500 frameborder=0></Iframe>";
	}
	
	
	function TampilHeaderMatrikulasi()
	{	echo "<p><table class=box cellspacing=1 align=center>
				<form name='filter_data' action='?' method=POST>
				<tr>
					<td><input type=button name='TambahKelompok' value='Tambah Kelompok'
						onClick=\"javascript:fnEditKelompok(1, '')\" /></td>
					<td><input type=button name='TampilKelompok' value='Tampilkan List Kelompok'
						onClick=\"javascript:fnTampilKelompok()\" /></td>
					<td><input type=button name='Otomatis' value='Kelompokkan Otomatis'
						onClick=\"javascript:fnOtomatisKelompok()\"></td>
				</tr>
				</table></p>";
	}
	
?>