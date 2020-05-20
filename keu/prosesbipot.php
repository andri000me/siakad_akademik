<?php
// Author  : Emanuel Setio Dewo
// Email   : setio.dewo@gmail.com 
// Start   : 25/11/2008


// *** Parameters ***
$TahunID = GetSetVar('TahunID');

// *** Main ***
TampilkanJudul("Proses Bipot Mahasiswa");
$gos = (empty($_REQUEST['gos']))? "Konf" : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Konf() {
  CheckFormScript('TahunID');
  echo Konfirmasi("Proses Bipot Mahasiswa (Massal)",
    "Anda akan memproses Biaya & Potongan (BIPOT) Mahasiswa secara massal.<br />
    Tentukan dulu tahun akademik yang akan diproses.<br />
    Hanya mahasiswa yang terdaftar di tahun akademik saja yang akan diproses.
    <hr size=1 color=silver />
    <form name='frmKonf' action='?' method=POST onSubmit='return CheckForm(this)'>
    <input type=hidden name='gos' value='fnProses' />
    Tahun Akademik: <input type=text name='TahunID' value='$_SESSION[TahunID]'
      size=6 maxlength=6 />
    <input type=submit name='btnProses' value='Mulai Proses' />
    <hr size=1 color=silver />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    </form>");
}

function fnProses() {
  $_SESSION['_bptMax'] = 2;
  $_SESSION['_bptPage'] = 0;
  $_SESSION['_bptCounter'] = 0;
  
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form name='frmProses' >
  
  <tr><th class=ttl colspan=4>Proses Penghitungan BIPOT Mhsw</th></tr>
  <tr><td class=inp>Proses:</td>
      <td class=ul><input type=text name='_bptCounter' value='' size=10 maxlength=50 /></td>
      <td class=inp>NIM/NPM:</td>
      <td class=ul><input type=text name='_bptMhswID' value='' size=20 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Nama Mhsw:</td>
      <td class=ul colspan=3>
        <input type=text name='_bptNamaMhsw' value='' size=50 maxlength=50 />
        <input type=button name='btnBatal' value='Batal & Kembali'
          onClick="location='?mnux=$_SESSION[mnux]'" />
        </td>
      </tr>
  <tr><td class=inp>BIPOT:</td>
      <td class=ul colspan=3>
        <input type=text name='_bptJumlah' value='0' size=20 maxlength=50 />
      </td>
      </tr>
  </form>
  </table>
  
  <script>
  function fnProgress(cnt, mhswid, nama, jml) {
    frmProses._bptCounter.value = cnt;
    frmProses._bptMhswID.value = mhswid;
    frmProses._bptNamaMhsw.value = nama;
    frmProses._bptJumlah.value = jml;
  }
  function fnSelesai(thn, cnt) {
    alert("Proses BIPOT mahasiswa tahun " + thn + " telah selesai. Berhasil diproses: " + cnt);
    window.location="../index.php?mnux=$_SESSION[mnux]";
  }
  </script>
  
  <iframe name='frmProsesDetail' src="$_SESSION[mnux].prc.php?gos=&_bptMax=$_SESSION[_bptMax]&_bptPage=0&_bptCounter=0" frameborder=0 width=600 height=200 align=center>
  Tidak mendukung frame.
  </iframe>
ESD;
}
?>
