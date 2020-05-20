<?php
// disesuaikan kembali untuk pelaporan EPSBED oleh Arisal Yanuarafi, September 2012
$HeaderMasterMhsw = array ( // MSMHS
  array("KDPTIMSMHS", "C", 6),  // Kode Perguruan Tinggi
  array("KDJENMSMHS", "C", 1),  // Kode Jenjang Studi
	array("KDPSTMSMHS", "C", 5),  // Kode Program Studi
	array("NIMHSMSMHS", "C", 15), // NIM
	array("NMMHSMSMHS", "C", 30), // Nama
	array("SHIFTMSMHS", "C", 1), // Kode ProgramID
	array("TPLHRMSMHS", "C", 20), // Tempat Lahir
	array("TGLHRMSMHS", "C", 15),     // Tanggal Lahir
	array("KDJEKMSMHS", "C", 1),  //  Jenis Kelamin
	array("TAHUNMSMHS", "C", 4),	// Tahun masuk
	array("SMAWLMSMHS", "C", 5),	// Semester awal terdaftar
	array("BTSTUMSMHS", "C", 5),	// Batas Studi
	array("ASSMAMSMHS", "C", 2),	// Kode Propinsi Pendidikan terakhir
	array("TGMSKMSMHS", "D"),			// Tanggal Masuk
	array("TGLLSMSMHS", "C", 12),			// Tanggal Lulus
	array("STMHSMSMHS", "C", 1),	// Kode Status Mhsw
	array("JNSDAFTAR", "C", 1),	// Kode Status Awal (Baru/Pindahan) 
	array("SKSDIMSMHS", "N", 3, 0),	// Jumlah SKS Pindahan
	array("ASNIMMSMHS", "C", 15),	// NIM asal perguruan tinggi (Pindahan)
	array("ASPTIMSMHS", "C", 6),  // Kode Perguruan tinggi sebelumnya (Pindahan)
	array("ASJENMSMHS", "C", 1),	// Kode Jenjang studi sebelumnya (Pindahan)
	array("ASPSTMSMHS", "C", 5), 	// Kode Program Studi sebelumnya (Pindahan)
	array("BISTUMSMHS", "C", 1), 	// Kode Biaya studi (S3)
	array("PEKSBMSMHS", "C", 1), 	// Kode Pekerjaan (S3)
	array("NMPEKMSMHS", "C", 40),	// Nama Tenpat bekerja jika bukan dosen (S3)
	array("PTPEKMSMHS", "C", 6),	// Kode PT tempat bekerja bila dosen (S3)
	array("PSPEKMSMHS", "C", 5),	// Kode PS tempat bekerja bila dosen (S3)
	array("NMPRMMSMHS", "C", 10),	// NIDN Promotor #
	array("NOKP1MSMHS", "C", 10),	// NIDN Promotor 1
	array("Alamat", "C", 60),	// Alamat
	array("Agama", "C", 2),	// Agama
	array("NamaIbu", "C", 50));	// Nama Ibu
					
$HeaderMasterDosen = array ( // MSDOS
	array("KDPTIMSDOS", "C", 6),	// Kode Perguruan Tinggi
	array("KDPSTMSDOS", "C", 5),	// Kode Program Studi
	array("KDJENMSDOS", "C", 1),	// Kode Jenjang Studi
	array("NOKTPMSDOS", "C", 25),	// No KTP dosen
	array("NODOSMSDOS", "C", 10),	// Nomor dosen (NIDN)
	array("NMDOSMSDOS", "C", 30),	// Nama dosen
	array("GELARMSDOS", "C", 10),	// Gelas Akademik
	array("TPLHRMSDOS", "C", 20),	// Tempat Lahir
	array("TGLHRMSDOS", "D"),			// Tanggal Lahir
	array("KDJEKMSDOS", "C", 1),	// Kode Jenis Kelamin
	array("KDJANMSDOS", "C", 1),	// Kode Jabatan akademik
	array("KDPDAMSDOS", "C", 1),	// Kode Pendidikan Tertinggi
	array("KDSTAMSDOS", "C", 1),	// Kode Ikatan status kerja
	array("STDOSMSDOS", "C", 1),	// Kode Aktifitas Dosen
	array("MLSEMMSDOS", "C", 1),	// Semester Dosen Mulai
	array("NIPNSMSDOS", "C", 9),	// NIP PNS
	array("PTINDMSDOS", "N", 6, 0)
	);	// Homebase

