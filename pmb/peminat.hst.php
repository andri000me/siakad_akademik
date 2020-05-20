<?php 
	//Author: Irvandy Goutama
	// Start Date: 25 January 2009
	
	session_start();
	include_once "../sisfokampus1.php";
	HeaderSisfoKampus("History Aplikan");
	
	$gos = (empty($_REQUEST['gos']))? "fnHistory" : $_REQUEST['gos'];
	$gos();
		
	function printStatus($aplikan, $statusAplikan)
	{	$colorRed = '#FF8080';
		$colorGreen = '#B0FFB0';
		$colorGrey = '#D3D3D3';
	
		$statuslanjut = $aplikan['StatusAplikanID'];
		if($status == '' or empty($status)) { echo "<tr bgcolor=$colorGrey>"; $StatusIcon = 'kali.png'; }
		else if($status == 'STOP') { echo "<tr bgcolor=$colorRed>"; $StatusIcon = 'N.gif'; }
		else
		{	if($status == $tahapbefore and $statusmundur == 'N') { echo "<tr bgcolor=$colorRed>"; $statuslanjut = 'STOP'; $StatusIcon = 'N.gif'; }
			else if($status == $tahap and $statusmundur == 'Y') { echo "<tr bgcolor=$colorGreen>"; $statuslanjut = ''; $StatusIcon = 'Y.gif'; }
			else { echo "<tr bgcolor=$colorGreen>"; $StatusIcon = 'Y.gif';}
		}
		
		echo "  <td class=inp><b>$no. $tahap</b> - $namatahap</td>
				<td class=ul1 align=center><img src='../img/$StatusIcon'></td>";
		
		
				
		echo	"</tr>";
		
		echo "<tr><td>&nbsp</td></tr>";
		
		return $statuslanjut;
	}	
	
	function TutupScript() {
	
	echo "<SCRIPT>
			function ttutup(bck) {
				opener.location='../index.php?mnux=$_SESSION[mnux]';
				self.close();
				return false;
			}
		</SCRIPT>";
	}
	
	function ReverseStatus()
	{	TutupScript();
		$AplikanID = $_REQUEST['id'];
		$StatusMundur = GetaField('aplikan', 'AplikanID', $AplikanID, "StatusMundur");
		
		if($StatusMundur == 'Y')
		{	
			$s = "update `aplikan` set StatusMundur = 'N',
										LoginMundur = '$_SESSION[_Login]',
										TanggalMundur = now()
									where AplikanID='$AplikanID'";
			$r = _query($s);
			
			/*$StatusAplikanMhswUpdate = GetaField('statusaplikanmhsw sam left outer join statusaplikan sa', 
				"sa.Urutan=(select max(Urutan) from statusaplikanmhsw sam2 where sam2.AplikanID='$AplikanID') and sam.AplikanID='$AplikanID' and sam.KodeID", 
				KodeID, 
				'StatusAplikanMhswID');
			$s = "update statusaplikanmhsw setStatusMundur='N' where StatusAplikanMhswID='$StatusAplikanMhswUpdate'";
			*/
			echo "<script>ttutup('$_SESSION[mnux]');</script>";
		}
		else if($StatusMundur == 'N')
		{	
			$s = "update `aplikan` set StatusMundur = 'Y' where AplikanID='$AplikanID'";
			$r = _query($s);
			
			echo "<script>ttutup('$_SESSION[mnux]');</script>";
		}
		else
		{   echo ErrorMessage("Error", "Status Mundur tidka dikenali. Harap hubungi administrator");
		}
		
	}
	
	function fnHistory()
	{	$colorRed = '#FF0000';
		$colorGreen = '#B0FFB0';
		$colorGrey = '#D3D3D3';
	
		$AplikanID = $_REQUEST['id'];
		$aplikan = GetFields('aplikan', "AplikanID='$AplikanID' and KodeID", KodeID, '*');
		
		echo "<table class=box cellspacing=1 align=center>
				<form action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden name='gos' value='ReverseStatus' />
				<input type=hidden name='id' value='$AplikanID' />
				
					<tr>
						<th class=ttl>Tahap</th>
						<th class=ttl>Status</th>
						<th class=ttl>TanggalEdit</th>
						<th class=ttl>LoginEdit</th>
						<th class=ttl>^</th>
					</tr>";
		
		$s = "select sam.TanggalEdit, sam.LoginEdit, sam.StatusMundur, sam.StatusAplikanMhswID, 
					sa.StatusAplikanID, sa.Urutan, sa.Nama
				from statusaplikan sa 
					left outer join statusaplikanmhsw sam on sam.StatusAplikanID=sa.StatusAplikanID and sam.AplikanID='$AplikanID' and sam.KodeID='".KodeID."' 
				where sa.NA='N'  and sa.KodeID='".KodeID."'
				order by sa.Urutan";
		$r = _query($s);
		
		$n = 0;
		while($w = _fetch_array($r))
		{	$n++;
			if($w['StatusMundur'] == 'Y') $bgcolor = $colorRed;
			else $bgcolor = $colorGreen;
			
			if($w['StatusAplikanMhswID']+0 == 0)
			{	$StatusIcon = 'N.gif';
				$bgcolor = $colorGrey;
			}
			else $StatusIcon = 'Y.gif';
			
			echo "<tr bgcolor=$bgcolor>
				<td class=inp><b>$n. $w[StatusAplikanID]</b> - $w[Nama]</td>
				<td class=ul1 align=center><img src='../img/$StatusIcon'></td>";
			echo "<td class=ul1 align=center>$w[TanggalEdit]</td>
				<td class=ul1 align=center>$w[LoginEdit]</td>";
		}
		
		/*
		$StatusAplikan = printStatus(2, $StatusAplikan, 'BLI', 'Tahap Pembelian Formulir', 'APL',
					"select TanggalBuat, LoginBuat from `pmbformjual` where AplikanID = '$AplikanID' and KodeID='".KodeID."'",
					'TanggalBuat', 'LoginBuat');
		
		$StatusAplikan = printStatus(3, $StatusAplikan, 'DFT', 'Tahap Pengembalian Formulir', 'BLI',
					"select TanggalBuat, LoginBuat from `pmb` where AplikanID = '$AplikanID' and KodeID='".KodeID."'",
					'TanggalBuat', 'LoginBuat');
		
		$data = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $aplikan['PMBID'], 'PMBPeriodID, ProdiID');
		$StatusAplikan = printStatus(4, $StatusAplikan, 'USM', 'Tahap Pelaksanaan USM', 'DFT', 
					"select min(TanggalEdit) as _Tanggal, LoginEdit from `ruangusm` where PMBID = '$PMBID' and KodeID='".KodeID."' group by TanggalEdit",
					'_Tanggal', 'LoginEdit');
					
		$StatusAplikan = printStatus(5, $StatusAplikan, 'LLS', 'Tahap Kelulusan USM', 'USM',
					"select TanggalEdit, LoginEdit from `pmb` where AplikanID = '$AplikanID' and KodeID='".KodeID."'",
					'TanggalEdit', 'LoginEdit');
		
		$StatusAplikan = printStatus(6, $StatusAplikan, 'REG', 'Tahap Registrasi Mahasiswa', 'LUL',
					"select TanggalBuat, LoginBuat from `mhsw` where AplikanID ='$AplikanID' and KodeID='".KodeID."'",
					'TanggalBuat', 'LoginBuat');
		*/
		
		echo "<tr>
				<td class=inp>Catatan Presenter</td>
				<td colspan=3><textarea name='CatatanPresenter' rows=15 cols=35>$aplikan[CatatanPresenter]</textarea></td>
			  </tr>
			  <tr>
				<td colspan=4 align=center>";
		//echo "<td class=ul1 align=center>$aplikan[TanggalMundur]</td>
				//<td class=ul1 align=center>$aplikan[LoginMundur]</td>";
		
		if($aplikan['StatusMundur']=='N')
		{
			echo "<input type=button name='Mundur' value='Mengundurkan Diri' onClick=\"this.form.submit()\" />"; 
		}
		else
		{
			echo "<input type=button name='Batal Mundur' value ='Batal Mundur' onClick=\"this.form.submit()\" />";
		}
			echo "</td>
			</tr>";
		
	}

?>