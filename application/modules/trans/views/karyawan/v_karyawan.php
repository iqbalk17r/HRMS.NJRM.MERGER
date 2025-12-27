<?php
/*
	@author : Junis
*/
?>

<style>
    .btn-circle.btn-lg {
        width: 50px;
        height: 50px;
        padding: 10px 16px;
        font-size: 18px;
        line-height: 1.33;
        border-radius: 25px;
    }
    .btn-circle.btn-xl {
        width: 70px;
        height: 70px;
        padding: 10px 16px;
        font-size: 24px;
        line-height: 1.33;
        border-radius: 35px;
    }
    .modal-dialog-fl {
        width: 96%;
        height: 90%;
        padding: 1%;
        position: static;
    }


</style>
<!-- Content Header (Page header) -->
<ol class="breadcrumb">
    <div class="pull-right"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
    <?php foreach ($y as $y1) { ?>
        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
            <li><a href="<?php echo site_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
        <?php } else { ?>
            <li class="active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
        <?php } ?>
    <?php } ?>
</ol>
<?php echo $message;?>

    <!-- Custom Tabs (Pulled to the right) -->
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs pull-right">
            <li><a href="#tab_3-2"  data-toggle="tab"><i class="fa fa-users"></i>     Karyawan Borong</a></li>
            <li><a href="#tab_2-2"  data-toggle="tab"><i class="fa fa-user-times"></i>     Karyawan Resign</a></li>
            <li class="active"><a href="#tab_1-1" data-toggle="tab"><i class="fa fa-user"></i>    Karyawan Aktif</a></li>
            <li class="pull-left header"><i class="fa fa-th"></i> Data Karyawan HR</li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1-1">
                <div class="box box-primary">
                    <div class="box-header">
                        <!--<h3 class="box-title">DATA KARYAWAN AKTIF</h3>-->
                        <!--<a href="#" data-toggle="modal" data-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Negara</a>-->
                        <?php if($akses['aksesinput']=='t') { ?>
                            <button class="btn btn-primary" onclick="add_person()" style="margin:10px; color:#ffffff;" title="Input Data Karyawan"><i class="glyphicon glyphicon-plus"></i></button>
                        <?php } ?>
                        <?php if($akses['aksesdownload']=='t') { ?>
                            <a href="<?php echo site_url("trans/karyawan/excel_listkaryawan")?>"  class="btn btn-default" style="margin:10px;" title="Download Excel"><i class="glyphicon glyphicon-download"></i></a>
                        <?php } ?>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive" style='overflow-x:scroll;'>
                        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th width="1%">No.</th>
                                <th>NIK</th>
                                <th>Nama Lengkap</th>
                                <th>FOTO</th>
                                <th>Department</th>
                                <th>Jabatan</th>
                                <th>Tgl Masuk</th>
                                <th>Kantor</th>
                                <th>VCard</th>
                                <!--th>Group Gaji</th-->
                                <th width="8%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_2-2">
                <div class="box box-primary">
                    <div class="box-header">
                        <!--<h3 class="box-title">DATA KARYAWAN RESIGN</h3>-->
                        <?php if($akses['aksesdownload']=='t') { ?>
                            <a href="<?php echo site_url("trans/karyawan/excel_listkaryawan_resign")?>"  class="btn btn-default" style="margin:10px;" title="Download Excel"><i class="glyphicon glyphicon-download"></i></a>
                        <?php } ?>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body table-responsive" style='overflow-x:scroll;'>
                        <table id="resign" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>NIK</th>
                                <th>Nama Lengkap</th>
                                <th>FOTO</th>
                                <th>Department</th>
                                <th>Jabatan</th>
                                <th>Tgl Resign</th>
                                <th>Alamat</th>
                                <!--th>Group Gaji</th--->
                                <th width="8%">Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php $no=0; foreach($list_resignkary as $ls): $no++; ?>
                                <tr>
                                    <td><?php echo $no;?></td>
                                    <td><?php echo $ls->nik;?></td>
                                    <td><?php echo $ls->nmlengkap;?></td>
                                    <td width='10%'>
                                        <?php if ($ls->image<>'') { ?>
                                            <div class="pull-left image">
                                                <img height='50px' width='50px' alt="User Image" class="img-box" src="<?php echo base_url('/assets/img/profile/').'/'.$ls->image;?>">
                                            </div>
                                            <!--<img  alt="User Image" src="<?php echo base_url('/assets/img/profile/').'/'.$ls->image;?>">-->
                                        <?php } else {?>
                                            <img height='50px' width='50px' alt="User Image" src="<?php echo base_url('/assets/img/user.png');?>">
                                        <?php }?>
                                    </td>
                                    <td><?php echo $ls->nmdept;?></td>
                                    <td><?php echo $ls->nmjabatan;?></td>
                                    <td width="8%"><?php echo $ls->tglkeluarkerja;?></td> <!-- salah cok hahah-->
                                    <td><?php echo $ls->alamatktp;?></td> <!-- salah cok hahah-->
                                    <td  width="8%"><a class="btn btn-sm btn-default" href="<?php echo base_url('trans/karyawan/detail_resign').'/'.trim($ls->nik) ?>" title="Detail Resign"><i class="fa fa-bars"></i></a></td> <!-- salah cok hahah-->

                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_3-2">
                <div class="box box-primary">
                    <div class="box-header">
                        <!--<h3 class="box-title">DATA KARYAWAN BORONG</h3>-->
                        <?php if($akses['aksesdownload']=='t') { ?>
                            <a href="<?php echo site_url("trans/karyawan/excel_karyborong")?>"  class="btn btn-default" style="margin:10px;">Export Excel</a>
                        <?php } ?>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body table-responsive" style='overflow-x:scroll;'>
                        <table id="boronggrid" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>NIK</th>
                                <th>Nama Lengkap</th>
                                <th>Department</th>
                                <th>Jabatan</th>
                                <!--th>Tgl Resign</th-->
                                <th>Alamat</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php $no=0; foreach($list_borong as $ls): $no++; ?>
                                <tr>
                                    <td><?php echo $no;?></td>
                                    <td><?php echo $ls->nik;?></td>
                                    <td><?php echo $ls->nmlengkap;?></td>
                                    <td><?php echo $ls->nmdept;?></td>
                                    <td><?php echo $ls->nmjabatan;?></td>
                                    <!--td width="8%"><?php echo $ls->tglkeluarkerja;?></td> <!-- salah cok hahah-->
                                    <td><?php echo $ls->alamatktp;?></td> <!-- salah cok hahah-->

                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>
    <!-- nav-tabs-custom -->


