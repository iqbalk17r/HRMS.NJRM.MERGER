<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends MX_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('m_absensi','master/m_akses'));
		$this->load->library(array('form_validation','template','upload','pdf','Excel_generator'));
		if(!$this->session->userdata('nik')){
            redirect('dashboard');
        }
	}

	function filter_absensi(){
		$data['title']='Tarikan Data Mesin Absensi'; //aksesconvert absensi
		$kmenu='I.T.B.6';
		if($this->uri->segment(4)=="success"){
				$data['message']="<div class='alert alert-success'>Data Berhasil Di Simpan </div>";
		} elseif ($this->uri->segment(4)=="exist"){
			$data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
		}
		else {
			$data['message']='';
		}
		$nama=$this->session->userdata('nik');
		$data['akses']=$this->m_akses->list_aksespermenu($nama,$kmenu)->row_array();
		$data['list_kanwil']=$this->m_absensi->q_kanwil()->result();
		$this->template->display('trans/absensi/v_filterabsensi',$data);
	}

	function ajax_tglakhir_ci($kdcabang){
		$data = $this->m_absensi->tglakhir_ci($kdcabang)->row_array();
		echo json_encode($data);
	}

	function filter_lembur(){
		$data['title']='Filter Data Lembur';
		$this->template->display('trans/absensi/v_filterlembur',$data);
	}

	function filter_koreksi(){
		if($this->uri->segment(4)=="success"){
				$data['message']="<div class='alert alert-success'>Data Berhasil di update </div>";
		} elseif ($this->uri->segment(4)=="exist"){
			$data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
		}
		else {
			$data['message']='';
		}

		$kmenu='I.T.B.7'; //aksesupdate koreksi absensi
		$nama=$this->session->userdata('nik');
		$data['akses']=$this->m_akses->list_aksespermenu($nama,$kmenu)->row_array();
		$data['title']='Filter Koreksi Data Absensi';
		$data['list_karyawan']=$this->m_absensi->q_karyawan()->result();
		$data['list_regu']=$this->m_absensi->q_regu()->result();
		$data['list_dept']=$this->m_absensi->q_department()->result();

		$this->template->display('trans/absensi/v_filterkoreksi',$data);
	}

	function filter_detail(){
		$kmenu='I.T.B.9'; //aksesview dan akses download absensi
		$nama=$this->session->userdata('nik');
		$data['akses']=$this->m_akses->list_aksespermenu($nama,$kmenu)->row_array();
		$data['title']='Filter Detail Data Absensi';
		$data['list_karyawan']=$this->m_absensi->q_karyawan()->result();
		$data['list_kanwil']=$this->m_absensi->q_kanwil()->result();
		$data['list_regu']=$this->m_absensi->q_regu()->result();
		$data['list_dept']=$this->m_absensi->q_department()->result();
		$data['list_trxabsen']=$this->m_absensi->q_trxabsen()->result();
		$this->template->display('trans/absensi/v_filterdtlabsensi',$data);
	}

	function filter_input(){
		if($this->uri->segment(4)=="kode_failed")
            $data['message']="<div class='alert alert-warning'>Absensi Gagal Disimpan</div>";
        else if($this->uri->segment(4)=="rep_succes")
			$data['message']="<div class='alert alert-success'>Absensi Sukses Disimpan </div>";
        else
            $data['message']='';
		$data['title']='DETAIL SIMPAN TRANSREADY';
		$kmenu='I.T.B.10'; //akses view transready
		$nama=$this->session->userdata('nik');
		$data['akses']=$this->m_akses->list_aksespermenu($nama,$kmenu)->row_array();
		$data['list_karyawan']=$this->m_absensi->q_karyawan()->result();
		$data['list_kanwil']=$this->m_absensi->q_kanwil()->result();
		//$tglakhir1=$this->m_absensi->tglakhir_tr()->row_array();
		//$tglakhir=$tglakhir1['tglakhir'];
		//$data['tglakhir']=$tglakhir;
		$this->template->display('trans/absensi/v_filtertarik',$data);
	}

	function ajax_tglakhir_tr($kdcabang){
		$data = $this->m_absensi->tglakhir_tr($kdcabang)->row_array();
		echo json_encode($data);
	}

	public function index()
	{
		$tanggal=$this->input->post('tgl');
		$kdcabang=$this->input->post('kdcabang');
		$tgl=explode(' - ',$tanggal);
		$tgla=$tgl[0].' 00:00:00';
		$tglb=$tgl[1].' 23:59:59';

		//echo date('m-d-Y H:i:s',strtotime($tgl1)).'<br>';
		//echo date('m-d-Y H:i:s',strtotime($tgl2));
		$tgl1=date('m-d-Y H:i:s',strtotime($tgla));
		$tgl2=date('m-d-Y H:i:s',strtotime($tglb));
		if (empty($tanggal)){
			redirect('trans/absensi/filter_absensi');
		}
		$data['title']="DATA MESIN ABSEN $tgla hingga $tglb Untuk Wilayah $kdcabang";
		$data['tgl1']=$tgla;
		$data['tgl2']=$tglb;
		$data['kdcabang']=$kdcabang;
		$data['message']='';
		if($kdcabang=='SBYMRG'){
            $data['ttldata']=$this->m_absensi->ttldata_sby($tgl1,$tgl2);
            $data['list_absen']=$this->m_absensi->show_user_sby($tgl1,$tgl2);
		} else if($kdcabang=='SMGDMK'){
				$data['ttldata']=$this->m_absensi->ttldata_dmk($tgl1,$tgl2);
				$data['list_absen']=$this->m_absensi->show_user_dmk($tgl1,$tgl2);
		} else if($kdcabang=='SMGCND'){
				$data['ttldata']=$this->m_absensi->ttldata_cnd($tgl1,$tgl2);
				$data['list_absen']=$this->m_absensi->show_user_cnd($tgl1,$tgl2);
		} else if($kdcabang=='JKTKPK'){
				$data['ttldata']=$this->m_absensi->ttldata_jkt($tgl1,$tgl2);
				$data['list_absen']=$this->m_absensi->show_user_jkt($tgl1,$tgl2);
		} else if($kdcabang=='SKHRJ'){
				$data['ttldata']=$this->m_absensi->ttldata_skhrj($tgl1,$tgl2);
				$data['list_absen']=$this->m_absensi->show_user_skhrj($tgl1,$tgl2);
		} else if($kdcabang=='JOG'){
				$data['ttldata']=$this->m_absensi->ttldata_jog($tgl1,$tgl2);
				$data['list_absen']=$this->m_absensi->show_user_jog($tgl1,$tgl2);	
		} else if($kdcabang=='RMBG'){
				$data['ttldata']=$this->m_absensi->ttldata_rmbg($tgl1,$tgl2);
				$data['list_absen']=$this->m_absensi->show_user_rmbg($tgl1,$tgl2);				
		}else { redirect('trans/absensi'); }

		$this->template->display('trans/absensi/v_absensi',$data);
	}

	public function lembur()
	{
		$tanggal=$this->input->post('tgl');
		$tgl=explode(' - ',$tanggal);
		$tgla=$tgl[0].' 00:00:00';
		$tglb=$tgl[1].' 23:59:59';
		//echo date('m-d-Y H:i:s',strtotime($tgl1)).'<br>';
		//echo date('m-d-Y H:i:s',strtotime($tgl2));
		$tgl1=date('m-d-Y H:i:s',strtotime($tgla));
		$tgl2=date('m-d-Y H:i:s',strtotime($tglb));
		if (empty($tanggal)){
			redirect('trans/absensi/filter_lembur');
		}

		$data['ttldata']=$this->m_absensi->ttldata_lembur($tgl1,$tgl2)->row_array();
		$data['title']="DATA MESIN ABSEN $tgla hingga $tglb";
		$data['tgl1']=$tgla;
		$data['tgl2']=$tglb;
		$data['message']='';
		$data['list_lembur']=$this->m_absensi->show_user_lembur($tgl1,$tgl2)->result();
		$this->template->display('trans/absensi/v_lembur',$data);
	}

	public function absen_mesin()
	{
		$tanggal=$this->input->post('tgl');
		$tgl=explode(' - ',$tanggal);
		$tgl1=$tgl[0].' 00:00:00';
		$tgl2=$tgl[1].' 23:59:59';

		if (empty($tanggal)){
			redirect('trans/absensi/filter');
		}
		$data['ttldata']=$this->m_absensi->ttldata($tgl1,$tgl2)->row_array();
		$data['title']="DATA MESIN ABSEN $tgl1 hingga $tgl2";
		$data['tgl1']=$tgl1;
		$data['tgl2']=$tgl2;
		$data['message']='';
		$data['list_absen']=$this->m_absensi->show_user($tgl1,$tgl2)->result();
		$this->template->display('trans/absensi/v_absensi',$data);
	}



	function tarik_data(){
		$tgla=$this->input->post('tgl1');
		$tglb=$this->input->post('tgl2');
		$tgl1=date('m-d-Y H:i:s',strtotime($tgla));
		$tgl2=date('m-d-Y H:i:s',strtotime($tglb));
		$tglawal=date('Y-m-d',strtotime($tgla));
		$tglakhir=date('Y-m-d',strtotime($tglb));
		$kdcabang=$this->input->post('kdcabang');

		if($kdcabang=='SBYMRG'){
				$datane=$this->m_absensi->show_user_sby($tgl1,$tgl2);
		} else if($kdcabang=='SMGDMK'){
				$datane=$this->m_absensi->show_user_dmk($tgl1,$tgl2);
		} else if($kdcabang=='SMGCND'){
				$datane=$this->m_absensi->show_user_cnd($tgl1,$tgl2);
		} else if($kdcabang=='JKTKPK'){
				$datane=$this->m_absensi->show_user_jkt($tgl1,$tgl2);
		} else if($kdcabang=='SKHRJ'){
				$datane=$this->m_absensi->show_user_skhrj($tgl1,$tgl2);
		} else if($kdcabang=='JOG'){
				$datane=$this->m_absensi->show_user_jog($tgl1,$tgl2);
		} else if($kdcabang=='RMBG'){
				$datane=$this->m_absensi->show_user_rmbg($tgl1,$tgl2);		
		} else { redirect('trans/absensi/filter'); }



		foreach ($datane as $dta){
			$badgenumber=($dta->Badgenumber);
			$userid=($dta->USERID);
			$checktime=($dta->CHECKTIME);
			$this->db->query("delete from sc_tmp.checkinout where userid='$userid' and badgenumber='$badgenumber' and checktime='$checktime'");
			$cek_fingerid=$this->m_absensi->q_cek_fingerid($badgenumber)->num_rows();
			if($cek_fingerid>0){
				$info=array(
				'userid'=>$dta->USERID,
				'badgenumber'=>$dta->Badgenumber,
				'nama'=>$dta->Name,
				'checktime'=>$dta->CHECKTIME,
				'inputan'=>'M',
				'inputby'=>$this->session->userdata('nik')
			);
				$this->m_absensi->simpan($info);
			}


		}

		//$txt='select sc_tmp.update_badgenumber('.chr(39).$tglawal.chr(39).','.chr(39).$tglakhir.chr(39).')';
		//$this->db->query($txt);
		echo json_encode(array("status" => TRUE));
	}

	function tarik_data_lembur(){
		$tgla=$this->input->post('tgl1');
		$tglb=$this->input->post('tgl2');
		$tgl1=date('m-d-Y H:i:s',strtotime($tgla));
		$tgl2=date('m-d-Y H:i:s',strtotime($tglb));
		//$tgl1='17-01-2016';
		//$tgl2='18-01-2016';
		$datane=$this->m_absensi->show_user_lembur($tgl1,$tgl2)->result();

		foreach ($datane as $dta){
			$info=array(
				'userid'=>$dta->USERID,
				'badgenumber'=>$dta->Badgenumber,
				'nama'=>$dta->Name,
				'checktime'=>$dta->CHECKTIME,
				'inputan'=>'M',
				'inputby'=>$this->session->userdata('nik')
			);
			$this->m_absensi->simpan_lembur($info);
		}
		echo json_encode(array("status" => TRUE));
	}

	function input_data(){
		$tanggal=$this->input->post('tgl');
		$kdcabang=$this->input->post('kdcabang');

		$tgl=explode(' - ',$tanggal);
		$tglawal1=$tgl[0];
		$tglakhir1=$tgl[1];
		$tglawal=date('Y-m-d',strtotime($tglawal1));
		$tglakhir=date('Y-m-d',strtotime($tglakhir1));

		/*$datane=$this->m_absensi->q_loopingjadwal($tglawal,$tglakhir)->result();


		foreach ($datane as $dta){

			$nik=$dta->nik;
			$tgl1=$dta->tgl_min_masuk;
			$tgl2=$dta->tgl_max_pulang;
			$cari_absen=$this->m_absensi->q_caricheckinout($nik,$tgl1,$tgl2)->row_array();
			if (empty($cari_absen['tgl_min']) and empty($cari_absen['tgl_max'])) {
				$jam_min=NULL;
				$jam_max=NULL;
			} else {
				$jam_min=$cari_absen['tgl_min'];
				$jam_max=$cari_absen['tgl_max'];
			}
			$info_absen=array(

				'nik'=>$dta->nik,
				'kdjamkerja'=>$dta->kdjamkerja,
				'tgl'=>$dta->tgl,

				'shiftke'=>$dta->shiftke,

				'jam_masuk'=>$dta->jam_masuk,
				'jam_masuk_min'=>$dta->jam_masuk_min,

				'jam_pulang'=>$dta->jam_pulang,
				//'jam_pulang_min'=>$dta->jam_pulang_min,
				'jam_pulang_min'=>$dta->jam_pulang_min,
				'jam_pulang_max'=>$dta->jam_pulang_max,
				'kdhari_masuk'=>$dta->kdharimasuk,
				'kdhari_pulang'=>$dta->kdharipulang,
				'jam_masuk_absen'=>$jam_min,
				'jam_pulang_absen'=>$jam_max,
			);


			$this->db->insert('sc_trx.transready',$info_absen);



		}*/
		$this->db->query("delete from sc_trx.transready where tgl between '$tglawal' and '$tglakhir' and nik in (select nik from sc_mst.karyawan where tglkeluarkerja is null and kdcabang='$kdcabang') and editan <> 't' ");
		$txt='select sc_tmp.pr_generate_transready('.chr(39).$tglawal.chr(39).','.chr(39).$tglakhir.chr(39).','.chr(39).$kdcabang.chr(39).')';
		$this->db->query($txt);
		redirect('trans/absensi/filter_input/rep_succes');
	}

	function input_data_new(){
		$tanggal=$this->input->post('tgl');

		$tgl=explode(' - ',$tanggal);
		$tglawal1=$tgl[0];
		$tglakhir1=$tgl[1];
		$tglawal=date('Y-m-d',strtotime($tglawal1));
		$tglakhir=date('Y-m-d',strtotime($tglakhir1));
		//echo $tglawal.'|'.$tglakhir;

		//$tglb=$this->input->post('tgl2');
		//$tgl1=date('m-d-Y H:i:s',strtotime($tgla));
		//$tgl2=date('m-d-Y H:i:s',strtotime($tglb));
		//$tgl1='17-01-2016';
		//$tgl2='18-01-2016';
		$datane=$this->m_absensi->q_loopingjadwal_new($tglawal,$tglakhir)->result();
		//$datane2=$this->m_absensi->q_showjadwal($tgl)->result();

		foreach ($datane as $dta){

			$nik=$dta->nik;
			$tgl1=$dta->tgl_min_masuk;
			$tgl2=$dta->tgl_max_pulang;
			$cari_absen=$this->m_absensi->q_caricheckinout($nik,$tgl1,$tgl2)->row_array();
			if (empty($cari_absen['tgl_min']) and empty($cari_absen['tgl_max'])) {
				$jam_min=NULL;
				$jam_max=NULL;
			} else {
				$jam_min=$cari_absen['tgl_min'];
				$jam_max=$cari_absen['tgl_max'];
			}
			$info_absen=array(
				//'userid'=>$dta->userid,
				//'badgenumber'=>$dta->badgenumber,
				//'checktime'=>$dta->checktime,
				'nik'=>$dta->nik,
				'kdjamkerja'=>$dta->kdjamkerja,
				'tgl'=>$dta->tgl,
				//'kdregu'=>$dta->kdregu,
				'shiftke'=>$dta->shiftke,
				//'shift'=>$dta->shift,
				'jam_masuk'=>$dta->jam_masuk,
				'jam_masuk_min'=>$dta->jam_masuk_min,
				//'jam_masuk_max'=>$dta->jam_masuk_max,
				'jam_pulang'=>$dta->jam_pulang,
				//'jam_pulang_min'=>$dta->jam_pulang_min,
				'jam_pulang_min'=>$dta->jam_pulang_min,
				'jam_pulang_max'=>$dta->jam_pulang_max,
				'kdhari_masuk'=>$dta->kdharimasuk,
				'kdhari_pulang'=>$dta->kdharipulang,
				'jam_masuk_absen'=>$jam_min,
				'jam_pulang_absen'=>$jam_max,
			);

			/*
			echo $nik.'|';
			echo $jam_min.'|';
			echo $jam_max.'|';
			echo $tgl1.'|';
			echo $tgl2.'<br>';
			*/
			$this->db->insert('sc_trx.transready',$info_absen);


			//echo $status;
			//$this->db->insert('sc_trx.transready',$info_absen);
			//$this->db->insert('sc_trx.transready',$status);

		}
		//echo 'sukses';
		redirect('trans/absensi/filter_input/rep_succes');
	}

	function update_status(){
			$data=$this->m_absensi->q_transready()->result();

				foreach ($data as $dta){
					$id=$dta->id;
					$jam=$dta->jam;
					$shiftke=$dta->shiftke;
					$jam_masuk=$dta->jam_masuk;
					$jam_masuk_min=$dta->jam_masuk_min;
					$jam_masuk_max=$dta->jam_masuk_min;
					$jam_pulang=$dta->jam_pulang;
					$jam_pulang_min=$dta->jam_pulang_min;
					$jam_pulang_max=$dta->jam_pulang_max;
					$kdhari_masuk=$dta->kdhari_masuk;
					$kdhari_pulang=$dta->kdhari_pulang;
					$tglcheck=$dta->tglcheck;
					$tgljadwal=$dta->tgljadwal;
					$checktime=$dta->checktime;

					//if ($jam>$jam_masuk_min and $jam<$jam_masuk_max) {
						//$status='STATUS MASUK';
					//}
					if ($shiftke=='3' and $checktime>$tgljadwal and $jam<$jam_pulang) {
						$status='MASUK SHIFT 3';
					} else if ($jam>$jam_masuk_min and $jam<$jam_masuk and $jam<$jam_pulang and $shiftke=='1'){
						$status='STATUS MASUK';
					} else if ($jam>$jam_masuk and $jam>$jam_pulang_min and $jam<$jam_pulang_max and $shiftke=='1'){
						$status='STATUS PULANG';
					} else if ($jam>$jam_masuk and $jam>$jam_pulang_min and $jam<$jam_pulang_max and $shiftke=='3'){
						$status='STATUS PULANG';
					} else if ($jam>$jam_masuk_min and $jam<$jam_masuk and $jam<$jam_pulang and $shiftke=='2'){
						$status='STATUS MASUK';
					} else {
						$status='ISTIRAHAT';
					}

					echo $id.'|'.$tglcheck.'|'.$jam.'Tanggal JAM CHEKIN|'.$shiftke.'<br>';
					echo $id.'|'.$jam_masuk.' JAM MASUK|'.$jam_pulang.' JAM PULANG|'.$status.'<br>';

					$info_status=array(

						'keterangan'=>$status,
					);
					$this->db->where('id',$id);
					$this->db->update('sc_trx.transready',$info_status);
				}







	}



	function koreksiabsensi(){

		$tanggal=$this->input->post('tgl');
		$tgl=explode(' - ',$tanggal);
		$tgl1=$tgl[0];
		$tgl2=$tgl[1];

		//$nama=trim($this->input->post('karyawan'));
		$nik=trim($this->input->post('karyawan'));
		if (empty($tanggal)){
			redirect('trans/absensi/filter_koreksi');
		} else {
			/*$data['title']="KOREKSI DATA ABSEN KARYAWAN";
			//echo $nama;
			//$data['list_absen']=$this->m_absensi->q_absensi_kor($tgl1,$tgl2,$nik)->result();

			$data['list_absen']=$this->m_absensi->q_transready_koreksi($nik,$tgl1,$tgl2)->result();
			$data['list_jam']=$this->m_absensi->q_jamkerja()->result();
			$data['nik']=$nik;
			$this->template->display('trans/absensi/v_koreksiabsensi',$data);*/
			$this->lihat_koreksi_kar($nik,$tgl1,$tgl2);
		}

	}

	function koreksiabsensi_dept(){

		$tanggal=$this->input->post('tgl');
		$tgl=explode(' - ',$tanggal);
		$tgl1=$tgl[0];
		$tgl2=$tgl[1];

		//$nama=trim($this->input->post('karyawan'));
		$kddept=trim($this->input->post('kddept'));
		if (empty($tanggal)){
			redirect('trans/absensi/filter_koreksi');
		} else {
			/*$data['title']="KOREKSI DATA ABSEN KARYAWAN";
			//echo $nama;
			//$data['list_absen']=$this->m_absensi->q_absensi_kor($tgl1,$tgl2,$nik)->result();

			$data['list_absen']=$this->m_absensi->q_transready_koreksidept($kddept,$tgl1,$tgl2)->result();
			$data['list_jam']=$this->m_absensi->q_jamkerja()->result();
			$data['kddept']=$kddept;
			$this->template->display('trans/absensi/v_koreksiabsensi_dept',$data);*/
			$this->lihat_koreksi($kddept,$tgl1,$tgl2);
		}

	}

	function lihat_koreksi($kddept,$tgl1,$tgl2){
			$data['title']="KOREKSI DATA ABSEN KARYAWAN";
			$data['list_absen']=$this->m_absensi->q_transready_koreksidept($kddept,$tgl1,$tgl2)->result();
			$data['list_jam']=$this->m_absensi->q_jamkerja()->result();
			$data['kddept']=$kddept;
			$data['tgl1']=$tgl1;
			$data['tgl2']=$tgl2;
			$this->template->display('trans/absensi/v_koreksiabsensi_dept',$data);

	}

	function lihat_koreksi_kar($nik,$tgl1,$tgl2){

			$data['title']="KOREKSI DATA ABSEN KARYAWAN";
			//echo $nama;
			//$data['list_absen']=$this->m_absensi->q_absensi_kor($tgl1,$tgl2,$nik)->result();

			$data['list_absen']=$this->m_absensi->q_transready_koreksi($nik,$tgl1,$tgl2)->result();
			$data['list_jam']=$this->m_absensi->q_jamkerja()->result();
			$data['nik']=$nik;
			$data['tgl1']=$tgl1;
			$data['tgl2']=$tgl2;
			$this->template->display('trans/absensi/v_koreksiabsensi',$data);

	}

	function show_edit($id,$kddept,$tgl1,$tgl2){

			$data['title']="KOREKSI DATA ABSEN KARYAWAN";
			//echo $nama;
			//$data['list_absen']=$this->m_absensi->q_absensi_kor($tgl1,$tgl2,$nik)->result();
			$data['kddept']=$kddept;
			$data['tgl1']=$tgl1;
			$data['tgl2']=$tgl2;
			$data['ld']=$this->m_absensi->q_show_edit($id)->row_array();
			$data['list_jam']=$this->m_absensi->q_jamkerja()->result();
			$this->template->display('trans/absensi/v_editkoresiabsensi_dept',$data);

	}


	function input_absensi(){
		$nik=$this->input->post('nik');
		$tgl=$this->input->post('tanggal1');
		$kdjamkerja=$this->input->post('kdjamkerja');
		//$editby=$this->input->post('editby');
		$jam_masuk=str_replace("_",0,$this->input->post('jam_masuk'));
		$jam_pulang=str_replace("_",0,$this->input->post('jam_pulang'));
		//$checktime=$tgl.' '.$jam;
			$info_absen=array(
				'nik'=>$nik,
				'tgl'=>$tgl,
				'kdjamkerja'=>$kdjamkerja,
				'jam_masuk_absen'=>$jam_masuk,
				'jam_pulang_absen'=>$jam_pulang,
				//'input_by'=>$this->session->userdata('nik'),
				//'input_date'=>date('Y-m-d H:i:s'),
			    'editan'=>'T',
			);
		$cek=$this->m_absensi->cek_absenexist($nik,$tgl,$kdjamkerja)->num_rows();
		if ($cek>0){
			redirect('trans/absensi/filter_koreksi/exist');
		} else {

			$this->db->insert('sc_trx.transready',$info_absen);
			redirect('trans/absensi/filter_koreksi/rep_succes');
		}



	}


	function detailabsensi(){
		$tanggal=$this->input->post('tgl');

		$tgl=explode(' - ',$tanggal);
		$ketsts1=$this->input->post('ketsts');
		if (!empty($ketsts1)) {
			$ketsts="and x.docref='$ketsts1'";
		} else {
			$ketsts='';
		}
		$tglawal=date('Y-m-d',strtotime($tgl[0]));
		$tglakhir=date('Y-m-d',strtotime($tgl[1]));
		$kanwil=trim($this->input->post('kanwil'));
		if (empty($tanggal) or empty($kanwil)){
			redirect('trans/absensi/filter_detail');
		} else {
			$data['title']="DETAIL DATA ABSEN PER WILAYAH";
			$data['list_absen']=$this->m_absensi->q_transready($kanwil,$tglawal,$tglakhir,$ketsts)->result();
			$this->template->display('trans/absensi/v_dtlabsensi_regu',$data);
		}

	}

	function detailabsensi_regu(){
		$tanggal=$this->input->post('tgl');

		$tgl=explode(' - ',$tanggal);
		$ketsts1=$this->input->post('ketsts');
		if (!empty($ketsts1)) {
			$ketsts="='$ketsts1'";


		} else {
			$ketsts=" is not null";

		}
		//$tgl1=$tgl[0].' 00:00:00';
		//$tgl2=$tgl[1].' 23:59:59';

		$tgl1=$tgl[0];
		$tgl2=$tgl[1];

		//$nama=trim($this->input->post('karyawan'));
		$kdregu=trim($this->input->post('kdregu'));
		if (empty($tanggal)){
			redirect('trans/absensi/filter_detail');
		} else {
			$data['title']="DETAIL DATA ABSEN KARYAWAN";
			//echo $nama;
			$data['list_absen']=$this->m_absensi->q_transready_regu($kdregu,$tgl1,$tgl2,$ketsts)->result();

			$this->template->display('trans/absensi/v_dtlabsensi_regu',$data);
		}
		//echo $ketsts1;

	}

	function detailabsensi_dept(){
		$tanggal=$this->input->post('tgl');

		$tgl=explode(' - ',$tanggal);
		$ketsts1=$this->input->post('ketsts');
		if (!empty($ketsts1)) {
			$ketsts="='$ketsts1'";


		} else {
			$ketsts=" is not null";

		}
		//$tgl1=$tgl[0].' 00:00:00';
		//$tgl2=$tgl[1].' 23:59:59';

		$tgl1=$tgl[0];
		$tgl2=$tgl[1];

		//$nama=trim($this->input->post('karyawan'));
		$kddept=trim($this->input->post('kddept'));
		if (empty($tanggal)){
			redirect('trans/absensi/filter_detail');
		} else {
			$data['title']="DETAIL DATA ABSEN KARYAWAN";
			//echo $nama;
			$data['list_absen']=$this->m_absensi->q_transready_koreksidept($kddept,$tgl1,$tgl2)->result();

			$this->template->display('trans/absensi/v_dtlabsensi',$data);
		}
		//echo $ketsts1;

	}

	function edit_absensi_old(){
		$id=$this->input->post('id');
		$tgl=$this->input->post('tanggal');
		//$editby=$this->input->post('editby');
		$jam_masuk=str_replace("_",0,$this->input->post('jam_masuk'));
		$jam_pulang=str_replace("_",0,$this->input->post('jam_pulang'));
		$checktime=$tgl.' '.$jam;
		$this->db->query("update sc_tmp.checkinout set checktime='$checktime',editan='T' where id='$id'");
		redirect('trans/absensi/filter_koreksi/add_succes');


	}

	function edit_absensi(){
		$id=$this->input->post('id');
		$tgl=$this->input->post('tanggal');
		$nik=$this->input->post('nik');
		$tgl1=$this->input->post('tgl1');
		$tgl2=$this->input->post('tgl2');
		//$editby=$this->input->post('editby');
		$jam_masuk=str_replace("_",0,$this->input->post('jam_masuk'));
		$jam_pulang=str_replace("_",0,$this->input->post('jam_pulang'));
		$checktime=$tgl.' '.$jam;
		$this->db->query("update sc_trx.transready set jam_masuk_absen='$jam_masuk',jam_pulang_absen='$jam_pulang',editan='T' where id='$id'");
		//redirect('trans/absensi/filter_koreksi/rep_succes');
		redirect("trans/absensi/lihat_koreksi_kar/$nik/$tgl1/$tgl2");


	}

	function edit_absensi_dept(){
		$id=$this->input->post('id');
		$tgl=$this->input->post('tanggal');
		$kddept=$this->input->post('kddept');
		$tgl1=$this->input->post('tgl1');
		$tgl2=$this->input->post('tgl2');
		//$editby=$this->input->post('editby');
		$jam_masuk=str_replace("_",0,$this->input->post('jam_masuk'));
		$jam_pulang=str_replace("_",0,$this->input->post('jam_pulang'));
		$checktime=$tgl.' '.$jam;
		$this->db->query("update sc_trx.transready set jam_masuk_absen='$jam_masuk',jam_pulang_absen='$jam_pulang',editan='T' where id='$id'");
		redirect("trans/absensi/lihat_koreksi/$kddept/$tgl1/$tgl2");


	}

	function hapus_absensi($id){
		$this->db->where('id',$id);
		$this->db->delete('sc_tmp.checkinout');
		redirect('trans/absensi/filter_koreksi/rep_succes');

	}

	public function ajax_edit($id)
	{
		$data = $this->m_absensi->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$data = array(
				'kdkepegawaian' => strtoupper($this->input->post('kdkepegawaian')),
				'nmkepegawaian' => strtoupper($this->input->post('nmkepegawaian')),
				'input_date' => date('d-m-Y H:i:s'),
				'input_by' => $this->session->userdata('nik'),
			);
		$insert = $this->m_absensi->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$data = array(
				'kdkepegawaian' => strtoupper($this->input->post('kdkepegawaian')),
				'nmkepegawaian' => strtoupper($this->input->post('nmkepegawaian')),
				'update_date' => date('d-m-Y H:i:s'),
				'update_by' => $this->session->userdata('nik'),
			);
		$this->m_absensi->update(array('kdkepegawaian' => $this->input->post('kdkepegawaian')), $data);
		echo json_encode(array("status" => TRUE));

		$data['message']='Update succes';
	}

	public function ajax_delete($id)
	{
		$this->m_absensi->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}

	function show_mdb(){
		$tgl1='2015-04-01';
		$tgl2='2015-04-30';
		$cek=$this->m_absensi->show_user_lembur($tgl1,$tgl2)->result();
		foreach ($cek as $ck){

			echo $ck->USERID.$ck->CHECKTIME;

		}

	}



	function delete_mdb(){
		$tgl1='2015-04-01';
		$tgl2='2015-04-30';
		$cek=$this->m_absensi->del_user_lembur($tgl1,$tgl2)->result();
		foreach ($cek as $ck){

			echo $ck->USERID.$ck->CHECKTIME;

		}

	}

		/* excel report absensi*/
	function pr_report_absensi(){
		$bln=$this->input->post('bln');
		$thn=$this->input->post('thn');
		$this->m_absensi->q_prreportabsensi($bln,$thn);
		echo json_encode(array("status" => TRUE));
	}

	function report_absensi($bln,$thn){

		$tglskr= date("Ym");
		$tglinp=$thn.$bln;
		$info=$this->m_absensi->excel_reportabsensi($bln,$thn);
        $this->excel_generator->set_query($info);
		$this->excel_generator->set_header(array('NIK','NAMALENGKAP','DEPARTMENT','JABATAN','REGU',
												'tgl1','tgl2','tgl3','tgl4','tgl5','tgl6','tgl7','tgl8','tgl9','tgl10','tgl11','tgl12','tgl13','tgl14','tgl15',
												'tgl16','tgl17','tgl18','tgl19','tgl20','tgl21','tgl22','tgl23','tgl24','tgl25','tgl26','tgl27','tgl28','tgl29','tgl30','tgl31',
												'SHIFT2','SHIFT3','ALPHA','CUTI','CUTI KHUSUS','DINAS'
												));
 	    $this->excel_generator->set_column(array('nik','nmlengkap','nmdept','nmjabatan','kdregu',
												'tgl1','tgl2','tgl3','tgl4','tgl5','tgl6','tgl7','tgl8','tgl9','tgl10','tgl11','tgl12','tgl13','tgl14','tgl15',
												'tgl16','tgl17','tgl18','tgl19','tgl20','tgl21','tgl22','tgl23','tgl24','tgl25','tgl26','tgl27','tgl28','tgl29','tgl30','tgl31',
												'shift2','shift3','alpha','cuti','ctkhusus','dinas'
												));
        $this->excel_generator->set_width(array(12,20,20,20,10,
												6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,
												6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,
												6,6,6,6,6,6
												));

		if(trim($tglskr)==trim($tglinp)){
				$this->excel_generator->exportTo2007("REPORT ABSENSI BULAN $bln TAHUN $thn sd MAX SIMPAN ABSEN");
		} else {
				$this->excel_generator->exportTo2007("REPORT ABSENSI BULAN $bln TAHUN $thn");
		}


	}

    function report_absensi_pernik(){
        $nik=$this->input->post('nik');
        $tanggal=$this->input->post('tgl');
        $tgl=explode(' - ',$tanggal);
        $tgl1=date('Y-m-d',strtotime($tgl[0]));
        $tgl2=date('Y-m-d',strtotime($tgl[1]));
        $info=$this->m_absensi->q_transready_koreksi($nik,$tgl1,$tgl2);
        $this->excel_generator->set_query($info);
        $this->excel_generator->set_header(array('NIK','NAMALENGKAP','TANGGAL','JAM_MASUK','JAM_PULANG','KETERANGAN'

        ));
        $this->excel_generator->set_column(array('nik','nmlengkap','tgl','jam_masuk_absen','jam_pulang_absen','ketsts'
        ));
        $this->excel_generator->set_width(array(12,20,20,10,10,20
        ));


        $this->excel_generator->exportTo2007("REPORT ABSENSI");


    }

    function report_absensi_perdept(){
        $kddept=$this->input->post('kddept');
        $tanggal=$this->input->post('tgl');
        $tgl=explode(' - ',$tanggal);
        $tgl1=date('Y-m-d',strtotime($tgl[0]));
        $tgl2=date('Y-m-d',strtotime($tgl[1]));
        $info=$this->m_absensi->q_transready_koreksidept($kddept,$tgl1,$tgl2);
        $this->excel_generator->set_query($info);
        $this->excel_generator->set_header(array('TANGGAL','NIK','NAMALENGKAP','JAM_MASUK','JAM_PULANG','KETERANGAN','DOKUMEN CUTI','KETERANGAN CUTI','DOKUMEN IJIN','KETERANGAN IJIN','DOKUMEN DINAS','KETERANGAN DINAS'

        ));
        $this->excel_generator->set_column(array('tgl','nik','nmlengkap','jam_masuk_absen','jam_pulang_absen','ketsts','nodokcuti','ketercuti','nodokijin','keterijin','nodokdinas','keterdinas'
        ));
        $this->excel_generator->set_width(array(12,10,30,10,10,20,20,20,20,20,20,20
        ));


        $this->excel_generator->exportTo2007("Laporan Absensi Per Department");


    }

	function uang_makan(){
		$data['title']="Laporan Absensi Uang Makan";
		$data['fingerprintwil']=$this->m_absensi->q_idfinger()->result();
		if($this->uri->segment(4)=="exist_data")
            $data['message']="<div class='alert alert-danger'>Data Sudah Ada</div>";
		else if($this->uri->segment(4)=="add_success")
			$data['message']="<div class='alert alert-success'>Input Data Sukses</div>";
		else if($this->uri->segment(4)=="fp_success")
			$data['message']="<div class='alert alert-success'>Download Data Sukses</div>";
		$this->template->display('trans/absensi/v_absen_um',$data);
	}

	function list_um(){
        $data['title']="Laporan Absensi Uang Makan";
		$data['fingerprintwil']=$this->m_absensi->q_idfinger()->result();
		$branch=$this->input->post('branch');
		$tgl=explode(' - ',$this->input->post('tgl'));
		if (empty($tgl) or empty($branch)) { redirect('trans/absensi/uang_makan'); }
		$awal=$tgl[0];
		$akhir=$tgl[1];


		$data['tgl']=$this->input->post('tgl');
		$data['branch']=$this->input->post('branch');
		$data['list_um']=$this->m_absensi->q_absensi_um($branch,$awal,$akhir)->result();
        $this->template->display('trans/absensi/v_absen_um_list',$data);
    }



    function filter_absen_mobile(){

        $data['title']='Tarikan Data Mesin Absensi Mobile'; //aksesconvert absensi
        $kmenu='I.T.B.6';
        if($this->uri->segment(4)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil Di Simpan </div>";
        } elseif ($this->uri->segment(4)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }
        $nama=$this->session->userdata('nik');
        $data['akses']=$this->m_akses->list_aksespermenu($nama,$kmenu)->row_array();
        $data['list_kanwil']=$this->m_absensi->q_kanwil()->result();
        $data['dtl_opt']=$this->m_absensi->q_dblink_option()->row_array();
        $this->template->display('trans/absensi/v_filter_absen_mobile',$data);
    }

    function save_option(){
          $dblink_id = $this->input->post('dblink_id');
          $branch = $this->input->post('branch');
          $idbu = $this->input->post('idbu');
          $c_hostaddr = base64_encode(trim($this->input->post('c_hostaddr')));
          $c_dbname = base64_encode(trim($this->input->post('c_dbname')));
          $c_userpg = base64_encode(trim($this->input->post('c_userpg')));
          $c_passpg = base64_encode(trim($this->input->post('c_passpg')));
          $doctype = $this->input->post('doctype');
          $erptype = $this->input->post('erptype');
          $description = $this->input->post('description');
          $inputdate = date('Y-m-d H:i:s');
          $inputby = $this->session->userdata('nama');

          $info = array(
                'idbu' => $idbu,
                'c_hostaddr' => $c_hostaddr,
                'c_dbname' => $c_dbname,
                'c_userpg' => $c_userpg,
                'c_passpg' => $c_passpg,
                'doctype' => $doctype,
                'erptype' => $erptype,
                'description' => $description,
                'inputdate' => $inputdate,
                'inputby' => $inputby,
          );

          $this->db->cache_delete('trans', 'absensi');
          $people = array('ARBI', 'RANDY', 'BAGOS');
          if(in_array(trim($this->session->userdata('nama')), $people)) {
              $this->db->where('branch','SBYNSA');
              $this->db->where('dblink_id','CRMDEV');
              $this->db->update("sc_mst.option_dblink",$info);
          }

          redirect('trans/absensi/filter_absen_mobile/rep_succes');
    }

    function ajax_tglakhir_mobile_cabang($kdcabang){
        $dtl_opt=$this->m_absensi->q_dblink_option()->row_array();
        $host = $dtl_opt['c_hostaddr']; $dbname = $dtl_opt['c_dbname']; $userpg = $dtl_opt['c_userpg']; $passpg = $dtl_opt['c_passpg'];
        $data = $this->m_absensi->tglakhir_mobile($kdcabang,$host,$dbname,$userpg,$passpg)->row_array();
        echo json_encode($data);
    }

    function show_mobile_attendance(){
        //$this->db->cache_delete('trans', 'absensi');

        $tanggal=$this->input->post('tgl');
        $kdcabang=$this->input->post('kdcabang');
        $maplikasi=$this->input->post('maplikasi');

        $tgl=explode(' - ',$tanggal);
        $tgla=$tgl[0].' 00:00:00';
        $tglb=$tgl[1].' 23:59:59';

        //echo date('m-d-Y H:i:s',strtotime($tgl1)).'<br>';
        //echo date('m-d-Y H:i:s',strtotime($tgl2));
        $tgl1=date('Y-m-d H:i:s',strtotime($tgla));
        $tgl2=date('Y-m-d H:i:s',strtotime($tglb));
        if (empty($tanggal)){
            redirect('trans/absensi/filter_absen_mobile');
        }
        $data['title']="DATA MESIN ABSEN $tgla hingga $tglb Untuk Wilayah $kdcabang";
        $data['tgl1']=$tgla;
        $data['tgl2']=$tglb;
        $data['kdcabang']=$kdcabang;
        $data['message']='';
        $data['maplikasi']=$maplikasi;

        $dtl_opt=$this->m_absensi->q_dblink_option()->row_array();
        $host = base64_decode($dtl_opt['c_hostaddr']); $dbname = base64_decode($dtl_opt['c_dbname']);
        $userpg = base64_decode($dtl_opt['c_userpg']); $passpg = base64_decode($dtl_opt['c_passpg']);

        if ($maplikasi==='MCRM') {
//            var_dump('dddddwww');die();
            /*khusus absensi checkin diluar kantor*/
            $data['list_absen']=$this->m_absensi->read_dblink_join_karyawan($host,$dbname,$userpg,$passpg,$tgl1,$tgl2,$kdcabang)->result();
            $data['ttldata']=sizeof($data['list_absen']);
        } else if ($maplikasi==='MABS') {
//            var_dump('ddddd');die();
            $data['list_absen']=$this->m_absensi->read_dblink_join_karyawan_mabs($host,$dbname,$userpg,$passpg,$tgl1,$tgl2,$kdcabang)->result();
            $data['ttldata']=sizeof($data['list_absen']);
        }

        $this->template->display('trans/absensi/v_absensi_mobile',$data);
    }

    function tarik_data_mobile(){
        $this->db->cache_delete('trans', 'absensi');

        $tgla=$this->input->post('tgl1');
        $tglb=$this->input->post('tgl2');
        $tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        $tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        $tglawal=date('Y-m-d H:i:s',strtotime($tgla));
        $tglakhir=date('Y-m-d H:i:s',strtotime($tglb));
        $kdcabang=$this->input->post('kdcabang');
        $maplikasi=$this->input->post('maplikasi');

        $dtl_opt=$this->m_absensi->q_dblink_option()->row_array();
        $host = base64_decode($dtl_opt['c_hostaddr']); $dbname = base64_decode($dtl_opt['c_dbname']);
        $userpg = base64_decode($dtl_opt['c_userpg']); $passpg = base64_decode($dtl_opt['c_passpg']);
        if ($maplikasi==='MCRM') {
            $datane = $this->m_absensi->read_dblink_join_karyawan($host,$dbname,$userpg,$passpg,$tglawal,$tglakhir,$kdcabang)->result();
        } else if ($maplikasi==='MABS') {
            $datane = $this->m_absensi->read_dblink_join_karyawan_mabs($host,$dbname,$userpg,$passpg,$tglawal,$tglakhir,$kdcabang)->result();
        }


        foreach ($datane as $dta){
            $badgenumber=trim($dta->idabsen);
            $userid=trim($dta->userid);
            $checktime=trim($dta->checktime);
            $customeroutletcode=trim($dta->customeroutletcode);
            $customercodelocal=trim($dta->customercodelocal);
            $custname=trim($dta->custname);
            $customertype=trim($dta->customertype);
            $this->db->query("delete from sc_tmp.checkinout where userid='$userid' and badgenumber='$badgenumber' and checktime='$checktime'");
            $cek_fingerid=$this->m_absensi->q_cek_fingerid($badgenumber)->num_rows();
            if($cek_fingerid>0){
                $info=array(
                    'userid'=>trim($dta->userid),
                    'badgenumber'=>$badgenumber,
                    'nama'=>trim($dta->usersname),
                    'checktime'=>$checktime,
                    'customeroutletcode'=>$customeroutletcode,
                    'customercodelocal'=>$customercodelocal,
                    'custname'=>$custname,
                    'customertype'=>$customertype,
                    'inputan'=>'S',
                    'inputby'=>$this->session->userdata('nik'),
                    'nik'=>$dta->nik
                );
                $this->db->insert('sc_tmp.checkinout',$info);
            }

        }
        echo json_encode(array("status" => TRUE));
    }
}
