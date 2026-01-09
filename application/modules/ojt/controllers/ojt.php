<?php

/**
 * author : Fiky Ashariza
 * modifier: DK
 */

class Ojt extends MX_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->model(array('master/m_akses', 'm_ojt', 'master/M_ApprovalRule', 'trans/m_stspeg'));
		$this->load->library(array('form_validation', 'template', 'upload', 'pdf', 'encrypt', 'Excel_generator', 'zip', 'Fiky_report', 'PHPExcel/PHPExcel/IOFactory'));

		if (!$this->session->userdata('nik')) {
			redirect('dashboard');
		}
	}

	
	function penilaian_karyawan(){
        $data['title'] = 'Penilaian Karyawan Ojt';
		$nik = isset($_GET['nik']) ? $_GET['nik'] : null; 
		$docno = isset($_GET['docno']) ? $_GET['docno'] : null; 

		$type = isset($_GET['type']) ? $_GET['type'] : null; 
		$data['type'] = $type;
		
		// data informasi umum;
		$this->load->model(array('trans/m_stspeg','trans/m_karyawan')); 
		$this->load->helper('my_helper');
		$infoumum = $this->m_stspeg->q_kar($nik, $docno)->result();
		$tglmulaikontrak = date('Ym', strtotime($infoumum[0]->tgl_mulai));
		$tglselesaikontrak = date('Ym', strtotime($infoumum[0]->tgl_selesai));
		foreach ($infoumum as &$info) {
			$info->selisih_tgl = $this->masa_kontrak($info->tgl_mulai, $info->tgl_selesai);
			$info->tgl_mulai1 = isset($info->tgl_mulai) ? formattgl($info->tgl_mulai) : null;
			$info->tgl_selesai1 = isset($info->tgl_selesai) ? formattgl($info->tgl_selesai) : null;
			$info->tglmasukkerja1 = isset($info->tglmasukkerja) ? formattgl($info->tglmasukkerja) : null;
		}
		//var_dump($infoumum);
		$data['infoumum'] = $infoumum;
		
		//data aspek_penilaian
		$aspek = $this->m_ojt->q_get_question()->result();

		$grouped_aspek = [];

		foreach ($aspek as $row) {
			$parent_key = $row->parent_kd_aspect;

			if (!isset($grouped_aspek[$parent_key])) {
				$grouped_aspek[$parent_key] = [
					'parent' => [
						'kd' => $row->parent_kd_aspect,
						'question' => $row->aspect_question,
						'type' => $row->type // simpan mentah (masih 't' / 'f')
					],
					'child' => []
				];
			}

			if ($row->type == 't' && $row->aspect_name) {
				$grouped_aspek[$parent_key]['child'][$row->aspect_name] = $row->aspect_desc;
			}
		}
		$data['aspek_grouped'] = $grouped_aspek;
		// var_dump($data['aspek_grouped']);
		// exit;

		$this->template->display('ojt/formojt.php', $data);
	}

	function save_penilaian_karyawanew(){
	$docno     = $this->input->post('docno');
    $nik       = $this->input->post('nik');
    $aspek     = $this->input->post('aspek');
    $catatan   = $this->input->post('catatan');
	$user      = $this->session->userdata('nik'); // atau pakai nama user Anda
    $usernm      = $this->m_ojt->get_name($this->session->userdata('nik')); // atau pakai nama user Anda
    $now       = date('Y-m-d H:i:s');

	$query = $this->db->query("SELECT sc_mst.pr_generate_numbering('OJTMODULE', 'SYSTEM_TEST', '{\"count3\": 9, \"prefix\": \"OJT\", \"counterid\": 1, \"xno\": 1}') AS auto_id");
	$auto_id = $query->row()->auto_id;

    // 1. Simpan ke master_ojt (insert kalau belum, update jika sudah)
    $cek = $this->db->get_where('sc_pk.master_ojt', ['kddok' => $docno]);

    if ($cek->num_rows() > 0) {
        // Sudah ada, update catatan & waktu
        $this->db->where('kddok', $docno)->update('sc_pk.master_ojt', [
            'notes' => $catatan,
            'updatedate' => $now,
            'updateby' => $user,
        ]);
    } else {
        // Belum ada, insert baru
        $this->db->insert('sc_pk.master_ojt', [
            'kddok' => $auto_id,
			'kdcontract' => $docno,
            'nik' => $nik,
            'notes' => $catatan,
            'inputdate' => $now,
            'inputby' => $user,
			'nik_panelist' => $user,
            'status' => 'A2'
        ]);
    }

    // 2. Tambahkan setiap aspek sebagai baris baru di detail_ojt
    foreach ($aspek as $kd_aspect => $score) {
        $this->db->insert('sc_pk.detail_ojt', [
            'kddok' => $auto_id,
            'kd_aspect' => $kd_aspect,
            'score' => $score,
            'assesor' => $user
        ]);
    }

		if ($this->db->affected_rows() > 0) {
		
				redirect("ojt/list_ojtpen/success_add"); // Redirect to a different page if $type is not null	
		} else {
				redirect("ojt/list_ojtpen/failed_add"); // Redirect to a different page if $type is not null
		}
	}

	function masa_kontrak($tgl_mulai, $tgl_selesai) {
		// Membuat objek DateTime
		$date1 = new DateTime($tgl_mulai);
		$date2 = new DateTime($tgl_selesai);
	
		// Hitung selisih tahun dan bulan
		$year_diff = $date2->format('Y') - $date1->format('Y');
		$month_diff = $date2->format('m') - $date1->format('m');
	
		// Jika bulan selisihnya negatif, kurangi tahun dan perbaiki bulan
		if ($month_diff < 0) {
			$year_diff--;
			$month_diff += 12;
		}
	
		// Total bulan yang dihitung dengan menambah 1 untuk bulan pertama
		$total_months = ($year_diff * 12) + $month_diff + 1;
	
		// Adjust total_months if it is 7 or 13
		if ($total_months == 7) {
			$total_months = 6;
		} elseif ($total_months == 13) {
			$total_months = 12;
		} elseif ($total_months == 5) {
			$total_months = 6;
		} elseif ($total_months == 11) {
			$total_months = 12;
		}
	
		return $total_months . ' bulan';
	}
	
	
	function list_karyawan_pk(){
		$data['title'] = 'Daftar Penilaian Karyawan dengan Atasan ' . $this->m_ojt->get_name($this->session->userdata('nik'));
		$nik = isset($_GET['nik']) ? $_GET['nik'] : null; 
		$docno = isset($_GET['docno']) ? $_GET['docno'] : null; 
		$param = "a.nik = '$nik' AND a.periode = '$docno'";
		$data['list_karyawan']=$this->m_ojt->get_list_karyawan()->result();
		//var_dump($data['list_karyawan']);
		$this->template->display('ojt/informasipk.php', $data);
	}

	function list_ojtpen(){
		$data['title'] = 'Daftar Penilaian Karyawan OJT dengan Atasan ' . $this->m_ojt->get_name($this->session->userdata('nik'));
		if(strpos($_SERVER['REQUEST_URI'],'success_add'))
			$data['message']="<div class='alert alert-success' style='display:none;' id='alertMessage'>PENILAIAN SUKSES</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'failed_add') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>PENILAIAN GAGAL</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'scoredit_success') !== false)
			$data['message']="<div class='alert alert-success' style='display:none;' id='alertMessage'>SUKSES UPDATE</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'scoredit_failed') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>GAGAL UPDATE</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'upd_success') !== false)
			$data['message']="<div class='alert alert-success' style='display:none;' id='alertMessage'>SUKSES UPDATE</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'upd_failed') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>GAGAL UPDATE</div>";
		else
			$data['message']="";

		// Add JavaScript to make the alert disappear after 5 seconds
			$data['message'] .= "
			<script>
				window.onload = function() {
					var alertMessage = document.getElementById('alertMessage');
					if (alertMessage) {
						alertMessage.style.display = 'block';
						setTimeout(function() {
							alertMessage.style.display = 'none'; // Hide the alert message after 1.5 seconds
						}, 1500);
					}
				};
			</script>";

		//data authorization
		$data['nikuser'] = $this->session->userdata('nik');

		$this->load->helper('my_helper');
		$listpk = $this->m_ojt->get_list_ojtpen('08.0512.006')->result();
		foreach ($listpk as &$info) {
			$info->periode = formattgl($info->tgl_mulai). " - " .formattgl($info->tgl_selesai);
		}
		$data['listpk'] = $listpk;
		//var_dump($data['listpk']);

		$this->template->display('ojt/listojtpen.php', $data);
	}

	function list_ojtappr(){
		$data['title'] = 'Daftar Persetujuan Penilaian Karyawan OJT dengan Atasan ' . $this->m_ojt->get_name($this->session->userdata('nik'));
		if($this->uri->segment(4)=="succes_add")
			$data['message']="<div class='alert alert-warning' style='display:none;' id='alertMessage'>SUKSES TAMBAH</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'upd_success') !== false)
			$data['message']="<div class='alert alert-success' style='display:none;' id='alertMessage'>SUKSES UPDATE</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'upd_failed') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>GAGAL UPDATE</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'del_success') !== false)
			$data['message']="<div class='alert alert-warning' style='display:none;' id='alertMessage'>SUKSES HAPUS</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'del_failed') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>GAGAL HAPUS</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'del_failed2') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>GAGAL HAPUS DOKUMEN TIDAK DITEMUKAN</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'upd_stat_success') !== false)
			$data['message']="<div class='alert alert-success' style='display:none;' id='alertMessage'>DOKUMEN SUKSES DISETUJUI</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'cancel_stat_success') !== false)
			$data['message']="<div class='alert alert-success' style='display:none;' id='alertMessage'>DOKUMEN BERHASIL DIBATALKAN</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'cancel_stat_failed2') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>PERSETUJUAN TIDAK BISA DIBATALKAN KARENA SUDAH FINAL</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'cancel_stat_failed') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>GAGAL MEMBATALKAN DOKUMEN</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'upd_stat_failed') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>GAGAL UPDATE STATUS</div>";
		else if(strpos($_SERVER['REQUEST_URI'], 'upd_stat_failed2') !== false)
			$data['message']="<div class='alert alert-danger' style='display:none;' id='alertMessage'>GAGAL UPDATE STATUS DOKUMEN TIDAK DITEMUKAN</div>";
		else
			$data['message']="";

		// Add JavaScript to make the alert disappear after 5 seconds
		if ($data['message'] != "<div class='alert alert-success' style='display:none;' id='alertMessage'>SUKSES UPDATE</div>" &&
			$data['message'] != "<div class='alert alert-danger' style='display:none;' id='alertMessage'>GAGAL UPDATE</div>") 
		 {
			$data['message'] .= "
			<script>
				window.onload = function() {
					var alertMessage = document.getElementById('alertMessage');
					if (alertMessage) {
						alertMessage.style.display = 'block';
						setTimeout(function() {
							window.location.replace(document.referrer);
						}, 1500);
					}
				};
			</script>";

		} else {
			$data['message'] .= "
			<script>
				window.onload = function() {
					var alertMessage = document.getElementById('alertMessage');
					if (alertMessage) {
						alertMessage.style.display = 'block';
						setTimeout(function() {
							alertMessage.style.display = 'none'; // Hide the alert message after 1.5 seconds
						}, 1500);
					}
				};
			</script>";
		}


		$this->load->helper('my_helper');
		$listpk = $this->m_ojt->get_list_ojtappr()->result();
		foreach ($listpk as &$info) {
			$info->periode = formattgl($info->tgl_mulai). " - " .formattgl($info->tgl_selesai);
		}
		$hr = $this->db->query("SELECT nik from sc_pk.master_appr_list where jobposition = 'HRGA'")->row()->nik;
		if (trim($this->session->userdata('nik')) == trim($hr)) {
			$data['listpk'] = $listpk;
		} else {
			$data['listpk'] = Null;
		}
		
		// var_dump($data['listpk'],$hr, $this->session->userdata('nik'));
		// exit;
		$this->template->display('ojt/listojtappr.php', $data);
	}

	function result_ojt($param = null){
		$data['title'] = 'Daftar Hasil Penilaian Karyawan OJT';
		$data['message'] = "";
		$param = isset($_GET['param']) ? $_GET['param'] : '';
		$data['list_karyawan']=$this->m_stspeg->list_karyawan()->result();
		$this->template->display('ojt/listojtresult.php', $data);

	}

	function list_result(){
		$data['message'] = "";
		// Get param from URI segment if available, otherwise fallback to $_GET
		$data['param'] = $param = $this->uri->segment(5) ? trim($this->uri->segment(5)) : (isset($_GET['param']) ? $_GET['param'] : '');
		$name = $this->m_ojt->get_name($param);
		$kddok = $this->m_ojt->get_kddok($param);
		$checkrekap = $this->m_ojt->check_rekap($kddok);
		$data['title'] = 'Daftar list penilaian karyawan ' . $name;
		$res = $this->m_ojt->list_result($param)->result();
		$notepanelist = $this->m_ojt->get_notes_panelist($param)->result();
		foreach ($notepanelist as &$name) {
			$name->nmpanelist = $this->ucstring($name->nmpanelist);
		}
		$this->load->helper('my_helper');
		foreach ($res as &$row) {
			if (isset($row->inputdate)) {
				$row->inputdate = formattgl($row->inputdate);
			}
		}
		$data['list_result']= $res;
		$data['checkrekap'] = $checkrekap;
		$data['kddok'] = $kddok;
		$data['datamodal'] = $notepanelist;
		//var_dump($data);
		//exit;
		$this->template->display('ojt/listojtresultscore.php', $data);

	}

	function edit_ojt_view(){
		$data['title'] = 'Edit Penilaian Ojt Karyawan';
		$nik = isset($_GET['nik']) ? $_GET['nik'] : null; 
		$docno = isset($_GET['docno']) ? $_GET['docno'] : null; 
		$kddok = isset($_GET['kddok']) ? $_GET['kddok'] : null; 
		$type = isset($_GET['type']) ? $_GET['type'] : null; 

		$data['type'] = $type;
		// data informasi umum;
		$this->load->model(array('trans/m_stspeg','trans/m_karyawan')); 
		$this->load->helper('my_helper');
		$infoumum = $this->m_stspeg->q_kar($nik, $docno)->result();
		$tglmulaikontrak = date('Ym', strtotime($infoumum[0]->tgl_mulai));
		$tglselesaikontrak = date('Ym', strtotime($infoumum[0]->tgl_selesai));
		foreach ($infoumum as &$info) {
			$info->selisih_tgl = $this->masa_kontrak($info->tgl_mulai, $info->tgl_selesai);
			$info->tgl_mulai1 = isset($info->tgl_mulai) ? formattgl($info->tgl_mulai) : null;
			$info->tgl_selesai1 = isset($info->tgl_selesai) ? formattgl($info->tgl_selesai) : null;
			$info->tglmasukkerja1 = isset($info->tglmasukkerja) ? formattgl($info->tglmasukkerja) : null;
		}
		//var_dump($infoumum);
		$data['infoumum'] = $infoumum;

		//data detail
		$dtl = $this->m_ojt->q_get_detail_lain_cetak($docno,$kddok)->row()->notes;
		$data['dtl'] = $dtl;
				
		//data aspek_penilaian
		$aspek = $this->m_ojt->q_get_question_edit($kddok)->result();
		$grouped_aspek = [];

		foreach ($aspek as $row) {
			$parent_key = $row->parent_kd_aspect;

			if (!isset($grouped_aspek[$parent_key])) {
				$grouped_aspek[$parent_key] = [
					'parent' => [
						'kd' => $row->parent_kd_aspect,
						'question' => $row->aspect_question,
						'type' => $row->type, // simpan mentah (masih 't' / 'f')
						'score' => $row->score, // tambahkan score
					],
					'child' => []
				];
			}

			if ($row->type == 't' && $row->aspect_name) {
				$grouped_aspek[$parent_key]['child'][$row->aspect_name] = $row->aspect_desc;
			}
		}
		$data['aspek_grouped'] = $grouped_aspek;

		//var_dump($data['aspek_grouped']);
		$this->template->display('ojt/formojtedit.php', $data);

	}

	function save_penilaian_karyawanupd(){
		// Debug: Check all POST input sent to this method
		// var_dump($this->input->post());
		// exit;
		// Or for quick output (remove after debugging):
		// echo '<pre>'; print_r($this->input->post()); echo '</pre>'; exit;

		$kddok    = $this->input->post('kddok');
		$nik      = $this->input->post('nik');
		$catatan  = $this->input->post('catatan');
		$aspek    = $this->input->post('aspek');
		$user     = $this->session->userdata('nik');
		$now      = date('Y-m-d H:i:s');

		// Update master_ojt
		$this->db->where('kddok', $kddok)->update('sc_pk.master_ojt', [
			'notes'      => $catatan,
			'updatedate' => $now,
			'updateby'   => $user,
		]);

			// var_dump($upd);
			// exit;

		// Update detail_ojt
		$updated = false;
		if (is_array($aspek)) {
			foreach ($aspek as $kd_aspect => $score) {
				$this->db->where(['kddok' => $kddok, 'kd_aspect' => $kd_aspect]);
				$exists = $this->db->get('sc_pk.detail_ojt')->num_rows() > 0;

				if ($exists) {
					$this->db->where(['kddok' => $kddok, 'kd_aspect' => $kd_aspect])
						->update('sc_pk.detail_ojt', [
							'score'   => $score,
							'assesor' => $user
						]);
					if ($this->db->affected_rows() > 0) {
						$updated = true;
					}
				} else {
					$this->db->insert('sc_pk.detail_ojt', [
						'kddok'    => $kddok,
						'kd_aspect'=> $kd_aspect,
						'score'    => $score,
						'assesor'  => $user
					]);
					if ($this->db->affected_rows() > 0) {
						$updated = true;
					}
				}
			}
		}

		if ($updated) {
			redirect('ojt/list_ojtpen/upd_success');
		} else {
			redirect('ojt/list_ojtpen/upd_failed');
		}
	}

	function approve_ojt(){
		$kddok = isset($_GET['kddok']) ? $_GET['kddok'] : null;	
		$status = isset($_GET['status']) ? $_GET['status'] : null;
		
		if ($kddok) {
            // Transition logic based on the current status
			if ($status == 'A2') {
				$new_status = 'P'; // Next status is A2
			} else {
				// If the current status doesn't match any known case, exit
				echo "Invalid current status!";
				return;
			}

			$apprdate = date('Y-m-d H:i:s'); // You can change the format as needed
            //Assuming you're storing the logged-in user's ID or username in session
            $apprby = 'Disetujui Sistem';

			$this->db->set('status', $new_status);
			$this->db->set('approvedate', $apprdate);
			$this->db->set('approveby', $apprby);
			$this->db->where('kddok', $kddok);
			$update = $this->db->update('sc_pk.master_ojt');

			// var_dump($update);
			// echo $status;
			// echo $new_status;
			// die;

            if ($update) {
                redirect('ojt/list_ojtappr/upd_stat_success');
            } else {
                redirect('ojt/list_ojtappr/upd_stat_failed');
            }
        } else {
            redirect('ojt/list_ojtappr/upd_stat_failed2');
        }


	}

	function cancel_pk(){

		$kddok = isset($_GET['kddok']) ? $_GET['kddok'] : null;	
		$status = isset($_GET['status']) ? $_GET['status'] : null;

		$canceldate = date('Y-m-d H:i:s'); // You can change the format as needed
		//Assuming you're storing the logged-in user's ID or username in session
		$cancelby = 'Dibatalkan Sistem';

		if($status != 'P'){
			$new_status = 'C';
			
			$this->db->set('status', $new_status);
			$this->db->set('canceldate', $canceldate);
			$this->db->set('cancelby', $cancelby);
			$this->db->set('cancelappr', true);
			$this->db->where('kddok', $kddok);
			$update = $this->db->update('sc_pk.master_pk');

			if ($update) {
				redirect('pk/list_pk/cancel_stat_success');
			} else {
				redirect('pk/list_pk/cancel_stat_failed');
			}
		} else {
			redirect('pk/list_pk/cancel_stat_failed2');
		}

	}

	function delete_pk(){
		 // Ambil input dari form atau parameter URL
		 $docno = $this->input->post('docno');
	 
		 // Ambil kddok dari master_pk berdasarkan nik dan docno
		 $query = $this->db->query("select kddok from sc_pk.master_pk where kddok = '$docno'");
		 $result = $query->row();
		 //$result = "PK000000006";
		 if ($result) {
			 $kddok = $result->kddok;
			 echo $kddok;
			
			 // Hapus dari detail_pk
			 $this->db->where('kddok', $kddok);
			 $this->db->delete('sc_pk.detail_pk');
	 
			 // Hapus dari master_pk
			 $this->db->where('kddok', $kddok);
			 $this->db->delete('sc_pk.master_pk');
	 
			 if ($this->db->affected_rows() > 0) {
				 // Jika penghapusan berhasil, redirect ke halaman sukses
				 redirect("pk/list_pk/del_success"); // Ganti dengan halaman redirect yang sesuai
			 } else {
				 // Jika tidak ada perubahan, redirect ke halaman gagal
				 redirect("pk/list_pk/del_failed"); // Ganti dengan halaman redirect yang sesuai
			 }
		 } else {
			 // Jika tidak ditemukan data yang cocok, redirect ke halaman gagal
			 redirect("pk/list_pk/del_failed2"); // Ganti dengan halaman redirect yang sesuai
		 }
	}


	public function addOrUpdateRekap()
	{

		$nik = $this->input->post('nik');
		$kddok = $this->input->post('kddok');
		$id = $this->input->post('id');
		$data = array(
			'kddok'             => $this->input->post('kddok'),
			'presentation_date' => $this->input->post('presentation_date'),
			'knowledge'         => $this->input->post('knowledge'),
			'skill'             => $this->input->post('skill'),
			'attitude'          => $this->input->post('attitude'),
			'recommendation'    => $this->input->post('recommendation'),
			'conclusion'        => $this->input->post('conclusion'),
		);

		if ($id) {
			$data['updateby']   = $this->session->userdata('nik');
			$data['updatedate'] = date('Y-m-d H:i:s');
			$this->db->where('id', $id);
			$result = $this->db->update('sc_pk.rekap_ojt', $data);
			if ($result) {
				$this->session->set_flashdata('message', '<div class="alert alert-success">Update rekap OJT berhasil.</div>');
			} else {
				$this->session->set_flashdata('message', '<div class="alert alert-danger">Update rekap OJT gagal.</div>');
			}
		} else {
			$data['inputby']    = $this->session->userdata('nik');
			$data['inputdate']  = date('Y-m-d H:i:s');
			$result = $this->db->insert('sc_pk.rekap_ojt', $data);
			if ($result) {
				$this->session->set_flashdata('message', '<div class="alert alert-success">Tambah rekap OJT berhasil.</div>');
			} else {
				$this->session->set_flashdata('message', '<div class="alert alert-danger">Tambah rekap OJT gagal.</div>');
			}
		}

		redirect("ojt/list_result?param=$nik");
	}
	public function modal_rekap_ojt()
	{
		$kddok = $this->input->post('kddok');
		$data = [];

		// var_dump($kddok);
		// exit();

		if ($kddok) {
			$data['rekap'] = $this->db->get_where('sc_pk.rekap_ojt', ['kddok' => $kddok])->row();
		} else {
			$data['rekap'] = null;
		}

		header('Content-Type: application/json');
		echo json_encode($data);
	}



	function cetak()
    {
        $enc_docno = $this->input->get('enc_docno');
		$enc_kddok = $this->input->get('enc_kddok');
        $data['jsonfile'] = "index.php/ojt/api_cetak/?enc_docno=$enc_docno&enc_kddok=$enc_kddok";
        $data['report_file'] = 'assets/mrt/pko.mrt';
        $data['title'] = "Cetak";
        $data['report_name'] = '';
        $this->load->view("stimulsoft/viewer_preview.php", $data);
    }

    function api_cetak()
    {
        //$docno = 123;
        $docno = $this->fiky_encryption->dekript($this->input->get('enc_docno'));
		$kddok = $this->fiky_encryption->dekript($this->input->get('enc_kddok'));
        $nik = $this->m_stspeg->q_show_edit_karkon($docno)->row()->nik;

        $nik = $this->db->query("
            select
                trim(nik) AS nik
            from sc_pk.master_ojt where kddok = '$kddok'; 
        ")->row()->nik;
//        var_dump($nik,$kddok);die();
        $this->load->helper('my_helper');
        $dataopt = [
            'kepala_sdm' => '',
            'nodok' => '',
            'tgl_berlaku' => '',
            'revisi' => '',
            'halaman' => ''
        ];
//        var_dump($nik,$docno);die();
        $infoumum = $this->m_stspeg->q_kar($nik, $docno)->result();
		$tglmulaikontrak = date('Ym', strtotime($infoumum[0]->tgl_mulai));
		$tglselesaikontrak = date('Ym', strtotime($infoumum[0]->tgl_selesai));
		foreach ($infoumum as &$info) {
            $info->nmlengkap = $this->ucstring(trim($info->nmlengkap));
            $info->nmjabatan = (property_exists($info, 'jabatan_cetak') && !empty($info->jabatan_cetak)) ? $info->jabatan_cetak : $this->ucstring(trim($info->nmjabatan));
            $info->nmatasan = ucfirst(strtolower(substr(trim($info->nmatasan), 0, 10)));
            $info->nmatasan2 = ucfirst(strtolower(substr(trim($info->nmatasan2), 0, 10)));
			$info->selisih_tgl = $this->m_ojt->masa_kontrak_cetak1($info->tgl_mulai, $info->tgl_selesai);
			$info->tgl_mulai1 = isset($info->tgl_mulai) ? formattgl($info->tgl_mulai) : null;
			$info->tgl_selesai1 = isset($info->tgl_selesai) ? formattgl($info->tgl_selesai) : null;
            $info->tglmasukkerja1 = isset($info->tglmasukkerja) ? formattgl($info->tglmasukkerja) : null;
		}

        //data detail
		$dtl = $this->m_ojt->q_get_detail_lain_cetak($docno,$kddok)->result();
//        var_dump($dtl);die();

		foreach ($dtl as &$dtldata) {
			$dtldata->nama_panelist = $this->ucstring(trim($this->m_ojt->get_name(trim($dtldata->nik_panelist))));
		}

		//data aspek_penilaian
		$aspek = $this->m_ojt->q_get_detail_penilaian_cetak($docno,$kddok)->result();
		$scores = array();

		foreach ($aspek as $row) {
			$key = "score" . $row->no;   // buat key dinamis, misal score1, score2, ...
			$scores[$key] = $row->score; // isi value dengan nilai score
		}

        //data option
        $dataopt = $this->m_ojt->get_appr_list_nm()->result();
		foreach ($dataopt as &$opt) {
            $opt->nama = $this->ucstring(trim($opt->nama));
		}

        //json
        header("Content-Type: text/json");
        echo json_encode(
            [   
                'infoumum' => $infoumum, 
                'detail' => $dtl,
                'aspek' => $scores,
                'approval' => $dataopt
            ],
            JSON_PRETTY_PRINT
        );
    }

	function cetak2()
    {
        $enc_docno = $this->input->get('enc_docno');
        $data['jsonfile'] = "index.php/ojt/api_cetak2/?enc_docno=$enc_docno";
        $data['report_file'] = 'assets/mrt/pkorekap.mrt';
        $data['title'] = "Cetak";
        $data['report_name'] = '';
        $this->load->view("stimulsoft/viewer_preview.php", $data);
    }

    function api_cetak2()
    {
        //$docno = 123;
        $docno = $this->fiky_encryption->dekript($this->input->get('enc_docno'));
        $nik = $this->m_stspeg->q_show_edit_karkon($docno)->row()->nik;
        $this->load->helper('my_helper');
        $dataopt = [
            'kepala_sdm' => '',
            'nodok' => '',
            'tgl_berlaku' => '',
            'revisi' => '',
            'halaman' => ''
        ];
        $infoumum = $this->m_stspeg->q_kar($nik, $docno)->result();
		$tglmulaikontrak = date('Ym', strtotime($infoumum[0]->tgl_mulai));
		$tglselesaikontrak = date('Ym', strtotime($infoumum[0]->tgl_selesai));
		foreach ($infoumum as &$info) {
            $info->nmlengkap = $this->ucstring(trim($info->nmlengkap));
			$info->nmdept = (property_exists($info, 'dept_cetak') && !empty($info->dept_cetak)) ? $info->dept_cetak : $this->ucstring(trim($info->nmdept));
			$info->nmsubdept = $this->ucstring(trim($info->nmsubdept));
            $info->nmjabatan = (property_exists($info, 'jabatan_cetak') && !empty($info->jabatan_cetak)) ? $info->jabatan_cetak : $this->ucstring(trim($info->nmjabatan));
            $info->nmatasan = ucfirst(strtolower(substr(trim($info->nmatasan), 0, 10)));
            $info->nmatasan2 = ucfirst(strtolower(substr(trim($info->nmatasan2), 0, 10)));
			$info->selisih_tgl = $this->m_ojt->masa_kontrak_cetak1($info->tgl_mulai, $info->tgl_selesai);
			$info->tgl_mulai1 = isset($info->tgl_mulai) ? formattgl($info->tgl_mulai) : null;
			$info->tgl_selesai1 = isset($info->tgl_selesai) ? formattgl($info->tgl_selesai) : null;
            $info->tglmasukkerja1 = isset($info->tglmasukkerja) ? formattgl($info->tglmasukkerja) : null;
		}
		//var_dump($infoumum);
		
        //data detail
		$dtl = $this->m_ojt->q_get_detail_lain_cetakrekap($docno)->result();
		
		foreach ($dtl as &$dtldata) {
			$dtldata->nama_panelist = $this->ucstring(trim($this->m_ojt->get_name($dtldata->inputby)));
			$dtldata->presentation_date = isset($dtldata->presentation_date) ? formattgl($dtldata->presentation_date) : null;

			// Parse recommendation for each detail if it exists
			if (isset($dtldata->recommendation)) {
				$recomendation = $dtldata->recommendation;
				$lines = preg_split("/\r\n|\n|\r/", $recomendation);
				// var_dump($lines); // For debugging
				$parsed = [];
			foreach ($lines as $line) {
				if (strpos($line, ':') !== false) {
						list($name, $status) = explode(':', $line, 2);
						$parsed[] = (object)[
							'nama' => trim($name),
							'status' => trim($status)
						];
					}
				}
			}
		}

        //data option
        $dataopt = $this->m_ojt->get_appr_list_nm()->result();
		foreach ($dataopt as &$opt) {
            $opt->nama = $this->ucstring(trim($opt->nama));
		}

        //json
        header("Content-Type: text/json");
        echo json_encode(
            [   
                'infoumum' => $infoumum, 
                'detail' => $dtl,
                'approval' => $dataopt,
				'recomendations' => $parsed
            ],
            JSON_PRETTY_PRINT
        );
    }

    function ucstring($string) {
            // Step 1: Replace spaces with underscores and convert to uppercase
            $string = strtolower(str_replace(' ', '_', $string));
                
            // Step 2: Replace underscores back with spaces
            $string = str_replace('_', ' ', $string);
            
            // Step 3: Capitalize the first letter of each word
            $string = ucwords(strtolower($string));

            return $string;
    }

}
