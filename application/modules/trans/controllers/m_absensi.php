<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_absensi extends CI_Model {

	var $table = 'USERINFO';
	var $column = array('USERID','Badgenumber','Name');
	var $order = array('USERID' => 'asc');
	private $dbsby,$dbdmk,$dbcnd,$dbjkt;


	public function __construct()
	{
		parent::__construct();
		$this->dbsby = $this->load->database('SBYMRG', TRUE);
		$this->dbdmk = $this->load->database('SMGDMK', TRUE);
		$this->dbcnd = $this->load->database('SMGCND', TRUE);
		$this->dbjkt = $this->load->database('JKTKPK', TRUE);
		$this->dbskhrj = $this->load->database('SKHRJ', TRUE);
	}

	function q_trxabsen(){
		return $this->db->query("select * from sc_mst.trxtype where jenistrx in ('ABSEN','PRESENT') and kdtrx not in ('IJD','IJP','LMB') order by uraian ");
	}
	function q_cek_fingerid($badgenumber){
		return $this->db->query("select * from sc_mst.karyawan where idabsen='$badgenumber'");
	}


	function show_user_sby($tgl1,$tgl2){
		return $this->dbsby->query("select top 10000 * from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#
								order by CHECKTIME ,Name asc
								");
	}

	function show_user_dmk($tgl1,$tgl2){
		return $this->dbdmk->query("select top 10000 * from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#
								order by CHECKTIME ,Name asc
								");
	}

	function show_user_cnd($tgl1,$tgl2){
		return $this->dbcnd->query("select top 10000 * from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#
								order by CHECKTIME ,Name asc
								");
	}
	function show_user_jkt($tgl1,$tgl2){
		return $this->dbjkt->query("select top 10000 * from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#
								order by CHECKTIME ,Name asc
								");
	}

	function show_user_skhrj($tgl1,$tgl2){
		return $this->dbskhrj->query("select top 10000 * from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#
								order by CHECKTIME ,Name asc
								");
	}

	function ttldata_sby($tgl1,$tgl2){
		return $this->dbsby->query("select count(*) as jumlah from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#								
								");
	}

	function ttldata_dmk($tgl1,$tgl2){
		return $this->dbdmk->query("select count(*) as jumlah from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#								
								");
	}

	function ttldata_cnd($tgl1,$tgl2){
		return $this->dbcnd->query("select count(*) as jumlah from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#								
								");
	}

	function ttldata_jkt($tgl1,$tgl2){
		return $this->dbjkt->query("select count(*) as jumlah from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#								
								");
	}

	function ttldata_skhrj($tgl1,$tgl2){
		return $this->dbjkt->query("select count(*) as jumlah from CHECKINOUT
								LEFT JOIN USERINFO on USERINFO.USERID=CHECKINOUT.USERID
								where CHECKTIME between #$tgl1# and #$tgl2#								
								");
	}

	function simpan($info){

		$this->db->insert('sc_tmp.checkinout',$info);
	}


	public function get_by_id($id)
	{
		//$this->db2->from($this->table);
		//$this->db2->where('USERID',$id);
		//$query = $this->db2->get();
		$query = $this->db2->query('select * from USERINFO where USERID="'.$id.'"');
		return $query->row();
	}

	public function save($data)
	{
		return $this->db2->insert($this->table, $data);
		//return $this->db2->insert_id();
	}

	public function update($where, $data)
	{
		$this->db2->update($this->table, $data, $where);
		return $this->db2->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db2->where('USERID', $id);
		$this->db2->delete($this->table);
	}

	function q_absensi_kor($tgl1,$tgl2,$nik){
		return $this->db->query("select b.nik,b.nmlengkap,to_char(a.checktime,'DD-MM-YYYY')as tanggal,to_char(a.checktime,'hh24:mi:ss')as jam,
								case 
								when a.editan='t' then 'YES'
								else 'NO' end as status_edit,
								a.* from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.nik
								where a.checktime between '$tgl1' and '$tgl2' and b.nik='$nik'
								order by a.checktime asc,a.nama asc
								");

	}

	function q_absensi2($tgl1,$nik){
		return $this->db->query("select t7.nmlengkap,t4.nik,t4.tanggal,t4.jam,t4.ket,
								case 
								when jam>t5.jam_masuk and ket='MASUK' then 'TELAT' 
								when jam>t5.jam_pulang and ket='PULANG' then 'LOYALITAS'
								when jam>t5.jam_pulang_min and jam<t5.jam_pulang and ket='PULANG' then 'PULANG AWAL'   
								when jam<t5.jam_masuk and jam>jam_masuk_min and ket='MASUK' then 'ONTIME'   
								when jam>t5.jam_pulang and jam<jam_pulang_max and ket='PULANG' then 'ONTIME'   
								when jam>t5.jam_istirahat_min and jam<jam_istirahatselesai and ket='ISTIRAHAT' then 'ONTIME'   
								else 'TIDAK TERDETEKSI'
								end as ketper
								from 
								(select nik,tanggal,min(jam) as jam,ket
								from (select t1.nik,tanggal,jam
								,case when jam>jam_masuk_min and jam<jam_masuk_max then 'MASUK'
								when jam>jam_pulang_min and jam<jam_pulang_max then 'PULANG' 
								when jam>jam_masuk_max and jam>jam_istirahat and jam<jam_istirahatselesai then 'ISTIRAHAT'
								else 'TIDAK ADA KETERANGAN' 
								end as ket
								from
								(select * from(select b.nik,cast(to_char(a.checktime,'DD-MM-YYYY') as date)as tanggal,cast(to_char(a.checktime,'hh24:mi:ss') as time) as jam,
								case when a.editan='t' then 'YES' else 'NO' end as status_edit,
								a.* from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.nik) as t6
								where t6.tanggal='$tgl1' and t6.nik='$nik'
								order by checktime asc,nama asc) as t1
								left outer join (select b.*,a.* from sc_trx.dtljadwalkerja a
								left outer join sc_mst.jam_kerja b on a.kdjamkerja=b.kdjam_kerja
								where a.nik='$nik' and a.tgl='$tgl1') as t2 on t1.nik=t2.nik and t1.tanggal=t2.tgl) as t3
								group by nik,tanggal,ket) as t4
								left outer join (select b.*,a.* from sc_trx.dtljadwalkerja a
								left outer join sc_mst.jam_kerja b on a.kdjamkerja=b.kdjam_kerja
								where a.nik='$nik' and a.tgl='$tgl1') as t5 on t4.nik=t5.nik and t4.tanggal=t5.tgl
								left outer join sc_mst.karyawan t7 on t4.nik=t7.nik");

	}

	function q_absensi($tgl1,$tgl2,$nik) {
		return $this->db->query("select t7.nmlengkap,t4.nik,t4.tanggal,t4.jam,t4.ket,
								case 
								when jam>t5.jam_masuk and ket='MASUK' then 'TELAT' 
								when jam>t5.jam_pulang and ket='PULANG' then 'LOYALITAS'
								when jam>t5.jam_pulang_min and jam<t5.jam_pulang and ket='PULANG' then 'PULANG AWAL'   
								when jam<t5.jam_masuk and jam>jam_masuk_min and ket='MASUK' then 'ONTIME'   
								when jam>t5.jam_pulang and jam<jam_pulang_max and ket='PULANG' then 'ONTIME'   
								when jam>t5.jam_istirahat_min and jam<jam_istirahatselesai and ket='ISTIRAHAT' then 'ONTIME'   
								else 'TIDAK TERDETEKSI'
								end as ketper
								from 
								(select distinct nik,tanggal,min(jam) as jam,ket
								from (select t1.nik,tanggal,jam
								,case when jam>jam_masuk_min and jam<jam_masuk_max then 'MASUK'
								when jam>jam_pulang_min and jam<jam_pulang_max then 'PULANG' 
								when jam>jam_masuk_max and jam>jam_istirahat and jam<jam_istirahatselesai then 'ISTIRAHAT'
								else 'TIDAK ADA KETERANGAN' 
								end as ket
								from
								(select * from(select b.nik,cast(to_char(a.checktime,'DD-MM-YYYY') as date)as tanggal,cast(to_char(a.checktime,'hh24:mi:ss') as time) as jam,
								case when a.editan='t' then 'YES' else 'NO' end as status_edit,
								a.* from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.nik) as t6
								where t6.tanggal between '$tgl1' and '$tgl2' and t6.nik='$nik' 
								order by checktime asc,nama asc) as t1
									left outer join (select b.*,a.* from sc_trx.dtljadwalkerja a
									left outer join sc_mst.jam_kerja b on a.kdjamkerja=b.kdjam_kerja
									where a.nik='$nik ' and a.tgl between '$tgl1' and '$tgl2') as t2 on t1.nik=t2.nik and t1.tanggal=t2.tgl) as t3
									group by nik,tanggal,ket) as t4
								left outer join (select b.*,a.* from sc_trx.dtljadwalkerja a
								left outer join sc_mst.jam_kerja b on a.kdjamkerja=b.kdjam_kerja
								where a.nik='$nik ' and a.tgl between '$tgl1' and '$tgl2') as t5 on t4.nik=t5.nik and t4.tanggal=t5.tgl
								left outer join sc_mst.karyawan t7 on t4.nik=t7.nik
								order by t4.tanggal asc,t4.ket asc");

	}
	function q_karyawan(){
		return $this->db->query("select * from sc_mst.karyawan order by nmlengkap asc");

	}

	function q_regu(){
		return $this->db->query("select * from sc_mst.regu order by nmregu asc");
	}

	function q_department(){
		return $this->db->query("select * from sc_mst.departmen order by nmdept asc");
	}

	function q_regu_detail(){
		return $this->db->query("select distinct kdregu,tgl from sc_trx.jadwalkerja");
	}

	function q_jamkerja(){
		return $this->db->query("select * from sc_mst.jam_kerja");
	}

	function q_jadwalkerja($tgl){
		return $this->db->query("select distinct tgl,to_char(tgl,'DD') as tglnya,
								to_char(tgl,'MM') as bln,to_char(tgl,'YYYY') as tahun
								from sc_trx.jadwalkerja
								where to_char(tgl,'mmYYYY')='$tgl'
								order by tgl asc
								");
	}

	function q_dtljadwalkerja($tgl_kerja){
		return $this->db->query("select  id,kdregu,kodejamkerja,to_char(tgl,'DD-MM-YYYY') as tgl,b.jam_masuk,b.jam_pulang from sc_trx.jadwalkerja a 
								left outer join sc_mst.jam_kerja b on a.kodejamkerja=b.kdjam_kerja
								where tgl='$tgl_kerja'");
	}


	function q_dtljadwalkerja_regu($tgl_kerja,$kdregu){
		return $this->db->query("select to_char(tgl,'DD-MM-YYYY') as tglnya,to_char(tgl,'DDMMYYYY') as tglkode,b.nmlengkap,c.jam_masuk,c.jam_pulang,a.* from sc_trx.dtljadwalkerja a
								left outer join sc_mst.karyawan b on a.nik=b.nik
								left outer join sc_mst.jam_kerja c on a.kdjamkerja=c.kdjam_kerja 

								where a.kdregu='$kdregu' and a.tgl='$tgl_kerja'");
	}

	function q_jadwalkerja_ns($tgl_kerja){
		return $this->db->query("select b.nmlengkap,a.* from sc_trx.jadwalkerja_ns a
								left outer join sc_mst.karyawan b on a.nik=b.nik
								where a.tgl='$tgl_kerja'");
	}

	function q_dtljadwalkerja_ns($tgl_kerja,$nik){
		return $this->db->query("select b.nmlengkap,a.* from sc_trx.jadwalkerja_ns a
								left outer join sc_mst.karyawan b on a.nik=b.nik
								where a.nik='$nik' and a.tgl='$tgl_kerja'");
	}

	function q_mesin(){
		return $this->db->query("select * from sc_mst.mesin");
	}

	function cek_exist($kdregu,$kdjamkerja,$tgl){
		return $this->db->query("select * from sc_trx.jadwalkerja a
								where kdregu='$kdregu' and kodejamkerja='$kdjamkerja' and tgl='$tgl'")->num_rows();
	}


	function cek_exist_detail($nik,$kdjamkerja,$tgl){
		return $this->db->query("select * from sc_trx.dtljadwalkerja a
								where nik='$nik' and kdjamkerja='$kdjamkerja' and tgl='$tgl'")->num_rows();
	}

	function cek_exist_ns($nik,$kdjamkerja,$tgl){
		return $this->db->query("select * from sc_trx.jadwalkerja_ns a
								where nik='$nik' and kodejamkerja='$kdjamkerja' and tgl='$tgl'")->num_rows();
	}

	function cek_shift($tgl_kerja,$shift){
		return $this->db->query("select * from sc_trx.jadwalkerja 
								where tgl='$tgl_kerja' and shift_tipe='$shift'")->num_rows();
	}

	function q_showcheckinout($tgl){
		return $this->db->query("select cast(to_char(checktime,'hh24:mi:ss')as time) as jam,c.kdjamkerja,* from 
								(select cast(to_char(a.checktime,'DD-MM-YYYY')as date) as tglchecktime,b.nik,a.* from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.nik
								where to_char(a.checktime,'DD-MM-YYYY')='$tgl' and b.nik in (select nik from sc_trx.dtljadwalkerja)) as t1 
								left outer join sc_trx.dtljadwalkerja c on c.nik=t1.nik and c.tgl=t1.tglchecktime
								left outer join sc_mst.jam_kerja d on d.kdjam_kerja=c.kdjamkerja");

	}


	function q_transready_old(){
		return $this->db->query("select cast((cast(tgl as date)- integer '1')||' '||to_char(jam_masuk_min,'hh24:mi:ss') as timestamp)as tgljadwal,
								cast(to_char(checktime,'hh24:mi:ss')as time) as jam,cast(to_char(checktime,'DD-MM-YYYY')as date) as tglcheck,* from sc_trx.transready 
								where to_char(checktime,'DD-MM-YYYY')='02-12-2015'	
								order by shiftke");

	}

	function q_loopingjadwal($tglawal,$tglakhir){

		return $this->db->query("select 
								a.nik,tgl,kdjamkerja,jam_masuk,jam_masuk_min,jam_pulang,jam_pulang_min,jam_pulang_max,kdharimasuk,kdharipulang,shiftke,
								case 
								when shiftke='3' and kdharimasuk='H-' then cast(to_char((cast(a.tgl as date)- integer '1'),'YYYY-MM-DD')||' '||to_char(b.jam_masuk_min,'HH24:MM:SS') as timestamp)	
								else cast(to_char((cast(a.tgl as date)),'YYYY-MM-DD')||' '||to_char(b.jam_masuk_min,'HH24:MM:SS') as timestamp) 
								end as tgl_min_masuk,
								case 
								when shiftke='2' and kdharipulang='H+' then cast(to_char((cast(a.tgl as date)+ integer '1'),'YYYY-MM-DD')||' '||to_char(b.jam_pulang_max,'HH24:MM:SS') as timestamp) 
								else cast(to_char((cast(a.tgl as date)),'YYYY-MM-DD')||' '||to_char(b.jam_pulang_max,'HH24:MM:SS') as timestamp)
								end as tgl_max_pulang
								from sc_trx.dtljadwalkerja a,sc_mst.jam_kerja b,sc_tmp.checkinout c, sc_mst.karyawan d
								where a.kdjamkerja=b.kdjam_kerja  and a.nik=d.nik and c.badgenumber=d.nik
								and to_char(tgl,'YYYY-MM-DD') between '$tglawal' and '$tglakhir' 
								group by a.nik,tgl,kdjamkerja,jam_masuk,jam_masuk_min,jam_pulang,jam_pulang_min,jam_pulang_max,kdharimasuk,kdharipulang,shiftke
								order by shiftke,tgl,nik
								");
	}


	//to_char(a.tgl,'YYYY-MM-DD')=to_char(c.checktime,'YYYY-MM-DD') and c.checktime is not null

	function q_caricheckinout($nik,$tgl1,$tgl2){

		return $this->db->query("select b.nik,min(checktime) as tgl_min,max(checktime) as tgl_max from sc_tmp.checkinout a,sc_mst.karyawan b
								where a.badgenumber=b.nik and  to_char(a.checktime,'YYYY-MM-DD hh24:mi:ss')>= '$tgl1' 
								and to_char(a.checktime,'YYYY-MM-DD hh24:mi:ss')<='$tgl2' and b.nik='$nik'
								
								group by b.nik");
	}

	function q_transready_koreksi($nik,$tgl1,$tgl2){
		return $this->db->query("select b.nmlengkap, 
										case 
										when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
										when shiftke='3' and jam_masuk_absen>=jam_masuk_min  then 'TIDAK TERLAMBAT'
										when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
										when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
										when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
										when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
										when jam_masuk_absen is null and jam_pulang_absen is null then 'TIDAK MASUK KERJA'
										when shiftke='2' and jam_masuk=jam_pulang then 'TIDAK ABSEN MASUK/KELUAR'
										else 'TIDAK ABSEN MASUK/KELUAR'
										end as ketsts,
										case when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is not null then 'CUTI' 
										when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null then 'ALPHA'
										else 'TIDAK ADA DOKUMEN'
										end as ketcuti,
										case when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is not null then 'IJIN' 
										when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is null then 'ALPHA'
										else 'TIDAK ADA DOKUMEN'
										end as ketijin,
										c.nodok as nodokcuti,d.nodok as nodokijin,
										a.* from sc_trx.transready a
										left outer join sc_mst.karyawan b on a.nik=b.nik
										left outer join sc_trx.cuti_karyawan c on a.nik=c.nik and a.tgl between c.tgl_mulai and c.tgl_selesai
										left outer join sc_trx.ijin_karyawan d on a.nik=d.nik and a.tgl=d.tgl_kerja 
										where a.nik='$nik' and a.tgl between '$tgl1' and '$tgl2'
										order by tgl asc
								");


	}

	function q_transready_koreksi_old($nik,$tgl1,$tgl2){
		return $this->db->query("select b.nmlengkap, 
												case 
												when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>=jam_masuk_min  then 'TIDAK TERLAMBAT'
												when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when jam_masuk_absen is null and jam_pulang_absen is null then 'TIDAK MASUK KERJA'
												when shiftke='2' and jam_masuk=jam_pulang then 'TIDAK ABSEN MASUK/KELUAR'
												else 'TIDAK ABSEN MASUK/KELUAR'
												end as ketsts,
												a.* from sc_trx.transready a
												left outer join sc_mst.karyawan b on a.nik=b.nik
												where a.nik='$nik' and a.tgl between '$tgl1' and '$tgl2'
												order by tgl asc					
								");


	}

	function q_transready_koreksidept($kddept,$tgl1,$tgl2){
		return $this->db->query("select b.nmlengkap, 
												case 
												when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>=jam_masuk_min  then 'TIDAK TERLAMBAT'
												when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when jam_masuk_absen is null and jam_pulang_absen is null then 'TIDAK MASUK KERJA'
												when shiftke='2' and jam_masuk=jam_pulang then 'TIDAK ABSEN MASUK/KELUAR'
												else 'TIDAK ABSEN MASUK/KELUAR'
												end as ketsts,
												case when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is not null then 'CUTI' 
												when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null and d.nodok is null and e.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketcuti,
												case when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is not null then 'IJIN' 
												when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null and d.nodok is null and e.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketijin,
												case when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is null and e.nodok is not null then 'DINAS' 
												when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null and d.nodok is null and e.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketdinas,
												c.nodok as nodokcuti,d.nodok as nodokijin,e.nodok as nodokdinas,c.keterangan as ketercuti,d.keterangan as keterijin,e.keperluan as keterdinas,
												a.* from sc_trx.transready a
												left outer join sc_mst.karyawan b on a.nik=b.nik
												left outer join sc_trx.cuti_karyawan c on a.nik=c.nik and a.tgl between c.tgl_mulai and c.tgl_selesai
												left outer join sc_trx.ijin_karyawan d on a.nik=d.nik and a.tgl=d.tgl_kerja
												left outer join sc_trx.dinas e on a.nik=e.nik and a.tgl between e.tgl_mulai and e.tgl_selesai	
												where b.bag_dept='$kddept' and b.statuskepegawaian<>'KO' and a.tgl between '$tgl1' and '$tgl2'
												order by tgl,nmlengkap asc						
								");


	}

	function q_show_edit($id){
		return $this->db->query("select b.nmlengkap, 
												case 
												when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>=jam_masuk_min  then 'TIDAK TERLAMBAT'
												when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when jam_masuk_absen is null and jam_pulang_absen is null then 'TIDAK MASUK KERJA'
												when shiftke='2' and jam_masuk=jam_pulang then 'TIDAK ABSEN MASUK/KELUAR'
												else 'TIDAK ABSEN MASUK/KELUAR'
												end as ketsts,
												case when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is not null then 'CUTI' 
												when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketcuti,
												case when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is not null then 'IJIN' 
												when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketijin,
												c.nodok as nodokcuti,d.nodok as nodokijin,
												a.* from sc_trx.transready a
												left outer join sc_mst.karyawan b on a.nik=b.nik
												left outer join sc_trx.cuti_karyawan c on a.nik=c.nik and a.tgl between c.tgl_mulai and c.tgl_selesai
												left outer join sc_trx.ijin_karyawan d on a.nik=d.nik and a.tgl=d.tgl_kerja 
												where a.id='$id'
												order by tgl asc					
								");


	}


	function q_transready($kanwil,$tglawal,$tglakhir,$ketsts){
		return $this->db->query("select x.*,y.uraian as keterangan from (
								select a.nik,b.nmlengkap,a.tgl,a.kdjamkerja,a.kdregu,a.nodok,a.tgl_mulai,a.tgl_selesai,a.kdpokok,a.masuk,a.keluar,
									case when kdpokok='CB' OR kdpokok='CT' then 'CT'
									when kdpokok='CK' then 'CK'
									when kdpokok='DN' then 'DN'
									when kdpokok='AL' then 'AL'
									when a.masuk<c.jam_masuk and a.keluar>=c.jam_pulang then 'TE'
									when a.masuk>c.jam_masuk and a.keluar>=c.jam_pulang then 'TA'
									when a.masuk<c.jam_masuk and a.keluar<=c.jam_pulang then 'TD'
									when a.masuk>c.jam_masuk and a.keluar<=c.jam_pulang then 'TF'
									end as docref 
									from sc_trx.listlinkjadwalcuti a
									left outer join sc_mst.karyawan b on a.nik=b.nik
									left outer join sc_mst.jam_kerja c on a.kdpokok=c.kdjam_kerja
									where a.nik in (select nik from sc_mst.karyawan where tglkeluarkerja is null and kdcabang='$kanwil') and ( to_char(a.tgl,'yyyy-mm-dd') between '$tglawal' and '$tglakhir')
								order by nmlengkap,tgl) as x left outer join sc_mst.trxtype y on x.docref=y.kdtrx and (y.jenistrx='ABSEN' OR y.jenistrx='PRESENT') where x.tgl is not null $ketsts order by nmlengkap
								");


	}

	function q_transready_regu($kdregu,$tglawal,$tglakhir,$ketsts){
		return $this->db->query("select x.*,y.uraian as keterangan from (
								select a.nik,b.nmlengkap,a.tgl,a.kdjamkerja,a.kdregu,a.nodok,a.tgl_mulai,a.tgl_selesai,a.kdpokok,a.masuk,a.keluar,
									case when kdpokok='CB' OR kdpokok='CT' then 'CT'
									when kdpokok='CK' then 'CK'
									when kdpokok='DN' then 'DN'
									when kdpokok='AL' then 'AL'
									when a.masuk<c.jam_masuk and a.keluar>=c.jam_pulang then 'TE'
									when a.masuk>c.jam_masuk and a.keluar>=c.jam_pulang then 'TA'
									when a.masuk<c.jam_masuk and a.keluar<=c.jam_pulang then 'TD'
									when a.masuk>c.jam_masuk and a.keluar<=c.jam_pulang then 'TF'
									end as docref 
									from sc_trx.listlinkjadwalcuti a
									left outer join sc_mst.karyawan b on a.nik=b.nik
									left outer join sc_mst.jam_kerja c on a.kdpokok=c.kdjam_kerja
									where a.nik in (select nik from sc_mst.karyawan where tglkeluarkerja is null and kdregu='$kdregu') and ( to_char(a.tgl,'yyyy-mm-dd') between '$tglawal' and '$tglakhir')
								order by nmlengkap,tgl) as x left outer join sc_mst.trxtype y on x.docref=y.kdtrx and (y.jenistrx='ABSEN' OR y.jenistrx='PRESENT') where x.tgl is not null $ketsts order by nmlengkap								
								");


	}

	function q_transready_dept($kddept,$tglawal,$tglakhir,$ketsts){
		return $this->db->query("select x.*,y.uraian as keterangan from (
								select a.nik,b.nmlengkap,a.tgl,a.kdjamkerja,a.kdregu,a.nodok,a.tgl_mulai,a.tgl_selesai,a.kdpokok,a.masuk,a.keluar,
									case when kdpokok='CB' OR kdpokok='CT' then 'CT'
									when kdpokok='CK' then 'CK'
									when kdpokok='DN' then 'DN'
									when kdpokok='AL' then 'AL'
									when a.masuk<c.jam_masuk and a.keluar>=c.jam_pulang then 'TE'
									when a.masuk>c.jam_masuk and a.keluar>=c.jam_pulang then 'TA'
									when a.masuk<c.jam_masuk and a.keluar<=c.jam_pulang then 'TD'
									when a.masuk>c.jam_masuk and a.keluar<=c.jam_pulang then 'TF'
									end as docref 
									from sc_trx.listlinkjadwalcuti a
									left outer join sc_mst.karyawan b on a.nik=b.nik
									left outer join sc_mst.jam_kerja c on a.kdpokok=c.kdjam_kerja
									where a.nik in (select nik from sc_mst.karyawan where tglkeluarkerja is null and bag_dept='$kddept') and ( to_char(a.tgl,'yyyy-mm-dd') between '$tglawal' and '$tglakhir')
								order by nmlengkap,tgl) as x left outer join sc_mst.trxtype y on x.docref=y.kdtrx and (y.jenistrx='ABSEN' OR y.jenistrx='PRESENT') where x.tgl is not null $ketsts order by nmlengkap								
								");


	}

	function cek_absenexist($nik,$tgl,$kdjamkerja){

		return $this->db->query("select * from sc_trx.transready where nik='$nik' and tgl='$tgl' and kdjamkerja='$kdjamkerja'");
	}

	function tglakhir_ci($kdcabang){
		return $this->db->query("select to_char(max(checktime),'DD-MM-YYYY')as lastdate from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.idabsen
								where b.kdcabang='$kdcabang'");
		}

	function tglakhir_tr($kdcabang){
		return $this->db->query("select to_char(max(tgl),'DD-MM-YYYY')as lastdate from sc_trx.transready a
								left outer join sc_mst.karyawan b on a.badgenumber=b.idabsen
								where b.kdcabang='$kdcabang'");

	}

	function q_prreportabsensi($bln,$thn){
		return $this->db->query("select sc_trx.pr_reportabsen('$bln','$thn')");
	}

	function excel_reportabsensi($bln,$thn){
		return $this->db->query("select a.*,b.nmlengkap,c.nmdept,d.nmjabatan from sc_trx.report_absen a 
									left outer join sc_mst.karyawan b on a.nik=b.nik
									left outer join sc_mst.departmen c on b.bag_dept=c.kddept
									left outer join sc_mst.jabatan d on b.jabatan=d.kdjabatan and b.bag_dept=d.kddept
									where bulan='$bln' and tahun='$thn' order by b.nmlengkap asc");
	}

	/*ABSENSI UANG MAKAN*/
	function q_idfinger(){
		return $this->db->query("select * from sc_mst.fingerprint");
	}

	function q_absensi_um($branch,$awal,$akhir){
			return $this->db->query("select ipaddress,nmlengkap,badgenumber,checkdate,checkin,checkout,hari,uangmakan,deskripsi,departement,
										case	
											when checkin<'08:00:00' and checkout>'16:00:00' and checkout<'17:00:00' and hari<>'Sabtu' then 'Tepat Waktu'
											when checkin<'08:00:00' and checkout>'13:00:00' and hari='Sabtu' then 'Tepat Waktu'
											when checkin>='08:00:00' and checkout>'13:00:00' and hari='Sabtu' and noijin is null then 'Telat Masuk'
											when checkin>'08:00:00' and checkout>'13:00:00' and hari='Sabtu' and left(noijin,2)='IK' then 'Ijin Khusus Keluar'
											when checkin>'08:00:00' and checkout>'13:00:00' and hari='Sabtu' and left(noijin,2)='PA' then 'Ijin Pulang Awal'
											when checkin>'08:00:00' and checkout>'13:00:00' and hari='Sabtu' and left(noijin,2)='DT' then 'Ijin Datang Terlambat'
											when checkin<'08:00:00' and checkout>'11:00:00' and checkout<'13:00:00' and hari='Sabtu' then 'Tepat Waktu & Pulang Awal'
											when checkin<'08:00:00' and checkout>'16:00:00' and hari<>'Sabtu' then 'Loyalitas'
											when checkin>='08:00:00' and checkout>'16:00:00' and hari<>'Sabtu' and noijin is null then 'Telat Masuk'
											when checkin>'08:00:00' and checkout>'16:00:00' and hari<>'Sabtu' and left(noijin,2)='IK' then 'Ijin Khusus Keluar'
											when checkin>'08:00:00' and checkout>'16:00:00' and hari<>'Sabtu' and left(noijin,2)='PA' then 'Ijin Pulang Awal'
											when checkin>'08:00:00' and checkout>'16:00:00' and hari<>'Sabtu' and left(noijin,2)='DT' then 'Ijin Datang Terlambat'
											when checkin<'08:00:00' and checkout<'16:00:00' and hari<>'Sabtu' and noijin is null then 'Pulang Awal'
											when checkin<'08:00:00' and checkout<'16:00:00' and hari<>'Sabtu' and left(noijin,2)='IK'  then 'Ijin Khusus Keluar'
											when checkin<'08:00:00' and checkout<'16:00:00' and hari<>'Sabtu' and left(noijin,2)='PA'  then 'Ijin Pulang Awal'
											when checkin<'08:00:00' and checkout<'16:00:00' and hari<>'Sabtu' and left(noijin,2)='DT'  then 'Ijin Datang Terlambat'
											when checkin>='08:00:00' and checkout<'16:00:00' and hari<>'Sabtu' and noijin is null then 'Telat & Pulang Awal'
											when checkin>'08:00:00' and checkout<'16:00:00' and hari<>'Sabtu' and left(noijin,2)='IK' then 'Ijin Khusus Keluar'
											when checkin>'08:00:00' and checkout<'16:00:00' and hari<>'Sabtu' and left(noijin,2)='PA' then 'Ijin Pulang Awal'
											when checkin>'08:00:00' and checkout<'16:00:00' and hari<>'Sabtu' and left(noijin,2)='DT' then 'Ijin Datang Terlambat'
											when checkin is null and checkout is null and badgenumber<>'TOTAL' and badgenumber<>'GRAND TOTAL' then 'Tidak Ceklog'
											when checkin is null and checkout is not null and noijin is null then 'Tidak Ceklog Masuk'
											when checkin is null and checkout is not null and left(noijin,2)='IK' then 'Ijin Khusus Keluar'
											when checkin is null and checkout is not null and left(noijin,2)='PA' then 'PULANG AWAL'
											when checkin is null and checkout is not null and left(noijin,2)='DT' then 'TERLAMBAT'
											when checkin is not null and checkout is null and noijin is null then 'Tidak Ceklog Keluar'
											when checkin is not null and checkout is null and noijin is not null then 'Ijin Khusus Keluar'
											
										end as ket
										from (
										select ipaddress,nmlengkap,badgenumber,to_char(checkdate,'dd-mm-YYYY') as checkdate,checkin,checkout,
											case when to_char(checkdate, 'Dy')='Mon' then 'Senin'
												 when to_char(checkdate, 'Dy')='Tue' then 'Selasa'
												 when to_char(checkdate, 'Dy')='Wed' then 'Rabu'
												 when to_char(checkdate, 'Dy')='Thu' then 'Kamis'
												 when to_char(checkdate, 'Dy')='Fri' then 'Jumat'
												 when to_char(checkdate, 'Dy')='Sat' then 'Sabtu'
												 when to_char(checkdate, 'Dy')='Sun' then 'Minggu'
											else 'Outdate' end as hari,
											--to_char(checkdate, 'Dy') as hari,
											CASE 
											WHEN ipaddress!='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin
											WHEN ipaddress!='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin									
											WHEN ipaddress!='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin	     									
											WHEN ipaddress!='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin

											WHEN ipaddress='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin
											WHEN ipaddress='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin									
											WHEN ipaddress='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin	     									
											WHEN ipaddress='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin
											WHEN trim(kduangmkn)='D' or trim(kduangmkn)='E' or noijin is not null then c.besaran-kantin
											
											ELSE '0'
											END
											as uangmakan,
											deskripsi,departement,noijin from 
											(select t1.ipaddress,t1.nmlengkap,t1.badgenumber,t1.checkdate,sum(t1.checkin) as checkin,sum(t1.checkout) as checkout,t1.deskripsi,t1.kduangmkn,t1.departement,
											case
																			when left(d.noijin,2)='DT' and to_char(sum(t1.checkin),'HH24:MI:SS')>='13:00:00' and to_char(sum(t1.checkin),'HH24:MI:SS') is not null then cast(0 as money)
																			when left(d.noijin,2)='IK' and (select cast(to_char(jamawal,'HH24:MI:SS')as time) from sc_hrd.ijin where d.noijin=kdijin||':'||to_char(tglijin,'yymm')||id)<='11:00:00'  then cast(0 as money)
																			else cast(coalesce(cast(tt.besaran as numeric),0) as money)end as kantin,
																			case 
																			when left(d.noijin,2)='IK' and to_char(sum(t1.checkin),'HH24:MI:SS')>='08:05:00' or to_char(sum(t1.checkin),'HH24:MI:SS') is null  then null
																			when left(d.noijin,2)='PA' and to_char(sum(t1.checkin),'HH24:MI:SS')>='08:05:00' and to_char(sum(t1.checkin),'HH24:MI:SS')<='13:00:00' then null
																			else d.noijin

																			end as noijin	from 
												(select a.ipaddress,b.nmlengkap,b.badgenumber,a.checkdate,min(a.checktime) as checkin,null as checkout,
												d.deskripsi,b.kduangmkn,e.departement,b.kdcabang from sc_hrd.pegawai b
												left outer join sc_hrd.transready a  on b.badgenumber=a.badgenumber
												left outer join sc_hrd.jabatan d on d.kdjabatan=b.kdjabatan and d.kdsubdept=b.kdsubdept and b.kddept=d.kddept
												left outer join sc_hrd.departement e on e.kddept=b.kddept			
												where checkdate between '$awal' and '$akhir' and 		
												checktype='IN'
												and a.ipaddress='$branch'
												group by a.ipaddress,b.nmlengkap,b.badgenumber,a.checktype,a.checkdate,d.deskripsi,b.kduangmkn,e.departement,b.kdcabang
												union all										
												select a.ipaddress,b.nmlengkap,b.badgenumber,a.checkdate,null as checkin,
													case when to_char(checkdate, 'Dy')='Sat' then max(a.checktime)
														 when to_char(checkdate, 'Dy')<>'Sat' and checktype='OUT' then max(a.checktime)
													end as checkout,
												d.deskripsi,b.kduangmkn,e.departement,b.kdcabang from sc_hrd.pegawai b
												left outer join sc_hrd.transready a  on b.badgenumber=a.badgenumber
												left outer join sc_hrd.jabatan d on d.kdjabatan=b.kdjabatan and d.kdsubdept=b.kdsubdept and b.kddept=d.kddept
												left outer join sc_hrd.departement e on e.kddept=b.kddept			
												where checkdate between '$awal' and '$akhir' and 
												checktype<>'IN' and a.ipaddress='$branch'
												group by a.ipaddress,b.nmlengkap,b.badgenumber,a.checktype,a.checkdate,d.deskripsi,b.kduangmkn,e.departement,b.kdcabang
												order by nmlengkap,checkdate ) as t1
											left outer join sc_hrd.kantin tt on t1.kdcabang=tt.kd_cab
											--tambahan ijin----
											left outer join (select max(noijin) as noijin,nip,max(kdijin) as kdijin,tglijin,max(keterangan_ijin),nmlengkap,max(desc_kdatt),max(deskripsi),tgl,departement,badgenumber,ipaddress 
												from (select  a.kdijin||':'||to_char(a.tglijin,'yymm')||a.id as noijin,a.nip,a.kdijin,a.tglijin,a.keterangan_ijin,b.nmlengkap,c.desc_kdatt,d.deskripsi,to_char(tglijin,'dd-mm-yyyy') as tgl,e.departement,b.badgenumber,f.ipaddress
													from sc_hrd.ijin a
													left outer join sc_hrd.pegawai b on a.nip=b.nip
													left outer join sc_hrd.kodeatt c on c.kdatt=a.kdijin
													left outer join sc_hrd.jabatan d on d.kdjabatan=b.kdjabatan and b.kdsubdept=d.kdsubdept
													left outer join sc_hrd.departement e on e.kddept=b.kddept
													left outer join sc_hrd.user_finger f on b.badgenumber=f.id
													where ((a.kdijin='IK' and to_char(jamakhir,'HH24:MI:SS')>='16:00:00' and to_char(jamawal,'HH24:MI:SS')>='08:00:00' )  or kdijin='PA' or kdijin='DT') and a.status<>'D' and a.status<>'C'
													order by a.tglijin desc) as t10
												group by nip,tglijin,nmlengkap,tgl,departement,badgenumber,ipaddress) d on d.badgenumber=t1.badgenumber and d.tglijin=t1.checkdate
											group by t1.ipaddress,t1.nmlengkap,t1.badgenumber,t1.checkdate,t1.kduangmkn,t1.deskripsi,t1.departement,tt.besaran,d.noijin) as t2
											--end tambahan ijin---
											--group by ipaddress,t1.nmlengkap,t1.badgenumber,t1.checkdate,t1.kduangmkn,t1.deskripsi,t1.departement,tt.besaran,d.noijin) as t2
										left outer join sc_hrd.uangmakan c on c.kdjabatan=t2.kduangmkn
										--order by nmlengkap
										union all
										--total
										select null as ipaddress,ttl.nmlengkap,'TOTAL' as badgenumber,null as checkdate, null as checktin, null as checkout, null as hari,sum(uangmakan),cast(count(nmlengkap)as character varying), null as departement,null as noijin from
											(
											select nmlengkap,t2.badgenumber,checkdate,checkin,checkout,
												CASE 
											WHEN ipaddress!='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin
											WHEN ipaddress!='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin									
											WHEN ipaddress!='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin	     									
											WHEN ipaddress!='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin

											WHEN ipaddress='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin
											WHEN ipaddress='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin									
											WHEN ipaddress='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin	     									
											WHEN ipaddress='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin
											WHEN trim(kduangmkn)='D' or trim(kduangmkn)='E' or noijin is not null then c.besaran-kantin
											
											ELSE '0'
													END
													as uangmakan,		
												deskripsi,departement,noijin from 
												(

												select t1.ipaddress,t1.nmlengkap,t1.badgenumber,t1.checkdate,sum(t1.checkin) as checkin,sum(t1.checkout) as checkout,t1.deskripsi,t1.kduangmkn,t1.departement,
											case
																			when left(d.noijin,2)='DT' and to_char(sum(t1.checkin),'HH24:MI:SS')>='13:00:00' and to_char(sum(t1.checkin),'HH24:MI:SS') is not null then cast(0 as money)
																			when left(d.noijin,2)='IK' and (select cast(to_char(jamawal,'HH24:MI:SS')as time) from sc_hrd.ijin where d.noijin=kdijin||':'||to_char(tglijin,'yymm')||id)<='11:00:00' then cast(0 as money)
																			else cast(coalesce(cast(tt.besaran as numeric),0) as money) end as kantin,
																			case 
																			when left(d.noijin,2)='IK' and to_char(sum(t1.checkin),'HH24:MI:SS')>='08:05:00' or to_char(sum(t1.checkin),'HH24:MI:SS') is null  then null
																			when left(d.noijin,2)='PA' and to_char(sum(t1.checkin),'HH24:MI:SS')>='08:05:00' and to_char(sum(t1.checkin),'HH24:MI:SS')<='13:00:00' then null
																			else d.noijin

																			end as noijin	from 
												(select a.ipaddress,b.nmlengkap,b.badgenumber,a.checkdate,min(a.checktime) as checkin,null as checkout,
												d.deskripsi,b.kduangmkn,e.departement,b.kdcabang from sc_hrd.pegawai b
												left outer join sc_hrd.transready a  on b.badgenumber=a.badgenumber
												left outer join sc_hrd.jabatan d on d.kdjabatan=b.kdjabatan and d.kdsubdept=b.kdsubdept and b.kddept=d.kddept
												left outer join sc_hrd.departement e on e.kddept=b.kddept			
												where checkdate between '$awal' and '$akhir' and 		
												checktype='IN'
												and a.ipaddress='$branch'
												group by a.ipaddress,b.nmlengkap,b.badgenumber,a.checktype,a.checkdate,d.deskripsi,b.kduangmkn,e.departement,b.kdcabang
												union all										
												select a.ipaddress,b.nmlengkap,b.badgenumber,a.checkdate,null as checkin,
													case when to_char(checkdate, 'Dy')='Sat' then max(a.checktime)
														 when to_char(checkdate, 'Dy')<>'Sat' and checktype='OUT' then max(a.checktime)
													end as checkout,
												d.deskripsi,b.kduangmkn,e.departement,b.kdcabang from sc_hrd.pegawai b
												left outer join sc_hrd.transready a  on b.badgenumber=a.badgenumber
												left outer join sc_hrd.jabatan d on d.kdjabatan=b.kdjabatan and d.kdsubdept=b.kdsubdept and b.kddept=d.kddept
												left outer join sc_hrd.departement e on e.kddept=b.kddept			
												where checkdate between '$awal' and '$akhir' and 
												checktype<>'IN' and a.ipaddress='$branch'
												group by a.ipaddress,b.nmlengkap,b.badgenumber,a.checktype,a.checkdate,d.deskripsi,b.kduangmkn,e.departement,b.kdcabang
												order by nmlengkap,checkdate
												) as t1
											left outer join sc_hrd.kantin tt on t1.kdcabang=tt.kd_cab
											--tambahan ijin----
											left outer join (select max(noijin) as noijin,nip,max(kdijin) as kdijin,tglijin,max(keterangan_ijin),nmlengkap,max(desc_kdatt),max(deskripsi),tgl,departement,badgenumber,ipaddress 
												from (select  a.kdijin||':'||to_char(a.tglijin,'yymm')||a.id as noijin,a.nip,a.kdijin,a.tglijin,a.keterangan_ijin,b.nmlengkap,c.desc_kdatt,d.deskripsi,to_char(tglijin,'dd-mm-yyyy') as tgl,e.departement,b.badgenumber,f.ipaddress
													from sc_hrd.ijin a
													left outer join sc_hrd.pegawai b on a.nip=b.nip
													left outer join sc_hrd.kodeatt c on c.kdatt=a.kdijin
													left outer join sc_hrd.jabatan d on d.kdjabatan=b.kdjabatan and b.kdsubdept=d.kdsubdept
													left outer join sc_hrd.departement e on e.kddept=b.kddept
													left outer join sc_hrd.user_finger f on b.badgenumber=f.id
													where ((a.kdijin='IK' and to_char(jamakhir,'HH24:MI:SS')>='16:00:00' and to_char(jamawal,'HH24:MI:SS')>='08:00:00' ) or kdijin='PA' or kdijin='DT') and a.status<>'D' and a.status<>'C'
													order by a.tglijin desc) as t10
												group by nip,tglijin,nmlengkap,tgl,departement,badgenumber,ipaddress) d on d.badgenumber=t1.badgenumber and d.tglijin=t1.checkdate
											group by t1.ipaddress,t1.nmlengkap,t1.badgenumber,t1.checkdate,t1.kduangmkn,t1.deskripsi,t1.departement,tt.besaran,d.noijin
											--end tambahan ijin---
											) as t2
											left outer join sc_hrd.uangmakan c on c.kdjabatan=t2.kduangmkn
											) as ttl
										group by nmlengkap,badgenumber
										--grand total
										union all
										
										select null as ipaddress,null as nmlengkap,'GRAND TOTAL' as badgenumber,null as checkdate, null as checktin, null as checkout, null as hari, sum(uangmakan),cast(count(nmlengkap)as character varying), null as departement, 
										null as noijin from
											(select ipaddress,nmlengkap,badgenumber,checkdate,checkin,checkout,
												CASE 
												WHEN ipaddress!='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin
												WHEN ipaddress!='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin
												WHEN ipaddress!='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin									
												WHEN ipaddress!='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin	     									
												WHEN ipaddress!='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:05:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin

												WHEN ipaddress='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin
												WHEN ipaddress='192.168.2.167' and deskripsi='SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin									
												WHEN ipaddress='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'13:00:00' and to_char(checkdate, 'Dy')<>'Sat' then c.besaran-kantin	     									
												WHEN ipaddress='192.168.2.167' and deskripsi<>'SALES REPRESNTATIF' and checkin<'08:10:00' and checkout>'11:00:00' and to_char(checkdate, 'Dy')='Sat' then c.besaran-kantin
												WHEN trim(kduangmkn)='D' or trim(kduangmkn)='E' or noijin is not null then c.besaran-kantin
												ELSE '0'
														END
														as uangmakan,		
												deskripsi,departement from 

												(select t1.ipaddress,t1.nmlengkap,t1.badgenumber,t1.checkdate,sum(t1.checkin) as checkin,sum(t1.checkout) as checkout,t1.deskripsi,t1.kduangmkn,t1.departement,

																			case
																			when left(d.noijin,2)='DT' and to_char(sum(t1.checkin),'HH24:MI:SS')>='13:00:00' and to_char(sum(t1.checkin),'HH24:MI:SS') is not null then cast(0 as money)
																			when left(d.noijin,2)='IK' and (select cast(to_char(jamawal,'HH24:MI:SS')as time) from sc_hrd.ijin where d.noijin=kdijin||':'||to_char(tglijin,'yymm')||id)<='11:00:00' then cast(0 as money)
																			else cast(coalesce(cast(tt.besaran as numeric),0) as money) end as kantin,
																			case 
																			when left(d.noijin,2)='IK' and to_char(sum(t1.checkin),'HH24:MI:SS')>='08:05:00' or to_char(sum(t1.checkin),'HH24:MI:SS') is null  then null
																			when left(d.noijin,2)='PA' and to_char(sum(t1.checkin),'HH24:MI:SS')>='08:05:00' and to_char(sum(t1.checkin),'HH24:MI:SS')<='13:00:00' then null
																			else d.noijin

																			end as noijin from 
																				(

													select a.ipaddress,b.nmlengkap,b.badgenumber,a.checkdate,min(a.checktime) as checkin,null as checkout,
													d.deskripsi,b.kduangmkn,e.departement,b.kdcabang from sc_hrd.pegawai b
													left outer join sc_hrd.transready a  on b.badgenumber=a.badgenumber
													left outer join sc_hrd.jabatan d on d.kdjabatan=b.kdjabatan and d.kdsubdept=b.kdsubdept and b.kddept=d.kddept
													left outer join sc_hrd.departement e on e.kddept=b.kddept			
													where checkdate between '$awal' and '$akhir' and 		
													checktype='IN'
													and a.ipaddress='$branch' 
													group by ipaddress,b.nmlengkap,b.badgenumber,a.checktype,a.checkdate,d.deskripsi,b.kduangmkn,e.departement,b.kdcabang 
												union all							
												select a.ipaddress,b.nmlengkap,b.badgenumber,a.checkdate,null as checkin,
													case when to_char(checkdate, 'Dy')='Sat' then max(a.checktime)
														 when to_char(checkdate, 'Dy')<>'Sat' and checktype='OUT' then max(a.checktime)
													end as checkout,
												d.deskripsi,b.kduangmkn,e.departement,b.kdcabang from sc_hrd.pegawai b
												left outer join sc_hrd.transready a  on b.badgenumber=a.badgenumber
												left outer join sc_hrd.jabatan d on d.kdjabatan=b.kdjabatan and d.kdsubdept=b.kdsubdept and b.kddept=d.kddept
												left outer join sc_hrd.departement e on e.kddept=b.kddept			
												where checkdate between '$awal' and '$akhir' and 
												checktype<>'IN' and a.ipaddress='$branch' 
												group by a.ipaddress,b.nmlengkap,b.badgenumber,a.checktype,a.checkdate,d.deskripsi,b.kduangmkn,e.departement,b.kdcabang
												order by nmlengkap,checkdate ) as t1 
											left outer join sc_hrd.kantin tt on t1.kdcabang=tt.kd_cab
											--tambahan ijin----
											left outer join (select max(noijin) as noijin,nip,max(kdijin) as kdijin,tglijin,max(keterangan_ijin),nmlengkap,max(desc_kdatt),max(deskripsi),tgl,departement,badgenumber,ipaddress 
												from (select  a.kdijin||':'||to_char(a.tglijin,'yymm')||a.id as noijin,a.nip,a.kdijin,a.tglijin,a.keterangan_ijin,b.nmlengkap,c.desc_kdatt,d.deskripsi,to_char(tglijin,'dd-mm-yyyy') as tgl,e.departement,b.badgenumber,f.ipaddress
														from sc_hrd.ijin a
														left outer join sc_hrd.pegawai b on a.nip=b.nip
														left outer join sc_hrd.kodeatt c on c.kdatt=a.kdijin
														left outer join sc_hrd.jabatan d on d.kdjabatan=b.kdjabatan and b.kdsubdept=d.kdsubdept
														left outer join sc_hrd.departement e on e.kddept=b.kddept
														left outer join sc_hrd.user_finger f on b.badgenumber=f.id
														where ((a.kdijin='IK' and to_char(jamakhir,'HH24:MI:SS')>='16:00:00' and to_char(jamawal,'HH24:MI:SS')>='08:00:00' ) or a.kdijin='PA' or kdijin='DT') and a.status<>'D' and a.status<>'C' 
														order by a.tglijin desc) as t10
												group by nip,tglijin,nmlengkap,tgl,departement,badgenumber,ipaddress) d on d.badgenumber=t1.badgenumber and d.tglijin=t1.checkdate
											
											--end tambahan ijin---
											group by t1.ipaddress,t1.nmlengkap,t1.badgenumber,t1.checkdate,t1.kduangmkn,t1.deskripsi,t1.departement,tt.besaran,noijin) as t2

											left outer join sc_hrd.uangmakan c on c.kdjabatan=t2.kduangmkn) as ttl
										order by nmlengkap,checkdate
										) as um		");

	}

	function q_kanwil(){
		return $this->db->query("select * from sc_mst.kantorwilayah order by desc_cabang");
	}


	function q_dblink_option($param){
	    return $this->db->query("select * from sc_mst.option_dblink where dblink_id='CRMDEV' and branch='SBYNSA' $param");
    }

    function tglakhir_mobile($kdcabang){
        return $this->db->query("select to_char(max(checktime),'DD-MM-YYYY')as lastdate from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.idabsen
								where b.kdcabang='$kdcabang'");
    }

    function read_dblink($host,$dbname,$dbuser,$dbpass){
	    return $this->db->query("select * from dblink(
                                       'hostaddr=$host dbname=$dbname user=$dbuser password=$dbpass',
                                       'select userid,usersname,nik,checktime from sc_trx.v_checkin_mobile')
                                    as t1(
                                      userid character(20),
                                      usersname character(20),
                                      nik character(20),
                                      checktime timestamp without time zone
                                    );");
    }

    function read_dblink_join_karyawan($host,$dbname,$dbuser,$dbpass,$tgl1,$tgl2,$kdcabang){
        return $this->db->query("select * from (select a.*,b.idabsen,b.kdcabang,b.nmlengkap from 
                                        (select * from dblink(
                                            'hostaddr=$host dbname=$dbname user=$dbuser password=$dbpass',
                                           'select userid,usersname,nik,checktime from sc_trx.v_checkin_mobile')
                                        as t1(
                                          userid character(20),
                                          usersname character(20),
                                          nik character(20),
                                          checktime timestamp without time zone
                                        )) a left outer join sc_mst.karyawan b on a.nik=b.nik) as x where userid is not null and 
                                        to_char(checktime,'yyyy-mm-dd HH24:mi:ss') between '$tgl1' and '$tgl2' and kdcabang='$kdcabang'
                                    ORDER BY usersname,checktime desc
                                    ");
    }

    function read_dblink_join_karyawan_mabs($host,$dbname,$dbuser,$dbpass,$tgl1,$tgl2,$kdcabang){
        return $this->db->query("_select * from (select a.*,b.idabsen,b.kdcabang,b.nmlengkap from 
                                        (select * from dblink(
                                            'hostaddr=$host dbname=$dbname user=$dbuser password=$dbpass',
                                           'select userid,usersname,nik,checktime from sc_trx.v_checkin_mobile_v2')
                                        as t1(
                                          userid character(20),
                                          usersname character(20),
                                          nik character(20),
                                          checktime timestamp without time zone
                                        )) a left outer join sc_mst.karyawan b on a.nik=b.nik) as x where userid is not null and 
                                        to_char(checktime,'yyyy-mm-dd HH24:mi:ss') between '$tgl1' and '$tgl2' and kdcabang='$kdcabang'
                                    ORDER BY usersname,checktime desc
                                    ");
    }

}