$HeaderAktivitasMhsw = array ( // TRAKM
	array("THSMSTRAKM", "C", 5),	// Tahun Semester Pelaporan data
	array("KDPTITRAKM", "C", 6),	// Kode Perguruan Tinggi
	array("KDJENTRAKM", "C", 1),	// Kode Jenjang Studi
	array("KDPSTTRAKM", "C", 5),	// Kode Program Studi
	array("NAMAPRODI", "C", 50),	// Kode Program Studi
	array("NIMHSTRAKM", "C", 15),	// Nomor Induk Mhsw
	array("NAMAMHSW", "C", 50),	// Nama Mhsw
	array("SKSEMTRAKM", "N", 3, 0),	// SKS yang diambil
	array("NLIPSTRAKM", "N", 4, 2),	// Nilai IPS
	array("SKSTTTRAKM", "N", 3, 0),	// SKS Total diperoleh
	array("NLIPKTRAKM", "N", 4, 2),	// Nilai IPK
	array("STATUSMHSW", "C", 1)	// Status Mhsw
	);
					
$HeaderAktivitasDosen = array ( // TRAKD
	array("THSMSTRAKD", "C", 5),	// Tahun Semester Pelaporan data
	array("KDPTITRAKD", "C", 6),	// Kode Perguruan Tinggi
	array("KDJENTRAKD", "C", 1),	// Kode Jenjang Studi
	array("KDPSTTRAKD", "C", 5),	// Kode Program Studi
	array("NAMAPRODI", "C", 50),	// Kode Program Studi
	array("NODOSTRAKD", "C", 10),	// NIDN Dosen
	array("NAMAMK", "C", 50),	// Kode Mata Kuliah
	array("KDKMKTRAKD", "C", 10),	// Kode Mata Kuliah
	array("KELASTRAKD", "C", 2),	// Kode Kelas Pararel
	array("TMRENTRAKD", "N", 2, 0),	// Tatap Muka yang direncanakan
	array("TMRELTRAKD", "N", 2, 0),	// Tatap Muka Realisasi
	array("NAMADTRAKD", "C", 25, 0)	// Tatap Muka Realisasi
	);
					
$HeaderNilaiMhsw = array( // TRNLM
	
  array("THSMSTRNLM", "C", 5),	// Tahun Semester Pelaporan data
  array("KDPTITRNLM", "C", 6),	// Kode Perguruan Tinggi
  array("KDJENTRNLM", "C", 1),	// Kode Jenjang Studi
  array("KDPSTTRNLM", "C", 5),	// Kode Program Studi
  array("NAMAPRODI", "C", 50),	// Kode Program Studi
  array("NAMAMHSW", "C", 50),	// Kode Mata Kuliah
  array("NIMHSTRNLM", "C", 15),	// NIM Mahasiswa
  array("NAMAMK", "C", 50),	// Kode Mata Kuliah
  array("KDKMKTRNLM", "C", 10),	// Kode Mata Kuliah
  array("NAMAKELAS", "C", 2),	// Kode Mata Kuliah
  array("NLAKHTRNLM", "C", 2),	// Nilai Berupa A B C D E
  array("BOBOTTRNLM", "N", 4, 2),	// Bobot Nilai
  array("NILAIAKHIR", "N", 4, 2),	// Bobot Nilai
);

