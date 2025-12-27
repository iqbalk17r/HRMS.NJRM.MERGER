<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Karyawan extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('m_karyawan','master/m_geo','master/m_agama','master/m_nikah','master/m_department','master/m_jabatan','m_bpjs','master/m_group_penggajian','master/m_bank',
            'm_riwayat_keluarga','m_riwayat_kesehatan','m_riwayat_pengalaman','m_riwayat_pendidikan','m_riwayat_pendidikan_nf','master/m_akses','recruitment/m_calonkaryawan','m_mutpromot','m_stspeg','payroll/m_master','pk/m_pk','m_skperingatan'));
        $this->load->library(array('form_validation','template','upload','pdf','Excel_generator','Fiky_version','Fiky_string','Fiky_menu','Fiky_encryption'));

        if(!$this->session->userdata('nik')){
            redirect('dashboard');
        }
    }

    public function index()
    {
        $data['title']='Data Karyawan';

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->userdata('nik'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/ALPHA.001'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['list_opt_neg']=$this->m_geo->list_opt_negara()->result();
        $data['list_opt_prov']=$this->m_geo->list_opt_prov()->result();
        $data['list_opt_agama']=$this->m_agama->q_agama()->result();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->result();
        $data['list_opt_dept']=$this->m_department->q_department()->result();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->result();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->result();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->result();
        $data['list_opt_m_grade_jabatan']=$this->m_jabatan->q_m_grade_jabatan()->result();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->result();
        $data['list_chainjobgrade']=$this->m_jabatan->chain_jobgrade()->result();//
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->result();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->result();
        //$data['list_opt_ptkp']=$this->m_karyawan->list_ptkp()->result();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->result();
        $data['list_opt_bank']=$this->m_bank->q_bank()->result();
        $data['list_resignkary']=$this->m_karyawan->list_karyresgn()->result();//
        $data['list_borong']=$this->m_karyawan->list_karyborong()->result();//
        $data['calon_karyawan']=$this->m_calonkaryawan->q_maxktpcalon()->result();//
        $data['list_finger']=$this->m_karyawan->q_finger()->result();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->result();// kantor wilayah
        $data['list_wilnom']=$this->m_karyawan->q_wilayah_nominal($p=null)->result();// wilayah nominal

        if($this->uri->segment(4)=="exist") {
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada!</div>";
        }
        else if($this->uri->segment(4)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil disimpan </div>";
        }
        else if($this->uri->segment(4)=="upsuccess"){
            $data['message']="<div class='alert alert-success'>Data Berhasil diupdate </div>";
        }
        else if($this->uri->segment(4)=="notacces"){
            $data['message']="<div class='alert alert-success'>Anda tidak Berhak untuk mengakses modul ini</div>";
        }
        else if($this->uri->segment(4)=="del"){
            $data['message']="<div class='alert alert-success'>Hapus Data Sukses</div>";
        }
        else if($this->uri->segment(4)=="del_exist"){
            $data['message']="<div class='alert alert-danger'>Ada data yang terkait, Hapus child data terlebih dahulu</div>";
        }
        else {
            $data['message']='';
        }

        $data['akses']=$this->m_akses->list_aksespermenu($nama,$kodemenu)->row_array();
        $this->template->display('trans/karyawan/v_karyawan',$data);
    }

    public function ajax_list()
    {
        $list = $this->m_karyawan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        $nama=trim($this->session->userdata('nik'));
        $kmenu='I.T.A.1';
        $akses=$this->m_akses->list_aksespermenu($nama,$kmenu)->row_array();
        foreach ($list as $person) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $person->nik;
            $row[] = $person->nmlengkap;
            if ($person->image<>'') {
                $row[] = 					//'test';
                    '<div align="center" class=" image">
					<img height="50px" width="50px" alt="User Image" class="img-box" src="'.site_url('/assets/img/profile').'/'.trim($person->image).'">
				</div>';
            } else {
                $row[] =
                    '<div align="center"  class="image">
					<img height="50px" width="50px" alt="User Image" src="'.site_url('/assets/img/user.png').'">
				</div>';
            }
            $row[] = $person->nmdept;
            $row[] = $person->nmjabatan;
            $row[] = $person->tglmasukkerja1;
            $row[] = $person->kdcabang;
            //$row[]= $person->grouppenggajian;
            //<a class="btn btn-sm btn-success" href="'.site_url('trans/mutprom/index').'/'.trim($person->nik).'" title="Detail"><i class="glyphicon glyphicon-pencil"></i> Mutasi</a>
            //add html for action
            $urlcontact = site_url('trans/karyawan/getcontact/'.bin2hex(json_encode(array('employee_id'=>trim($person->nik)))));
            $row[] =
                '<a class="btn btn-block btn-info getcontact" href="javascript:void(0)" data-href="'.$urlcontact.'" title="Kartu Kontak" > Kartu Nama </a>';
            if($akses['aksesdelete']=='t' and $akses['aksesupdate']=='t'){
                $row[] =
                    '<a class="btn btn-sm btn-default" href="'.site_url('trans/karyawan/detail').'/'.trim($person->nik).'" title="Detail"><i class="fa fa-bars"></i> </a>
						<a class="btn btn-sm btn-danger" href="'.site_url('trans/karyawan/ajax_delete').'/'.trim($person->nik).'" title="Hapus" onclick="return confirm('."'Apakah Anda Yakin Menghapus Nik Ini?  ".trim($person->nik)."'".')"><i class="fa fa-trash-o"></i> </a>';

            } else if ($akses['aksesdelete']=='f' and $akses['aksesupdate']=='t'){
                $row[]='<a class="btn btn-sm btn-success" href="'.site_url('trans/karyawan/detail').'/'.trim($person->nik).'" title="Detail"><i class="fa fa-bars"></i> </a>';
            } else if ($akses['aksesdelete']=='t' and $akses['aksesupdate']=='f'){
                $row[]='<a class="btn btn-sm btn-danger" href="'.site_url('trans/karyawan/ajax_delete').'/'.trim($person->nik).'" title="Hapus" onclick="return confirm('."'Apakah Anda Yakin Menghapus Nik Ini?  ".trim($person->nik)."'".')"><i class="fa fa-trash-o"></i> </a>';
            } else{
                $row[]='';
            }

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_karyawan->count_all(),
            "recordsFiltered" => $this->m_karyawan->count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->m_karyawan->get_by_id($id)->row();
        echo json_encode($data);
    }
    public function detail($id)
    {
        $nik=$this->uri->segment(4);
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->userdata('nik'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/ALPHA.001'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']='';
        $data['nik']=$nik;
        $data['list_opt_agama']=$this->m_agama->q_agama()->result();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->result();
        $data['list_opt_dept']=$this->m_department->q_department()->result();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->result();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->result();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->result();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->result();
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->result();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->result();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->result();
        $data['list_opt_bank']=$this->m_bank->q_bank()->result();
        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->result();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->result();
        $data['list_finger']=$this->m_karyawan->q_finger()->result();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->result();//
        $data['list_wilnom']=$this->m_karyawan->q_wilayah_nominal($p=null)->result();// wilayah nominal

        /*MUTASI*/
        //$data['list_karyawan']=$this->m_mutpromot->list_karyawan()->result();
        $data['list_karyawan']=$this->m_mutpromot->list_nik($id)->result();
        $data['list_mutasi']=$this->m_mutpromot->get_mutasinik($id)->result();
        /*END MUTASI*/
        /*STATUS KEPEGAWAIN*/
        $data['list_lk']=$this->m_stspeg->list_karyawan_index($id)->row_array();
        $data['list_kepegawaian']=$this->m_stspeg->list_kepegawaian()->result();
        $data['list_stspeg']=$this->m_stspeg->q_stspeg($id)->result();
        $data['list_rk']=$this->m_stspeg->q_stspeg($id)->row_array();
        /**/
        /*BPJS KARYAWAN*/
        $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->result();
        $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->result();
        $data['list_bpjskaryawan']=$this->m_bpjs->list_bpjs_karyawan($id)->result();
        $data['list_faskes']=$this->m_bpjs->list_faskes()->result();
        $data['list_kelas']=$this->m_bpjs->q_trxtype()->result();
        $data['list_karyawan_bpjs']=$this->m_bpjs->list_karyawan()->result();
        /*ADD RIWAYAT KELUARGA*/
        $data['list_keluarga']=$this->m_riwayat_keluarga->list_keluarga()->result();
        $data['list_negara']=$this->m_riwayat_keluarga->list_negara()->result();
        $data['list_prov']=$this->m_riwayat_keluarga->list_prov()->result();
        $data['list_kotakab']=$this->m_riwayat_keluarga->list_kotakab()->result();
        $data['list_jenjang_pendidikan']=$this->m_riwayat_keluarga->list_jenjang_pendidikan()->result();
        $data['list_riwayat_keluarga']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->result();
        $data['list_rku']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->row_array();
        /**/
        /*RIWAYAT KESEHATAN*/

        $data['list_karyawan_kesehatan']=$this->m_riwayat_kesehatan->list_karyawan()->result();
        $data['list_lkes']=$this->m_riwayat_kesehatan->list_karyawan_index($nik)->row_array();
        $data['list_penyakit']=$this->m_riwayat_kesehatan->list_penyakit()->result();
        $data['list_riwayat_kesehatan']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->result();
        $data['list_rkes']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->row_array();
        //$data['akses']=$this->m_akses->list_aksespermenu($nama,$kmenu)->row_array();
        /**/
        /*RIWAYAT KERJA /PENGALAMAN*/

        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->result();
        $data['list_rpglm']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->row_array();
        /**/

        /* PELATIHAN KARYAWAN */
        $data['list_pennf']=$this->m_riwayat_pendidikan_nf->list_karyawan_index($nik)->row_array();
        $data['list_keahlian']=$this->m_riwayat_pendidikan_nf->list_keahlian()->result();
        $data['list_riwayat_pendidikan_nf']=$this->m_riwayat_pendidikan_nf->q_riwayat_pendidikan_nf($nik)->result();
        /**/

        /*PENDIDIKAN */
        $data['list_pendidikan']=$this->m_riwayat_pendidikan->list_pendidikan()->result();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->result();
        /**/

        /* SP KARYAWAN */
        $param = " AND nik = '$nik' AND status = 'P'";
        $lsp = $this->m_skperingatan->read_trxskperingatan($param);
        $data['list_spkaryawan'] = $lsp->result();
        /* END SP KARYAWAN */

        $data['title'] = "Detail Karyawan";
        //$data['dtl'] = $this->m_karyawan->get_dtl_id($id)->row_array();
        $data['lp'] = $this->m_karyawan->get_dtl_id($id)->row_array();

        $this->template->display('trans/karyawan/v_detailhrdkary',$data);
    }

    public function detail_resign($id)
    {
        $nik=$this->uri->segment(4);
        $data['message']='';
        $data['nik']=$nik;
        $data['list_opt_agama']=$this->m_agama->q_agama()->result();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->result();
        $data['list_opt_dept']=$this->m_department->q_department()->result();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->result();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->result();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->result();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->result();
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->result();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->result();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->result();
        $data['list_opt_bank']=$this->m_bank->q_bank()->result();
        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->result();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->result();
        $data['list_finger']=$this->m_karyawan->q_finger()->result();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->result();//
        $data['list_wilnom']=$this->m_karyawan->q_wilayah_nominal($p=null)->result();// wilayah nominal

        /*MUTASI*/
        //$data['list_karyawan']=$this->m_mutpromot->list_karyawan()->result();
        $data['list_karyawan']=$this->m_mutpromot->list_nik($id)->result();
        $data['list_mutasi']=$this->m_mutpromot->get_mutasinik($id)->result();
        /*END MUTASI*/
        /*STATUS KEPEGAWAIN*/
        $data['list_lk']=$this->m_stspeg->list_karyawan_index($id)->row_array();
        $data['list_kepegawaian']=$this->m_stspeg->list_kepegawaian()->result();
        $data['list_stspeg']=$this->m_stspeg->q_stspeg($id)->result();
        $data['list_rk']=$this->m_stspeg->q_stspeg($id)->row_array();
        /**/
        /*BPJS KARYAWAN*/
        $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->result();
        $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->result();
        $data['list_bpjskaryawan']=$this->m_bpjs->list_bpjs_karyawan($id)->result();
        $data['list_faskes']=$this->m_bpjs->list_faskes()->result();
        $data['list_kelas']=$this->m_bpjs->q_trxtype()->result();
        $data['list_karyawan_bpjs']=$this->m_bpjs->list_karyawan()->result();
        /*ADD RIWAYAT KELUARGA*/
        $data['list_keluarga']=$this->m_riwayat_keluarga->list_keluarga()->result();
        $data['list_negara']=$this->m_riwayat_keluarga->list_negara()->result();
        $data['list_prov']=$this->m_riwayat_keluarga->list_prov()->result();
        $data['list_kotakab']=$this->m_riwayat_keluarga->list_kotakab()->result();
        $data['list_jenjang_pendidikan']=$this->m_riwayat_keluarga->list_jenjang_pendidikan()->result();
        $data['list_riwayat_keluarga']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->result();
        $data['list_rku']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->row_array();
        /**/
        /*RIWAYAT KESEHATAN*/

        $data['list_karyawan_kesehatan']=$this->m_riwayat_kesehatan->list_karyawan()->result();
        $data['list_lkes']=$this->m_riwayat_kesehatan->list_karyawan_index($nik)->row_array();
        $data['list_penyakit']=$this->m_riwayat_kesehatan->list_penyakit()->result();
        $data['list_riwayat_kesehatan']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->result();
        $data['list_rkes']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->row_array();
        //$data['akses']=$this->m_akses->list_aksespermenu($nama,$kmenu)->row_array();
        /**/
        /*RIWAYAT KERJA /PENGALAMAN*/

        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->result();
        $data['list_rpglm']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->row_array();
        /**/

        /* PELATIHAN KARYAWAN */
        $data['list_pennf']=$this->m_riwayat_pendidikan_nf->list_karyawan_index($nik)->row_array();
        $data['list_keahlian']=$this->m_riwayat_pendidikan_nf->list_keahlian()->result();
        $data['list_riwayat_pendidikan_nf']=$this->m_riwayat_pendidikan_nf->q_riwayat_pendidikan_nf($nik)->result();
        /**/

        /*PENDIDIKAN */
        $data['list_pendidikan']=$this->m_riwayat_pendidikan->list_pendidikan()->result();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->result();
        /**/


        $data['title'] = "Detail Karyawan";
        //$data['dtl'] = $this->m_karyawan->get_dtl_id($id)->row_array();
        $data['lp'] = $this->m_karyawan->get_dtl_id($id)->row_array();
        $this->template->display('trans/karyawan/v_detailhrdkary_resign',$data);
    }

    public function edit($id) {
        $data["message"] = "";
        $data["list_opt_neg"] = $this->m_geo->list_opt_negara()->result();
        $data["list_opt_agama"] = $this->m_agama->q_agama()->result();
        $data["list_opt_nikah"] = $this->m_nikah->q_nikah()->result();
        $data["list_opt_dept"] = $this->m_department->q_department()->result();
        $data["list_opt_lvljabt"] = $this->m_jabatan->q_lvljabatan()->result();
        $data["list_opt_atasan"] = $this->m_karyawan->list_karyawan()->result();

        $data["list_opt_ptkp"] = $this->m_bpjs->list_ptkp()->result();
        $data["list_opt_grp_gaji"] = $this->m_group_penggajian->q_group_penggajian()->result();
        $data["list_opt_bank"] = $this->m_bank->q_bank()->result();
        $data["list_finger"] = $this->m_karyawan->q_finger()->result();
        $data["list_kanwil"] = $this->m_karyawan->q_kanwil()->result();
        $data["list_wilnom"] = $this->m_karyawan->q_wilayah_nominal($p = null)->result();
        $data["title"] = "Edit Karyawan";
        
        /* CODE UNTUK VERSI*/
        $nama = trim($this->session->userdata("nik"));
        $kodemenu = "I.T.A.1";
        $versirelease = "I.T.A.1/ALPHA.001";
        $releasedate = date("2019-04-12 00:00:00");
        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);
        $data["x"] = $x["rows"];
        $data["y"] = $x["res"];
        $data["t"] = $x["xn"];
        $data["kodemenu"] = $kodemenu;
        $data["version"] = $versidb;
        /* END CODE UNTUK VERSI */

        $data["dtl"] = $this->m_karyawan->get_by_id($id)->row_array();
        var_dump($data["dtl"]);
        $this->template->display("trans/karyawan/v_editkary", $data);
    }

    public function ajax_add()
    {
        $nama = trim($this->session->userdata('nik'));
        $branch=strtoupper($this->input->post('branch'));
        $unlimitedktp=$this->input->post('ktp_seumurhdp');
        ///if ($unlimitedktp=='f' or $unlimitedktp=='F'){
        $tgl_ktp1=$this->input->post('ktpberlaku');
        $nik=trim(strtoupper($this->input->post('nik')));
        $tglnpwps=$this->input->post('tglnpwp');
        //$besaranptkp=strtoupper($this->input->post('besaranptkp'));
        $ktpkeluar=strtoupper($this->input->post('ktpdikeluarkan'));
        $tglkeluar=strtoupper($this->input->post('tgldikeluarkan'));
        $tgllahir= strtoupper($this->input->post('tgllahir'));
        $gp=$this->input->post('gajipokok');
        $gbpjs=$this->input->post('gajibpjs');
        $gjnaker1=$this->input->post('gajinaker');
        $status_ptkp=strtoupper($this->input->post('status_ptkp'));
        $kdcabang=strtoupper($this->input->post('kdcabang'));
        $besaranptkp1=$this->m_karyawan->q_besaranptkp($status_ptkp)->row_array();
        $besaranptkp=$besaranptkp1['besaranpertahun'];
        $dfktp=$this->input->post('dfktp');
        $nokk=trim($this->input->post('nokk'));
        $kdwilayahnominal=trim($this->input->post('kdwilayahnominal'));
        $kdlvlgp=trim($this->input->post('kdlvlgp'));
        if($dfktp=='t'){ $noktp=strtoupper($this->input->post('noktp2')); } else { $noktp=strtoupper($this->input->post('noktp1')); };

        $idbu=$this->input->post('idbu');

        if(empty($tgl_ktp1)){
            $tgl_ktp=null;
        } else {
            $tgl_ktp=$tgl_ktp1;
        }
        if (empty($tgllahir)){
            $tgllahir1=null;
        } else {
            $tgllahir1=$tgllahir;
        }
        if (empty($ktpkeluar)){
            $ktpdikeluarkan=null;
        } else{
            $ktpdikeluarkan=$ktpkeluar;
        }
        if (empty($tglkeluar)){
            $tgldikeluarkan=null;
        } else{
            $tgldikeluarkan=$tglkeluar;
        }
        if (empty($besaranptkp))
        {
            $ptkp=null;
        } else {
            $ptkp=$besaranptkp;
        }
        if (empty($tglnpwps)){
            $tglnpwp=null;
        } else {
            $tglnpwp=$tglnpwps;
        }
        if (empty($gp)){
            $gajipokok=null;
        } else {
            $gajipokok=$gp;
        }
        if (empty($gbpjs)){
            $gajibpjs=null;
        } else {
            $gajibpjs=$gbpjs;
        }
        if (empty($gjnaker1)){
            $gjnaker=null;
        } else {
            $gjnaker=$gjnaker1;
        }



        $data = array(
            'branch'=>$branch,
            'nik' =>$nama,
            'nmlengkap' => strtoupper($this->input->post('nmlengkap')),
            'callname' => strtoupper($this->input->post('callname')),
            'jk' => strtoupper($this->input->post('jk')),
            'neglahir' => strtoupper($this->input->post('neglahir')),
            'provlahir' => strtoupper($this->input->post('provlahir')),
            'kotalahir' => strtoupper($this->input->post('kotalahir')),
            'tgllahir' => $tgllahir1,
            'kd_agama' => strtoupper($this->input->post('kd_agama')),
            'stswn' => strtoupper($this->input->post('stswn')),
            'stsfisik' => strtoupper($this->input->post('stsfisik')),
            'ketfisik' => strtoupper($this->input->post('ketfisik')),
            'noktp' => $noktp,
            'tgl_ktp' => $tgl_ktp,
            'ktp_seumurhdp' => strtoupper($this->input->post('ktp_seumurhdp')),
            'ktpdikeluarkan' => $ktpdikeluarkan,
            'tgldikeluarkan' => $tgldikeluarkan,
            'status_pernikahan' => strtoupper($this->input->post('stastus_pernikahan')),
            'gol_darah' => strtoupper($this->input->post('gol_darah')),
            'negktp' => strtoupper($this->input->post('negktp')),
            'provktp' => strtoupper($this->input->post('provktp')),
            'kotaktp' => strtoupper($this->input->post('kotaktp')),
            'kecktp' => strtoupper($this->input->post('kecktp')),
            'kelktp' => strtoupper($this->input->post('kelktp')),
            'alamatktp' => strtoupper($this->input->post('alamatktp')),
            'negtinggal' => strtoupper($this->input->post('negtinggal')),
            'provtinggal' => strtoupper($this->input->post('provtinggal')),
            'kotatinggal' => strtoupper($this->input->post('kotatinggal')),
            'kectinggal' => strtoupper($this->input->post('kectinggal')),
            'keltinggal' => strtoupper($this->input->post('keltinggal')),
            'alamattinggal' => strtoupper($this->input->post('alamattinggal')),
            'nohp1' => strtoupper($this->input->post('nohp1')),
            'nohp2' => strtoupper($this->input->post('nohp2')),
            'npwp' => strtoupper($this->input->post('npwp')),
            'tglnpwp' => $tglnpwp,
            'bag_dept' => strtoupper($this->input->post('bag_dept')),
            'subbag_dept' => strtoupper($this->input->post('subbag_dept')),
            'jabatan' => strtoupper($this->input->post('jabatan')),
            'lvl_jabatan' => strtoupper($this->input->post('lvl_jabatan')),
            'kdgradejabatan' => strtoupper($this->input->post('kdgradejabatan')),
            'grade_golongan' => strtoupper($this->input->post('grade_golongan')),
            'nik_atasan' => strtoupper($this->input->post('nik_atasan')),
            'nik_atasan2' => strtoupper($this->input->post('nik_atasan2')),
            'status_ptkp' => strtoupper($this->input->post('status_ptkp')),
            'besaranptkp' => $besaranptkp,
            'tglmasukkerja' => strtoupper($this->input->post('tglmasukkerja')),
            //'tglkeluarkerja' => strtoupper($this->input->post('tglkeluarkerja')),
            'masakerja' => strtoupper($this->input->post('masakerja')),
            //'statuskepegawaian' => strtoupper($this->input->post('statuskepegawaian')),
            'grouppenggajian' => strtoupper($this->input->post('grouppenggajian')),
            'gajipokok' => $gajipokok,
            'gajibpjs' => $gajibpjs,
            'gajinaker' => $gjnaker,
            'namabank' => strtoupper($this->input->post('namabank')),
            'namapemilikrekening' => strtoupper($this->input->post('namapemilikrekening')),
            'norek' => strtoupper($this->input->post('norek')),
            //'shift' => strtoupper($this->input->post('shift')),
            'idabsen' => strtoupper($this->input->post('idabsen')),
            'idmesin' => $this->input->post('idmesin'),
            'cardnumber' => $this->input->post('cardnumber'),
            'email' => strtoupper($this->input->post('email')),
            //'bolehcuti' => strtoupper($this->input->post('bolehcuti')),
            //'sisacuti' => strtoupper($this->input->post('sisacuti')),
            'inputdate'=>date("d-m-Y H:i:s"),
            'inputby'=>$this->session->userdata('nik'),
            'tjborong'=>$this->input->post('borong'),
            'tjshift'=>$this->input->post('shift'),
            'tjlembur'=>$this->input->post('lembur'),
            'kdcabang'=>$kdcabang,
            'nokk'=>$nokk,
            'kdwilayahnominal'=>$kdwilayahnominal,
            'kdlvlgp' => $kdlvlgp,
            'callplan' => $this->input->post('callplan'),
            'idbu'=>$idbu
        );
 
        $cek=$this->m_karyawan->cek_exist($nik);
        if ($cek>0) {
            redirect('trans/karyawan/index/exist');
        } else {
            $insert = $this->m_karyawan->save($data);
            $this->load->model(array('master/m_option', 'master/m_akses', 'master/m_user','master/M_UserSidia',));
            $employee = $this->m_karyawan->q_karyawan_read('TRUE AND trim(noktp) = \'' . trim($noktp) . '\' ')->row();
//            $this->createUser(trim($employee->nik));
            if(trim($this->input->post('borong')) == 'f'){
                if ($this->createUser(trim($employee->nik)) == TRUE){
                    echo json_encode(array("status" => TRUE));
                    redirect('trans/karyawan/index/success');
                }else{
                    echo json_encode(array("status" => FALSE));
                }
            }else{
                echo json_encode(array("status" => TRUE));
                redirect('trans/karyawan/index/success');
            }

        }

    }

    public function ajax_update()
    {
        $nama = trim($this->session->userdata('nik'));
        $username = trim($this->session->userdata('nama'));
        $inputdate = date('Y-m-d H:i:s');
        $branch=strtoupper($this->input->post('branch'));
        $unlimitedktp=$this->input->post('ktp_seumurhdp');
        if ($unlimitedktp=='f'){
            $tgl_ktp=$this->input->post('tgl_ktp');
        } else {
            $tgl_ktp=null;
        }
        $nik=trim(strtoupper($this->input->post('nik')));
        $tglnpwps=$this->input->post('tglnpwp');
        //$besaranptkp=strtoupper($this->input->post('besaranptkp'));
        $ktpkeluar=strtoupper($this->input->post('ktpdikeluarkan'));
        $tglkeluar=strtoupper($this->input->post('tgldikeluarkan'));
        $tgllahir= strtoupper($this->input->post('tgllahir'));
        //$gp=$this->input->post('gajipokok'); //disabled by request
        //$gbpjs=$this->input->post('gajibpjs');
        //$gjnaker1=$this->input->post('gajibpjs');
        $status_ptkp=strtoupper($this->input->post('status_ptkp'));
        $besaranptkp1=$this->m_karyawan->q_besaranptkp($status_ptkp)->row_array();
        $besaranptkp=$besaranptkp1['besaranpertahun'];
        $kdcabang=$this->input->post('kdcabang');
        $nokk=trim($this->input->post('nokk'));
        $kdwilayahnominal=trim($this->input->post('kdwilayahnominal'));
        //$kdlvlgp=trim($this->input->post('kdlvlgp'));
        $deviceid=trim($this->input->post('deviceid'));
        if (empty($tgllahir)){
            $tgllahir1=null;
        } else {
            $tgllahir1=$tgllahir;
        }
        if (empty($ktpkeluar)){
            $ktpdikeluarkan=null;
        } else{
            $ktpdikeluarkan=$ktpkeluar;
        }
        if (empty($tglkeluar)){
            $tgldikeluarkan=null;
        } else{
            $tgldikeluarkan=$tglkeluar;
        }
        if (empty($besaranptkp))
        {
            $ptkp=null;
        } else {
            $ptkp=$besaranptkp;
        }
        if (empty($tglnpwps)){
            $tglnpwp=null;
        } else {
            $tglnpwp=$tglnpwps;
        }
        if (empty($gp)){
            $gajipokok=null;
        } else {
            $gajipokok=$gp;
        }
        if (empty($gbpjs)){
            $gajibpjs=null;
        } else {
            $gajibpjs=$gbpjs;
        }
        if (empty($gjnaker1)){
            $gjnaker=null;
        } else {
            $gjnaker=$gjnaker1;
        }



        $data = array(
            'branch' =>$branch,
            'nik' =>$nik,
            'nmlengkap' => strtoupper($this->input->post('nmlengkap')),
            'callname' => strtoupper($this->input->post('callname')),
            'jk' => strtoupper($this->input->post('jk')),
            'neglahir' => strtoupper($this->input->post('neglahir')),
            'provlahir' => strtoupper($this->input->post('provlahir')),
            'kotalahir' => strtoupper($this->input->post('kotalahir')),
            'tgllahir' => $tgllahir1,
            'kd_agama' => strtoupper($this->input->post('kd_agama')),
            'stswn' => strtoupper($this->input->post('stswn')),
            'stsfisik' => strtoupper($this->input->post('stsfisik')),
            'ketfisik' => strtoupper($this->input->post('ketfisik')),
            'noktp' => strtoupper($this->input->post('noktp')),
            'tgl_ktp' => $tgl_ktp,
            'ktp_seumurhdp' => strtoupper($this->input->post('ktp_seumurhdp')),
            'ktpdikeluarkan' => $ktpdikeluarkan,
            'tgldikeluarkan' => $tgldikeluarkan,
            'status_pernikahan' => strtoupper($this->input->post('status_pernikahan')),
            'gol_darah' => strtoupper($this->input->post('gol_darah')),
            'negktp' => strtoupper($this->input->post('negktp')),
            'provktp' => strtoupper($this->input->post('provktp')),
            'kotaktp' => strtoupper($this->input->post('kotaktp')),
            'kecktp' => strtoupper($this->input->post('kecktp')),
            'kelktp' => strtoupper($this->input->post('kelktp')),
            'alamatktp' => strtoupper($this->input->post('alamatktp')),
            'negtinggal' => strtoupper($this->input->post('negtinggal')),
            'provtinggal' => strtoupper($this->input->post('provtinggal')),
            'kotatinggal' => strtoupper($this->input->post('kotatinggal')),
            'kectinggal' => strtoupper($this->input->post('kectinggal')),
            'keltinggal' => strtoupper($this->input->post('keltinggal')),
            'alamattinggal' => strtoupper($this->input->post('alamattinggal')),
            'nohp1' => strtoupper($this->input->post('nohp1')),
            'nohp2' => strtoupper($this->input->post('nohp2')),
            'npwp' => strtoupper($this->input->post('npwp')),
            'tglnpwp' =>$tglnpwp,
            'bag_dept' => strtoupper($this->input->post('dept')),
            'subbag_dept' => strtoupper($this->input->post('subbag_dept')),
            'jabatan' => strtoupper($this->input->post('jabatan')),
            'lvl_jabatan' => strtoupper($this->input->post('lvl_jabatan')),
            //'kdgradejabatan' => strtoupper($this->input->post('kdgradejabatan')),
            'grade_golongan' => strtoupper($this->input->post('grade_golongan')),
            'nik_atasan' => strtoupper($this->input->post('nik_atasan')),
            'nik_atasan2' => strtoupper($this->input->post('nik_atasan2')),
            'status_ptkp' => strtoupper($this->input->post('status_ptkp')),
            'besaranptkp' => $besaranptkp,
            'tglmasukkerja' =>date('Y-m-d',strtotime($this->input->post('tglmasukkerja'))),
            //'tglkeluarkerja' => strtoupper($this->input->post('tglkeluarkerja')),
            'masakerja' => strtoupper($this->input->post('masakerja')),
            ///'statuskepegawaian' => strtoupper($this->input->post('statuskepegawaian')),
            'grouppenggajian' => strtoupper($this->input->post('grouppenggajian')),
            //'gajipokok' => $gajipokok,
            //'gajibpjs' => $gajibpjs,
            //'gajinaker' => $gjnaker,
            'namabank' => strtoupper($this->input->post('namabank')),
            'namapemilikrekening' => strtoupper($this->input->post('namapemilikrekening')),
            'norek' => strtoupper($this->input->post('norek')),
            //'shift' => strtoupper($this->input->post('shift')),
            'idabsen' => strtoupper($this->input->post('idabsen')),
            'idmesin' => strtoupper($this->input->post('idmesin')),
            'email' => strtoupper($this->input->post('email')),
            'email2' => strtoupper($this->input->post('email2')),
            //'bolehcuti' => strtoupper($this->input->post('bolehcuti')),
            //'sisacuti' => strtoupper($this->input->post('sisacuti')),
            'inputdate'=>date("d-m-Y H:i:s"),
            'inputby'=>$this->session->userdata('nik'),
            'tjborong'=>$this->input->post('borong'),
            'tjshift'=>$this->input->post('shift'),
            'tjlembur'=>$this->input->post('lembur'),
            'kdcabang'=>$kdcabang,
            'nokk'=>$nokk,
            'kdwilayahnominal'=>$kdwilayahnominal,
            //'kdlvlgp'=>$kdlvlgp,
            'deviceid'=>$deviceid,
            'updateby' => $username,
            'updatedate' => $inputdate,
            'callplan' => $this->input->post('callplan'),
            'idbu' => $this->input->post('idbu')
        );
		
		//print_r($data);die();

        $this->m_karyawan->update(array('nik' => $this->input->post('nik')), $data);
        echo json_encode(array("status" => TRUE));
        redirect('trans/karyawan/index/upsuccess');

    }

    public function ajax_delete($id)
    {
        $this->m_karyawan->delete_by_id($id);
        //echo json_encode(array("status" => TRUE));
        redirect('trans/karyawan/index/del');
    }

    public function excel_listkaryawan(){

        $datane=$this->m_karyawan->list_karyawan();
        $this->excel_generator->set_query($datane);
        $this->excel_generator->set_header(array('Nik','Nama lengkap','Panggilan','kelamin','Negara lahir','Prov lahir','Kota lahir','Tgllahir','Kode Agama','No ktp',
            'Status Pernikahan','Golongan Darah','Negara','Provinsi','Kota',
            'Kecamatan','Kelurahan','Alamat','No HP1','No HP2','NPWP','TGL NPWP','Bagian','Sub Bagian','Jabatan','Lvl Jabatan','Grade golongan','Atasan1','Atasan2','Status PTKP',
            'Besaran PTKP','Tgl masuk kerja','Tgl keluar kerja','Masakerja','Status kepegawaian','Group penggajian','Bank','Nama pemilik rekening','no rekening','ID absen','Email',
            'Sisa Cuti','id mesin','Card number','Status','Mobile Deviceid'
        ));



        $this->excel_generator->set_column(array('nik','nmlengkap','callname','jk','namanegara','nmprovlahir','nmkotalahir','tgllahir','nmagama','noktp',
            'status_pernikahan','gol_darah','namanegara','nmprovtinggal','nmkotatinggal',
            'nmkectinggal','nmdesatinggal','alamattinggal','nohp1','nohp2','npwp','tglnpwp','nmdept','nmsubdept','nmjabatan','nmlvljabatan','nmgrade','nik_atasan','nik_atasan2','status_ptkp',
            'besaranptkp','tglmasukkerja','tglkeluarkerja','masakerja','statuskepegawaian','grouppenggajian','namabank','namapemilikrekening','norek','idabsen','email',
            'sisacuti','idmesin','cardnumber','status','deviceid'
        ));

        $this->excel_generator->set_width(array(10,20,20,20,20,20,20,20,20,20,
            20,20,20,20,20,
            10,20,20,20,20,20,20,20,20,20,20,20,20,20,20,
            10,20,20,20,20,20,20,20,20,20,20,
            20,20,20,20,20
        ));
        $this->excel_generator->exportTo2007('Master Karyawan');
    }

    public function excel_listkaryawan_resign(){

        $datane=$this->m_karyawan->list_karyawan_resign();
        $this->excel_generator->set_query($datane);
        $this->excel_generator->set_header(array('Nik','Nama lengkap','Panggilan','kelamin','Negara lahir','Prov lahir','Kota lahir','Tgllahir','Kode Agama','No ktp',
            'Status Pernikahan','Golongan Darah','Negara','Provinsi','Kota',
            'Kecamatan','Kelurahan','Alamat','No HP1','No HP2','NPWP','TGL NPWP','Bagian','Sub Bagian','Jabatan','Lvl Jabatan','Grade golongan','Atasan1','Atasan2','Status PTKP',
            'Besaran PTKP','Tgl masuk kerja','Tgl keluar kerja','Masakerja','Status kepegawaian','Group penggajian','Bank','Nama pemilik rekening','no rekening','ID absen','Email',
            'Sisa Cuti','id mesin','Card number','Status','Mobile Deviceid'
        ));



        $this->excel_generator->set_column(array('nik','nmlengkap','callname','jk','namanegara','nmprovlahir','nmkotalahir','tgllahir','nmagama','noktp',
            'status_pernikahan','gol_darah','namanegara','nmprovtinggal','nmkotatinggal',
            'nmkectinggal','nmdesatinggal','alamattinggal','nohp1','nohp2','npwp','tglnpwp','nmdept','nmsubdept','nmjabatan','nmlvljabatan','nmgrade','nik_atasan','nik_atasan2','status_ptkp',
            'besaranptkp','tglmasukkerja','tglkeluarkerja','masakerja','statuskepegawaian','grouppenggajian','namabank','namapemilikrekening','norek','idabsen','email',
            'sisacuti','idmesin','cardnumber','status','deviceid'
        ));

        $this->excel_generator->set_width(array(10,20,20,20,20,20,20,20,20,20,
            20,20,20,20,20,
            10,20,20,20,20,20,20,20,20,20,20,20,20,20,20,
            10,20,20,20,20,20,20,20,20,20,20,
            20,20,20,20,20
        ));
        $this->excel_generator->exportTo2007('Daftar Karyawan Resign');
    }


    function up_foto(){
        $data['title']="Profile User";
        $nik=$this->input->post('nik');
        $nm=$this->m_karyawan->get_by_id($nik)->row_array();
        $nama=$nm['nmlengkap'];
        //setting konfigurasi upload image
        $config['upload_path'] = './assets/img/profile/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']	= '5000';
        $config['max_width']  = '3624';
        $config['max_height']  = '3200';

        $this->upload->initialize($config);
        if(!$this->upload->do_upload('gambar')){
            $gambar="";
            $data['message']="<div class='alert alert-danger'>Gambar Tidak Sesuai</div>";
            echo 'Gambar Tidak Sesuai</br>
					Format yang sesuai:</br>
					* Ukuran File yang di ijinkan max 2MB</br>
					* Lebar Max 2000 pixel</br>
					* Tinggi Max 2000 pixel</br>					
					';
        }else{
            //Image Resizing
            $config['source_image'] = $this->upload->upload_path.$this->upload->file_name;
            $config['maintain_ratio'] = FALSE;
            $config['width'] = 480;
            $config['height'] = 640;

            $this->load->library('image_lib', $config);

            if ( ! $this->image_lib->resize()){
                $this->session->set_flashdata('message', $this->image_lib->display_errors('', ''));
            } else {
                $res = $this->upload->data();
                $file_path     = $res['file_path'];
                $file         = $res['full_path'];
                $file_ext     = $res['file_ext'];
                $final_file_name = trim(trim($nik)).$file_ext;
                if(file_exists($file_path.$final_file_name)){
                    unlink($file_path.$final_file_name);
                }
                rename($file,$file_path.$final_file_name);
                //$gambar=$this->upload->file_name;
                $gambar=$final_file_name;
                $info=array('image'=>$gambar);
                //update foto pegawai
                $this->m_karyawan->save_foto($nik,$info);
                $data['message']="<div class='alert alert-success'>Data Berhasil diupdate</div>";
                //redirect("hrd/hrd/detail_peg/$nip");
            }

            $x=base_url('assets/img/profile/'.$gambar);
            //echo "<img id='gbr' src='$x' width='100%' height='100%' alt='User Image'>";
            redirect("trans/karyawan/detail/$nik");
        }
        //tampilkan pesan

        //tampilkan data anggota
        //$data['anggota']=$this->m_user->cekId($id)->row_array();
        //$this->template->display('hrd/hrd/view_detail_pegawai',$data);
    }

    function ajax_req_recruitment($ktp){

        $data = $this->m_calonkaryawan->q_linkinputkaryawan($ktp)->row_array();
        echo json_encode($data);

    }

    function ajax_cekktpkembar($noktp){

        $data = $this->m_calonkaryawan->q_ajaxktp($noktp)->num_rows();
        echo json_encode($data);
        //echo 'tae';
    }
    public function excel_karyborong(){

        $datane=$this->m_karyawan->list_karyborong();
        $this->excel_generator->set_query($datane);
        $this->excel_generator->set_header(array('Nik','Nama lengkap','Panggilan','kelamin','Negara lahir','Prov lahir','Kota lahir','Tgllahir','Kode Agama','No ktp',
            'Status Pernikahan','Golongan Darah','Negara','Provinsi','Kota',
            'Kecamatan','Kelurahan','Alamat','No HP1','No HP2','NPWP','TGL NPWP','Bagian','Sub Bagian','Jabatan','Lvl Jabatan','Grade golongan','Atasan1','Atasan2','Status PTKP',
            'Besaran PTKP','Tgl masuk kerja','Tgl keluar kerja','Masakerja','Status kepegawaian','Group penggajian','Gaji pokok','Gaji bpjs','Bank','Nama pemilik rekening','no rekening','Shift','ID absen','Email',
            'Sisa Cuti','id mesin','Card number','Status','Cost center','tj tetap','Gaji tetap','Gaji naker','Borong'
        ));



        $this->excel_generator->set_column(array('nik','nmlengkap','callname','jk','neglahir','provlahir','kotalahir','tgllahir','kd_agama','noktp',
            'status_pernikahan','gol_darah','negtinggal','provtinggal','kotatinggal',
            'kectinggal','keltinggal','alamattinggal','nohp1','nohp2','npwp','tglnpwp','bag_dept','subbag_dept','jabatan','lvl_jabatan','grade_golongan','nik_atasan','nik_atasan2','status_ptkp',
            'besaranptkp','tglmasukkerja','tglkeluarkerja','masakerja','statuskepegawaian','grouppenggajian','gajipokok','gajibpjs','namabank','namapemilikrekening','norek','tjshift','idabsen','email',
            'sisacuti','idmesin','cardnumber','status','costcenter','tj_tetap','gajitetap','gajinaker','tjborong'
        ));

        $this->excel_generator->set_width(array(10,20,20,20,20,20,20,20,20,20,
            20,20,20,20,20,
            10,20,20,20,20,20,20,20,20,20,20,20,20,20,20,
            10,20,20,20,20,20,20,20,20,20,20,20,20,20,
            20,20,20,20,20,20,20,20,7
        ));
        $this->excel_generator->exportTo2007('Master Karyawan Borong');
    }

    /* MUTASI */
    function simpanmutasi (){
        $type=trim($this->input->post('type'));
        $nik=trim($this->input->post('newnik'));
        $newkddept=strtoupper(trim($this->input->post('newkddept')));
        $newkdsubdept=strtoupper(trim($this->input->post('newkdsubdept')));
        $newkdjabatan=strtoupper(trim($this->input->post('newkdjabatan')));
        $newkdlevel=strtoupper(trim($this->input->post('newkdlevel')));
        $newnikatasan=strtoupper(trim($this->input->post('newnikatasan')));
        $newnikatasan2=strtoupper(trim($this->input->post('newnikatasan2')));
        $nosk=strtoupper(trim($this->input->post('nodoksk')));
        $tglsk=strtoupper(trim($this->input->post('tglsk')));
        $tglmemo=strtoupper(trim($this->input->post('tglmemo')));
        $tglefektif=strtoupper(trim($this->input->post('tglefektif')));
        $ket=strtoupper(trim($this->input->post('ket')));
        $info=array(
            'nik'=>$nik,
            'newkddept'=>$newkddept,
            'newkdsubdept'=>$newkdsubdept,
            'newkdjabatan'=>$newkdjabatan,
            'newkdlevel'=>$newkdlevel,
            'newnikatasan'=>$newnikatasan,
            'newnikatasan2'=>$newnikatasan2,
            'nodoksk'=>$nosk,
            'tglsk'=>$tglsk,
            'tglmemo'=>$tglmemo,
            'tglefektif'=>$tglefektif,
            'ket'=>$ket,
            'inputdate'=>date('Y-m-d H:i:s'),
            'inputby'=>$this->session->userdata('nik')
        );

        $this->db->insert('sc_tmp.mutasi',$info);
        redirect("trans/karyawan/detail/$nik/success");

    }

    function deletemutasi($id,$nik){
        $this->db->where('nik',$nik);
        $this->db->where('nodokumen',$id);
        $this->db->delete('sc_mst.mutasi');
        redirect("trans/karyawan/detail/$nik/success");
    }
    function approvemutasi($id,$nik){
        $this->db->query("update sc_mst.mutasi set status='P' where nodokumen='$id' and nik='$nik'");
        redirect("trans/karyawan/detail/$nik/success");
    }

    /*ADD MUTASI & PROMOSI KARYAWAN*/

    function add_stspeg(){
        $nik1=explode('|',$this->input->post('nik'));
        $nik=trim($this->input->post('nik'));
        $nosk=$this->input->post('noskstspeg');
        $kdkepegawaian=$this->input->post('kdkepegawaian');
        $tgl_mulai=$this->input->post('tgl_mulai');
        $tgl_selesai=$this->input->post('tgl_selesai');
        if ($tgl_mulai==''){
            $tgl_mulai=null;
        }
        if ($tgl_selesai==''){
            $tgl_selesai=null;
        }
        $cuti=$this->input->post('cuti');
        $ojt=$this->input->post('ojt');
        $keterangan=$this->input->post('keterangan');
        $tgl_input=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');

        //echo $sub;
        $info=array(
            'nik'=>$nik,
            'nodok'=>$this->session->userdata('nik'),
            'nosk'=>$nosk,
            'kdkepegawaian'=>strtoupper($kdkepegawaian),
            'tgl_mulai'=>$tgl_mulai,
            'tgl_selesai'=>$tgl_selesai,
            'cuti'=>strtoupper($cuti),
            'ojt'=>strtoupper($ojt),
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        //var_dump($info);

        $this->db->insert('sc_tmp.status_kepegawaian',$info);
        if ($this->db->affected_rows() > 0) {
            // Insert was successful
            redirect("trans/karyawan/detail/$nik/success");
        } else {
            // Insert failed
            redirect("trans/karyawan/detail/$nik/failure");
        }
        //echo $inputby;
    }

    function editview_stspeg($nik,$no_urut){
        //echo "test";

        if (empty($no_urut)){
            redirect("trans/stspeg/index/$nik");
        } else {
            $data['title']='EDIT DATA RIWAYAT KELUARGA';
            if($this->uri->segment(5)=="upsuccess"){
                $data['message']="<div class='alert alert-success'>Data Berhasil di update </div>";
            }
            else {
                $data['message']='';
            }
            $nik=$this->uri->segment(4);
            $data['nik']=$nik;
            $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->result();
            $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->result();
            $data['list_bpjskaryawan']=$this->m_bpjs->q_bpjs_karyawan()->result();
            $data['list_faskes']=$this->m_bpjs->list_faskes()->result();
            $data['list_kelas']=$this->m_bpjs->q_trxtype()->result();
            $data['list_karyawan']=$this->m_bpjs->list_karyawan()->result();
            $data['list_keluarga']=$this->m_stspeg->list_keluarga()->result();
            $data['list_negara']=$this->m_stspeg->list_negara()->result();
            $data['list_prov']=$this->m_stspeg->list_prov()->result();
            $data['list_tgl_mulai']=$this->m_stspeg->list_tgl_mulai()->result();
            $data['list_jenjang_kepegawaian']=$this->m_stspeg->list_jenjang_kepegawaian()->result();
            $data['list_rk']=$this->m_stspeg->q_stspeg_edit($nik,$no_urut)->row_array();
            $this->template->display('trans/stspeg/v_edit',$data);
        }
    }

    function detail_stspeg($nik,$no_urut){
        //echo "test";

        if (empty($no_urut)){
            redirect("trans/stspeg/index/$nik");
        } else {
            $data['title']='DETAIL DATA RIWAYAT KELUARGA';
            if($this->uri->segment(5)=="upsuccess"){
                $data['message']="<div class='alert alert-success'>Data Berhasil di update </div>";
            }
            else {
                $data['message']='';
            }
            $nik=$this->uri->segment(4);
            $data['nik']=$nik;
            $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->result();
            $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->result();
            $data['list_bpjskaryawan']=$this->m_bpjs->q_bpjs_karyawan()->result();
            $data['list_faskes']=$this->m_bpjs->list_faskes()->result();
            $data['list_kelas']=$this->m_bpjs->q_trxtype()->result();
            $data['list_karyawan']=$this->m_bpjs->list_karyawan()->result();
            $data['list_keluarga']=$this->m_stspeg->list_keluarga()->result();
            $data['list_negara']=$this->m_stspeg->list_negara()->result();
            $data['list_prov']=$this->m_stspeg->list_prov()->result();
            $data['list_tgl_mulai']=$this->m_stspeg->list_tgl_mulai()->result();
            $data['list_jenjang_kepegawaian']=$this->m_stspeg->list_jenjang_kepegawaian()->result();
            $data['list_rk']=$this->m_stspeg->q_stspeg_edit($nik,$no_urut)->row_array();
            $this->template->display('trans/stspeg/v_detail',$data);
        }
    }
    function edit_stspeg(){
        //$nik1=explode('|',);
        $nodok=$this->input->post('nodok');
        $nik=$this->input->post('nik');
        $kdkepegawaian=$this->input->post('kdkepegawaian');
        $tgl_selesai=$this->input->post('tgl_selesai');

        if ($tgl_selesai==''){
            $tgl_selesai=null;
        }

        $cuti=$this->input->post('cuti');
        $tgl_mulai=$this->input->post('tgl_mulai');
        if ($tgl_mulai==''){
            $tgl_mulai=null;
        }
        $keterangan=$this->input->post('keterangan');
        $tgl_input=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');
        //$no_urut=$this->input->post('no_urut');

        $info=array(
            'kdkepegawaian'=>strtoupper($kdkepegawaian),
            'tgl_mulai'=>($tgl_mulai),
            'tgl_selesai'=>($tgl_selesai),
            'cuti'=>strtoupper($cuti),
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );
        //$this->db->where('custcode',$kode);


        $this->db->where('nodok',$nodok);
        $this->db->where('nik',$nik);
        //$this->db->where('kdkepegawaian',$kdkepegawaian);
        $this->db->update('sc_trx.status_kepegawaian',$info);
        redirect("trans/stspeg/index/$nik/rep_succes");

        //echo $inputby;
    }

    function hps_stspeg($nik,$nodok){
        $this->db->where('nodok',$nodok);
        $this->db->delete('sc_trx.status_kepegawaian');
        redirect("trans/stspeg/index/$nik/del_succes");
    }

    /* ---------------------ADD BPJS KARYAWAN ------------------------*/

    function add_bpjs(){
        $id_bpjs=trim(strtoupper(str_replace(" ","",$this->input->post('id_bpjs'))));
        //$nmbpjs=$this->input->post('nmbpjs');
        //$kdsubdept=$this->input->post('kdsubdept');
        //$subdept=explode('|',$this->input->post('kdsubdept'));
        //$sub=$subdept[1];
        //$kode_bpjs1=explode('|',);
        $kode_bpjs=strtoupper($this->input->post('kode_bpjs'));
        //$kodekomponen1=explode('|',;
        $kodekomponen=strtoupper($this->input->post('kodekomponen'));
        //$kodefaskes1=explode
        $kodefaskes=strtoupper($this->input->post('kodefaskes'));
        //$kodefaskes3=explode('|',;
        $kodefaskes2=strtoupper($this->input->post('kodefaskes2'));
        $nik=$this->input->post('nik');
        $kelas=$this->input->post('kelas');
        $keterangan=$this->input->post('keterangan');
        $tgl_berlaku=$this->input->post('tgl_berlaku');
        $tgl_input=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');


        //echo $sub;
        $info=array(
            'id_bpjs'=>$id_bpjs,
            'kode_bpjs'=>$kode_bpjs,
            'kodekomponen'=>$kodekomponen,
            'kodefaskes'=>$kodefaskes,
            'kodefaskes2'=>$kodefaskes2,
            'nik'=>$nik,
            'kelas'=>strtoupper($kelas),
            'keterangan'=>strtoupper($keterangan),
            'tgl_berlaku'=>$tgl_berlaku,
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $cek=$this->m_bpjs->q_cek_bpjs($kode_bpjs,$nik,$kodekomponen,$id_bpjs)->num_rows();
        if ($cek>0){
            redirect("trans/karyawan/detail/$nik/kode_failed");;
        } else {
            $this->db->insert('sc_trx.bpjs_karyawan',$info);
            redirect("trans/karyawan/detail/$nik/success");
        }

    }

    function edit_bpjs(){
        $id_bpjs=trim(strtoupper(str_replace(" ","",$this->input->post('id_bpjs'))));
        //$nmbpjs=$this->input->post('nmbpjs');
        //$kdsubdept=$this->input->post('kdsubdept');
        //$subdept=explode('|',$this->input->post('kdsubdept'));
        //$sub=$subdept[1];
        //$kode_bpjs1=explode('|',);
        $kode_bpjs=strtoupper($this->input->post('kode_bpjs'));
        //$kodekomponen1=explode('|',;
        $kodekomponen=strtoupper($this->input->post('kodekomponen'));
        //$kodefaskes1=explode
        $kodefaskes=strtoupper($this->input->post('kodefaskes'));
        //$kodefaskes3=explode('|',;
        $kodefaskes2=strtoupper($this->input->post('kodefaskes2'));
        $nik=$this->input->post('nik');
        $kelas=$this->input->post('kelas');
        $keterangan=$this->input->post('keterangan');
        $tgl_berlaku=$this->input->post('tgl_berlaku');
        $tgl_input=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');
        $info=array(
            'kode_bpjs'=>$kode_bpjs,
            'kodekomponen'=>$kodekomponen,
            'kodefaskes'=>$kodefaskes,
            'kodefaskes2'=>$kodefaskes2,
            'nik'=>$nik,
            'kelas'=>strtoupper($kelas),
            'keterangan'=>strtoupper($keterangan),
            'tgl_berlaku'=>$tgl_berlaku,
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );
        //$this->db->where('custcode',$kode);

        $this->db->where('nik',$nik);
        $this->db->where('kode_bpjs',$kode_bpjs);
        $this->db->where('kodekomponen',$kodekomponen);
        $this->db->where('id_bpjs',$id_bpjs);
        $this->db->update('sc_trx.bpjs_karyawan',$info);
        redirect("trans/karyawan/detail/$nik/success");

    }

    function hps_bpjs($nik,$id_bpjs){

        $this->db->where('id_bpjs',$id_bpjs);
        $this->db->delete('sc_trx.bpjs_karyawan');
        redirect("trans/karyawan/detail/$nik/del_success");
    }


    function karyawan_self(){
        $data['title']='Profile Karyawan';
        $nama=$this->session->userdata('nik');
        $id=$nama;
        $data['list_opt_neg']=$this->m_geo->list_opt_negara()->result();
        $data['list_opt_prov']=$this->m_geo->list_opt_prov()->result();
        $data['list_opt_agama']=$this->m_agama->q_agama()->result();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->result();
        $data['list_opt_dept']=$this->m_department->q_department()->result();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->result();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->result();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->result();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->result();
        $data['list_chainjobgrade']=$this->m_jabatan->chain_jobgrade()->result();//
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->result();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->result();
        //$data['list_opt_ptkp']=$this->m_karyawan->list_ptkp()->result();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->result();
        $data['list_opt_bank']=$this->m_bank->q_bank()->result();
        $data['list_resignkary']=$this->m_karyawan->list_karyresgn()->result();//
        $data['list_borong']=$this->m_karyawan->list_karyborong()->result();//
        $data['calon_karyawan']=$this->m_calonkaryawan->q_maxktpcalon()->result();//
        $data['list_finger']=$this->m_karyawan->q_finger()->result();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->result();//
        $data['list_self']=$this->m_karyawan->get_dtl_id($id)->result();

        if($this->uri->segment(4)=="exist") {
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada!</div>";
        }
        else if($this->uri->segment(4)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil disimpan </div>";
        }
        else if($this->uri->segment(4)=="upsuccess"){
            $data['message']="<div class='alert alert-success'>Data Berhasil diupdate </div>";
        }
        else if($this->uri->segment(4)=="notacces"){
            $data['message']="<div class='alert alert-success'>Anda tidak Berhak untuk mengakses modul ini</div>";
        }
        else if($this->uri->segment(4)=="del"){
            $data['message']="<div class='alert alert-success'>Hapus Data Sukses</div>";
        }
        else if($this->uri->segment(4)=="del_exist"){
            $data['message']="<div class='alert alert-danger'>Ada data yang terkait, Hapus child data terlebih dahulu</div>";
        }
        else {
            $data['message']='';
        }


        $this->template->display('trans/karyawan/v_karyawan_self',$data);
    }

    function detail_self($id)
    {
        $nik=$this->uri->segment(4);
        $data['message']='';
        $data['list_opt_agama']=$this->m_agama->q_agama()->result();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->result();
        $data['list_opt_dept']=$this->m_department->q_department()->result();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->result();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->result();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->result();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->result();
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->result();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->result();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->result();
        $data['list_opt_bank']=$this->m_bank->q_bank()->result();
        $data['list_riwayat_keluarga']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->result();
        $data['list_riwayat_kesehatan']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->result();
        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->result();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->result();
        $data['list_finger']=$this->m_karyawan->q_finger()->result();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->result();//

        /*MUTASI*/
        //$data['list_karyawan']=$this->m_mutpromot->list_karyawan()->result();
        $data['list_karyawan']=$this->m_mutpromot->list_nik($id)->result();
        $data['list_mutasi']=$this->m_mutpromot->get_mutasinik($id)->result();
        /*END MUTASI*/
        /*STATUS KEPEGAWAIN*/
        $data['list_lk']=$this->m_stspeg->list_karyawan_index($id)->row_array();
        $data['list_kepegawaian']=$this->m_stspeg->list_kepegawaian()->result();
        $data['list_stspeg']=$this->m_stspeg->q_stspeg($id)->result();
        $data['list_spg']=$this->m_stspeg->q_stspeg($id)->row_array();
        /**/
        /*BPJS KARYAWAN*/
        $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->result();
        $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->result();
        $data['list_bpjskaryawan']=$this->m_bpjs->list_bpjs_karyawan($id)->result();
        $data['list_faskes']=$this->m_bpjs->list_faskes()->result();
        $data['list_kelas']=$this->m_bpjs->q_trxtype()->result();
        $data['list_karyawan_bpjs']=$this->m_bpjs->list_karyawan()->result();
        $data['list_lk']=$this->m_bpjs->list_karyawan_index($id)->row_array();

        /* SP KARYAWAN */
        $param = " AND nik = '$nik' AND status = 'P'";
        $lsp = $this->m_skperingatan->read_trxskperingatan($param);
        $data['list_spkaryawan'] = $lsp->result();
        /* END SP KARYAWAN */


        $data['title'] = "Detail Karyawan";
        //$data['dtl'] = $this->m_karyawan->get_dtl_id($id)->row_array();
        $data['lp'] = $this->m_karyawan->get_dtl_id($id)->row_array();
        
        $this->template->display('trans/karyawan/v_detailhrdkary_self',$data);
    }
    function edit_self($id)
    {
        $data['message']='';
        $data['list_opt_neg']=$this->m_geo->list_opt_negara()->result();
        $data['list_opt_prov']=$this->m_geo->list_opt_prov()->result();
        $data['list_opt_kotakab']=$this->m_geo->list_opt_kotakab()->result();
        $data['list_opt_kec']=$this->m_geo->list_opt_kec()->result();
        //$data['list_opt_keldesa']=$this->m_geo->list_opt_keldesa()->result();
        $data['list_opt_agama']=$this->m_agama->q_agama()->result();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->result();
        $data['list_opt_dept']=$this->m_department->q_department()->result();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->result();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->result();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->result();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->result();
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->result();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->result();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->result();
        $data['list_opt_bank']=$this->m_bank->q_bank()->result();
        $data['list_finger']=$this->m_karyawan->q_finger()->result();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->result();//
        $data['title'] = "Edit Karyawan";
        $data['dtl'] = $this->m_karyawan->get_by_id($id)->row_array();
        $this->template->display('trans/karyawan/v_editkary_self',$data);
    }

    function update_self_person(){
        $nik=trim($this->input->post('nik'));
        $nohp1=trim($this->input->post('nohp1'));
        $nohp2=trim($this->input->post('nohp2'));
        $email=trim($this->input->post('email'));
        $info=array(
            'nohp1'=>$nohp1,
            'nohp2'=>$nohp2,
            'email'=>$email
        );
        $this->db->where('nik',$nik);
        $this->db->update('sc_mst.karyawan',$info);
        redirect("trans/karyawan/karyawan_self/upsuccess");
    }

    function add_riwayat_keluarga(){
        $nik1=explode('|',$this->input->post('nik'));
        $nik=$nik1[0];
        $kdkeluarga=$this->input->post('kdkeluarga');
        $nama=$this->input->post('nama');
        $kelamin=$this->input->post('kelamin');
        $kodenegara=$this->input->post('kodenegara');
        $kodeprov=$this->input->post('kodeprov');
        $kodekotakab=$this->input->post('kodekotakab');
        $tgl_lahir=$this->input->post('tgl_lahir');
        $kdjenjang_pendidikan=$this->input->post('kdjenjang_pendidikan');
        $pekerjaan=$this->input->post('pekerjaan');
        $status_hidup=$this->input->post('status_hidup');
        $status_tanggungan=$this->input->post('status_tanggungan');
        $npwp_tgl1=$this->input->post('npwp_tgl');
        if ($npwp_tgl1==''){
            $npwp_tgl=NULL;
        } else {
            $npwp_tgl=$npwp_tgl1;
        }
        $id_bpjs=trim(strtoupper(str_replace(" ","",$this->input->post('id_bpjs'))));
        $no_npwp1=str_replace("_","",$this->input->post('no_npwp'));
        if ($no_npwp1==''){
            $no_npwp=NULL;
        } else {
            $no_npwp=$no_npwp1;
        }
        $kode_bpjs1=explode('|',$this->input->post('kode_bpjs'));
        $kode_bpjs=$kode_bpjs1[0];
        $kodekomponen1=explode('|',$this->input->post('kodekomponen'));
        $kodekomponen=$kodekomponen1[0];
        $kodefaskes1=explode('|',$this->input->post('kodefaskes'));
        $kodefaskes=$kodefaskes1[0];
        $kodefaskes3=explode('|',$this->input->post('kodefaskes2'));
        $kodefaskes2=$kodefaskes3[0];
        $kelas=$this->input->post('kelas');
        $keterangan=$this->input->post('keterangan');
        $tgl_berlaku1=$this->input->post('tgl_berlaku');
        if ($tgl_berlaku1==''){
            $tgl_berlaku=NULL;
        } else {
            $tgl_berlaku=$tgl_berlaku1;
        }
        $tgl_input=date('Y-m-d');
        $inputby=$this->session->userdata('nik');
        if (empty($kdkeluarga)){
            redirect("trans/karyawan/index/");
        }

        $info=array(
            'nik'=>$nik,
            'kdkeluarga'=>$kdkeluarga,
            'nama'=>strtoupper($nama),
            'kelamin'=>$kelamin,
            'kodenegara'=>$kodenegara,
            'kodeprov'=>$kodeprov,
            'kodekotakab'=>$kodekotakab,
            'tgl_lahir'=>$tgl_lahir,
            'kdjenjang_pendidikan'=>$kdjenjang_pendidikan,
            'pekerjaan'=>strtoupper($pekerjaan),
            'status_hidup'=>strtoupper($status_hidup),
            'status_tanggungan'=>strtoupper($status_tanggungan),
            'no_npwp'=>$no_npwp,
            'npwp_tgl'=>$npwp_tgl,
            'id_bpjs'=>$id_bpjs,
            'kode_bpjs'=>$kode_bpjs,
            'kodekomponen'=>$kodekomponen,
            'kodefaskes'=>$kodefaskes,
            'kodefaskes2'=>$kodefaskes2,
            'kelas'=>strtoupper($kelas),
            'keterangan'=>strtoupper($keterangan),
            'tgl_berlaku'=>$tgl_berlaku,
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $this->db->insert('sc_trx.riwayat_keluarga',$info);
        redirect("trans/karyawan/detail/$nik/rep_succes");


    }

    function add_riwayat_kesehatan(){
        $nik=$this->input->post('nik');
        $kdpenyakit=$this->input->post('kdpenyakit');
        $periode=$this->input->post('periode');
        $keterangan=$this->input->post('keterangan');
        $tgl_input=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');
        $info=array(
            'nik'=>$nik,
            'kdpenyakit'=>strtoupper($kdpenyakit),
            'periode'=>$periode,
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );
        $this->db->insert('sc_trx.riwayat_kesehatan',$info);
        redirect("trans/karyawan/detail/$nik/rep_succes");

    }

    function edit_riwayat_kesehatan(){
        $nik=$this->input->post('nik');
        $kdpenyakit=$this->input->post('kdpenyakit');
        $periode=$this->input->post('periode');
        $keterangan=$this->input->post('keterangan');
        $inputby=$this->input->post('inputby');
        $no_urut=$this->input->post('no_urut');

        $info=array(
            'kdpenyakit'=>strtoupper($kdpenyakit),
            'periode'=>strtoupper($periode),
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );
        $this->db->where('nik',$nik);
        $this->db->where('no_urut',$no_urut);
        $this->db->update('sc_trx.riwayat_kesehatan',$info);
        redirect("trans/karyawan/detail/$nik/rep_succes");

    }

    function hps_riwayat_kesehatan($nik,$no_urut){
        $this->db->where('nik',$nik);
        $this->db->where('no_urut',$no_urut);
        $this->db->delete('sc_trx.riwayat_kesehatan');
        redirect("trans/karyawan/detail/$nik/del_succes");
    }

    function add_riwayat_pengalaman(){

        $nik=$this->input->post('nik');
        $nmperusahaan=$this->input->post('nmperusahaan');
        $bidang_usaha=$this->input->post('bidang_usaha');
        $tahun_masuk=$this->input->post('tahun_masuk');
        $tahun_keluar=$this->input->post('tahun_keluar');
        $bagian=$this->input->post('bagian');
        $jabatan=$this->input->post('jabatan');
        $nmatasan=$this->input->post('nmatasan');
        $jbtatasan=$this->input->post('jbtatasan');
        $keterangan=$this->input->post('keterangan');
        $tgl_input=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');

        $info=array(
            'nik'=>$nik,
            'nmperusahaan'=>strtoupper($nmperusahaan),
            'bidang_usaha'=>strtoupper($bidang_usaha),
            'bagian'=>strtoupper($bagian),
            'jabatan'=>strtoupper($jabatan),
            'nmatasan'=>strtoupper($nmatasan),
            'jbtatasan'=>strtoupper($jbtatasan),
            'tahun_masuk'=>$tahun_masuk,
            'tahun_keluar'=>$tahun_keluar,
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $this->db->insert('sc_trx.riwayat_pengalaman',$info);
        redirect("trans/karyawan/detail/$nik/rep_succes");

    }

    function edit_riwayat_pengalaman(){
        $nik=$this->input->post('nik');
        $nmperusahaan=$this->input->post('nmperusahaan');
        $bidang_usaha=$this->input->post('bidang_usaha');
        $tahun_masuk=$this->input->post('tahun_masuk');
        $tahun_keluar=$this->input->post('tahun_keluar');
        $bagian=$this->input->post('bagian');
        $jabatan=$this->input->post('jabatan');
        $nmatasan=$this->input->post('nmatasan');
        $jbtatasan=$this->input->post('jbtatasan');
        $keterangan=$this->input->post('keterangan');
        $inputby=$this->input->post('inputby');
        $no_urut=$this->input->post('no_urut');

        $info=array(
            'nmperusahaan'=>strtoupper($nmperusahaan),
            'bidang_usaha'=>strtoupper($bidang_usaha),
            'bagian'=>strtoupper($bagian),
            'jabatan'=>strtoupper($jabatan),
            'nmatasan'=>strtoupper($nmatasan),
            'jbtatasan'=>strtoupper($jbtatasan),
            'tahun_masuk'=>$tahun_masuk,
            'tahun_keluar'=>$tahun_keluar,
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );
        $this->db->where('nik',$nik);
        $this->db->where('no_urut',$no_urut);
        $this->db->update('sc_trx.riwayat_pengalaman',$info);
        redirect("trans/karyawan/detail/$nik/rep_succes");

    }

    function hps_riwayat_pengalaman($nik,$no_urut){
        $this->db->where('nik',$nik);
        $this->db->where('no_urut',$no_urut);
        $this->db->delete('sc_trx.riwayat_pengalaman');
        redirect("trans/karyawan/detail/$nik/del_succes");
    }


    function add_riwayat_pendidikan_nf(){

        $nik=$this->input->post('nik');
        $kdkeahlian=$this->input->post('kdkeahlian');
        $nmkursus=$this->input->post('nmkursus');
        $nminstitusi=$this->input->post('nminstitusi');
        $tahun_masuk=str_replace("_","",$this->input->post('tahun_masuk'));
        $tahun_keluar=str_replace("_","",$this->input->post('tahun_keluar'));
        $keterangan=$this->input->post('keterangan');
        $tgl_input=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');

        $info=array(
            'nik'=>$nik,
            'kdkeahlian'=>$kdkeahlian,
            'nmkursus'=>strtoupper($nmkursus),
            'nminstitusi'=>strtoupper($nminstitusi),
            'tahun_keluar'=>strtoupper($tahun_keluar),
            'tahun_masuk'=>strtoupper($tahun_masuk),
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $this->db->insert('sc_trx.riwayat_pendidikan_nf',$info);
        redirect("trans/karyawan/detail/$nik/rep_succes");

    }

    function edit_riwayat_pendidikan_nf(){

        $nik=$this->input->post('nik');
        $kdkeahlian=$this->input->post('kdkeahlian');
        $nmkursus=$this->input->post('nmkursus');
        $nminstitusi=$this->input->post('nminstitusi');
        $tahun_masuk=str_replace("_","",$this->input->post('tahun_masuk'));
        $tahun_keluar=str_replace("_","",$this->input->post('tahun_keluar'));
        $keterangan=$this->input->post('keterangan');
        $inputby=$this->input->post('inputby');
        $no_urut=$this->input->post('no_urut');

        $info=array(
            'nmkursus'=>strtoupper($nmkursus),
            'kdkeahlian'=>strtoupper($kdkeahlian),
            'nminstitusi'=>strtoupper($nminstitusi),
            'tahun_keluar'=>strtoupper($tahun_keluar),
            'tahun_masuk'=>strtoupper($tahun_masuk),
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );

        $this->db->where('no_urut',$no_urut);
        $this->db->update('sc_trx.riwayat_pendidikan_nf',$info);
        redirect("trans/karyawan/detail/$nik/rep_succes");
    }

    function hps_riwayat_pendidikan_nf($nik,$no_urut){
        $this->db->where('no_urut',$no_urut);
        $this->db->delete('sc_trx.riwayat_pendidikan_nf');
        redirect("trans/karyawan/detail/$nik/del_succes");
    }

    function add_riwayat_pendidikan(){

        $nik=$this->input->post('nik');
        $kdpendidikan=$this->input->post('kdpendidikan');
        $nmsekolah=$this->input->post('nmsekolah');
        $jurusan=$this->input->post('jurusan');
        $program_studi=$this->input->post('program_studi');
        $kodeprov=$this->input->post('kodeprov');
        $kotakab=$this->input->post('kotakab');
        $tahun_masuk=str_replace("_","",$this->input->post('tahun_masuk'));
        $tahun_keluar=str_replace("_","",$this->input->post('tahun_keluar'));
        $nilai=$this->input->post('nilai');
        $keterangan=$this->input->post('keterangan');
        $tgl_input=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');

        $info=array(
            'nik'=>$nik,
            'kdpendidikan'=>$kdpendidikan,
            'nmsekolah'=>strtoupper($nmsekolah),
            'kotakab'=>strtoupper($kotakab),
            'jurusan'=>strtoupper($jurusan),
            'program_studi'=>strtoupper($program_studi),
            'nilai'=>strtoupper($nilai),
            'tahun_keluar'=>strtoupper($tahun_keluar),
            'tahun_masuk'=>strtoupper($tahun_masuk),
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $this->db->insert('sc_trx.riwayat_pendidikan',$info);
        redirect("trans/karyawan/detail/$nik/rep_succes");

    }
    function edit_riwayat_pendidikan(){
        $nik=$this->input->post('nik');
        $kdpendidikan=$this->input->post('kdpendidikan');
        $nmsekolah=$this->input->post('nmsekolah');
        $jurusan=$this->input->post('jurusan');
        $program_studi=$this->input->post('program_studi');
        $kodeprov=$this->input->post('kodeprov');
        $kotakab=$this->input->post('kotakab');
        $tahun_masuk=str_replace("_","",$this->input->post('tahun_masuk'));
        $tahun_keluar=str_replace("_","",$this->input->post('tahun_keluar'));
        $nilai=$this->input->post('nilai');
        $keterangan=$this->input->post('keterangan');
        $tgl_input=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');
        $no_urut=$this->input->post('no_urut');

        $info=array(
            'nmsekolah'=>strtoupper($nmsekolah),
            'kotakab'=>strtoupper($kotakab),
            'jurusan'=>strtoupper($jurusan),
            'program_studi'=>strtoupper($program_studi),
            'nilai'=>strtoupper($nilai),
            'tahun_keluar'=>strtoupper($tahun_keluar),
            'tahun_masuk'=>strtoupper($tahun_masuk),
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );

        $this->db->where('no_urut',$no_urut);
        $this->db->update('sc_trx.riwayat_pendidikan',$info);
        redirect("trans/karyawan/detail/$nik/rep_succes");

    }

    function hps_riwayat_pendidikan($nik,$no_urut){
        $this->db->where('no_urut',$no_urut);
        $this->db->delete('sc_trx.riwayat_pendidikan');
        redirect("trans/karyawan/detail/$nik/del_succes");
    }

    function get_provinsi() {
        $kodenegara = $this->input->post("kodenegara");
        echo json_encode($this->m_geo->list_opt_prov("WHERE kodenegara = '$kodenegara'")->result());
    }

    function get_kotakab() {
        $kodeprov = $this->input->post("kodeprov");
        echo json_encode($this->m_geo->list_opt_kotakab("WHERE kodeprov = '$kodeprov'")->result());
    }

    function get_kec() {
        $kodekotakab = $this->input->post("kodekotakab");
        echo json_encode($this->m_geo->list_opt_kec("WHERE kodekotakab = '$kodekotakab'")->result());
    }

    function get_keldesa() {
        $kodekec = $this->input->post("kodekec");
        echo json_encode($this->m_geo->list_opt_keldesa("WHERE kodekec = '$kodekec'")->result());
    }

    function get_subdept() {
        $kddept = $this->input->post("kddept");
        echo json_encode($this->m_department->q_subdepartment("WHERE a.kddept = '$kddept'")->result());
    }

    function get_jabatan() {
        $kddept = $this->input->post("kddept");
        $kdsubdept = $this->input->post("kdsubdept");
        echo json_encode($this->m_jabatan->q_jabatan("WHERE a.kddept = '$kddept' AND a.kdsubdept = '$kdsubdept'")->result());
    }

    function get_grade_golongan() {
        $kdlvl = $this->input->post("kdlvl");
        echo json_encode($this->m_jabatan->q_jobgrade("WHERE a.kdlvl = '$kdlvl'")->result());
    }

    function get_kdlvlgp() {
        $kdgrade = $this->input->post("kdgrade");
        echo json_encode($this->m_jabatan->q_lvlgp(" AND b.kdgrade = '$kdgrade'")->result());
    }

    public function createUser($nik = null, $password= null){
        $this->load->library(array('generatepassword'));
        $this->load->model(array('master/m_option','master/m_user','master/M_UserSidia','master/M_Branch',));
        $employee = $this->m_karyawan->q_karyawan_read('TRUE AND trim(nik) = \'' . trim($nik) . '\' ')->row();
        $branchDefault = $this->M_Branch->q_master_read_where(' AND cdefault = \'YES\' ')->row();
        $defaultAccess = $this->m_option->q_cekoption(trim('DEFACS'))->row()->value1;
        $menu = ($defaultAccess ? explode('/',$defaultAccess) : array('I.T.B.4d', 'I.T.B.16', 'I.T.A.15') );
        $passwordGenerate = $this->generatepassword->sidia(trim($employee->nik), TRUE);
        /*Notification::begin*/
        $search = array('[fullname]', '[companyName]', '[appname1]', '[username1]', '[password1]','[appname2]', '[username2]', '[password2]','[appname3]', '[username3]', '[password3]', );
        $replace = array(trim($employee->nmlengkap), $branchDefault->branchname, 'HRMS', trim($employee->nik), trim($employee->nik), 'Mobile', trim($employee->nik), trim($employee->nik), 'Sidia', trim($employee->nik), trim($employee->nik));
        $mailMessage = str_replace($search, $replace, $this->load->view('template/mailmessage/account_information',array(), true));
        /*Notification::end*/
        try {
            $this->db->trans_start();
            $this->m_user->q_user_create(array(
                'branch' => trim($employee->kdcabang),
                'nik' => trim($employee->nik),
                'username' => trim($employee->nik),
                'passwordweb' => md5(trim($employee->nik)),
                'level_id' => trim($employee->lvl_jabatan),
                'level_akses' => trim($employee->lvl_jabatan),
                'expdate' => date('Y-m-d', strtotime(' + 1 years')),
                'hold_id' => 'N',
                'image' => 'admin.jpg',
                'loccode' => trim($employee->kdcabang),
                'inputdate' => date('d-m-Y'),
                'inputby' => $this->session->userdata('nik')
            ));
            foreach ($menu as $row) {
                $this->m_akses->q_akses_create(array(
                    'nik' => trim($employee->nik),
                    'kodemenu' => $row,
                    'hold_id' => true,
                    'aksesview' => true,
                    'aksesinput' => true,
                    'aksesupdate' => true,
                    'aksesdelete' => true,
                    'aksesapprove' => true,
                    'aksesconvert' => true,
                    'aksesprint' => true,
                    'aksesdownload' => true,
                    'aksesapprove2' => true,
                    'aksesapprove3' => true,
                    'aksesfilter' => true,
                    'username' => trim($employee->nik),
                ));
            }
            $this->M_UserSidia->q_transaction_create(array(
                'branch' => (!empty($employee->branch) ? $employee->branch : $branchDefault->branch),
                'nik' => $employee->nik,
                'userid' => $employee->nik,
                'usersname' => trim($employee->callname),
                'userlname' => trim($employee->callname),
                'password' => $passwordGenerate,
                'groupuser' => 'IT',
                'level' => 'E',
                'location' => 'JA',
                'divisi' => 'AC',
                'hold' => 'Yes',
                'expdate' => date('Y-m-d H:i:s', strtotime('+1 years')),
                'inputdate' => date('Y-m-d H:i:s'),
                'inputby' => trim($this->session->userdata('nik')),
                'email' => $employee->email,
            ));

            $info = array(
                'docno' => $employee->nik,
                'doctype' => 'ACCOUNT',
                'erptype' => 'HRMS',
                'send_date' => date('Y-m-d H:i:s'),
                'doctypename' => 'ACCOUNT INFORMATION',
                'mailto' => $employee->email,
                'mailsender' =>'noreply_nusa@nusaboard.co.id',
                'mailsubject' => 'Informasi akun karyawan',
                'mailtype' => 'html',
                'mailmessage' => $mailMessage,
                'sentby' => 'SYSTEM',
                'mailstatus' => 'NO_SENT',
            );
            if (!empty ($employee->email)) {
                //$this->db->insert('public.mail_outbox', $info);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                return true;
            } else {
                throw new Exception("Error DB", 1);
                return false;
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
        }
    }

    public function actionaccountpopup($param)
    {
        $json = json_decode(hex2bin($param));
        $employee = $this->m_karyawan->q_karyawan_read('TRUE AND trim(nik) = \'' . trim($json->nik) . '\' ')->row();
        header('Content-Type: application/json');
        switch (strtolower($this->input->get_post('action'))) {
            case 'read':
                echo json_encode(array(
                    'data' => array(),
                    'canread' => true,
                    'next' => site_url('trans/karyawan/accountread/' . bin2hex(json_encode(array('nik'=>trim($employee->nik))))),
                ));
                break;
        }

    }
    public function accountread($param)
    {
        $json = json_decode(
            hex2bin($param)
        );
        $this->load->library(array('generatepassword'));
        $this->load->model(array('master/m_user','master/M_UserSidia'));
        $employee = $this->m_karyawan->q_karyawan_read('TRUE AND trim(nik) = \'' . trim($json->nik) . '\' ')->row();
        $userHrms = $this->m_user->q_user_read_where(' AND trim(nik) = \''.trim($employee->nik).'\' ')->row();
        $userSidia = $this->M_UserSidia->q_transaction_read_where(' AND nik = \''.trim($employee->nik).'\' ')->row();

        $accountList = array();

        if (!empty($userSidia) && !empty($userHrms)){
            $passDecript = $this->generatepassword->sidia($userSidia->password,FALSE);
            array_push($accountList,array('application'=>'HRMS','username'=>$userHrms->username,'password'=>$passDecript));
            array_push($accountList,array('application'=>'SIDIA','username'=>$userSidia->userid,'password'=>$passDecript));
        }else{
            array_push($accountList,array('application'=>'HRMS','username'=>$userHrms->username));
        }
        $this->load->view('trans/karyawan/modals/v_account', array(
            'accountList' => $accountList,
            'modalTitle' => 'Daftar Akun',
            'modalSize' => 'modal-sm'
        ));
    }

    function cetak()
    {
        $enc_docno = $this->input->get('enc_docno');
        $data['jsonfile'] = "index.php/trans/karyawan/api_cetak/?enc_docno=$enc_docno";
        $data['report_file'] = 'assets/mrt/pkk.mrt';
        $data['title'] = "Cetak";
        $data['report_name'] = '';
        $this->load->view("stimulsoft/viewer_preview.php", $data);
    }

    function api_cetak()
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
            $info->nmjabatan = (property_exists($info, 'jabatan_cetak') && !empty($info->jabatan_cetak)) ? $info->jabatan_cetak : $this->ucstring(trim($info->nmjabatan));
            $info->nmatasan = ucfirst(strtolower(substr(trim($info->nmatasan), 0, 10)));
            $info->nmatasan2 = ucfirst(strtolower(substr(trim($info->nmatasan2), 0, 10)));
			$info->selisih_tgl = $this->m_karyawan->masa_kontrak_cetak1($info->tgl_mulai, $info->tgl_selesai);
			$info->tgl_mulai1 = isset($info->tgl_mulai) ? formattgl($info->tgl_mulai) : null;
			$info->tgl_selesai1 = isset($info->tgl_selesai) ? formattgl($info->tgl_selesai) : null;
            $info->tglmasukkerja1 = isset($info->tglmasukkerja) ? formattgl($info->tglmasukkerja) : null;
		}
		//var_dump($infoumum);
		
        //data detail
        $dtl = $this->m_pk->q_get_detail_lain_cetak($docno)->result();

		//data kondite
		$periodekon = $this->m_pk->periode_kondite($tglmulaikontrak, $tglselesaikontrak);
		$paramkondite = "a.nik = '$nik' AND a.periode in ($periodekon)";
		$kondite = $this->m_pk->q_kondite_cetak_periode($paramkondite)->result();
		
		//data kpi
        $paramkpi = (int) filter_var($infoumum[0]->selisih_tgl, FILTER_SANITIZE_NUMBER_INT);
		$kpi = $this->m_pk->q_kpi_list_periode($nik, $paramkpi)->result();
        $newFormat = new stdClass(); // buat object baru untuk menyimpan data yang sudah diformat
        $letters = range('a', 'z'); // buat array huruf dari a sampai z
        
        // loop data kpi
        foreach ($kpi as $index => $item) {
            $monthKey = "month" . ($index + 1); // buat key menggunakan bulan$index++.
            $pointKey = "poin" . ($index + 1); // buat key untuk poin menggunakan bulan$index++.
            $formattedMonth = $item->periode_formatted; 
            $kpiPoint = $item->kpi_point; 
        
            // buat format mirip dengan dokumen penilaian
            $newFormat->{$monthKey} = $letters[$index] . ".  " . $formattedMonth;
            $newFormat->{$pointKey} = $kpiPoint;
        }

		//data aspek_penilaian
		$aspek = $this->m_pk->q_get_detail_penilaian_cetak($docno)->result();

        //data option
        $dataopt = $this->m_pk->get_appr_list_nm()->result();

        // 1. Definisikan prioritas job
        $job_priority = [
            'D' => 1,
            'GM' => 2,
            'HRGA' => 3
        ];

        // 2. Urutkan $dataopt berdasarkan prioritas job
        usort($dataopt, function($a, $b) use ($job_priority) {
            $prioA = isset($job_priority[$a->job]) ? $job_priority[$a->job] : 999;
            $prioB = isset($job_priority[$b->job]) ? $job_priority[$b->job] : 999;
        
            if ($prioA == $prioB) return 0;
            return ($prioA < $prioB) ? -1 : 1;
        });

        // 3. Lakukan pemrosesan seperti biasa
        foreach ($dataopt as $opt) {
            $opt->nama = $this->ucstring($opt->nama);
        }

        $newdataopt = new stdClass();
        foreach ($dataopt as $index => $opt) {
            $key = "approval" . ($index + 1);
            $newdataopt->{$key} = $opt->nama;
            $newdataopt->{$key . "_job" . ($index + 1)} = $opt->job;
        }

        //json
        header("Content-Type: text/json");
        echo json_encode(
            [   
                'infoumum' => $infoumum,
                'kondite' => $kondite,
                'kpi' => $newFormat,  
                'detail' => $dtl,
                'aspek' => $aspek,
                'option' => $newdataopt
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

    function cetak2()
    {
        $enc_docno = $this->input->get('enc_docno');
        $docno = $this->fiky_encryption->dekript($this->input->get('enc_docno'));
        $transaction = $this->m_stspeg->q_transaction_read_where(' AND nodok = \'' . $docno . '\' ')->row();
        $kontrak_ke = $this->m_karyawan->jml_pkwt($transaction->nik)->result();
        $nodoc_to_find = $docno;
        $number_pk = null; 

        //var_dump($kontrak_ke);
        // foreach ($kontrak_ke as $item) {
        //     //var_dump($item);
        //     if (trim($item->nodok) == $nodoc_to_find) {
        //         echo "Match found: " . $item->nodok;
        //         $number_pk = $item->row_number; 
        //         break; // Exit the loop once a match is found
        //     }
        // }

        switch ($transaction->kdkepegawaian) {
            case 'KT':
                $reportFile = 'assets/mrt/pkwtt_nusa.mrt';
                $data['report_name'] = 'PKWTT & SK';
                break;
            case 'KD':
                $reportFile = 'assets/mrt/pkwtt_nusa.mrt';
                $data['report_name'] = 'PKWT DIKARYAKAN KONTRAK KE ' . $transaction->counter;
                break;
            default:
                $reportFile = 'assets/mrt/pkwt_nusa.mrt';
                $data['report_name'] = 'PKWT KONTRAK KE ' . $number_pk;
                break;
        }

        // $this->load->library('ciqrcode');
        // $qr['string'] = rand(10, 99) . $docno . '.' . date('d-m-Y.H:i:s');
        // $qr['size'] = 2;
        // $qrCode = $this->ciqrcode->generatebase64qr($qr);

        //$this->m_stspeg->insert_stspeg_document($docno, $qr['string']);

        //$data['qr_code'] = $qrCode;
        $data['nodok'] = $docno;
        $data['jsonfile'] = "index.php/trans/karyawan/api_cetak2/?enc_docno=$enc_docno";
        $data['report_file'] = $reportFile;
        $data['title'] = "Cetak";
        $data['nik'] = $this->session->userdata('nik');
        $data['download_date'] = date('d-m-Y H:i:s');

         $this->load->view("stimulsoft/viewer_preview.php",$data);
        //$this->load->view("stimulsoft/viewer_preview_save.php", $data);
    }

    function api_cetak2()
    {
        $this->load->model(array('trans/M_Employee'));
        $docno = $this->fiky_encryption->dekript($this->input->get('enc_docno'));
        //        $pihak1 = array_map('trim', $this->m_stspeg->q_spv("and jabatan = 'HRD-1' and lvl_jabatan='03' and statuskepegawaian<>'KO'")->row_array());
        $signature = $this->M_Employee->signatureSetup();
        $transaction = $this->m_stspeg->q_transaction_read_where(' AND nodok = \'' . $docno . '\' ')->row();
//        var_dump($transaction);die();
        $pihak1 = array(
            // 'nmlengkap' => ucwords(strtolower(($transaction->kdkepegawaian == 'KT' || $transaction->kdkepegawaian == 'KD') ? $signature['CONTRACT:SIGNATURE:USERNAME:KT'] : $signature['CONTRACT:SIGNATURE:USERNAME:KK'])),
            // 'nmjabatan' => ucwords(strtolower((($transaction->kdkepegawaian == 'KT' || $transaction->kdkepegawaian == 'KD') ? $signature['CONTRACT:SIGNATURE:POSITION:KT'] : $signature['CONTRACT:SIGNATURE:POSITION:KK']))),
            // 'alamatktp' => ucwords(strtolower($signature['CONTRACT:SIGNATURE:OFFICEADDRESS'])),
            'nmlengkap' => 'Merry Chrissinda',
            'nmjabatan' => 'Direktur',
            'alamatktp' => 'Jl. Kupang Indah 10/10, RT. 03, RW. 05, Kel. Dukuh Kupang, Kec. Dukuh Pakis, Surabaya'
        );
        $nik = $this->m_stspeg->q_show_edit_karkon($docno)->row()->nik;
        $pihak2 = array_map('trim', $this->m_stspeg->q_kar($nik, $docno)->row_array());
        $specialDepartmentArr = explode(",", $signature['CONTRACT:SPECIAL:DEPARTMENT']);
        $pihak2['nmdept_format'] = (!in_array($pihak2['department_id'], $specialDepartmentArr) ? $pihak2['nmsubdept'] : $pihak2['nmdept']);
        $pihak2['nosk'] = trim($this->m_stspeg->q_stspeg_edit($nik, $docno)->row()->nosk);
        $masa_pkwt_terakhir = $this->m_karyawan->pkwt_terakhir($nik)->result();
        $pihak2['pkwt_ke'] = end($masa_pkwt_terakhir)->row_number;
        $tgl_pkwt_last = $masa_pkwt_terakhir[0]->tgl_selesai;
        $this->load->helper('my_helper');
        //        var_dump();die();
        $info = [
            'tglkontrak' => $transaction->tgl_mulai,
            'tglctk' => $pihak2['tgl_cetak'],
            'nmhari' => nmhari($pihak2['tgl_cetak']),
            'tgl' => date('j', strtotime($pihak2['tgl_cetak'])),
            'bulan' => nmbulan($pihak2['tgl_cetak']),
            'tahun' => date('Y', strtotime($pihak2['tgl_cetak'])),
            'nmhari2' => nmhari($transaction->tgl_mulai),
            'tgl2' => date('j', strtotime($transaction->tgl_mulai)),
            'bulan2' => nmbulan($transaction->tgl_mulai),
            'tahun2' => date('Y', strtotime($transaction->tgl_mulai)),
        ];
        $kdkepegawaian = $transaction->kdkepegawaian;
        if ($kdkepegawaian != 'KT') {
            $pihak2['masa_kontrak_bln'] = $this->m_karyawan->masa_kontrak($pihak2['tgl_mulai'], $pihak2['tgl_selesai']);
        }
        $pihak2['wilpen'] = $this->ucstring($this->m_karyawan->penempatan_karyawan(trim($pihak2['nmlengkap']))->row()->cabang);
        $pihak2['nmlengkap'] = $this->ucstring(trim($pihak2['nmlengkap']));
        $pihak2['alamatktp'] = $this->ucstring(trim($pihak2['alamatktp']));
        $pihak2['nmjabatan'] = !empty($pihak2['jabatan_cetak']) ? $pihak2['jabatan_cetak'] : $this->ucstring(trim($pihak2['nmjabatan']));
        $pihak2['nmdept'] = !empty($pihak2['dept_cetak']) ? $pihak2['dept_cetak'] : $this->ucstring(trim($pihak2['nmdept']));
        $pihak2['nmkepegawaian'] = convertNumberToRomanInString($pihak2['nmkepegawaian']);
        $pihak2['tgl_mulai'] = formattgl($pihak2['tgl_mulai']);
        $pihak2['tgllahir'] = formattgl($pihak2['tgllahir']);
        $pihak2['tgl_selesai'] = $pihak2['tgl_selesai'] <> '-' ? formattgl($pihak2['tgl_selesai']) : '-';
        $pihak2['tgl_cetak'] = formattgl($pihak2['tgl_cetak']);
        $pihak2['tanggal_pkwt_terakhir'] = formattgl($tgl_pkwt_last);
        header("Content-Type: text/json");
        echo json_encode(
            [
                'info' => $info,
                'pihak1' => $pihak1,
                'pihak2' => $pihak2,
            ],
            JSON_PRETTY_PRINT
        );
    }

    public function getcontact($param)
    {
        $this->load->library('ciqrcode');
        $this->load->model(array('trans/M_Employee','api/M_Setup'));
        $json = json_decode(hex2bin($param));
        $employeeData = $this->M_Employee->q_get_employee_contact(' AND employee_id = \''.$json->employee_id.'\' ')->row();
        $dirPath = 'assets/img/employee/qrvcard/';
        $qrPath  = $dirPath . $employeeData->employee_id . '.png';
        if (file_exists($qrPath)) {
            unlink($qrPath);
        }
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true); // recursive = true
        }
        $image = 'kosong';
        if (!empty($employeeData)) {
            $this->load->model(array('master/M_Branch'));
            $branch = $this->M_Branch->q_master_read_where(" AND cdefault = 'YES' LIMIT 1 ")->row();
            $address = $branch->address;
            $isQRwithLogo = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:U:L\'', 'YES');
            $defaultWebsite = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:WEB:1\'', 'nusantarajaya.co.id');
//            var_dump($isQRwithLogo);die();
            if ($isQRwithLogo == 'YES') {
                $image = $this->ciqrcode->generateVCardQRCodeWithLogo(array(
                    'prefix' => '',
                    'first_name' => $employeeData->employee_name,
//                    'last_name' => '',
                    'phone' => $employeeData->phone1,
                    'work_phone' => $employeeData->phone2,
//                    'email' => $employeeData->email,
//                    'birthday' => $employeeData->born_date,
                    'address' => $employeeData->home_address,
//                    'address_home'=>array(
//                        'street' => $employeeData->home_address,
//                        'city' => $employeeData->city_name,
//                        'state' => $employeeData->province_name,
//                        'postal' => '-',
//                        'country' => $employeeData->country_name,
//                    ),
                    /*'address_work' =>array(
                        'street' => $address,
                    ),*/
                    'website' => $defaultWebsite,
                    'organization' => $employeeData->organization,
//                    'job_title' => $employeeData->position_name,
//                    'image' => site_url('assets/img/logo-depan/Nusantara.png'),
                ), $qrPath);
            }else{
                $image = $this->ciqrcode->generateVCardQRCode(array(
                    'prefix' => '',
                    'first_name' => $employeeData->employee_name,
//                    'last_name' => '',
                    'phone' => $employeeData->phone1,
                    'work_phone' => $employeeData->phone2,
//                    'email' => $employeeData->email,
//                    'birthday' => $employeeData->born_date,
//                    'address_home'=>array(
//                        'street' => $employeeData->home_address,
//                        'city' => $employeeData->city_name,
//                        'state' => $employeeData->province_name,
//                        'postal' => '-',
//                        'country' => $employeeData->country_name,
//                    ),
//                    'address_work' =>array(
//                        'street' => $address,
//                    ),
                    'website' => $defaultWebsite,
                    'organization' => $employeeData->organization,
//                    'job_title' => $employeeData->position_name,
//                    'image' => site_url('assets/img/logo-depan/Nusantara.png'),
                ), $qrPath);
            }
        }
        $vcard = $this->exportvcard($image,$employeeData);
        $data = array(
            'vcard' => $vcard,
            'downloadUrl' => site_url('trans/karyawan/downloadvcard/'.bin2hex(json_encode(array('employee_id'=>$employeeData->employee_id)))),
            'downloadQR' => site_url('trans/karyawan/downloadqrvcard/'.bin2hex(json_encode(array('employee_id'=>$employeeData->employee_id)))),
            'modalTitle' => 'VCard Karyawan',
            'modalSize' => 'modal-md',
            'content' => 'trans/karyawan/modals/v_vcard',
        );
        $this->load->view($data['content'],$data);

    }

    public function downloadvcard($param)
    {
        $json = json_decode(hex2bin($param));
        $file = FCPATH . 'assets/img/employee/vcard/' . $json->employee_id . '.jpg';

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        } else {
            show_404();
        }
    }
    public function downloadqrvcard($param)
    {
        $json = json_decode(hex2bin($param));
        $file = FCPATH . 'assets/img/employee/qrvcard/' . $json->employee_id . '.png';

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        } else {
            show_404();
        }
    }



    public function exportvcard($qrcontact, $employeeData = array())
    {
        $this->load->model(array('api/M_Setup','master/M_Branch'));
        // load file background dan convert ke base64
        $backgroundv1 = 'data:image/jpeg;base64,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U5+aNv4o2X+Fl6YqZQ5T0srzvDZov3b97+U6uiiioPYCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAwfGXhax8XaHcaNeYXf88cneOQfdavlDWNJvtD1OfSdQj8ue1bay+tfZfGR715v8W/huviqx/tfSlH9qWin5f8Anun93/e/u1+WeI/B/wDbeF/tDCR/f0//ACaP+a6fce9kmZfVJ+zqfDI+caKdIrxsySo6snyMrU2v5ra5XZn26dzqPAPjrUPBGrC5h3T2c523Nvu+8v8Af/36+m/D/iDSvEmmxappdwssEv8A30rcfK3o1fHldF4L8da14Jv/ALRp7+bby/6+2b7sv/xDf7VfpPA3HdXh+f1TGe9h5f8Akv8Ah8u6+48PNcojjP3tH4j61ye4xS1zng7xpovjGx+2aXN86f66Bv8AWRN6MK6EDrz1r+ksHjKGOoxr4aXNGXU+IqU5Upcsx1FFIehrsJvY/OX/AIKXeM/7Q+IXhrwPby7o9F057yVV/wCes7Y2/wDfMS/9918a16p+1F4xk8cfH7xrrpcNEmqNYQf9crf9wv8A6K3f8DryuuyPwn4vnGJ+s46pU/vBX15/wTf+Hf8AwkHxS1T4g3kO+28MWflW7N2urjcmf+/Sy/8AfaV8h1+pn/BPvwavhn9n2y1iWIrc+Jb+41B933tit5C/+it3/A6mp8J6HDOF+tZhHm+z7x9M0UUVyn6wFFFFABRRRQAUUUUXsA0MK4n4jfEix8EWXkw7ZtSnX9xD/d/2m/2ay/iP8XbDwukml6LJFd6p91v4o7f/AH/9r/Zr571HUL7VruXUdQuJLmedtzSN95q/JOOPEOjlcZZflsuat9qX2Y/8H8vwPoMqyaeJfta3whqOoXerXs2o6hcNNPO26SRv4qr0UV/O1SpOvP2lT4j7OEORWRs+DP8AkbtE/wCwlbf+jFoq38O9LvtU8ZaTHY25k8i8iuZcfwxKyszUV+ocFYLEVMDKcY6OX6I+Yz2a+sR9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PW/GUCrGvzQ2bfeb/rr/APE16toXg7wz4dQLo+j29s3/AD027pP++m+attRx8vSvueGPCzDZbUjiszl7Scfs/Z/4P5HlY7P54iPs6PuoVQFXYtLRRX66lZWR86FFFFMDnvGnhHQ/HvhnUfCPiSzW60vVIGt542/utxuU/wALLwyt7V+P/wAdvgt4j+Bvjy88I61G8lq7vLpd7s+W8tN/yt/vf3l/hav2e+XK815d8fPgZ4c+PPge58K6vGsF/AWn0vUNm5rS4+vdW6Mvp9FrWnLkPneIMn/tShzU/wCJH+rH43UV0PjzwJ4m+GvivUPBvi7TZbLUdPl2srfdZP4GVv41b+Bq56ug/KZ05058kzX8JeK/EXgjxDZeKPC+qTafqdhJ5kFxC3zL/wDFp/s1+pH7Mv7V/hX476TFouqTQaV4zto83VgzbVuNvWWD+8n+z95f/Hq/KCrOm6lfaLqFvqmk309peWkqS288ErLLE6fcZWT7lTKPMevlGdV8rqe770f5T93KQ18Ofs5/8FANP1CG18G/HCZbO+ULFF4gVf3Fwf8Apuq/cb/aX5P92vtmxvrPUrSG+0+4juLa4RZIpom3K6t0ZWFc0och+pYDMqGZU/aUZFuiiipO8KKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA8W/aN/Zx8J/tBeGTZXjJp/iGxV/7L1RY/mjb/nlJ/fibuv/AAJa/Kb4gfD/AMWfDDxVeeD/ABppcljqVk3zK33JV/hlVv41b+9X7g/xda8z+NnwH8BfHbw62ieMLDbdRK/2HUoE23Nm5/uv3X+8jfK1axqcp8vnvD8MxXtqHu1P/Sj8Z6K9Q+Of7PPj74D682n+JrNrrS7h/wDQdWgVvstyvp/sP/sP/wCPL89eX1qfmeIw9XDT9nVjyyCvXPgt+1F8WPgdJHb+Gda+3aNv3S6Pf/vLZv7+3+OJv93/AMeryOigeHxFXCS9pQlyyP1S+Dv7c3wZ+JkMNh4h1IeENbfCNa6lLiB2/wCmc/3P++tre1fRlvcQXcKXFtMksUi7lZW3Ky1+ENeg/Df4/fF74TOq+BfHGoWNr/FZSt59q3/bKXcn/AvvVEqf8p9lgeMZQ9zGR5v70T9o930oxX56+Av+CmXiSzMdv8SPAdjfr917rSZGhk/3vKfcrf8Afa17t4Z/4KBfs6+IFVNQ1rVfD8rfwalprH/x6DzV/Ws/ZSPp8NxBl+J+Gpy/4tD6T+b1FG4eorzvSf2hvgbrSI2n/Fzwm7N91JNWgjc/8Bdg1dRa+N/Bt9F51l4s0aeP+9HfRMv6NU2PSjiqNT4ZxN6isePxR4bmmS3j8QadJLI21US6Tcze3zUUWNFVT6mxRRRSNAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigDI8SeG9B8XaPcaD4l0m21LTrxNk9tcRrIjr7g18E/tCf8E99W0aSfxV8Dll1GxPzyaFPLmeD/rg7f61f9l/m/36/Qtvu/exSfw/eq4y5TzMwyrDZjHlrR/7ePwm1TS9S0PUJtK1rTbqxvLR/KntrmJopYm/usr/AHKrV+zXxc/Z/wDhd8atP+y+OPDsct0i7YNSt/3V5B/uy/8Asrbl/wBmvh74rf8ABOr4leGPN1L4a6pb+K7BfmW1l2214i/7rfI//fa/7tbxqRkfn2YcLYvBe/S/eR/rofI1FaHiDw34g8J6m+i+J9FvtLv4v9Zb3sDQSr/wF6z6s+bnB0/dmFFFFBAUu5v71JRQPmZ3nwDZv+F5fDz5/wDmatK/9KoqKPgH/wAl1+Hn/Y1aV/6VRUUpbn2PDrbw89ftfoj9qaKKK4z9JCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA5vxZ4A8E+PLP+zfGfhTStat8/Kl7bJLs/wB3cPl/4DXzr4+/4J1fBfxRvufCN5q3hK7f7qwSfabUf9spfm/75da+rOfQUv4VfPynFiMuwuM/jU4yPzS8Vf8ABNj4xaX5k3hjxN4f1uFPuq8sttO3/AWV0/8AH68g8R/spftEeFZCmo/CfXp9v8Wnx/bl/wDJfdX7F7f9mlxVe1keBX4PwNX+HKUT8OtU8A+PND3f254M1zT9n3vtOmyxf+hrWD5b/wBx6/eLy1/u5/Cq76bYyEtLYQMW/vRqar2550uCY/Zrf+Sn4v8AwFST/hePw9+Rv+Rp0r/0qior9nV0vTVYOlhbqy/dby14opKtY9TAcN/VKfJz3LtFFFYn1IUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUANXavtSgg15d8V/wBoz4S/BhBD448VQpqDp5kenWy+fcv/AMAX7n+8+1fevmLxN/wU8s45JIfB3wpuJk3YiuNS1ERf99RIjf8AodXGMpHl4rOcDgpctapqfdwxRXwf8M/+CiXizxj4+8P+D9U+HOmRxa9qtrpnnQXsqtB58qx78OrB9u/7vFFV7Jk4fPMHiI81OWiPvGiiisj1gooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigBNwb8a+Pf2yv2wz8N/tPwv8AhtdK3iaaP/T9SUZTTUb+BfWfb/3z/vfd94/aC+KVv8G/hPrnjhnX7XbQ+Rp0bf8ALW7l+WIfQMdzf7KtX436tqmpa3qN1rGrXkt5fX0r3FxPI255ZWbc7NWtKPMfIcUZzLAw+rUfjl+QmpalqWs31xqurX899eXTebcXNzK0ssr/AN5mf79VqKK6D82bb3O8+Af/ACXX4ef9jVpX/pVFRR8A/wDkuvw8/wCxq0r/ANKoqKUtz6/hxf7PP/F+iP2pooorjP0kKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA+If+CnniS4tfCvgjwjD/AKnUb+61CT/et0RE/wDR7V+fFfef/BUHS7pofh/rS/8AHvG2oWr/AOy7eQy/+gt/3zXwZXVS+A/JOJ5SlmVTm/u/kFFFFWfPnefAP/kuvw8/7GrSv/SqKij4B/8AJdfh5/2NWlf+lUVFKW59lw5/u8/8X6I/amiiiuM/SQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigD56/bd+Fs3xI+BOpyabC82p+GmXWbVFXLOsassq/wDfp3b/AHlSvyfr95GXcu1sYr8uv2yv2WtS+EviK6+IHhDT/M8GaxcbtkKf8gy4b/lk/wD0yLfcb/gH93dvTn9k+F4tyqU/9tpf9vf5nzBRRRWx+fHefAP/AJLr8PP+xq0r/wBKoqKPgH/yXX4ef9jVpX/pVFRSlufZcOf7vP8Axfoj9qaKKK4z9JCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACqWpaZYavYz6ZqljDd2d1G0U1vPGskUqt95WVvvCrtFANX0Z8QfGr/AIJy6PrFzdeIPg3riaTPL+8/sfUAzW27/pnKPnT/AHW3f7yV8s+Jv2R/2i/Clx5N38K9XvF/hk01Vvlb/v1u/wDH6/YJdx9KdWsasj5rGcK4HFy54+7/AIT8jfgj8DPjPYfGXwPqGo/CvxbZWlh4i066ubm60eeKKKJLpWd3dl2/dWiv1w28/dX8qKftR4PhunhIckZ3H0UUVifSBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAB9aTpXOXnxE8BafcfZb7xpoVtPnb5U2pQq3/AHyWrV0/VtL1WHztL1O0u4/70MqyL/47TM41qcvdjIv0UUUjQKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigD8H7pn+0TfP8AxPVrRfEGveGb6PVfD+t32mXkX3bm0naCVf8AgSVTuv8Aj4n/AN56ZXafg/tJQl7rPrH4J/8ABQL4jeCZrfR/ifG3ivRd+1rlvlv4F/2W+7L/ALr/ADf7dfob4G8d+FPiT4atPFfgvWoNS027X5JYT9xu6sv8DL/davxBr279lf8AaH1T4D+PYWupml8K6xKkGsWn9xf+fhf9tP8Ax5fl/wBzOdP+U+uyPiSrQqxoYuXNF/8Akp+u9FV7S7t723ju7WRZYpkWSOSNtysrfxLViuY/Sk76hRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUU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RRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQBwvxD+Dfw1+KtmLP4geDNP1U7dqTNFtnj/wB2VfnX8Gr4m+Nn/BOnxJobT678G9TfW7Nfn/sm9dY7xP8ArnL9yX/gW1v96v0SxjtRVxnKJ5OPybB5jH95H3v5up+FOsaPrHh/VLjRNd02607ULRvKuLa5iaOWJ/7rK9Ua/Y342fs4/DX476WYvFWk/Z9VhQra6ta/JdQH03fxr/st/wCO1+ZXx8/Zt8ffALXPs/iC2+3aLcNtsNZt0/cT/wCy3/PKX/Zb/gG6to1OY/O834fr5Y/aL3qf83/yR5PRRRWh8+fYX7If7Z1x4Daz+GPxWvpJ/Dj7YtN1ST5m0z+7FLj70Hv/AAf7n3f0Ztbq2vreO8tJI54ZlWSORG3Kyt0ZWr8JK+z/ANiL9rL/AIRe4s/g38R9Sxotw4i0TUpm/wCPOVv+XeVv+eTfwt/C3+x9zGpT+0fb8O8QOMo4PFy937Mj9FqKAcjIorA/QwooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArI8UeFfD/jLRbvw74n0m21LTL1PKuLa4Tcjr71r0nUUEThGa5Jn5VftW/sj618EtQk8WeFI7i/8E3c3yzH5pdOdv8A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AUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUANznpzmvl79s79qCP4O+HX8G+DNQjPjHW4MIyN/yDbduPPP+23IQfVv4fm6P9qH9qTw/wDAHQ302x8nUvF+oR5sNP8AM+SFf+e8/wDcQf3erew3Mv5X+KvFXiDxr4hvfFHibUJL/U9Sk866uJPvM3/xH+xWtOJ8hxFn0cLCWGw0v3n/AKT/AMEzZpZriRppXaWR23MzfeZ6ZRRXQfml76sK/R7/AIJ6/As+E/CE/wAXvEFmV1bxNF5WnLInzQafu+9/21dd3+6qf3q+Wf2R/wBnu6+OfxCSTVrVl8LaC63WrS/89/7lqv8AtN/6Bv8A9mv1ktbW3sraOzs4Y4YoUWOKONdqqq9FWsakvsn3PCWT80vr1X/t3/Ms0UUVgfoIUUUUAFFFFACfSqWraZa6zp1zpd6nmQXMbROvqKuilrGtRjXpypVPhkOMuT3onxx4h0W48O65eaLef6y1l27v7yfwN/3xWdXs/wC0L4bCy2Hii3X73+hz/wDoS/8As1eMV/IHFWTTyHNq2D+yvh/wy2/ryP0bLcT9bw8agV9A/APxI2oeH7jQLh90mnNuj/65N/8AZb6+fq7b4QeIP7C8cWaM22G//wBDk/4F9z/x7bXocAZv/ZGeUakvhl7sv+3v+DYyznDfWMLJfyn1FRRRX9ap3Pz0KKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigBrDvtzivlP9pz9trwz8KftXg74fNa614uUNFLJv3W2nP8A7Z/jl/2P++v7tfQPxUmmt/h3r01vK8TrYy7WRipHHYivz1m8HeEWuPm8K6Of+3GL/wCJrSCPneIMwrYOi4UNJPqfMfiTxJrvjDXb3xN4m1Se/wBTv5PMnuJm3Mzf5/grNr6t/wCEN8If9Cro/wD4Axf/ABNH/CG+EP8AoVdH/wDAGL/4mui5+avDurLmlLU+Uq674U/C/wAUfGLxtYeCPCdt5lzdNvmnYfuraL+KWX/ZX/7Cvf8A/hDfCH/Qq6P/AOAMX/xNfTv7HPh/QdJs/FN1peh2FnMxt1aS3tkjYjY/BKgHFTKR25ZlcMVjI0aktD1z4PfCfwz8GfA9j4G8MwYht/3lzcOv726uG+/K+P4m/wDHV2r0Fd7u6+1JH3pexrmZ+t06UaEY0aeiQ6iiikaBRRRQAUUUUAFFFFAHNfELRf8AhIPCOp6aqhpHgaSL/rovzJ+q18mV9qyfcevn6+0fSPtlx/xK7P8A1jf8sF/wr8O8Wsqp1q2HxcZWlaS/U+o4dryUZ0+h5XUlvcTWtxHc277ZInRlb/br0r+ydL/6Btr/AN+V/wAKP7J0v/oG2v8A35X/AAr8YhhfZTUov8/8z6F4m6+E980m+h1LS7PUoP8AV3UCTJ9GXdVz3rD8CqqeFdMVFCgW44AxW5/DX9l5dUdXB06st3GP5H51VXJKSQ6iiiu0zCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigD/2Q==';
        $backgroundv2 = 'data:image/jpeg;base64,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Svg79rP/AIIU/D/9pP44at4ztdf1Xwdc623n6haWUaGK5uSzF7jBYYaQFScdwTxnA++grZX5untUD6czMSs8kYJ6Iq4/8eB5rWE+XY8zMcpw+PpqliFsyzRRRWR6YUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRQDmkByOmKAWquLRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUANMmOqnp17U1pvkyfl+uQPzNfjN4h/4OZ/HKavILD4b+FLe1jdhsuL64mkOP9sbR/wCO12nwn/4Odba51OGHxl8LZreyf/WXei6n58sf/bCWNM/9/K19jPsfH0+OMpnLl5/wf+R+tNFeGfsmf8FF/hR+2jpe/wAFeJbWbU0XdNpN4Rb38A/veUTl1/203J/tV7X9vHlhtuQQTkHIwOuO5/Ac1Diz6jD4ujXgp0ZJp9ixRRRUnTYKKKKBBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAfyW6o+dRuuB9+SoZB5g7EjpuAbH51Jqf/ACEbr/fkqOvUP5Ikryui/wCGvFOo+C/EFnq2j395peqWE3n213aymOe3f++jj5lb3Br9tf8Agjt/wV2b9rPb8OviJcWUHxCsod9jdpiNdfjQEnK4ws6AFiFPzKCRyGA/DutfwP461X4beNNJ8Q6JeS2Gr6Hdx3tlcxnDwyxkFDn2IH1wM5AXEVKakfRcOcQ1ctxMZQb5ftX6n9X4kJGdp2jnPqPanV5H+xX+0ja/ta/sx+EfiBZwiD/hILJWuoEP/HtdRsY5ox7LIrrnAyADjnFeuZ5rz2mnY/o/DV41qSrQd09UFFFFI2CiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA/kr1P8A5CN1/vyVHUmp/wDIRuv9+So69Q/ksKKKKDK+tkftT/wbRfES5139mTxv4bmn82Lw9r/2m2RuTBHcx5x/ul4nbHqTX6WAfOPpX5b/APBsV4WltPhD8VNcZG2Xur2lkh7EwQO5/Sda/Ucvh+n3RXBV+K5/SPCEpPKqLn1ViSiiisj6gKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigD+SvU/wDkI3X+/JUdSan/AMhG6/35Kjr1Ln8kNa2CljXzuE+Y+gBJPbj154FG3DYYqG2hyM9FP8X09+g7kV+in/BFT/glhqHx18d6d8VPHOmT2/gvQrhJ9DtrlNo124TOHAOCYIyCSSMMRhc4JClJLc9XJcqqY/ELDwV13P0i/wCCRv7MVz+yx+w/4U0PUrUWfiDVVbW9WQ8MJ7g7grDsViEaEeqHp0r6f8v5ic9aihshCMBuOfqQeT+Oc/nip68+TTP6WwWEjhqEKMNohRRRUHYFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB/Mz4o/4Jt/HzR9cuIJfhB8QpXaVgGtdFmuY8HvvjBX9a7D4V/8EZ/2jPirqMMUfw7v9Bt5P9Zc63KlikH1Rz5h/wCAI1f0ZNbAg8nkde9IbRduBkevJ5rq+tPsfm1Lw0wUZXdWX4H5pfsO/wDBvD4U+F17aeIfi1qdv401SGUTxaRZFl0hHHeRnxJOfbCIe6Gv0h0/RLXStMgtbaCO1tbZFjhihQIkSrgBABwFAAAA6Y46DF4Rbe5J65J3fzp/JXHH5VjKo3ufaZZlGGwFP2eGjbzCiiisz1QooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigBGO3qR+dLX5YaH/wAHPGgPdPFq/wAJdYtdjMubLWlus4/34I677wp/wcofBPWJ44tU8PfEDRGb70stlbSwL+KTlv8AxytfYT7HzdLi/LKnw1V9z/yP0Ror5q+Gn/BX39nT4puiWXxP0HTpn/5Z6yX0sj8bhUX9a9/8K+PNF8daUt/omq6dq9i/S4srmOeI/wDAkJFRys9jD5hhq/8ABmpejua1FRfagOuBzjk4z7D3qQNnoKk67i0UgJPbFLQDdgooooGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAH8lmq7W1K4O0fK8nX5v55qJAIydo2qeqr8o/MfN+tTaomNRuef45Khr1Ln8jxcfs3BTsUgEgdgAFA/BQBXSfDb4w+Kvg5rC6j4T8Sa54bvl6TaZevaN+cZB/Wubop+6awxFWPwyfydvyPuX9n/8A4OBfjv8ACO4tofEd1pHxC0uI7WTVLcW935fos8O0bv8AakST6V+h/wCy/wD8F8fgn8fltbPxFd3fw41u4Pl+TrHzWLv6JeKPKx7yeXX4F0gG0kgAkjbgjII9++fcEVlOlGR9ZlXGmZ4N8vPzr+9/Vz+snQfElh4m0qC+028tb+yuk8yGe3mWWOVfVWXgj6GrrTYH3TnrjoT9K/mW/ZO/4KF/Fb9jDVkk8F+JbhdK37ptGvy11p1yPRoycqf9pGV/9qv1z/YO/wCC8Hw7/adu7Hw54zhX4eeLbjCQi8mVtM1GQ/d8qc4Kseyuoz2Zq5p0n9k/Usj42weNfs5+5LzPvmioY7wTBSmGDZIIOR7fnUnmDbnjH1rGx9nGSkrrYdRRRSKCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo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T5raS3maN0ZXRijoykMjAZKkHoSAcDuVIOCKZXYfiFuWXK9z64/4JEf8FDJv2GvjvFZ6zcPJ8PvFbpDq8bZIsZAMR3sY54QZXP8AErEHkIV/oR0nXYdb02G6tpI57edA8ckciukitgqVYHBDAgg9wRX8mu3KkEk5O7HbP+Ht0r9vf+Df39u4fG34KTfCnxDe7vE3geJX015JDvvNLYDYOTy0LMFPJ2q8XvXNiIdj9W8PuIZc31Cu9HrH/I/RyiikDZJ46VyH7Atri0U0y9OOTVDxB4ntfDGjXGoXsiW9pao0kkjtgACsq9enRpyq1HaMVdvyHFOUlFbssvPHbxsSdoHOT3/pXmXxA/a88E+AZZYZNROo3cXWGzXzCf8AgX3P1r5s+Pv7U+rfFe+kstNmn0zw+nyCFG2Pcj+8+OfwryP16fN97A4P4dP0r+XOOPpBfV631XIad/78v0X+Z91lvBrqR58ZLXsfWV7/AMFE9JST/R/D2pSR/wB6SRYz+XNa/hz9vzwjqtwI9QtdU0kH/lrLF5kf/jmW/wDHa+NhjOdqg+woBKhdvyEd0+U/mPm/WvzSh49cVQnzyqRflyr/ADPanwfl8o2SaP0p8JfELRvHmmJeaTqFtfW7DIeJ84+o6j8cVq+eV6rtPYk8V+bHgrx3q3w81uPUNIvZ7O4Q5IRv3b/VejfjmvtT9nD9ou0+NmkyRzJHZazZg+fBnIkQdJF9jX9BeHXjDg+IZrBYmPs8R/5LL0PkM64bq4L36bvDues0U1pCGxilVt3av2/mV7HzL0FoooqgCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooARRgVEbMNIST8p6gDr9fpgYqaihabCaVuW10fiP/wX1/4J7QfBD4jJ8XPCVm0PhzxbdeVrlvGP3dhftgrKoH3UmKk4xxKGOcOAPzfr+p79oH4FaH+0h8HfEHgjxLCbrSPEVm9rOMDfGSdyup7MjhWU9ig96/mT/aH+CGr/ALNvxs8TeBtcVRqXhq+ktJH2lVmjUjbOBz8jKVccn5WBruoT5tGfhPHmQrC4lYqkvdm7PyZxtej/ALJn7RWpfso/tFeF/HumM6y+H73zJ4UYgXdsxPnRN7SKzDvgkHBwK84oGFbIyMcA55x6ZrVxT3PhcLialGsq0HZrY/q5+HnxF074oeBtJ8RaNPFd6VrVrFe2syvxJFIodW+u08jscjtW5uPzcV+bP/Buf+1XJ8TP2e9b+GWqXIk1LwDcCbT97fM9jO74VR3EcocZz0kjHFfpMx2HHXNefKKW5/TeTZisdhIYpddxu35cnqf0r5N/bu+Mr6lrUXhCzlzb2oE1/jje55jT8FIJ9c19YXcm2KQ/3RX5vfEvxPJ4v+Iut6jKwY3V08gP90DhBnvhQB+Ffz74+8R1svyeGDw8uWVZ2b7JdD9A4RwcK+KlVmrqJiEg5wApPBK8HHpSUUV/ETTvzt3Z+q2CiiigYVr+AfGl58O/F1jrNg7LcWUokxuIEqjrGf8AZNZFFdOAxdTBV44mg3GUfh8n3MauHhUpypzV0z9KfBnjG28b+E7HV7J/Mtr+FZUbPYjP+P5VtKwxXhP7BfiObW/g3LaSsp/sq9kt4uOiYV//AEJ2P04r3IRkOxz1Ff6V8HZu80ybD4+W84K/qfiGYYb2GJqUu2xLRRRX1ByBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFADWOCPU/pX5D/8HJP7I62V94Z+M2k27A3DDQtbMa4G8BmtpmHckCSMsewiFfrw45avnb/gqd4G0/4g/wDBPr4wWWqRNNDZeHLrVICDho57dGuImB9pIk/DI71tSk1LQ+e4oy2njMtqU57pX+Z/NlRRRXcfzS1bQ+o/+COP7Rh/Zq/by8G3dxdeRpPilz4fv9xwojujGsZJ7YnW3PsM/j/RekokCtkHI/8A11/JppE0ltqFtPFI8UwdZFdDhkYknIPYgnI9CBX9U/wk8QXPif4XeF9Ru2V7rUdLtrmdgMbneDcx/OuWtFH7L4aY2cqNShLaNvxOhukLWrjPLLX5l6naGwvrmGT/AFkE0sT+pIOM1+nEnf8A3a/O74/aZDpfxm8RwwJsjF/Ngf8AAq/lX6SWFvhMJiU/tS/Q/ojgmt+/qRORooor+Qz9KCiiigAooooh7zSA+uP+CeNqyfDzXLj7ySaiIwOmCI48n8mH5V9FEZYV5B+xLpcGn/AHTXiQK1zNPLIf7zC4dAf++UUfhXsB+8K/0d8M8L9W4awkb393/M/Es5qe1x9Vi0UUV9+eYFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAH/2Q==';
        $backgroundv3 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/4iOISUNDX1BST0ZJTEUAAQEAACN4bGNtcwIQAABtbnRyUkdCIFhZWiAH3wALAAoADAASADhhY3NwKm5peAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA9tYAAQAAAADTLWxjbXMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAtkZXNjAAABCAAAALBjcHJ0AAABuAAAARJ3dHB0AAACzAAAABRjaGFkAAAC4AAAACxyWFlaAAADDAAAABRiWFlaAAADIAAAABRnWFlaAAADNAAAABRyVFJDAAADSAAAIAxnVFJDAAADSAAAIAxiVFJDAAADSAAAIAxjaHJtAAAjVAAAACRkZXNjAAAAAAAAABxzUkdCLWVsbGUtVjItc3JnYnRyYy5pY2MAAAAAAAAAAAAAAB0AcwBSAEcAQgAtAGUAbABsAGUALQBWADIALQBzAHIAZwBiAHQAcgBjAC4AaQBjAGMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHRleHQAAAAAQ29weXJpZ2h0IDIwMTUsIEVsbGUgU3RvbmUgKHdlYnNpdGU6IGh0dHA6Ly9uaW5lZGVncmVlc2JlbG93LmNvbS87IGVtYWlsOiBlbGxlc3RvbmVAbmluZWRlZ3JlZXNiZWxvdy5jb20pLiBUaGlzIElDQyBwcm9maWxlIGlzIGxpY2Vuc2VkIHVuZGVyIGEgQ3JlYXRpdmUgQ29tbW9ucyBBdHRyaWJ1dGlvbi1TaGFyZUFsaWtlIDMuMCBVbnBvcnRlZCBMaWNlbnNlIChodHRwczovL2NyZWF0aXZlY29tbW9ucy5vcmcvbGljZW5zZXMvYnktc2EvMy4wL2xlZ2FsY29kZSkuAAAAAFhZWiAAAAAAAAD21gABAAAAANMtc2YzMgAAAAAAAQxCAAAF3v//8yUAAAeTAAD9kP//+6H///2iAAAD3AAAwG5YWVogAAAAAAAAb6AAADj1AAADkFhZWiAAAAAAAAAknwAAD4QAALbEWFlaIAAAAAAAAGKXAAC3hwAAGNljdXJ2AAAAAAAAEAAAAAABAAIABAAFAAYABwAJAAoACwAMAA4ADwAQABEAEwAUABUAFgAYABkAGgAbABwAHgAfACAAIQAjACQAJQAmACgAKQAqACsALQAuAC8AMAAyADMANAA1ADcAOAA5ADoAOwA9AD4APwBAAEIAQwBEAEUARwBIAEkASgBMAE0ATgBPAFEAUgBTAFQAVQBXAFgAWQBaAFwAXQBeAF8AYQBiAGMAZABmAGcAaABpAGsAbABtAG4AbwBxAHIAcwB0AHYAdwB4AHkAewB8AH0AfgCAAIEAggCDAIUAhgCHAIgAiQCLAIwAjQCOAJAAkQCSAJMAlQCWAJcAmACaAJsAnACdAJ8AoAChAKIApAClAKYApwCoAKoAqwCsAK0ArwCwALEAsgC0ALUAtgC3ALkAugC7ALwAvgC/AMAAwQDCAMQAxQDGAMcAyQDKAMsAzADOAM8A0ADRANMA1ADVANcA2ADZANoA3ADdAN4A4ADhAOIA5ADlAOYA6ADpAOoA7ADtAO8A8ADxAPMA9AD2APcA+AD6APsA/QD+AP8BAQECAQQBBQEHAQgBCgELAQ0BDgEPAREBEgEUARUBFwEYARoBGwEdAR8BIAEiASMBJQEmASgBKQErAS0BLgEwATEBMwE0ATYBOAE5ATsBPAE+AUABQQFDAUUBRgFIAUoBSwFNAU8BUAFSAVQBVQFXAVkBWgFcAV4BYAFhAWMBZQFnAWgBagFsAW4BbwFxAXMBdQF2AXgBegF8AX4BfwGBAYMBhQGHAYkBigGMAY4BkAGSAZQBlgGXAZkBmwGdAZ8BoQGjAaUBpwGpAasBrAGuAbABsgG0AbYBuAG6AbwBvgHAAcIBxAHGAcgBygHMAc4B0AHSAdQB1gHYAdoB3AHeAeEB4wHlAecB6QHrAe0B7wHxAfMB9QH4AfoB/AH+AgACAgIEAgcCCQILAg0CDwISAhQCFgIYAhoCHQIfAiECIwIlAigCKgIsAi4CMQIzAjUCOAI6AjwCPgJBAkMCRQJIAkoCTAJPAlECUwJWAlgCWgJdAl8CYQJkAmYCaQJrAm0CcAJyAnUCdwJ5AnwCfgKBAoMChgKIAosCjQKQApIClQKXApoCnAKfAqECpAKmAqkCqwKuArACswK1ArgCuwK9AsACwgLFAsgCygLNAs8C0gLVAtcC2gLdAt8C4gLkAucC6gLsAu8C8gL1AvcC+gL9Av8DAgMFAwgDCgMNAxADEwMVAxgDGwMeAyADIwMmAykDLAMuAzEDNAM3AzoDPQM/A0IDRQNIA0sDTgNRA1QDVgNZA1wDXwNiA2UDaANrA24DcQN0A3cDegN9A4ADggOFA4gDiwOOA5EDlAOYA5sDngOhA6QDpwOqA60DsAOzA7YDuQO8A78DwgPFA8kDzAPPA9ID1QPYA9sD3wPiA+UD6APrA+4D8gP1A/gD+wP+BAIEBQQIBAsEDwQSBBUEGAQcBB8EIgQlBCkELAQvBDMENgQ5BD0EQARDBEcESgRNBFEEVARXBFsEXgRiBGUEaARsBG8EcwR2BHkEfQSABIQEhwSLBI4EkgSVBJkEnASgBKMEpwSqBK4EsQS1BLgEvAS/BMMExgTKBM4E0QTVBNgE3ATgBOME5wTqBO4E8gT1BPkE/QUABQQFCAULBQ8FEwUWBRoFHgUiBSUFKQUtBTEFNAU4BTwFQAVDBUcFSwVPBVIFVgVaBV4FYgVmBWkFbQVxBXUFeQV9BYEFhAWIBYwFkAWUBZgFnAWgBaQFqAWsBa8FswW3BbsFvwXDBccFywXPBdMF1wXbBd8F4wXnBesF7wX0BfgF/AYABgQGCAYMBhAGFAYYBhwGIQYlBikGLQYxBjUGOQY+BkIGRgZKBk4GUwZXBlsGXwZjBmgGbAZwBnQGeQZ9BoEGhQaKBo4GkgaXBpsGnwakBqgGrAaxBrUGuQa+BsIGxgbLBs8G1AbYBtwG4QblBuoG7gbyBvcG+wcABwQHCQcNBxIHFgcbBx8HJAcoBy0HMQc2BzoHPwdDB0gHTQdRB1YHWgdfB2MHaAdtB3EHdgd7B38HhAeJB40HkgeXB5sHoAelB6kHrgezB7cHvAfBB8YHygfPB9QH2QfdB+IH5wfsB/EH9Qf6B/8IBAgJCA0IEggXCBwIIQgmCCsILwg0CDkIPghDCEgITQhSCFcIXAhhCGYIawhwCHUIegh/CIQIiQiOCJMImAidCKIIpwisCLEItgi7CMAIxQjKCM8I1AjZCN8I5AjpCO4I8wj4CP0JAwkICQ0JEgkXCR0JIgknCSwJMQk3CTwJQQlGCUwJUQlWCVsJYQlmCWsJcQl2CXsJgQmGCYsJkQmWCZsJoQmmCasJsQm2CbwJwQnGCcwJ0QnXCdwJ4gnnCe0J8gn4Cf0KAgoICg0KEwoZCh4KJAopCi8KNAo6Cj8KRQpKClAKVgpbCmEKZgpsCnIKdwp9CoMKiAqOCpQKmQqfCqUKqgqwCrYKvArBCscKzQrTCtgK3grkCuoK7wr1CvsLAQsHCwwLEgsYCx4LJAsqCy8LNQs7C0ELRwtNC1MLWQtfC2QLagtwC3YLfAuCC4gLjguUC5oLoAumC6wLsgu4C74LxAvKC9AL1gvcC+IL6QvvC/UL+wwBDAcMDQwTDBkMIAwmDCwMMgw4DD4MRQxLDFEMVwxdDGQMagxwDHYMfQyDDIkMjwyWDJwMogyoDK8MtQy7DMIMyAzODNUM2wzhDOgM7gz1DPsNAQ0IDQ4NFQ0bDSENKA0uDTUNOw1CDUgNTw1VDVwNYg1pDW8Ndg18DYMNiQ2QDZYNnQ2kDaoNsQ23Db4NxQ3LDdIN2Q3fDeYN7A3zDfoOAQ4HDg4OFQ4bDiIOKQ4vDjYOPQ5EDkoOUQ5YDl8OZg5sDnMOeg6BDogOjg6VDpwOow6qDrEOuA6+DsUOzA7TDtoO4Q7oDu8O9g79DwQPCw8SDxkPIA8nDy4PNQ88D0MPSg9RD1gPXw9mD20PdA97D4IPiQ+QD5gPnw+mD60PtA+7D8IPyg/RD9gP3w/mD+0P9Q/8EAMQChASEBkQIBAnEC8QNhA9EEQQTBBTEFoQYhBpEHAQeBB/EIYQjhCVEJ0QpBCrELMQuhDCEMkQ0BDYEN8Q5xDuEPYQ/REFEQwRFBEbESMRKhEyETkRQRFIEVARVxFfEWcRbhF2EX0RhRGNEZQRnBGkEasRsxG7EcIRyhHSEdkR4RHpEfAR+BIAEggSDxIXEh8SJxIuEjYSPhJGEk4SVRJdEmUSbRJ1En0ShBKMEpQSnBKkEqwStBK8EsQSzBLUEtsS4xLrEvMS+xMDEwsTExMbEyMTKxMzEzsTRBNME1QTXBNkE2wTdBN8E4QTjBOUE50TpROtE7UTvRPFE80T1hPeE+YT7hP2E/8UBxQPFBcUIBQoFDAUOBRBFEkUURRaFGIUahRzFHsUgxSMFJQUnBSlFK0UthS+FMYUzxTXFOAU6BTxFPkVARUKFRIVGxUjFSwVNBU9FUUVThVXFV8VaBVwFXkVgRWKFZMVmxWkFawVtRW+FcYVzxXYFeAV6RXyFfoWAxYMFhQWHRYmFi8WNxZAFkkWUhZaFmMWbBZ1Fn4WhhaPFpgWoRaqFrMWuxbEFs0W1hbfFugW8Rb6FwMXDBcUFx0XJhcvFzgXQRdKF1MXXBdlF24XdxeAF4kXkhecF6UXrhe3F8AXyRfSF9sX5BftF/cYABgJGBIYGxgkGC4YNxhAGEkYUhhcGGUYbhh3GIEYihiTGJwYphivGLgYwhjLGNQY3hjnGPAY+hkDGQwZFhkfGSkZMhk7GUUZThlYGWEZaxl0GX4ZhxmRGZoZpBmtGbcZwBnKGdMZ3RnmGfAZ+hoDGg0aFhogGioaMxo9GkYaUBpaGmMabRp3GoEaihqUGp4apxqxGrsaxRrOGtga4hrsGvUa/xsJGxMbHRsnGzAbOhtEG04bWBtiG2wbdRt/G4kbkxudG6cbsRu7G8UbzxvZG+Mb7Rv3HAEcCxwVHB8cKRwzHD0cRxxRHFscZRxwHHochByOHJgcohysHLYcwRzLHNUc3xzpHPQc/h0IHRIdHB0nHTEdOx1FHVAdWh1kHW8deR2DHY4dmB2iHa0dtx3BHcwd1h3hHesd9R4AHgoeFR4fHioeNB4+HkkeUx5eHmgecx59Hogekx6dHqgesh69Hsce0h7cHuce8h78HwcfEh8cHycfMh88H0cfUh9cH2cfch98H4cfkh+dH6cfsh+9H8gf0h/dH+gf8x/+IAggEyAeICkgNCA/IEogVCBfIGogdSCAIIsgliChIKwgtyDCIM0g2CDjIO4g+SEEIQ8hGiElITAhOyFGIVEhXCFnIXIhfiGJIZQhnyGqIbUhwCHMIdch4iHtIfgiBCIPIhoiJSIwIjwiRyJSIl4iaSJ0In8iiyKWIqEirSK4IsMizyLaIuYi8SL8IwgjEyMfIyojNSNBI0wjWCNjI28jeiOGI5EjnSOoI7QjvyPLI9Yj4iPuI/kkBSQQJBwkKCQzJD8kSyRWJGIkbiR5JIUkkSScJKgktCS/JMsk1yTjJO4k+iUGJRIlHiUpJTUlQSVNJVklZSVwJXwliCWUJaAlrCW4JcQl0CXcJecl8yX/JgsmFyYjJi8mOyZHJlMmXyZrJncmhCaQJpwmqCa0JsAmzCbYJuQm8Cb9JwknFSchJy0nOSdGJ1InXidqJ3YngyePJ5snpye0J8AnzCfZJ+Un8Sf9KAooFigjKC8oOyhIKFQoYChtKHkohiiSKJ4oqyi3KMQo0CjdKOko9ikCKQ8pGykoKTQpQSlNKVopZylzKYApjCmZKaYpsim/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ZNRk6WT+ZRNlKWU+ZVNlaGV9ZZNlqGW9ZdJl6GX9ZhJmJ2Y9ZlJmZ2Z9ZpJmp2a9ZtJm6Gb9ZxJnKGc9Z1NnaGd+Z5NnqWe+Z9Rn6Wf/aBRoKmg/aFVoamiAaJZoq2jBaNZo7GkCaRdpLWlDaVhpbmmEaZlpr2nFadtp8GoGahxqMmpIal1qc2qJap9qtWrKauBq9msMayJrOGtOa2RremuQa6ZrvGvSa+hr/mwUbCpsQGxWbGxsgmyYbK5sxGzabPBtBm0cbTNtSW1fbXVti22hbbhtzm3kbfpuEW4nbj1uU25qboBulm6tbsNu2W7wbwZvHG8zb0lvYG92b4xvo2+5b9Bv5m/9cBNwKnBAcFdwbXCEcJpwsXDHcN5w9HELcSJxOHFPcWZxfHGTcapxwHHXce5yBHIbcjJySHJfcnZyjXKkcrpy0XLocv9zFnMsc0NzWnNxc4hzn3O2c81z5HP6dBF0KHQ/dFZ0bXSEdJt0snTJdOB093UOdSZ1PXVUdWt1gnWZdbB1x3XedfZ2DXYkdjt2UnZqdoF2mHavdsd23nb1dwx3JHc7d1J3aneBd5h3sHfHd9539ngNeCV4PHhUeGt4gniaeLF4yXjgePh5D3kneT55VnlueYV5nXm0ecx543n7ehN6KnpCelp6cXqJeqF6uHrQeuh7AHsXey97R3tfe3Z7jnume7571nvufAV8HXw1fE18ZXx9fJV8rXzFfNx89H0MfSR9PH1UfWx9hH2cfbR9zX3lff1+FX4tfkV+XX51fo1+pX6+ftZ+7n8Gfx5/N39Pf2d/f3+Xf7B/yH/gf/mAEYApgEGAWoBygIqAo4C7gNSA7IEEgR2BNYFOgWaBf4GXgbCByIHhgfmCEoIqgkOCW4J0goyCpYK+gtaC74MHgyCDOYNRg2qDg4Obg7SDzYPlg/6EF4QwhEiEYYR6hJOErITEhN2E9oUPhSiFQYVahXKFi4Wkhb2F1oXvhgiGIYY6hlOGbIaFhp6Gt4bQhumHAocbhzSHTYdnh4CHmYeyh8uH5If9iBeIMIhJiGKIe4iViK6Ix4jgiPqJE4ksiUaJX4l4iZGJq4nEid6J94oQiiqKQ4pdinaKj4qpisKK3Ir1iw+LKItCi1uLdYuOi6iLwovbi/WMDowojEKMW4x1jI+MqIzCjNyM9Y0PjSmNQo1cjXaNkI2pjcON3Y33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';
        $backgroundv4 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/4iOISUNDX1BST0ZJTEUAAQEAACN4bGNtcwIQAABtbnRyUkdCIFhZWiAH3wALAAoADAASADhhY3NwKm5peAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA9tYAAQAAAADTLWxjbXMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAtkZXNjAAABCAAAALBjcHJ0AAABuAAAARJ3dHB0AAACzAAAABRjaGFkAAAC4AAAACxyWFlaAAADDAAAABRiWFlaAAADIAAAABRnWFlaAAADNAAAABRyVFJDAAADSAAAIAxnVFJDAAADSAAAIAxiVFJDAAADSAAAIAxjaHJtAAAjVAAAACRkZXNjAAAAAAAAABxzUkdCLWVsbGUtVjItc3JnYnRyYy5pY2MAAAAAAAAAAAAAAB0AcwBSAEcAQgAtAGUAbABsAGUALQBWADIALQBzAHIAZwBiAHQAcgBjAC4AaQBjAGMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHRleHQAAAAAQ29weXJpZ2h0IDIwMTUsIEVsbGUgU3RvbmUgKHdlYnNpdGU6IGh0dHA6Ly9uaW5lZGVncmVlc2JlbG93LmNvbS87IGVtYWlsOiBlbGxlc3RvbmVAbmluZWRlZ3JlZXNiZWxvdy5jb20pLiBUaGlzIElDQyBwcm9maWxlIGlzIGxpY2Vuc2VkIHVuZGVyIGEgQ3JlYXRpdmUgQ29tbW9ucyBBdHRyaWJ1dGlvbi1TaGFyZUFsaWtlIDMuMCBVbnBvcnRlZCBMaWNlbnNlIChodHRwczovL2NyZWF0aXZlY29tbW9ucy5vcmcvbGljZW5zZXMvYnktc2EvMy4wL2xlZ2FsY29kZSkuAAAAAFhZWiAAAAAAAAD21gABAAAAANMtc2YzMgAAAAAAAQxCAAAF3v//8yUAAAeTAAD9kP//+6H///2iAAAD3AAAwG5YWVogAAAAAAAAb6AAADj1AAADkFhZWiAAAAAAAAAknwAAD4QAALbEWFlaIAAAAAAAAGKXAAC3hwAAGNljdXJ2AAAAAAAAEAAAAAABAAIABAAFAAYABwAJAAoACwAMAA4ADwAQABEAEwAUABUAFgAYABkAGgAbABwAHgAfACAAIQAjACQAJQAmACgAKQAqACsALQAuAC8AMAAyADMANAA1ADcAOAA5ADoAOwA9AD4APwBAAEIAQwBEAEUARwBIAEkASgBMAE0ATgBPAFEAUgBTAFQAVQBXAFgAWQBaAFwAXQBeAF8AYQBiAGMAZABmAGcAaABpAGsAbABtAG4AbwBxAHIAcwB0AHYAdwB4AHkAewB8AH0AfgCAAIEAggCDAIUAhgCHAIgAiQCLAIwAjQCOAJAAkQCSAJMAlQCWAJcAmACaAJsAnACdAJ8AoAChAKIApAClAKYApwCoAKoAqwCsAK0ArwCwALEAsgC0ALUAtgC3ALkAugC7ALwAvgC/AMAAwQDCAMQAxQDGAMcAyQDKAMsAzADOAM8A0ADRANMA1ADVANcA2ADZANoA3ADdAN4A4ADhAOIA5ADlAOYA6ADpAOoA7ADtAO8A8ADxAPMA9AD2APcA+AD6APsA/QD+AP8BAQECAQQBBQEHAQgBCgELAQ0BDgEPAREBEgEUARUBFwEYARoBGwEdAR8BIAEiASMBJQEmASgBKQErAS0BLgEwATEBMwE0ATYBOAE5ATsBPAE+AUABQQFDAUUBRgFIAUoBSwFNAU8BUAFSAVQBVQFXAVkBWgFcAV4BYAFhAWMBZQFnAWgBagFsAW4BbwFxAXMBdQF2AXgBegF8AX4BfwGBAYMBhQGHAYkBigGMAY4BkAGSAZQBlgGXAZkBmwGdAZ8BoQGjAaUBpwGpAasBrAGuAbABsgG0AbYBuAG6AbwBvgHAAcIBxAHGAcgBygHMAc4B0AHSAdQB1gHYAdoB3AHeAeEB4wHlAecB6QHrAe0B7wHxAfMB9QH4AfoB/AH+AgACAgIEAgcCCQILAg0CDwISAhQCFgIYAhoCHQIfAiECIwIlAigCKgIsAi4CMQIzAjUCOAI6AjwCPgJBAkMCRQJIAkoCTAJPAlECUwJWAlgCWgJdAl8CYQJkAmYCaQJrAm0CcAJyAnUCdwJ5AnwCfgKBAoMChgKIAosCjQKQApIClQKXApoCnAKfAqECpAKmAqkCqwKuArACswK1ArgCuwK9AsACwgLFAsgCygLNAs8C0gLVAtcC2gLdAt8C4gLkAucC6gLsAu8C8gL1AvcC+gL9Av8DAgMFAwgDCgMNAxADEwMVAxgDGwMeAyADIwMmAykDLAMuAzEDNAM3AzoDPQM/A0IDRQNIA0sDTgNRA1QDVgNZA1wDXwNiA2UDaANrA24DcQN0A3cDegN9A4ADggOFA4gDiwOOA5EDlAOYA5sDngOhA6QDpwOqA60DsAOzA7YDuQO8A78DwgPFA8kDzAPPA9ID1QPYA9sD3wPiA+UD6APrA+4D8gP1A/gD+wP+BAIEBQQIBAsEDwQSBBUEGAQcBB8EIgQlBCkELAQvBDMENgQ5BD0EQARDBEcESgRNBFEEVARXBFsEXgRiBGUEaARsBG8EcwR2BHkEfQSABIQEhwSLBI4EkgSVBJkEnASgBKMEpwSqBK4EsQS1BLgEvAS/BMMExgTKBM4E0QTVBNgE3ATgBOME5wTqBO4E8gT1BPkE/QUABQQFCAULBQ8FEwUWBRoFHgUiBSUFKQUtBTEFNAU4BTwFQAVDBUcFSwVPBVIFVgVaBV4FYgVmBWkFbQVxBXUFeQV9BYEFhAWIBYwFkAWUBZgFnAWgBaQFqAWsBa8FswW3BbsFvwXDBccFywXPBdMF1wXbBd8F4wXnBesF7wX0BfgF/AYABgQGCAYMBhAGFAYYBhwGIQYlBikGLQYxBjUGOQY+BkIGRgZKBk4GUwZXBlsGXwZjBmgGbAZwBnQGeQZ9BoEGhQaKBo4GkgaXBpsGnwakBqgGrAaxBrUGuQa+BsIGxgbLBs8G1AbYBtwG4QblBuoG7gbyBvcG+wcABwQHCQcNBxIHFgcbBx8HJAcoBy0HMQc2BzoHPwdDB0gHTQdRB1YHWgdfB2MHaAdtB3EHdgd7B38HhAeJB40HkgeXB5sHoAelB6kHrgezB7cHvAfBB8YHygfPB9QH2QfdB+IH5wfsB/EH9Qf6B/8IBAgJCA0IEggXCBwIIQgmCCsILwg0CDkIPghDCEgITQhSCFcIXAhhCGYIawhwCHUIegh/CIQIiQiOCJMImAidCKIIpwisCLEItgi7CMAIxQjKCM8I1AjZCN8I5AjpCO4I8wj4CP0JAwkICQ0JEgkXCR0JIgknCSwJMQk3CTwJQQlGCUwJUQlWCVsJYQlmCWsJcQl2CXsJgQmGCYsJkQmWCZsJoQmmCasJsQm2CbwJwQnGCcwJ0QnXCdwJ4gnnCe0J8gn4Cf0KAgoICg0KEwoZCh4KJAopCi8KNAo6Cj8KRQpKClAKVgpbCmEKZgpsCnIKdwp9CoMKiAqOCpQKmQqfCqUKqgqwCrYKvArBCscKzQrTCtgK3grkCuoK7wr1CvsLAQsHCwwLEgsYCx4LJAsqCy8LNQs7C0ELRwtNC1MLWQtfC2QLagtwC3YLfAuCC4gLjguUC5oLoAumC6wLsgu4C74LxAvKC9AL1gvcC+IL6QvvC/UL+wwBDAcMDQwTDBkMIAwmDCwMMgw4DD4MRQxLDFEMVwxdDGQMagxwDHYMfQyDDIkMjwyWDJwMogyoDK8MtQy7DMIMyAzODNUM2wzhDOgM7gz1DPsNAQ0IDQ4NFQ0bDSENKA0uDTUNOw1CDUgNTw1VDVwNYg1pDW8Ndg18DYMNiQ2QDZYNnQ2kDaoNsQ23Db4NxQ3LDdIN2Q3fDeYN7A3zDfoOAQ4HDg4OFQ4bDiIOKQ4vDjYOPQ5EDkoOUQ5YDl8OZg5sDnMOeg6BDogOjg6VDpwOow6qDrEOuA6+DsUOzA7TDtoO4Q7oDu8O9g79DwQPCw8SDxkPIA8nDy4PNQ88D0MPSg9RD1gPXw9mD20PdA97D4IPiQ+QD5gPnw+mD60PtA+7D8IPyg/RD9gP3w/mD+0P9Q/8EAMQChASEBkQIBAnEC8QNhA9EEQQTBBTEFoQYhBpEHAQeBB/EIYQjhCVEJ0QpBCrELMQuhDCEMkQ0BDYEN8Q5xDuEPYQ/REFEQwRFBEbESMRKhEyETkRQRFIEVARVxFfEWcRbhF2EX0RhRGNEZQRnBGkEasRsxG7EcIRyhHSEdkR4RHpEfAR+BIAEggSDxIXEh8SJxIuEjYSPhJGEk4SVRJdEmUSbRJ1En0ShBKMEpQSnBKkEqwStBK8EsQSzBLUEtsS4xLrEvMS+xMDEwsTExMbEyMTKxMzEzsTRBNME1QTXBNkE2wTdBN8E4QTjBOUE50TpROtE7UTvRPFE80T1hPeE+YT7hP2E/8UBxQPFBcUIBQoFDAUOBRBFEkUURRaFGIUahRzFHsUgxSMFJQUnBSlFK0UthS+FMYUzxTXFOAU6BTxFPkVARUKFRIVGxUjFSwVNBU9FUUVThVXFV8VaBVwFXkVgRWKFZMVmxWkFawVtRW+FcYVzxXYFeAV6RXyFfoWAxYMFhQWHRYmFi8WNxZAFkkWUhZaFmMWbBZ1Fn4WhhaPFpgWoRaqFrMWuxbEFs0W1hbfFugW8Rb6FwMXDBcUFx0XJhcvFzgXQRdKF1MXXBdlF24XdxeAF4kXkhecF6UXrhe3F8AXyRfSF9sX5BftF/cYABgJGBIYGxgkGC4YNxhAGEkYUhhcGGUYbhh3GIEYihiTGJwYphivGLgYwhjLGNQY3hjnGPAY+hkDGQwZFhkfGSkZMhk7GUUZThlYGWEZaxl0GX4ZhxmRGZoZpBmtGbcZwBnKGdMZ3RnmGfAZ+hoDGg0aFhogGioaMxo9GkYaUBpaGmMabRp3GoEaihqUGp4apxqxGrsaxRrOGtga4hrsGvUa/xsJGxMbHRsnGzAbOhtEG04bWBtiG2wbdRt/G4kbkxudG6cbsRu7G8UbzxvZG+Mb7Rv3HAEcCxwVHB8cKRwzHD0cRxxRHFscZRxwHHochByOHJgcohysHLYcwRzLHNUc3xzpHPQc/h0IHRIdHB0nHTEdOx1FHVAdWh1kHW8deR2DHY4dmB2iHa0dtx3BHcwd1h3hHesd9R4AHgoeFR4fHioeNB4+HkkeUx5eHmgecx59Hogekx6dHqgesh69Hsce0h7cHuce8h78HwcfEh8cHycfMh88H0cfUh9cH2cfch98H4cfkh+dH6cfsh+9H8gf0h/dH+gf8x/+IAggEyAeICkgNCA/IEogVCBfIGogdSCAIIsgliChIKwgtyDCIM0g2CDjIO4g+SEEIQ8hGiElITAhOyFGIVEhXCFnIXIhfiGJIZQhnyGqIbUhwCHMIdch4iHtIfgiBCIPIhoiJSIwIjwiRyJSIl4iaSJ0In8iiyKWIqEirSK4IsMizyLaIuYi8SL8IwgjEyMfIyojNSNBI0wjWCNjI28jeiOGI5EjnSOoI7QjvyPLI9Yj4iPuI/kkBSQQJBwkKCQzJD8kSyRWJGIkbiR5JIUkkSScJKgktCS/JMsk1yTjJO4k+iUGJRIlHiUpJTUlQSVNJVklZSVwJXwliCWUJaAlrCW4JcQl0CXcJecl8yX/JgsmFyYjJi8mOyZHJlMmXyZrJncmhCaQJpwmqCa0JsAmzCbYJuQm8Cb9JwknFSchJy0nOSdGJ1InXidqJ3YngyePJ5snpye0J8AnzCfZJ+Un8Sf9KAooFigjKC8oOyhIKFQoYChtKHkohiiSKJ4oqyi3KMQo0CjdKOko9ikCKQ8pGykoKTQpQSlNKVopZylzKYApjCmZKaYpsim/Kcwp2CnlKfEp/ioLKhgqJCoxKj4qSipXKmQqcSp9KooqlyqkKrEqvSrKKtcq5CrxKv4rCisXKyQrMSs+K0srWCtlK3IrfyuMK5krpSuyK78rzCvZK+Yr8ywBLA4sGywoLDUsQixPLFwsaSx2LIMskCyeLKssuCzFLNIs3yztLPotBy0ULSEtLy08LUktVi1kLXEtfi2LLZktpi2zLcEtzi3bLekt9i4ELhEuHi4sLjkuRy5ULmEuby58Loouly6lLrIuwC7NLtsu6C72LwMvES8eLywvOi9HL1UvYi9wL34viy+ZL6cvtC/CL9Av3S/rL/kwBjAUMCIwLzA9MEswWTBnMHQwgjCQMJ4wrDC5MMcw1TDjMPEw/zENMRoxKDE2MUQxUjFgMW4xfDGKMZgxpjG0McIx0DHeMewx+jIIMhYyJDIyMkAyTjJcMmoyeTKHMpUyozKxMr8yzTLcMuoy+DMGMxQzIzMxMz8zTTNcM2ozeDOGM5UzozOxM8AzzjPcM+sz+TQHNBY0JDQzNEE0TzReNGw0ezSJNJg0pjS1NMM00jTgNO80/TUMNRo1KTU3NUY1VDVjNXI1gDWPNZ01rDW7Nck12DXnNfU2BDYTNiE2MDY/Nk42XDZrNno2iTaXNqY2tTbENtM24TbwNv83DjcdNyw3OzdJN1g3Zzd2N4U3lDejN7I3wTfQN9837jf9OAw4GzgqODk4SDhXOGY4dTiEOJM4ojixOME40DjfOO44/TkMORs5Kzk6OUk5WDlnOXc5hjmVOaQ5tDnDOdI54TnxOgA6DzofOi46PTpNOlw6azp7Ooo6mjqpOrg6yDrXOuc69jsGOxU7JTs0O0Q7UztjO3I7gjuRO6E7sDvAO9A73zvvO/48DjwePC08PTxNPFw8bDx8PIs8mzyrPLo8yjzaPOo8+T0JPRk9KT05PUg9WD1oPXg9iD2YPac9tz3HPdc95z33Pgc+Fz4nPjc+Rz5XPmc+dz6HPpc+pz63Psc+1z7nPvc/Bz8XPyc/Nz9HP1c/Zz94P4g/mD+oP7g/yD/ZP+k/+UAJQBlAKkA6QEpAWkBrQHtAi0CcQKxAvEDNQN1A7UD+QQ5BHkEvQT9BT0FgQXBBgUGRQaJBskHDQdNB5EH0QgVCFUImQjZCR0JXQmhCeEKJQppCqkK7QstC3ELtQv1DDkMfQy9DQENRQ2FDckODQ5RDpEO1Q8ZD10PnQ/hECUQaRCtEO0RMRF1EbkR/RJBEoUSyRMJE00TkRPVFBkUXRShFOUVKRVtFbEV9RY5Fn0WwRcFF0kXjRfRGBUYXRihGOUZKRltGbEZ9Ro9GoEaxRsJG00bkRvZHB0cYRylHO0dMR11HbkeAR5FHoke0R8VH1kfoR/lICkgcSC1IP0hQSGFIc0iESJZIp0i5SMpI3EjtSP9JEEkiSTNJRUlWSWhJekmLSZ1JrknASdJJ40n1SgZKGEoqSjtKTUpfSnFKgkqUSqZKt0rJSttK7Ur/SxBLIks0S0ZLWEtpS3tLjUufS7FLw0vVS+dL+UwKTBxMLkxATFJMZEx2TIhMmkysTL5M0EziTPRNBk0ZTStNPU1PTWFNc02FTZdNqU28Tc5N4E3yTgROF04pTjtOTU5fTnJOhE6WTqlOu07NTt9O8k8ETxZPKU87T05PYE9yT4VPl0+qT7xPzk/hT/NQBlAYUCtQPVBQUGJQdVCHUJpQrVC/UNJQ5FD3UQlRHFEvUUFRVFFnUXlRjFGfUbFRxFHXUelR/FIPUiJSNFJHUlpSbVKAUpJSpVK4UstS3lLxUwRTFlMpUzxTT1NiU3VTiFObU65TwVPUU+dT+lQNVCBUM1RGVFlUbFR/VJJUpVS4VMtU3lTyVQVVGFUrVT5VUVVlVXhVi1WeVbFVxVXYVetV/lYSViVWOFZLVl9WclaFVplWrFa/VtNW5lb6Vw1XIFc0V0dXW1duV4JXlVepV7xX0FfjV/dYClgeWDFYRVhYWGxYgFiTWKdYuljOWOJY9VkJWR1ZMFlEWVhZa1l/WZNZp1m6Wc5Z4ln2WglaHVoxWkVaWVpsWoBalFqoWrxa0FrkWvhbC1sfWzNbR1tbW29bg1uXW6tbv1vTW+db+1wPXCNcN1xLXGBcdFyIXJxcsFzEXNhc7F0BXRVdKV09XVFdZV16XY5dol22Xctd313zXgheHF4wXkReWV5tXoJell6qXr9e017nXvxfEF8lXzlfTl9iX3dfi1+gX7RfyV/dX/JgBmAbYC9gRGBYYG1ggmCWYKtgv2DUYOlg/WESYSdhO2FQYWVhemGOYaNhuGHNYeFh9mILYiBiNWJJYl5ic2KIYp1ismLHYtti8GMFYxpjL2NEY1ljbmODY5hjrWPCY9dj7GQBZBZkK2RAZFVkamR/ZJVkqmS/ZNRk6WT+ZRNlKWU+ZVNlaGV9ZZNlqGW9ZdJl6GX9ZhJmJ2Y9ZlJmZ2Z9ZpJmp2a9ZtJm6Gb9ZxJnKGc9Z1NnaGd+Z5NnqWe+Z9Rn6Wf/aBRoKmg/aFVoamiAaJZoq2jBaNZo7GkCaRdpLWlDaVhpbmmEaZlpr2nFadtp8GoGahxqMmpIal1qc2qJap9qtWrKauBq9msMayJrOGtOa2RremuQa6ZrvGvSa+hr/mwUbCpsQGxWbGxsgmyYbK5sxGzabPBtBm0cbTNtSW1fbXVti22hbbhtzm3kbfpuEW4nbj1uU25qboBulm6tbsNu2W7wbwZvHG8zb0lvYG92b4xvo2+5b9Bv5m/9cBNwKnBAcFdwbXCEcJpwsXDHcN5w9HELcSJxOHFPcWZxfHGTcapxwHHXce5yBHIbcjJySHJfcnZyjXKkcrpy0XLocv9zFnMsc0NzWnNxc4hzn3O2c81z5HP6dBF0KHQ/dFZ0bXSEdJt0snTJdOB093UOdSZ1PXVUdWt1gnWZdbB1x3XedfZ2DXYkdjt2UnZqdoF2mHavdsd23nb1dwx3JHc7d1J3aneBd5h3sHfHd9539ngNeCV4PHhUeGt4gniaeLF4yXjgePh5D3kneT55VnlueYV5nXm0ecx543n7ehN6KnpCelp6cXqJeqF6uHrQeuh7AHsXey97R3tfe3Z7jnume7571nvufAV8HXw1fE18ZXx9fJV8rXzFfNx89H0MfSR9PH1UfWx9hH2cfbR9zX3lff1+FX4tfkV+XX51fo1+pX6+ftZ+7n8Gfx5/N39Pf2d/f3+Xf7B/yH/gf/mAEYApgEGAWoBygIqAo4C7gNSA7IEEgR2BNYFOgWaBf4GXgbCByIHhgfmCEoIqgkOCW4J0goyCpYK+gtaC74MHgyCDOYNRg2qDg4Obg7SDzYPlg/6EF4QwhEiEYYR6hJOErITEhN2E9oUPhSiFQYVahXKFi4Wkhb2F1oXvhgiGIYY6hlOGbIaFhp6Gt4bQhumHAocbhzSHTYdnh4CHmYeyh8uH5If9iBeIMIhJiGKIe4iViK6Ix4jgiPqJE4ksiUaJX4l4iZGJq4nEid6J94oQiiqKQ4pdinaKj4qpisKK3Ir1iw+LKItCi1uLdYuOi6iLwovbi/WMDowojEKMW4x1jI+MqIzCjNyM9Y0PjSmNQo1cjXaNkI2pjcON3Y33jhGOK45Ejl6OeI6SjqyOxo7gjvqPE48tj0ePYY97j5WPr4/Jj+OP/ZAXkDGQS5BlkH+QmpC0kM6Q6JECkRyRNpFQkWuRhZGfkbmR05HukgiSIpI8kleScZKLkqaSwJLakvSTD5Mpk0STXpN4k5OTrZPIk+KT/JQXlDGUTJRmlIGUm5S2lNCU65UFlSCVO5VVlXCVipWllcCV2pX1lg+WKpZFll+WepaVlrCWypbllwCXG5c1l1CXa5eGl6GXu5fWl/GYDJgnmEKYXZh3mJKYrZjImOOY/pkZmTSZT5lqmYWZoJm7mdaZ8ZoMmieaQppemnmalJqvmsqa5ZsAmxybN5tSm22biJukm7+b2pv1nBGcLJxHnGOcfpyZnLWc0JzrnQedIp09nVmddJ2Qnaudxp3inf2eGZ40nlCea56HnqKevp7anvWfEZ8sn0ifY59/n5uftp/Sn+6gCaAloEGgXKB4oJSgsKDLoOehA6EfoTqhVqFyoY6hqqHGoeGh/aIZojWiUaJtoomipaLBot2i+aMVozGjTaNpo4WjoaO9o9mj9aQRpC2kSaRlpIGknqS6pNak8qUOpSqlR6VjpX+lm6W4pdSl8KYMpimmRaZhpn6mmqa2ptOm76cLpyinRKdgp32nmae2p9Kn76gLqCioRKhhqH2omqi2qNOo76kMqSmpRaliqX6pm6m4qdSp8aoOqiqqR6pkqoCqnaq6qteq86sQqy2rSqtnq4OroKu9q9qr96wUrDCsTaxqrIespKzBrN6s+60YrTWtUq1vrYytqa3GreOuAK4drjquV650rpKur67MrumvBq8jr0CvXq97r5ivta/Tr/CwDbAqsEiwZbCCsJ+wvbDasPexFbEysVCxbbGKsaixxbHjsgCyHrI7slmydrKUsrGyz7LsswqzJ7NFs2KzgLOes7uz2bP2tBS0MrRPtG20i7SotMa05LUCtR+1PbVbtXm1lrW0tdK18LYOtiy2SbZntoW2o7bBtt+2/bcbtzm3V7d1t5O3sbfPt+24C7gpuEe4ZbiDuKG4v7jduPu5Gbk4uVa5dLmSubC5zrntugu6KbpHuma6hLqiusC637r9uxu7OrtYu3a7lbuzu9G78LwOvC28S7xqvIi8przFvOO9Ar0gvT+9Xb18vZu9ub3Yvfa+Fb4zvlK+cb6Pvq6+zb7rvwq/Kb9Hv2a/hb+kv8K/4cAAwB/APsBcwHvAmsC5wNjA98EVwTTBU8FywZHBsMHPwe7CDcIswkvCasKJwqjCx8LmwwXDJMNDw2LDgcOgw8DD38P+xB3EPMRbxHvEmsS5xNjE98UXxTbFVcV1xZTFs8XSxfLGEcYwxlDGb8aPxq7GzcbtxwzHLMdLx2vHiseqx8nH6cgIyCjIR8hnyIbIpsjFyOXJBckkyUTJZMmDyaPJw8niygLKIspBymHKgcqhysDK4MsAyyDLQMtfy3/Ln8u/y9/L/8wfzD/MXsx+zJ7MvszezP7NHs0+zV7Nfs2ezb7N3s3+zh/OP85fzn/On86/zt/O/88gz0DPYM+Az6DPwc/h0AHQIdBC0GLQgtCi0MPQ49ED0STRRNFl0YXRpdHG0ebSB9In0kfSaNKI0qnSydLq0wrTK9NM02zTjdOt087T7tQP1DDUUNRx1JLUstTT1PTVFNU11VbVd9WX1bjV2dX61hrWO9Zc1n3Wnta/1t/XANch10LXY9eE16XXxtfn2AjYKdhK2GvYjNit2M7Y79kQ2THZUtlz2ZTZtdnW2fjaGdo62lvafNqe2r/a4NsB2yLbRNtl24bbqNvJ2+rcC9wt3E7cb9yR3LLc1Nz13RbdON1Z3XvdnN2+3d/eAd4i3kTeZd6H3qjeyt7s3w3fL99Q33LflN+139ff+eAa4DzgXuB/4KHgw+Dl4QbhKOFK4WzhjeGv4dHh8+IV4jfiWeJ64pzivuLg4wLjJONG42jjiuOs487j8OQS5DTkVuR45JrkvOTe5QHlI+VF5WflieWr5c3l8OYS5jTmVuZ55pvmvebf5wLnJOdG52nni+et59Dn8ugU6DfoWeh76J7owOjj6QXpKOlK6W3pj+my6dTp9+oZ6jzqXuqB6qTqxurp6wvrLutR63Prluu569zr/uwh7ETsZuyJ7Kzsz+zy7RTtN+1a7X3toO3D7eXuCO4r7k7uce6U7rfu2u797yDvQ+9m74nvrO/P7/LwFfA48FvwfvCh8MXw6PEL8S7xUfF08Zjxu/He8gHyJPJI8mvyjvKx8tXy+PMb8z/zYvOF86nzzPPw9BP0NvRa9H30ofTE9Oj1C/Uv9VL1dvWZ9b314PYE9if2S/Zv9pL2tvbZ9v33IfdE92j3jPew99P39/gb+D74YviG+Kr4zvjx+RX5Ofld+YH5pfnJ+ez6EPo0+lj6fPqg+sT66PsM+zD7VPt4+5z7wPvk/Aj8LPxQ/HX8mfy9/OH9Bf0p/U39cv2W/br93v4C/if+S/5v/pT+uP7c/wD/Jf9J/23/kv+2/9v//2Nocm0AAAAAAAMAAAAAo9cAAFR8AABMzQAAmZoAACZnAAAPXP/bAEMAAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/bAEMBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/CABEIAf4DWwMBIgACEQEDEQH/xAAeAAEAAQMFAQAAAAAAAAAAAAAACQEICgIDBAYHBf/EAB0BAQAABwEBAAAAAAAAAAAAAAABAgMEBQYHCAn/2gAMAwEAAhADEAAAAZ+AGmsIVaNRVprBVpI6lKxFKRjqbepDU06gAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABo10lh5JZ/ItYn5c3rq1OrV8u7/e37LGffV7G5l6U29zvuqNNdieHkUW1r9ul9w+SyU/GFuIQySVPBdO712D51lzyD0iTnlWkXZesOf8AV4qvLY/d+4VInJzjY9+nxOUvufA+/Y93CFQUQqpVEUQq01Qq01Rq01KtGoqppg1tGoqpVEpSMNSgqpUAKCrTUq01KqUi1KUhDUpSEdSiKqlUClIzalKywBEUjCrTUqpUBEAAAAAAAAAADT1btVLKpGz8O/WxjwF1zg/U+W5rnb6fVoxvavWHOr0+Nydr1JoWJ986SeNjI+ZmvRzY4vKR8Aj0mD5n6lj7d6+d8/fQHcL4vNfTvdPKcaDwyYKH3sfk5St2M+KnF9i43JxvqEIXHDsvuWxbq+g5RHpON1kZS5PxPpUbtptTUsobwG4GCmjuEjt3OLrlFz2LxT2yG6TLXf3mYmmWPVwmqz+PWP6bBTP+sQE0nwGV59eFOau167r2N7oEmU8Mpj1UuuNZYu9516La9k89tm6RBzX5xlgfRh0mGk2+0n0bHZvWraLOHZ/d5iqUthnZrANW557Pv2zHb9YhfZPXB5Vj9p1nmeu41NwVzy7JQrs71n2G33yewGwO85DPx9bHwTYzK2+/jlZGFv07f+T9OwOTJ9j9mxnbm7nl2R1ubO9adiBMAAAAAAAAAAAEG35Z6vpxFeND5khllXh7qXSBx3apRKVp9W/PnCgJn/iQr6PbNN9GHMJNQx4uDkWWgrDql5sMFpdKXJx1wuSqS7j6TCvNNHjVsIssjqD6eOfDbuvTqtOjBGPnWLblJ4tt5xvTK7Fbqq6NfhaX27qKvlGQTzrwUWnWbS8ovF0yiKuH3YbJkYbqWxRf5YOJ9lj1tTxPrj/ZrA6uiZSNucB11tLfJyfSLUrqbXpm7GhJdjqVNctg13SfTuuL3uya45+RXa9njzg7nDg9rc33chnHq9UnsOrXrWLX1RhOPa9dJSx9BxQxET7wDXnC5q7mti5q16xtQPTeYttTWdv5UpMbVxzLIy9/iEl0se8wWWe3h2J3vEMjuPW17x+XYPP8nvGxyh5Ml92AicPFwlyG782VSMOvzPJR9siZlhtO+bqlaeeAAAAAAAAAAAAA0cLnKEbWLe5KtrhO08jRq2e+6n59j2dutUv+Ee25DuLrc6p5G+nT5xrXoLu/WbDOF5m3b4kZGQr0r0twXxGP603Z2rmHcchvGz9Glq5Ru58j6+P9DBGfzvFryk8Wy745JvHvKn6XG3gz9I839JraBlFQvzQ2v2XoTHkyh8Wb3+65JklQH8uxKTLd1ynIb5iaezVtgin+NG2uujtkIs0q6j45lJ4reVDJsnnmMPL7D7Pg7iOd9RUwlrGTrjES/wAmZ9Fg7nBg+hQlmiZyT4sZcnH7IbHjIfW1OcmujXjfStkEAs/cAl3wrI+uZtluTt+wR9wZ3mWr3vDLsbdvaOlTW/w8mzExyM6G4xlWOXxWm1NR98s6yvMaCnk5R5K8dTIkk6LHXCXdtbhccru1th9z6JGjt5LuJfkmUt0uLUrZ9bBEAAAAAAAAAAIDb6Tjqvd9NmnReKbPIRWxD1PKW1znH39rtOrYn/zr+rBcl5hb+xfdKnatMu98g5D65sgbm389u0+oX0263He7uSYxvkEpEW3efIVa0uXmx88HsPE5eO9RBC46fj1ZIdKurx/3971ZMxCd5fP+qaq0btaO8WBxOZLtK+mYu99cztY2PyNz6ahv0OVieTzStouJp6dk91mxMfEgHIUOhwm+dz7KmsbNN+tLdYEe9TaKulWYRDZJFFx492jvNaezY7t4MsNJ9SpuUrS3i1WF7JJpV1Lwz2L6STYcci82WmlTUtHnvo9KW5438kEkFKmnxK23z91jDbsVvvpT2bG2myuWT4HHAvqlVTWu35d6so7njdyaSHVqai10rS3MEQAAAAAAAAAGmuim87sS735p4I60Vcd2WtCaaUTRr2/q558+fYvfjuz4ywi+TmaUOXTjcqjkuo8Tve3grzXq425n7PZsvvT0T2VgN8XYNMaW/r2PiyZHsLb0wn3nC3UOQ4ZHmOEmhzWxSEOQ2dSO42dEJuS2CG+42uLeU0QjuOJrjDkODuRhynG0QcxsaIOU0a05o2UvJbBHfcLcjLyWxRDkOPtQc1s7yc2KRl5DZ2zlOJqR5Li70G407ByWxqg3WztxjymxWMu80a4TgAAAAAAAAAAU4nL4thUjJ0cvifMDu4WNUCULa3tn6u+e8djk+e90vfPsjsYEtPU6e5Rcz1WaSbQy3I6z2HrNvumN/JJFHt3/AAKaCwO+mOKls+RfALPfjvSZ65Xxa/6PWrrMiXvFqPltLY7CsmbHUlmmxsYMicZHn9TXLyuJxJn5NkxnJdo88gJThx9RsT58cJMPDfLrDvDKekT844s+0uThnvUjV7fV1rIb2triWnaoDZtYHb7rjmcV2QXCFJXHG2H/ADblezJL5ceeWmOeFXIH9BjKkwodMixsquotWuOU3Ey8QgXxSbHDtPvCle/UwVn/AN677x+EJIIBJmoo4Xc6PukRUuFDf4yKfPjIrc9mJt3sxu1mk8Wn7x3chSjn4Fp+sc7IsmrcmAOfLHdhLebdlZjdiltZ9/tSuUnsos+6eW9gqaDkN9+8u9RsvQoQugAAAAAAAAAKbW7SSNkvi8j1ifiPp3TRwLcClIzSi7O9p+rnn3HOyHPo7s+s7dhN/VJMtG70uVVPifPPvdmrTz0DvcZrlbULALaZlay3+OhfBKLyp8dExankJFSHG3PIcJYm/OZqStABflIbpknhbmA7NWGRgKkevWTWUFfWp+aTY2Lfp0vNZb/HUvzk2rGjCb45kL6Y0bHoiMljjwuYcvGp+qqEW9s08WmFaODsF/8AqhkIDbqpRtU2NgNns3NcuViG9TkjRsoEeyTj6o2cVdp2QOLHfJpOkmWhE9glb1T2OPZkHbmqF/GbanO9oY+CX2OXfVGlGfH7kY7JCnJTcRqhk8aa6qaXfnwlscMeR1syX+PLMjcdRPBz0LIDRsfK/WtOqlugQrAAAAAAAAAAAbfzvqKMbeOi3guXZ61n031euXtdQ6BigAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAClUARAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA06tOpAEQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAKVEARAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANOpAAEQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAI/L8sTj3C745ky67LrybbqvIaNct6CIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGJO+38TJeS0kMb1IZHK9+3jmz92feO8NvcpbUCIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHjsJuQlsVNVxMErEU17wityNtyazyr+0wCz4WHovlCnsIImnSbjQhLraUI6lKxiEQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAClaIbEUksOzNiMS6shEfGQ85JnIYe3S32VNu+cei4/0lqaduFSvkfm1s3mfd/dfmeOPPO43T3BRrdv6dgJEdzrnYPYnOdYvJQiAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAoqOp40mUHHpX0KDZSt9wOT+ZPFRyirLuXZuhd8tp591i1rbpX5vdsCWcD3y8GOWRn2zyvfrp1egtSCMQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHy/qbUZMX3y6UyLK/8AM1ZzYMZAmSnStruT6FzP05H7WtPnD2sJZxWEewSO2b3k+2eV7ldOr0HqQRAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHzuhYO49OW99c0vJ3TrSORj7j5ePTkJY93ROAPXvIeRt/Lcsmvx/uYn1RajbXKB495h32yB7d8rz/uXk3avcrjOnYDj9jV9jc4pWlbyQIgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIx+FV8rO/KVW0FaJJkP0wER/q3zf1ytHsPy1PtdtFV6J4l93ycrRLsvS2rcjVTVt+O011IlSaIRAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAf/EADgQAAAFAwMBBgUEAQIHAAAAAAEEBQYHAAIDCBESEBMgITE2YBQVFhdAMjU3QRgiMCYnNEJQkLD/2gAIAQEAAQUC9yO4sfuT/ni3Xzxar54tU1HH80LBv3xH/a39rXWhcDqQxRzvQoZzkTDfcBdaLb9Rp4vdBY6csamHLnyoupZy4c7SeqE9U0N6uHalB6I5HIVfqOYyYclmW2ltaS2+QcmpY78Qm6kXiXzsWSm+/ige1VZMwKxNQTzCYa6EzpggYb7nKrFnS6pSdZh2PPpF7wzs12BtT6V8pIrW9MJYzWmNxrUE8Tis6vIKbDiPtVcSzuBTT/8Ab37m/Xf8Tf8A8dsFORv4VkqZLZymfpjyX4b8L7VceKsvjacwZixvpb5txSwrSDIeG+050ZWG/K4Kk0vmLSB1j0vlKsbrmy2YLMs/xzZe2HegO8juNO2TmmyjSNNbBXFPfendJrUZJpImxgLSnvuA+TvkFtMa0vPccmTNo71kvtxg4Zzj9BvHU02e0Rp/j1VvKHSp/AA9L7tgW5mYjdU0SaGG4FKy7kFOZzozRTLJ/jnJeROllEsIjSrN8fpCg0ZHaz3yb0oTpHicd/yCjiv8go3r/IKOKQ5oYTgUwHejJnEVw5NQEb23tOSWq9sob0NO2TmkyTX+QUb1/kHHFFJ3jc3kT1Ekplw6HjmBPLXagI4tvaMiNZ70H5g04W4XWsJskYIZ+5cFTtGpxPVel+DPhxwrLNjXuVU0qvp6g3FZOylURUO5Gy3caIXEACp9jU6o3dIpjk691rHaFlvV3+lv7jF+GGG5CZoufKaj/XzN9X7BtqP9fM31d/VanfBZD9QeFs1SgoLyxW1wV476cSzivXLaGnQt4G43zRnMcMls+UqYaS3icjcrUv6GHy0/SN8EZHxBy+otNXrrL+lVHdU8K8K8KYXi+bfKfnJ8kYu/jFjj+l3zYNDWo/191i2QDrIcGO7kA1qHcfylmedRE5Bbb8t/N2CllDIrGBZQTyLl7mbDiy45uYpNmuWEmSUeDqcDXRXIjyBELjZOZjS06GOLZntjrgFTpQ/gAOl4b1qCYJBCMxw1PrJ3JaYQSSYdx3elh860+yNwHUf69Zvq/wDrUf6+Zvq+grU9+8B55OXw2UcnaxPc0LXaRLt0+TdMGMdxi1GwmNJGDwAa1JOT4JAwYMpnPmL5SpjTY5fi0EK1LehqxZMmHJE0g43y3XL6i01euhABo9DMbqRyYYvY7VZQjUaxIwHAyEeI4+QVDyrUG4xV3oUImz1fpGMXEDoZI+Wo/wBfNYtgOOf7WR7tPbAajaSvEKYZnIbZd1T24xWn0WJGjltojbdHbiB1M380azYMRjGuMQd8qeew5Ol1aoLrK0w5MfzAONDZZcDohBiuO5w6cXSQq6x8R+eaGo5XIU23egu0lvWpO+36I07Zsdj/AA7ru9LD+oS+b4YvnzFTD/eVz4UGb6vrUf6+Zvq/fpqd/eA/V/2TJFaqhrFFDxwhmbs4yAg3R5LKE/rbdxoamZyfUT/hNA+eyFOLe+Qv6GnF9OP8K1Lehg88xYwWpju88yXCsGsZ5W01euq2rUH/ABwNQ5/GtLaoWRElSUDCsoad2ngUyC2lmUNV00OPiY8dtSHr4mbMEDX3nk2nA6nE6TCAjHHCspZDAmJzkWcLeQjprOfNwAzMKs3VMgYSlDTQ5d8Qfn7UOO0eg705XMjtVMkd8mH84Y/ehlhuJruhGdiUFDWc0VLAaLoLhKvnTwUz2FjTmYy20tQzZPJktyUL/VW8unm0ssKRUR+Jwdx3elbvOEGwnvFnryGottWpm+r/AOtTKXficCcdypp9BWCLgSrxqfXMWX3q3Eu5bXwCsg2W04odj9y3K+mUKdzMcLJPN9aON5bKZrTJd9OC1rNS++/JfH0iHo8zyBJR+Q6x334xYrgtdTT1L+hqWY7F3RB001euumoP+OBqHP41rUW5PlbPpmzWrslBdzlyu9fYDiFqu+0Qut1Ievk0jlVFFzQc8Gqh1p2SGncg/wB6j3J8A2aaM3rLOQHSv5HSvRq4vpZ62eIfmDW/cuqX3oYdzt6RM8zLOdoU5lu1FIGjpo7mx5cmG5ouS9TxShG6a+UfNivw5OjTcp5pL5AziOk+ru9LXeemEN06do6FyJFMz1f/AFLbKF7NLJjyF8rIk50sMXFqBeq4Q3rTsxcxlSyZLcGOX5PzPZVas1vlq4ceqHKGORZKU5DMpacaWFInhAqV1LuXsiVYYUkzPiywlJuLH4DWmhy9oS1LehqisP8Al1OseC3VitNProOmoP8Ajcah3+NRGp1cfz19pCQoLyl9jpQpfi98tdN3AKh9yXOZhaj/AF8zPV5srgOFX21s7NdERvgWU7OW9k2uT6hfyKiqbhU/sdKNOGMns1E/yqKnH9Tsb8oaV18gjYz79VDA3OZduvKvNeLDikTH2dZPI3jy4TXTFbdfkwheGOQhyfH9GbzBwjbyqQCfy98dY+xZcLG6r5TIoI2SIZHsvgFnuBqJVwb1KEJL2JwM+Jn/AInSHlcADUmwmReOZYjB+IeUoy3aoZWLp5VjecgSKp5Q0WwnMDv04n8WRVYT0Rsl2DPjvSGK8F3LFEM2s7LbUtsGQnE+2ZDDyNuS0NguCnxDTwLueJGBILefc4tVadzPCJJGpgJRxFZriQyDjR1eFH+mqEDR86205LekyoCq5WP9o5HGo1Rz6CyDfbdgcimTTBuEItcCI4Qp2ImJxt7JD8jYckAtl4Ne2dY7dric7OiSQMTo23Cbo3NPNM+0cj0xS7tKx/miaS8maC4wXW+shT2b9jpa98QSNjugJtu5skQ/JGnGu2IpIyZznM/duCpzjw0gL3SEI8NuNfCngh3KxK6y7Hf4bsdAzFq2rUHHxy1Q6RgwDz5XsNtuOzqIb1sFbAHTiFbB12CtgrYK2rYK2rYK4hXCyuNoVsFcQoAAKG0LqAACtq2AK2Aa2CtgGtgCtgrauIddt62Cttq2riAVxCtq2CtgCuIVsFbdNgriFbBWwVxDptXG0K4h+W7FEVFY7wgA0ZK4DeJZ08sVUzo2nlipecmUKksHgFbUeQkpQEm20Ujdt0y47Mtq/ArDW8qbpxY5Qwko6aiEwtC32rlHjhuEb7u8Ncw33rl4hcA1zCgHfpcO1BeA0I7UFwXVuG9cgoREK+aJ4Z7R3obq7UN+W9dp/q5jXaUGTx5hQXb0N21WjvVw1zrlW40N9Wjv0Edg50IiFdpuPPwC7ehyca33rkPIOg1yrlQjXaePIaC7euW1cq7Txt36X3catERoREKtv3rmFbjXOt9+nLxG7agEaEdqG7arbq5UH5OUOVhjEOAx3hp4Od45n4o/eNp4WQ816R4xcTTkNplG62n87cMbkFhKZlLV11iQ1H2vtZdnB0YlqOtORs0bZLeX13JLd3kwV9cMSpPchrf1ARgt+qSLBjce6EQlByXNVkt14LKCvFM+I4VlNcWSMlJki2vGI9OSgfzvSRGFJxt1I+NzOBRgxqPZt5tQzwNZ3RpvdpkzfNp88mx0gZpSdFyTIkjMBfK57DRWbnkdVXvp0dmdXQqv/TES+uGZIeK5ibDYwutwF1NYJYpJYEfvtajl3Ot3uKTnvjArHLA+qHCKk1VvC429Wo9dW0gwlEpiWU6HJVdAuVVUC6QmKbsXlNVQzeGR44R3I54rfUkSGqP90s1FBhshady4srMbOT6rZlamDpwqmadXvmUyU+va9ttrTWeOGViX15cKyTj8ccjOBdLyjZ4Bf+lgr64YlXUoqqaeZhM2aOxtqSVlNPVUJSUfsIhqckuU39WyvHys11vG40D8gfGnulXElLvu47lTZIccpvl9kYGZSm025qW9D6Y/2Hyrelv9mQ0FScZ4Vw/8l00j/wAEoWS0nL2TIGOyNLviJUlmG7nsayN2Y48xw7Mio6FLUq4+1NuFMbBdiwO5fnjEley/PJuW5wMVV017fXa2G6NCHjKKgdLphFvH09yyO3Fgkx5Oni4L4vZL7czIothdUtvFZUCrMaMfWIKu7YjXbWlI+/Grsm+OHfCTdSjkEukZU9r/AGy05OT5k2NSTXJEzOm1skbyuo9x/At6xMbP2y03OX4pDrVB/wBWhSy+ms24QaSo4njqGcfyllpSa2c0daanHzwakWuRvSdODXJKSxPrkBGYyCmNk0wdNLl2zAO9anv25nuA2z3FJLtyPp3aZPBbmiwbZQKZ7DBWQslhyVd+IdpbkCOhAZY1QWj8RBOay+MtTRjDcvodl1mnZlOpfaCorrrulVwMxD+mmx+RtSomF1Qqsoh1Gz9dwDoNHy+S6ZcZbDYAVqKTj6kydOCaopqDN73frdcLTOKig21m269Jg5BWikjTfFxtOV9OyeeT2XNkVK+NaNSlIqqkQdFikjHJuLynkcGSXpU+GgqNl8uvLCK5JCkdZ02JpVJ0/wB66hO6Rm+vGJRnCNrnIladUdXIPVWx5MyXCyAuFZKnY+pFmPGkJZXiQkyIDrNNOVQUVjTzprJYzhZUJrEQSLNy+cW2VHUD/VCFIcXKrKXXNgdz2iog9Hw0CEBx2sAtyeUc7zkXLpnSfh4YxuRrSDqNTVFQb2nhOPpzLl3C43ZIYaZUb4eJizmaEiBWp624DLPZ2J3wRDTpPsl7Tn9ROJ8kNNaVkJMZNczGkmfSJ1RYWm1KU08hPguBeeiXprTTCY20dzMCSLPLUkmqKgnGmCqH4faEfKeOPdOKUpkFedIuUHHRCT5EbiXDsVLakvSciuNdaKa8n6yMEER2s53FKjEB+tpJcsgxQbQGi95acDxTsCZGem3EIviU2ycjl9sdy4ne2PyRowVwGrD7AJZ7xjxT3LR3/qxs1BssoQAa7Ozfatq2Aa4hXG2gAArYK4W1xtrjbXEK+GwBfxtrhaFcLa4gNcQriFcba4hXZ2CHG2tgrjbXEK4hXG2uFtcba7Owa421sFcLa4W1eXwZLuFtcQrYK420FloVsFcba2CuFtbBQ22jXC2uyx1wtriFcQrhbXC2uFtbBXG2tgCtgrhaNcba2CuNtDhwjcFloVsFXl8GQeFtcbavw4slBbaFdnZQWWhXCygAA/O4hW3/AMUkPdgf+vRpag2qt34DOEzjD3W0pEdbKyR/M7deIW3b0A+6g3tqM52PIl5BQJqZT3OeTjyWa6xrKCswD6EvpjjTLfcztZDbeRGRIdXmR3I2kVQYKujKxFcTvcuwDWXFZlxyvBtpbH49YZk3IzVbFkDLb3BHpv7guAKmyIQvs6wDIt6qUt7lw7UrvFMSxMP9WyXWPlftFOkKy64qeLnsIe3bwDacY0tbR/ohLJ1vK7TcZF2IHTene6cgZO4jrRtGMJigXUynj7dWUgitpbya5xnOLppxePwih0cikKUlDdcI91hqlxc8A+3RDetQbMtWW10R1Q0iqqKpllpK/qRswhg7yRlHCqh4h7dNF8Jsu9W7kajprwrTq4/mjQqRcQ9j3kjF2yrb7euDetS7dDCe6ac1n4F7W05kwVRJENh7rETLjB4Pb86IvzeO+jAURSnsFD407mtfbf3EhHNrJhMTS6YU9s5jOHBaYd6EWrLIaZbV8j30Mjm6CR89Lb1wrCF0xZLsOVNM2HU+tgGlhmJiiJhhrGK+xkr9wp8f+JIiVIYfbWcxnM3947Z2Rzox3/2LcILKep2h0DfpsFbe4fOlj926NYRFuYst+C9GfJzDdjydoH9+3f/EAEwRAAIBAgQDBQQFCQQIBQUAAAECAwQRAAUSIQYxQRMiUWFxFDKBoQdCUpGxEBUwM1BiwdHwFiMkcjQ2Q1OCkuHxCBcmcKN0kLLC0v/aAAgBAwEBPwHBIBsTv6H+WCbbm4B5Hx/rzxv9k/LHz/JY+GD3fe2vy/ofsoi9h0xwlWwNroKqOBnf9Q8kcZFr3IZmGq/hz+WDR0g1XpafVbSo7GPTawuT3fHlz2tjiTIjQytUUyE07kNZQe4bDVcAWC6r235fK48RjYBzyKqXueVhjiHjjNGq5qfL6jsIIn0q2hdWwGre/wBq9vLHDnHOYwVccFfMKyOolWC8nvxlrd63Kw9cZdRtW1UFPDIzdvuWPIA8/PYcvLC8GZasBXtJTLbntbV/Xl8MZplz5XVtSuQRpEiP0KN436jcfDHFnGtctVPQZc/ZJTv2Zk5EkW12Iv8AWv4bYyHjnNKSpRKxva4ZXVCZD301W3X0xGweNHBLB0VwT+8oPyvb8nz9OmDt5+mPgcBgTYG+PC4Iv44Bubdcb33BA8Tyxv4H4f1fAN+Vz8CPxt+Tx8vn6YNx9U/kJAx/Xr6Y5C5BGL35d70x3vsn5fzwSR9U/wAsEgczt49MX2v0/HF/Df0xv4fhjrbl+S/O+w8TyPpgG9z0HXx9MAg7j9LFI8LiWNisiMChHS1sZDnC5rEqE/4lFtKu3TYEb9RbEqJPG8Mqq8bC3n588Ztwmwm10WkxOfdYnUuwvyU9f66YkXXHMvjGy7c7sMZ7llTltfUJMh0NIWWTfQQ/e5m3jviFJkLTxQlzGVYOoJ0sLd425bY4D4oo5jQVayDVBEkVZH9eNgAhOke8D7wt08+SZhRSQmeKojdAutjrVbA7/WK8uo8ccR5hHmNe8kW8KxLCrdWINz8Lkj4Y4pyupy3NavtVJilnaSOQX0Wl74uSBuNVjzsb4y6hqa6sp4KeNpGMqtdeQUEXJOKWPsaaCIm5SJFPqFAP3cvycZ8T1mQvSx0iIWmQyEt9lWsR644X4jTPKJ5D3ayO3aRG1yG92RBckp0J27wOMy43zGkz58uiRDBHVx0r6veLEhCR5ar+Hp0xxTm9Rk+Tmrp0TtmkjjU9NTD8Lj/pjg/iGr4gpKuSsCh4XjjXRyuUv922OJc0lyjKKqshVTNHosT8F/Efdjg7iKrz+OtFZpDQiMqV5AFev/KccQfSDJTTNSZRCnaLpVqht2c6Reyi47puN/DEXHufwFWqT2ygi8Ukegm++2nfrjIs0bN6CKsenNOXB1J9UWYr3Sd9wL8hiomWnR532SJHPxC6sD6RcyFdo0xGn7U3B9/s9Wm/ht64p5hUQxTL7ssaSC3LvKD/ABxxVxlmGTZoKGmihaNUjlZmvco/Mf5rqfK1sZJnMec5eK6PaSNXSSEW1CUXtte1iBceRxlnGuZ1XEEWXSrEaf2l6c/avrbn/ltY/wAccZcRV2RRUxoo49cxOotyAB+/5Y/8xs6J92kHTvRFr+NjcXF/IYy/6QM6mraemeOArK4DaQVsCR039eeMyr1y7K6nMJFB7CAShNjclQbL8cZd9IOYVGZUgq4oUopHjRtH1e2YBNV7W0g3bw352wrX1FdO57pHu7C9/Q4zzjnNMvzSro4YqYxwS9mrSRFmJUANuG3717bcumE+kjOkkVZKen09SEdR48rWH3+eOGuIY+IKVp7dnPE4jmTa1+jJYm62tzsb9MZvXjLMtq65hq9mjLhD9YjoPMnGV/SHX1GZ0q1aQrSSvHGwT6vbEBNVwBsCC3oeeB8OZ5crX2I8j+mpqialnSeCRomU3Oj6/k2Mm4ip8x0RysIqq1mU2Cm2wKm+9xYnz+/A7nQNe3n4Y5bi1+uOPqKnnyKaaRVSWNhofkW9OvljgbL6JuHQ5gjd6lXWdioY7OyC9+XdFxit4LraGqFfw3XPSn3npt/I2UgHZudj44Xi7P8AKGEef5XJLBewnh1gf5u6LHzvbfGUcRZTnAApJ1EoHegchZE8rX3vz2v5440pKepyCsknQAwWMb7XPLl+GPo7pKaPKpqqNA9S5sWaxZADYafgLnHh6D8PyfSf/pOX/wD00g/+VR/+wxkuaVOR11JWR6rMiROnSSBt3sOrKSbeBxX1MVZxGamEkxz5nSyJfnZ3B71ie99rfnj6QP8AV4fuzxSH0W9/j4Y+jL/Qcytv/fRPt4dnb77nHHn+rla3QmMj/m1fxt64+jVC0WaBA15UhT4lT/8A0MVdNVZDmzvLSdsIJzcyq3ZtZr7MwAYf15Yh43yOvjEWaZTFCl0UTokZQ2sPFX26+nhjLKigqKFWyt4np7DT2XIeIseRvfHHmY+xZI6K+mWpZIo7HqfeJ8rbbdcfmWoOSHN9BANctLqPWE7uw/4r744FzI1uULGW1vSO1Mw+tYOezb0Edr+Y+/6QR/6ikHO1HTj5yY4Zz2bIa11kLiKc/wB7Geg02DKDYavDHD5V+K6eRDdZMx7RfGzszb+e+/nipoaSsI9qginCX0iRA34449paajzqNKamhhj9lUlEGldbNYEADy3xk2UZW+XZdP7DT9qaSnk7Uxgvdo1NycfSRmXs9DTZeh707hpAth/cDY/P4Yq8mqKHL6DM9L+z1etz5FGKQkeVhc/C18cMZh+cskpKpiO0SMJKF+0imO49dN98Z/KsHFVbLIvaRpWM0iHla4O3wxm/E3D9flb01LlfY1JChJjHEq3AGrdXJ57csfRnTziWtqLMtN2YG9+/NtunQgcjyOPpJzIxUdNl6taSoZXlA/3OwJPXn5Yrsnny/L8uzHQRBUl3dh710cpAw5bBdzv4WGOG8xGZZNSVJbVIsKJLbqydwn46b72/TqSrBlbQy8mU2bFBxbX0cfZTWnQDuMT3/wDiv8QMEgBjy0qWJPKwxxpxRJmsr5fAWFLC9jewLEAauRP1r2+eODuJ5MrqFoZixoZymsLbUGdtClQSBz3O/LFNDLVTQQQbmW1iPA8tXw58+WDwOZaaQS1SszJ34+yEiHYbd634Y4s4B9gmkqsqvQ1sBMjCMsvb2UN3LDYEbW0874zLizNcxofzdVMNCHS+m9yVNu95+PnjhTiWfJapYmJallZUZRzubb2JA6+OEcSIkg5OiuPRgD+T6Tgxq8s2sOwk36fr4z/D78Pw+K/g6izCnj/xtJG9lt3pF7RrkWvewxQxFcwo0sw/xNLIS/iJe+OZNw1wMcUUBzDI6mJBqcLqUfC+OEOI04bqKmGuDxQysFe67jTYXt4eHlbHGPF1Jm9ItFlbNJCSO0kYBUBPTnqPwBx9H2XNQZVPVztoNRIJlO+kRRKI97gHcqSNrWtit414fmrZ6Gup1lplbSKgokuokDWQlwy2ckedr44hHBr0rSZW/wDipDcKFYKDy91gAOWPoxkqTHWayTGGVBcWF7XGke7yx9IdYavNYaGMs8dMunQpFzO1nG19wNVj18sGPicZd7D7HVtSC0ojERI1bHUPLzx9HeYtQ5xJSS9wVcZ7rbBZh49Ry8OePpBuM/JXfVFSrfodF2f4G+3zxxZw0pyyizWlj74ghM4W9/cWx225c9/vxwntxDla351Ctfw08wetxi9728T+OPpFUnPYxb3qSIg9Nnbb1xkm2U5ZHbvfm+nJ8F0xLzPjji6skzbiGVIVeURp7JDGNJNrLr7oY79pqtirj4oloo6apo6laSlQKilCFVV5H4jc4+jTMD2tTlc11LBpIV2tZR3+t76j4Yz2ITcWVcTfqZqzvN4hnEdh5g44y4ViyaKkrKEO8XdMo5qh2vq9enrjgbMqWtylI41SKSG6zJsCObCU/uWIued77Y4qrpM44gmEQaYRq1HEqWa4XZiAD1cEr5HFZFxLNQpT1dHVCmp0CxqUsqotgCb+PM4+jWtYCry+U2/3Udx5atvXHz6fd/L9LDBNUOIoI2ldvqoLn44h4PzKSNXfsYmP1HZtQ36gJbz5nniq4XzOnIHZLMp91oiT99wuJRrjnU7Axso8yRjO6KXLa+phn59qSNnsRJ317xFuTDrjJ6CfMMzpYYB3tURJ30gJJrNyoP1f+tscOTJQV9MZfcsqHV02A/r78CaIAypInZkA6ye6dt7enLHFNXFV5jI8WlxFCIhb3SSO8fna/PpjiPLZMtzOrWRdMbzNIjWbTaXv7G3TVY+mMvo5a6sggpx2jdqhut9Nrg7m2KSIw00ETe8kUan1Cj8nEHC9JxAY2qJZUMUZVAvLdlb+GMsy6HLKGnoYrvHBF2ff+tqJZr/Em2JuAcplzD84CWdG1I4iFuzVg+trb8j6Y0rp0WuvKx8MZvwPlmaSvOGanmktqZVUjYBeR8hjLfo7yqjmjnqpJK5oTdI3AiiJ5guFJvp6YESKvZBE7Fk0MlrALa2lQPAbfPGZfR9lNYzyU7PSyOS1wAygnwG344h+jGBXUz5lJMgP6vsVUf8A5E4yzK6TKaVKOkTTGH1vJ/tGb+NvXD8CZdLmTZlJUTtI0xlsdxe9/H4YCBV0rfZOzHQabW8MQ8CZbT5j+co5p+1EhkCse6Cx1ML35XvjOuDaDOqv2yeWVJBGqgL7upF0g/HYnzwlHEtIlC395B2IifXuxsoXFFwLllBmC5jDJL2iy9osf1FF+Qx8sZ5wjQ55VRVc8sqvHoFl5aV6Df8ArfEFGkNGtErvoSHsVk/2mm1vlih4Ey6jr1r+3nkdXaTv2PeLat/T1xNCKiCWnltokXR3V3ta3X+eMq4JospzEZnBUTNOvaBUb3LSW5/PFXwPl1XmjZoZZRIzxydl9QMrhjbfrz9TjMstp8zopaCf9RN7xHvC1gLfdjJeEKPJZKloJ5iKqnancX5KdtQ87YoeBMtoq5K7tpmdG1WNiCb3vbb474qIVqqeSnl9110d0Dly6/zxlPBVDk9f+cqeomacFrI36uzc7/8AbHyvv8Tufn+k8/MD4nHDOUx0VNHUyD/ETLqJ5kAnuje3JbfPFj1dmPjthWZOXeHg3T0xsTZum4xXZdlebjRVxU9QE6DSHH+ZtjilyzK8qQilpKanfTrjJZdZUHexO9738sLJHIRplDMFU91gxFwDvY49sqdPZ+1TCO3IsezA69f4Y7RGGtHDi9mIII22J9BiuoMszIRpWxQSof1eq3ePKwbne+KTKsry9l9noqemdvd5XJv0NvjiSaOL9dIqE/bNr+mFYMLrutr6umFdXXVGwkAYKdBvYk2xcEsAVOlWY2ZfqnSRz5g+O2C6qgkYqqFlUFnSxLeB1dOuBLGWKB0L/VUMpL/5bHfGtTcXGoWut9x6jBKrzZd+QLKNXpcjF/AX8bFTb1sbYDKSQCCy8xcX8f44NgCb7DnhWV7dmQ4JYFlOy6Rc6sK6Mnaal7Pq+pdI+eDJGql2dVQW75Yad7dRjWu3eABFwSQL+AHmeYx0vjUu92VfDUyjV6AnF+nWwNrg7E26bYDo19LKVBILhl0gg2IO9x92O0TSWLKAP3l39De3zx2iWVtQszhAb9SL336Dlgb3t0JH3Y1pq06l133W/eUfaI+zjWl7a11H3FvvJbbuDrgOrX0MHIZUYKblC1ve9L74DIzFVdSykhgDy0mxvgsq++wT7Oo21f5cXG+4uLXW4vvvi4JCqVY9bMvd82BO2O0S9g6Em9gHU3tsbb/pYjZl8O0S9/UYp7GCAra3ZR8v8i3+f5WFydiRoddujWuD6YeqZ5s07SuzGOpSsZaVaNS8Z0va0hLAgXHQHGZ5bNV8Mrm1bLUJmFJRsukSOmoFzoJG25TSTjgfKRBl9PmHtNRLNUR6jA76hztsWbfl9+KslqSp0cxG9tPNSFub/wDTEGfZnS02aUUj1AhzETJBOOcU6ye7G2ra6kX3vzxW1dR+aODphUSd6ughnYHpcA6ybe8dzz544vmmgzDh6NJZEMlcO6CP7yPb7LG/8cZ1R1iZvVVWcU+Y1lAXBgemqWWOnUKu4Vbbj6wI96+DnVBS8I1M9BW1Evf7CPtt6gSOfcJP2b2G/ugY4Jr56fMJMtravU1WsU1OA2sGT9YwuOWlTpbwYEYXO6zKs2z2RxLNSTSVcN3kOmFmlfTpsSBa/jjIsrXOOGF9omqjJBU1lQpEzatJX+7B36M2w+z92OB8t1wS5tPW1XaUMzEK76oUijj7ysS19THcbW354ps9qFz787tWt7FUV/YPTM92CsQEkCAkdna2o3ve9hjjGqQ8RZf2klStF7MZnNIzXI0kg6e6Olxjgmtl7HN5nnqHy2HtDTzz3NvrMHJJsVvuN7C2Mqzuoiz+HMZK9moa6tNIY2bZGvoVivRCADf5YqGBjnC7/wCHmlXSVuQtxqAJDeY2xkWbvS8O55XTzuxSqnii07nVINCIoJG421fG2OD6p/b5cqzCT2lM0pDMD2rHsmC6Qq/ZfxHj1xHSZs+btwwKpzTLN236ws/Zm0oAPPuq1iOlscYV8z5kmXUtcaZMnoBMTr0maoQKQp8drDqb9MZDmP5zyeiqx32kjjSS3SQLpc+hYE3xn9TT/wBqMwWtqKxYoEjaKOnZrbQx961wAuq9z8scPZnV0/DWY5hUF39nWc5fqJLyxMzaffsCQxPXa3ljhOueLMjT18vtEWdUz1DqJmYwvuoRVHuvt5Drc4lpc1izv+zMVVJ7JVyieOTWxmjjI7XShNuQ2YeOOMamU11Jk9JXeyvllF7Qx16e1aPchyNy56Df+GOG8y/OmTU1XYlxHol8SyEoW8d9Or44zfO5cm4zknaZ2ptFMlRFuUWCZI0Ljp3XuW8PPlimzufOOM6NoJJPY4qwwU4j2jkiSMK72JFxrDG9scIzyNmnEqS1DkLWzNHqPdCo5FhvcWtbl6HHB9VJNW5+0speOKpbQ7G6oG7255del8cXzyCq4dMMziKXM40cxkWZNr9eV8ZbNM/G2YQmolkiATTASAgAhjsQCwHmcRVMkvEvE6K5dY8rdlCSNpSRY1AA6A+NuuIK14qaF4qzMFzZ6oJTQnvU2795deq+o8/d64ojIaSnMpJl7GPtb/7zSNf3Nf8ASA2+++OFs5hmhWjnl0yxggBjzF9rfDngaRtqtbyJ9OQ8MCxFyyqOhY21enXFt/eYAnvjbla22Ml4ciyiWvl1JOayd5kEqBuy1MW2J677+eMzoFzOinopHIWaHs9Y2N+m3IAcvhj+zpjyWDKaatmp2pyCtShtJbVrZfS5I9MCn/wvspc7xdm8n1mJjCFj5k7nfH9iKJsolyqWV21VUlZHUf7WOV9I0g/YsN/wxU8LUlZklNk0jtGtKqGOeL9Z20fJ97ddzjLeDewq4q7MK+fMJ6WRTSiU6kjRQB1+tt6YzDhOqqqqeopc9raIVDXmiVRIjiwGnSWWwsLdcPwVTtQU+XCqdYI6kVdRpRQaqQG/et7oPlfC8G0FNX0eYZeWopKZjqjBMqyK3v7tuCxudhYXxHwflyx5pDJeSPM5WmYNuYnbclCfE3OMnySPKctOWxyM0Z7Ydp9fTLaw+A2xQcLxUGUVuVx1Mtq52aR+o1RhbA+vyw3AGUewJToGSqSNUFVckk31aiOVwdh5DH9l75nRZjLWtP7HQexCF410S9xkDPv0BH3Yi4SaGkzOhizOaOmzGUSdmsa2i6uq78nNwf3QMScB5WaGOljus8ciTe1/XMiWsdPIbC2M34WlzCSCpgzWqpquGlFMZENklAAXvIDYbbbc+uH4Kp2yyDLFrJo1WpWrqZVUaquYb98clXpsfO2H4MoErKGtoJJKGWlkBl0d/t02LKdR2ub8umBw9AM9/Piu0c3Z9mI1N1toCX36kC58CTzwnBWWvUVtTXaq2SrL6dZI7EOb90j3t998ZFkgySFoI6h5Y+21oGFtKdFt5cvhg8LQyZvmOZTTa1r6c03ZFA3ZIVAul+u18Dg9jlRyeXMZZKQz6lGnQyQXuYwwPU325YfgXK1loJ8vMmXTUUqsZo2MrVCLbZw9gpIG9sPw/FJntNnbTydrTxtGAOt1Zbkcuu+P7HZfPmFZmFdqrJanUseslewVvs2Pe8d8ZFkCZGKlIqmSVJWvGjbKg8NvDltzxmHCNPmNdX10tQ4aso1pRFoUpHpAs1zvfUNWKPgqjoqvKqyGeRZcsQ20gATOxJYv6kn4YzTg9autety+tmyuSoJasWDdZyefhbV188Zbw5S5dltRl0TMorNRqJlN5Gcn3rnFHwQsdfT1VZmFRUw0j9pBAxuquDdTv88Z1wsuY1SZlSVUmX16d0vCbLIoAXvkb3sMZZwrT5fTVq9q71dYjJJUndmD+9c8+Z28sDgqFcqWgSpKzw1orYKrQDJGwa5W/OxxAjxxRpI/auqgPJa2pupsP0oZlKsh0Mv1l54h4mzmBBGlUSoFl1KrH4ki5xPneaVJ1S1Tk/unQPuX/wCw7/2+79rfx3+/cf8AtFcfH7PXHy/auTfSJURtHBmo7YHY1KIob/jGoDYbczfnigzGjzKMS0UyzqRc6SLr46hfa2AQdwbjx/H9qMjwM4dDdTplVwR2Z2ABHP8A5QcZXm9blFQk1JM8S3BkhB7jDn8xjhzialz2nJBWKriFpafYE+aC+9x3j53wCCLj+v8At+08/wCFcuzuFiUFNVRi8U8KgMT4SLsGv48xjN8mrMoneGpjtY/3bLqKyrt31JA5fWG1muBfFFWz0NTFV0snZzQsCNB7souCRJ6cuuOHM/iz6jWUDs6hAFmi22I21Lue6bX3t+XzPdHieRwGU/W28d/5YWzbqbj9nG/1efW+M5yilzqjkp6lBqUXhlUd+N7XBB228RyOM3y2fKK2SlnQAqw0Fb6ZFIB1LcDffveDXGOG85lyfMY5wzezuyxToOqm19IJtcedsQTJURRSxm8ciKyt0sQNvUdfPF9r2POwHU+mMk4WWqSOqr2YRPukI5gXt3uVr2vsf5YXh/KVXR7HEQBztv8AHGbcJUzRGSgPZSgFjFvo2+zzO/yw8bRFke4ZCVIb3tv2dy5c+t8cdZIMxy96uJF9so4zKh6FB7wNgTq8Pxwb3UcgLE/a182+d8fR9mzVtBLl8zkvS/qifss5NvHa/wB2MngFXmlJDIO4G389PP4HScKoChQAAosAPLb8gADBue1jji+jSDMYZlGlZxdgPEbfywOvqfx/ZzIkiOji6PdZB+4Vtbz+OOI8u/NecVtLtoDGaO3Ls3s4HqA1vXrjgmvNHnlILkR1DCNgPF1Gi/oTvjKaj2bMqWZyNMb2a37x57223xrUqrXBDgMCN+f9b4+N8WPMC4HPy9ccY1Imr440YMtOADbqXAYgfK+LW/H7/wBmRUlVObQ08sn+VTiPhzOZLEUUgB+1pH8cf2Uzu1/ZP/kj/iwx9LGR1eWZll89RAYvaYZYm3BuyAEG4JUjSRyN78xjL5Wpq2kb3Xjnimv07NenqdJt088Qt2kMUi+86RuPiqn+OMk4q9kVKauF4lGlZOZ/4t+mFz3KXGoVkA621Yzbi2CKF4qBmd5BZnsNHhsb3+Q3xI7yOZJDqdjqJPicbnnz/ZiRQJZY4o4bctK7/hhmjDaWJYjrdhz8gPwwpiva6281dj064/8AEPCjU+QvfSqGqtoFr91ehwli0Za53hAPWxJxR8J0lVkuWz08jRzyUNKx1e5fsU8LnpjMsqnyyTRUaZAPdZXBWxAPWzdfs+mGa3JBY9FNvDxtjSPC3l0/Z3//xABAEQABAwIDBQQGCAUDBQAAAAABAgMRAAQSITEFBhNBUQciYXEQFDKBkaEIFSMwQlCx8DNSwdHhIGKyQFNykPH/2gAIAQIBAT8B9JUlIJJ0qZIgSOuWXnJn5flpBzjUGU9JjnXbxu5tq0CN6NiXe0m2MAa2m1a3l0hLKjAQ6G21hAQvRSsoOutI29t1C0OJ23tYKSjBH1jdkBRMlf8AG9pGg8q7IO1FG8Nszu9tl9KNrtNYLZx1X2l6hrLiYyTLuEArBhRVMAiDWMDLvEjI9ek9M9R4VzjrSWRAmlNDDlW9m8VvursS+21cJCkWbchGWJ1xSghtCASATJ5kc6e+kFvj9Y+uNtWRtUPQ3ZKbhJYJz4rglWOPAwedbjb222+ewrfbFujhKWVt3LEg8G4bICk6nJchSP8AaRMUhlMAnmAf60poEZcutEQSP9Pw9Iz9Ej7n4eifTHlU/wDQ31jb7Rtrmzu0B22umHLd5pQBSttwQZB5jkfLpXaZ2c3m5G0lKYaU7si6dPqT6cSsGIBXAeUUpSlaSSBn3kgGZyq0unrW4Tc2zq7e5t3EOIW2opXiRB9oGUlJ6a88q3F7fbX1D1bekO+ssJCUXLLSVl6Dh+1747+GDpBz0oe0D0IpKgYil5Zda7Tt1396d1No7JYj1lQRc2ok4S8wtLiUrIBMEA6A5xT2628LN2rZ6tkbQ9Z4/BwJtH1BRmMSXA3wynnOLIaia7It0rrdDdVuzvk4Ly9fXfXDWKeEpXDSynTL7NCSscjTZBSnyz8+fzpSglOfOjmT6GmgunW8B8K4IKJ5600hJVGetPI4Z8OXyppOJVPN4DlzpFuNVkUWWuXhS0hKiBp/gUBJiuCOGCYmiIypDYUAaUiFRyotQ2DlTSEqzNcBFLYRhpKO/FFlJT41zw9KQ0kpBNcBunG8GmnzpAlcGlMow5Vp99tzYez94dnv7M2owi4tH21IU2oaKPsuIVq24g5hSfKu0Xsn2rug65eWTK73ZBXIuWkrWq3SQITcpwyOmJOJJEEkEkUUXJzYKgPxpCUmF8s1LQRI/Dn7jl6GCcUcquFHHloI/QUl1P4+6dAeulKYtVOIcSvhLQCJwYkO4jJgYO7ryOtFot/hOs5+OfPzy8KZJxeFPqIUemX6J9NtpSk486iERTP8T31dDT99KZEK91XBEjzFSFJH6c6LShmk93x1pYIPe50yMSo+fwNT3sHSn04VecfoKaSSkeVKSFAH960r+GPfQURpTCiUnF0pa1yRTAxGp76h1j9BTicDpnw/QUkS2IoJUFZkGnzlTInvUFAqVrn/AGp1OBWfP799lNwgtOJDjSkqQttSA4haVahxKso/fhW9HYHu9tu6N3sm4c2E6tZVcstJ4zCyoe0hKsPD5EpSIGmlcwOtNt4QKcRik5Vtna1psOxutq3ywi2sWFuLKoAkaJEkd5XLTzp36Si2b44dh49n8YJLrjvDdDZjvpSnGnQz7edbs71WG9GyLfatioqt3kLUoFWNTSkGFoWZOc+z4dKCAk/P4506nEPGojLz9FvoaC4XB0pXskjSmT9oddaeTjzHh/SkNkZ5fv3U/mqAelcNae8P8UlTvOKuIy60wnCMR51KZ8ev+aeTiQDrTJhOh0pDkrIPWniMOXoZPd91KHe8zTIDac+fMfualOvOnxoqD+8qE8IRypDgVlmD406Di06aU0AlOYiaGEaZmn0ykGPvtpbVstj2j1/tJ9uztGE4lvPrShMeGck9ABJ5CtpfSC3Ps3lItGLy9Tigvt4EtuDLNAU4Dlp3kjStj9ue5G0kq4907sxSRPDukjPPkttbgJ5wqDGcRQ9oHpQUCKWoBNdrmxr/AHi3K2ts3Zja3rqWXeA2YccbaXjdjSe5pGppbTzTrtu4y4VB9THq7ja+KCF4EJwgHMAAHl0JrsP2Ff7G3NQztBosrv7t+6DKsQUzbqSltsELAwqUUFyNMKgZnKkKkCToI9wyHy1peQ86OvobdKKUskzQeUE4TXjzpL6gINKfKhFHUEajrQfNeseFLWpZk+FF44cPKpzmTXGVGEaUl4pEUFZzRcn0IdKRFFUkGi6SI5UDhMiluqUAKS8oJwmkqgzRdJjwovEiOVAkGRReURB+9UrBj64ceI+ykDLP4dK7Xt+breXb9xs5Dritl7LuFMIa4hQ2443CXHcKSoK+1CsJOqYyBoJzUVOFX8vcSAOgieQy8ddaSpSU4Rh1xE4RiJ8T5ZaeiVAc6lZ6xSm+9iUDlkk6YQdR4z405uxsFdyLpWx9mquCvHxjaN8RK/5pjvEnOTQSUgJgQAAIEZDTIUklOQnL5VjUqelQTyqDp6DlWFXSoPQ+dRUHofRFHKsJmKwmoPT059DXhpRBHvog+mKg1FHu61Hpg9PvdopcNlfBr212jqG+vE4bmGPfFbTbdRf3yVhZcRdPB6deLjPF1M5Lxa9PSnI56UMhyisXfgRHT4U8TJTlGVD20g9a4STnl7/dQSApYyyiPgKbHtGOtIUktjROuuuvhQTiWdD4jQ/pTqDhxAZJInrRbEI05Uo4FxlGUfAU64RCYyPT99a4fd0zFIyQSRprTw9jxilNjBI6Z0AZSY5/CnB3kwOk/KnU6EDQCaOAoBj9JpsCJI1mKWghRFIyQnxpxMrA+PwHvpxAgRyia7paxxprOtNgASoe1pS0EGkplsZZ1gAaM6jX+lLRGDnMU8kYU5GmROKRp/YVHc0zM/8AKsIwp+fxpSQcsgBH9KVEmNPvFYsojuknPQ90AfOZrtl7Nb3YW1rjeXZjLz2y715T93wUqWm2ddhTnEQhMpSpwqIOkHMjOu9JxJCJzEqSAZzyzn5a0BM5EkZEJBVHP8MiulKcnTwpKiDJorBPOvxBXSi9JymKDhxEnnRd5JFJeAAGAUHoJIET/iuMcC0n8WlF1RA+dFckHpRclU8hp8qD6gT0NcTJQ/mINcQGJGlB8ienSkuwdMuXhXGOIn4fKuMrOseUVxjAA5UpeIz5foB/SuJkBnlRcEz+9K4ys+hrGcKk9dK4xhIHL9/CluYvl/akulIilOkpUOsZ+VB3ITy0oukkdBRdHIGedIdjI6Upyf3/AJoO5np/gUcz96+w3ctrZeQlbSwUrbUkLQ6kjMOpVkocoraPYxuBtJ5T7uxg0tS8ZSxcOtt4uZSgZJk5wMq2P2Zbn7DSsWOyLf7UDGbhPrKstIU7MDw68/8A2VraGifnRQpOvx/NYnQiimRBinW8By0/NEOEa6UhQUBSkg668qcQUn99PTl1FT1y8OZrEJAOSjok6/2+deX5chZSqOVJViE06AU1EZUSBqYExJ00n9M67TO3BexXrnZG7DLDl2yssu7RehxAVAxcFCT3in2ZXzGlL7UN/XXuMveTaSTOLA26kIGmQBbyT0HKtx+3na9nct2G9B+s7N1aUKvlKAu7MKIhajCErRn4HlnVneMXlrbXVs6Lhi5bDjTyIwlJTiEwYmPHWhmJ/LmVwQKNPJAUTyy89K7RtsObD3Q23tFglNwzaHgHopw8LH4KGM/KnPtVqdcW44tai4VLMkqX3iTn41nzqEylRSDgOKP+4Rolz/b8fKvo97buNo7r3ljcuKc+rL/7PEZwsujicJM8kElKf9oFf3Pw/Lgc0+BpC8QHWnEyDXaVslzbm5u37G3bLj5s8bKB+ItLDpSOckJOXxpxJbUtCxhU0eGpJyIUjuKTBzlJEHy9He/lOo+H82sR8/CM6+j7sVyw3Zvr55OD6wuOI1Mjut42s5A1KciJBB1qZz65/wBvyzaG8mwtlJK9obVsLYJ9riXTIUMpzTjxfKrjtj7PLYqB3gYdw82W3XEnyUlGEx4Gk9uXZ2peH64X5+qvx/wrdTfDYO9zVw5sK9Tei0CC6AClYC1QO6uFZRnIHhR76FlPeKYGEe0TlkOXzoiSUnumZUmRnlmlXhFdpXYd9b3rm291kIQ+4pTtzYEqbCnIE8JOCDjPezKRJ6U52Zb+tvFpe7G0grEUylsKTrkcQVEEZ1uN2E7Uv7pq73nbXZbOacS69ZkqTcXBQRDKxhhDaoBUQokgxFWllbWVoxZ2jSGLVltKG22xhAQkDCnLp11P5a7e3VyouXNyt5a/aLsrKj1KjKicuZrvkYgIQdIiPhin5ViTpIxf+A/Uj+tfRiXhvN4UThUW7VyUalIWRChkn4E5U73kKg8NQK3QUfyhA18cX/2rbt03h2FvDtCw2m2ztTZre0rtttJTw7llr1lcYXRIWQNOIkxoCBW6O+myd8bM3GzxcIW3/EYeStDiFnlxM0KB5FK4AgEDQQkwFDPlmVEeaj+/dWEkYVKMTplQ/Lf/xABjEAABAwIDAwcHBQsHCAcDDQABAgMEBREAEiETMUEGFCJRYXHwECMyQoGRoRVSYrHBFiAkM2BydrTR4fE0QEN1gqK1JTU2U2N0kpUmVHODssLSZGXTB0RFUJOUo6Wws8PU4v/aAAgBAQAGPwL8o9+DLp8ybGeh3cWmNJfZDrP9JmS2tIUUAZ0m19CL2Nsf53qn/MJft/peBuj+zfjj/PFU/wCYS/8A4uP88VXv+UJen/4vx327Ccc2lLvPjCy9f5Qjg6nrPz/Z1nG/8p7HUHQjfcHeCNxBHXguNJPMZJUtnedmdMzRJueiVXBJucx8rUmMrI80oFBHE/NVw2atdpcfNwkpUESkWEiP6yVj0lIvvaOvS+88e7x1Y+Ua5JyZswixWunLmLFrpYZHqpKmw4+4W2Gc6Nq4krSFkUOi0ynRwTlNQLs6UUjdm2T0ZlJI1I2R39FVhctiu0emT4udOdUDawpQCr6Nl2RIYU5p0GlNthyxBfb0ump0OXtUXCJEV1Ozmw3eLcqPqW9QQFpK2l5SW3F2VbF72A1P7+rCmgt2U4n0hGSlSUnqK1LQD/ZvaxvwwEOCREv6z7acg78ilm3WdLXwHG1pcQtIUlSdQQeIO6x/j5H6rWJrMCBFTnekPqyoHUnipa1HoobbBccVZKATph9jkvRYzcdBKWptZW4p14cHTAjOsrjZt6G3HHzaxWUKzNpSalTqNPi3GdCW5MCTbjkfD8hlKe1yMrcLkXwXaa6uLOYSFTKVLCRKYSdNojISmQwVAht5tWo1cQi6QfHi35KuRHhosdFXrIWPRUn27xuI34ciSk5XEeio3yuJ9Rxs8UKG6/EK8rcqM4W3WjcKG4geo4BqptXrDAbNo81IBcjEglX02TuLasvo+kj1t+N/k7gffbFWmF5SoUKU7T6U2FHZtxIiiyHmwNAZhQZK1+mdoEFWRttKfHjjj3/HfrimzA7kpst5qn1htX4o055fnHz/ALWEbSWSLK6K0XyOOJVfxc/D1sMwY6il2bmzqTvQynKCP7dzrv00OPHi/Wd5448eLdm44VSXVqLS0Kdj31yLbOZaE31s4k7r2Tk6IGuPcD8D8AcL5MtP/wCSuTxYOybNku1V1ht52Q5xLsVDojta+a8/kyl13OANANwGgGtzl6rnfbHV3afVx7d+KfXKcsiREfQVN5ilMpkrSXorp9ZL6Els3+dmHSSkiFUoq88aoRI02Ov5zEpoPsq9rawfyWsMrctoKLDtu7oL/wBmdNB0kk3Ra67ux5DZaebVZaVfWOCkHehY0O7eD5UuNLLS0KzJWjolKu9Nt/rDcr1r2whDiGXlpFi4QAV9pCQBu7PIe7hv3i378SY0gWfjPvR30m90vNPOJeQb69F3OB9G3Dy77d/o6dK54ervPXbjikVVhwOs1CnwpiF8TtWm3Dn4hxJul1B/FrCmlDMhWIEg3yLjraSeAcS5m7tQ58PLEWm9mESHFnhlLK2tf7SxbFxxse2xFv245XNvhQcNdnPALvfZSXTJjqF/VXHdaUOAvlGiQB5PG/cLdt8cko799q3ydpAWlWhbPMGDsrcNkCG/7P3inHFhDaELWtZ3JCE5iSbWAABNyeG5R3KAqM11AVlS6ilzQhdzvTtW2zl3gZkp3HLnvoajQKg3NjJXsnPNvMvMucEvMPobdRrfKso2bllZFHKcdXu0tcH6tMMwq7OW1LkNc4bjsRX5LmwzlCVr2IOTMpKsua2ayrbjiLSYlSkNzJzoZiokwJbKXnVEZWw4WVIStRslIWU5s2l7aeNNL69o7sMQa7NdblyGecoYjxnZKks5yhKnNkDkzlKsl/Syqt6OIdJiVCQiXPeSxH51CfjNrdXohsOOgJzKVZI7SMe6/Zjxx0xE+6CaqOudtObNMx3pDjiWtmHFZWkqyhBcR6Vr5tNxsxH+VJDO3WlsPSIMhphJVuK1qSMo4qNjYDhjs0t231vfcQRbgLEHfpgrWUpQkFSlKUEgAC5JUdAALk33AX68KaTUHa3ISrIWqK0mSm97ZkzH3YlOdTe4zMy3NUqFgRYkfc/W9kDbOVwEL9qFSggHqu8AfVKrHCG35syirXu+Vomza7SZMNcyMhCdMzjrqEDMnXqblQZLMqK8gOMyIziHmXUK9FTTyVFtdzuO448adh1Oo+3yDt8acOzWwuRrwMqkT6m85NhuqakoiQJb6GHU72VPJa2K1o9BzZuOFDyXG3A0pOzEWkwKm61OmOpaipmwZcdt91XosB1TeyS46fNshbiCt9bTbYeWsNKv16+NSPaCQeB6sKq9dliHCS62znyLcWt509BptttKlKWpKVnsynCUfKU5kG13XKVNUhA7Qy06s62CgE36WVJ2imwWZsKQzLiSWm3o8hhaXGnW3EhSVoWgqSpJBuCFHQ24XV7/AG6buzrvY2tiVTZNTkuyoTy4z6YtOlvIQ+0opcRtdklCyk6EoUpPV2yo9BnqekxG0PPRnoz0Z5La1ZAsB5IC0hdgsovkzIzWzC/x7+juv38bHEmE7VJL7sR5yM8Y9NmuNpeZWUuIC9ikLIO9SCpB4W4/y2o/8ql/+nH8tqX/ACqX/wCnH8tqP/Kpf/pxFpMGovomzXEsxUSYMthLzqvRaS4pkthajZIC1Ivm0vw8adneMOSX3EMx2G3Xn3nFpbbbaaTnWtbjlmkIQgKWpbjjaUhNybXspKKnNfAVZK26XKCV6A3Ql1DToTrucaSsKzJIBFsSI1BqJdlxWucPRH478R8MZsu0QJDIS6kLyNubJStmHUlVipGNfHHTs8jEGvTXW5UhkyEMRozslSWM5bStzZ+hnKVZb+llVb0cfy2pf8rlfsx/Lql/yuV9qcJaNZeiXtZyXTp7bevznEMOoaSPWceKG03Fz1ImU+bGnRHgC1IiOokMKH0XmlrQe05v7Ix9nEeOHkemynm48WK07Ikvuqytsx2UFx15XRPRbSm59HTjewKgKjOdSk5Q6ilyy0rQKuDkCtAQNQm6gro2sTITQKjzh+KkLkRXGXY8lDalZEubN5CLouDcozZdL2zC/u/j7f54MFSfMTWknYv+rw826PWaJG4dJBJKCLrzLjSmlNOoJuk6Xt8wnRaFb0qT3bxj49vce77zx43ezEvljSIpepE7I7Vm2NVwJmja5BSM1okoBL7kiykMSC8hSLLbye0jxbd3XV15jeyfH22sO3h1YbecZfbbe0acU04GnstjZvbNJS8QvZ3GbKUk2CuH3McoHFCiSny5BnEl1NLkPHzwWr0l01905lLHThv7RTqVpkqdZ2KlApUEyIz6CFAKy+bcC0koU2RrlSpWZJSeokociOuti+V9hO1QsdZy+hv9E678BtmDIOo6S0FpA71L+zXTCis7WW/l2zovYBJJS0kX0CCdTbp6Zr2x8ON9SN3Zbf8ADCeWdDjLkyG2AzW4rYLjimI6bM1FtCOktLTSdnJQ30kIDT2XZokKBGunWQTuBsbaZhc9V0ZFBOVQUv8AfYe02OUfTsoDRJScwKY8h9h1vk5AfS5UZpSpKJBQrSnRzv27zgQl0pUrm8faOFeZxrJlSAlKQlKUgWCQBu00t1W3feco/wCoqt+ovY0AFt2UBPHst7euwB3DDE85nKVNUiJWo4uc0RRP4S22PTkQ1HatJ/pOmz0dqXG2JsR5EmLKZbkxn2zmbeYeRtG3UK9ZLiVZh2EabsRf0dp/f/LKkd+/jjkr+klDPt+Uowv7ifecdw/aMRd3+jsDgD/88qR+3HJYcDyjodxwP+U4vDd3Hhr1ny8l7Ej/ACbO4n/rLWB7PH7evcdMJPZr224X4aX/AInEzkvSpbkeg0mQ7El83WptVVnR3C3J5wpOq4cZ5C47TGbYyChyQ6hxCo+Tja2p/aq2W35ygq1gk6aDo7xpdK0Cx9b1hrwN+l1i2OpQIPSuN17E7/S3IWlwlJ+ZfWfJiyH2uTDDCm6iyu/NpM6UM0Nttv0Ocozh595uym+ilwkOWPi+7j1948lXrkj8XTIEiVlvYuuNtnYMJ+nIf2bCBxU4BiRLkr2smU+7IkPEC7zzyi466bC3TcUpQsB0Sk4jyo6tnIivsyWHBoW3WFh1tY+klaU5e3FGrjVstTp8aSpINw28tsbdnvZf2jR/M8lL/SiEP/yusn6wMd9xqMycxV63flAJ4jorukkY+4aqv2iSnFucnnnV5gzJXndepqieiG5Kto7DSgi74db/ABkhOFDfvHs9E37zfstbFe3W+WqppYW/l8jhuxU/0Xm/4rRvdgjrSR2br2t1bye627FS/rCZbs8+rSx3EG4Psxwxu+AxwxyN3f6U0Hu1qcYbus3y9iVKOB7PaLce3D1PacyzOUTwpradLmIPP1AgcUKjp5s5fhJFrE3werNcDsPSBI3esfbc7ycUOorWG4b0pNNqJNkoEKpKTGcWr6LDqmZRtwY6rjHfbq+zgN3ffHjqxG3f6OwOA/65Uv247tey3H1T7N2Nw11BtlNuy9x33HVgG2npJ0uLjjroSMRNpJX9z859uPVoZKlNIZfXkMxlrUIdjFzbK2eXbBOzOYlOM3BVlDUHeB1admmm4jfgeN+gGEUhly0vlDMEUi9nE06L5+a6m3qlzmsRY9ZEtQ3Y7tE/OCRuH1qHao4oslxzLFnOikzCo9HYVBbbKCon0UtSjHkKIscjKk3yKWlXj2fwGgN/59spCLLA828n8Y2Ruyq6gdch6O/TCg+grj3OylJB2ax/tLfi17vongNDjt8dX3im3WkONupLbiFpCm1IUCFJWlVwUEXuk6G5uNThiRS2QxSK607Ijx06Nw5UdaETIrA9WONtHkto9FBlOJQAkYWqptpfplEYTPfYULokyFObGJHcH+rJLr6+vYBJ6KiMO0SpwmXYDraWUpQhCFx8g8y5DcCbx3WlBJZUgHigoKFGzsltp2rUEqLjdTiMnPFGa45+hJWYbqAu22F4pBcIfaKlNqTFZdRVKKFXNFmqVsWhcnNBkjMuDe6lWabMcq6XM1G6i01UXXOT0xdgpqpW5jn9YpqLaUx1C+9b7cYm6bNjpYRJgSI0yOsXQ9DebfYVu9F1kuJV7AfZi/X5N2n7wPgNey19+IPKijx24kaqyVxKlGaSG44qCkrealtNp6LapiUvF9CAlsrZ2uXaOuqVTKK4VCJnVKqK0HKpMKGNo+G1CykrkK2cJC0kFBlZ9yThmn02I1DhRW0tMR2EhttCbfNHrKvdalXcXoXFKUSfveUf9RVb9RewcaW3633W10/4sp9mPuGqr/RWXX+T8h1XHpKepBza5lHay4oHpZZCPSW2MRf0dg/r1TH2fDruTyV/SOh/4nFx7MRv0dgfrdRxyV/SOh/4nF8vJf8Aq2d+stYHeMLyAFexXkB9ErynKFdhOmHdqVl3aKLpXfMXPWK765xohV9egL7sNHlrzY03mjvMxOTmgfKm1YLHPAQpvZCOJarvDZBaUZ9Di1PZo0qnqSW0pitw5ERSPmDY5mSkpscgNspF0jAejwjyemFQKn6JsozLgyOJyLgKQ5CSm6tqpTDTLy3G287qkBScQ6JSm1JjREWzu5C++6rV6RJWjRx59y61EBLaU5GW0IbaSlPkpnJxpZ2lalc6khNriHTVMrSlfFIdmOMOJKcpJjlN8hUksxmEl1991thptO9150hDaEgestSm22gNM+UbsPRZLZafjvFl9Ct7brTmVbavptqQtDg3bQKG4DFT5NuuXdpEoTYqVEX5jUb5kI+izLQ4snUjnARfIEJGKX+lML/Cq1jx86+GnGFqadacDjK21FtbTqSVocaUggtrQ4A4laLKQtKVJIUkHAXIWhNcpuSLVWdMzpCfMT0JFgG5aBmOQBpElMhASBYYr39c1P4zXz7T1kdE30xU/wBFpv8AilGxriTPl8nErlTHlyJC26lWI6VvOG61hmPUWmG8x1ytNoT1DEqr0Ki8yntzILSH/lGrSLIeeyuDZS5z7JzJ0uWyR6pBx4+dbFArFWoRlVCdGdckv/KdZZ2i0yn2wdlHqLTKOghIs22kaX33OI9VpfJ9DE6IraR3nJ1VlbJdiM6Wpk59nOm90qLZKFWWghQBxp4ufF/o3wKO2vNF5ORUxuBHPZiGZMxeg0UECNHWm9kqYXYAqczSOasre5nEemycn9DDYsX5CvoM5wT347t4HWN+XrF+i0d5Va+/FBqal7SVzNuHPNwVc+gjm0harbtupHOUiwGR5JA1x7MRf0dgfrdRxychymm32JVcpbDzLmqHWHprLTyDpuU2sp0sRe4N7HF/uSpB4n8HtfTrvrilVuhwmaXLfqKYD8WKpWxlMrjPP7fm5WcvNSxkuwED8JG1v0Mcb94vvzIV1emhNh6p13gY5JyXipTsjk7RXXFr9Nxa6dGUpxVuK75z34Hfr+7tBsfzc2HoDa80Xk4w3TW9xvLITJnLv84OrRFcT6OaGDa98S1xmHH0QIip0zZAZmIiHmGFyD1obcfaSu+5K74SQrKQU2Uk+i5mTlVf5t7G49dSRu0xQq1mzPyILTU3sqET8Em2+gZDK1N2Au2pK/W/n3XhTT7aXELFilQCgR3HTu6sLkUc9phOKyD/ALp5Wa19egq99Ago1uptyJJQtBspPNHdP7xv2G+o18ov/HsxyNauC4Pl1R67f5ETmI6jlIH5p6scrmSfOLiUpaBxKG3pqXO2wLrd+8Y7vZ9WMpSkgi1rC1uq3v8Aj14dkJgLos50qWqVRnOapWtW8uwlBcI5jq6W2GnHf6R24SQ47QJ0GuMC5THWfk2cR81DT5cgrA1zqM5CvRyNi5GAFCu8m5dzkymRGS9ktfIUKMeW2jON20QNprmz4bi8rYIq0fRKqhCDUaoo4Zno52EaRcWsEtxFnKrzrqiABOoFRZmt2Bdbvs5MVZ/opcVaRIjq35StshWVVlEC513brW7Pj38cUpsnpr5Swrbr6U+q3OXq1CSeGccbYUF2u/Qqg0yDxcD0J9Q16m23e0gnqwR7e/8Ab97yk/qGrfqD+D2E+4E4MrZO822vNjIy+bEgtOPJbvwdW2ham+BS05xw1KjOuMyI7zchh5leR1tbLrZS8w5vQ4hYu2r1QNqrQKxSau+1spzVBhU+opGjapsd+YpbscaWjPtvNPNi3RK1t70Y5K/pHQ/8Ti4GIv6OQP16qD6hjkt+kVGPunsH6wMdunk5L/1bP/WWcDvGE620/Yd3stfhfTUjFT5S0iG7M5PT3nqhI5ujMukyZLhdkoeaSFKTFU44XGXcqWGRmbddbAZQd28e8H23IPzihCVeqCNcIkQZcuG+2LNvRZDsd1H5jjSkqT3pI0AG5IshLtSTXYjds0atJ2ysgPStPQG5oXY6KekvJSbWYc3YXHYQumVlhvaPUqQQolAsFOxJAsmS2lRG0BbZkNBaFPR20kKWbnjpu+z4/SzW6OUDFYW05niUlSaLFt6I5gSJJHAhc0vkL1KkgAnIEgUjMm8ekbStyNL25jkEW3C/PnYm/wBUKUNU4qLrabRa4hFYYsOjmfK25g/7TnzUlxQ+Y42q3TxRlrWERaqTQ5d92SoON83J4DJORGOfRSRcBWRSwrs/dil/pTC/wqtYH53s9Pcfm5vRz7k5tdbYaElpbReYaktBYKSth9F2nUn1krF7LT0TY23Yh1mJdbWcR6hF3JlwHT+EM9QdCApcRZ0blZb3acfQ7VJrN9jMqMyUzm0UGn3lOoBSd2it25Ju3qpClGp/otN/xSjeWb/WNM/WMHuP/iOOSv8Aucj9fleSo1eWcsamwpM588dlGaU6sJHFSgnIkcVqSOOJ9UmKzSqhKkzpKhu2shzaOb91nllCU+iEZcoGOV9WnNBcaXHPJpFx6bTzXOKkB2KYcgap1zKUkEWxVaPK/lFLmSoDp1GZcWQUAp+i8lIkMlP9G4hQ4HHKDkq65o9lrcAHQ3Tsoc5IB6kmCUIT0bNuKy3KyT4tu0+324jfo7A/XKjiNNiOFqVDkMyozoCVFp+O4l1peVYUhWVxKVZVpUhVrKSRpj/SmR/9xpP/APQw3Kr9WlVJ5lJQztlJQ2ylVs+xYZS0w0V2G0U22lS7Jzk5RanUWnozy6jLbYb4hrN+MkLQNdky2CtxfoIsM/pDEKmxk2j06LHhMA3uGozKGmxfjZtCBfrxVq5I/FUqDJmlP+sUyytTbI+m+5labtqVrSnjiXNkrLkmXJekPuHet55fOHXDw84txduGUptjljUJ7X4PXWHOTTBIseaGOpc9SVaKG0XIYTdKgQuLcEKF8T6ZKTaVTpkmnvp4CREWppwf2VoGXgbIXv1xXeSrq9Giis05KjrsnMkWc2B6iWnOZZUp0O0dXvzEnX/6g1SPd5NPdxPVvw7V61MRGis3CNMz0l7L0Y8VkdKQ+4oHK22NACtakNIcWlyqqbMaEw0IlMiFWZTEVKiu7qh0VyH1qLzqk3ACm20qWhtC1MVtlG3jbNUWoxb25xCdW2XEJNlZXkKQh5hSU5tq2lK/wZUi7VXostMiM7ZLqTYSIkiwK40xkG7EhGZJKTdCkqS8y47GdZdX7/gbe7S/tx4GLyJDDKddXnEtD++r34cgzEUyswnhZcZ0RprJ35TlOcXTrlKRmT6pGHJ/Id3mkgFSjRJj6jDdFr2hyFlTzC9DljyHdgq4AeiJStSrsLqFCrkFYbUwq7DnpEhtxlXQejvJCsrS0qbeRctLKcrzw+6ouUaqsJ86W4cuVCmKHRCo/NWX3GXVm5VGey7M287lVYRkQkOsUKlB1MFLoSl6S+6Ql+W+EEhN0tNpZaOrIz6AuHFPrdOUEy6e+HWwoZkOJOZt1p1NxmadadcQrVJzKQUqCkpwJMBXN5zCRz+lvKBkxFG42qbBPOoS1ghmW2hAUAoOtsPJcYa8bwTf2brdn3nKT+oav+oP4V3kewqsfhjlzQakDspUqllp5I87EktNSVx5TBuLLZdCCU3CXm9pHczNOuJM6iVZnYzYLym3QL7NxJHQfYKvSjSGlXaPouNK3dIpx48W426yTvJvyV/SOh/4nFx7Mcn6vZWym0t2n3t0Q7T5injc8CtqogpHEMq7cQakzbawJkWa1muUlUV5C0Aga+cdbsRwQtRHoi0CsU15L0WdFZkMqBzaOICsi/muNklDiD0kLSQrAPDuv48HcDjmkJ5L8agwkU5xxBSpBnqcW/MCFD0g1nYjLIOUSGH0CxQb0WkJSpRqNThRLJv6D0htDqjbUBDRWskWsE3vjXhp+74A+7qwL5dbp6RAHx1Kt9rA+tuvh192jpp0x0lRmUYmnOqWv0nHGG2+ZPOKI1ceiurOvS1N3FULlOq4BKItVhBR7M82GsFPsprileqBY3RBr0Qsl1KjGlM+ehS0pIzqjSE2zFvM3tULQ061tEbRtOcYptagEplwJjTyLWG3SFedirP+qkNZ2loPRIVuviPIbN0PNNvJPWl1IWn+6Rit1wkbSFBdMUH15r2VmE33LlONJV9G53DCnHFFa3FFxbirkrWs5lKud97+lvUbk64qUqBTYE56otRmVOTTICmGmFPLKGdg4jR9TiC7mv8AiG7W6V6YahS6fCdpqpGyeiKkZlMyNjtmnA64oEZmGy3pdF3LWzG7biVlpxJStKwSC24lRWhy41GTKDfgcpGuKFXARtJkFvnQHqTWLx5rfciW08E/RseOKX+lEL/Cq1j2+8XOh60nik6HS40GOSNYprGavUWhsuN5E3cnU8Al+DlBG0cbAD0NO/MhcZso5xfHEdep0Jsq19L26K0GwNihRyr0T493cOA3DWw1xU/0Wm/4pRvLN/rGmfrGD3H/AMRxyV/3SR+vy8e3330t46sR6Iw7llcoJqW1Dcv5PgKbkyVdHUIck8yjncFJePC+Ov53/De3xR6V/T+iLMUCn0WkvMsOSXFypCpXOH35T63s7qWnUo6Dam44ygdFgYl8oJEOPCkTQxt2ou2LCnY7DcbbDbLWc6mWmUqtbVGY9NSlGhVoryx4stLU0km3ydKCo04/SU3HeW82k3BW2D6SUkAg3Chf+6La8cRv0dgfrdRxT6YwW0vVCdFhNKcWpCErkvJZSpeVp05AVdKwuNLYl16bIo02JBQ2uQ3THqi5KS2tSWi+GnaYlGzZW4hx85+gwlxzQIx1Xy6HgVi6b8Qk7+kLi9j0knEiuQ21L5RbdUKquSltuOwk/wBA3ACAlTEOW1lWt4jaPyhKYU4WYzbbffw9v7j78U/k627ldrk5D8oJ9L5MpbjTzh01GaaqIUWtn2S0G6Nokg20GW9vSHSHR10ukWKr7gQkdEWxC5PU+h0l2PC2wMh9yVtnn35Dkh1xxLboTqlZ9EWTlCU2TYYqFffiR4T1SW067HiZ9glaI7LCnBtFKUXH1Ml9xRNypzfih1Va9nF50mHPJNk8xnfgz619Yjh0SwOuPi/Xr/Ds/n37j9548br4nIS+VUmjvPU6mMpJ2KkMrLL8q25XO3mlOovfzOwULG2Ph7hbx79+Pf8AEWPvBIPWCRuOIK9sU0qqvNU6rtrV5sR3lZES1A31hrKVgixyXT6BIx47/rvjaJsuS8SiO2d19Luq+g1dNz9LC3pUh150mxKlnL/3Y9EN/NsNba4DjK1tLSQc7RKXU9qSN5HUcLhTVZp0dsOBXB9m9go/7VOmcdZScPlDDLXKCK0tVLqFsq1KCcyYkle5yK8sBJ2ocDB842AVLC3GXUZHGlKZcSd4UhfTSocCFpsQeACDmy3Pj3DqHUkaDgNceOIt9RI7ievFPrlPWdrFfSXWsxSiVGXlbkxnfnJdZ6Fj6JstGVaEqEWZHVnYlx2ZLK/nNPNpW2odikFJ+85Sf1DV/wBQfwr87/zY5V9XPab/APsS/wCHaLjccDlHS4+etUVlxUhtoedqFMHnHWwPXkwwFPRU73El1gXJZCfGmg0/83eq3q45LfpHQ/8AE4uBiTCjJHypBX8o0o7s0phtxJjqPVKYW6yn1RILDjl0NnDjDqFsvx3FMvMuApeYeZXZTbqfUcSUi4HX81Wq26S8zIp7y9o7S5zRei7TXzrWRxp+OrUlewdQ24bF5CylOHqfGagURD6FtvSKYiQqo5FpyqLMl2Q42wSCcrjSW5DKgFtOhYCgom3FRVcW3amx1DnE3OXN01XAUlbvLaewtMWAHYdH2gID810bGVLbHR2jcVkllKyFI5w++kpbkxC3GU4taUNtpKlqWoJSlKRdalqVoAEgkkkdtt4+T6a6tHJqmPKERIJT8pyG1W+VHkWALZKbQG1XUy3mWsNSHXmWm4iZjVYprVgiNWULkqZTr0GprZTNAPqpdceabyDZobGbOA7yNQpy3SUjlBs03+dkNHeyp6kqcJ78QXJcNinQ6cJHMorCnHFjnXN9st6StLYevzZrIENoCOlp0hiDSoLe2mVCWxEjN3tmefcShFzvCEqs465/RsocVcC+GI6PQjtNNJ09VpCUDTXeB7N2++KJyVZXrKeNYn2JCkx4148Jsj1tvJckLtw5o2rfjW3tITdWlkC6koTmUk8QAlSreiLNvtcmiW3W0OIKqrQ0LKHEhxBUhdTSUqyqCVpKRZaVaYceXyYORtpazaq0VaugM9ghupKWq4SQLC+bLjTW5IHonMDoNErWNFbxuyoT85V6zyVeWPwV0ViBmN1KjyTzaagDgGJDcZduPO1q34pf6UQ/8LrX2Y9v2nHJEf8AueP8Mxt7fqvg8pqXHIodaeVzpLYGzp1TcKnXbJH9DMsuSyBYKcL8dsBa4+X7N/vO4nqtpkyetmJqf6LTf8Vo2Pd9Xkm/1jTP1jB8escclf8AdJP6/KwL+P37vjiZFaXeJQGUUZqyiQZDXnagfzkS3FRldfNEq364iUilR+d1Cc5sY0YOMtbRQQtaiVyHWWUhCEqJUtY4dWP9GD2XrFAv18KpbjbQndr2u1et0Mw6cyptt2R8oUuSG1vrDTF2ok994hx8ttFQQQkOXPDH0d+uoy20UN1yNTlUnhY3BOKLJdXmmQG/kid0s521PCWm1rJ1LkmHzWWvqU+QNBiN+j0D9cqOOS36R0P/ABOLh6HJaQ/GlsPR32XBnQ4y8gocbUD6ikFSVerY678VShOhWxZfW/T3Dpt6bJWpyI7f5+W7T+Xo7ZpwDEZ2S7lpFVLdOqoJOVppxw7CbYbxBfcLix/qXZJAzkHGYEajMDv01OhGh+rqFiMVMNqvFolqHHsrQqhLcMtdt1jNdfQFb1BpNyQlNotIo8YzKlLLgjRwtpoObJpyQ8VuvraaTZhp1WZxxN1BIvgf9GN3/vigdd+FV/s79QnXebqq1doZg09LrbLkgTqZKyLd0bu1CmyH9SnJcNKAzd2PR3cDm10v6yG94+jfgd+KFUHF55TUUU+cTbOZdPPNXHF2sAqQG0SrAADb2SALD+d/X3ftwVSXczluhHbGZ9f9gW07dw7cWhIZhI9U6Pu+0qSWr/RydHjfGf5SkAnqKEj/AIEpCfhjWUJI4IfZQsdv4pCHjf521sjiFXwnbQHNrbp7J9tTd/okpBt37t3DyHuxJZfzbdp91t4K3h5CsrgI4WUN2lhYDQAeVtCEla1LQlCBe61qOVKNNdb7uNsNBZusJSHDwUQLL9677vZiGlV9mIyynqzqWNr8Et3G7s8sEI9Fe3Dtvmc2dXr/AN4hvv44F+q/2/A2PeAeGOVkXZ7MIr9UW2i1rMPS3Xo9uzYOIy/RyjcB5fcFfmnok94JTbHJFt7NtU8nKLnC/SSr5Oj9A9rfoHu11+8qtPZttZ1NnxGyd2eRFcaTfdpdWpuLYWFclpx6RupCoq2+sltQk6pCvQzC59a+K8a/AVTnKlNiqjMOuNLe2UZp5Klr2SlBOYui3twL7t5Hcf8Ay3v3i+JFT5H0rn1JqKlyXYsdbCHKfKUvM+1s35DRLDpXtGAyFhHTT0U7MY5PyJPJ+TBixaxAmPypa2ksssw5LcpfRQVLWpQaKU2Va51vpj6sft+vx1kYcrNFcapVeX0n84Ig1OyUJBlFsOKjO2R/K2Y7ylFStuy6crjSm5vJqpuJGY84p0Z2pQ1I4KEmCiU2yDwS+ranUqS3bIEsxeTFefUSBpSpikJ7VOqYShHYXdkka69TE/lspFNhNrS4KTFfS7UJAB0Eh9vOzDbVpfYPrf32LBT048GFHaixIzLbEeOwkJZZabSEpaby26KQOrD8WQhLrEhl1l5pW5xp1BacSfoqQooVodFEcTd+VyNqCJbClKdRSqk5sZCcxvso84p2EnecqpOwWmwDrz107NSKjyYrLOS93m4TklgdiJUMSoq/p7OSr1cyUcdkpl1Lt/xamXc9/wA0pCrnsHfhDdM5N1Z/Ob7dURUWLY7tpOmbCK0N9to6M1jk1FlfLtdcYm8oNmpDDbF1waWl5JCsqnEpU9KKFbN1zLkA0Z6KypW/+Hd13uddTxxVqlEoMqoU60NmmPsOR1N81ahsgpyrcaUlXOTIWrNn6S9LWxSRXKE7T6PHmR5NRemOMBC4sd5D7kdCG3XFuOSsnNUkFJa2+0vkC7eP4ezhv9bA6+l8RuHtse5JxVjQaE9UKPJluyoD0VbJS2zIJc5spDjzam1RlKUwPSzIbSo66mk1KZQZVPp2WYxU331x0t80ehPJCcqHHFKVzlMZacuTpI1zXOGINDjc7mw6xEqPNto2046yiLLiubMuFKc6TMSrLgf9FKjqdOlEG/pZbqkelYLuqxSDl01xycpNQQGpsKlRWpLIUFbF7ZhTjWZJIJQpRRmBsbaYm0SpM7aFPZLLgScrje4tPR1283Ijuht1ly6dkpG09TEiJGorlVituqTHqEBUYMymiolDiWVyNoxZGVKm3PRIyi5SpSqnWK9S3aXGVR3Kc0mQtlTz7z86FIzISy45ZLaIatpmt+MRl4493j2eSbTKNGM2cZMKSiMlxttbiI8hKncpcITog4t9ydQzaD0oourQ3JVI3HXMQO62OT9JqbWxnxIf4SzmSvZOPPOyC2Sm4ujahJ13jDnNwkv7NzYhfoF3L5vNqDbNvsRpcbyMSpEnkzUHZD7778h3aQyHH3nVLWtK0v2VmvfMR0llatQRZ/lFylpiqcmLDdi01h91tchcmUpKHZADCzkDMZDrXT386zJGdsKScVahvlIFRgvsIWT+KeKbxn7cdhJ2Lm+2ZKQbgkYWz9ystYQot5mnIakLtZBcatIA2asiSm4zWPSvfHKGPygprtMp0pUF6G3IUwpxUxBkpkqQllxdgpnZB0r/ANWyEblYhVmhUpyqQzR2IjuwdYS6w+xJlLUFJdcRdK0SEKbKb6hy+4YoMqXyekQocGrU6dJkyXoaEoZiymnl9HnJW6rodENDh0r3GOvTTu4j3jdx44hVGiRkv16kkoSznbZXOp7xu9GS46pDIeZdyvxy8sNpvIG9yxI+5Wf8wkc31N0i6Fc4yBKraZibHPnyoLWIcKtxxH5SxIEyNHadfakX2JeRTBIeadeZUvZ7FLqg8o2Td3U6urc5LVJx1x1ZccK4zmZalZiva861Sq+YqWCorK7nE7lFykp5py0RVwKbGecacf2j7iVSZNmiQ2EMs7Ju/ppkub8uh4dXdv8Arvis0FWTaVCC8iMtd8rUxsbaE8oApUW2pSGVOAHpIug3SojCmxyVm9BVug5DLdxYEhSJNinJk84kDMrNbQY5QQ+UVOdpsORLiyqe3IUyXC+pMlM5YDTjmVshMLKDZOhKRcrx7v51tQA5KdumKydM69NT9BPpKPdhciQ6XXHDmzq4jgQPRSm90pCQB0fvr+N3g4l8p4Mda6FWXVSpLiEqUKfUnFDboe35GZKzt2NMmrqAptDaRj+Hv0J06tT34+0+iO0639g39YA1h8o5sdbdAo0huShbqCBUKg0u8dhq+jrTLiQ9JWnzdgy3dSVuJV7u72e6+EuRxeXEzraT/rEKy7VrvVlTl4ixtvwptacq0qKVIVdKkkerlVrp87crhuOLcfm36VuNk719ydcKqktstrcRkjNqBCw2u+Za0+rmGTJfpDpXOuPtx929MjuvRZLLLVcQ0krMaQwhuOzNUkZlNsuR0IbeeSNiyphKlNFyQp3F+HeFfVu/NJUobys5gE/ZvPsRvV7FADje4tHQW3G6JEcbfqk2xS2WUOA80ZXreVIWkICUqVkSFLNgMJbQAlKEhKUgAZQLgAJGiUgCyRw3HUfeePH7cbvLe2v3u7y38u7Hop92NBbux48W7PJ467/Wcai/j9mh6wSDocePj1ntOvk3b9/l8dn7B95u/b7/AL/d48DTqxu+83Y3feeL+/fjx44e3jjd95YADhppp1d3Z/Ox44HEjUlqITEa1083+MUm3z13N9/Ddjx4A7Bpv6z99r4sb4cjSmGZMZ5CkPMvtpdbdQrehxLgUlSDxBBw5JhGo0QrOYs06Q05DBO/IxKYkrQkeq20tptGY5UdSJM1dTrSmyFBqfJaZi3G4KbiMxnlJV6zbjjjTlgFII3tRocZqJGZbDbLDDaWWmkfMabbCUIR1BKR8fLmlw23FHQrGZCz3rbUhZt2qwFx4DQWn0VOZ31JPWkvrcyntHlU24lK0LSpK0rSFJWk6FCgdClQ0Uk6KGhw5IZiyqG+6pS1KpUjZs3UbkJiyEyYrKAblLcdllsFSzludEPS5VcqjY3xZUphhhf55hRosgjsQ8ntwzT6VCYgw44s0xHb2bae3rWs+s4sqWr1lHGnjifedT1kknU/kq4epJPuGFLUbqWpSie9RP2n7/x7zru68WJ8dh008HHj6sCx+q2/pXso8N263HB1H8b8dAbbtL2trvxvHv6t+uoH28Mfw17fb9nkGtvHVv8Adjfu+v3A29+NT4+32cbDjjf4O7Qjr6+NxjePd9nvx41Hd19mLC3wvpw9ovjr99uvgDbTS50xzUzonOf+r85ZD3/2RVtNeBy648fXjf46u/2Hdi19eAGt7dlgTv6Vt2nXjf3ns6/buHxxluAbXtdNx1aXvr3H2Y7+NwU9ovYagA8NOOO61/aL23WNtE6fXiyjZXV0d3A78w6terTG8fD9uL5rjsG7vxvHj931Y8e7r3WOo44A6/qG/wAdeN47ri4/b19mNfqP17sfu1vu92vDqxv47+ux1Gp7Dx7se6328OvtPk8eL9WO3q0PeNN/WO7A47/bb9u/Fgfq/bffbcjFz8Nd1hvsBv67Y0IPDTdm/cNT0uGmNTa/d+784b+22NPHty2PDdxuOGLXHu+zPmv/AGbY/h494Hk32x6v/EP2fZjx/H4YGu/h+/vIHtxlvu9L0RbfrYm9vYb9mNf3dovr8cvZxxvHw/fj0h9f7NcaeOrXdbffjuxa4BAub2Fvzh6QCrG2mDf9w6wOPv6/Jvt/Edw3X9Ydx4b79vX27gN99BfH8fsSq3t0xob+7420H8Mele9su7XTedOON/8AD3aE2OLE2Psv3ewW9+PHu8m/2ccfDq6rm50039uO7ssPA+u+N9u/dj4a29mtxv4Y3/EafDGhHv8A3fb/ADpafnJUn3jD7JvmZdcaN/oKP349v1X+vHKSnQeUNfBPKiqU+BDjVecygBFSdiRI7LLb6W0gJS2jQAnKCbkk4bqtUncsafH2qWxJcqsx9hLq0qKEOJVIcbAVlOVUhBZKrNKSouIxymi6r5WQadOpyXYy0R+evSYTxpspKrtNtOuKzMv2yo27K1pAQU4an19ip0+I9KENt1VXZkBUlwPvobtGlvLBWy04vO7l3W6Vui/J5Os1KpNQ3EtPrTVGY6W31tlxLf4XLZUo7NSVZms9vo6XodPr21FWjRlplpff5w6k85f2SVPbRwKys7NOiuHXfyVNSSUqTAmFKkmxBEdwggjcQeOIlZZnypIQ8kTIkmU8tmbHUVKcadzqVrZS9m7bNHJ2l9klxtfJms0KfIRCqtaYczMPLZUofJ1Szx3w0sdKNIQptxpd8khk78qVYqZlSH5Sm+Ukttsvurd2baabSlhCM5OVOda12TbpqUreScQGF1iqLYc5aqYUwqfJUypk1NbBa2RcyZNl0QMum8WOuD3d2/t38f2YpTL9YqbrLtflBxp2dKcYUFbUZC0XMtjlSlIACE9WHuR9NmyKfTqcxG+UhFWplyfImxmJiUOvoyL2LEd1oJaS6hD6nXQ6F7MWjVqIKW6JkVudFgGpFE9xL7SXmj0mhAYU8lSLJ55ZKirbKaCb4qX3Uv1CNDU6limUee4h51rYk7ecOk8WGVqXsWkJeG3DSnltFrmri63Um1luWqMqFAKSAsTJyubMON5uiVR0rMogg9BlXbinVn5RqDphTo0iW0ZT/wCFMIkBUiO8hbigtMlpTiAVDRDuZvKpIIjyo5C2JDLb7Sk+ipt1IdbUOxQUDjlEuJVqjGMOppMbZTJCQxlZYX5psObPLckrQUlCk+kk5U25UTUv835Q0jk/UmamzHcUlxqQiE9s5rKgQosyUAuIWPXS6n1U4qbEidLdZ+5mW6pt2S84yVoqdLCHlIUopU6lLrgCz0gFqF9ccpKvDhVJ6kKmyZUd9FXjoaRBALgLbBmbRsIQD0Agb92I9HpD0+XUZRc2Efny0rVsGnn3fOLeQ0MjLS1elY5ccoTyrjS4jMpqniGmVPjzbuNmaXykMTJRQEocbJ2obzFxOXNlOSNydgTX2IlDhpXObZfdZQ7OntiSW30oUEvJZgc0W3nCi0p2RkyqJxW+S8+W/JWkIq8AyXVuuhAUmHNbzOE9FP4AtCUaDNI0snFblU+ZKgS0u0xKJMR5yM+2HKjFQsNvMqQ4nMm4VZW5Vt2Jf3P1TlZUzDU1zrm9bqA2XOlPKZzky05dqWn7L1tksdAkYajV2ZWH0x3GRUaPXZL83PGdspeyefU6ptzYKU6y8wtLZU0NttE2SWJTZu3IYafZNgPNuIS4k6E62OY26O4a2JM2BCnyWoFDSimBDElbTapjaSZri0hWQvNSHlxtxsGMybG5xU6DPkuSZdGlIeYdedW6tUCck5EBTpU6rm8hh4LKlK/GoA0sPIe7HJxmTWKpIZelTEusvT5LrSx8nSzYtrdKN6RbTThisV123+TYLrzSVk2clKSGYbObeOcSnGY9wQRtSoEEXwmqprFRM1qSJm0Mx/MtSV7XKrp5THWoFIbIKNln6OgwBCluwlV2lR5sGQ06pJiylspkNtuFsglKHbsS0m5yl/LZ1LZS5E5Qvzvk7bu0yuwZLz76oim1nLNSySpO1hlvaktJ8/FUtDIU7IZSUM0d+a2zLloplCgtPPNNtsFZ2ciQGVZUKeP4VUJWpaZv01MRmk4cU9IfmI5OUiS+5IlOrcfmzLLfWA44pSxzua6pmO3m81nbZT6GDVvlmpfKPOedB7na9rts2cebB2ezt0SyEbC4VdvdikVxjRFTgRpRRfOWnFtjbMKXxUw9nZVfpeb6Wpx468clG6TVqlTESGqsuQIE6TDDpQqn5S9zZxtTuzSVbMEkIzLsBnVdmsUmVyxmwHtopmWzWp5zBhTkd4pRzpLl0PtEgXurUDdiJyU5RTpFTiVEuRmnKipTlRhT221rbCnnfPKQ8tGwcbfddUgqbLWTpZp9UmqyxKbDkzZCrbmYzCn3VAXOboJNk78446YmVZ2q1BEiXNfmBDc6UlDBfeU9zdpG08002lY801lQCUICcgtiE45JdYXXKPzSZJiuKS7GqCAqPJcZIUld25zSnGwSNo1bNocKZqj8+QKfK5vV4Kn3XkVCC4NHmNqvKraR1JlU53S7mRS1IjuyEYZh8nZE8Ulp5qn0aLFccYcqMhx0NqlKaFlOKmP5Wo7bnRQylm6W33XziOxVJrj7kCG/U6zKfkLfbS9k5zLDanCfwaIkbNki2dphLpGdxeKhWF1OoNOzJr0tppuZIHN0KXmbjsJS4nKyhtKUDJa6sma+Y3odXcVnlri82nniZsJaoshZ4+eW3zhH+zeSRofJyXRGlSI6HZ1Q2qWXnGkuZGYuzLiUKAXsytWTNfLnVltc4qHJSpzHJE2BaoU1chxTr70B4hEhrauEqUIbxbVZZUrLJNuiiwapFOlFir15amw40oofjUxgpVLeSpHTbL7hYiNH19q4UnM3pylakTJUhkQIjiW333XEpXzlXTyqUQlxQAKiBrexvjlFHjVmqx47MiAhphioS2mW0mmQVEIaQ6EAFSlE2TxwgnW6Be/distMVipsstVqKhtlqdJbZSkNxzlDSXA3l6xlseN8Dttfjwtx7Bg9x+r3j2YpLL1YqbzL3KCSlxh2dJcZWlW1BSWlOFvL0U2SE2T6trnHJNuBUJsJDrFXLqYkp6OHVJdpuUubJaM5Tc5c18uZWW2ZV+T8mZIelSHBVc78h1bzy8tbqSE5nHCpasqUhKbnQAAaY5NtQKjOhNuQJ7riIkp+Olbm2ZQFLDS0ZrJFk3uE6kWKlEu1Pn0r5RRyXrjiJu3c50hxh2e204l/NtEuNpbRlWFZgU5r5tcKg0StcqanLajqkrYZrtQDojpeSlx3pybKCVvpBFws5kJSbJAEcVWpV9h7Kh7mVdkSZ8KXH2qh6L7rwIWQG1hkpeQkmy0LKFppVcaQWkVODGl7EqC9gt1pKnWNonRexczNFY0UpJIsOin+ceN+77cGalJ5tN6VwNEvg2Wg/nk5vbpjxv4j2fb9946jjlLUI+UPweWtXmMFaSpG2i1uQ+2kpBSTnKMoHG+ByemLacjSXmlLhUyEhEiW4gpcjtLAUp1eR4IKGgUBbmW6XFBCcTJNYYVEn12Q1J5k5cPRYcdotxUSEabF9Wd1wsKSl1hCm25CW5KXWWqV+lEL/Cq147ccpP61jfqYH2Duxx79/wBhxw7LdX7cVX+rpv6s5hVOpLW3m81mykM+u6mFGVKcQzuO3WlvKykHpuENnRWDydU9mpIqyKs3HV6UeWhqTFd5sMo2aH23Qh1OiMzW0QhCicVVJ3/dPMzcf/omjHf7RuxBVKUGNjy7Afz6BpZrRZWkk7kpkaZj/R9M6DC1LVkQhKlLUuyUpSBcrKjolsJuSo7uJ0Vjk86xdxD1cedSoD00bOQ6FZbgjoBLyr+glLqTqnCK/RJcWHWUMNx5DMy7cWehonZKVIbQ4ph9KTsjdh1DrYZSS3kudtHTygpsGEpbqlUybz6lISAvzj0OG/KiJaGfRMtqzYsVptlQE8l+UqWXJy4zjlPqLDSY65RYbU86xKZQdkp0x0rdbeZYjIcDTvmElOY0Tkqyq6IiFVioDRSFPP54sBtY3pWhpMlxQOmyk57ZkpI5HyqbVIUrlDtJRrkWOoKmETyuRDD/AEQlPye02phaTudWlCbAaxITjmaXyfcVSXdenzdBDsFZSellEVxMbOrVa4zhJUoE45TsoIDjtWQhF/nFhhIueA+d2DtOK7RXPwSU5DnUKqR1dJiRCmNuNodSAOk0Evc7iO+kEJtmyrWFVPS3/RedoTcj/KtGG8aWI3cfncMVf+rpn6o5jkzoNV1g+35CqR+vh7N2JU+SvZxoMZ+VJc4Nsxm1PurPYENq79BiPWOVMqPFp8usyazUnJrv4MlDan5zMLMvNmbcWlmFlVomO4pCbJQkCNPp0xEyiQa6/GTLaeztvUSYt2MVKNk51sQZO2KFAjbsZ/TSFYrShuL1Jsez5Vi7uw7werFVc5PNsKTO5sag49BVN2LUPnJbX0VoDabSHs2bRRCCeihWEB1SJNWn7LnLqcrESFAYLKXHQyBdMaM2UdFJU+70U3ck2Q9MnkJ2FBo9mWzpnVGjbGIzp0buuhplKRuChbS1nZXLOoR49OXCqk2Y9MOUS5MxhccdMDoyQ/MVLSoDMVxrbipKoTZlNPQZ0l7k9IkMrzxn0SX0MxZSHCAC3zhEdxKwElIWsXyqUDwtvzX3C3pG/wC724JSrMMqiFaWuASRcaAWHfjkuP8A2uXvGv8Am6d6XVoRu6jikclmXDtKlI+UpY0/kkAFMdLw4IelKU+nT0qengVAxpaKrT1cqBXpEl2DtrSU0t8N00R8uz6ZbfjMzUhV8rT8kA5dMTuT7yyp+hSc7Nz01QKit5xNjxKJqJhPzG3I40BSMUrlTGszKqTiqdOaTomSuOEvxpZO7ahhssrvoQ2wbFSArFX5XvDaT25rlFihWa0NkR4syStIvlK5POmhntnSEOJCgHnc1L5NsLIercznMlKRugU2zgSsAgt7aY4y4jTpJiv/ADCMLnKqkAcq01tMtuFtPw1VKyt04xsmW5UqSvn2ikq5tdRUb4qvJp5zz1Hl89itmwVzKo5i4gI3gMzUPPE5lHZy2BcJypwPjjkd/u9Y7/xlM3ey/Z8MRaLSuax6ayJbcOS9AU64FSZD8lxaJTv4O640886pKRtbZFIUgpyhLPKZ1KlUyizXpkqU6Px1SU0ossJTYfhIcW3KeyXQhrZanNlwijNuZZXKSZzewVkUmBDyzZmqd6StMeMses3Ksu+Y35SS5lVhMcp/laE/R4bjtpDkaAgtSUoOUkmYmoTLD1pMOGNwGK7yVeOrTiaxAvuU24W409tsKP4ttwRcoSB+NePXim8rkDZVGLLbpklQGkmJJD62i8rguItKtipNlWfUkkpQ2E1flHNTtHqAmNHgtEDZty5qJAel2FumwwzkjAgoBkrdCQ600tDlPaVaXyikt01KUkZkxEfhM9dlXCmy02iG/ppz3grXHK9+pVSnxuUfOIa6JFeXs5OzpoMiXsTYk88amlhpIsC/EZ4nFc5KurttgK3AGmllIiTUIvvTlMN1tO4DakDfj2+7xv8Abjkn/vtS37vxcHf2Yo/KBht38GfDrrW7ncF0rizI6L9BaXmtoE3vs15XRlU2lQqFZjJWqA2hMWmIy22dLh5nEvujeOcKL0w57lCnW2bgIQkcp+ymRB7edL3+1XwxynzaXk05X9lVKpyxbr82QEq3bXoqxHksrC2HWW3Wlp1StpxCVtqSeIUhVwoaWOK3zdXOM/KJllBb1C3kGPHU2PpIeztn8wHicJufV1PjT4jByKCgLg5ClWo3p39mt+K04oZTZQ+6KRqL6jz67jsyWPcRjkcvgWK2nOoHKLLpRJ04jMLDdrqDihoQoKMd2qMvNggqYc+V5r2zdVqnPs3W3CPmuDHJxhKwp5mlyXn2gdUtPywhoniNoWnLfmHD4WCD9yNfXroSlb1QWhXcpCkrHYrEmqcnWkvTHKc7DdQ5Ecmo2DsiO7fZNHMlW3YYAcymyC4nMkq6VNZkluoVR60CnRIyGocVpCnCXSLk2Ga7sqQo57BVlJiskIotCzpcVTaexGdcQnI2uQAVSloRvSlUhTqgk6gEBXTCyf5xbDkWQOi4DrrdC7dFae1Nt248QcKbkNks67KSn0XEDcVK9BDmozi2umXccfx+3+Hk+Hbj/wD0nyeLdX29YxMaUySpf/yjPqyFBOdhzlKqzm7LsltKz+lYp36YGVltHcgJvpu0FiOzUX13geSAiBDkzVs8oob7iIrK33G2fk+qtF7ZtpUopSt1CT+fjlDz+DKhbWqslsS47sdagiIgrKUupSS2kKTYi5ObXdimQ6FIk06juxGHGJMeKyvn1RckL2jSpEhp5DhZaQjJFaShzzqnVIeSgpxRJ1aY5tV5VNiPz2MimskhxpJXmZXZTC1/jFx1DNHK9iq5RmNSQ2krWuBLSkJClEqUwsAAJBuSdBilvzKRU4jDUWrZ3ZEJ9ltGaBJaTnccbCcildFOTpla0AaGxPKXk5T5Mmm1h489iQYy3jTp5GdSwy0kkRZ3TdB6LTElTiVFpKkDFTRPhSoLq+UktxCJUdyMtaPk6kozobeAUpN28m0GdpTiVhC1WNpfK/k5DkT4s94SajChIW5MgzAkIXKZbZCnXI0nIZCyEqLL5fBKG1ITj7lHag+826lMFwMwEpqkpsAN8zUpptUhalJRlKGUtzJN3MxczO4HK/lHEXDlbB1uj059GWYyh9BakTpLWQORlrjkssxlAv3edXIQlWRSo82kM1ZPJ6mts/Jj3J91/aiSpDa5kqoIifhoeS+Cy3dBi81baUw2hxyUp1dGfqj+1cQWvPUiAKiELBbybQwUu3UCrpuMGSsnouXBw3yvrUCRS4dPYlJpjMxpyPKlTJrTkZbyYi8rqIrcd6QVOPJa2inkbAKAdU1JfcpFXjNVuutx0Pu0+Q1zKkokNRmXXtu2jZriQUByTm3qQ4nKlSnE4qMmlVmqS6lHhSXYkV1qNklSmmVuIjkoaSvzykoTmBz7S5zdUqmzaRU40GtwVpccegyUoalQQ5JiqW9l2aUc3VKa6Shmceb9I7qy8xRqo805WojjbzcCQppTakxDtEuhotLaykgm5soOA6WGE8pKNGUqu0hs84jsJVtapTeipbeVLWd2XEAJYTcFxlb7BzK5ts6i/OpdQgsnkzKRtZUR6O1tF1KkqS2S62jzyktrOVBP4pVwMVFloKU49BlNJsCSVKYWgWASele2nwxyfkyaPU4sdj5WU47JhvMtt5qROY6TjiEp1W4kI6768MP06lw5kt6tTGITxhR35DjMEAypTpSwhdw5skxChYyqQ+5bUXFSncoF1OhNx5KIkNnmuzckqbuuW6USmRaOnMw2ypIurM9mPRTil/IqKpXIU5l7aOCGqQqPLYeGdD6Yieih5qQ2WVEXc2b2+xwhdRhzY8+KzR4EtEhhxp4/J9XiQxLUh5IV51lCJCr/ADjfHLxmWxtYktqiRXUrTdh5C0VxD7e836ChfsWQN+NpBCyimzOcU9Tt9lUqVI02MhaGsig5EccjS0oSVNyAp1BbdDaxybj8noNSmxOU6mKu8+xCfcKYMRhmSy3LQ22VNOuyJEYlNkKyxXEG6AU4+V+UMiqUV9yWtEOLzdpClR20tHbOIkM7VGaTt0IsR5lsK3nEWLRmatWILsZmZFmohurWh5t1zax3DDQNk6ypKXx6PmltFO44pz9EfnUnlA/DhypsLMunyakplhbU+nIcshxgPuBUmOAAh5LTaHrNuKOKlyZjTJtJizFP88gyIiUvtOOoU1JcQqdFW5F5wErDy2S3538XsClKUt8s6vCkQoEJh/5K5028y9Nly2Fsl9DDnnOaNRXXgl9xOydccaMcqKVWqmxodWLSZrdBppMGVsxHiOGJt9tsNmlmQ+H5oXcHZuAZshy4cLPKKqGYI6tjmai7HnBQrIVZUZ9ltDZR0VkJ42tEalUeqsRKkiTRqiVwJOVrOsONO7QJLezaqDDTKnl9IR1yClWVIAoXMIEuZsqwtTwiR3ZCmkqiOhClNobPmypOp3i/biotT4cmCt3lJMeQ3KYdacLRgUttLiG3EBWQqQUpNj6Kr7sVHmtEqj7EBxqh07LT5WVfM1XfXtlNpaU05LdlrbkIvnYUyjNkOUgnlHVDKLIv5mLsVPBI6X4rMUeqNdEW6zenok0WqIYlPvUGoHmMotZZWTK8HEoU0llqS1GeW6cqgwytKVbPOnHu+vx8Mcj15VZQxWU59Mt9pSj2b03G8YptDkN7KU63VZMB11FjFqDFZqTsJ7T0UKT0HFhYVzd5SAelj5BqDDzUSsykUefFWhW0iVNt1bUN1CUNlCMr2aO8UrS2WpDiiCEM7NyNFotYkQaNEjU6IWYMxbMl15ImyXmlBlTe1ddeEckKyrRFY6sQ3J9eqTFRcisGW20zGLLUotpU+hsuJDmREnabJWqs2zVpxpq10erKbiVhyjzXUQZKo7sOS4unvyA4lnZqYSlXPW7E/i0K3642cKHJlqbq9PeWzGZeed2ID6FqLKGlKISpxHDjjlSuoU+ZBD8ymhvnUd2PtVMtS9rkD6EEhIcRn35c4tbTAp8OkVV+BQ4TMVlbMGQ4y7JlpZqEl9C20KTlXmixnFf7HvxT5FTrVTi1J6Gw9NjttR8jMpxAccZSpbGYpYK1IFz0XG2zvveGsUqqPNUmtqp78pFPlLbl0x95UJ6QFtNrbUl2E+qVHT0UpKGzbMkFPS7Ou+7S/b2dVr45LmnwZc3Yzqhteax3JGy2jUUILiWkKPSIVlvobHfbFHqrVJnJrNEq9SC4pivCY9S5zyQo7LIHVBlaUFG/K04/lIxy85RS6VNTPkw0UijxHokhMwxxMhyJ76I2S+VzK2024E7QbGSkKAWsK5RuzqdPhNuU6GkLlxHYyVq27iuhtUJuoBI0G7jvw1yq5PxzJqUSOItRprKSZU2K04VMyIgSlW2lMJUoKjFOZ9BbCCpxhppZ5LMVB2Khg82bakwEuT4AuvNFYdkMqdbb6WVLbolutZNnGdSlOXELlXykhyYVMhPifGTPS43LqlQQ4HWXCy8US9ilSTIW4+0gPkJA2qNqMT4fJec7CqudL6UMuiOqew3nD9NMg5SwmSg523EuNeebaQ66mOp0Go0GJLqFIbkqcVKgzIXn2XnB51SUVKHtozym9JDrewSolm7aH2jaLyvqcOTBpNNbedpypaFsrqUqS2tlotodCXjHYQ8t1cjIEuLRHQ2txvaAKgMrbYqkNwTKU+8CG9ts1NqjPrAWUtSgrZqytuZXAy9sXdllMynoRKpBfKlSaZUYyHYjrjY2e3jKcUY5X5wL5zFe2UtJbWdqgN4FSnGS4y+838pcoJrWSGzHbUM6YgQllh91CdI8CFkZCsxdLSXFyW+UVIgNnm1P5KzYUVoErVsmKaW2m17lF6yU5r9NdwskqUcVRWzUUo5NS21rKeilSqnSciVby2pzZPKCVWzhCsujZsmqUbaxYcmQK7RHmugiM+JBeegZlFTX4K8TlaN0OR3ISVJVtHQqlV9pvZc+j+fbAUkNTGFrjzGkBQuWm5DSwyu5ztZFd/8AORhTMhlDzS96FpzJ9x0B6jvwVQpDkTf5taS+jsyqW6Fgb9LrHUE8ejMhEcCrbAn2Bu3uVhJmT9B6SI7Sh7nXNB/wq9mEp5ntLeut1ZWrtJCgPh5NcXtc6b9d2468e3r13/eePF+3fjcN9/bjx48W8m7xvxu8Xv8AXrjd4343YLoaQHCnKV5Rny/Nzb7dl8bvA3ewcBw16zjQDq9nV+aOA3Ddux6I8fZ2Y8ePZuvrvGPHj9vHH7z1W06vZjd4/Z2Y3eBuxa3Zf1t9/S9IG9jcG4IB3gY3ePHx1x48ftxuxu8eDju7T1W+3d7d+N2N3jx9nUMbvH24vlF91+Nuq/V2dp6zj954fZ2brabjjx49nf1nG7x+22l95G/G7xw93AbhpbcMBS2m1KT6KikFSd3oq3jcN3VjcPHgX67C+7G7r79e3fjx2fsGPtub+/xx6zjQeB5N3Zpp2cMePHjsGBpu3ePGmnHG7GoxuHjxfv1349Abwq5F+kNytfW+lv7cXt8T4B6jvHC2PHj9+u/Hjx4tuxu99zuN951343fE9VvbodevS+4Y3fEnwOzdu6hjdi9uN9dfrx37/JqOFvZjd46u7s3X13+Tdxv7d1/doesXG44Cy02VpBCVFAKkg7wk2uAbagb8aAdnZ3Y3dns6u7CVLZbWUHMkqQDlVvzJvuPaNcbu3Fre/Xs+rQ9YvfecWcQF2N+n0teB1vu4dWNBj0d+/t36HrGp03XJO/FwLW3fs7tBpj0R/Dd9nuHUMafz7cP/ANSqaiVxpzk3PcsM0hYkU5Z4KTMShtbaVH/rEVpCLi7qtSG3o7rb7TqQttxtYcbcSr0VJcQVIUlXBQNjjxf9h7Pyq7N9uAPYNw9mAqjVFXM0nO7TJeaTTXBfp/g+Yc3U5fpvR1xySE7R0lLaSxT5ZTRK+rKj5OlLAamOWP8Am6QuwdKvSEVzLMy282tN14337Ru07bez2WwfypHYbjv68XRodyTwBzZhu14Ep9UK13hJDVG5XvP1GkdFuPVVEyajTkaJbTJISVzIyN39NLSn8XtVBDDjM6BJZmRJKEusSYq0OsvNKFwttaSpKkndvuCCD+VDsGpRJEKYyTtY0phxmQ33tu7M2+aRmzDXdlKv3/WOB8nDutp26btePXxwEXdnUCS5mqFMLgujMenOh59G5aQen0kol2bafuQy9GjVejzG5kCWgLZdR6vBbTyfTZkNLuh5p0JU2sZLXBwft/dp+Uxg1yntP6HYS0J2c2KreFRpSemjUAlk523bdNtWW6XqgwVVjk8klXyg0jK/DSogJ+UWU5w0jhzpsGP0bKIUsAfX/D1R80E57dJWik+UPDaSaHKUBV6alQ843okyo6VdBMtlPom6duEtx3FJRldjxqrTZLcuDNaS9Gfb3LQbg5uKHWyC0+yoJcYfbcaWlKkkflOttaUrQtJQtCgFJWlQsUqB0KSLgg6EaYkco+RcdWzQVPTuT7KScjQ6brtIbGZRWOm4uCiyMmZUZsujYv68Dbx7CP3iy1Y8eL9uE0eqSCOTVVeAdzappcxdkInNi3RZV0W5aE6BoIeCCI5BzpOZKrFJG4jrTvunqUCQrhcWJ/Kbwfhx1sfYMSuWHJiL59G0kVymMJOV5HpvVOK0m9nEEqdmMo1eTneQC8FlfsuOOmqTcjTNnSsKA0FvacfVwseGS253fszuGvZj7jaq/nn0tjNSnXCCqVTWuiYmYlJL1ORsykdL8GzJTZDCAn3fVv37ifqP3nx931YU2k87kJ3tsnoI/wC0e1Qm+trAnQ3websxmG/Vulbyh/bvkV/wDGq4znYqObe5vIr+9hLdRi7L5z8de0CT1rYI2jY7S4rj1YTIiPIeaVuWg5h8NQesEY8W/J3x9XE3t7L4+6aixtnQKm8Ey47AARTKg5wbTqhEaaBmjCwbae/BQlKFMJHA927sKfoKTZQv0gSpJ3eSn1qnObKXTpDUlk+ostKzLZd4bB1nape0upu6BqrFOr1PVdmcwFqQT04746L8ZzqcjuhTahoDbMNFeX6+7C6XT3MuQZJj6d+Y72myOz0jj49uv2/HHj49fk7Orh7t2EvMLOyzJ27Hquti90gcFdLo2thuXGUFIWBfrQv1m1DgpHH8nplJqEdEiFNYXHeZUBqhWvm/mOoUA4y4khTTyG3UkFAIqFBmXVzVzNFfIsJcF7zkWSnh5xs2cG9L6XUHVJ8nxt1gcMTeR8p3KxUQqo0u5FhMZSeds67i/GCXgkG2aJoLuOZz3+P2+3ySZKPx1g2z/wBo8Q0k2OhyZ89jcdHdglSiVEkqJJJKr3JJ43J8D75dOWfMywpxvqS+2CtVu11vN7Wxg/k77/jp9WEcpIjN6jyeKlvqQOk7SXVXkpVbeIzmSTmIOzZ52oEZjj+GnWk/SSb67inKRx8lOq0JWWXT5jMtg7gVtqvlPAhzRpSVApLa13GKfV4is0apQo01gnfs5LKXk5upQC8qxwWFDh5KdH4OuuuW7Wktj4B5Wm7jvAIHcPt++pzgNss2Nc/RLyMw9o0PZcbicewfk89FkNpeYkNuMPNL1S406goW2RxC0kpUNxSVXxWqCv0YMtSYyjvchOgPwnD2qiuNZupeYcPJr4O8fUVf2bbibv0V1V5HJ+WWki9zzCdnkxT15USBMjDqSwPJTJFtG3nkKPY6lv8A+Hj2ffU5oC+ebGB/M2yCv3Iur2Y931D7b/k9uv4tf2C+KHyoaTYTGXaRNXa3n42Z+Co20JeS6+m+/KwkXygAHvPs7PZ5JFKUqzdcpchpCRbWTCPPEH+ywmZ7V7rnGvgnUj44kx2x+EJG2jX3F9oFSUdzgzNnqCr7wCFJ6juOigLCwI6+vtv972307ez24VUVDzMQKDZOl33ErbIHXkRmHH0wd4SR9ff1fk/VlpSVPUhcars2F8ojOhuUvtyQH5a9QRpe2YAjq0GnVoNPffXyclp4VkDNcpyXVDT8HkSmosgX+apl9YV9G+PHf8BbyO1OnNFaVZlyo7f4xBvcvNg+knVRcbGosnZBPSx48A/QPSTx3+Xwfhv9vDjhLMZBy3G1f/o2kHjnGmfQ5R33w3EjjooT0jrdaj6Szc7yR7OH5NZ3nm2UD0luKSlP/GpQA9owbzUvH/2dK3gbfTbQW/72PNRJjvfsEJ+Lub+7joUnTgTL+sCPp71Y0p8cfnPrv/4E46VMaP5soj/+Jf2YrNLfprrfyjS58C7b6XbGXEeZBILbegUodf7fHE7u8cfI06glKmnEuJI60ap/vWPsxCmI9CZFjyk/mvtJcT/dI8ljhTrP4FIUSS4yOgtRtq4z6BOnpI2ZN+kVaWIYVHlI4KC9ir+0hZsPYpXs42Mdlr6S5LeX+5nI/wCE4SuoySq29mJ0fZzhVlZT6wSlF+N9LBiIyhltO5LYsO8n1ldZN/yb2kh5x9fBbqitQ68pOqeF7W4dWL8es6n3/e/X3b/rAxLbtbZyXkWHDI4ofVbyfHS19OOulhx49WOT7FQihTaKNTGw/GPqohtJBLbpGY2Av0x9FKuAMOS25pqj0Hk33ZmV2cHHUpseBx4Hk3eT9uv1/lFbrv8AUcVP+sJv606PqAHk9314ol9T8mQ/dzduw9mEuNLW04k3StpRQsH85Nj7N2G49SRztJ02yMqX7XAFx0UL9IdROpzdebcDb4i/2jX4C2v5Pf/EACwQAQEAAgIBAwMEAwEBAAMAAAERITEAQVFhcYEQkfChscHRIEBg4fEwkLD/2gAIAQEAAT8h/wCjyu2PV4TMMXhS2OUjDUvMTMyNr2/jh2S80sXRIigUzPWFGFAgF5ETDURGsx5nuuzjd2jBHZLfRzE1jmf8F59/05BPXl9Od+3LzPXB9eefoob5fd9jm5h+3LngjTx/ycOPzhUYEaloBh5c4zNlZIATZxZAveH+On552Pj/AOZ8no44otursWeYoSJonC6yJBCCybQC+tnDCraCa0mydeO/qk1vKaygxe+8Z4OIE4MjMF5qCESylsDTJhCMYE4aEKBdVjOJxaX4y2tqWxZVggKXKu6enV8/rTgixYmJjycD164rz0FDK6fR8w0w4xzIsrmezU3cK4JPLUPl0Bk9xxvXzxaG4s6IkLWKj2rUZupkAiteCc+/S3U5+hqGhYeOaqWY41/SyJcRraGJrtlU/QPo/wDKOcfn365nNKNjOAmTLQFDCF8QkBy+GyZGR0c/eRPD/wC8+51hmPGOns08DEixQF8E2Xqd8VsjkCJ5iEPVFCmSxnjyxWwQM0ov6pnGfM5hg21NdZOyrOfCaxrSjipkztY7ueecudrWRHaMKWY5SrcEFhDqtKWuXKxtYN3gSna9CY49oW4ENOweLDcOZ+NHjDlmq+y2w5sDi3rfou1dp8gw5YLoQ2GCK2oS1ojHYwzbb+hz0GMUwmkdZjCXAqJpGIVACBRRIuXkBUBO0CXzQMdaKxy8hTxA4F5YLghzWGRIT4iPm3+NnL/hfpHnme+P023PHPbl5GrnlfofPz9G9cycEfzX/wCC5l/9+lzynLOR6/bg3nz9FOX15n63l9H7PL6f4vvOZ/O+HB/28E690/m/idvMyQukEuJtwMaFwWR2KyPSmPNC3Li6566fJh+5n++Fs1fq25EnJqIhzH8n/FgFcBgsrm/QISh3W3Q0m3wDHfEUedgcim52J0+uBY9Voo8VBkEi9cQYp0h6VNAIg5pkRDlkh6JAu/Zzr9jwXXrkd5+g0xmGQlauG7b2c3cgD9Eo9TbvXpxBWsJNttBeCA+sHTmeJlrS9QiZHXIYzYqU4NI771q/XGWRHNLVb0EDDgV5tHYa+iLLRrjAoGoWKtx0oq5QXO2HJ0PUteVbiSW6tkhwZK9UpI93PCxwApJsVwIMlwdW+9vB0PI8Uc9H0+kj4dM1zWe964KqO7FwQXCDG6QUph3s4pW3R7oH6vGFRTvo2WWVpXzElq13IiB0wudKoUbLcGAxMSAzUUMOBGl+WQmvZaHFOO/yjE0XFulcB3LhBDfX3Gd5wGGjYLptO55xYvJvNjWQACEnPOmI2k0zA11GMiumJ3zs+bzZDKerr7qhWNSZznBY7IESdHnSF0BocOMHE5Ohrw8cASabNO1sKqmPoEARtolxD0Bm8aDdv+nUh3TPNIbp0iiD0w8IvEYJ3U9Cl7tdIFdg4rE7IHqNWzThyUyS9qXFtwNfzE5A2QtBsoad8SZQ45yMg8CLg7vg+jB+L5+NQsR/8WR00Gcee579ZObTCEqeTiKpo6RzZigB+ZdqG3pdAInbIIpzKsFJSaIGVq/DWHyVzCBjzvjZi6ckpjcbeOQ8zQFDguOtJcf/AE/9nx8J7/xnF4TGRLeGfOqZV4zVgK2fQIdhCpFLbj1SvZTCaVvOXXHrlLQWD+lUjt1OcYJVEOQC0twoDwDBNQSk4TnIZu3WlhpHQ7kMv+4a4OLrrX69XcppeDQQNauA8CQDktxarsR5MPEwyZZOSYt8nAvwadZznrn56fJp+fo8sXu434bPK0A7JdElnBSmh5ZuNDfijZELBGyNyYiT2GPKYd4LifyHkd9S8tTejYyrwywRbwBdK9ZW7enjQaxqp3089GYNHi1JMZDU86iYm2uMaXB+zTEjvToS8IWJsGELkEg7AACQMJQOAGVwKYyYw5V4q+HdgrpXwPFAC4hS4HKMkLCcN+afJ+5U1FyzPYgZtsWqgSzgwQ4wBAQISBAExP8AAHsQ/H59wdhxgtiMAKWTFxhpQCE4TSR0KAgrIbnlCLOIFhsDnwYwMpxASSE8hsipVlhjj8L3QdIqZQAuiG3gAnTMro0asYu3F0TwEh1ROwiuVK40Y5k3Ji5809jwUIEAT9h+mP2DgDL1rfo/ucpsCUD4Rn94uudVRpoxm9nn7FopxIk1a2DcmDkrgfVXUoKNSoX1bCPDIuAWwe4tgVG1g08TaIMG0EQTtzLYOAdGj23FxcpZxrjU4tKusFHgGOrlUzvC2ZQRGESaGd5XDkWHc4D5HwKU50foC3cVLAyM8wtZEhDktUjFeQPU7FLZcBLidZPp6kcwUZ3iO16IPXLvA9TgUTFnWdKEgZ4SGTRQrH1sC+ggYMvCdKNkvAW81JkICgMAjAYCcyHMuFYLWGqhZLrV4TGxC7NBKlSDwyU4CbSQagq7Coca49i9g/rn4/8Ahz2D3D+uA8DWyPDlcQ6ZSCY+HC+TfxMzJienIMkaz9aOLg1oHDoqUpg4I9nCkTuDSjelDMMCBGS2imZGidKGDtkrw4OPYf3cww6fZKZZXK35muS1l2XIHhW6gbzvocTCGyp9R5dRNNs9IhEfFAjIdGL68MEjLK68SqqiJgmQQASLOw22hsLWXVPlX3Cjep4vFKzgJgE2EwRxaOWuk2cjBk0XC7PfPMwt8XFYa4c0MHN9ZieGAKZcgAB/urbL+b9953FO3ioaCcJikx1kqrNXilEiLF20rRhF5GsLdnIkdRwe/wCfql3+inSde/8AO+BM5nlAKgI5oyEGaDI0qnowo4cgRO9fhFlBNtSEYYyRrwuHAdPlt8ioXD6kfOHPNUoa8RnnnvIZDKMWzIZEhPBpc3bLbhxCcY/ntKxMP0/X7z2Ofn34IjWFDHlE1kCSgILybV/A1py8Of2/nV525B8KWQvAad1Gl7ZUdiiwGiGP2h+gfE/x8/qP5eRYbALuGT1wZ6XCiNdoM+zy2NQFdEvt6pgG+yQTEA0/Savw+/1Lauj4/nmn0U/nPHM5JiYrx5nVx3uceiBBb3Itlf0HDbNGmOHV9eU0HoQ2H8LWW+ba0ohRzVjh7P6i1D9C7e82brlhD0OsdHj09ueHU/k/94kTZHSjloVQrhS7/YQfjBoxXlolAlrTMc0+wAICz2U0CJBWopMOBV9dft9IdSRm76g4fJQo4dInGyL/AMzB08VaUiB+6hK1wGo/JYPGX3Q9C3SEkfp3wAtp+hzx3s4cs0BWx467H5f3Tjs3kOIfb8Pj8vBhpHUPRqZshSSsFazDTBD0A+4+FACvweM17BGuTAhgsuChyIrzqrnpyLqjVDwEGsczkFW7z5PUT1WSKm1GnEAIBQiCw42e79n6Ry4E6AE6asrCIgbZOcti3lI5TMzqvMSDbF0wup+5mjaYYA4E1u1O5B5JIu0WAhBeCEZAMcq6yPhu+gZOno4a+Z02vih9vnGXugKCgUau7QxKZeieHA+2iUSrfDSMIrAs9hl+NC3/ALwsIDu6/rlgLPG6TVPRdiXgy0VnHXYtNEZkOdJ1BlblmIiMSMGH0jDR68sTwiW3kGIJpqgNKpAcVrhyWVsaY9h3dOuDKTqJkbvSHUMb5SsjbWoqJGjUyO1IXaGK2hahOThbNALbItX0kuEhGUFBr2L+BgI4kdHNsKMzhlwcWE0XxtCViikFzVVXbQt6NwejDoE4UkR5UmBdYgzXCXJJpBXylktewKCeiyw7h0rvSVrjhf8AHJ7krPefsHFjlyhRqCE29BYeP5dZXMNi03xbApZYmsAFQvgUOasoDxn2rz12x/bfCfHm89bj7D/sj4zThRLgL4dc16GL/M5mn/wwc/KeOTbqji4qdv1GwM4zoTS4qQiz8qWEBWF8F0Ys+mx5ioRGEu3I/RQycPAjG07gmAAMSivLPIR96R4OdbRe4tggQ0nshlVvl5PXqjiZKfpnM5CEjX8mfIE7x8IZFqlimzf4b8md6Pm3k9rHAYZOcQYuJ3Y0OQmctiY2jmVEA+mvpSjLrDPSmxkTm72ydZP+xljvpwBXHPBQxlTCaoyPIWHPCVmILPa+hje5+fbkfl5+d8eLP4JwPxfQZP3jOrQX58yrhwqfagATYECYJcqBZhrP0fF7hg5tK+mUdeIqbc8JwB486iJpwGjk6buc4h4P0ds+2fRiBRAwB+yKAFqlIMkhSExNfJp7sOK+n4AoMZish8Q5aUJteZKnUl5OszIiPS24poarVyUlpBtD6BuzAxiRQJdTgWohQA8tMTFAbohIATNAjOY6gKIEQ5GeUpK9Fh1iKMYJyE7UjNT8vPP+4npyXZyfnzW/ffEql7Wv4+nkauEokA4BWr6HVEHWiaVrwnIODchLySwNfyOC2TvDOxP5yeKkA+MMCPgdHyJj9Erk1RneZ48mF38Di18m660uPjb1rlSEU7mBKRM7UcY5iPBcIBfISjtbw47naKniBC5iRz8Bl1yqc6GUcwJ4V1oJVjTSx4UThKdS+2s6pFF9/hssA/eU28pdb95EcwRhEGteqmSIEPXs95Oc/wCMD7v5SD5SftnmUSuG6OK1mHjIcD9KTKXyrsgMqy5mD0PA+q9GDr+jV/LqZ3jPnnlrHoVWMBigFHw3JSGXyXKLpSvBiDToaMLhjspECB0NlKYsl+2/IA5ZhEn+4NgghHhiT8qvZK1ZKhLw2A7B1Mo4bOxc1sTQ4FKZ0KrBz1tOx2ePZS4AKwahfgjXgCYpFux1Di1W3kBWu6EucoLiAOKY5/TEAeep2c8Ne/hEDHMBzx50dw4aMUKcdZyjKt1xlWa9Srn+IE2aNbnho41ryvN4L9lDPjBzJ8PBwMIetZcSkwu8IvQEUAQc2PyP7OeTqqe4fMKJpME1FtjmqiXwEqFmqwksCkSOhGwvD8+Oh46mWgq85H59j6fn/H6gblkVhB+g+5Gn6LxIR1L0MUA8E5A45suY0xZgLto9mW04s/mpchq3mzclSmvsBrH8AdAnhWDybhy5XHBgeNNJmBqKwNEiPZPoyx8xbHqzdlbsw0hBJcLWglEqeMXH3gSZsWVDfIZSbLOqtJziVoFqUUZTEhh0Rjza54GQ/NmB3bYjQeNx4EM0ynKhnMBwikxQ1MjJkACLgejzy0LRwdDtzGSZOF65ROp64uwgA27rphyo7amgn+4pN965J59P/Ax9uDff88h9XCr69bo67Sgaw04V6sYk5lV8YkGVxlwA9AAHwF8tVSoo0wwCYQYx2YS2jIKjYYEmEZeoMujcVQJbtSGzVyHuPDARzMuJSFLqg742ACyHuKaAgyZQgfTpUO9FE2GdPC1UjGlIgzUsbTkTuznpLZIxeKRT8vZNGZQOLW04rVXtXQT0DA+BZhVFL6iI5Ijw4rQ3BYqVzd4Zydua8EnGrsKjIlcFzzdqv1zI8nwfQTCpckddX3KjSJUFWVTGJlGIbysTJ65zkaOjmyJdL4jh4Bp4/m8ScCxC1bokuJnCvC6cynFSIEUKHFj9ENZ992dzJAtdUWDajhoj16tg4mbEtxL1bhYSO2rlQ+P3WXtQ7CxaBNBBULyRm+JXToMKDyyJnBljrt6EWLB4wgsSo6epFBaooWHlTl4CClr1u+WxazeqgWIbHBJVEJarelpixTMQPZiNOFk2LleVBbIamIEymSpZeIZHGkgQsQFRzN6b+2LQ01DNeZGpWXAgchzJWxxPbA1UVf1Pbv7LZ9fv/wBbn9uFOs4TboPLDSzfxyCQ4JLQIu5MTDTQPg4GxDIrUsqV9Ixvvx+ofQ5fxnm3seVkPa+zxCl23FgahO+F69F4mXOuSoFYxhzVicUFIoZIIKLZ3CHiMuSlUKgNoVLi8HDzdZhSFsGYY0CXZgzoUyIAoxK4GZ3OrFSgp6KEHfxoPPw/tez2aex4fMLNJz4VYpBxN5g9C1BtcAYI8YdU6lKC6AhzBAFCgCqydBXy3JEMXytIlg3msjIeWD61UCl3FUbAWNMQrvY2Ym8rgi3Y3cFEYzbhpeMqLUMy3qXDVQCConC1+JUw4pcxkLw7fj/aaSer7G5+w74oDLI1MiRaVoLU4f5A2QxVSnYDeSSJmYjYecE9Pt4UDsVkFjuw1TpsMN+j5Vzg3BXWrS8ynsc+2z1NfLwqRWUY4aWgAIIM+mHlcpoN6qA5AR5InIhCOEAzMCBpz5LChg9bW3E2Vej2nsGj0DoMHPy9/ffHSNGY2WGDNkqCRRIADKUNQj4ROgMhPGLiPJI2W3MMQ+gXGUUB55ZmuRnxzELKwzk3IEBzbpK/VKC9oCSl07oBWPIxbKIihWF5RowhDPiNkHFsWbzBwlCIKYIvuBeg6cQq+ueDCWwbhqhfnsOSqymgTDomSFeSbmzN3zGM79B58MFM4RGFOXqo7wxPMihguSR9EnkgYkWM4lxsOygbQFYmRlFR4uhD67ZgMOkWHAEv1bWgR4w1tV49lxXETtww8EFxCJCu15QQi5o3On8SFipEd5xyYcn4dEIro8wDJDw5nCCrEDY/ljGXyh0V2Mpa5JzcOdDL7ircMJ5MultvJmEKc+sRqpnCoSmJJ8Defua5mLPBhpPSWAIYihea5S9gGOxfBlwTaOuZvse64BKlN2fOj2NzsS5uU1cWgVpBom3VsOLpOWhuBEnT1cTUBSZ6yxjsmtlc8JPsRh9TYVcOV37HgV6gqJ4DQgtKhBQkhnmaJSssqas0eyZ+mVaUZZvmY3nEeDt1HGhEoM2IQngl0s2Pw1CAsnd5LbGu6mdKrsbhoj7ZC5CjJWnBEMXT2lGNVOWTFjb4+XvMqz5nfB6CSJgaKATIDeW8WLUoDQlKWqc1PzyiN7U8YWB7hDInXv4EDI8KKNF2i4lYGgRcAWgMUSqUppyUgkScLw/5OB4AqPUeDaqYSTYEp7WVXcp2mhTwRDk97cq2gzOXkrvCyN4kCbAUAYrPA8FL7DPkbfLnrARqgbLryKqt9q78AtX6oswJ9jDlc0c3eMI9cyvGLn5/2a0MtPbG/jiAQtRSCfQr7B3OZ03plVhzJSBaXnl85Zj/AOfH0FNKfP5/51z3z6uX7ueJZzC1MNvZQ3GA2TOA1eBqzHfMvUtrkhxHJPlK9iC6e8y61nE09jpQLwSpq5xH1Z/USm2V8GYCIfZ4fsvuK3m6d2Qe06zzdAyuM6P5M76jiurkGG1GAV2EYKXNpOIZL6KjriUBIwMvD7+TQTxEuv64iEIDUGZ/LFEtolLIbE03OkNGBw/YVcARPXA5N9N8AwCqAOQDuwAWiVNM6asDYBIX/ANv3TsZiYYU0MImOek9pjzrWXfnvhqHp3rLPis8cQd/nUfJ6ONeDmX7j4D9gPjm7GXty4xtzpe+3gBr6aMGGnpzFI/PXfJdcjXXu9ePHxwBgygL2zV88jxzYyKRTCnuZx1464qRCeEv78+alwzNXHXDUPsHwGD45msLq9h4Hr0Iemebcb9X+8TqazNvNIeXztLfqP8A4HCgIDS+Yj8KQ1UEHSHvtVkqcwC0UKvEPWtZfv7+HYKG3hYgKsG2rX1qvuvHYPD8iJ+octs/LbPIgjvBnjuFv/qezkNMiJw1CQAmgCAGgDo56Q+L17eOR657FH7jeeiqRfwf14AWdz2xjWj4339Ebev6iN8kXDjvYctcbEtbH1tPSa6nAVCVX5VV+7+xoOId/nf759+GEQanXRrWAHgIQ4u2L5MOpmS4+3XI33529u3PbPHXFkc0bhTXmOT0cUGUOBIEwG249bb5bXt5+2sUsbmOfneRwvJti/n4eDBgOR+L7cgfnu/y+3XFNn/x2ezDGuVEm9uTp+wd9cW67Haanh0xTWQEWr7GfT2v3lfLlzynXd2/pnB6GPTgBr1/VX91x11jiH+6j4wmT44EhIQAAsASCsGMyc2sy5VVfu3B0aMwK81/srt7/ufxOBMl+kZoJlNhvIc7Xtb4+A0HUgqAaf44DIwzPQPihTTI0ZwrA8uAJA4GQMYONasxDpSf4dAM43gYKDo9oaCROBo+/roAEo3Kyrk0Y3+rf34H3Lc+cn88bA7Bea9OKiZkrxbmCol2D6iOs4IAvr8a9TPPHp/Sfy8CX5ZmKtcC2CnDskTjMjADqniHEsVvd9FQpQNnMPXPhAWdWWHlGykMqr6qqPlRRzUEX/k/HzxqP98fuccogG3PvVVL/AT/ACcjQCqsADsRD4C5JysESau/Y8Grw1nyerfvb74nd4nAkgbAA9kL02tYgl4mj2UpRGkU2LSk+bzA+w3MDGOgwKmoTOB6xsyM4dO+Q5iIp72zoGW89OMJtgwYdksCbtV1M7YwBTyeSC1dZcAChzu6ITAZZgbHW8EiiTCagA7WrQB1OGMLKs3MD22ydMczChxiCkLNy9IpHucEvkmTaxVwEYQ21zx6Q/ZXQhR0ZhgTNe/0ZnwgGenzs4BhBWCwz4GVRUmBU1ynqrkAg3hfw7gloAUIlWFyIsEpNuQwZo/JaJnev9RQFDpAOWBKqe41yvXLoq4Jsk0dznI5lAgOYqsC+xY1aN88gedj9K4NAUnR6tsHv3ngjNmZ69U7XJmfRzL957exAvRMGL0ggxT4cGSUcUyXQdglWhnyGGNJWNKlKkhnt1uDdax6vWPPEEq42RVEeHC2E2czggYiwQBFAK0Gx21OJV1syXEwSGHW/onM8sOe8hZ6hG+DT3eF9+TMGYaoCoFivjmEbYGD+jbAWH3FAd6E4E7LjpWd75hZ5cgOQUPbCfjGCLLRyDAqIDKQz6ObhhEsQgJFjDWt0YiPPMg3J77MVBWWLjxJLYvod1nM4a41t6xtZ703xhB69az+nLOnxvtvXF6GsGSr71Par1OC7HG3E9gxh5QPXfHGRtlCmvQd12OB1rSlc9wwJRwTwbCVPLMZaEiYrs7aZE9orryPeZQ+c8SK7TbE1BC2/H7cp9Gdmny6Eh24TTzAGzUVWMuljqHG8tydAmsIQJaM6Ka5gdgxSlMOcjhvYkXgVwiADGHlY5EBjJeAHas6HvQejBeeZOVYEo3u2kl8uAuZQiHQdhgAOrDBJLOVLj3ZvatEBJo6jwy8oTTa4WZOtw4L9F9L+42+5xZOv1+Pd64Zh9sk/Zb9ryAr3dmeBdCszInEXypnoDbWgxRjDqFRh6ovXdEtx7PF0bUEEbKIm2EHJmcB2abn3BCXcynI7H6J2rhToi8S93xifsvx/s9q4T9TzgP15gDFCMGY+XO3t/yeKnl8MCjHUFO5m833wQWNj9YF8QgHlCZPYkvmDOqebVn6ik04pe0wnYIYZ5DHEptqRgIU6o7vnnBjxbgYVFTdrTxA9EF2QIAgyJTguOoqTZOH2M5gD/AidnGTDcuZK5vvAx9gIq55gbPq8LzJY5Vlvy6irBsEDlERPDlJbzitKdo8ILwTwVso8HkjII9t6PQK05QkDK31ttW/DpzxiLefFHxDbMy5IaSwy3OmshKnE1w2NrO8aLx1rkXp7RE0RHmw8CLwLUCUrTTEYDW/CbFBxH1QuCNnrHAEkA1bjw5HYKZNDLbovqga19hwYvWB5w14iR4iBQJXqVsOuydnQyJAytQQYW0Wg1e6nAyJHuNPVMQuBV8+4zCJSOEUObbWyRseHqCHjgSR3jTtBhBZu58c8SWPxrlwkGl/L2fH883zpJxMpor4LoThs0rdO15qdDrdESY8Q0pToAIYShUB65Txc4RQDn8KWkawEUovHBMFnDrsUW2FDmwsGNiWryJjkheMok2kc2G0SgKgEJQJzuMidcapLPH+v2cIbEJ3PPjaOxuJ7yWQR2EWrQjOVcTQYb4t/RXJZ2YPltgBurmTMTxKAkjB0DlQshpGXkdRltuc3NyIwkLM7KYOcQ5rkpmLq4zTqDaacgiBOGplkbSd5EWO1F5xKi3vl1A8mEIQHH34laV47qPRmuQtEkSqSMAXHDlxapiONVL8ogffmVDDIEnhiHMjHI+hmlnfiCufUTcpuyt/vwh6xDxmwcr0kcJHcFSpwKqWxr25aq8yHOQwomRhInqhOnjxzcFFuzKZYZEZOFONEzXghDkA1jO0OdCnjgThSgEqGGnhmC8Izr0LsGoilAZK63yTqxGJdfJBZUEvwnm1pcL1FonXbCcAKMD/AGBYcIPfv1Yfen45AsX0544MviYQEP1Dv8sxj32f8Wd8TXuz555NJJJAJy6mBNcLVEe3XUijx0Q9/JVf8H5v5LFZZekEMOPfww0xw96eq+09gDwMTPFTtVVF8DXsYJ53y3ut7PQqhl2dfPLcmRvtXhFzLshJKScUwVFxxJwNFUo4Zz8YAAkBwWV0u7onrwbcuuUhBJ0RLXeQm0hLswQUWPNEwcF5RgDYAkR4WttZovDtwJzmS5AMQ+csupA4YOuOgwmhNTEc2qvaxU3a44hwYOlqMA0IKGGqOG1lsK1Dgyysi1goqXHsRa8xqmE5hqziMl3lbiFoMZlhMKgnzaZAA93LkhH0i+ne88FRSnA7ck3kjCZGnANZ6dvX27y6Wc1vfvuIw1jw5KgORE2fPRGDRlWyFRxVaQDsIzwg4g2OdkMtJBiQz0wXYI8VdC4sL5OA/c2YsoKPB4QUFaIuFflBYn/5EMdYyOPLGXalExhUlwx8stCdKRW1TiINQHa3hjK+iZIA7JSDe8PkacdiMkJcgcPN64M92ndrHFNhFHuDyPgqeiYjmqhILiLBAQM2Cjt56aQwabk0YlzsNURymMR0NV1J2bU4gDzbME7OKeYYpAVpTqDzc/GKcz8mNO6CIyVlbFrkjRNfuKEIRxGjHuss1374qmhimlT+ZLA4nIF3tqBMSK8laoTsx+NEUGFoIQr4dIvYvVLka/AzwiVI4YmeTT8Kajokniui4BKHBzQ4xYezMJUacMv6lqrWw40R3UNJ4M9Ex7f+YHIvi3qxjfpcw8CJLAgpJKp+A1Ea5gogSqT5ymVFomuVjK20srQjXAy4ZSD6KS83vjHJe9jyypOOV5i8kHF+I3KoYmILKJ4oGDSWXEWJnZgehjnGgH0KYiIkD4udI3Abq1Qp3t54bUI3Jvxh0s3AHFlygC8BgIhJekPbeCZ9oftrihuGXicAVjpxEExOebnNG/E7HKLxO9qIheUr2Zx2PUFVHZAuUG3+xNB3yI9H1f333xLrMKDEWJJJxxmXcIKmCxCUN8D7exGOlyc+duj6OPZTDs6nXvfTi5HZ5/o487+mvUzLRpS/J0VKL6rA8esh4NksKWUigsu04myCDS53fTU4aI51EWHJ0w7nEIsztZv1Ccqw4TrHIFCEVwOPjkbySloYMEtHILtvoZvaAG1YXiBQGIdjUaISlOGbizlD3hapFfLZ1euXxKULi+gvW5UWCTzMkLLQ07L2SpV9DDMFctyCTQLjJ7GccxQXzlhHLPhMvDAiu5LzMcwn4kwkRcNikuTpwiVwrRhYQC/3fkEx4bhvK/8A9qoVS4wR5CS6vXR1YqrQTm8ocgDisoAcc1L0ycHDMAWIl2ni1TN94kGgLPIDbN4hiUuXJtwEPDAAJjvggeYAXN5nrlEROsn3E8s4PJgJRraFSxegNWlmui8exlzAZnu7qKiMLocQWx8UPThzav0VC0NyOMIeesoaYM3yIZVIMXertApPJsD+IfLnrTJO3C9qU1gX4/oeMpmdZyllVzGB+YpMLYDiQmzPQYfyF1vFtPUmt+UkhOoft8NDb5DO1UlPRjWIgBKt6cEH6lKYROFtY8lQKlAW0u4PBZg3x7lTvCCcGm9zym9ZfQuKSSYUSOQ+DJnHQ9nmc5dkRC3JgEs5FBC63G2C2h6otzhBgERinCc0qvEPuPB7lye1jLi2dl2y+KcBkPh6bhroAgoByxVJDVLKqEmVMqJyhIUty6KvJhq8Ct18tXU6kH1ZlQNzN2WYc8KsRPsCV6FZo4Bbgy10iNoTB8jvPDMzcXR5OEnX6UdYF95lxcU0U4vO0gvzCX6cfw8X71nGV4ymwwqgXeFkVUI8fhr37g8yBqXqwH0cBcbEXjrUgCe9WSAplzeRIEx3YPEdYNgfhpUeiRtrszhe2IYwJVU13MaFo+4KiNeIpQeMwajPCbWEcrgCYioNEGtmLwUKqGxdMRsTWhfCkoT17LQIJAnBEPJFAACR23M3/aXYLv41wuUQp/XIG4ZenHKnikgWSY/aKmK8miy0c9W17nq8Ys5NE8n3i/aZ19MqRVVEq9ANT6aVcj8mubg5FS7ORLEE27KXkeP3+/v5dvfIz6+rxxEv/iP7h76ccBIGJNt1LbcMe1nBxbLiWnbb89+XO+VIS730qHsVh0QYAFNmmnx+ZO++fqB29MZtlzNenLXHP80++Tyh4IEQH87wu5ch055lsXP6qtNNVW7VXPCKNKC3om/0OBa0kMwIgNEPBUGwiigCYQqhdlPDYBhzPcmrMxlB6wMMenHCTA2VmpEuTutBEE6J2O01PDpmTWWVaBZc95DyLcPI2u1X0n5iesez0wcDyZYO9aDwHgx6Z4oItNjGFFwESFKIAmgG3FC+xh8/yZ56XjtuJ3b0X0ZvLE09FOxmEYzJpKJFOYZp4zDK4NG2usahzTjFIYMpCWIiyEsiBFiIfevmb8VnjRjlAGgyCs2urkrY4otyC1GnClQlc5SZa4PBxRSRo9TyZK5SpNlsRgiGAqgFx0jYxQc1m/V+29HWhcNn3EqqrCVVrgSwASBzHJlGVmPdrHuREYINZV6QqhdDVFi8BsK7hB1cazPuBEX9WVzCIpoZgiQkkOW6/VJNCODqIemWhNBr+Ab+vfqKESyz7PzvN289L8z/AHPbGsc9pjYqAgqGAs8OTMeS61g3gWw8HoYgGgMiadyqlw5WrKVzTohC0C7+95pBxM3Wf7ea+rxh8xTZXBp1wPMg1QFCMpSiGXUcg5ndjc5C3I2hcEGDAcy2Zt2zBDFmNz+Rz0/Da3CJW1kN9eROYJlrZHBqKmWXgGo1jAoqSxKCRYyYgP7bFw1blKoABITxHr67t82tu++BLkXIlWB2ZgMGPThRCVfUwK+sAvgPHFtlnnXvNXw7Ot8ewXbX2Z89+e7wLJGu3RfLyXzGKvN2N7MzM611jx1t4tKWQxqK9ylOxKgxdIUjsqKoBAo8hxToTLD6MYx1ji+EJNu9K8+hxxaVXW7SBz5xVc5eB4JzopW2sc1ltoA4ALFBNPYTBWrapsFFAAOBjA5Du7PhORIE1PzPzzAkT1MNvI3XWgUvFwKERiGMPCyaMKY4ttDGOrFBoSATIBqGkB7fnlX3V2q/7W+TkPB8Yvv5+eRaE+QB+EyfHI9fu/3/APw3d/8AWaH/AOvRfX9P+sPZgLtiGcZh+zgBvWaKhQsv9euVm68JA95MGqD3X/qtEMBAMA7LLnoXvmO3ttOwmB4HIMalhpfJ4A0qBOAg9QclbSFzlNKwR5RHj/qTCuw9ACA9QWPVeQODmQJFGccg4TOQyreAhUxrTQgHIrDq+mi4LglNTh706fTw+3/Tgt9Uw69cAaC8rDJNdfuS+YVPE53c6TfTvkDRAJg39H0sMMVxgENIIPLgClg8eDbdc7NcISRI1pclYxgEdzARJO/K/wDTPjGMRwAG0IWLgDoCeEzYciKuDWTDsmWGoNi1EVqYC/SpfUj+fp7KaXjPERlsIEBQDc8yDj7shDA23A0f9K7BZ+zs9RxRwwpg4gNc4I7XY9QoTHBEpCENKI41nhx7ABRZzTOuhSDnKYvoflxkikTSTQ62Kg5bgwQRRS9TyroEMCKCqNgh5bgVj/ioTivnHscK88X1fscG8v8AzrssxmTIx1yC6RFXRwsgQ+gyaDNt4IF9oiCYHoByoQFJ9IZnWSqpcJQxy89uaK8CVuOMANiz8ATmop0mexy2Avf+Bga7p0EVXWxl51D8Sjo9ETptDl6dP0EQh6Q9bi7q6vJsK2y0THl5l58EeEJ7kcp2LjWCd7HAfUTrvjXL6e7eTv3vx/ziFHv8/JrkQm8du9ToWyY5y5YC4kWjHNx1yTPqVXdWaC5iWZ/HHw3wSIMqiqqJHkUDfKb4dXCJ5q2Y4s4rGQ7rryPmb64y2qhMICT0jkBw80iLchs92ZpMrl28+XO2tfdv0WzM3z8nT7mn551NjN5+/wDx5QnOjcvYTadjIPIWzg4O82UPccP+cIO78KJ7JEfUzzFFXZkqY8L10+Nb08EYcIBmfRbL69iG9z9nFeqYkL5U3AmSRwKxVnYDqQgUxm/pAI2ug/8AnMpURiwPSq5IZwcbp0UaWNob6gYAfzle/v8Ak6/w/jJ7mn39eLcQ5T11WQcxDOQpbqevhh5388t/PP8Azh7Aw+EVPFSM6U0vBWxEDGlE1SxUTVy31WEIjIaGdm8f2Prj137mnvmRv+EGXFaERrxHDOjGUCnbA10CuXA4tsGUKsy9gySOcv4vP4v+TDMTPV+17w6QJKvaPu5f1v7SQ4fwf86auZReuw+HDPDlCE9p6sNqaD3vpsOuj5hemLDPZBwzm606k3RPnc5OGfSCzw2T7nHCT18Fo9Fd94w7p46BPbL/AD/kjLNS6O0iSZEQbeVHNxHp1Y9z58cNf86IzsKdgoimRTIzF4ezct6cr3SeWZbUhKfDHyVtq3fPz7/z67BfLys8kBC16kwsLVxqMAuy6V+JDfVNAAZnZVAHuA2gzlPgxBS8BCPkMaAP8IuDYR5Z/wDtsm+LXnT4d+Ws7cOEz116AF+Hnu8P+d/P44D62UyncYUtcz/FBZSrclVbPUPbgnExKeEPLoFe/QvHS77X1XD5B68Ai94PR8/pPuaXkq+F8lcPNYQDVLhMBq+uW5ZG6TxfX560Ze85h2Me8cYJN3LDYRQR5JlxyXmakEZI5Tc8FeQ4E/5hUC6Cff4Fr1p4VDLBOihYZxrvlQY6T6LKy+9JrlTP1G/AHwt5JmjD+Hr2Z473s12YLrrIef8A0+7qWUgYhMDIfLrXG8xLUsXI2hmu12fTedLAax1QFexzekTbfy9XkOMAU7uX77/XlkjO3MmzyRWxeZaLrV+TXqbDdOE4zErvOcFjdricEPsOafMmswGcQ/PSgfdL3l4cvAP0P5/5pLsBWHu2kyalaSC0VbyE8qv68/X3zPa6+OQ8H258p7Kftysl5jFCZ0jIspnb3cTSjhFiZBRYYIB5ZWufoMRixEISrkXxuwm3DUgXYoSDgnUVK8PEUKrmsDTMhFsDynO/WR+uX1x7cQ9/biW0TW+e5np7LuPXxy2EU8JD7lWdXXW+AIdeOdvx/wBBPsD7Jp+pnfIwYFJ8L7fHXarz3BKogmhpx28QE7FV0IOIDBo628j6ARXhIearseN4jMwHnuIRSUsnDGMBlM9Bj0YDlvc5AUF638/1yI75vk/5v//aAAwDAQACAAMAAAAQz665w7005wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwx8Tm4Gx5jsuCsJAHCGPFMBGBCFDLCNFCBHEEJMDCBCCAAAAAAAAAAAA6AAGtiPE6QjSIAIi6PxiVK44HvjRTMiOopVf6wycbpAAAAAAAAAAAACQdAFFuAE7P/rAAJUozb0nOTkxCboTV9maIk7SK2xAIAAAAAAAAAAAAA5hdspaHrgijAM/StMhmwpiImTllLBD3oOlJ9FMrlBAAAAAAAAAAACL5fPtW7HmuGAAnymDU9linr6ZRFL4BbN1e+xBFpMorAAAAAAAAAAAJmwQVSJ+px1aDGDPAEBKLFJONBLAGMNBHJFFMNGHCAIAAAAAAAAAAAYiAAjqrln0Kcwf0eLuM0FWclBWeGHgk8LzJapWDnkZCAAAAAAAAAAAX0CIcC3eaOcJBAoq3V/rnWWf+9MbqfpI/A3cYsdHKuhAAAAAAAAAAAAd6EAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAhAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAjAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAARuIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAVkFAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAhPyGBAFFFKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAUribuEcyEjCAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgqZatJAAFKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHNICIBA4CAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGPK0B/JFrCLAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADzCoDjCaHDAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/8QAKREBAQEAAgICAQMFAAMBAAAAAREhADFBUWFxgVCR8BAwobHBYNHhgP/aAAgBAwEBPxCYeb4O/wCfw4uwJle504ezpzkAECxlTryazB1zvPtegINd9Ny/Peeolekz7snJsp93P5/3m6CdAcBX0qX5+R5sGsQ6t/LPv8cRMRH0/OnV7N/SQCKDWdrRI/gv+uWcJmKiRGrBQPRwo0TYFCnytMiqo04NQ5QVSAyYIJsIcKPQwo4iBRJnFXtMSQ1j7zbDzeOmAsGkaFR87wM5O6OgrhQixESwzeW7AFpHkWKeyOXYDhrAr31RlX/Q/BQtkBQGihM30lRCjaDhRaAIxQfXCI1MKC1ZpZUzsLya82LJOZMHwf0uwFWIL9nrN8XhYxT40+PDOxp74qXrO2EP8+PPAgElyw+Ghv1eKjA1GAxnhe+z2ScAAxXHEnv1cT2I+eGAoskKMxva9H/2XUYLWFzzBg89dR88ByzO/AOkhlmPh5ExIwUoyl7FODer0DruD5bLGzTmK+GgDOu99N/jBEoiezigL33jDrtnz4vKIo0ECdpnR7ix60eKoIJ3todS9Vv08BG+mBUs7sDv3+3JWOexPr2XzPvPOQLv3ATp2in7X65oAKR9nMErizQ6fE5FbhIx7BpQw0fp4mCGvSp92T/XzwEyj81PaWn7f75QSqJ2Lb6Qb+Ofzyf7jwF0JAhpO18WNDc5AQ4RCFg2tyxs4CJR/k3yf3XeExDQ3zaKERscU45tvSAe6NQWZTTeOAB1ugOjy37PxF0IoSuBSxuCuTdhtuG/kPjD7z9+TbTolAw0pmBiIULwiAMdCFDHU6XiYiIiXNFWKNa0AnCmIZABU3VAsCYnH0RQmijUkrVdACcaiNtJgEEIYAApvEd4hVxjMCOyStQePQC1pENDsPTx6/pQIj80SV6YBJ5vWbRM9ZiAAHCASSpgiPAcBEdlUjri7mIWNy5dmxdvbJTIgkVuz2UBro8aHI6KqHjoTx25u3pIqakEqzBxK9y+QI3zC8pqrHyI8XcGIhBlIFLEZ1x4VMXKGFwK6LKA8evdMFAXwTxVNEIbzTDNrBkiYEF/feH2UiWLNPnX/reMvzXWwg4CFNF8BuGgJoErQSlClE4flSLqAYkpotJwybdVkYwRhZVJ129xQgWkAJZATwSRl4JimPECMgRaNG90GMJOgKbAq903Pbxag0rs66DmsiMOGXJY0pUBgwBGb65cUiHmIYiwetDTgoBRiJmEqHyHMcNlioEjuJNk7dd8OTi7Gw132UzpvF6wm2/zGdrKGCuiwGe1UoZQiMMTP7wo3ImymFCQj3QJOCjYGLooiphBE+ImpZqbjyEQTTO/P3zRWnR8zDofHvr88SHowIoxdp4J4zxAHOuwdJLRhWR74UHNlCogguqHyA651say+FFxqgDWh3WDxwqSqWErqahXi5vTL3qjvVhTTeiPKiFC1tghAGvkkLKIqJJqFp7Wr8/0X5xfaX/P4r61shqCcUkEK4D2BV1htJJgAAqAQFBeGy3EfWfakMHdOWIGX9/X2dDOpUXyXE/KCRj3CnwnW8eyZIGQ5tMpPTjnH1M/vURCEyE4OTEdgVSG87VU0cQTkgg0sFCTXsU8WGiLAkKKIVSF9CXk1d220B8hlin45FPfIqTeVhbEQjB4WawE99Hj5w88Qu5ANSmQkagnb44YiseQvzDxhZW8fXEG5ulqDvR1PrhzgLxWBCRrSq9NUkNcMrtINLjVy27yGYRYZslpA+FNqWcBToe7X9mqqOoeFyLTWuum4Jh3ddjp6tTgCxezU3q8uOkcAqzQIAkq8VJYQgwFiEUWg5DV7MqIghHyPcE18cu1KGCYUBHBGk8EzTOkYWLiVUbhz18g/uD/AIs+/wC8YRgookRdOvCNzlUEi4ii1QzoKwKvh0PQR7Eff35njljL40Yd88pMkXAfzRk5BYdlE0VzhMitZCXgZDuAEimO7zg5WhsEgphcfKMkc8NlCgVgkYmy3oyFCGxTGpWn1IQL/bACAKq+TBZHbrr8FH5Bj8jr3/Q0FWXBSIbaiCmx43i4E5IDmaHQs6QM52dpIYz1gxOgqYcCoKwdAwIKL0Yay3hJHAHMKqqKPKVuCtYzFBsWwqHhQON8eAESRDIkkhqAS4987YkTQWEVg/SIzAPaPwW3zbyDDloBAHSFiNVd70jAQSgEIDKUII3k7szlpkRKq4RElnEDb1KuMKaQxMeRvO3QhNQSX2DMb7XjtAr4iUCrp0Hm9gmoKdoKPFIyRLlm8BRUDvhiGfHCi/AVNobasMnzxXkXWQOlsUyXJppylCIx6KAku6iLOuCogWeUunYJ23SPL0RS9NHgpUox2upARCJ+bACIyTFg82R+gEooOnUbTwWMgw+WWIVHQQYRnGLqMzy3NESyWacedy0bhKADLVtSPLgQjaUKhNo2+M228EQiQCT3hPtCPyf3XccAkrNEwKKlgns4qINfS4YFAEw8OXEKBVgvTR9UpD41bBLofXXljtw5UkvtgIDfciBpWXiz7fm0hkqyXpPJxkoEIbamwlNqZqTgHrUYMoirVVCI948c9spRrgNlqNEYUhY0SahEJah0KBQ5vYOTFLTpo5Dzz2eM3DZhmZ8f0i3M0D8pja+1/Iii8oFCEaJ+MnXhzOfyHDuEqey5JyD0CtROA91kvyfnjbkddDJle2QVVPHNeiEiKeVlDQLvEKiIbgAUgRZZgXisQi4KsVO1rrpOuE9kojCYJoLd7smcBe47sdK76AghDlm3GAUhqYHyzOfew5QUoKIXqvn44fXwOEgVrGdQ+eZ4E/CQ1EIRq0zgETeICoePGdMD6bvnRBgVBtdXDV3yrVQAqgdb+DffzxCbJDVWnljGC+FKGgAjgz2gRhFwPrkOeBEqEYxM7sPGHJ6n0QJCCYzfRsxIOIZw3Ehrus9eOTrcEHziox6dg40FNAAgzpbNgXTb3eJOAkAYDYYKbmPCIsG5YJV3out6Hj6g+hIwqVGF7R6TgGqAhQIhV8zX+eAFgUUHQjD4pnx/cGSF6pLhHfAP3TgUSFlI6CHR8+WWlXi1AU8D30Q78cpCGSCT2wPer9/upsjR0JlX6R/388UP8E2iYuvjtAJ4OE7T1RjfYoFPA98TWwjyoUB30LhN4CCSssDj86p5Yd8xw2HAqqgIIrOpMwSjVkmaDVBDsw3iJaRuAAGSqkBAuPlWCPQB1IURlCHAzDKHUPKsQ+ZvZRvEkkESgHTy7wsGAUb+UAIwD3wS63IwwEO5sRHHZHGljtLCH7v98GAyggugpSieb6ym8mQveVegxjj1p8lrKPI2aGl+VFPy8kvCaGgKDmB2fFziRiNXrBf2sbNHl2JAFhsxCM6d/MiGYaaMxRonTQKYpvEL+FhuAIlbh/8AZmmI4CyhdFA9JUc4abxB+TKjPXXJ9KQC9QyV1mg56bxAilhCHQFKBq7hxxLrlrhDCJoM7m8mtqCsGygo77DSS8Zx5NAigotFJ5GcRsEafCuKex7HycyFoBikF7gB17zBpyYYV9iUpIG9zC8SAbOBIB46OzjLy4xOy8B9jP5BlpMAJ0ILrXBj+/BogSwMAwfSdz4vCWQpAmDAI302cYmoEinyMcevXec/n18fZ5+f7gVx+EAVe+g14npM7oECph259I/J1/RoHGsNLNR7a60gMOBTBwBrzHoBWUnBU11AtfCEJ3Z4edJD+kfSMFRaJhlXsWe8C3Qve0Xz54hEYBHRgApIpg9oNWA6lBAw7AqL45GKekqghoYlhT54GBvy2NLFSDoU3hYOnphL0qVC0d3iAZIMAxHoRNeMqZb7dPRy5MPcLePW6eqqooBtBDaxZ6PQSbFEl4mGyG52AG3DLpoQKjRzEmESl0C0fFgoSN3GPugYEA5eXNBIIFSTRFb2BgkGYEpgVUoiiJaVSM2nTKF0BgJRIt74SQqETLCTWt4MSJVy7JQFqCQKF4UPmJCCKEt2BDBI6cVcZIt6gVGzeFZZABrU9ETaPa8E/qzMIVZQZEiqODUaJA0SKUl0xbw1cOFm7B0YgCCm8fKxSE6NGMAKxS8O4BQADoWvMQXpkUaFMhvGeQvA2U0h0+cCRdVNLe8FP7HVHG0YBJSpbyUqoUSglGJ0c1HOPwRScWy9G07t8XbRMIGEGEMFYeeAeB6JwMsNGU27y9cWVlAtirLmHCiKhQETEiD0Otl/uBCdg+FP/gcNMK0dy61RAMjsicNGEqjEMdINQkX57w3XsePslBRo0Os4GGzQFo9/nBa766OTm7ak4CtWxTpU3jn3ITraJBARcqeEXppyNpiaE3oU8cSdHPp1T2BTW5vCNBGDrS0Txe19FjP96SC/QiluuXtGsho7+52ADU3j9TsOoAHWLArvOnf8LWUGoWnmXtix/U0riiQJAZwzlsbSGKKqZFS+XU6E1NRYUvJwN4g9Z3YdFkPg6eeYDjacp7DFQVBdZzyis5jaioFipT4OS9IcsjENo5qcac28rVF7TZBh0K6orExTvpgTWiKBdW07qxQ6AqVlAQV46Phyd/wU8LrjYtqUQWxilIJYFVghTqyoVCYkOgjxX8QAYxCgnHYgVq8om6rq28qnpFY+yl9KNDKWyVhce+SYuKoDwgA0fK8iQMwHfRFA6ZoWDxUcCeQYkq2ICzTtiFgxFuVD7ACsVE5riKHBuwqUVN8mt8AMHDIEYvZ2c6AoxJLFxDYal17GRyQsQSWmt60KTlnPWkKBwA8LvVJweWCI0ABlXH165fFSUVQz0QBaCo5yDpt8hX1qPk6+eK1VAMgrgV/ft2/3WZOEdGjRdNPDchJw0IoZKKRDuVX28MgDQQejACw/LcKr/wDuZIz9WG3vFV9qft6/Vjy+0/Xc+Yl+b+rAgD4A/AQ/wf8Alng33jNBKdaPvhZor0y/4/VQdLGTrRUAwEgKK5qxwPaDQlkLZnB4rJFlGDQ6RH5P1TxkuK6EUpHDrvfBNWgFohF0aogi+NOHExQlYGUzRkVWnAS0TvqPkTwqj8n6mo2hYIIACsq4S9cGgAaRiR9Uo4gAAVFTdQhHzdEghvrg1Qyg6XEdVCFSOKb4T7nXhxcfH9JBxdOozqV7poaem8GWAdojxt/6nXEMFIJdfil7zZ+3N8iPpl/wv6bCMUnoTJMev+Hnh/TxS6hUpor5IIcNEkdgNHqAiBAUK1JswV9CHRtATv2UWnRUJIpLBUAleKFUyQqsKfJWam59GHHdkhoFcEYiOvBJQgZWTKS3w7/64+xImmcgNBbglg7zofmOlOvXpXfyfp26Op6Ho+ZA4PeSRsVamKIk7DylTF+hj2SMT4jt4KYqzurKqXGEoBZOXiQFEG8SJgDjGs64T1mQAegddX2q35gYdfz5eNQ7BTEU+dzHz1wqaMkEi0w619eQOfJcD6FB/H6cQ8qANAgOKrmIvHIA7XQi0GYiJCCxWJN2a3o6AUWTB6ZaCK9sJgQNVTp6vAjymICOvssixocZ4Ag06aXzOuvxwQUdidH36E3j3h9Uih1gqpEciKhIUF7d+Xz7/TCVFIqG53kjRsj+YT0cq8vkbPL118N4mNR3qfHhF2aevZxEyJovZgIVAQEeVGsqwACduwJWYnKrb++t1rGU4m7yvZSgIU7NJcEFgXRVSVnTEIMBDY1Pt64g5krKQk3JJBCXp4qlVC3S1/BJDxxdIwWdYT/n6Z1QQWQY/E8dK7+OIowttxNASzW9zDjIYs+cq4vmfGeB422fSprJ2AKirHUI8LNMJDG3e90uZ29GciSewq11k1bVvEpLCkkDJEAI+HaaEDscIH4fm0L/ANBgoEhaHu67t9/vxa39N//EACoRAQEAAgIBAwMEAwEBAQAAAAERITEAQVFhcYGRofCxwdHhEFDxMGCQ/9oACAECAQE/EJrWfqe/KvT9j9Xl9H7fzwwQIEyrQiB7z44SGYFKSsgALE7b4V2J6MvvhTOzPBFlz/PERSa7xP59Nb/1SBRuKmASenWmYdhzEgoB7VgGYzAAou5qdggoXWojsmuS6T+aIWxQ0SSEBBAIIiBZwGhaixsKnzAfOM/1wBQs3X+Pz7uoAhl9LrWcTGPflv6VlaQc1KwVXHOlCkcQNMkgMBjVLLFSSaYgNRYixyXss4qUAW0TFzhMWXj3EAHRxLod69t+npAz/AXs+f8Ak4kZ+nAvYe9/icnr078/mzj9fbjgu/Q3zJInvjihtPj+N/bnrfZ4I5P8Bew98ftxI9PtwL2Huzj9fbnx84/m/bmPJ98fbgXv5M/bk9T7v6HEDMvt/cftwybno0f0Tj7j6mThXY93PEDFz6Z9dnBHJxwXfob4FBoXpY7mT7+3Ej0+3/qpCC7NwJZUskQjk0B4p0FMI7AASG/yCjDBCpDES1OG1tyBgyR2pIIUznt1F97/AN4Lk0b/AD89+IFJRCde/YfHFfRMMgFIgK1hLGamcZaXCSULSAQzmOZLooqWDVMt4JGwnsGmXDSemMOOIcnKBL1uoBM1ZOMUWKy7n+AbY2NxgnZ/WOQ9WhSriDb5fzoDUlFfXBd2YZPBjlKqRmJmev7fHM6S5huAG18v05g8SCD8fn/eAGGRHIjja9Z+OBhi5XMfG/8AmEbxKqokIehevNuzmptJd7H7rxpN4q6PX4N+OWzTaHo4zjqfzXlL4f8AeJSqiuYbnRyedkBeqFrNW5nEQKRsXz7devrhQeKG1fjf01j1+vEXEfz34HBK2xbOvzfycBZFBDzcCjNXHtrzxwmAV38fE+E4QQtUbjx7+fHLoVv6id71+Bxs3eO8kDy+n9zlwwMwOBINPVXXRXi5CE17FMe56XvfEJwQzT6dvx1yGB1+fn3/APXNdTrz63gICDKDSrTbQETlzXHNARUVEqAShTHVxCpojgs1cD2/jjaaBIvk+2f0h5CwJ2LOx8Nenz1Ti6Q+WCFEjc40YfPjgiBBHEEGoYq0KporERVBHNt2j7BGAxzFRaDeu78anqekIY5DF1ZntYmbTGng0v8AhRPq8AOErBW77gkvr15OUyRhKf8ADgq9v2K/pxn4fqv89eMS+BjzT25U5hTy6cb6H7cDismkenxG4znxwqrLB9SgbW6WzM5b1UA/B7a5ZBqCtZMCXJpwZ45eiDXkFfCZno4k5dcSBPqaO1+DjEJjK3tvjmEFHNvUYgtIZ8UN3mJeAfd5vx8l4sKukDRrx88qiQSemB8efzvlVgtyuye3U/R+UOWgAYh3OcVuZ7eBAjCrGTKbZ1n5xxgoFBr110PXE0h3FnU6jik1m9Bw2S0lMeR/o13zPnA6XIGPnpM/HAQIIAyCAbnzmz21xlIyEmshrV+Ne3HH2+5f/YgdjdQQ5hSBkcqYA7i8MosWtAZTAUyHaA8Zx7/bmTRdqV7UlCbr64yTkmwhlvQHrsC37dt3AqDVxKAFQqEaAIJY6GR1IMIjBwDKEMHBYNUWV0xy4DYU7QAfQSON4voLsMMrbiAm+g36+nBZmohT3dX/AAupcui6j+e3EXENecxnjtfHjOeOgLHR7b9fveGUB4JtwHfvPP2WJpgqprLQ3Wpua7PGgXauI692+3HyqZFWNzRkfLUk39QEJVBg2sdJgfPz7MgfYWJi3GPevrugQUBpeikDG9PjxnghIgz6+76BrvxmxTSwGMIxNMH8cMHSqljHQ711g9NPKuS9DRcbfGeEQQICdQy5huMd/YHg6DevB5f8ZmHY69Tz6cs8zAW3R1P+9crDkY0em55fy8M4RvMDrB4jN+vxwUyDHWcJG8Yh2Y9DjCBoRp4/v6447YBRYno+fRZidXhnUjGVNHxV/R0Y4ZxBtCStVfXiLLZUN5npIY+JyS5DVTO4a9gL4Jy3YnUSOMf+izpfQi/qcanSdgqFy9FHI3B48BwSSvVzOahxvB+cUCUASq6EF4d4iF3cPt8+3CCOJ76x1Ti5Vadf3P6244jKKiFWsrYUwRhxYZUqGLSsBUjA1Nh5jGlUKRyAkRwKBRZPe9yeA0vlC5HQm/ozm6NPPnG/8AoGFxN5lt9uvnicsWfYC2OcW8cBRMZv1pfXv55UXsVUxfH5N54SCy5Hztyb9ofbmAEPj0/evrZxXIJV/R6zR6B1wMHQAT42ONX5muKmMp6b+fefGs8oYMry4A9ur1nsDKBDBOv06uVy7zeELYbPxx47vJZkar8+Hu9+s5is1Mal+P8AueKLuzXpidXHt7eHEzrz3+fs9f4RdJDv+J4xfPEiGEWY14wnQ9Z4Izg05frfOj549Zc2L+b73zwstV8zwemfPFAUfDj16Pi64jLK3Xn7X18/PCAGwquXO9fbHD0IDHu7fTPv5xrlXrrDjRNl9eu88wUddvXevdnl8cVWsr4/9BpoZgxFNVMpYoZi04bDvkOsnW3CW5JMBVgpdRQlWWVZcrCGUA8DBoTH1SADhE3PZ9cfl4FYR5DDXrX1/XHK8lZj3mzza73riORB6RF44q06YMcDdCrJMbRKgjdu4ZYBkIAFAACGCQOdOOiNW6miv6l0cVkcAt8QWwsH4r3wrROZg9v+e/EFQ02dn5eRN49xz9uBii1hP7np70mXniT+XW8cT3L0Y/n017cE568uv7+OZEGkyGMg+fj3HkTYmLn+r+b4J6nveZA3ZEyZ14ft+3NoI+ol+35HiDGXHl3Jo9eZJXpjD7f3OR6FzGGvfX8/4i0x5Cn68BWhWN42U1eKAno397mPBKRZNCmT9tfpyPh3HGvfvHffpxJvg0HALK/8f545MKHYKPsz9ZwT6XVpZuYzO+FhDHU/udNvjldC4Fgsvnncj7zHXfyHvyPh8HreCXIDtIfX6fXj9fb/ANAwpYW0hJWr0z2HfEZsEszCdBYDNOjHTRBvua+NPqf4UOyIHm7etnvwBr7FbnLTvxsxnRwGLqQe1LLk3m7OLLM+7INwer9e+SBXD2yrvZv49uKdAglA2BuHc/f3WwgLtbTxTb13nhRExWiOCe+NfPkpYLWiwZbAzWPT7PppO4ALlEzEwF7ZwQF9wCzD1Vyae2cU1AiTO4I4MZWX1zHjbbpZC1viVQz0vfEMtSQLkMuDSXv441JAVucJS7VBlhkc74EgjQpkVgHpred+3BqJkobjHPsEZg88RKWETWBoESXGJRB45mE4nRlTo9Ms2c28SgmUm03l/XmKBRMqQUg/qnTh4jLFAaaIOnyXE6E1wt9NQFGyZms6rt4jIaYXSCdbn/eXZHLTkbm08evZxTEERtw6JDAGalwZwKgqcGnV9u01q5t53QVMGmNCkxQUYDMtrSgWApNWw6fV62V3Z094oJcYpH0s98ddWvWHr+JywAPM8tyjGWJ4jjU4xYNEp4WxfMxqjqw4Ui2NTsXGfffz45NiIpesk8e/v6cgCN2gsg8tzmcQ61xRlohk35+vXAHAk+anSdq5vg3wAhA4EnR77c/+gS7I4mkFg4rVqO1OEWKByEgMeM6iCJAFUBwSmsgiJhxpCkQtAFRJG7uscEu2N/W4z6+nG3oJQ4yB73NzDHXEcGkxvq4x4/nxxkpDDq4nl+mvuvLiaQx3j7cZWEAD6ANz5H68BaSDaIAGsnnGd+eCCiJmwy70uB0Y9OUQyqu1X0nt1jGbxCnBAmMBvyL3uPByxUKLMJaOnHWPSlQm5AOhjxhzx87Ytmwh349uRIxQYuqvWx09z14xOgMbwBJQmP59GzCNDxJbnvOJOOccANYCE9dZcehxoJmAbJAtWd3XLarwE9zHG75x45nZRh5aGfGtDnycVykfn4br43p4OF0TGJnrO52jyAmKtla11s+nL7EXpjJ0H0fvyQ2zJCSj57mvPw8UgNQrCqAjmITHjo4EiCELn3EceTeTxyj8WMaAhnzQ8PvxAOBN+gUPNU1qZ5kqItLDEDDPg+s4ZNh49/f8eZVi0VrDZ8evFACwHbxeyem7C8bnMCO5ik115+nHgkQGAGAPOfE93xxCjcpAZW5MYt7fvjDlxukuXo6Gc3ONcjS3o7KCpZsZGyepxiIQevj/ANR5V+pAh2cEgN64vdFV0UMrMoVDHKT4bQMRZxVB6Ix/+Drj7fcv+2tznQZ3jD+n+2ZWa/8ArO/Pr1/P2/22WM9V5lZnXuaM8YQmHTI+3eB7DT/tUJUWIVvXpzHEZ98+5H0vNErkCqYLceb5x7/7RPJenb5bd5/OlmGPhdfS4/bc4Eho2hBw7f4/XiiyYMXECdTXr03V/wA0CoBhysXQwZerLvWeJDsdNCmoXu4svXCTJ12oLnFBC1GOCOUJqnkwnw0/13jAiRbWXFDv1/SAA7O/TH08XM65UzLbrz89GPU4pK2LfnP78HpUJACBius/YCsxX+9RkgdpXAiSJxktkJ5FWIO1QQNXh/R92YUlllAkRU87YkdlAsDFQrHihpQY7KXP+uRRUXEz3prja46e7wljkhrOIJ3+++AE6Ar0dag3N7OF7SBgeaXoksDJeNqZnmjSsq39x5V0useDB0dH98Ek4AcSeiLmlYxwuQG2T9iVDDDq6C5pIB1gsvx/roIYaX5uPP29+ZANG2dMO/w5ZY0W3IyTG8m8X5o9O0VBIZSgMsVxOyprHogECQypSXEEREHHt3+nxwoUfAGBVBSgFY1BF4EzImDOUBkTi+RDwQESAeyX7J/rNVl2AWChhudIlvLFhF3QUdyI0iI5GMgBjJNTSLtzjzuYQMi/G8ipOkjQ8D7PJLI2JMjRE+eGmgi0uB5KWQXx1kmmj26bGNeYWqKG9EhoC6Siiy5zyhbSMBhOa7ImeHHl2FAAUBKsKtWlAGYAJ4MHzN79/wDWNvBZMQKAG2DqEBA0hYFxKimrbRnJjipAjYI7OyY93pyQLQ0pogcIBGFjgTQWjZnAZusybyxzBylOBISSNEcJDqg3LSbtjCUI8hpIjFSgUCK4dUtBYquD0SEJBTKMz6PKgIEAhoAh9v8AW//EACsQAQEAAgIBAwIHAQEBAQEAAAERACExUUFhcZEQgSBgobHB0fBA8eGQsP/aAAgBAQABPxCvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8uV7flyvb8v95GsTSaubsseDnxTYopPFhdI3LNwWMNMwAOR01W6axUH6XQpuKDuxgqAMMCzTZIm6qyAOASJa3II0HhAWwErANHQMvZ9NuV7fl+rCStvE9JzgvX3A/r+cgdjU0Nk6sl/X4qgxfJu/wAa65m/GCqB1FE7H0/36ZCgPd7/AF96+32Vp5Bzxr043iKcraQ1DuecFo6k+59ItBbLDiXn3xJwe4M94LvxBykATd7bA2ni+OcDRvzySTe3j+sUBVQNO+tF4f8AyflPZZtb5N94a0R/DC5RARhLiRwDk7PClVEccNCEFaDSv7HnxhQCiqRZwoHCC0FaUoIYfBNETasBAaTJT4sAsW2iEAQrKEcLqgaSLfMrdUgDdn0A0oCUDKgWLkgaWKLkAQtxdeRIcbmvMGbCScklJmKKXs5wwtq1UfCawf1SV2CcDDLlv0CIJIw5EMHQhotwGIWZmWDYRUowr5Mj6L94oiUXakjhNXQgABMWEimhOQag4PIFGjqY00wKKgPgPPHP398KkTTWBnNksaLSDyKBWRFph2Nwb9NannS0smTn4GIGrT3fL0mwaV8geQdhW1B2N/KZjZFvDHW6CKPCJ1wuCvWXlCa0g2qV7ANlQDLRtwIdeAeeQAHsG1F27HmE0Fc3uNBSiVylCwNmgowxRC8R8ghaQiMA05or83cpHTosbAADYFBB2CIohpO8qa79/wB8SELAkYNE8SNcshArg7VzaeqbkUQIXK7uElCBLUSQuVgogKXChKdchRrGch+7t9XCbPsrLoFqgBDAi1RFu8sckV3ZQlFCYFrwrRfTJDyLZarcoEIGwBQGsFTaIHGAvH7iEWqDToVi8lOg4aQmx2/Vdggwy/4GsAjpXHsKDSSK1hlTUVCG0ehacNhZq8IbNkFtwCGGczhJdlK1ysHESDQqgr+D+cRFu8KvpPH/ANf4+ojY8T+f2n64ldKTnVv9efGCS3XeJsTXgnH889/pMP6Hvv8AjwYkTel/TV/nj/zaCCbLTxf6xBCKvDmfqf8AvgcahRfL39txPPv6YwnTftx/nEOF4eCXid+367MCeKeXRP8Ae2Wl89EUxLaCSab9p+vP0ClBzb568OUlYnxPfW/974Mx2S//AFw3+u8XlGh4J/7g3ffjr/36Punx/I59195/AYXy37e/WN4NevM4/fE8ETnVa66n39curz7bwTF1uE/yYkxZ6+vWJA7R4hf2z0vkwKl12TBa0Piao+wHPTfTN3ac6142x61gmVvpP5/33wH3cDL+mBtW9fr6f3gjw5v7e383+MY8L7C/tj1+6H8OCTSt4aa7qH4N63765zw9TwN9+p9sXR0B4ef2Sexz7RLtjf0+PL08fpgNjZyST9D+f+pBanAn2ec8SlLpwulEAZqiMeAGW9oTbYptSAPBAZJgtDxBAUoqYcG1ef8Af7ju5wgUOER7jAHyCEFGGKD0eeAOLCpjiEIIFJWxrLcjYaVhf9zi+ASogoDxNiL4BxubsJk1LPeqS4fotkvApoFsDcQ4s4d8zvIRAPFeAKeEQna0QaihIWsFgutvdoK52traLAxiRkdyFZ0ynO4l83LfNIR9tFVcCBoGrSQVIvs8ie4BYQj3UH8i8GP6vtgNHJoK7hBg5WwsrmiTXt3UK1AN2v4A4z5VcWjOUZOZmhBJcKMacZUxD/8A23rSZdRfBQ10IY1kCIwcu1oGG07TM8HDVWRLBY7IpG5/vQNOMb+QNg3CZkBrK+BzuBQ9HmIbnIjpsFMu2uOmEo40bIqaepqB4G8+BOSCCBOqOQE8JR1h/pYPhPsArgzDTErN0fCKG7aybckSNS6ZYd8QqT7YpQrLomBxMOS6WPHcTCKQ/qM+KoPNC5NgogA30IAM4EsLxpqtRRuoVmQs8ekgLVbeAlIOXQG8kIvVxPjnPKRUVNwBQDZPKGBxW6HU0UTi08UenlsDg3k2fSkCjjBQQlJEcLZ9Cx0y+gVes1paE+LlqMZQ8yVZk3EWpZbO6WJclvd4Q3JKuyNeUwAGsQlmLmkELdwNHwl3eWcJCGZAo1TVAhcJrVoOgu0fD2wGg3i6ZhkZx2+ee/vn+o7/AK/r3pLid/Dk/wB/ZjfV3hk3xjkDjQHltQ7qswiIzbViIVteWJdnj5vGpyVdKZQy3uuF6NyNPgwPz5wRAgKDF23iESlLAKgvIREyIQgSTex1kLTWmNE1DodRgmO0EQSN4d082cksYU0xvCvt+ws8nGWbbQKCnkaYQcRUn2JAWuDxxBbG0Lxt2zXh8ioSnBIx5d/pi0T1IfbT2Wk4DMkDYKKq/mCbTX2k91O7RKGxxRtsUBDaTZk5NM1P+y5ptGq0FI6RYaIEAyYQILVQAAZBwxoopS2QZiI0LKca8FAByOol8SBVTwFGinOq1fU0eIZWStwWHhd/C/xhCBSGNnQAIKXicVTDVByGcgmR0HBRYRLsyA2eqRBgEpdBkhoHldqbh02gFGID4fZV+1DuC/q3HkQStZEABgyKBzVzKABy+PPQmyDe9yQoDG1XFLwyWPkGyxQkwAd6DaPOMINBBtyRmxciBElVYv5S8KDBwCWOWQ9c6sp14M08CKgFXWzhQuwcHM47l/p5TsEfMRDPw2QN0miAGKAkfVDoKtPI6k9mDwcgAhBQhmEJqwgLpisIre4VZgRWZ6Cbb612hPm4OsihAAkIQAqekKGg0EA5zl4RueJEFnUNBQCU0qqSRCnNAyYK7EGdJoQ6AAQjxP1caHvMM86AAEEZ6BB9gexgKCqGEAiLBBgJbOOFFoKXGDGgKTSygCYjRQVikVigt6pV6AuZIm2jFjCci1mCZVHBOzcq8cGwoAsWuKeVym1DZPB8DfhiONQqiTBbAIpOypmkWiTJILLXGCJZJhJ9zewqRopsjxKqsCQN1w6PMnP3yCqt1CG7EwvL0wgt9TtqT5AyK7zIcT5mAUBIKYpY13fJBO2cASGEWFDK1lFCAoiMRCKG9gLXHB3XM4roClyRbB5UgI528kYhk2F1khmViawhhYtFIBAgKaMNRgNBxRpRqYFVB1l5gEAWQkALZSzETWSwKqhBQ25cFWy+y96ufHs5OrCokt917QcefcxBiHxkERMwINLMbzCySIULQojorojdaPh0d6MJYCGhMoU3BnGUoY5+fmhhS8gSLXxMq+GKOSwCALbEyYC1NpHyUH7JycP2MRHYpWQKI4JFKEEAAhTLQaKkcjwMrXKKQkSCee0mnWqpg2IMxoSJdxXt8Tz1/UIKQAm8LAXK0BQEdIRxUUwg3AimqYAeBYEY3bEfifkXR3JQWI0Kzja001Y6FL1WEUfoAkcgv4pYp7KKPAt60CJBCJP+1sTQqVdLSCwAgBACANq781DLcKXBRLW0jZIigRe8EIzXUCXLLEEyIUmyv0/jBIVqpQ2jaHhTpggE3TByR2l064Ni8JFghxwthILn1eiBSoWBVH0V2VKe0kKiLFTQrnYgL95gEZUGFFkq3SIwZhRIowgghg21QsFlNb4vKTFSo6nLUJeQBYful5teJbQeek5eBE12YFa6nrPKBSWhTAFbKrlEQZGGWCGxclNkRIXhYLgMRKADLDvKK2CQEKpCAHRAEmg4D8Nb9Uw05ToBy/JQ8hdMgA3iGuRtKjuX91CmyjwI6toqj6SKvn7/AO/18b5+lhx/b9j6Tf8Af7YZ3IPCbQBKUjRCCKytbLGhhbaUSPIHcLetMMgLjTlqFDxEStjSwAiH6AcQa3uWPDiw1vRrjXmuAiAoAABoAQBsAALo3mzTLK+RmPrB0479ASRDyOLFgvsAA/bwGGFS4juOLqWaaMopHELGtdkkCJraFwQVbEYDfRtC8B7+yeMbt41/b+D98goqYU4CHa+4yoRwL8Sgc0ZvvgwvbpAQZHfYaIsGIIQqivJ0DQCn+1/reWonIBIg82UUYbFHSjC/O+2Ie0xtnjVnL6mEJjByQZRfKX18t+n9e2WDmkw7zmxe/pB6XX2x6SPRCIgF3p1CrZL5RKJg6iJ9uGAMnWjCqOrE51kU9UxZjYWM5Ar5QY1CPuVPjQAljmgNoGHprz5N2fI/asb648D6uIfWCEzPAb1vrc0hgJE1E0kyXdRXCsocIhFhmhO6I5MmuHyXEiKeiWAgUghI2whgRTjSqUhgTgbcDi6zQnOCV0zLRUECfHJpRYPMpQgJialFM+GmK2n+NkD/ANu4MWCIiIxOHix6xPvKeYV2Epoaus3Bu7NShYlMUAws2N7mABCgCAB+kgynGPclk1tkoOzJaWPjNKCCGQExbsiL0JsmAoO6ctch8TcqrAdeAcmroSCcmKGqmApNSkQhlBwHPB2yADQKJytoKSlqSqFb8XiPR7GJEq2muwXydy+NoBCJbTIa/wAodyjaYoHklkaK5wgo1rlNGIhVQ+c0JJRGHSLnk4X6YQ9wEIkwbOkAAkVRCtSC1AisJ3eAOJ/v4/AN/Sn2X9wyBYIKo1GRF9ddi3eIHb5iM19OKwOQvYdXgwRAIYYCWg9z6NOiZb+EvzyBfDgT6+fG/PjIBGm8asNTey3DbknnRIASVSrvSAQaAiLRoYiEmsdnN8izqKzsJVcoGg8+B4njHokshgxxOTT538Z/if5zhNoMGLuQNHcISAAjCYwMebVmz8ugWFAgOE8AQMPERmXiSt9tSvLLNSapWB0WvF11UI+fzfAI7pHaIFCHWVRNg9CMhAVMOAkCREWNkhA27y39SQC4wBEDdqoVKPlLFAUihmxSz4NytA5AuXjTc+80PGmsQlyQWkHxgwLs1HVX7Jb81fv/AE40JqCpGExwHaEmFnQ0jMQ0Fcs0HBBaeFCNGrDmhSPboQ6QZUNFsB/1P9azYb5l+4wGs552uPdwBPyacju84lLwRo4UfCHvN4Ty6Jt0BnnoP35yHG99r/eCxOMbFgoqoq5VxLXJMkySlwBpD26MmJYRglLPjeQu+BFrLmGIJomAkDUFBMrCPINzg2PqVFIa2RiQAHAB7GaPmSlTi4SDNAgBaBNAI4hY7QSkDEVlMAIwgKEYYw90BsCpMIjTiSE0SVrIFqeDGZUhPETVxAGOHpFkl2W0DBqjEaoRCCj3Go9XIeMoC88AFIRCwU4zmq8YG6aCsWmC3kiXfkdPCgiawWjqT7n/AGUR2l8yXnyc/eZKGCRG1u+LZ9uddYpsvLgbSwMFPCTXWLWuUEwArVYBee/oZFORC0WgxOF0csDreFMNAgtc8QzamwQaY520GE+ooxlwuTHYAH0NpOSsQsrAFiDpSAKMiiipR4AG7jWjGxg0tNL2h5M61yLKGxAt/qNLfpj4cCBR7zmuYst7R8ZjLekasdKDcXWlxfimX0cLgdrHIGC6jXHrBSG/K9CvhGX8KWwLovH3JqCZjIiGlVFRHAStESI0hFK/ARPuLvDsccbARLUAR3I3zZ6h98AF5wpzhIFPgzC+QoWkRijxzAQBkj6NX1gd8kQYVEPKFNnZgNB8BDolPNCGSV3avKcRvGhzxAquV7PfHrUiV12qcw7A2lLNocGgyRZWZcFPa8Td3dtCGb2SSwEXU0lEB6e6DyThZNMLsbIQYQWRYJRs4HJq+vik4KL1XTZnJpBiW+TaNjBnGUBcsvl3hWGdtFhBPy1vc3KioMXSQAAtKoaY9Q/3WqFGIgXsgdcuAfQsnSKDBGCEDXKolhtouOAY0eKiDhg8iQBCAC95gBHhkhrsjzNT4wR7/K5fPnJSOw+Kn2YlfMrYT1OuzfIKXDG1SABughA7E5cyC8ryvl2g0p3ub5Rq/RX4fH1MnL6VkFcCoAVLnbBdQTRawkS5TmDMadP0xAztIFyrJqOjdOY2kmtudYSgMC4jL6etCxd4arCIMt2pFBPCcZ4OojBTElAC3tI1rd/6v/P+Mcw4IPBeT2o1eAM+RIODx8yFCVGwbLwze5Zbj3NUkWJxNjFKttOwHwnsat1dAEv5xCUvQMsqNkA2k8P4Y40ZqXC4IZPBAutagoRrU2Mykc9TYhmQtcFMbxtiF5KQkZV8AbvUaHZRDHiFXvn7f9h2dPKKOprlvGvuNM1Nu8FV9VUBeeQXxnmsEoNCmgVXmm0Ocj2/p/WNjOfGKKIAIjXnLIUFFhhglVxBsGtl8PPUAoHVUE4sCqNvjOBOilIjhAuw2dhN94NA8q0x7vDayFgCk5VcRPASEiqbQUQTFEQsqXB7JrAiGrFjmibC/CHk1554HEu7D8A61IiXe0BDcDAOotLRr5liEKlrbeQWs2zYySF0AKmgI6herVxAEQMCaFHgXgcBwuA4XIlxd97sULHS4t+SuEVO/gwFRHwLaZ0Wm+ftc+3r5u/OGNG5KmVHkRrRvFmvf2oyuZb4yieSm0ggl1CEU0uVkCvkl98pGFTtBaLS8bLrmb1gJD+5zTx1NeOFj7BtIGsZjychTI6EKL0Fx8WW7L16ZK4+AHG7bDkTo2yVWcEjOxP10UmuckXCwnQ0lB5B5iu8jQ82ADyJbyqFXHugt194RZ4C1V6KMCN9BiRivM8hpoCgpsDXR802XB+sRpAuInJ6UXSFbMGzDXJacm2+SMMm+CQwVgcnNiZyrHxyMF9YQUUxPdiITQeJEkISGiUM8MI5SYqEwRX220hCJgJSA4UhNviXQd8aHw6qVwYvaf2q/sYLS3aQxu0YpAnvNZFPPNwMu7LzMQlCUQtPMQT6LWQnaXQe6d/VxKiqJwHIdB3/AF9KK8Ge9L/v/fkH9kXLBEj5WFtyRvDvz2DMAui0CiYqRotxQI2+hbJlLMADiKZFMiImFIwivmMQ/KS3XNDAAMOEyzx03NVxB+1FUS/iZaRpKC8k/qYweDhAIYECjq16N5fAef1uNIs85pTck22j04EDCENatojfBB8lL7DZ13gV4RnktdOuNHHK9lwDVyF6bJGKSKDkiR7FjTDdMrPsXjayHoi+QPGMwktI3qBAaoFwuHIpoMWdR+LspWxLeQMjKVycjUwbdFS6XmopfEBoXjhOKfr/ANS+uWg4lfYU8VNIaQ4003gkAAyCiAVZFNygBAFpEeslLJTctI+U4oXTdVwSEin6EiwCTKdA5CFRIgBtIlMkEZXx/OAOoy7wqLcAG3IQYonuZxcwCPqZ2eXn/HenUJ+lICGGdijew8WAQAKioRPKAZSBA6AqDEG01oAAPAQAqYijAVzpNIiDQToQeoIPm4MxQwWQPW8OgZiAFSBQsAuyCk0rlF07UR+y7KYQx+pANlaKnjKJxCrRWp3PXQA8WCBqn4FhfIBhIMKIzOjjpQFsJTraUEoxhdtniq4E6mjbgolBEgB8Hwk0QsaBXYBGalnyTvD+owZe1FDwS01EhUQQcCG8MBBYjYU0CvBSOjheRDoWXu+uBDSED2Bx5YqMjj7Vw2jglEUCiQfMarztx7IHyofEcbxhqBg5VaxJYz4ZliYrFsMwraA22O20XHKZZupycBdIj6IxKU914QBKauKKmY+mEIeGQsxuVPXWu1N9/SIZEUFIwIL2ARGAMcCDWqtJ2sdcWwhlIvDTZhZw7nTb6RAdg0EgJxBXBSLQeCLZsETkAM5prGbkohPXa8ZemGMrs0acJACdPB3v66gCUAtVCWSSbrWrsEQcOKTlJBGbdSEwFr6g7i/N+5apHcpiNncMgMoi+5SAdgur2oeSIBWpeU22ik8rtUfpQrKyvfWC1c2A4NoAY3QhvR3IsXQbkBlu4FArGE6kOAXU1W+WsGCSN6rCKtZ8AMOPYlDeW0AArFyDRytFdiFWBTKKVw1gWzQacM7DUOq6EynaTZF1LBZMmVmj2Zs+IlxvVt2i4B+LYNwMjRo6LNsz0hJUQwQbpHrQEs7A6mCpkkVdk59bhSwJgGjT9a7gDmyhuTZpi6FHtf0M2KLRDxZOgcY+q8mKgUllPQ4F1O6nrJqmOSheNQ2PYbXkTQvIakAOCWqiMn4lWsRaCqlkYbQhOY5OCECt/wCjgqAGCqkI0aDSI3ZgVoOhHG2qEGCAxdYuFURZdYwTtkBDSxE7Hkk8t6XzYYAVm3l5WcVds9XEKh6IH3BinhRaaK4qt2PO296L7rrXGXbaFX+P9+jgAjD6EiDVvEFLGgjnFdhMXjTj2KoQQCgRRFnoUlzwsVNmpTiBCNSIp4cNoUeQGxkRjwrCopVQwUzTRMu0SC1rwJB1qX1Zzt83D9nHlWKCooo3ESLjERkKhryuLqsYbKAQI+NBi2JXYBaKA4LMBAUBF9JqXwcXbyQsyDqTYIoQEEULmiKoUZ0CVCGak6pcBuT41yVv+tGctI0h4FAJCJl/8A4FYnCYUlpa2pLlXsu9ACqkGiioBUahGLJQqrRLZYKTgMAACbCy+UERQCDDuyRJUFGxTUUVArEqe0A8Gc5KgtFQjcmAIIBwtqwFa9u15dyrDguoZyIgiREER0iNE9EwJQTQCKiETgBYcFYbwGAkmgHlfcVVbXyuESGlK1Y8iqqPkdYCQOwhoFCRZCEIDc5apAWCoFKKqBdtdu8BujeR2fDT21rNwQDSJYLCKeXJ0rdGeCIM4UoXtavlcUEUNBc2aGHZlGXWcQkQKIZaBNGwPJdtB8NAtgkdUppRVdlVNm1V22iI7ULQ30bAUkKxUVsUy6KcgQBCLKHChSdLiLBA5KxSghcKAK4JCLC7VQCKRCIgFB2EAlprRgFkQNpBdqHaqYBIIbSLWk2KPZOFFRReKNRQFJiwIlgIQ5DCF2lBShG3i+kIvE5EkAAGAAAAIGKyyIoVQiKa7A6TYPIJyOjyTPFQJmraAAwM5HYNmhEbVqKLs+XRiiE5AXgiVSAKBg2h9JQaHCoMUBBBQakUDvEqS7yHAcUEqSZnHIgCxgRYKlQFPABMRUibTYgYIMALwBIg4cCAgLJCNtNCaXGZT7baNW6a2oW7LCrgLpeFLCgKKBgWUgDWIEgF22wACkUloJQcUAKCDQ+1eFFIRVWBdiMAoKoVpChAkhyGyrVd22VK8EAC6KNLHYUVI60s6YkQQQoAvLyu4K7gwLKQBrOFXSedwUiVKLaFNGKAeoo2qlL5ndALCYBFRRKlOMLAfRAPFSmlaG1XsEANaBSrVU1bXFARbSNSNYK2+CVTtXa7VZUcAwAA2aN3ABRVBQY0XC4+doaMhEgEQjWAOytEuiqNk0vygAIEOf8Af1weP+jydR/cn85qUEvuEDU8MxwqXCMZi4XQ0JSgKiw0wqQAAAGCMpHjh+El7+U/bIoNCTC0yNa6s0akPUE+KTpXvANDhPxwd2AoKSOLL0ftlTgpciGYdLAe6W6AGqoYOTa7B5sibhSgQLrNQUKIHUAn2X3byDisZo76F9FulCGC/wCTsChxV6HW3ggCwhpUAmkBpnNOCZAVuxNvgDzthVq2rQQCZbS7Yx2j4wx0CMuhVpR1VxChJFkSQ4DrQBw3ploaConNoQXAATBVVhKqvyovyoIsfZXtB/jBChtCUVLs2AKeeEa4shXdrREpiKiCofxnIgEhIWmCC4MG6kVUC29WtUCKTUhAwFSIogzUKAdkEpoYIE0itqD6oI1CC28cIdK3e2iJLyRgD0cBIA0oJwO3DS8P3tqFBMBigDGwTCyM2WbZXnQls3esAM5N7aROboXsSsEzYOYSn2BCCSoGrxBbWJHinXTDuMpxAMLQURxQBSnYbpOb00GIAOYgIHYS74SokFFCIJaUMEZNTctYtCmJ2YWCg0mTQqp1hYosgUcSuSV3CIXtRGAQggOq9tIGN+AmBESo8TdGAvLBNGgGdMCiJtxSKydNt54FsCXBSVItiYgKguiAKnqeDCiExWKFTkPeJKRXEvEaVpAAIJbQiRylpwgGwraNBIupKlAHyiop407ckHhsmyShsZo75GiCTttqBhAkRAWn5SARYFWiGXTQwVuLcoDKQYr5KaIQoQ6Bgi5B304hgBLJKxCrAzJEIiGSwgILyMRUIR5QAClusvXCWkyCRz0pkhqCayLQ8WIFF3o6GVC03ZEKaiXR0YjCNeAYcgLhRSwAGaMyUAa8pwlqxw/QTrFqEq0CHJATWFyGyJ2iwrLhAUOGoNAMD0ZVtOtFDithKANTC8giIVXCYjtbzVIWi2xIxO5EzLWqN4ew7OleOFNPDNYLQYFNFkVDok8jO8Jypk8sKBXiB2XRvkmNhQRIC1lKEAiCka8zB9GthKLItnnLSlts0FWEtiDjN1q3AaWddgAFEcl7WPVxD05NOghDdlDMdKuasl3LCaThBVjJWUAxLbRmBWiBNasND3aGlTpagUJIUS4WX4BQGpgIFaAlRHAl9KCIMtrlI7SOmoAsVXJ0CAfQMU68Ec9lI3WEBTYGLSRLGCIpMA7XLFQGlU9AWCCJ3hGUlYIDESNvlBrMaExxUOdKqlt0rAJAEBGgoHUVdXQNHpQCYVilCA1oRbcIpBijQxaFWAV3xEzzmzykOzUEWzB03jehRa0zE1N4XVAqggZpzQ1MhGPHniPQmcXVL3dWbWBVSy5XsEmootQJBJUHRw1YIEKCxtglnRA1g6Phu+Am6CgcCVHTeMF2wS8BGim2hZhCoGlAUbtBoyol3zz/ANCDz4v6858TCjW1wpCPa0MiqiAuGxSQVhsT8TSTzf4/+5uCqXrZEVDS6SAATCHfwR/ZovMuvb27DmNgkHgJxBRO+yscb9VU2kUrp+PmZQxmb42qlEI9wplM8scXKXr1DfLpsfJ16k33+mOnWBqKEjARBLhkzhG3+O5FjDSiBodCsU/aM+r2OJDle2CM5m7VBUTN7FDUlzqwoccrS6S1zVAhOa9CkKehSzpZj8aiM3XDqPJfuPI9Mebm4cTpN35TCd+UMrlOh1kQ0Pg55pPNU3DH4/J9ueWhSs0xxc1bEzAtpRIMrQFHTLLQUtwRYlcaULGzCuZ7Fg3nJhdVAGGI9MEGke7+8a9gDgg0mSc+ZUbS4hQQvovTRlcOVILQxLOgHrMtsKhuD6Tr+73RVyzycpEi4lUNq/hDMgnGNxs7pxNQNQSyCc5QLqxHHZHHQnZziB6SAwb6D8AEc5N4IQ36KaOwIzfDUDmCKAXSSPueHkxZqDp844KSl5TgK5XMstHE9QpKTzhChOMUuTVJoEYx+gB5YCN3WArh6muQ5RnvpvRzi2CdjEE6doTvAt61om3zUN8PITexQpck/SU7mZ8o1eBX6P7KffHFLr3yWQv81KGDEDwDdds1jWJytYAk0rLDgPtwQp6AtxFCGULWKH7iCAkJw6egV6LATdJ0r4itTeJsSsqHV7fodSNhtvKMy7Ls+tguJaKl2NViW2BXJHqJbXdgMwANjBoEZofZcemR09XjlGyksSLyalK48QLvowBXlJJvLE+Hxr3sAPsJ3AMEr0RqkeOo1IHD5I4lUlaoKV1H1mO85cMpdNKOkAGypcoigGCjQWUHtXJMoXwVDmMHeLM1XajIJUtemWovfstXgu1QzBO4NgbfRbRSGHX4jjxwNtWbVLQkqC8oOpzyQq84rxzbPoXODF8R9e/Iuhgi7TYDQC3NUF/9Dhsv/wB/3nAZseB8ICnkCSSO6pg0m4ei9QRHmGPk6UIiJWkC5ZslLawJ+DRRZ+nF344POAbokNNyrZvbO+BJkRJXGl/KhNPv0LPTPU/Z8t1NE+5nXK/FJAXNSHiDACShkKJDNDxCekAiBYAyEgNUcCbKoAyMQAETVVS4VCEuwXwAcgZxLwsobFAP7bfHDxNbf0Rl3m7vBYBbWHD2LUEHrkJgXLUC4epa0EJKFEGlqfKNVhTzjBMwCExcCco5Frl8V8Ibw8QmMxPDI8C5zoC332WNRyiFzhxj4OsnI8YReNAKghr3Ig3M7oO9BSPhlCrOID3uwPpewtct6wHuvqvCn0LwAwwEFrLEELhp40yE0GfUVnFBQriEHb0m10dwI1KRAIAXBnMaQFQB1CVfe/bySTWiqzq+G7Ms0iDLxmvHwRew+3G/ePDgmDVxGdmLREVigJAo4oCAp79pkS9p/XkaBbg3G1CLi0IUWelZOtZX+llUyTiuYYm/cxRCcJFwpgsyy9ynKZRCEXE30EAvrgGxmYNCRxVIAEjGACUVRXGwWIK7t1AzKmm7RQDNAYeSAav4hg1RgJgIEf4c05ZGSeGFKk/yICCfhXsfiHogBg6tGDMZJc/Ytqwxs6KoDSwsIjoEGJOBdeiEHgRDpp5nNODBRsG/gjyLLiE1WEWKeio4p45TZoL/ABVZQXfp+tjoglGJe174mafFOWSa2cAWMJhqxbC5jwWb+ekM4cW9d0kDHuzJce+oPlNhgHvcya1Lm1CHWe3UA78qA767rAbglVKjrhku1WLbMA7iE4RRAFrsDAsABGw8E04MU3ucLjiItsyqQLfyNxtcJdviFuKQZqAuFg+jYqwMMwLJ1iQXYEuRus54OaKqbkzUHOSPQOW6TIqlR4eqYkriFR3DVA7Uyo0tsuc4mjKDty6KXWe1g4nnkymw7wxPs0lvJTAxJZCzO2jWhcDZb0AgoOlhcwwsNALStgFOACVEeTkchFSWjfFga2NGxxljfHsz7CJCC1wfH/oABBTrf+8YqwYIgDERgQMUREdiII+dh8hslKLat7TIJEr4k7hCCVarWaCGgrA1d4UipA/REK8ECuuncadDOE3WDRIgGnYavZ4zZDw2ncnH7+198U0IndFCbOiyNoUIyTSActikeG7WSL5ym5C5XBHiRhEUhIKhAIPGueLLtt0BfWSyglYX4xsmtsLRgFgRwChq144t5I1UDIVQP+1Xm7njKND03o75AATpOAHl7SqwYXKL1lxSFNPFWnTfq1d8ZGGcMEBnjzaesa8E5KRFlCnpA6tY3OOY8I0CT2cAl4H2J+S/1G17n8EY7UDOukk/CcLDNXbg4YtO4huzt2YlwyKM22VgUVB05pQIwcb8c5bHQgfu8oS8CQtg23iaoqx3S+J3EgBvg6bx4A+aIj4nHnI3rABeTlq79wST+hhEdMXe9j1aEHWXFSXAYxi7QsEOyeSjiTglCEYR0ni0t1wNF4CNMWaENugxUu28fza1+jrhzAz/AJU3BGLUxcSWl8LGlUcVuVjf97yRJB5FFwezax9BjZoWr8LUBYTdPjhlk3Ra21oYCvt/DNzxSPT2awGYACiqKsIECZgWXTJyaER5DtggZpMV9LvxJgH1annXirHS5O8g7GqdTeNCYtFkDQk6kOdFmjxEqKd8PnAx2xIPKMvRcBLJQginBAEVjNTgrpyl3nd+wDtksRobgO1/BWfs1LwQSAfOXqpBBBemoJ7zQCE+zgMdxVgIm9Nl4tmJ5bQNlJkIWMyogpz9x5A2AcOYxMegKGaZWyV2hntI7KixXlTgMei8McVzUBY0hGhDqMCEK4bBVPB6IWZN3GXA3lWVh9z4fV0i1kE3T34NgnO2YNoPXUzO/Rng9ONqeNsvNy7TS+CBNhdsjAU+gTRjrLm9MI/58/WCZOMbLe7431owBFYaR8qUWOKjZtvTKg1x2Fjk5JqDwRJFspu+DXP5eWi7BL9cVknK+qMBaRB9EoWqVrgYH4uNlxPFvkuZJMwX51YXcmzsK/8Aq3Bp1V5HSbrPHvc03x7kZEwoZC1o5kh6lQldCZlHLMjYu55KgTQLSEcs0Qec6EE4uI02ZJZpTxK2I0cAAMQef3T9sGgkhbyVaTh375pFCU0jZSsFNbAD46cqVNUqqtUsCmGdZ4LU4sldSuiGFIwyiu4OxGbRwDYkdOsPBRkqF9CSDxExBBYNKNJWlq8igc8OwNlSkKxSKEwZyJsglRFCbEZQ2QaCazTJ8nbTDwBNu4WAMd4sIiMRNVm2gVFrmnipLvXULgaxDQBVwOIQGUEgh2PBaklzcUlfWgHyeTdRciRAGlQlWhQBqLOcKqhAnIggUTGA2tyKEDUSKbEHOzSCtAgSqADbAIDGWVOEdDYS5B0N0EgHgIAxNso2biwUCJM4GiEl7iIpllEoZA0gnAQSqQJUvlkiq7JSDipRGFDKWR21tXXepWqoFWUStMtcr1B0kM7FttheHQRwjMoqxAvvowGCJEGoldVSiIEUoQkEQgIamN8FTbjw4aIlqJsqWwJaHQAxEVdBrQUgIQQcU50gCoBAEQEARRVVYart2LarxWkQ0ktlIKQgYAggTSGm8IGqWLXIIgFAwwCWAz8tK7Qoi8UEyAiAAIEAEJ1DQkiisVhYxnhxoikgCZnKs0UHIiVvJcAqSimFCA0lSjCDBCiqtETwjE3pvUVSuFRXNX4WJkZsAKjcHRjQgwFBzAjeDs2RhLOLWbTKukSOGXqBlDSQnigKiIyUiA4SYdm0LfvUNUGYBUbVW4KWISopZyjasVQRakvrV3zIWAC9VkqSCAFaQkjS6eMVVJiIUBQgJsWGBE7WPwTCZDgy1SULMSW12Su4ieAAQsbQNDTshQ4EQ4c+HmjQ7Xa1oimXyDQcdVqTtAraKwaAC9TZIjD08ARpa5CATF5hILukgoAAYCIAbHImkXmmdLalkEgqdTSikAiRRQKq47KVVOkVtAtoFQRER2QpCzkdE4drRVKQbUtaEQpSFsR1uCOxLyAoAQAS3jz3MAHBLRJAdnQBEiqBRBAW6qrI8hybW8E2hQTcVjK1kgOeQf0dCEBKlaVKopglAaBRAQNGAtlyi5QAUziTO3mhbUDBEcWkNQWHBU7OLq0AUgsqFgJJVGDcVY2aFhtg3yjNuXdUN4qiKhFpTTRTOVNANDBpWikhVXZbpKeVRNgCO6IQ2bhEaAAGGTBMgsUyrBjwKh7aPhf/AOriCjulm059Bj97iHKve0H3B3x5uR0TiTXXHHGvnvAxAB5AD2CHo2m5y4cpLCm8xFPNFheDPce7vypf/wCG6IBOEv5s/RfmxMRkAk73f4n5sAOP9z/b/wDg5Dxx50ft2f8Azxhs7/NfX2gBddgNI+C/OP1bq0iaNqpu4CBGNlY2YJloSfmkoMsnTCDRAqt7IQ+ZBtKFdgpcc6SBmWdUq29eTx5F0QKySGhcoVFcCFbBoHN+ePzSkHlJBmARAnCiCVxqAQ/eT1FHoU/EFwVZz4GsWGZIXX5CFfGSYxOtxWhXUNcAQHtC30/M7V4OoggF4JMomFaARDUV28BrbG8IaUgh2RHDCCDYbShQStIARAiQyDBO0vcDWaz7gx9s1wEOc6LE9BbI19oPcRC5Al6rAVcGLQk/MpYqDugIBoQePJX0wRHtpc3m6prLNE54s8G2wpgACggDpHkTjZU8vBpspbIWLFfFAW56/B6aL+ZoworImJbAt3CkTSVTIigm+2TgO/E/IUAiV+4VAwAzbqBIRitJrydNnY+nmxEBQiChqFBqhCA8XU2BfWqs2ZIVaF8Xd7ExCVgSfLvrXX/v8fgBlUr4LeNcMt1xxzgGjBacnW00nnm+M2CL1A+J9nnINkHMS/zPYPfN/SSWzzevbOwqb8HpzDRzzPvrBu+/HXf5bYwfOz7b3/TpxABQpNDtDLTtGlQt2fCQxYCJfRAKIqIfAnNMbYn+hoKopuKjApLQDnIXcSOxmjmqZsmhI4bAsMK2a5BbmvqZTCDC+eRA6M5xwxTWBDbXgEkHRvH0rHdouBQKYmQBQ6sC6esofKK4BHBLVyd+cRBqMxaPUZhXkDCkgJiy2DsjiHoYtISGtK38tIKKbOMQ40WIoxNiiKKFVSFFDEB1Sib+c1IhQBmqM2CGrzE9FzLBrFOavOQAU0MPWLCCig1SlLEsdOl5HIEgvxFwdGh6hopVjj4sjQs+EklA+vv9/wBsd26DZOVs9fHjBiSCqCkJAJRXDErwZOakjSvSoyiziLIFrsJCuqgVqSkIKK7VaXTR0b27NFX9nT/hu5DKyVyaPYjk+9yfd4+Q5lYBSlAaVCmGXWYRawoNwWitIvJq9Bo9dE++Hvfy0gy+OMNnZFESVcBQQAoILWZeJYRK5qgzHq5MTSbAYKE/QCBjIECkpCI1U6uoaUQ2Asyv5kAFz440wGqALYDzDZEwClHkHAvPBLu+8wxghqCVJRGaFljv41CHBVBaAiwHlfKUbS98kPAspgrfr5HyhOkodBNDZUGKJbNvuF8AA6HmsUbA0SBE8AUpEIo0EAVOhN8gp+XCzWgoLSi5SNCuMC/Bxg1NxYcecyKLBqUDEJQV0kCbdT0CaDUg0NsHghhD0NjYOE3HnocYNakrzcpYSzItTlnWo7Naeep9zLWbmK64AcS29hCgvJu7Ji9uoqr5V/FJvtoVgbkNpUePmgVLKtBWLrdAABAABDjo+KT7T8uWb8A6/b/zzk2x/bL5b8UENCifGI7Go1Ygb9IOAWLKJT9DSVVwraqnsPi1oxwTLQKQgoSTrVRyq1u+MDufhQd0FTQ6gOSGDhV5rQ9xXNdy8fipoxZaBUQZAABcIBABECNmgtSrXiMAHT1/v8uJSPGANDWUgCheFBFuTfToFHcoreE4GaSgGyIGo1AEbkFCIPO9JHZpFDgigNAEAVgYlf3rgVqwgoiQKSVkV5ADsRMBALoQgWoI5RkfRXmvsAnpsASh/hYBDEllkXWyAKicEVqdbaAKokKRXMnsGEA6YvgiGfIqMek8En6/l3r0afcduyLp1Y8gjEwAVUJmiZ5YQEBCq+TNYDYt4wf0Npgq6dHaiDYSBIuVQgVQQQIFlEVXeSCxKFKbBNWbEdNIkyNHoAt7LQwIGFWN2RrmIaYNx3T6oDVVEgVDkeJadwlJNVMk3nw6TVAJyYAClpsSME0EJp6Xmu3bArvbAFdsLmxu3/7+WJ1rlebSuRLQQLhLDQ2t4YBtabEcUK9EWnMObbZDRYpNl+E7pVPGueMUlbX9sO6R4nC1pbOtPGEck4vHPEYl2bSk5RbfEcVqCJSIQ2OkSVAQ19JVBKvbA6Mybgms2hkpCFIoQdABFq1WJNeIuvbr+PGTQMgrgg1eFLysbdoysApSmylQldnNIBIgXmdGWzGcuTtXzbUTHbZkUNd2i42txqQ0iKMRg4sW81JhaQiTYCwCBroYvzy7/TP9/vnACzzz+WGbfgrb4dqJMHAuik7ATZZWVc4tlkEIIeittXSwthFOU6oZ7U19pwdGbCAPIA+9F9Lxuc4tRJ77+ef1zeGGEBENLdB4FecAQDZDDEkXw0FRWMME1sJHvDnhUoutslLOYoSzQ4MrLVuA80qFPGJFM+AuRHcKSJB9yZT5TdAnpufs74PVBAoKd++EvBLddXcFqoEgoeQbULRYXgyYBLagjsCxwK10rEQ7TaHmBYHoQ9MP8PT8tfM8FYexwfafiWIZCu/TpVIgjEqMDMX6a/z0jTuv0AoGgUgIagrRb7vMTejhe1xHRZbWRRPnlTAIpbZWchgMOr0MoyIXaCF52dBABgyYWsEYAjJzNCvU6TbxV1unEwY8OIB49U++CeCT1lvoe3b9sANm/dyFXvn7flr/2Q==';
        $background = $backgroundv1;
        $employee_id   = $employeeData->employee_id;
        $employee_name = $employeeData->employee_name;
        $position_name = $employeeData->position_name;
        $phone1        = $employeeData->phone1;
        $email         = strtolower($employeeData->email);

        $setupWhatsapp1 = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:WA:1\'', '0813-4808-7991');
        $setupWhatsapp2 = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:WA:2\'', '0812-5152-6284');
        $setupWhatsapp3 = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:WA:3\'', '0812-5152-6026');
        $setupPhone1 = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:P:1\'', '0511-3266789');
        $setupWeb1 = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:WEB:1\'', 'nusantarajaya.co.id');
        $setupIg1 = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:IG:1\'', 'nusantara_jaya.id');
        $setupMail1 = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:M:1\'', 'nusa.njrmbjm@nusantarajaya.co.id');
        $branch = $this->M_Branch->q_master_read_where(" AND cdefault = 'YES' LIMIT 1 ")->row();
        $address = $branch->address;

        $path = 'assets/img/employee/vcard';

        // cek folder
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        /*if(file_exists($output = 'assets/img/employee/vcard/' . $employee_id . '.jpg')){
            return $output;
        }*/

        $message = '
    <style>
        .card-container {
            position: relative;
            width: 180mm;
            height: 110mm;
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background-image: url(\''.$background.'\');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            padding: 5mm;
            box-sizing: border-box;
            overflow: auto;
        }

        table.main{
            margin-top: 15px;
            width: 100%;
        }
        .card-container p {
            margin: 0;
            font-size: 12pt;
            color: #000;
        }

        .card-container .name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
            margin-top: 100px;
        }

        .qr-image {
            width: 200px;
            height: 200px;
            float: left;
            margin-top: 100px;
        }
        .name{
            color: darkgreen !important;
            margin-left: 10px;
        }

        .info-table td {
            padding: 1px;
            font-size: 11pt;
            background: #fff;
        }
        table#footer-table{
            margin-top: 10px;
            position: relative;
            left: 5px;
        }
        td.mr-3{
            margin-left: 1px !important;
        }
        .text-white1{
            color: #fff;
        }
        p.employee-data{
            margin-left: 20px;
        }
    </style>
     <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css"> 
     <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/bower_components/Ionicons/css/ionicons.min.css"> 
    <div class="card-container">
        <table class="main" >
            <tr>
                <td>
                    <p class="name">'.$employee_name.'</p>
                    <p class="employee-data">'.$position_name.'</p>
                    <p class="employee-data">'.$phone1.'</p>
                    <p class="employee-data">'.$email.'</p>
            
                    <table id="footer-table" class="info-table">
                        <tr>
                             <td colspan="4">'.$address.'</td>
                        </tr>
                        <tr>
                            
                            <td><span><i class="fa fa-whatsapp"></i> '.$setupWhatsapp1.'</span></td>
            
                            <td><span><i class="fa fa-whatsapp"></i> '.$setupWhatsapp2.'</span></td>
                            
                            
                        </tr>
                        <tr> 
                            <td><span><i class="fa fa-whatsapp"></i> '.$setupWhatsapp3.'</span></td>
                            <td><span><i class="fa fa-phone"></i> '.$setupPhone1.'</span></td>
                            
                            
                            
                        </tr>
                        <tr>
                            <td><span><i class="fa fa-globe"></i> '.$setupWeb1.'</span></td>
                            <td><span><i class="fa fa-instagram"></i> '.$setupIg1.'</span></td>
                            
                        </tr>
                        <tr>
                            <td colspan="3"><span><i class="fa fa-envelope"></i> '.$setupMail1.'</span></td>
                            
                        </tr>
                    </table>
                </td>
                <td style="min-width: 200px">
                    '.$qrcontact.'
                </td>
            </tr>
        </table>
    </div>';

//        var_dump($message);die();

        $output = 'assets/img/employee/vcard/' . $employee_id . '.jpg';
        if (file_exists($output)){
            unlink($output);
        }
        $this->imageCreator($message, $output);
        return $output;
    }

    public function imageCreator($message = null, $output = null)
    {
        $this->load->library('wkhtmltoimage');
        return $this->wkhtmltoimage->convert($message, 650, 450, $output, false);
    }

        public function convertimagetobase64()
    {
        $path = 'assets/img/base_vcard_v4.jpeg';

// ambil data binary gambar
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);

// encode jadi base64
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        var_dump($base64);
    }

    public function generateallvcard()
    {

        $this->load->library('ciqrcode');
        $this->load->model(array('trans/M_Employee','api/M_Setup'));
//        $json = json_decode(hex2bin($param));
        $allemployeeData = $this->M_Employee->q_get_employee_contact(' AND TRUE ')->result();
        foreach ($allemployeeData as $index => $allemployeeDatum) {
            $employeeData = $this->M_Employee->q_get_employee_contact(' AND employee_id = \''.$allemployeeDatum->employee_id.'\' ')->row();
            $dirPath = 'assets/img/employee/qrvcard/';
            $qrPath  = $dirPath . $employeeData->employee_id . '.png';
            if (file_exists($qrPath)) {
                unlink($qrPath);
            }
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true); // recursive = true
            }
            $image = 'kosong';
            if (!empty($employeeData)) {
                $this->load->model(array('master/M_Branch'));
                $branch = $this->M_Branch->q_master_read_where(" AND cdefault = 'YES' LIMIT 1 ")->row();
                $address = $branch->address;
                $isQRwithLogo = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:U:L\'', 'YES');
                $defaultWebsite = $this->M_Setup->q_mst_read_value(' AND parameter = \'V:WEB:1\'', 'nusantarajaya.co.id');
//            var_dump($isQRwithLogo);die();
                if ($isQRwithLogo == 'YES') {
                    $image = $this->ciqrcode->generateVCardQRCodeWithLogo(array(
                        'prefix' => '',
                        'first_name' => $employeeData->employee_name,
//                    'last_name' => '',
                        'phone' => $employeeData->phone1,
                        'work_phone' => $employeeData->phone2,
//                    'email' => $employeeData->email,
//                    'birthday' => $employeeData->born_date,
                        'address' => $employeeData->home_address,
//                    'address_home'=>array(
//                        'street' => $employeeData->home_address,
//                        'city' => $employeeData->city_name,
//                        'state' => $employeeData->province_name,
//                        'postal' => '-',
//                        'country' => $employeeData->country_name,
//                    ),
                        /*'address_work' =>array(
                            'street' => $address,
                        ),*/
                        'website' => $defaultWebsite,
                        'organization' => $employeeData->organization,
//                    'job_title' => $employeeData->position_name,
//                    'image' => site_url('assets/img/logo-depan/Nusantara.png'),
                    ), $qrPath);
                }else{
                    $image = $this->ciqrcode->generateVCardQRCode(array(
                        'prefix' => '',
                        'first_name' => $employeeData->employee_name,
//                    'last_name' => '',
                        'phone' => $employeeData->phone1,
                        'work_phone' => $employeeData->phone2,
//                    'email' => $employeeData->email,
//                    'birthday' => $employeeData->born_date,
//                    'address_home'=>array(
//                        'street' => $employeeData->home_address,
//                        'city' => $employeeData->city_name,
//                        'state' => $employeeData->province_name,
//                        'postal' => '-',
//                        'country' => $employeeData->country_name,
//                    ),
//                    'address_work' =>array(
//                        'street' => $address,
//                    ),
                        'website' => $defaultWebsite,
                        'organization' => $employeeData->organization,
//                    'job_title' => $employeeData->position_name,
//                    'image' => site_url('assets/img/logo-depan/Nusantara.png'),
                    ), $qrPath);
                }
            }
            $vcard = $this->exportvcard($image,$employeeData);
        }

    }

    function getIdbu() {

        $search = $this->input->post('search');
        $perpage = (int)$this->input->post('perpage');
        $page    = (int)$this->input->post('page');
    
        $offset = ($page - 1) * $perpage;
    
        $query = $this->db->query(
            "SELECT * FROM sc_mst.idbu ORDER BY idbu ASC LIMIT ? OFFSET ?",
            [$perpage, $offset]
        );
    
        $data  = $query->result_array();
        $count = count($data);
    
        header('Content-Type: application/json');
        echo json_encode([
            'totalcount' => $count,
            'group'      => $data
        ]);
    }
    
    
        
}