<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-dialog-fl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" align="center">INPUT DATA KARYAWAN</h3>
            </div>
            <div class="modal-body form">
                <form action="<?php echo site_url('trans/karyawan/ajax_add'); ?>" method='post' id="form">
                    <input type="hidden" value="INPUT" name="type"/>
                    <div class="form-horizontal">
                        <div class="stepwizard ">
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step col-sm-1">
                                    <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                                    <p>Step 1</p>
                                </div>
                                <div class="stepwizard-step col-sm-1">
                                    <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                    <p>Step 2</p>
                                </div>
                                <div class="stepwizard-step col-sm-1">
                                    <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                    <p>Step 3</p>
                                </div>
                                <div class="stepwizard-step col-sm-1">
                                    <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                    <p>Step 4</p>
                                </div>
                                <div class="stepwizard-step col-sm-1">
                                    <a href="#step-5" type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                    <p>Step 5</p>
                                </div>
                                <div class="stepwizard-step col-sm-1">
                                    <a href="#step-6" type="button" class="btn btn-default btn-circle" disabled="disabled">6</a>
                                    <p>Step 6</p>
                                </div>
                                <div class="stepwizard-step col-sm-1">
                                    <a href="#step-7" type="button" class="btn btn-default btn-circle"  disabled="disabled">7</a>
                                    <p>Step 7</p>
                                </div>
                                <div class="stepwizard-step col-sm-1">
                                    <a href="#step-8" type="button" class="btn btn-default btn-circle"  disabled="disabled">8</a>
                                    <p>Step 8</p>
                                </div>
                            </div>
                        </div>
                        <div class="row setup-content " id="step-1">
                            <div class="col-sm-12 ">
                                <div class="col-sm-12">
                                    <h3> Step 1</h3>
                                    <div class="row">
                                        <div class="col-sm-6 ">
                                            <div class="box box-primary" >
                                                <div class="box-header">
                                                </div>
                                                <div class="box-body">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Pilih Dari Daftar KTP Pelamar</label>
                                                        <div class="col-sm-9">
                                                            <select  name="dfktp" id="dfktp" style="text-transform:uppercase;"  class="form-control" type="text" required>
                                                                <option value="f">TIDAK</option>
                                                                <option value="t">YA</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <script type="text/javascript" charset="utf-8">
                                                        $(function() {
                                                            $('.ktp1').show();
                                                            $('.ktp2').hide();
                                                            $('.tglpel').hide();
                                                            $('#noktp2').removeAttr('required');
                                                            //$('#tgllam').removeAttr('required');
                                                            //$('#tgllow').removeAttr('required');
                                                            $('#dfktp').change(function(){

                                                                var dfktp=$(this).val();

                                                                if(dfktp=='t'){
                                                                    $('.ktp2').show();
                                                                    $('.tglpel').show();
                                                                    $('.ktp1').hide();
                                                                    //$('#noktp2').prop('required',true);
                                                                    //$('#tgllow').prop('required',true);
                                                                    //$('#tgllam').prop('required',true);
                                                                    $('#noktp1').removeAttr('required');
                                                                    $('.plktp').prop('readOnly',true);
                                                                    //$('#nmlengkap').val('test');
                                                                } else if(dfktp=='f'){
                                                                    $('.ktp2').hide();
                                                                    $('.tglpel').hide();
                                                                    $('.ktp1').show();
                                                                    $('#noktp2').removeAttr('required');
                                                                    //	$('#noktp1').prop('required',true);

                                                                    //	$('#noktp2').removeAttr('required');
                                                                    $('.plktp').prop('readOnly',false);

                                                                    //$('#tgllam').removeAttr('required');
                                                                    //$('#tgllow').removeAttr('required');
                                                                    document.getElementById("form").reset();


                                                                }

                                                            });


                                                            $('#noktp2').change(function(){

                                                                var ktp2=$(this).val();
                                                                /*		var url = "<!?php echo site_url('trans/karyawan/ajax_req_recruitment');?>/"+$(this).val();
                                                                                $('#kotakab').load(url);
                                                                        var nmlengkap=$('#nmktprec').val();
                                                                        $('#nmlengkap').val(nmlengkap); */
                                                                //Ajax Load data from ajax
                                                                $.ajax({
                                                                    url : "<?php echo site_url('trans/karyawan/ajax_req_recruitment')?>/" + $(this).val(),
                                                                    type: "GET",
                                                                    dataType: "JSON",
                                                                    success: function(data)
                                                                    {

                                                                        $('[name="nmlengkap"]').val(data.nmlengkap);
                                                                        $('[name="jk"]').val(data.jk);
                                                                        $('[name="neglahir"]').val(data.neglahir);
                                                                        $('[name="tgllahir"]').val(data.tgllahir);

                                                                        //$('[name="kotalahir"]').val(data.kotalahir);
                                                                        //$('[name="provlahir"]').val(data.provtinggal);
                                                                        //$('#provinsi').val(data.provtinggal);
                                                                        //$('[name="kotalahir"]').val(data.kotatinggal);

                                                                        $('[name="kd_agama"]').val(data.kd_agama);
                                                                        $('[name="alamattinggal"]').val(data.alamattinggal);
                                                                        $('[name="nohp1"]').val(data.nohp1);
                                                                        $('[name="nohp2"]').val(data.nohp2);
                                                                        $('[name="email"]').val(data.email);


                                                                        $('[name="stastus_pernikahan"]').val(data.status_pernikahan);


                                                                    },
                                                                    error: function (jqXHR, textStatus, errorThrown)
                                                                    {
                                                                        alert('Error get data from ajax');
                                                                    }
                                                                });

                                                                $.ajax({
                                                                    url : "<?php echo site_url('trans/karyawan/ajax_cekktpkembar')?>/" + $(this).val(),
                                                                    type: "GET",
                                                                    dataType: "JSON",
                                                                    success: function(data)
                                                                    {
                                                                        if (data>=1){
                                                                            alert('PERINGATAN NO KTP : '+ktp2+ ' SUDAH PERNAH TERDAFTAR SEBAGAI KARYAWAN SILAHKAN CEK KEMBALI !!! (Click OK untuk Melanjutkan)');
                                                                        }

                                                                    },
                                                                    error: function (jqXHR, textStatus, errorThrown)
                                                                    {
                                                                        alert('Error get data from ajax');
                                                                    }
                                                                });


                                                            });

                                                        });


                                                    </script>
                                                    <div class="form-group ktp1">
                                                        <label class="control-label col-sm-3">No KTP</label>
                                                        <div class="col-sm-9">
                                                            <input name="noktp1" id="noktp1" style="text-transform:uppercase;" placeholder="No KTP" class="form-control" type="text" maxlength="18" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">No Kartu Keluarga (KK)</label>
                                                        <div class="col-sm-9">
                                                            <input name="nokk" id="nokk" style="text-transform:uppercase;" placeholder="No KK" class="form-control" type="text" maxlength="20" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group ktp2">
                                                        <label class="control-label col-sm-3">Pilih KTP Dari Pelamar</label>
                                                        <div class="col-sm-9">
                                                            <select  name="noktp2" id="noktp2" style="text-transform:uppercase;"  class="form-control" type="text" >
                                                                <option value="A"><?php echo 'KTP'.' || '.'NAMA LENGKAP';?></option>
                                                                <?php foreach ($calon_karyawan as $lon){ ?>
                                                                    <option value="<?php echo trim($lon->noktp);?>"><?php echo trim($lon->noktp).' || '.trim($lon->nmlengkap);?></option>
                                                                <?php };?>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Nama Lengkap Karyawan</label>
                                                        <div class="col-sm-9">
                                                            <input name="nmlengkap" style="text-transform:uppercase;" placeholder="Nama Lengkap" class="form-control plktp" id="nmlengkap" type="text" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 ">
                                            <div class="box box-primary" >
                                                <div class="box-header">
                                                </div>
                                                <div class="box-body">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Nama Panggilan</label>
                                                        <div class="col-sm-9">
                                                            <input name="callname" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" id="callname" type="text" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Jenis Kelamin</label>
                                                        <div class="col-sm-9">
                                                            <select  name="jk" style="text-transform:uppercase;"  class="form-control plktp" type="text" required>
                                                                <option value="L">LAKI-LAKI</option>
                                                                <option value="P">PEREMPUAN</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Tempat Lahir (Negara)</label>
                                                        <div class="col-sm-9">
                                                            <select type="text" name="neglahir" id='negara' class="form-control col-sm-12" required>
                                                                <?php foreach ($list_opt_neg as $lon){ ?>
                                                                    <option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>
                                                                <?php };?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <script type="text/javascript" charset="utf-8">
                                                        $(document).ready(function(){
                                                            $("#provinsi").change(function (){
                                                                var url = "<?php echo site_url('master/wilayah/add_ajax_kab');?>/"+$(this).val();
                                                                $('#kotakab').load(url).prop("disabled", false);
                                                                return false;
                                                            })
                                                        });
                                                        $(function() {
                                                            $("#provinsi").chained("#negara");
                                                        });
                                                    </script>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Tempat Lahir (Provinsi)</label>
                                                        <div class="col-sm-9">
                                                            <select type="text" name="provlahir" id='provinsi' class="form-control col-sm-12"  required="required">
                                                                <option value="">-KOSONG-</option>
                                                                <?php foreach ($list_opt_prov as $lop){ ?>
                                                                    <option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>
                                                                <?php };?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Tempat Lahir (Kota/Kabupaten)</label>
                                                        <div class="col-sm-9">
                                                            <select type="text" name="kotalahir" id='kotakab' class="form-control col-sm-12"  required="required" disabled>
                                                                <option value="">-KOSONG-</option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="nextBtn btn btn-primary btn-circle btn-lg pull-right" type="button"><i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row setup-content" id="step-2">
                            <div class="col-sm-12">
                                <h3> Step 2</h3>
                                <div class="row">
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">

                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Agama</label>
                                                    <div class="col-sm-8">

                                                        <select  name="kd_agama" class="form-control col-sm-12 plktp" required>
                                                            <option value="" >-- PILIH AGAMA --</option>
                                                            <?php foreach ($list_opt_agama as $loa){ ?>
                                                                <option value="<?php echo trim($loa->kdagama);?>" ><?php echo trim($loa->nmagama);?></option>
                                                            <?php };?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Tanggal Lahir</label>
                                                    <div class="col-sm-8">
                                                        <input name="tgllahir"   class="form-control" id="tgl" placeholder="Tanggal Lahir" data-date-format="dd-mm-yyyy" type="text" required>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Keadaan Fisik</label>
                                                    <div class="col-sm-8">
                                                        <select id="fisikselector" name="stsfisik" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
                                                            <option value="t">BAIK & SEHAT</option>
                                                            <option value="f">CACAT FISIK</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" >
                                                    <label class="control-label col-sm-3">Keterangan Jika Cacat</label>
                                                    <div class="col-sm-8">
                                                        <textarea name="ketfisik" style="text-transform:uppercase;" placeholder="Deskripsikan Cacat fisik" class="form-control" ></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="prevBtn btn btn-warning btn-circle btn-lg pull-left" type="button"><i class="fa fa-arrow-left"></i></button>
                                <button class="nextBtn btn btn-primary btn-circle btn-lg pull-right" type="button"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="row setup-content" id="step-3">
                            <div class="col-sm-12 ">
                                <h3> Step 3</h3>
                                <div class="row">
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <!--div class="form-group"> pindah depan untuk lookup karyawan
                                                  <label class="control-label col-sm-3">No KTP</label>
                                                  <div class="col-sm-9">
                                                    <input name="noktp" style="text-transform:uppercase;" placeholder="No Ktp" class="form-control" type="text" maxlength="18" required>
                                                  </div>
                                                </div-->
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">KTP Dikeluaran di</label>
                                                    <div class="col-sm-9">
                                                        <input name="ktpdikeluarkan" style="text-transform:uppercase;" placeholder="Kota KTP di keluarkan" class="form-control" type="text" maxlength="20" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Tgl KTP Keluar</label>
                                                    <div class="col-sm-9">
                                                        <input name="tgldikeluarkan" style="text-transform:uppercase;" placeholder="Tanggal KTP Di keluarkan" data-date-format="dd-mm-yyyy" class="form-control" id="tgl2" type="text" >
                                                    </div>
                                                </div>
                                                <script type="text/javascript" charset="utf-8">
                                                    $(function() {
                                                        //ktpseumurhidup
                                                        $('#ktps').change(function(){
                                                            $('.ktps').show();
                                                            $('#ktpz' + $(this).val()).hide();
                                                            $('#modal_form').modal('show'); // show bootstrap modal
                                                        });
                                                    });
                                                </script>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">KTP seumur hidup</label>
                                                    <div class="col-sm-9">
                                                        <select id="ktps" name="ktp_seumurhdp" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
                                                            <option value="f">TIDAK</option>
                                                            <option value="t">IYA</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div id="ktpzt" class="ktps">
                                                        <label class="control-label col-sm-3">Tanggal Berlaku</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" name="ktpberlaku" data-date-format="dd-mm-yyyy" id="tglktp" class="form-control" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Kewarganegaraan</label>
                                                    <div class="col-sm-9">
                                                        <select  name="stswn" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" required="required">
                                                            <option value="t">Warga Negara Indonesia</option>
                                                            <option value="f">Warga Negara Asing</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Status Pernikahan</label>
                                                    <div class="col-sm-9">
                                                        <select name="stastus_pernikahan" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" required>
                                                            <option value="" >-- PILIH STATUS NIKAH--</option>
                                                            <?php foreach ($list_opt_nikah as $lonikah){ ?>
                                                                <option value="<?php echo trim($lonikah->kdnikah);?>" ><?php echo trim($lonikah->nmnikah);?></option>
                                                            <?php };?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Golongan Darah</label>
                                                    <div class="col-sm-9">
                                                        <select name="gol_darah" style="text-transform:uppercase;"  class="form-control" type="text" required="required">
                                                            <option value="A">A</option>
                                                            <option value="B">B</option>
                                                            <option value="AB">AB</option>
                                                            <option value="O">O</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="prevBtn btn btn-warning btn-circle btn-lg pull-left" type="button"><i class="fa fa-arrow-left"></i></button>
                                <button class="nextBtn btn btn-primary btn-circle btn-lg pull-right" type="button"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="row setup-content" id="step-4">
                            <div class="col-sm-12 ">
                                <div class="col-sm-12">
                                    <h3> Step 4</h3>
                                    <div class="row">
                                        <div class="col-sm-6 ">
                                            <div class="box box-primary" >
                                                <div class="box-header">
                                                    <h4>Sesuai Ktp</h4>
                                                </div>
                                                <div class="box-body">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">NEGARA</label>
                                                        <div class="col-sm-8">
                                                            <select name="negktp" id='almnegara' class="form-control col-sm-12">
                                                                <option value="">--Pilih Negara---</option>
                                                                <?php foreach ($list_opt_neg as $lon){ ?>
                                                                    <option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>
                                                                <?php };?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <script type="text/javascript" charset="utf-8">
                                                        $(document).ready(function(){
                                                            $("#almprovinsi").change(function (){
                                                                var url = "<?php echo site_url('master/wilayah/add_ajax_kab');?>/"+$(this).val();
                                                                $('#almkotakab').load(url).prop("disabled", false);
                                                                return false;
                                                            })

                                                            $("#almkotakab").change(function (){
                                                                var url = "<?php echo site_url('master/wilayah/add_ajax_kec');?>/"+$(this).val();
                                                                $('#almkec').load(url).prop("disabled", false);
                                                                return false;
                                                            })

                                                            $("#almkec").change(function (){
                                                                var url = "<?php echo site_url('master/wilayah/add_ajax_des');?>/"+$(this).val();
                                                                $('#almkeldesa').load(url).prop("disabled", false);
                                                                return false;
                                                            })
                                                        });
                                                        $(function() {
                                                            $("#almprovinsi").chained("#almnegara");
                                                        });
                                                    </script>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Provinsi</label>
                                                        <div class="col-sm-8">
                                                            <select name="provktp" id='almprovinsi' class="form-control col-sm-12">
                                                                <option value="">-KOSONG-</option>
                                                                <?php foreach ($list_opt_prov as $lop){ ?>
                                                                    <option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>
                                                                <?php };?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Kota/Kabupaten</label>
                                                        <div class="col-sm-8">
                                                            <select name="kotaktp" id='almkotakab' class="form-control col-sm-12" disabled>
                                                                <option value="">-Pilih provinsi Dahulu-</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Kecamatan</label>
                                                        <div class="col-sm-8">
                                                            <select name="kecktp" id='almkec' class="form-control col-sm-12" disabled>
                                                                <option value="">-Pilih Kota/Kabupaten Dahulu-</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Kelurahan/Desa</label>
                                                        <div class="col-sm-8">
                                                            <select name="kelktp" id='almkeldesa' class="form-control col-sm-12" disabled>
                                                                <option value="">-Pilih Kecamatan Dahulu-</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div  class="form-group"  >
                                                        <label class="control-label col-sm-3">Alamat</label>
                                                        <div class="col-sm-9">
                                                            <textarea name="alamatktp" style="text-transform:uppercase;" placeholder="Alamat sesuai dengan KTP" class="form-control" ></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="box box-primary" >
                                                <div class="box-header">
                                                    <h4>Sesuai Tempat Tinggal</h4>
                                                </div>
                                                <div class="box-body">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">NEGARA</label>
                                                        <div class="col-sm-8">
                                                            <select name="negtinggal" id='almsnegara' class="form-control col-sm-12">
                                                                <?php foreach ($list_opt_neg as $lon){ ?>
                                                                    <option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>
                                                                <?php };?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <script type="text/javascript" charset="utf-8">
                                                        $(document).ready(function(){
                                                            $("#almsprovinsi").change(function (){
                                                                var url = "<?php echo site_url('master/wilayah/add_ajax_kab');?>/"+$(this).val();
                                                                $('#almskotakab').load(url).prop("disabled", false);
                                                                return false;
                                                            })

                                                            $("#almskotakab").change(function (){
                                                                var url = "<?php echo site_url('master/wilayah/add_ajax_kec');?>/"+$(this).val();
                                                                $('#almskec').load(url).prop("disabled", false);
                                                                return false;
                                                            })

                                                            $("#almskec").change(function (){
                                                                var url = "<?php echo site_url('master/wilayah/add_ajax_des');?>/"+$(this).val();
                                                                $('#almskeldesa').load(url).prop("disabled", false);
                                                                return false;
                                                            })
                                                        });
                                                        $(function() {
                                                            $("#almsprovinsi").chained("#almsnegara");
                                                        });
                                                    </script>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Provinsi</label>
                                                        <div class="col-sm-8">
                                                            <select name="provtinggal" id='almsprovinsi' class="form-control col-sm-12" >
                                                                <option value="">-Pilih Provinsi-</option>
                                                                <?php foreach ($list_opt_prov as $lop){ ?>
                                                                    <option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>
                                                                <?php };?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Kota/Kabupaten</label>
                                                        <div class="col-sm-8">
                                                            <select name="kotatinggal" id='almskotakab' class="form-control col-sm-12" disabled>
                                                                <option value="">-Pilih Provinsi Dahulu-</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Kecamatan</label>
                                                        <div class="col-sm-8">
                                                            <select name="kectinggal" id='almskec' class="form-control col-sm-12" disabled>
                                                                <option value="">-Pilih Kota/Kabupaten Dahulu-</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Kelurahan/Desa</label>
                                                        <div class="col-sm-8">
                                                            <select name="keltinggal" id='almskeldesa' class="form-control col-sm-12" disabled>
                                                                <option value="">-Pilih Kecamatan Dahulu-</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div  class="form-group"  >
                                                        <label class="control-label col-sm-3">Alamat</label>
                                                        <div class="col-sm-9">
                                                            <textarea name="alamattinggal" style="text-transform:uppercase;" placeholder="Alamat sesuai tempat tinggal" class="form-control plktp" ></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="prevBtn btn btn-warning btn-circle btn-lg pull-left" type="button"><i class="fa fa-arrow-left"></i></button>
                                <button class="nextBtn btn btn-primary btn-circle btn-lg pull-right" type="button"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="row setup-content" id="step-5">
                            <div class="col-sm-12 ">
                                <h3> Step 5</h3>
                                <div class="row">
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">NO HP Utama</label>
                                                    <div class="col-sm-9">
                                                        <input name="nohp1" style="text-transform:uppercase;" placeholder="Nomor Hp Utama" class="form-control plktp" type="text" maxlength="13" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">NO HP kedua</label>
                                                    <div class="col-sm-9">
                                                        <input name="nohp2" style="text-transform:uppercase;" placeholder="Nomor Hp Lainnya" class="form-control plktp" type="text" maxlength="13">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Email</label>
                                                    <div class="col-sm-9">
                                                        <input name="email" placeholder="Alamat email" class="form-control plktp" type="email" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">NPWP</label>
                                                    <div class="col-sm-9">
                                                        <input name="npwp" id="npwp" style="text-transform:uppercase;" placeholder="Nomor NPWP" class="form-control" type="text" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Tanggal NPWP</label>
                                                    <div class="col-sm-9">
                                                        <input name="tglnpwp" style="text-transform:uppercase;" placeholder="Tanggal NPWP" id="tglnpwp" data-date-format="dd-mm-yyyy" class="form-control" type="text" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="prevBtn btn btn-warning btn-circle btn-lg pull-left" type="button"><i class="fa fa-arrow-left"></i></button>
                                <button class="nextBtn btn btn-primary btn-circle btn-lg pull-right" type="button"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="row setup-content" id="step-6">
                            <div class="col-sm-12 ">
                                <h3> Step 6</h3>
                                <div class="row">
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Departemen</label>
                                                    <div class="col-sm-8">
                                                        <select name="bag_dept" class="form-control input-sm " placeholder="---KETIK DEPARTEMEN---" id="dept">
                                                            <option value="" class=""></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function() {
                                                        var totalCount,
                                                            page,
                                                            perPage = 7;
                                                        $('#dept').selectize({
                                                            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                                            valueField: 'kddept',
                                                            labelField: 'nmdept',
                                                            searchField: ['nmdept'],
                                                            options: [],
                                                            create: false,
                                                            render: {
                                                                option: function(item, escape) {
                                                                    return '' +
                                                                        '<div class=\'row\'>' +
                                                                        '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.kddept) + '</div>' +
                                                                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.nmdept) + '</div>' +
                                                                        '</div>' +
                                                                    '';
                                                                }
                                                            },
                                                            load: function(query, callback) {
                                                                query = JSON.parse(query);
                                                                page = query.page || 1;

                                                                if(!totalCount || totalCount > ((page - 1) * perPage)) {
                                                                    $.post(base('master/department/req_department'), {
                                                                        _search_: query.search,
                                                                        _perpage_: perPage,
                                                                        _page_: page
                                                                    }).done(function(json) {
                                                                        totalCount = json.totalcount;
                                                                        callback(json.group);
                                                                    }).fail(function(jqxhr, textStatus, error) {
                                                                        callback();
                                                                    });
                                                                } else {
                                                                    callback();
                                                                }
                                                            }
                                                        }).on('change click', function() {
                                                            $('#subdept').selectize()[0].selectize.clearOptions();
                                                            $('#jabatan').selectize()[0].selectize.clearOptions();
                                                        });
                                                    });
                                                </script>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Sub Departemen</label>
                                                    <div class="col-sm-8">
                                                        <select name="subbag_dept" class="form-control input-sm " placeholder="---KETIK SUB DEPARTEMEN---" id="subdept">
                                                            <option value="" class=""></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function() {
                                                        var totalCount,
                                                            page,
                                                            perPage = 7;
                                                        $('#subdept').selectize({
                                                            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                                            valueField: 'kdsubdept',
                                                            labelField: 'nmsubdept',
                                                            searchField: ['nmsubdept'],
                                                            options: [],
                                                            create: false,
                                                            render: {
                                                                option: function(item, escape) {
                                                                    return '' +
                                                                        '<div class=\'row\'>' +
                                                                        '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.kdsubdept) + '</div>' +
                                                                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.nmsubdept) + '</div>' +
                                                                        '</div>' +
                                                                        '';
                                                                }
                                                            },
                                                            load: function(query, callback) {
                                                                query = JSON.parse(query);
                                                                page = query.page || 1;

                                                                if(!totalCount || totalCount > ((page - 1) * perPage)) {
                                                                    $.post(base('master/department/req_subdepartment'), {
                                                                        _search_: query.search,
                                                                        _perpage_: perPage,
                                                                        _page_: page,
                                                                        kddept: $('#dept').val()
                                                                    }).done(function(json) {
                                                                        totalCount = json.totalcount;
                                                                        callback(json.group);
                                                                    }).fail(function(jqxhr, textStatus, error) {
                                                                        callback();
                                                                    });
                                                                } else {
                                                                    callback();
                                                                }
                                                            }
                                                        }).on('change click', function() {
                                                            $('#jabatan').selectize()[0].selectize.clearOptions();
                                                        });
                                                    });
                                                </script>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Jabatan</label>
                                                    <div class="col-sm-8">
                                                        <select name="jabatan" class="form-control input-sm " placeholder="---KETIK JABATAN---" id="jabatan">
                                                            <option value="" class=""></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function() {
                                                        var totalCount,
                                                            page,
                                                            perPage = 7;
                                                        $('#jabatan').selectize({
                                                            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                                            valueField: 'kdjabatan',
                                                            labelField: 'nmjabatan',
                                                            searchField: ['nmjabatan'],
                                                            options: [],
                                                            create: false,
                                                            render: {
                                                                option: function(item, escape) {
                                                                    return '' +
                                                                        '<div class=\'row\'>' +
                                                                        '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.kdjabatan) + '</div>' +
                                                                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.nmjabatan) + '</div>' +
                                                                        '</div>' +
                                                                        '';
                                                                }
                                                            },
                                                            load: function(query, callback) {
                                                                query = JSON.parse(query);
                                                                page = query.page || 1;

                                                                if(!totalCount || totalCount > ((page - 1) * perPage)) {
                                                                    $.post(base('master/department/req_jabatan'), {
                                                                        _search_: query.search,
                                                                        _perpage_: perPage,
                                                                        _page_: page,
                                                                        kdsubdept: $('#subdept').val()
                                                                    }).done(function(json) {
                                                                        totalCount = json.totalcount;
                                                                        callback(json.group);
                                                                    }).fail(function(jqxhr, textStatus, error) {
                                                                        callback();
                                                                    });
                                                                } else {
                                                                    callback();
                                                                }
                                                            }
                                                        });
                                                    });
                                                </script>
