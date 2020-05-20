 <?php
 session_start();
 $idKampus=_fetch_array(_query("SELECT * FROM identitas"));
 $Institusi=$idKampus['Kode'];
 $Logo=$idKampus['Logo'];

 /*submit tombol event data*/
 if (isset($_POST['login'])) { 

  $kode_keamanan=$_POST['kode_keamanan'];
  $hasil=$_SESSION['val1'] + $_SESSION['val2'];
  $_SESSION['nilai']=$hasil;

  if ($_SESSION['nilai'] == $kode_keamanan) {

    $username = barasiah($_POST['username']);
    $password = barasiah($_POST['password']);
    /*cek permasing table*/
    if (empty($username)) {
      $_SESSION['pesan'] = "<div class='callout callout-danger'>Silahkan Isikan Username</div>";

    }elseif(empty($password)){
      $_SESSION['pesan'] = "<div class='callout callout-danger'>Password Tidak Boleh Kosong</div>";
    }else{ 
      $mhsw = _query("SELECT a.Nama as log_nama, a.* from mhsw a left join level b on (a.LevelID=b.LevelID) where a.Login='$username' AND a.Password=md5('".$password."') group by a.MhswID limit 1");
      $dosen =_query("SELECT a.Nama as log_nama, a.* from dosen a left join level b on (a.LevelID=b.LevelID) where a.Login='$username' AND a.Password=md5('".$password."') group by a.NIDN limit 1");
      $karyawan =_query("SELECT a.Nama as log_nama, a.* from karyawan a left join level b on (a.LevelID=b.LevelID) where a.Login='$username'  AND a.Password=LEFT(PASSWORD('".$password."'),10) group by a.Login limit 1");    

      if (_num_rows($mhsw) > 0) {
        $qmhs = _fetch_array($mhsw);
        $_Nama =  $qmhs['log_nama'];
        $_TabelUser = 'mhsw'; 
        $_LevelID = $qmhs['LevelID'];
        $Level='Mahasiswa';
        $_Superuser = '';
        $_ProdiID = $qmhs['ProdiID'];
        $_KodeID = $qmhs['KodeID'];
        $mdlid = 0; 
        $username = $qmhs['Login'];
        $gos = 'berhasil';
        $waktu_online = 600;
        $sess = 'ada';
      }elseif(_num_rows($dosen) > 0){
        $qdosen = _fetch_array($dosen);
        $_Nama = $qdosen['log_nama'];
        $_TabelUser ='dosen';
        $_LevelID = $qdosen['LevelID'];
        $Level='Dosen';
        $_Superuser = '';
        $_ProdiID = $qdosen['ProdiID'];
        $_KodeID = $qdosen['KodeID'];
        $mdlid = 0;
        $username = $qdosen['Login'];
        $gos = 'berhasil';
        $waktu_online = 1800;
        $sess = 'ada';
      }elseif(_num_rows($karyawan) > 0){ 
        $qkaryawan = _fetch_array($karyawan);
        $_Nama = $qkaryawan['log_nama'];
        $_TabelUser = 'karyawan';
        $_LevelID = $qkaryawan['LevelID'];
        $Level='STAFF';
        $_Superuser = $qkaryawan['Superuser'];
        $_ProdiID = $qkaryawan['ProdiID'];
        $_KodeID = $qkaryawan['KodeID'];
        $mdlid = 0;
        $username = $qkaryawan['Login'];
        $gos = 'berhasil';
        $waktu_online = 1800;
        $sess = 'ada';
      }else{
        $sess = 'kosong';
      }
      
      if ($sess == 'ada') { 

       $_SESSION['KodeID']=KodeID;
       $_SESSION['mnux']='zayed';
       $_SESSION['x_username']='p0rtal';
       $_SESSION['x_hostname']='localhost';
       $_SESSION['x_password']='xiYmr8Hztj9364IX';
       $_SESSION['x_name']='unes_portal';
       $_SESSION['Captcha']=$kode_keamanan;
       $_SESSION['Password']=$password;
       $_SESSION['Login']=$username;
       //$_SESSION['captchacode']=$captchacode;
       $_SESSION['_Login']=$username;
       $_SESSION['_Nama']=$_Nama;
       $_SESSION['_TabelUser']=$_TabelUser;
       $_SESSION['_LevelID']=$_LevelID;
       $_SESSION['_Session']=session_id();
       $_SESSION['_Superuser']='';
       $_SESSION['_ProdiID']=$_ProdiID;
       $_SESSION['_KodeID']=$_KodeID;
       $_SESSION['mdlid']=$mdlid;
       $_SESSION['username']=$username;
       $_SESSION['Level_akses'] = $Level;
       $_SESSION['gos']=$gos; 
       $_SESSION['ThnAkd'] =''; 
       $_SESSION['astcr'] =''; 
       $_SESSION['astkeycr'] ='';
       $_SESSION['astpage'] ='';
       $_SESSION['klp'] ='';
       $_SESSION['nds'] = '';
       $_SESSION['asturt'] = 'AssetID';
       $_SESSION['LokasiID'] = '';
       $_SESSION['KelompokID'] = '';
       $_SESSION['Pemakai'] = '';
       $_SESSION['Tahun'] = '';
       $_SESSION['timeout'] = time()+$waktu_online;
       $_SESSION['waktu_login'] = $waktu_online;
       $_SESSION['login'] = 1;
      
       $mulai = time()+$waktu_online;
       update_chat(session_id(),$username,$_LevelID,$_SERVER['REMOTE_ADDR'],$mulai,time());
       unset($_SESSION['pesan']);
       echo "<script>window.location='".base_url()."home';</script>";
     }elseif($sess == 'kosong'){
      $_SESSION['pesan'] = "<div class='callout callout-warning'>Username dan password salah.</div>";
    }     
  }
}else{
  $_SESSION['pesan'] = "<div class='callout callout-warning'>Maaf Kode Keamanan Yang anda masukan salah</div>";
}
} 
$un = _fetch_array(_query("SELECT Nama from identitas order by Kode"));
$universitas=isset($un['Nama']) ? $un['Nama'] : 'Undefined';
$val1=rand(1,10);
$val2=rand(2,10);

$_SESSION['val1'] = $val1;
$_SESSION['val2'] = $val2;

?>
