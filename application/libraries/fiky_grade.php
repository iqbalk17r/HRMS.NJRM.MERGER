<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 5/3/19 8:44 AM
 *  * Last Modified: 4/12/19 11:11 AM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2019 .All rights reserved.
 *
 */

class Fiky_grade
{

    protected $_CI;

       function __construct(){
           $this->_CI=&get_instance();
           $this->_CI->load->model(array('master/m_akses','master/m_menu'));
           $this->_CI->load->library(array('session','Fiky_version','Fiky_string','Fiky_menu','Fiky_encryption','Fiky_wilayah'));
       }

    function coba(){
        return 'TEST';
        /**
         * P1 : KODEMENU
         * P2 : NAMA VERSI
         * P3 : SESSION
         */
    }

    function list_department($param = null){
        return $this->_CI->db->query("select * from sc_mst.departmen where kddept is not null and hold = 'N' $param order by kddept asc");
    }

    function list_subdepartment($param = null){
        return $this->_CI->db->query("select * from sc_mst.subdepartmen where kdsubdept is not null and hold = 'N' $param order by kdsubdept asc");
    }

    function list_jabatan($param = null){
        return $this->_CI->db->query("select * from sc_mst.jabatan where kdjabatan is not null and hold = 'N' $param order by kdjabatan asc");
    }

    function list_lvljabatan($param = null){
        return $this->_CI->db->query("select * from sc_mst.lvljabatan where kdlvl is not null and hold = 'N' $param order by kdlvl asc");
    }

