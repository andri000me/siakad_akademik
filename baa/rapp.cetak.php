<?php
 $s0 = "select DISTINCT(left(TahunID,4)) as THN from tahun where TahunID>20102 order by TahunID DESC";
  $r0 = _query($s0);
  $opttahun = "<option value=''></option>";
  while($w0 = _fetch_array($r0))
  {  $ck = ($w0['THN'] == $_SESSION['thn'])? "selected" : '';
     $opttahun .=  "<option value='$w0[THN]' $ck>$w0[THN]</option>";
  }
  TampilkanJudul("Cetak RAPP");
  echo "<form name='cetak' method=post action='baa/rapp.php'><table width=400 class=box><tr><td class=inp>Tahun Akd</td>
  		<td class=ul1><select name='thn'>$opttahun</select> <input type=submit value='Cetak'></td></tr>
		</table></form>";
  