$HeaderKelulusanMhsw = array(      // *** TRLSM
  array('THSMSTRLSM', 'C', 5),     // Tahun semester
  array('KDPTITRLSM', 'C', 6),     // Kode PT
  array('KDJENTRLSM', 'C', 1),     // Kode jenjang
  array('KDPSTTRLSM', 'C', 5),     // Kode program studi
  array('NIMHSTRLSM', 'C', 15),    // NIM
  array('MAHASISWA', 'C', 50),     // *** Tambahan untuk STKIP PGRI Pontianak
  array('STMHSTRLSM', 'C', 1),     // Kode status mshw
  array('TGLLSTRLSM', 'C',10),        // Tgl Lulus
  array('SKSTTTRLSM', 'N', 19, 5), // Total SKS
  array('NLIPKTRLSM', 'N', 19, 5), // IPK
  array('NOSKRTRLSM', 'C', 30),    // SK Rektor
  array('TGLRETRLSM', 'D'),        // Tgl SK
  array('NOIJATRLSM', 'D'),    // No ijazah
  array('STLLSTRLSM', 'D'),     // Status lulus
  array('JNLLSTRLSM', 'C', 255),		//Jenis Skripsi Kelompok/Individual
  array('BLAWLTRLSM', 'C',10),		//Bulan dan Tahun Awal Skripsi
  array('BLAKHTRLSM', 'C',10),		//Bulan dan Tahun Akhir Skripsi
  array('NODS1TRLSM', 'C',10),		//Dosen Pembimbing 1
  array('NODS2TRLSM', 'C', 10),		// Dosen Pembimbing 2
  array('NODS3TRLSM', 'C', 10),
  array('NODS4TRLSM', 'C', 10),
  array('NODS5TRLSM', 'C', 10)
);
					
$HeaderAlamat = array (
          array("No", "N", 5, 0),
          array("MhswID", "C", 15),
          array("Nama", "C", 35),
          array("Alamat", "C", 65),
          array("RT/RW", "C", 8),
          array("Kota", "C", 25),
          array("Kode Pos", "C", 8),
          array("Telepon", "C", 15)
          );
$HeaderMatakuliah = array( // *** TBKMK
  array('THSMSTBKMK', 'C', 5),     // Tahun akademik
  array('KDPTITBKMK', 'C', 6),     // Kode PT Institusi
  array('KDJENTBKMK', 'C', 1),     // Kode jenjang prodi
  array('KDPSTTBKMK', 'C', 5),     // Kode Prodi
  array('KDKMKTBKMK', 'C', 10),    // Kode Matakuliah
  array('NAKMKTBKMK', 'C', 40),    // Nama Matakuliah
  array('SKSMKTBKMK', 'N', 19, 5), // SKS matakuliah
  array('SKSTMTBKMK', 'N', 2, 0),  // SKS Tatap muka
  array('SKSPRTBKMK', 'N', 2, 0),  // SKS Praktikum
  array('SKSLPTBKMK', 'N', 2, 0),  // SKS Lapangan
  array('SEMESTBKMK', 'C', 2),     // Semester MK
  array('KDWPLTBKMK', 'C', 1),     // Kode: A-wajib, B-pilihan, C-wajib peminatan, D-pilihan peminatan, S-skripsi
  array('KDKURTBKMK', 'C', 1),     // Kode Kurikulum Inti/Institusi
  array('KDKELTBKMK', 'C', 1),     // Kode Kelompok Mata Kuliah
  array('NODOSTBKMK', 'C', 10),    // NIDN
  array('STKMKTBKMK', 'C', 1),     // Status MK (A-aktif, H-hapus)
  array('SLBUSTBKMK', 'C', 1),     // Ketersediaan Silabus
  array('SAPPPTBKMK', 'C', 1),     // Ketesedian satuan acara pengajaran (Y/T)
  array('BHNAJTBKMK', 'C', 1),     // Ketersediaan bahan ajar/Diktat (Y/T)
  array('DIKTTTBKMK', 'C', 1) 		// Diktat
  );
$HeaderNilaiPindahan = array ( // TRNLP
	array("THSMSTRNLP", "C", 5),	// Tahun Semester Pelaporan data
	array("KDPTITRNLP", "C", 6),	// Kode Perguruan Tinggi
	array("KDJENTRNLP", "C", 1),	// Kode Jenjang Studi
	array("KDPSTTRNLP", "C", 5),	// Kode Program Studi
	array("NIMHSTRNLP", "C", 15),	// NPM Mahasiswa
	array("KDKMKTRNLP", "C", 10),	// Kode Mata Kuliah
	array("NLAKHTRNLP", "C", 2),	// Nilai
	array("BOBOTTRNLP", "N", 4, 2),	// Bobot
	array("KELASTRNLP", "C", 2)	// Kelas
	);
?>