    function list_grade($param = null){
        return $this->_CI->db->query("select * from 
                                        (select a.*,b.nmlvljabatan from sc_mst.jobgrade a 
                                        left outer join sc_mst.lvljabatan b on a.kdlvl=b.kdlvl) as x
                                        where kdgrade is not null $param                                        
                                        order by kdgrade asc");
    }

    function list_lvlgaji($param = null){
        return $this->_CI->db->query("select * from sc_mst.m_lvlgp
                                        where c_hold ='NO' $param
                                        order by kdlvlgp");
    }

    function getDepartment($p){
        $gmed=json_decode($p);
        $gmed->search;
        $search = $gmed->search;
        $perpage = $gmed->perpage;
        $page = $gmed->page;
        $param_c="";
        $count = $this->list_department($param_c)->num_rows();
        $search = strtoupper(urldecode($search));

        $perpage = intval($perpage);
        $perpage = $perpage < 1 ? $count : $perpage;


        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (kddept ilike '%$search%' or nmdept ilike '%$search%')";
        $result = $this->list_department($param)->result();
        header('Content-Type: application/json');
        $datanya = json_encode(
            array(
                'totalcount' => $count,
                'search' => $search,
                'perpage' => $perpage,
                'page' => $page,
                'limit' => $limit,
                'group' => $result
            ),
            JSON_PRETTY_PRINT
        );
        return $datanya;
    }

    function getSubDepartment($p){
        $gmed=json_decode($p);
        $gmed->search;
        $search = $gmed->search;
        $perpage = $gmed->perpage;
        $page = $gmed->page;
        $kddept = $gmed->kddept;
        $param_c="";
        $count = $this->list_subdepartment($param_c)->num_rows();
        $search = strtoupper(urldecode($search));

        $perpage = intval($perpage);
        $perpage = $perpage < 1 ? $count : $perpage;


        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and kddept = '$kddept' and (kdsubdept ilike '%$search%' or nmsubdept ilike '%$search%')";
        $result = $this->list_subdepartment($param)->result();
        header('Content-Type: application/json');
        $datanya = json_encode(
            array(
                'totalcount' => $count,
                'search' => $search,
                'perpage' => $perpage,
                'page' => $page,
                'limit' => $limit,
                'group' => $result,
                'kddept' => $kddept
            ),
            JSON_PRETTY_PRINT
        );
        return $datanya;
    }

    function getJabatan($p){
        $gmed=json_decode($p);
        $gmed->search;
        $search = $gmed->search;
        $perpage = $gmed->perpage;
        $page = $gmed->page;
        $kdsubdept = $gmed->kdsubdept;
        $param_c="";
        $count = $this->list_jabatan($param_c)->num_rows();
        $search = strtoupper(urldecode($search));

        $perpage = intval($perpage);
        $perpage = $perpage < 1 ? $count : $perpage;


        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and kdsubdept = '$kdsubdept' and (kdjabatan ilike '%$search%' or nmjabatan ilike '%$search%')";
        $result = $this->list_jabatan($param)->result();
        header('Content-Type: application/json');
        $datanya = json_encode(
            array(
                'totalcount' => $count,
                'search' => $search,
                'perpage' => $perpage,
                'page' => $page,
                'limit' => $limit,
                'group' => $result,
                'kdsubdept' => $kdsubdept
            ),
            JSON_PRETTY_PRINT
        );
        return $datanya;
    }

   function getLvljabatan($p){
       $gmed=json_decode($p);
       $gmed->search;
       $search = $gmed->search;
       $perpage = $gmed->perpage;
       $page = $gmed->page;
       $param_c="";
       $count = $this->list_lvljabatan($param_c)->num_rows();
       $search = strtoupper(urldecode($search));

       $perpage = intval($perpage);
       $perpage = $perpage < 1 ? $count : $perpage;


       $page = intval($page);
       $limit = $perpage * $page;

       $param=" and (kdlvl ilike '%$search%' or nmlvljabatan ilike '%$search%')";
       $result = $this->list_lvljabatan($param)->result();
       header('Content-Type: application/json');
       $datanya = json_encode(
           array(
               'totalcount' => $count,
               'search' => $search,
               'perpage' => $perpage,
               'page' => $page,
               'limit' => $limit,
               'group' => $result
           ),
           JSON_PRETTY_PRINT
       );
       return $datanya;
   }

   function getJobgrade($p){
       $gmed=json_decode($p);
       $gmed->search;
       $search = $gmed->search;
       $perpage = $gmed->perpage;
       $page = $gmed->page;
       $lvl_jabatan = $gmed->lvl_jabatan;

       $param_c="";
       $count = $this->list_grade($param_c)->num_rows();

       $search = strtoupper(urldecode($search));

       $perpage = intval($perpage);
       $perpage = $perpage < 1 ? $count : $perpage;

       $page = intval($page);
       $limit = $perpage * $page;
       $param=" and kdlvl = '$lvl_jabatan' and (nmgrade ilike '%$search%' or nmgrade ilike '%$search%')";
       $result = $this->list_grade($param)->result();
       header('Content-Type: application/json');
       echo json_encode(
           array(
               'totalcount' => $count,
               'search' => $search,
               'perpage' => $perpage,
               'page' => $page,
               'limit' => $limit,
               'group' => $result,
               'lvl_jabatan' => $lvl_jabatan
           ),
           JSON_PRETTY_PRINT
       );
   }

    function getKdlvlgp($p){
        $gmed=json_decode($p);
        $gmed->search;
        $search = $gmed->search;
        $perpage = $gmed->perpage;
        $page = $gmed->page;
        $grade_golongan = $gmed->grade_golongan;


        $dtlgrade= $this->list_grade(" and kdgrade='$grade_golongan'")->row_array();
        $kdlvlgpmin = trim($dtlgrade['kdlvlgpmin']) ?: 0;
        $kdlvlgpmax = trim($dtlgrade['kdlvlgpmax']) ?: 0;

        $param_c="";
        $count = $this->list_lvlgaji($param_c)->num_rows();

        $search = strtoupper(urldecode($search));

        $perpage = intval($perpage);
        $perpage = $perpage < 1 ? $count : $perpage;

        $page = intval($page);
        $limit = $perpage * $page;
        $param=" and kdlvlgp::INT between '$kdlvlgpmin'::INT and '$kdlvlgpmax'::INT and kdlvlgp ilike '%$search%'";
        $result = $this->list_lvlgaji($param)->result();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'totalcount' => $count,
                'search' => $search,
                'perpage' => $perpage,
                'page' => $page,
                'limit' => $limit,
                'group' => $result,
                'grade_golongan' => $grade_golongan
            ),
            JSON_PRETTY_PRINT
        );
    }

    
}
