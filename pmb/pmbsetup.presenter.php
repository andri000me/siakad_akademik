<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 27 Maret 2009

// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrPresenter' : $_REQUEST['sub'];
$sub();

// *** functions ***
function DftrPresenterScript() {
  echo <<<SCR
  <script>
	function PresenterEdt(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].presenter.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=500, height=300, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

function DftrPresenter() {
  DftrPresenterScript();
  $gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', 'PMBPeriodID');
  $n = 0;
  echo "<p><table class=box cellspacing=1 align=center width=500>
			<form name='datatech' action='?mnux=$_SESSION[mnux]' method=POST>
				<input type=hidden name='gel' value='$gelombang'>";
  echo "<tr>
		<td class=ul1 colspan=6>
      <input type=button name='TambahPresenter' value='Tambah Presenter' onClick=\"PresenterEdt(1, '', '$_SESSION[mnux]')\"/>
      <input type=button name='Refresh' value='Refresh'
        onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />
    </td>
    </tr>";
  echo "<tr>
    <th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>NA</th>
    </tr>";
  
	$s="select * from presenter where KodeID='".KodeID."'";
	$r=_query($s);
	while($w=_fetch_array($r))
	{
		$n++;
		$c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
		$edit= "<a href='#' onClick=\"javascript:PresenterEdt(0, '$w[PresenterID]', '$_SESSION[mnux]')\"><img src='img/edit.png' /></a>";
		echo "<tr>
		  <td class=inp width=28>$n $edit</td>
		  <td $c width=40>$w[PresenterID]</td>
		  <td $c>$w[Nama]</td>
		  <td class=ul1 align=center width=10><img src='img/book$w[NA].gif' /></td>
		  <td class=ul1 width=10>
			</td>
		  </tr>";
	}

  echo "</form></table>";
}
