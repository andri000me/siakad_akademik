<?php
	//Author: Irvandy Goutama
	// Start: 8 Januari 2009
	// Email: irvandygoutama@gmail.com
	
	// *** Parameters ***
	$md = $_REQUEST['md'];
	$opk_kelas = GetSetVar('opk_kelas', '');	
	$prodi = GetSetVar('prodi', '');
	$program = GetSetVar('program', '');
	$kelamin_by = GetSetVar('kelamin_by', '');
	$nilai_dari = GetSetVar('nilai_dari', '0');
	$nilai_sampai = GetSetVar('nilai_sampai', '100');
	loadJavaScripts();
		
	// *** Main ***
	$sub = (empty($_REQUEST['sub']))? 'HalamanUtamaOPK' : $_REQUEST['sub'];
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
					
					function cetakKelas() {
						lnk = '$_SESSION[mnux].cetak.php?TahunID=$_SESSION[opk_tahun]&KelasID=$_SESSION[opk_kelas]';
						//alert(lnk);
						win2 = window.open(lnk, '', 'width=620, height=300, scrollbars, status');
						if (win2.opener == null) childWindow.opener = self;
					}
					
					function fnEditKelas(md, id) {
						lnk = '$_SESSION[mnux].edt.php?md='+md+'&kid='+id;
						//alert(lnk);
						win2 = window.open(lnk, '', 'width=620, height=300, scrollbars, status');
						if (win2.opener == null) childWindow.opener = self;
						}
					function fnTampilKelas() {
						lnk = '$_SESSION[mnux].list.php'
						win2 = window.open(lnk, '', 'width=1000, height=600, scrollbars, status');
						if(win32.opener == null) childWindow.opener=self;
						}
					function fnOtomatisKelas() {
						lnk = '$_SESSION[mnux].otomatis.php'
						win2 = window.open(lnk, '', 'width=800, height=600, scrollbars, status');
						if(win32.opener == null) childWindow.opener=self;
						}
					</script>
		";		
	}

	// *** Functions ***
		
	function HalamanUtamaOPK()
	{	TampilkanJudul("Pembagian Kelas");
		TampilHeaderOPK();
		
		echo  "<Iframe name='frame1' src='$_SESSION[mnux].frame1.php' align=left width=39.5% height=1600 frameborder=0></Iframe>";

		echo "<Iframe name='frame2' src='$_SESSION[mnux].frame2.php' align=right width=59.5% height=1600 frameborder=0></Iframe>";
	}
	
	
	function TampilHeaderOPK()
	{	echo "<p><table class=box cellspacing=1 align=center>
				<form name='filter_data' action='?' method=POST>
				<tr>
					<td><input type=button name='TambahKelas' value='Tambah Kelas'
						onClick=\"javascript:fnEditKelas(1, '')\" /></td>
					<td><input type=button name='TampilKelas' value='Tampilkan List Kelas'
						onClick=\"javascript:fnTampilKelas()\" /></td>
					<td><input type=button name='Otomatis' value='Kelaskan Otomatis'
						onClick=\"javascript:fnOtomatisKelas()\"></td>
				</tr>
				</table></p>";
	}
	
?>