<!--                                                <div class="form-group">-->
<!--                                                    <label class="control-label col-sm-3">Grading Level Jabatan</label>-->
<!--                                                    <div class="col-sm-8">-->
<!--                                                        <select name="kdgradejabatan" id='kdgradejabatan' class="form-control col-sm-12" >-->
<!--                                                            <option value="">-KOSONG-</option>-->
<!--                                                            --><?php //foreach ($list_opt_m_grade_jabatan as $xjab){ ?>
<!--                                                                <option value="--><?php //echo trim($xjab->kdgradejabatan);?><!--">--><?php //echo trim($xjab->nmgradejabatan);?><!--</option>-->
<!--                                                            --><?php //};?>
<!--                                                        </select>-->
<!--                                                    </div>-->
<!--                                                </div>-->
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Job Grade</label>
                                                    <div class="col-sm-8">
                                                        <select name="lvl_jabatan" class="form-control input-sm " placeholder="---KETIK JOB GRADE---" id="lvl_jabatan">
                                                            <option value="" class=""></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function() {
                                                        var totalCount,
                                                            page,
                                                            perPage = 7;
                                                        $('#lvl_jabatan').selectize({
                                                            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                                            valueField: 'kdlvl',
                                                            labelField: 'nmlvljabatan',
                                                            searchField: ['nmlvljabatan'],
                                                            options: [],
                                                            create: false,
                                                            render: {
                                                                option: function(item, escape) {
                                                                    return '' +
                                                                        '<div class=\'row\'>' +
                                                                        '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.kdlvl) + '</div>' +
                                                                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.nmlvljabatan) + '</div>' +
                                                                        '</div>' +
                                                                        '';
                                                                }
                                                            },
                                                            load: function(query, callback) {
                                                                query = JSON.parse(query);
                                                                page = query.page || 1;

                                                                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                                                                    $.post(base('master/jabatan/req_lvljabatan'), {
                                                                        _search_: query.search,
                                                                        _perpage_: perPage,
                                                                        _page_: page
                                                                    }).done(function(json) {
                                                                        totalCount = json.totalcount;
                                                                        callback(json.group);
                                                                    }).fail(function( jqxhr, textStatus, error ) {
                                                                        callback();
                                                                    });
                                                                } else {
                                                                    callback();
                                                                }
                                                            }
                                                        }).on('change click', function() {
                                                            $('#grade_golongan').selectize()[0].selectize.clearOptions();
                                                            $('#kdlvlgp').selectize()[0].selectize.clearOptions();
                                                        });


                                                    });
                                                </script>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Level Grade</label>
                                                    <div class="col-sm-8">
                                                        <select name="grade_golongan" class="form-control input-sm " placeholder="---KETIK LEVEL GRADE---" id="grade_golongan">
                                                            <option value="" class=""></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function() {
                                                        var totalCount,
                                                            page,
                                                            perPage = 7;
                                                        $('#grade_golongan').selectize({
                                                            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                                            valueField: 'kdgrade',
                                                            labelField: 'nmgrade',
                                                            searchField: ['nmgrade'],
                                                            options: [],
                                                            create: false,
                                                            render: {
                                                                option: function(item, escape) {
                                                                    return '' +
                                                                        '<div class=\'row\'>' +
                                                                        '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.kdgrade) + '</div>' +
                                                                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.nmgrade) + '</div>' +
                                                                        '</div>' +
                                                                        '';
                                                                }
                                                            },
                                                            load: function(query, callback) {
                                                                query = JSON.parse(query);
                                                                page = query.page || 1;

                                                                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                                                                    $.post(base('master/jabatan/req_jobgrade'), {
                                                                        _search_: query.search,
                                                                        _perpage_: perPage,
                                                                        _page_: page,
                                                                        lvl_jabatan: $('#lvl_jabatan').val()
                                                                    }).done(function(json) {
                                                                        totalCount = json.totalcount;
                                                                        callback(json.group);
                                                                    }).fail(function( jqxhr, textStatus, error ) {
                                                                        callback();
                                                                    });
                                                                } else {
                                                                    callback();
                                                                }
                                                            }
                                                        }).on('change click', function() {
                                                            $('#kdlvlgp').selectize()[0].selectize.clearOptions();
                                                        });


                                                    });
                                                </script>
                                                <!--<div class="form-group">
                                                    <label class="control-label col-sm-3">Golongan</label>
                                                    <div class="col-sm-8">
                                                        <select name="kdlvlgp" class="form-control input-sm " placeholder="---KETIK GOLONGAN---" id="kdlvlgp">
                                                            <option value="" class=""></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function() {
                                                        var totalCount,
                                                            page,
                                                            perPage = 7;
                                                        $('#kdlvlgp').selectize({
                                                            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                                            valueField: 'kdlvlgp',
                                                            labelField: 'kdlvlgp',
                                                            searchField: ['kdlvlgp'],
                                                            options: [],
                                                            create: false,
                                                            render: {
                                                                option: function(item, escape) {
                                                                    return '' +
                                                                        '<div class=\'row\'>' +
                                                                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.kdlvlgp) + '</div>' +
                                                                        '</div>' +
                                                                        '';
                                                                }
                                                            },
                                                            load: function(query, callback) {
                                                                query = JSON.parse(query);
                                                                page = query.page || 1;

                                                                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                                                                    $.post(base('master/jabatan/req_kdlvlgp'), {
                                                                        _search_: query.search,
                                                                        _perpage_: perPage,
                                                                        _page_: page,
                                                                        grade_golongan: $('#grade_golongan').val()
                                                                    }).done(function(json) {
                                                                        totalCount = json.totalcount;
                                                                        callback(json.group);
                                                                    }).fail(function( jqxhr, textStatus, error ) {
                                                                        callback();
                                                                    });
                                                                } else {
                                                                    callback();
                                                                }
                                                            }
                                                        });


                                                    });
                                                </script>-->
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">idbu</label>
                                                    <div class="col-sm-8">
                                                        <select name="idbu" class="form-control input-sm" placeholder="---KETIK IDBU---" id="idbu">
                                                            <option value="" class=""></option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <script type="text/javascript">
                                                    $('#idbu').selectize({
                                                        plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                                        valueField: 'idbu',
                                                        labelField: 'idbuname',
                                                        searchField: ['idbuname'],
                                                        options: [],
                                                        create: false,
                                                        render: {
                                                            option: function(item, escape) {
                                                                return `
                                                                    <div class="row">
                                                                        <div class="col-xs-3">${escape(item.idbu)}</div>
                                                                        <div class="col-xs-9">${escape(item.idbuname)}</div>
                                                                    </div>
                                                                `;
                                                            }
                                                        },
                                                        load: function(query, callback) {
                                                            $.post(base('trans/karyawan/getIdbu'), {
                                                                search: query,
                                                                perpage: 7,
                                                                page: 1
                                                            })
                                                            .done(function(res) {
                                                                callback(res.group);
                                                            })
                                                            .fail(function() {
                                                                callback();
                                                            });
                                                        }
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Atasan</label>
                                                    <div class="col-sm-8">
                                                        <!--<select name="nik_atasan" id="nikatasan" class="form-control col-sm-12" required>	-->
                                                        <select id="nikatasan" class="form-control" data-placeholder="Pilih Atasan" name="nik_atasan" required>
                                                            <option value="">--Pilih Atasan--</option>
                                                            <?php foreach ($list_opt_atasan as $loan){ ?>
                                                                <option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>
                                                            <?php };?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Atasan Ke-2</label>
                                                    <div class="col-sm-8">
                                                        <!--<select name="nik_atasan" id="nikatasan" class="form-control col-sm-12" required>	-->
                                                        <select id="nikatasan2" class="form-control" data-placeholder="Pilih Atasan" name="nik_atasan2" >
                                                            <option value="">--Pilih Atasan--</option>
                                                            <?php foreach ($list_opt_atasan as $loan){ ?>
                                                                <option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>
                                                            <?php };?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">PTKP</label>
                                                    <div class="col-sm-8">
                                                        <select name="status_ptkp" class="form-control col-sm-12" >
                                                            <?php foreach ($list_opt_ptkp as $lptkp){ ?>
                                                                <option value="<?php echo trim($lptkp->kodeptkp);?>" ><?php echo trim($lptkp->kodeptkp).' | '.trim($lptkp->besaranpertahun);?></option>
                                                            <?php };?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Tanggal Masuk</label>
                                                    <div class="col-sm-8">
                                                        <input name="tglmasukkerja" style="text-transform:uppercase;" placeholder="Tanggal Masuk Karyawan" id="tglmasuk" data-date-format="dd-mm-yyyy" class="form-control" type="text" required="required">
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Kantor Wilayah</label>
                                                        <div class="col-sm-8">
                                                            <select name="kdcabang" id='kdcabang' class="form-control col-sm-12" required>
                                                                <?php foreach ($list_kanwil as $lf){ ?>
                                                                    <option value="<?php echo trim($lf->kdcabang);?>" ><?php echo trim($lf->desc_cabang);?></option>
                                                                <?php };?>
                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Branch ID</label>
                                                        <div class="col-sm-8">
                                                            <input name="branch" value="SBYNSA" class="form-control" type="input" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="prevBtn btn btn-warning btn-circle btn-lg pull-left" type="button"><i class="fa fa-arrow-left"></i></button>
                                <button class="nextBtn btn btn-primary btn-circle btn-lg pull-right" type="button"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="row setup-content" id="step-7">
                            <div class="col-sm-12 ">
                                <h3> Step 7</h3>
                                <div class="row">
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Group Penggajian</label>
                                                    <div class="col-sm-9">
                                                        <select name="grouppenggajian" class="form-control col-sm-12">
                                                            <?php foreach ($list_opt_grp_gaji as $lgaji){ ?>
                                                                <option value="<?php echo trim($lgaji->kdgroup_pg);?>" ><?php echo trim($lgaji->kdgroup_pg).' | '.trim($lgaji->nmgroup_pg);?></option>
                                                            <?php };?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Basis Gaji Wilayah</label>
                                                    <div class="col-sm-9">
                                                        <select name="kdwilayahnominal" id='kdwilayahnominal' class="form-control col-sm-12" >
                                                            <option value="" >-- PILIH WILAYAH BASIS --</option>
                                                            <?php foreach ($list_wilnom as $lw){ ?>
                                                                <option value="<?php echo trim($lw->kdwilayahnominal);?>" ><?php echo trim($lw->nmwilayahnominal);?></option>
                                                            <?php };?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <?php /*
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Gaji Pokok</label>
                                                    <div class="col-sm-9">
                                                        <input name="gajipokok" style="text-transform:uppercase;" placeholder="Gaji Pokok" class="form-control" maxlength="12" type="number" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Gaji BPJS KES</label>
                                                    <div class="col-sm-9">
                                                        <input name="gajibpjs" style="text-transform:uppercase;" placeholder="Gaji BPJS Kesehatan" class="form-control" maxlength="12" type="number" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Gaji BPJS NAKER</label>
                                                    <div class="col-sm-9">
                                                        <input name="gajinaker" placeholder="Gaji BPJS NAKER" class="form-control" maxlength="12" type="number" disabled>
                                                    </div>
                                                </div>
 */     ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Nama BANK</label>
                                                    <div class="col-sm-9">
                                                        <select name="namabank" id='namabank' class="form-control col-sm-12" >
                                                            <?php foreach ($list_opt_bank as $lbank){ ?>
                                                                <option value="<?php echo trim($lbank->kdbank);?>" ><?php echo trim($lbank->nmbank);?></option>
                                                            <?php };?>
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Nama Pemilik Rekening</label>
                                                    <div class="col-sm-9">
                                                        <input name="namapemilikrekening" style="text-transform:uppercase;" placeholder="Nama Pemilik Rekening" class="form-control" type="text" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Nomor Rekening</label>
                                                    <div class="col-sm-9">
                                                        <input name="norek" style="text-transform:uppercase;" placeholder="Nomor Rekening" class="form-control" type="number">
                                                    </div>
                                                </div>
                                                <!--<div class="form-group">
                                                  <label class="control-label col-sm-3">ID Absensi</label>
                                                  <div class="col-sm-9">
                                                    <input name="idabsen" style="text-transform:uppercase;" placeholder="Nomor ID Absensi" class="form-control" type="text" required>
                                                  </div>
                                                </div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="prevBtn btn btn-warning btn-circle btn-lg pull-left" type="button"><i class="fa fa-arrow-left"></i></button>
                                <button class="nextBtn btn btn-primary btn-circle btn-lg pull-right" type="button"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="row setup-content" id="step-8">
                            <div class="col-sm-12 ">
                                <h3> Step 8</h3>
                                <div class="row">
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">ID Absensi</label>
                                                    <div class="col-sm-9">
                                                        <input name="idabsen" style="text-transform:uppercase;" placeholder="Nomor ID Absensi" class="form-control" type="text" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">ID Mesin</label>
                                                    <div class="col-sm-9">
                                                        <select type="text" class="form-control" name="idmesin" id="idmesin">
                                                            <option   value="1"> 1</option>
                                                            <option   value="2"> 2</option>
                                                            <option   value="3"> 3</option>
                                                            <option   value="4"> 4</option>
                                                            <option   value="5"> 5</option>
                                                            <option   value="6"> 6</option>
                                                            <option   value="7"> 7</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">Card Number</label>
                                                    <div class="col-sm-9">
                                                        <input name="cardnumber" style="text-transform:uppercase;" maxlength="12" placeholder="Card Number" class="form-control" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 ">
                                        <div class="box box-primary" >
                                            <div class="box-header">
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">BORONG</label>
                                                    <div class="col-sm-9">
                                                        <select type="text" class="form-control" name="borong" id="borong">
                                                            <option   value="f"> TIDAK</option>
                                                            <option   value="t"> YA</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">SHIFT</label>
                                                    <div class="col-sm-9">
                                                        <select type="text" class="form-control" name="shift" id="shift">
                                                            <option   value="f"> TIDAK</option>
                                                            <option   value="t"> YA</option>
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">LEMBUR</label>
                                                    <div class="col-sm-9">
                                                        <select type="text" class="form-control" name="lembur" id="lembur">
                                                            <option   value="f"> TIDAK</option>
                                                            <option   value="t"> YA</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-3">CALLPLAN</label>
                                                    <div class="col-sm-9">
                                                        <select type="text" class="form-control" name="callplan" id="callplan">
                                                            <option   value="f"> TIDAK</option>
                                                            <option   value="t"> YA</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="prevBtn btn btn-warning btn-circle btn-lg pull-left" type="button"><i class="fa fa-arrow-left"></i></button>
                                <!--<button class="btn btn-success btn-sm pull-right" id="btnSave" onclick="save()" type="submit">Submit</button>-->
                                <button class="btn btn-success btn-circle btn-lg pull-right" type="submit" title="Submit Pucing Kawan - Kawan"><i class="fa fa-check-circle-o"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <!--button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button-->
                </div>
            </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<div class="modal fade" id="modify-data" data-backdrop="static" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true"></div>

<script type="text/javascript">
    function loadmodal(url,config = '') {
        Swal.fire({
            title: 'Memuat data...',
            text: 'Harap tunggu',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });
        $('div#modify-data')
            .empty()
            .load(url, {config:config}, function (response, status, xhr) {
                if (status === 'error') {
                    Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-sm btn-success m-1',
                            cancelButton: 'btn btn-sm btn-warning m-1',
                            denyButton: 'btn btn-sm btn-danger m-1',
                        },
                        buttonsStyling: false,
                    }).fire({
                        position: 'top',
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        html: (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText),
                        showCloseButton: true,
                        showConfirmButton: false,
                        showDenyButton: true,
                        denyButtonText: `Tutup`,
                    }).then(function () {
                    });
                } else {
                    Swal.close()
                    $('div#modify-data').modal('show');
                }
            });
    }
    var save_method; //for save method string
    var table;
    $(document).ready(function() {
        //datatables
        table = $('#table').DataTable({

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language": {
                <?php echo $this->fiky_encryption->constant('datatable_language'); ?>
            },

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo site_url('trans/karyawan/ajax_list')?>",
                "type": "POST",
                "dataFilter": function(data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json); // return JSON string
                }
            },

            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [ -1 ], //last column
                    "orderable": false, //set not orderable
                },
            ],

        });

        $("table#table").on('click', 'a.getcontact', function () {
            var row = $(this);
            loadmodal(row.data('href'))
        })

        $("#resign").dataTable();
        $("#boronggrid").dataTable();
        $("#noktp2").selectize();

        $('#fisikselector').change(function(){
            $('.fisiks').hide();
            $('#' + $(this).val()).show();
        });
    });


    function add_person()
    {
        save_method = 'add';
        var validator = $('#form').data('bootstrapValidator');
        validator.resetForm();
        /*	  $('#rootwizard').bootstrapWizard({onNext: function(tab, navigation, index) {
                    if(index==1) {
                         $("#resign").dataTable();
                        // Make sure we entered the name
                        if($('.required').val()==true) {
                            alert('Masukan Nik');
                            $('#.required').focus();
                            return false;
                        }
                    }

                }});*/
        $('#tgl').datepicker();
        $('#tglktp').datepicker();
        $('#tgl2').datepicker();
        $('#tglmasuk').datepicker();
        $('#tglnpwp').datepicker();
        $('#nikatasan').selectize();
        $('#nikatasan2').selectize();
        //$('#form')[0].reset(); // reset form on modals
        //$('.form-group').removeClass('has-error'); // clear error class
        //$('.help-block').empty(); // clear error string
        $('#modal_form').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        }); // show bootstrap modal
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Input Data Karyawan'); // Set Title to Bootstrap modal title
        $('[name="type"]').val('INPUT');
    }

    function edit_person(id)
    {
        save_method = 'update';

        $('#editform')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('trans/karyawan/ajax_edit/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {

                $('[name="nik"]').val(data.nik);
                $('[name="nmlengkap"]').val(data.nmlengkap);
                $('[name="callname"]').val(data.callname);
                $('[name="jk"]').val(data.jk);
                $('[name="neglahir"]').val(data.neglahir);
                $('[name="provlahir"]').val(data.provlahir);
                $('[name="kotalahir"]').val(data.kotalahir);

                // show bootstrap modal when complete loaded
                $('#modal_form').modal('hide');
                $('#edit_form').modal('show');
                $('.modal-title').text('Edit Data Karyawan'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function detail_person(id)
    {

        $('#detailform')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('trans/karyawan/ajax_edit/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="nik"]').val(data.nik);
                $('[name="nmlengkap"]').val(data.nmlengkap);
                $('[name="callname"]').val(data.callname);
                $('[name="jk"]').val(data.jk);
                $('[name="neglahir"]').val(data.neglahir);
                $('[name="provlahir"]').val(data.provlahir);
                $('[name="kotalahir"]').val(data.kotalahir);
                $('[name="tgllahir"]').val(data.tgllahir);
                $('[name="kd_agama"]').val(data.kd_agama);
                $('[name="stswn"]').val(data.stswn);
                $('[name="stsfisik"]').val(data.stsfisik);
                $('[name="ketfisik"]').val(data.ketfisik);
                $('[name="noktp"]').val(data.noktp);
                /*
                //$('[name="tgl_ktp"]').val(data.tgl_ktp);
                $('[name="ktp_seumurhdp"]').val(data.ktp_seumurhdp);
                $('[name="ktpdikeluarakan"]').val(data.ktpdikeluarakan);
                $('[name="tgldikeluarakan"]').val(data.tgldikeluarakan);
                $('[name="status_pernikahan"]').val(data.status_pernikahan);
                $('[name="gol_darah"]').val(data.gol_darah);
                $('[name="negktp"]').val(data.negktp);
                $('[name="provktp"]').val(data.provktp);
                $('[name="kotaktp"]').val(data.kotaktp);
                $('[name="kecktp"]').val(data.kecktp);
                $('[name="kelktp"]').val(data.kelktp);
                $('[name="alamatktp"]').val(data.alamatktp);
                $('[negtinggal"]').val(data.negtinggal);
                $('[provtinggal"]').val(data.provtinggal);
                $('[kotatinggal"]').val(data.kotatinggal);
                $('[kectinggal"]').val(data.kectinggal);
                $('[keltinggal"]').val(data.keltinggal);
                $('[alamattinggal"]').val(data.alamattinggal);
                $('[nohp1"]').val(data.nohp1);
                $('[nohp2"]').val(data.nohp2);
                $('[npwp"]').val(data.npwp);
                $('[tglnpwp"]').val(data.tglnpwp);
                $('[bag_dept"]').val(data.bag_dept);
                $('[subbag_dept"]').val(data.subbag_dept);
                $('[jabatan"]').val(data.jabatan);
                $('[lvl_jabatan"]').val(data.lvl_jabatan);
                $('[grade_golongan"]').val(data.grade_golongan);
                $('[nik_atasan"]').val(data.nik_atasan);
                $('[status_ptkp"]').val(data.status_ptkp);
                $('[besaranptkp"]').val(data.besaranptkp);
                $('[tglmasukkerja"]').val(data.tglmasukkerja);
                                //'tglkeluarkerja"]').val(data.tglkeluarkerja);
                $('[masakerja"]').val(data.masakerja);
                $('[statuskepegawaian"]').val(data.statuskepegawaian);
                $('[grouppenggajian"]').val(data.grouppenggajian);
                $('[gajipokok"]').val(data.gajipokok);
                $('[gajibpjs"]').val(data.gajibpjs);
                $('[namabank"]').val(data.namabank);
                $('[namapemilikrekening"]').val(data.namapemilikrekening);
                $('[norek"]').val(data.norek);
                                //'shift"]').val(data.shift);
                $('[idabsen"]').val(data.idabsen);
                $('[email"]').val(data.email);
                                //'bolehcuti"]').val(data.bolehcuti);
                                //'sisacuti"]').val(data.sisacuti);
                */
                // show bootstrap modal when complete loaded
                $('#modal_form').modal('hide');
                $('#edit_form').modal('hide');
                $('#detail_form').modal('show');
                $('.modal-title').text('Detail Karyawan'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax
    }

    function save()
    {
        var url;
        if(save_method == 'add')
        {
            url = "<?php echo site_url('trans/karyawan/ajax_add')?>";
            //data = $('#form').serialize();
        }
        else
        {
            url = "<?php echo site_url('trans/karyawan/ajax_update')?>";
            //data = $('#editform').serialize();
        }

        // ajax adding data to database
        $.ajax({
            url : url,
            type: "POST",
            data: $('#form').serialize(),
            //data: data,
            dataType: "JSON",
            success: function(data)
            {
                //if success close modal and reload ajax table
                $('#modal_form').modal('hide');
                $('#edit_form').modal('hide');
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error Menambahkan / update data');
            }
        });
    }

    function delete_person(id)
    {
        if(confirm('Are you sure delete this data?'))
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo site_url('trans/karyawan/ajax_delete')?>/"+id,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    //if success reload ajax table
                    $('#modal_form').modal('hide');
                    reload_table();
                    alert('Hapus Data Sukses');
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error adding / update data');
                }
            });

        }
    }



</script>
