<?php

function SetStatusAplikan($Status, $AplikanID, $TahunID, $cekBefore=0)
{	$ada = GetaField('statusaplikanmhsw', "StatusAplikanID='$Status' and AplikanID='$AplikanID' and TahunID='$TahunID' and KodeID", KodeID, "StatusAplikanMhswID");
	if(empty($ada))
	{
		$s = "insert into statusaplikanmhsw set KodeID='".KodeID."', AplikanID='$AplikanID', StatusAplikanID='$Status', TahunID='$TahunID',  
					TanggalBuat=now(), LoginBuat='$_SESSION[_Login]',
					TanggalEdit=now(), LoginEdit='$_SESSION[_Login]'";
		$r = _query($s);
		
		$maxUrutan = GetaField('statusaplikanmhsw sam left outer join statusaplikan sa on sam.StatusAplikanID=sa.StatusAplikanID', "sam.AplikanID='$AplikanID' and sam.KodeID", KodeID, "max(sa.Urutan)+0");
		$maxStatus = GetaField('statusaplikan', "Urutan='$maxUrutan' and KodeID", KodeID, "StatusAplikanID"); 
		
		$s = "update aplikan set StatusAplikanID = '$maxStatus' where AplikanID='$AplikanID'";
		$r = _query($s);
		
	}
	else
	{	$s = "update statusaplikanmhsw set TanggalEdit=now(), LoginEdit='$_SESSION[_Login]'";
		$r = _query($s);
	}
}

/*function DeleteStatusAplikan($Status, $AplikanID)
{	$s = "insert into statusaplikanmhsw set KodeID='".KodeID."', AplikanID='$AplikanID', StatusAplikanID='$Status', 
				TanggalBuat=now(), LoginBuat='$_SESSION[_Login]',
				TanggalEdit=now(), LoginEdit='$_SESSION[_Login]'";
	$r = _query($s);
}*/	

?>
