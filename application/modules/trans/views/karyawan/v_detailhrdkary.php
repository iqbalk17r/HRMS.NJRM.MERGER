<?php
/*
	@author : jancok polll \m/
*/
error_reporting(0);
?>
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
<?php echo validation_errors();?>
<div class="row">

	<!--<div class="col-sm-12">-->
	<!--<a href="<?php /*echo site_url('trans/karyawan/')*/?>" type="button" class="btn btn-default">Kembali</a>	-->
		<!--button type="button" class="btn btn-default" onclick="history.back();">Kembali</button-->
	<!--</div>-->
	<div class="col-sm-12">


		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_1" data-toggle="tab">Profile Karyawan</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_2" data-toggle="tab">Mutasi/Promosi</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_3" data-toggle="tab">Status/Pegawai</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_4" data-toggle="tab">Riwayat Keluarga</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_5" data-toggle="tab">Riwayat Kesehatan</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_6" data-toggle="tab">Riwayat Kerja</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_7" data-toggle="tab">Riwayat Pelatihan</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_8" data-toggle="tab">Riwayat Pendidikan</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_9" data-toggle="tab">BPJS</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_10" data-toggle="tab">SP</a></li>
			</ul>
		</div>
		<div class="tab-content">
			<div class="chart tab-pane active" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_1"  >
                <!--content 1-->
                <div class="col-sm-6">
                    <div class="box box-info col-sm-12">
                        <div class="box-body" style="padding:5px;">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label for="inputnip" class="col-sm-4 control-label">NIK</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="nik" name="nik" value="<?php echo $lp['nik'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputnama" class="col-sm-4 control-label">Nama Lengkap</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="nama" name="nama" value="<?php echo $lp['nmlengkap'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputdept" class="col-sm-4 control-label">Departemen</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['nmdept'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputsub" class="col-sm-4 control-label">Sub Departemen</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="nama" name="nama" value="<?php echo $lp['nmsubdept'];?>" readonly>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="inputjab" class="col-sm-4 control-label">Jabatan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="jabt" name="jabt" value="<?php echo $lp['nmjabatan'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Job Grade</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['nmlvljabatan'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Level Grade</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['nmgrade'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Golongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['kdlvlgp'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <!-- Wilayah di hidden
							<div class="form-group">
								<label for="inputwil" class="col-sm-4 control-label">Wilayah</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="wil" name="wil" value="<?php echo $lp['areaname'];?>" readonly>
								</div>
							</div>
							-->
                                <div class="form-group">
                                    <label for="inputjk" class="col-sm-4 control-label">Jenis Kelamin</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="jk" name="jk" value="<?php if ($lp['jk']=='L') { echo 'Pria';} else {echo 'Wanita';}?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputkota" class="col-sm-4 control-label">Kota</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="kota" name="kota" value="<?php echo $lp['nmkotaktp'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputalamat" class="col-sm-4 control-label">Alamat</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control input-sm" rows="3" name="alamat" id="alamat" placeholder="alamat" readonly><?php echo $lp['alamatktp'];?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputalamat" class="col-sm-4 control-label">Alamat2</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control input-sm" rows="3" name="alamat2" id="alamat2" placeholder="alamat sesuai KTP" readonly><?php echo $lp['alamattinggal'];?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputatasan" class="col-sm-4 control-label">Atasan Ke-1</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="nama" name="nama" value="<?php echo $lp['nmatasan1'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="inputatasan" class="col-sm-4 control-label">Atasan Ke-2</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="nama" name="nama" value="<?php echo $lp['nmatasan2'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="inputttl" class="col-sm-4 control-label">Kota Lahir</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="kotalahir" name="kotalahir" value="<?php echo $lp['nmkotalahir'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputttl" class="col-sm-4 control-label">Tanggal Lahir</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="tgl" name="tgl" value="<?php echo date('d-m-Y', strtotime($lp['tgllahir']));?>" data-date-format="dd-mm-yyyy" readonly>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="inputstatusrmh" class="col-sm-4 control-label">Kantor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="kdcabang" name="kdcabang" value="<?php echo $lp['nmcabang'];?>" readonly>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label for="inputstatusrmh" class="col-sm-4 control-label">Idbu</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="idbu" name="idbu" value="<?php echo $lp['idbuname'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputstatusnikah" class="col-sm-4 control-label">Status Pernikahan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="status_pernikahan" name="status_pernikahan" value="<?php echo $lp['nmstatus_pernikahan'];?>" readonly>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="inputtelp" class="col-sm-4 control-label">No. Telp</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="nohp1" name="nohp1" value="<?php echo $lp['nohp1'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputtelp" class="col-sm-4 control-label">No. Telp 2</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="nohp2" name="nohp2" value="<?php echo $lp['nohp2'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputtelp" class="col-sm-4 control-label">No. Telp 3</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="telp3" name="nohp3" value="<?php echo $lp['nohp3'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputtelp" class="col-sm-4 control-label">NPWP</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="npwp" name="npwp" value="<?php echo $lp['npwp'];?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputtelp" class="col-sm-4 control-label">No Rekening</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="norek" name="norek" value="<?php echo trim($lp['norek']);?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputtelp" class="col-sm-4 control-label">Nama Bank</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="nmbank" name="nmbank" value="<?php echo trim($lp['nmbank']);?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                        </div>
                    </div>
                </div>
                <!--content 2-->
                <div class="col-sm-6">
                    <div class="box box-info col-sm-12">
                        <div class="box-body" style="padding:5px;">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <a href="#" type="button" class="col-sm-4 btn btn-default" data-toggle="modal" data-target=".gantigambar">Ganti Foto</a>
                                    <div class="col-sm-4">
                                        <img src="<?php if ($lp['image']<>'') { echo base_url('assets/img/profile/'.$lp['image']);} else { echo base_url('assets/img/user.png');} ;?>" width="100%"alt="User Image"  border="5">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="agama" class="col-sm-4 control-label">Agama</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="agama" name="agama" value="<?php echo $lp['nmagama'];?>" disabled>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="inputmasuk" class="col-sm-4 control-label">Masuk Kerja</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="masuk" name="masuk" value="<?php echo date('d-m-Y', strtotime($lp['tglmasukkerja']));?>" data-date-format="dd-mm-yyyy" disabled>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="inputkeluar" class="col-sm-4 control-label">Keluar Kerja</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" <?php
                                        $keluar = strtotime($lp['tglkeluarkerja']);
                                        if (empty($lp['tglkeluarkerja'])){
                                            echo "placeholder='Masih Bekerja'";
                                        } else {
                                            $tanggalkeluar = date('d-m-Y',$keluar);
                                            if ($tangalkeluar=='01-01-1970'){
                                                echo "placeholder='Masih Bekerja'";
                                            } else {
                                                echo "value='$tanggalkeluar'";
                                                echo $lp['tglkeluarkerja'];
                                            }
                                        }
                                        ?> id="keluar" name="keluarkrj"  data-date-format="dd-mm-yyyy" disabled>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="wn" class="col-sm-4 control-label">Kewarganegaraan</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $kodewn=trim($lp['stswn']);
                                        switch ($kodewn){
                                            case 'T': echo '<input type="text" class="form-control input-sm" id="wn" name="wn" value="WNI" readonly>'; break;
                                            case 'F': echo '<input type="text" class="form-control input-sm" id="wn" name="wn" value="WNA" readonly>'; break;
                                        }
                                        ?>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Gol. Darah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="darah" name="darah" value="<?php echo $lp['gol_darah'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Masa Kerja</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php
                                        $kta_awal = array('years','year','mons','mon','days','day');
                                        $kta_akhir = array('tahun','tahun','bulan','bulan','hari','hari');
                                        $pesan= str_replace($kta_awal,$kta_akhir,$lp['masakerja1']);
                                        echo $pesan;//$lp['masakerja'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Id Absensi</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['idabsen'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Email</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['email'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
								      <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Email 2</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['email2'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Group Penggajian</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['grouppenggajian'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Kantor Wilayah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['nmcabang'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
                                <div class="form-group">
                                    <label for="darah" class="col-sm-4 control-label">Wilayah Base Range Gaji</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" value="<?php echo $lp['nmwilayahnominal'];?>" readonly>
                                    </div>
                                    <div class="col-sm-10"></div>
                                </div>
								<div class="form-group">
									<label for="darah" class="col-sm-4 control-label">Callplan</label>
									<div class="col-sm-8">
										<input type="text" class="form-control input-sm" value="<?php echo (strtolower(trim($lp['callplan'])) == 't') ? 'YA' : 'TIDAK'; ?>" readonly>
									</div>
									<div class="col-sm-10"></div>
								</div>
                            </div>
                        </div>
                    </div>
                </div>

				<div class="col-sm-6">
					<div class="box box-info col-sm-6">
						<div class="box-body" style="padding:5px;">
							<div class="form-horizontal">
							<div class="form-group">
								<label for="ktp" class="col-sm-4 control-label">No. KTP</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="ktp" name="ktp" value="<?php echo $lp['noktp'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="tktp" class="col-sm-4 control-label">Dikeluarkan di</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="tktp" name="tktp" value="<?php echo $lp['kotaktp'];?>" readonly>
								</div>
									<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="berlaku" class="col-sm-4 control-label">Berlaku</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="berlaku" name="berlaku" value="<?php //echo date('d-m-Y', strtotime($lp['tglmulaiktp']));?> <?php echo date('d-m-Y', strtotime($lp['tgldikeluarkan']));?>" data-date-format="dd-mm-yyyy" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="nokk" class="col-sm-4 control-label">No. Kartu Keluarga (KK)</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="nokk" name="nokk" value="<?php echo $lp['nokk'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
                            <div class="form-group">
                                <label for="nokk" class="col-sm-4 control-label">Status PTKP</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control input-sm" id="status_ptkp" name="status_ptkp" value="<?php echo $lp['nmstatus_ptkp'];?>" readonly>
                                </div>
                                <div class="col-sm-10"></div>
                            </div>

							</div>
						</div>
					</div>
				</div>

				<!--BUTTON-->
                <div class="col-sm-12">
					<div class="form-group">
                        <div class="col-sm-3" style="margin-top: 10px">
                            <a href="<?php echo site_url('trans/karyawan/')?>" type="button" class="btn btn-default pull-left"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
						<div class="col-sm-9" style="margin-top: 10px">
							<a href="<?php echo site_url('trans/karyawan/edit').'/'.$lp['nik'];?>" class="btn btn-primary pull-right" onclick="return confirm('Anda Yakin Ubah Data ini?')" style="margin:10px"><i class="fa fa-gear"></i> EDIT PROFILE KARYAWAN </a>
						</div>
						<div class="col-sm-10"></div>
					</div>
                </div>
			<!--	</div><!-- ./col -->

			</div><!-- ./tab 1-->
            <div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_2">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <a href="#" type="button" class="btn btn-success" data-toggle="modal" data-target="#inputmutasi"><i class="fa fa-plus"></i> Input Mutasi</a>
                        </div>
                        <div class="box-body table-responsive" style='overflow-x:scroll;'>
                            <table id="example1"  class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode</th>
                                    <th>NIK</th>
                                    <th>Departement Lama</th>
                                    <th>Subdepartement Lama</th>
                                    <th>Jabatan Lama</th>
                                    <th>Atasan Lama</th>
                                    <th>Departement Baru</th>
                                    <th>Subdepartement Baru</th>
                                    <th>Jabatan Baru</th>
                                    <!--th>Level Baru</th-->
                                    <th>Atasan Baru</th>
                                    <th>No Dok SK</th>
                                    <th>Tgl SK</th>
                                    <th>Tgl Memo</th>
                                    <th>Tgl Efektif</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php $no=1; foreach ($list_mutasi as $lm) {?>
                                    <tr>
                                        <td><?php echo $no; $no++;?></td>
                                        <td><?php echo $lm->nodokumen;?></td>
                                        <td><?php echo $lm->nik;?></td>
                                        <td><?php echo $lm->olddept;?></td>
                                        <td><?php echo $lm->oldsubdept;?></td>
                                        <td><?php echo $lm->oldjabatan;?></td>
                                        <!--td><?php echo $lm->oldlevel;?></td-->
                                        <td><?php echo $lm->oldatasan;?></td>
                                        <td><?php echo $lm->newdept;?></td>
                                        <td><?php echo $lm->newsubdept;?></td>
                                        <td><?php echo $lm->newjabatan;?></td>
                                        <!--td><?php echo $lm->newlevel;?></td-->
                                        <td><?php echo $lm->newatasan;?></td>
                                        <td><?php echo $lm->nodoksk;?></td>
                                        <td><?php echo $lm->tglsk;?></td>
                                        <td><?php echo $lm->tglmemo;?></td>
                                        <td><?php echo $lm->tglefektif;?></td>
                                        <td><?php echo $lm->ket;?></td>
                                        <td>
                                            <?php if (trim($lm->status)=='I'){?>
                                                <a class="btn btn-sm btn-success" href="<?php echo site_url('trans/karyawan/approvemutasi/').'/'.trim($lm->nodokumen).'/'.trim($lm->nik);?>" title="Detail"><i class="glyphicon glyphicon-pencil"></i> Setujui</a>
                                                <a class="btn btn-sm btn-danger" href="<?php echo site_url('trans/karyawan/deletemutasi').'/'.trim($lm->nodokumen).'/'.trim($lm->nik);?>" title="Hapus" onclick="return_confirm('<?php echo trim($lm->nodokumen);?>')"><i class="glyphicon glyphicon-trash"></i> Batal/Hapus</a>
                                            <?php } else { echo 'DISETUJUI';} ?>

                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--Status KErja STATUS KEPEGAWAIAN-->
            <div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_3">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <h3> Status Kepegawaian Karyawan</h3>
                            <a href="#" type="button" class="btn btn-default" data-toggle="modal" data-target="#inputstskerja"><i class="fa fa-plus"></i> Input Status Kerja</a>
                        </div>
                        <div class="box-body">
                            <table id="example2" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <!--<th>NIK</th>
                                    <th>Nama Karyawan</th>-->
                                    <th>No Dokumen</th>
                                    <th>No SK</th>
                                    <th>Nama Status</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Berakhir</th>
                                    <th>Keterangan</th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_stspeg as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lu->nodok;?></td>
                                        <td><?php echo $lu->nosk;?></td>
                                        <td><?php echo $lu->nmkepegawaian;?></td>
                                        <td><?php echo $lu->tgl_mulai1;?></td>
                                        <td><?php echo $lu->tgl_selesai1;?></td>
                                        <td><?php echo $lu->keterangan;?></td>

                                        <!--td>

                                        <a data-toggle="modal" data-target="#<?php echo trim($lu->nodok);?>" href='#'  class="btn btn-default  btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>


                                        <a  href="<?php $nik=trim($lu->nik); echo site_url("trans/stspeg/hps_stspeg/$nik/$lu->nodok")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                            <i class="fa fa-trash-o"></i> Hapus
                                        </a>

                                    </td-->
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end off Statu KErja-->

            <!-------RIWAYAT KELUARGA---------!---->
            <div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_4">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <h3> Riwayat Keluarga </h3>
                            <a href="#" type="button" class="btn btn-default" data-toggle="modal" data-target=".keluarga"><i class="fa fa-plus"></i> Input Keluarga</a>
                        </div>
                        <div class="box-body">
                            <table id="example4" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <!--<th>NIK</th>
                                    <th>Nama Karyawan</th>-->
                                    <th>Nama Keluarga</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Status Di Keluarga</th>
                                    <th>Tempat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Jenjang Pendidikan</th>
                                    <th>Status Tanggungan</th>
                                    <!--th>Action</th-->
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_riwayat_keluarga as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <!--<td><?php echo $lu->nik;?></td>
                                    <td><?php echo $lu->nmlengkap;?></td>-->
                                        <td><?php echo $lu->nama;?></td>
                                        <td><?php echo $lu->kelamin;?></td>
                                        <td><?php echo $lu->nmkeluarga;?></td>
                                        <td><?php echo $lu->namakotakab;?></td>
                                        <td><?php echo $lu->tgl_lahir;?></td>
                                        <td><?php echo $lu->nmjenjang_pendidikan;?></td>
                                        <td><?php echo $lu->status_tanggungan1;?></td>


                                        <!--td>
                                        <a href='<?php  $nik=trim($lu->nik); echo site_url("trans/riwayat_keluarga/detail/$nik/$lu->no_urut")?>' class="btn btn-default  btn-sm">
                                            <i class="fa fa-edit"></i> Detail
                                        </a>
                                    <?php if($akses['aksesupdate']=='t') { ?>
                                        <a href='<?php  $nik=trim($lu->nik); echo site_url("trans/riwayat_keluarga/edit/$nik/$lu->no_urut")?>' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    <?php } ?>
                                    <?php if($akses['aksesdelete']=='t') { ?>
                                        <a  href="<?php $nik=trim($lu->nik); echo site_url("trans/riwayat_keluarga/hps_riwayat_keluarga/$nik/$lu->no_urut")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                            <i class="fa fa-trash-o"></i> Hapus
                                        </a>
                                    <?php } ?>
                                    </td-->
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--END OF RIWAYAT KELUARGA------->
            <!--RIWAYAT KESEHATAN KARYAWAN-->
            <div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_5">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <h3> RIWAYAT KESEHATAN </h3>
                            <a href="#" type="button" class="btn btn-default" data-toggle="modal" data-target=".kesehatan">Input Kesehatan</a>
                        </div>
                        <div class="box-body">
                            <table id="example5" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <!--<th>NIK</th>
                                    <th>Nama Karyawan</th>-->
                                    <th>Nama penyakit</th>
                                    <th>Periode</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_riwayat_kesehatan as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lu->nmpenyakit;?></td>
                                        <td><?php echo $lu->periode;?></td>
                                        <td><?php echo $lu->keterangan;?></td>
                                        <td>
                                            <a data-toggle="modal" data-target="#<?php echo trim($lu->no_urut);?>" href='#' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>

                                            <a  href="<?php $nik=trim($lu->nik); echo site_url("trans/karyawan/hps_riwayat_kesehatan/$nik/$lu->no_urut")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-trash-o"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END RIWAYAT KESEHATAN -->
            <!-- RIWAYAT KERJA DAN PENGALAMAN-->
            <div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_6">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <h3> Riwayat Kerja & Pengalaman</h3>
                            <a href="#" type="button" class="btn btn-default" data-toggle="modal" data-target=".inputkerja"><i class="fa fa-plus"></i> Input Riwayat Kerja</a>
                        </div>
                        <div class="box-body">
                            <table id="example6" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Bagian</th>
                                    <th>Jabatan</th>
                                    <th>Bulan/Tahun Masuk</th>
                                    <th>Bulan/Tahun Keluar</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_riwayat_pengalaman as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lu->nmperusahaan;?></td>
                                        <td><?php echo $lu->bagian;?></td>
                                        <td><?php echo $lu->jabatan;?></td>
                                        <td><?php echo $lu->tahun_masuk1;?></td>
                                        <td><?php echo $lu->tahun_keluar1;?></td>
                                        <td>
                                            <a data-toggle="modal" data-target="#rwtpglm<?php echo trim($lu->no_urut);?>" href='#' class="btn btn-default  btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>

                                            <a  href="<?php $nik=trim($lu->nik); echo site_url("trans/karyawan/hps_riwayat_pengalaman/$nik/$lu->no_urut")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-trash-o"></i> Hapus
                                            </a>

                                        </td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END RIWAYAT KERJA DAN PENGALAMAN-->
            <!-- INPUT PELATIHAN KARYAWAN -->
            <div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_7">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <h3>Riwayat Pelatihan Karyawan</h3>
                            <a href="#" type="button" class="btn btn-default" data-toggle="modal" data-target=".inputpelatihan"><i class="fa fa-plus"></i> Input Pelatihan</a>
                        </div>
                        <div class="box-body">
                            <table id="example7" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Keahlian</th>
                                    <th>Nama Kursus</th>
                                    <th>Nama Institusi</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_riwayat_pendidikan_nf as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lu->nmkeahlian;?></td>
                                        <td><?php echo $lu->nmkursus;?></td>
                                        <td><?php echo $lu->nminstitusi;?></td>
                                        <td><?php echo $lu->tahun_masuk1;?></td>
                                        <td><?php echo $lu->tahun_keluar1;?></td>
                                        <td><?php echo $lu->keterangan;?></td>

                                        <td>


                                            <a data-toggle="modal" data-target="#pennf<?php echo trim($lu->no_urut);?>" href='#' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>

                                            <a  href="<?php $nik=trim($lu->nik); echo site_url("trans/karyawan/hps_riwayat_pendidikan_nf/$nik/$lu->no_urut")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-trash-o"></i> Hapus
                                            </a>

                                        </td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--- END INPUT PELATIHAN ----->
            <!--- TAB RIWAYAT PENDIDIKAN KARYAWAN-->
            <div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_8">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <h3>Riwayat Pendidikan Karyawan</h3>
                            <a href="#" type="button" class="btn btn-default" data-toggle="modal" data-target=".pendidikan"><i class="fa fa-plus"></i> Input Pendidikan</a>
                        </div>
                        <div class="box-body">
                            <table id="example8" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Pendidikan</th>
                                    <th>Nama Sekolah</th>
                                    <th>Kota/Kab</th>
                                    <th>Jurusan</th>
                                    <th>Program Studi</th>
                                    <th>Tahun Masuk</th>
                                    <th>Tahun Keluar</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_riwayat_pendidikan as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lu->nmpendidikan;?></td>
                                        <td><?php echo $lu->nmsekolah;?></td>
                                        <td><?php echo $lu->kotakab;?></td>
                                        <td><?php echo $lu->jurusan;?></td>
                                        <td><?php echo $lu->program_studi;?></td>
                                        <td><?php echo $lu->tahun_masuk;?></td>
                                        <td><?php echo $lu->tahun_keluar;?></td>
                                        <td><?php echo $lu->keterangan;?></td>

                                        <td>

                                            <a data-toggle="modal" data-target="#<?php echo trim($lu->no_urut);?>" href='#' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>

                                            <a  href="<?php $nik=trim($lu->nik); echo site_url("trans/karyawan/hps_riwayat_pendidikan/$nik/$lu->no_urut")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-trash-o"></i> Hapus
                                            </a>

                                        </td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--END TAB RIWAYAT PENDIDIKAN KARYAWAN-->
            <!---TAB BPJS KARYAWAN -->
            <div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_9">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <h3> BPJS Karyawan</h3>
                            <a href="#" type="button" class="btn btn-default" data-toggle="modal" data-target="#inputbpjs"><i class="fa fa-plus"></i> Input BPJS</a>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <table id="example9" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode Bpjs</th>
                                    <th>Kode Komponen Bpjs</th>
                                    <th>Kode Faskes Utama</th>
                                    <th>Kode Faskes Tambahan</th>
                                    <th>Id Bpjs</th>
                                    <th>Kelas</th>
                                    <th>Tanggal Berlaku</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_bpjskaryawan as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lu->kode_bpjs;?></td>
                                        <td><?php echo $lu->namakomponen;?></td>
                                        <td><?php echo $lu->namafaskes;?></td>
                                        <td><?php echo $lu->namafaskes2;?></td>
                                        <td><?php echo $lu->id_bpjs;?></td>
                                        <td><?php echo $lu->uraian;?></td>
                                        <td><?php echo $lu->tgl_berlaku;?></td>
                                        <td><?php echo $lu->keterangan;?></td>
                                        <td>

                                            <a data-toggle="modal" data-target="#<?php echo trim($lu->id_bpjs);?>" href='#'  class="btn btn-default  btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>


                                            <a  href="<?php $nik=trim($lu->nik); echo  site_url("trans/karyawan/hps_bpjs/$nik/$lu->id_bpjs")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-trash-o"></i> Hapus
                                            </a>

                                        </td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--End TAB BPJS-->
			
			<!---SP KARYAWAN -->
            <div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_10">
                <div class="col-sm-12">
                    <div class="box box-info col-sm-12">
                        <div class="box-body" style="padding:5px;">
                            <div class="row">
                                <div class="box-header">
                                    <h3> Surat Peringatan Karyawan</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <div class="col-sm-12">
                                        <table id="examplex" class="table table-bordered table-striped" >
                                            <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Code</th>
                                                <th>Reference</th>
                                                <th>Type</th>
                                                <th>Start</th>
                                                <th>End</th>
                                                <th>Description</th>
                                                <th>Link</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $no=0; foreach($list_spkaryawan as $lu): $no++;?>
                                                <tr>
                                                    <td width="2%"><?php echo $no;?></td>
                                                    <td><?php echo $lu->docno;?></td>
                                                    <td><?php echo $lu->docref;?></td>
                                                    <td><?php echo $lu->spname;?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($lu->startdate));?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($lu->enddate));?></td>
                                                    <td><?php echo $lu->description;?></td>
                                                    <td>
                                                        <?php if($lu->att_name == ''): ?>
                                                            -
                                                        <?php else: ?>
                                                            <a href="#" onclick="window.open('<?= site_url('assets/files/skperingatan') . '/' . $lu->att_name; ?>')"><?= $lu->att_name ?></a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach;?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">

                                        </div>
                                        <div class="col-sm-10"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

	</div>


</div>

<!--Modal INPUT RIWAYAT KESEHATAN-->
<div class="modal fade kesehatan" id="input" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Input Riwayat Kesehatan <?php echo $nik.'|'.$list_lk['nmlengkap'];?></h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/add_riwayat_kesehatan')?>" method="post">
      <div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama penyakit</label>
								<div class="col-sm-8">
									<input type="text" id="kdpenyakit" name="kdpenyakit" class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">Periode Tahun</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="periode" id="kdbahasa">
										<option value="2014" >2014</option>
										<option value="2015" >2015</option>
										<option value="2016" >2016</option>
										<option value="2017" >2017</option>
										<option value="2018" >2018</option>
										<option value="2019" >2019</option>
										<option value="2020" >2020</option>
										<option value="2021" >2021</option>
										<option value="2022" >2022</option>
										<option value="2023" >2023</option>
										<option value="2024" >2024</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
								</div>
							</div>

						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<!-- END RIWAYAT KESEHATAN --->


<!--Modal Edit Riwayat Kesehatan-->
<?php foreach ($list_riwayat_kesehatan as $lb){?>
<div class="modal fade" id="<?php echo trim($lb->no_urut); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Riwayat Kesehatan</h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/edit_riwayat_kesehatan')?>" method="post">
<div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama penyakit</label>
								<div class="col-sm-8">
									<input type="text" id="kdpenyakit" name="kdpenyakit" value="<?php echo trim($lb->kdpenyakit); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">Periode Tahun</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="periode" id="kdbahasa">
										<option <?php if (trim($lb->periode)=='2014') {echo 'selected';} ?> value="2014" >2014</option>
										<option <?php if (trim($lb->periode)=='2015') {echo 'selected';} ?> value="2015" >2015</option>
										<option <?php if (trim($lb->periode)=='2016') {echo 'selected';} ?> value="2016" >2016</option>
										<option <?php if (trim($lb->periode)=='2017') {echo 'selected';} ?> value="2017" >2017</option>
										<option <?php if (trim($lb->periode)=='2018') {echo 'selected';} ?> value="2018" >2018</option>
										<option <?php if (trim($lb->periode)=='2019') {echo 'selected';} ?> value="2019" >2019</option>
										<option <?php if (trim($lb->periode)=='2020') {echo 'selected';} ?> value="2020" >2020</option>
										<option <?php if (trim($lb->periode)=='2021') {echo 'selected';} ?> value="2021" >2021</option>
										<option <?php if (trim($lb->periode)=='2022') {echo 'selected';} ?> value="2022" >2022</option>
										<option <?php if (trim($lb->periode)=='2023') {echo 'selected';} ?> value="2023" >2023</option>
										<option <?php if (trim($lb->periode)=='2024') {echo 'selected';} ?> value="2024" >2024</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"><?php echo trim($lb->keterangan); ?></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
									<input type="hidden" id="inputby" name="no_urut"  value="<?php echo trim($lb->no_urut);?>" class="form-control" readonly>
								</div>
							</div>

						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<?php } ?>

	<!--Edit dan View status kontrak Kerja-->
	<?php foreach ($list_kontrak as $listkon){?>
	<div class="modal fade ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">EDIT Status Kontrak Kerja</h4>
			</div>
			<div class="modal-body">
				<div class="nav-tabs-custom">
					<!-- Tabs within a box -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>tab_1" data-toggle="tab">View Data</a></li>
						<li><a href="#ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>tab_2" data-toggle="tab">Edit Data</a></li>
					</ul>
					<div class="tab-content no-padding">
						<!-- Morris chart - Sales -->
						<div class="chart tab-pane active" id="ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>tab_1" style="position: relative; height: 125px;">
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tanggal Mulai</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" name="tglmulai" value="<?php
									$timestamp1 = strtotime($listkon->tanggal1);
									$tanggal1 = date('d-m-Y',$timestamp1);
									echo $tanggal1;
									?>" data-date-format="dd-mm-yyyy" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tanggal Selesai</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" name="tglakhir" value="<?php
									$timestamp2 = strtotime($listkon->tanggal2);
									$tanggal2 = date('d-m-Y',$timestamp2);
									echo $tanggal2;
									?>" data-date-format="dd-mm-yyyy" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Masa Kerja (Dalam Tahun)</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" value="<?php echo $listkon->masakerja;?>"name="masakerja" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Status Kerja</label>
								<div class="col-sm-6">
								  <select class="form-control input-sm" name="kdkontrak" disabled>
									<?php foreach ($list_kodekontrak as $likodekon){?>
										<option value="<?php echo $likodekon->kdkontrak;?>" <?php if ($likodekon->kdkontrak==$listkon->kdkontrak) {echo 'selected';}?>><?php echo $likodekon->desc_kontrak;?></option>
									<?php }?>
								  </select>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
									<label for="nosk" class="col-sm-4 control-label">No. SK (Jika Pegawai Tetap)</label>
									<div class="col-sm-6">
										<input value="<?php echo trim($listkon->no_sk);?>" type="text" class="form-control input-sm" name="no_sk" id="no_sk" disabled>
									</div>
									<div class="col-sm-10"></div>
								</div>
						</div>
						<div class="chart tab-pane" id="ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>tab_2" style="position: relative; height: 125px;">
							<form action="<?php echo site_url('hrd/hrd/edit_stskerja');?>" method="post">
							  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">
							  <input type="hidden" name="nomor" value="<?php echo $listkon->nomor;?>">
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Tanggal Mulai</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" name="tglmulai" value="<?php
										$timestampb1 = strtotime($listkon->tanggal1);
										$tanggalb1 = date('d-m-Y',$timestampb1);
										echo $tanggalb1;
										?>" data-date-format="dd-mm-yyyy">
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Tanggal Selesai</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" name="tglakhir" value="<?php
										$timestampb2 = strtotime($listkon->tanggal2);
										$tanggalb2 = date('d-m-Y',$timestampb2);
										echo $tanggalb2;
										?>"  data-date-format="dd-mm-yyyy">
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Masa Kerja (Dalam Tahun)</label>
									<div class="col-sm-6">
									  <input type="text" class="form-control input-sm" value="<?php echo $listkon->masakerja;?>" name="masakerja">
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Status Kerja</label>
									<div class="col-sm-6">
									  <select class="form-control input-sm" name="kdkontrak">
										<?php foreach ($list_kodekontrak as $likodekon){?>
											<option value="<?php echo $likodekon->kdkontrak;?>" <?php if ($likodekon->kdkontrak==$listkon->kdkontrak) {echo 'selected';}?>><?php echo $likodekon->desc_kontrak;?></option>
										<?php }?>
									  </select>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="nosk" class="col-sm-4 control-label">No. SK (Jika Pegawai Tetap)</label>
									<div class="col-sm-6">
										<input value="<?php echo trim($listkon->no_sk);?>" type="text" class="form-control input-sm" name="no_sk" id="no_sk">
									</div>
									<div class="col-sm-10"></div>
								</div>
									<button onclick="return confirm('Simpan data Status Kontrak Kerja ini?')" type="submit" class="btn btn-primary">Simpan</button>
								</form>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	  </div>
	</div>
	<?php }?>
	<!--end Edit dan View status kontrak Kerja-->

	<!--Edit dan View Pelatihan-->
	<?php foreach ($list_pelatihan as $lipel){?>
	<div class="modal fade epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">EDIT Pelatihan</h4>
			</div>
			<div class="modal-body">
				<div class="nav-tabs-custom">
					<!-- Tabs within a box -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>tab_1" data-toggle="tab">View Data</a></li>
						<li><a href="#epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>tab_2" data-toggle="tab">Edit Data</a></li>
					</ul>
					<div class="tab-content no-padding">
						<!-- Morris chart - Sales -->
						<div class="chart tab-pane active" id="epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>tab_1" >
							<div class="form-group">
							<label for="tujuan" class="col-sm-4 control-label">Tanggal Pelatihan</label>
							<div class="col-sm-6">
								<input type="text" class="form-control input-sm" name="tglpelatihan" value="<?php echo $lipel->tglpelatihan;?>" readonly>
							</div>
							<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Lama Pelatihan</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" value="<?php echo trim($lipel->lamapelatihan);?>" maxlength="12" name="lamapelatihan" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Nama Pelatihan</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" value="<?php echo $lipel->nmpelatihan;?>" name="nmpelatihan" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Tempat/Lokasi</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" value="<?php echo $lipel->tempatpelatihan;?>" name="tempatpelatihan" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Trainer</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" value="<?php echo $lipel->trainer;?>" name="trainer" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Keterangan</label>
								<div class="col-sm-6">
									<textarea class="form-control input-sm" name="ketpelatihan" readonly><?php echo $lipel->ketpelatihan;?></textarea>
								</div>
								<div class="col-sm-10"></div>
							</div>
						</div>
						<div class="chart tab-pane" id="epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>tab_2" >
							<form action="<?php echo site_url('hrd/hrd/edit_pelatihan');?>" method="post">
							  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">
							  <input type="hidden" name="kdpelatihan" value="<?php echo trim($lipel->kdpelatihan);?>">
								<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tanggal Pelatihan</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" name="tglpelatihan" value="<?php echo $lipel->tglpelatihan;?>" id="tglpelatihan<?php echo trim($lipel->kdpelatihan);?>" data-date-format="dd-mm-yyyy">
								</div>
								<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Lama Pelatihan</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" value="<?php echo trim($lipel->lamapelatihan);?>" maxlength="12" name="lamapelatihan" required>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Nama Pelatihan</label>
									<div class="col-sm-6">
									  <input type="text" class="form-control input-sm" value="<?php echo $lipel->nmpelatihan;?>" name="nmpelatihan" required>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Tempat/Lokasi</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" value="<?php echo $lipel->tempatpelatihan;?>" name="tempatpelatihan" required>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Trainer</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" value="<?php echo trim($lipel->trainer);?>" maxlength="25" name="trainer" required>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Keterangan</label>
									<div class="col-sm-6">
										<textarea class="form-control input-sm" name="ketpelatihan"><?php echo $lipel->ketpelatihan;?></textarea>
									</div>
									<div class="col-sm-10"></div>
								</div>
									<button onclick="return confirm('Simpan perubahan Data Pelatihan ini?')" type="submit" class="btn btn-primary">Simpan</button>
								</form>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	  </div>
	</div>
	<?php }?>
	<!--end Edit dan View Pelatihan-->


	<!--Inputan Modal Mutasi-->
	<!-- Bootstrap modal Input -->
  <div class="modal fade" id="inputmutasi" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Input Mutasi dan Promosi Baru</h3>
      </div>
      <div class="modal-body form">
        <form action="<?php echo site_url('trans/karyawan/simpanmutasi');?>" method="post" class="form-horizontal">
          <!--<input type="hidden" value="" name="id"/> -->
          <div class="form-body">
            <div class="form-group">
				<label class="control-label col-sm-3">Pilih NIK</label>
				<div class="col-sm-8">
					<select name="newnik" id='listkary1' class="form-control col-sm-12" >
						<option value="">-Pilih Nik & Karyawan-</option>
						<?php foreach ($list_karyawan as $ls){ ?>
						<option value="<?php echo trim($ls->nik);?>" ><?php echo trim($ls->nmlengkap).' || '. trim($ls->nik) ;?></option>
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Department</label>
				<div class="col-sm-8">
						<select name="newkddept" id='dept' class="form-control col-sm-12" >
						<option value="">-Pilih Departemen-</option>
						<?php foreach ($list_opt_dept as $lodept){ ?>
						<option value="<?php echo trim($lodept->kddept);?>" ><?php echo trim($lodept->nmdept);?></option>
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Sub Department</label>
				<div class="col-sm-8">
					<select name="newkdsubdept" id='subdept' class="form-control col-sm-12" >
						<option value="">-KOSONG-</option>
						<?php foreach ($list_opt_subdept as $losdept){ ?>
						<option value="<?php echo trim($losdept->kdsubdept);?>" class="<?php echo trim($losdept->kddept);?>"><?php echo trim($losdept->nmsubdept);?></option>
						<?php };?>
					</select>
				</div>
			</div>
				<script type="text/javascript" charset="utf-8">
							  $(function() {
							$('#dept').selectize();
							//$('#jobgrade').selectize();
							$("#subdept").chained("#dept");
							//$('#subdept').selectize();
							$("#jabatan").chained("#dept");

							$("#jobgrade").chained("#jabatan");

							  //
							  //$('#jabatan').selectize();
							  });
					</script>
			<div class="form-group">
				<label class="control-label col-sm-3">Jabatan</label>
				<div class="col-sm-8">
					<select name="newkdjabatan" id='jabatan' class="form-control col-sm-12" >
						<option value="">-KOSONG-</option>
						<?php foreach ($list_opt_jabt as $lojab){ ?>
						<option value="<?php echo trim($lojab->kdjabatan);?>" class="<?php echo trim($lojab->kddept);?>"><?php echo trim($lojab->nmjabatan);?></option>
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Level Jabatan</label>
				<div class="col-sm-8">
					<select name="newkdlevel" id='lvljabatan' class="form-control col-sm-12" >
					<option value="">-Level Jabatan-</option>
						<?php foreach ($list_opt_lvljabt as $loljab){ ?>
						<option value="<?php echo trim($loljab->kdlvl);?>" ><?php echo trim($loljab->nmlvljabatan);?></option>
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Atasan</label>
				<div class="col-sm-8">
					<select name="newnikatasan" class="form-control col-sm-12" required>
					<option value="">-Pilih NIK Atasan Utama-</option>
						<?php foreach ($list_opt_atasan as $loan){ ?>
						<option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nmlengkap);?></option>
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Atasan-2</label>
				<div class="col-sm-8">
					<select name="newnikatasan2" class="form-control col-sm-12" required>
					<option value="">-Pilih NIK Atasan Kedua-</option>
						<?php foreach ($list_opt_atasan as $loan){ ?>
						<option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nmlengkap);?></option>
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
              <label class="control-label col-md-3">No SK</label>
              <div class="col-md-9">
                <input name="nodoksk" placeholder="Nomor Surat Keputusan" style="text-transform:uppercase;" class="form-control" type="text">
              </div>
            </div>
			<div class="form-group">
			  <label class="control-label col-sm-3">Tanggal SK</label>
			  <div class="col-sm-9">
				<input name="tglsk" style="text-transform:uppercase;" placeholder="Tanggal Surat Keputusan" id="tglsk" data-date-format="dd-mm-yyyy" class="form-control" type="text" required>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-sm-3">Tanggal Memo</label>
			  <div class="col-sm-9">
				<input name="tglmemo" style="text-transform:uppercase;" placeholder="Tanggal Memo Mutasi/Promosi" id="tglmemo" data-date-format="dd-mm-yyyy" class="form-control" type="text" required>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-sm-3">Tanggal Efektif</label>
			  <div class="col-sm-9">
				<input name="tglefektif" style="text-transform:uppercase;" placeholder="Tanggal Masuk Karyawan" id="tglefektif" data-date-format="dd-mm-yyyy" class="form-control" type="text" required>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-sm-3">Keterangan</label>
			  <div class="col-sm-9">
				<textarea name="ket" style="text-transform:uppercase;" placeholder="Keterangan Mutasi / Promosi pegawai" id="tglmasuk" data-date-format="dd-mm-yyyy" class="form-control" type="text"></textarea>
			  </div>
			</div>
          </div>

          </div>
          <div class="modal-footer">
            <button  type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          </div>
		  </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal -->


<!--- INPUT STATUS KERJA ----->
<div class="modal fade" id="inputstskerja" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Input Status Kepegawaian <?php echo $nik.'|'.$list_lk['nmlengkap'];?></h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/add_stspeg')?>" method="post">
      <div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $list_lk['nik']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Department</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nmdept"  value="<?php echo $list_lk['nmdept']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Sub Department</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nmsubdept"  value="<?php echo $list_lk['nmsubdept']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jabatan</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nmjabatan"  value="<?php echo $list_lk['nmjabatan']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jabatan</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nosk"  value="<?php echo $list_lk['nmjabatan']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Atasan</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nmatasan"  value="<?php echo $list_lk['nmatasan']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>



						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
						<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">Nama Kepegawaian</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kdkepegawaian" id="kdkepegawaian1">
									<option value="">--Pilih Kepegawaian--></option>
									  <?php foreach($list_kepegawaian as $listkan){?>
									  <!--option value=""> Masukkan Opsi </option-->
									  <option value="<?php echo trim($listkan->kdkepegawaian);?>" ><?php echo $listkan->nmkepegawaian;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<script type="text/javascript" charset="utf-8">
							$(function() {

											$('#kdkepegawaian1').change(function(){

												var kdkepegawaian=$('#kdkepegawaian1').val();

													if(kdkepegawaian=='KT'){
														$('#tglselesai').hide();
															$('#tglmulai').show();
														$('#dateselesai').removeAttr('required');
														//$('#statptg1').prop('required',true);
													} else if(kdkepegawaian=='KK'){
														$('#tglmulai').show();
															$('#datemulai').prop('required',true);
														$('#tglselesai').show();
															$('#dateselesai').prop('required',true);

													} else if(kdkepegawaian=='HL'){
														$('#tglmulai').show();
															$('#datemulai').prop('required',true);
														$('#tglselesai').show();
															$('#dateselesai').prop('required',true);
													}  else if(kdkepegawaian=='MG'){
														$('#tglmulai').show();
															$('#datemulai').prop('required',true);
														$('#tglselesai').show();
															$('#dateselesai').prop('required',true);
													}	else if(kdkepegawaian=='KO'){
														$('#datemulai').removeAttr('required');
														$('#tglmulai').hide();
														$('#tglselesai').show();
															$('#dateselesai').prop('required',true);
														$('#bolehcuti').hide();
													}


											});
										});
							</script>
							<div id="tglmulai" class="tglmulaiKO" >
							<div class="form-group">
								<label class="col-sm-4">Tanggal Mulai</label>
								<div class="col-sm-8">
									<input type="text" id="datemulai" name="tgl_mulai" data-date-format="dd-mm-yyyy"  class="form-control" required>
								</div>
							</div>
							</div>
							<div class="form-group">
							<div id="tglselesai" class="tglselesaiKT" >
								<label class="col-sm-4">Tanggal Selesai</label>
								<div class="col-sm-8">
									<input type="text" id="dateselesai" name="tgl_selesai" data-date-format="dd-mm-yyyy"  class="form-control" required>
								</div>
							</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">No. SK</label>
								<div class="col-sm-8">
									<input type="text" id="noskstspeg" name="noskstspeg" class="form-control" style="text-transform:uppercase" maxlength="50">
								</div>
							</div>
							<div class="form-group">
							<div id="bolehcuti" class="bolehcutiKO bolehcutiMG" >
								<label class="col-sm-4">Boleh Cuti</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="cuti" id="kdbahasa">
										<option  value="F" >TIDAK</option>
										<option  value="T" >YA</option>
									</select>
								</div>
							</div>
							</div>
							<div class="form-group">
							<div id="ojt">
								<label class="col-sm-4">OJT</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="ojt" id="ojt_select">
										<option value="F">TIDAK</option>
										<option value="T">YA</option>
									</select>
								</div>
							</div>
							</div>
							<div class="form-group" id="form_duedate" style="display:none;">
							<div id="duedate_group" >
								<label class="col-sm-4">Due Date OJT</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="duedate_ojt" id="duedate_ojt" data-date-format="dd-mm-yyyy" placeholder="dd-mm-yyyy">
								</div>
							</div>
							</div>
							<script>
								$(function() {
									$('#ojt_select').change(function() {
										if ($(this).val() === 'T') {
											$('#form_duedate').show();
											$('#duedate_ojt').prop('required', true);
											$('#duedate_ojt').datepicker();
										} else {
											$('#form_duedate').hide();
											$('#duedate_ojt').prop('required', false);
											$('#duedate_ojt').val('');
										}
									});
								});
							</script>
							
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
								</div>
							</div>

						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>



      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
	<!--end input Status Kerja-->

<!--END OF MODAL RIWAYAT KELUARGA --->
<div class="modal fade keluarga" id="input" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Input Riwayat Keluarga <?php echo $nik.'|'.$list_lk['nmlengkap'];?></h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/add_riwayat_keluarga')?>" method="post">
      <div class="modal-body">
		<div class="row">
			<div class="col-sm-4">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Status Di Keluarga</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kdkeluarga" id="kdkeluarga">
									  <?php foreach($list_keluarga as $listkan){?>
									  <option value="<?php echo trim($listkan->kdkeluarga);?>" ><?php echo $listkan->nmkeluarga;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Keluarga</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nama"  class="form-control" style="text-transform:uppercase" maxlength="40" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jenis Kelamin</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kelamin" id="kotakab">

										<option value="PRIA" >PRIA</option>
										<option value="WANITA" >WANITA</option>
									</select>
								</div>
							</div>
							<script type="text/javascript" charset="utf-8">
							  $(function() {
								$("#kodeprov").chained("#kodenegara");
								$("#kodekotakab").chained("#kodeprov");
								$("#kodekomponen").chained("#kode_bpjs");
							  });
							</script>
							<div class="form-group">
								<label class="col-sm-4">Tempat Lahir Negara</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kodenegara" id="kodenegara">
									  <?php foreach($list_negara as $listkan){?>
									  <option value="<?php echo trim($listkan->kodenegara);?>" ><?php echo $listkan->namanegara;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tempat Lahir Provinsi</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kodeprov" id="kodeprov">
									  <?php foreach($list_prov as $listkan){?>
									  <option value="<?php echo trim($listkan->kodeprov);?>" class="<?php echo trim($listkan->kodenegara);?>"><?php echo $listkan->namaprov;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tempat Lahir Kota/Kab</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kodekotakab" id="kodekotakab">
									  <?php foreach($list_kotakab as $listkan){?>
										<option value="<?php echo trim($listkan->kodekotakab);?>" class="<?php echo trim($listkan->kodeprov);?>" ><?php echo $listkan->namakotakab;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tanggal Lahir</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput" name="tgl_lahir"   data-date-format="dd-mm-yyyy" class="form-control dateinput" required>
								</div>
							</div>


						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-4">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">Jenjang Pendidikan</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kdjenjang_pendidikan" id="kdjenjang_pendidikan">
									  <?php foreach($list_jenjang_pendidikan as $listkan){?>
									  <option value="<?php echo trim($listkan->kdjenjang_pendidikan);?>" ><?php echo $listkan->nmjenjang_pendidikan;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Pekerjaan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="pekerjaan"  class="form-control" style="text-transform:uppercase" maxlength="30" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Status Hidup</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="status_hidup" id="kotakab">

										<option value="T" >MASIH HIDUP</option>
										<option value="F" >MENINGGAL</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Status Tanggungan</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="status_tanggungan" id="colorselector">

										<option value="F" >NO</option>
										<option value="T" >YES</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">No. NPWP</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="no_npwp"  class="form-control" data-inputmask='"mask": "99.999.999.9-999.999"' data-mask=""  >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tanggal Mulai Berlaku Npwp</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput1" name="npwp_tgl"   data-date-format="dd-mm-yyyy" class="form-control dateinput" >
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-4">

			</div>
		</div><!--row-->
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<!--END OF MODAL RIWAYAT KELUARGA --->
<!--INPUT RIWAYAT KERJA -->
<div class="modal fade inputkerja" id="input" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Input Riwayat Pengalaman Kerja <?php echo $nik.'|'.$list_lk['nmlengkap'];?></h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/add_riwayat_pengalaman')?>" method="post">
      <div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Perusahaan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nmperusahaan"  class="form-control" style="text-transform:uppercase" maxlength="50" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Bidang Usaha</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="bidang_usaha"  class="form-control" style="text-transform:uppercase" maxlength="40" required>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Bulan/Tahun Masuk</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput" name="tahun_masuk"   data-date-format="dd-mm-yyyy" class="form-control dateinput" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Bulan/Tahun Keluar</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput1" name="tahun_keluar"   data-date-format="dd-mm-yyyy" class="form-control dateinput" required>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">Bagian</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="bagian"  class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jabatan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="jabatan"  class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Atasan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nmatasan"  class="form-control" style="text-transform:uppercase" maxlength="50" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jabatan Atasan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="jbtatasan"  class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
								</div>
							</div>

						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>



      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
	<!---- EDIT RIWAYAT KERJA KARYAWAN -->

<?php foreach ($list_riwayat_pengalaman as $ld){?>
<div class="modal fade" id="rwtpglm<?php echo trim($ld->no_urut); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Riwayat Pengalaman Kerja</h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/edit_riwayat_pengalaman')?>" method="post">
<div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>

								<div class="form-group">
								<label class="col-sm-4">Nama Perusahaan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nmperusahaan"  value="<?php echo trim($ld->nmperusahaan);?>" class="form-control" style="text-transform:uppercase" maxlength="50" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Bidang Usaha</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="bidang_usaha"  value="<?php echo trim($ld->bidang_usaha);?>" class="form-control" style="text-transform:uppercase" maxlength="40" reado>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Bulan/Tahun Masuk</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput2" name="tahun_masuk"   value="<?php echo trim($ld->tahun_masuk1);?>" data-date-format="dd-mm-yyyy" class="form-control dateinput" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Bulan/Tahun Keluar</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput3" name="tahun_keluar" value="<?php echo trim($ld->tahun_keluar1);?>"  data-date-format="dd-mm-yyyy" class="form-control dateinput" required>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
								<div class="form-group">
								<label class="col-sm-4">Bagian</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="bagian"  value="<?php echo trim($ld->bagian);?>" class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jabatan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="jabatan" value="<?php echo trim($ld->jabatan);?>"  class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Atasan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nmatasan" value="<?php echo trim($ld->nmatasan);?>" class="form-control" style="text-transform:uppercase" maxlength="50" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jabatan Atasan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="jbtatasan"  value="<?php echo trim($ld->jbtatasan);?>" class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"><?php echo trim($ld->keterangan); ?></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
									<input type="hidden" id="inputby" name="no_urut"  value="<?php echo trim($ld->no_urut);?>" class="form-control" readonly>
								</div>
							</div>

						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<?php } ?>
<!--end input Riwayat Kerja-->
<!--INPUT PELATIHAN PENDIDIKAN NF-->
	<div class="modal fade inputpelatihan" id="input" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Input Riwayat keahlian Non Formal <?php echo $nik.'|'.$list_lk['nmlengkap'];?></h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/add_riwayat_pendidikan_nf')?>" method="post">
      <div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Bidang Keahlian</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kdkeahlian" id="kdkeahlian">
									  <?php foreach($list_keahlian as $listkan){?>
									  <option value="<?php echo trim($listkan->kdkeahlian);?>" ><?php echo $listkan->nmkeahlian;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Kursus</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nmkursus"  class="form-control" style="text-transform:uppercase" maxlength="40" required>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Nama Institusi</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nminstitusi"  class="form-control" style="text-transform:uppercase" maxlength="30" required>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">Tanggal Mulai</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput" name="tahun_masuk" data-date-format="dd-mm-yyyy"  class="form-control dateinput" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tanggal Selesai</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput1" name="tahun_keluar" data-date-format="dd-mm-yyyy"  class="form-control dateinput" required>
								</div>
							</div>


							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
								</div>
							</div>

						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>

<!--Modal untuk Edit PELATIHAN---->
<?php foreach ($list_riwayat_pendidikan_nf as $lb){?>
<div class="modal fade" id="pennf<?php echo trim($lb->no_urut); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Riwayat Pendidikan Non Formal</h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/edit_riwayat_pendidikan_nf')?>" method="post">
<div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Bidang Keahlian</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kdkeahlian" id="kdkeahlian">
									  <?php foreach($list_keahlian as $listkan){?>
									  <option <?php if (trim($lb->kdkeahlian)==trim($listkan->kdkeahlian)) { echo 'selected';}?> value="<?php echo trim($listkan->kdkeahlian);?>" ><?php echo $listkan->nmkeahlian;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Kursus</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nmkursus" value="<?php echo trim($lb->nmkursus); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" required>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Nama Institusi</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nminstitusi"  value="<?php echo trim($lb->nminstitusi); ?>" class="form-control" style="text-transform:uppercase" maxlength="30" required>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">Tanggal Mulai</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput2" name="tahun_masuk"  value="<?php echo trim($lb->tahun_masuk1); ?>" data-date-format="dd-mm-yyyy" class="form-control dateinput" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tanggal Selesai</label>
								<div class="col-sm-8">
									<input type="text" id="dateinput3" name="tahun_keluar" value="<?php echo trim($lb->tahun_keluar1); ?>" data-date-format="dd-mm-yyyy" class="form-control dateinput" required>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"><?php echo trim($lb->keterangan); ?></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
									<input type="hidden" id="inputby" name="no_urut"  value="<?php echo trim($lb->no_urut);?>" class="form-control" readonly>
								</div>
							</div>

						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<?php } ?>

<!--end PELATIHAN OR PENDIDIKAN NF-->


<!-- MODAL PENDIDIKAN FORMAL ---->
<div class="modal fade pendidikan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Input Riwayat Pendidikan Formal <?php echo $nik.'|'.$list_lk['nmlengkap'];?></h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/add_riwayat_pendidikan')?>" method="post">
      <div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Pendidikan</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kdpendidikan" id="kdpendidikan">
									  <?php foreach($list_pendidikan as $listkan){?>
									  <option value="<?php echo trim($listkan->kdpendidikan);?>" ><?php echo $listkan->nmpendidikan;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Sekolah</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nmsekolah"  class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Kota/Kab</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="kotakab"  class="form-control" style="text-transform:uppercase" maxlength="30" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jurusan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="jurusan"  class="form-control" style="text-transform:uppercase" maxlength="30" >
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">Program Studi</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="program_studi"  class="form-control" style="text-transform:uppercase" maxlength="30" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tahun Masuk</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="tahun_masuk"  data-inputmask='"mask": "9999"' data-mask="" class="form-control" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tahun keluar</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="tahun_keluar"  data-inputmask='"mask": "9999"' data-mask="" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nilai/IPK</label>
								<div class="col-sm-8">
									<input type="number" step="0.01" id="kddept" name="nilai"  class="form-control" placeholder="0" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
								</div>
							</div>

						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>



<!--MODEL EDIT RIWAYAT PENDIDIKAN FORMAL-->
<?php foreach ($list_riwayat_pendidikan as $lb){?>
<div class="modal fade" id="<?php echo trim($lb->no_urut); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Riwayat Pendidikan Formal</h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/edit_riwayat_pendidikan')?>" method="post">
<div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Pendidikan</label>
								<div class="col-sm-8">
									<input type="text" id="nik" name="kdpendidikan"  value="<?php echo $lb->nmpendidikan; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Sekolah</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nmsekolah" value="<?php echo trim($lb->nmsekolah); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Kota/Kab</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="kotakab"  class="form-control" value="<?php echo trim($lb->kotakab); ?>" style="text-transform:uppercase" maxlength="30" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jurusan</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="jurusan"  value="<?php echo trim($lb->jurusan); ?>" class="form-control" style="text-transform:uppercase" maxlength="30" >
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">Program Studi</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="program_studi" value="<?php echo trim($lb->program_studi); ?>" class="form-control" style="text-transform:uppercase" maxlength="30" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tahun Masuk</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="tahun_masuk"  value="<?php echo trim($lb->tahun_masuk); ?>" data-inputmask='"mask": "9999"' data-mask="" class="form-control" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tahun keluar</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="tahun_keluar" value="<?php echo trim($lb->tahun_keluar); ?>" data-inputmask='"mask": "9999"' data-mask="" class="form-control" >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nilai/IPK</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nilai" value="<?php echo $lb->nilai; ?>" class="form-control" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"><?php echo trim($lb->keterangan); ?></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
									<input type="hidden" id="inputby" name="no_urut"  value="<?php echo trim($lb->no_urut);?>" class="form-control" readonly>
								</div>
							</div>

						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<?php } ?>

<!-- END PENDIDIKAN FORMAL MODAL -->




	<!--Ganti Gambar-->
	<div class="modal fade gantigambar" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Ubah Foto</h4>
				</div>
					<div class="row">
						<div class="col-md-12">
							<div class="box box-primary">
                                <!-- form start -->
                                <form class="form-horizontal" action="<?php echo site_url('trans/karyawan/up_foto');?>" method="post" enctype="multipart/form-data">
                                    <div class="box-body">
										<div class="col-md-12">
											<div class="form-group">
												<img src="<?php if ($lp['image']<>'') { echo base_url('assets/img/profile/'.$lp['image']);} else { echo base_url('assets/img/user.png');} ;?>" width="100%" height="100%" alt="User Image" >
											</div>
											<div class="form-group">
												<label for="exampleInputFile">File input</label>
												<input type="hidden" value="<?php echo $lp['nik'];?>" name="nik">
												<input type="file" id="exampleInputFile" name="gambar">
												<p class="help-block">Upload file jpg.</p>
												<button onclick="return confirm('Ubah Foto ini?')" type="submit" class="btn btn-primary">Simpan</button>
											</div>
										</div>
                                    </div><!-- /.box-body -->
                                </form>
                            </div>
						</div>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

				</div>
			</div>
		  </div>
		</div>
	<!--end Pelatihan-->

<!--Modal untuk Input Nama Bpjs-->
<div class="modal fade" id="inputbpjs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Input Bpjs</h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/add_bpjs')?>" method="post">
      <div class="modal-body">
		<div class="row">
			<div class="col-sm-12">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<script type="text/javascript" charset="utf-8">
							  $(function() {
								$("#kodekomponen").chained("#kode_bpjs");
								$("#cjabt").chained("#csubdept");
								$("#kode_bpjs").selectize();
								$("#kodekomponen").selectize();
								$("#kodefaskes").selectize();
								$("#kodefaskes2").selectize();
								$("#tgl_bpjs").datepicker();
								$("#tgl_bpjs2").datepicker();

							  });
							</script>
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">

									 <input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>

								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Bpjs</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kode_bpjs" id="kode_bpjs">
									  <?php foreach($list_bpjs as $listkan){?>
									  <option value="<?php echo trim($listkan->kode_bpjs)?>" ><?php echo $listkan->kode_bpjs.'|'.$listkan->nama_bpjs;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Komponen Bpjs</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kodekomponen" id="kodekomponen">
									<option value="" ><?php echo '--PILIH PILIH KODE KOMPONEN BPJS NAKER--';?></option>
									  <?php foreach($list_bpjskomponen as $listkan){?>
									  	<option value="<?php echo trim($listkan->kodekomponen);?>" class="<?php echo trim($listkan->kode_bpjs);?>"><?php echo $listkan->kodekomponen.'|'.$listkan->namakomponen;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Id Bpjs</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="id_bpjs" class="form-control" style="text-transform:uppercase" maxlength="15" required>

								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Faskes Utama</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kodefaskes" id="kodefaskes">
									<option value="" ><?php echo '--PILIH FASKES UTAMA--';?></option>
									  <?php foreach($list_faskes as $listkan){?>
									  <option value="<?php echo trim($listkan->kodefaskes);?>" ><?php echo $listkan->kodefaskes.'|'.$listkan->namafaskes;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Faskes Tambahan</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kodefaskes2" id="kodefaskes2">
									<option value="" ><?php echo '--PILIH FASKES KEDUA--';?></option>
									  <?php foreach($list_faskes as $listkan){?>
									  <option value="<?php echo trim($listkan->kodefaskes);?>" ><?php echo $listkan->kodefaskes.'|'.$listkan->namafaskes;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kelas</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kelas" id="kelas">
									<option value="" ><?php echo '--PILIH KAMAR KELAS--';?></option>
									  <?php foreach($list_kelas as $listkan){?>
									  <option value="<?php echo trim($listkan->kdtrx);?>" ><?php echo $listkan->uraian;?></option>
									  <?php }?>
									</select>

								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tanggal Mulai Berlaku</label>
								<div class="col-sm-8">
									<input type="text" id="tgl_bpjs" name="tgl_berlaku"   data-date-format="dd-mm-yyyy" class="form-control" required>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div><!--row-->
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>

<!--Modal untuk Edit Bpjs Karyawan-->
<?php foreach ($list_bpjskaryawan as $lb){?>
<div class="modal fade" id="<?php echo trim($lb->id_bpjs); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Bpjs Karyawan</h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/edit_bpjs')?>" method="post">
      <div class="modal-body">
		<div class="row">
			<div class="col-sm-12">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="nik"  value="<?php echo trim($lb->nik);?>" class="form-control" style="text-transform:uppercase" maxlength="15" readonly>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Kode Bpjs</label>
								<div class="col-sm-8">
									<input type="text" id="nmdept" name="kode_bpjs" value="<?php echo $lb->kode_bpjs;?>"  style="text-transform:uppercase" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Komponen Bpjs</label>
								<div class="col-sm-8">
									<input type="text" id="nmdept" name="kodekomponen" value="<?php echo $lb->kodekomponen;?>"  style="text-transform:uppercase" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Id Bpjs</label>
								<div class="col-sm-8">
									<input type="text" id="kddept" name="id_bpjs"  value="<?php echo $lb->id_bpjs;?>" class="form-control" style="text-transform:uppercase" maxlength="15" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Faskes Utama</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kodefaskes" id="kodefaskes">
									 <option value="" ><?php echo '--PILIH KODE FASKES UTAMA--';?></option>
									  <?php foreach($list_faskes as $listkan){?>
									   <option <?php if (trim($lb->kodefaskes)==trim($listkan->kodefaskes)) { echo 'selected';} ?> value="<?php echo trim($listkan->kodefaskes);?>" ><?php echo $listkan->kodefaskes.'|'.$listkan->namafaskes;?></option>
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Faskes Tambahan</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kodefaskes2" id="kodefaskes2">
									 <option value="" ><?php echo '--PILIH FASKES KEDUA--';?></option>
									  <?php foreach($list_faskes as $listkan){?>
									   <option <?php if (trim($lb->kodefaskes2)==trim($listkan->kodefaskes)) { echo 'selected';} ?> value="<?php echo trim($listkan->kodefaskes);?>" ><?php echo $listkan->kodefaskes.'|'.$listkan->namafaskes;?></option>
									  <?php }?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Kelas</label>
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kelas" id="kelas">
									<option value="" ><?php echo '--PILIH KAMAR KELAS--';?></option>
									 <?php foreach($list_kelas as $listkan){?>
									 		  <option <?php if (trim($lb->kelas)==trim($listkan->kdtrx)) {echo 'selected';}?> value="<?php echo trim($listkan->kdtrx);?>" ><?php echo $listkan->uraian;?></option>
									  <?php }?>
									</select>

								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tanggal Mulai Berlaku</label>
								<div class="col-sm-8">
									<input type="text" id="tgl_bpjs2" name="tgl_berlaku"  value="<?php echo $lb->tgl_berlaku1;?>" data-date-format="dd-mm-yyyy" class="form-control">
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>
								<div class="col-sm-8">
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"><?php echo $lb->keterangan;?></textarea>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div><!--row-->
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<?php } ?>



</div>

<script>
	//Date picker
    $('#tanggal').datepicker();
    $('#tglmutasi').datepicker();
    $('#tglmemo').datepicker();
    $('#example1').dataTable();
    $('#example2').dataTable();
    $('#example3').dataTable();
    $('#example4').dataTable();
    $('#example5').dataTable();
    $('#example6').dataTable();
    $('#example7').dataTable();
    $('#example8').dataTable();
    $('#example9').dataTable();
    $('#examplex').dataTable();
	$("#listkary1").selectize();
					$("#tglsk").datepicker();
				$("#tglefektif").datepicker();
					$("#datemulai").datepicker();
				$("#dateselesai").datepicker();
				$("#datemulai2").datepicker();
				$("#dateselesai2").datepicker();


	<?php
	foreach ($list_mutasi as $emut){
		echo "$('#emut".trim($emut->nip).trim($emut->nomor)."').datepicker();";
	}
	foreach ($list_keluarga as $kelu){
		echo "$('#kelu".trim($kelu->nir).trim($kelu->nomor)."').datepicker();";
	}
	?>
    $('#inputkeluarga').datepicker();
	$('#masuk').datepicker();
	$('#stskrjmulai').datepicker();
	$('#stskrjakhir').datepicker();
	$('.dateinput').datepicker();
	$('#tglm').datepicker();
	$('#tglpelatihan').datepicker();
	<?php foreach ($list_pelatihan as $lipel){?>
	$('#tglpelatihan<?php echo trim($lipel->kdpelatihan);?>').datepicker();
	<?php }?>
	$('#keluar').datepicker();
	///$('#berlaku').daterangepicker();
	$("[data-mask]").inputmask();

</script>
