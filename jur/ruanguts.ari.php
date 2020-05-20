<tr><td class=inp>Ruang UTS 2:</td>
      <td class=ul1>
        <input type=text name='UTSRuangID2' value='$w[UTSRuangID]' size=10 maxlength=50 
          onKeyUp="javascript:CariRuang2('$_SESSION[_utsProdi]', 'frmJadwalUTS')" />
        &raquo;
      <a href='#'
        onClick="javascript:CariRuang2('$_SESSION[_utsProdi]', 'frmJadwalUTS')" />Cari...</a> |
      <a href='#' onClick="javascript:frmJadwalUTS.UTSRuangID2.value=''">Reset</a> <sub>*) Kosongkan jika memakai 1 ruang </sub>
        </td>
      <td class=inp>Kapasitas:</td>
      <td class=ul1>
        <input type=text name='UTSKapasitas2' value='$w[UTSKapasitas]' size=4 maxlength=5 />
        <sub>orang</sub>
        </td>
      </tr>
  <tr><td class=inp>Kolom Ujian:</td>
	  <td class=ul1><input type=text name='UTSKolomUjian2' value='$w[UTSKolomUjian]' onChange="HitungBaris('frmJadwalUTS')" size=1 maxlength=2 />
	  <td class=inp>Baris Ujian:</td>
	  <td class=ul1><input type=text name='UTSBarisUjian2' value='$w[UTSBarisUjian]' size=1 maxlength=2 disabled />
  </tr>
  
  <tr><td class=inp>Dosen Pengawas:</td>
      <td class=ul1 colspan=3 nowrap>
      <input type=text name='UTSDosenID2' value='$w[UTSDosenID]' size=10 maxlength=50 />
      <input type=text name='UTSDosen2' value='$w[UTSDosen]' size=30 maxlength=50 onKeyUp="javascript:CariDosen2('$_SESSION[_utsProdi]', 'frmJadwalUTS')" />
      <div style='text-align:right'>
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$_SESSION[_utsProdi]', 'frmJadwalUTS')" />Cari...</a> |
      <a href='#' onClick="javascript:frmJadwalUTS.UTSDosenID2.value='';frmJadwalUTS.UTSDosen2.value=''">Reset</a>
      </div>
      </td>
      </tr>
