<script type="text/javascript">
    $(document).ready(function() {
        $("#tglahir").datepicker();
        $("#tgldikeluarkan").datepicker();
        $("#tglberlaku").datepicker();
        $("#tglnpwp").datepicker();
        $("#tglmasukkerja").datepicker();
    });

    var firstLoad = true;
</script>

<ol class="breadcrumb">
    <div class="pull-right"><i style="color:transparent;"><?= $t ?></i> Versi: <?= $version ?></div>
    <?php foreach($y as $y1): ?>
        <?php if(trim($y1->kodemenu) != trim($kodemenu)): ?>
            <li><a href="<?= site_url(trim($y1->linkmenu)) ?>"><i class="fa <?= trim($y1->iconmenu) ?>"></i> <?= trim($y1->namamenu) ?></a></li>
        <?php else: ?>
            <li class="active"><i class="fa <?= trim($y1->iconmenu) ?>"></i> <?= trim($y1->namamenu) ?></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ol>
<?= $message ?>
</br>

<div class="row">
    <form action="<?= site_url('trans/karyawan/ajax_update') ?>" method='post'>
        <div class="col-sm-12">
            <div class="form-horizontal">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab">Profile Karyawan</a></li>
                        <li><a href="#tab_2" data-toggle="tab">Fisik</a></li>
                        <li><a href="#tab_3" data-toggle="tab">Data ID</a></li>
                        <li><a href="#tab_4" data-toggle="tab">Alamat</a></li>
                        <li><a href="#tab_5" data-toggle="tab">Kontak</a></li>
                        <li><a href="#tab_6" data-toggle="tab">Jabatan Pekerjaan</a></li>
                        <li><a href="#tab_7" data-toggle="tab">Salary</a></li>
                        <li><a href="#tab_8" data-toggle="tab">Absensi</a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="col-sm-12 ">
                            <div class="col-sm-12">
                                <h3> Step 1</h3>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">NIK Karyawan</label>
                                    <div class="col-sm-6">
                                        <input name="nik" id="nik" style="text-transform: uppercase;" value="<?= trim($dtl['nik']) ?>" placeholder="Nomor Induk Karyawan" class="form-control" type="text" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Nama Lengkap Karyawan</label>
                                    <div class="col-sm-6">
                                        <input name="nmlengkap" style="text-transform: uppercase;" value="<?= trim($dtl['nmlengkap']) ?>" placeholder="Nama Lengkap" class="form-control" type="text" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Nama Panggilan</label>
                                    <div class="col-sm-6">
                                        <input name="callname" style="text-transform: uppercase;" placeholder="Nama Panggilan" value="<?= trim($dtl['callname']) ?>" class="form-control" type="text" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Jenis Kelamin</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="jk" name="jk" placeholder="--- JENIS KELAMIN ---" required>
                                            <option value="" class=""></option>
                                            <option value="L">LAKI-LAKI</option>
                                            <option value="P">PEREMPUAN</option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#jk').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#jk").addClass("selectize-hidden-accessible");
                                                $('#jk')[0].selectize.setValue("<?= trim($dtl['jk']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Tanggal Lahir</label>
                                    <div class="col-sm-6">
                                        <input id="tglahir"  name="tgllahir" value="<?= date("d-m-Y", strtotime(trim($dtl['tgllahir']))) ?>" style="text-transform: uppercase;" placeholder="Tanggal Lahir" data-date-format="dd-mm-yyyy" class="form-control tgl" type="text" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Tempat Lahir (Negara)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="neglahir" name="neglahir" placeholder="--- NEGARA ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_neg as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kodenegara'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#neglahir').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodenegara',
                                                    labelField: 'namanegara',
                                                    searchField: ['namanegara'],
                                                    sortField: 'namanegara',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#provlahir')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_provinsi",
                                                        type: "post",
                                                        data: {
                                                            kodenegara: $('#neglahir').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#provlahir')[0].selectize.addOption({
                                                                    kodeprov: data[i].kodeprov.trim(),
                                                                    namaprov: data[i].namaprov.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#provlahir')[0].selectize.setValue("<?= trim($dtl['provlahir']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#neglahir").addClass("selectize-hidden-accessible");
                                                $('#neglahir')[0].selectize.setValue("<?= trim($dtl['neglahir']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Tempat Lahir (Provinsi)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="provlahir" name="provlahir" placeholder="--- PROVINSI ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#provlahir').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodeprov',
                                                    labelField: 'namaprov',
                                                    searchField: ['namaprov'],
                                                    sortField: 'namaprov',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#kotalahir')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_kotakab",
                                                        type: "post",
                                                        data: {
                                                            kodeprov: $('#provlahir').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#kotalahir')[0].selectize.addOption({
                                                                    kodekotakab: data[i].kodekotakab.trim(),
                                                                    namakotakab: data[i].namakotakab.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#kotalahir')[0].selectize.setValue("<?= trim($dtl['kotalahir']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#provlahir").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Tempat Lahir (Kota/Kabupaten)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="kotalahir" name="kotalahir" placeholder="--- KOTA ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kotalahir').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodekotakab',
                                                    labelField: 'namakotakab',
                                                    searchField: ['namakotakab'],
                                                    sortField: 'namakotakab',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#kotalahir").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Agama</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="kd_agama" name="kd_agama" placeholder="--- AGAMA ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_agama as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kdagama'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kd_agama').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdagama',
                                                    labelField: 'nmagama',
                                                    searchField: ['nmagama'],
                                                    sortField: 'nmagama',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#kd_agama").addClass("selectize-hidden-accessible");
                                                $('#kd_agama')[0].selectize.setValue("<?= trim($dtl['kd_agama']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_2">
                        <div class="col-sm-12 ">
                            <div class="col-sm-12">
                                <h3> Step 2</h3>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Keadaan Fisik</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="stsfisik" name="stsfisik" placeholder="--- KEADAAN FISIK ---" required>
                                            <option value="" class=""></option>
                                            <option value="t">BAIK & SEHAT</option>
                                            <option value="f">CACAT FISIK</option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#stsfisik').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    var stsfisik = $('#stsfisik').val();
                                                    $('.ketfisik-label').hide();
                                                    $("#ketfisik").prop('required', false);
                                                    if (stsfisik == "f") {
                                                        $('.ketfisik-label').show();
                                                        $("#ketfisik").prop('required', true);
                                                    }
                                                });
                                                $("#stsfisik").addClass("selectize-hidden-accessible");
                                                $('#stsfisik')[0].selectize.setValue("<?= trim($dtl['stsfisik']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group ketfisik-label">
                                    <label class="control-label col-sm-3">Keterangan Jika Cacat</label>
                                    <div class="col-sm-6">
                                        <textarea id="ketfisik" name="ketfisik" style="text-transform: uppercase;" placeholder="Deskripsikan Cacat fisik" class="form-control"><?= trim($dtl['ketfisik']) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_3">
                        <div class="col-sm-12 ">
                            <div class="col-sm-12">
                                <h3> Step 3</h3>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">No KTP</label>
                                    <div class="col-sm-6">
                                        <input name="noktp" style="text-transform: uppercase;" placeholder="No Ktp" value="<?= trim($dtl['noktp']) ?>" class="form-control" type="text" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">KTP Dikeluaran Di</label>
                                    <div class="col-sm-6">
                                        <input name="ktpdikeluarkan" style="text-transform: uppercase;" value="<?= trim($dtl['ktpdikeluarkan']) ?>" placeholder="Kota KTP di keluarkan" class="form-control" type="text" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Tanggal KTP Dikeluaran</label>
                                    <div class="col-sm-6">
                                        <input id="tgldikeluarkan" name="tgldikeluarkan" style="text-transform: uppercase;" value="<?= date("d-m-Y", strtotime(trim($dtl['tgldikeluarkan'])))?>" placeholder="Tanggal KTP Di keluarkan" data-date-format="dd-mm-yyyy" class="form-control" type="text" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">KTP Seumur Hidup</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="ktp_seumurhdp" name="ktp_seumurhdp" placeholder="--- KTP SEUMUR HIDUP ---" required>
                                            <option value="" class=""></option>
                                            <option value="t">YA</option>
                                            <option value="f">TIDAK</option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#ktp_seumurhdp').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    var ktp_seumurhdp = $('#ktp_seumurhdp').val();
                                                    $('.tglberlaku-label').hide();
                                                    $("#tglberlaku").prop('required', false);
                                                    if (ktp_seumurhdp == "f") {
                                                        $('.tglberlaku-label').show();
                                                        $("#tglberlaku").prop('required', true);
                                                    }
                                                });
                                                $("#ktp_seumurhdp").addClass("selectize-hidden-accessible");
                                                $('#ktp_seumurhdp')[0].selectize.setValue("<?= trim($dtl['ktp_seumurhdp']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group tglberlaku-label">
                                    <label class="control-label col-sm-3">Tanggal Berlaku</label>
                                    <div class="col-sm-6">
                                        <input id="tglberlaku" name="tglberlaku" style="text-transform: uppercase;" value="<?= trim($dtl['tglktp1']) == "" ? null : date("d-m-Y", strtotime(trim($dtl['tglktp1'])))?>" placeholder="Tanggal Berlaku" data-date-format="dd-mm-yyyy" class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">No Kartu Keluarga (KK)</label>
                                    <div class="col-sm-6">
                                        <input name="nokk" style="text-transform: uppercase;" placeholder="NO KK" value="<?= trim($dtl['nokk']) ?>" class="form-control" type="text" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Kewarganegaraan</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="stswn" name="stswn" placeholder="--- KEWARGANEGARAAN ---" required>
                                            <option value="" class=""></option>
                                            <option value="T">WARGA NEGARA INDONESIA</option>
                                            <option value="F">WARGA NEGARA ASING</option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#stswn').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#stswn").addClass("selectize-hidden-accessible");
                                                $('#stswn')[0].selectize.setValue("<?= trim($dtl['stswn']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Status Pernikahan</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="status_pernikahan" name="status_pernikahan" placeholder="--- STATUS PERNIKAHAN ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_nikah as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kdnikah'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#status_pernikahan').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdnikah',
                                                    labelField: 'nmnikah',
                                                    searchField: ['nmnikah'],
                                                    sortField: 'nmnikah',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#status_pernikahan").addClass("selectize-hidden-accessible");
                                                $('#status_pernikahan')[0].selectize.setValue("<?= trim($dtl['status_pernikahan']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Golongan Darah</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="gol_darah" name="gol_darah" placeholder="--- GOLONGAN DARAH ---" required>
                                            <option value="" class=""></option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#gol_darah').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#gol_darah").addClass("selectize-hidden-accessible");
                                                $('#gol_darah')[0].selectize.setValue("<?= trim($dtl['gol_darah']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_4">
                        <div class="col-sm-12 ">
                            <div class="col-sm-12">
                                <h3> Step 4</h3>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Negara (Sesuai KTP)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="negktp" name="negktp" placeholder="--- NEGARA ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_neg as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kodenegara'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#negktp').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodenegara',
                                                    labelField: 'namanegara',
                                                    searchField: ['namanegara'],
                                                    sortField: 'namanegara',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#provktp')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_provinsi",
                                                        type: "post",
                                                        data: {
                                                            kodenegara: $('#negktp').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#provktp')[0].selectize.addOption({
                                                                    kodeprov: data[i].kodeprov.trim(),
                                                                    namaprov: data[i].namaprov.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#provktp')[0].selectize.setValue("<?= trim($dtl['provktp']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#negktp").addClass("selectize-hidden-accessible");
                                                $('#negktp')[0].selectize.setValue("<?= trim($dtl['negktp']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Provinsi (Sesuai KTP)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="provktp" name="provktp" placeholder="--- PROVINSI ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#provktp').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodeprov',
                                                    labelField: 'namaprov',
                                                    searchField: ['namaprov'],
                                                    sortField: 'namaprov',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#kotaktp')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_kotakab",
                                                        type: "post",
                                                        data: {
                                                            kodeprov: $('#provktp').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#kotaktp')[0].selectize.addOption({
                                                                    kodekotakab: data[i].kodekotakab.trim(),
                                                                    namakotakab: data[i].namakotakab.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#kotaktp')[0].selectize.setValue("<?= trim($dtl['kotaktp']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#provktp").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Kota/Kabupaten (Sesuai KTP)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="kotaktp" name="kotaktp" placeholder="--- KOTA ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kotaktp').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodekotakab',
                                                    labelField: 'namakotakab',
                                                    searchField: ['namakotakab'],
                                                    sortField: 'namakotakab',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#kecktp')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_kec",
                                                        type: "post",
                                                        data: {
                                                            kodekotakab: $('#kotaktp').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#kecktp')[0].selectize.addOption({
                                                                    kodekec: data[i].kodekec.trim(),
                                                                    namakec: data[i].namakec.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#kecktp')[0].selectize.setValue("<?= trim($dtl['kecktp']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#kotaktp").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Kecamatan (Sesuai KTP)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="kecktp" name="kecktp" placeholder="--- KECAMATAN ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kecktp').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodekec',
                                                    labelField: 'namakec',
                                                    searchField: ['namakec'],
                                                    sortField: 'namakec',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#kelktp')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_keldesa",
                                                        type: "post",
                                                        data: {
                                                            kodekec: $('#kecktp').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#kelktp')[0].selectize.addOption({
                                                                    kodekeldesa: data[i].kodekeldesa.trim(),
                                                                    namakeldesa: data[i].namakeldesa.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#kelktp')[0].selectize.setValue("<?= trim($dtl['kelktp']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#kecktp").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Kelurahan/Desa (Sesuai KTP)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="kelktp" name="kelktp" placeholder="--- KELURAHAN/DESA ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kelktp').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodekeldesa',
                                                    labelField: 'namakeldesa',
                                                    searchField: ['namakeldesa'],
                                                    sortField: 'namakeldesa',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#kelktp").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div  class="form-group"  >
                                    <label class="control-label col-sm-3">Alamat (Sesuai KTP)</label>
                                    <div class="col-sm-6">
                                        <textarea name="alamatktp" style="text-transform: uppercase;" placeholder="Alamat Sesuai Dengan KTP" class="form-control" required><?= trim($dtl['alamatktp']) ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Negara (Sesuai Tempat Tinggal)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="negtinggal" name="negtinggal" placeholder="--- NEGARA ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_neg as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kodenegara'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#negtinggal').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodenegara',
                                                    labelField: 'namanegara',
                                                    searchField: ['namanegara'],
                                                    sortField: 'namanegara',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#provtinggal')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_provinsi",
                                                        type: "post",
                                                        data: {
                                                            kodenegara: $('#negtinggal').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#provtinggal')[0].selectize.addOption({
                                                                    kodeprov: data[i].kodeprov.trim(),
                                                                    namaprov: data[i].namaprov.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#provtinggal')[0].selectize.setValue("<?= trim($dtl['provtinggal']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#negtinggal").addClass("selectize-hidden-accessible");
                                                $('#negtinggal')[0].selectize.setValue("<?= trim($dtl['negtinggal']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Provinsi (Sesuai Tempat Tinggal)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="provtinggal" name="provtinggal" placeholder="--- PROVINSI ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#provtinggal').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodeprov',
                                                    labelField: 'namaprov',
                                                    searchField: ['namaprov'],
                                                    sortField: 'namaprov',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#kotatinggal')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_kotakab",
                                                        type: "post",
                                                        data: {
                                                            kodeprov: $('#provtinggal').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#kotatinggal')[0].selectize.addOption({
                                                                    kodekotakab: data[i].kodekotakab.trim(),
                                                                    namakotakab: data[i].namakotakab.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#kotatinggal')[0].selectize.setValue("<?= trim($dtl['kotatinggal']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#provtinggal").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Kota/Kabupaten (Sesuai Tempat Tinggal)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="kotatinggal" name="kotatinggal" placeholder="--- KOTA ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kotatinggal').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodekotakab',
                                                    labelField: 'namakotakab',
                                                    searchField: ['namakotakab'],
                                                    sortField: 'namakotakab',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#kectinggal')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_kec",
                                                        type: "post",
                                                        data: {
                                                            kodekotakab: $('#kotatinggal').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#kectinggal')[0].selectize.addOption({
                                                                    kodekec: data[i].kodekec.trim(),
                                                                    namakec: data[i].namakec.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#kectinggal')[0].selectize.setValue("<?= trim($dtl['kectinggal']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#kotatinggal").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Kecamatan (Sesuai Tempat Tinggal)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="kectinggal" name="kectinggal" placeholder="--- KECAMATAN ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kectinggal').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodekec',
                                                    labelField: 'namakec',
                                                    searchField: ['namakec'],
                                                    sortField: 'namakec',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#keltinggal')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_keldesa",
                                                        type: "post",
                                                        data: {
                                                            kodekec: $('#kectinggal').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#keltinggal')[0].selectize.addOption({
                                                                    kodekeldesa: data[i].kodekeldesa.trim(),
                                                                    namakeldesa: data[i].namakeldesa.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#keltinggal')[0].selectize.setValue("<?= trim($dtl['keltinggal']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#kectinggal").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Kelurahan/Desa (Sesuai Tempat Tinggal)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="keltinggal" name="keltinggal" placeholder="--- KELURAHAN/DESA ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#keltinggal').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodekeldesa',
                                                    labelField: 'namakeldesa',
                                                    searchField: ['namakeldesa'],
                                                    sortField: 'namakeldesa',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#keltinggal").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div  class="form-group"  >
                                    <label class="control-label col-sm-3">Alamat (Sesuai Tempat Tinggal)</label>
                                    <div class="col-sm-6">
                                        <textarea name="alamattinggal" style="text-transform: uppercase;" placeholder="Alamat Sesuai Dengan KTP" class="form-control" required><?= trim($dtl['alamattinggal']) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_5">
                        <div class="col-sm-12 ">
                            <div class="col-sm-12">
                                <h3> Step 5</h3>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Nomor HP Utama</label>
                                    <div class="col-sm-6">
                                        <input name="nohp1" value="<?= trim($dtl['nohp1']) ?>" style="text-transform: uppercase;" placeholder="Nomor HP Utama" class="form-control" type="input" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Nomor HP Lainnya</label>
                                    <div class="col-sm-6">
                                        <input name="nohp2" value="<?= trim($dtl['nohp2']) ?>" style="text-transform: uppercase;" placeholder="Nomor HP Lainnya" class="form-control" type="input">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Email</label>
                                    <div class="col-sm-6">
                                        <input name="email" value="<?= trim($dtl['email']) ?>" style="text-transform: uppercase;" placeholder="Alamat email" class="form-control" type="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Email 2</label>
                                    <div class="col-sm-6">
                                        <input name="email2" value="<?= trim($dtl['email2']) ?>" style="text-transform: uppercase;" placeholder="Alamat email" class="form-control" type="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">NPWP</label>
                                    <div class="col-sm-6">
                                        <input name="npwp" value="<?= trim($dtl['npwp']) ?>" style="text-transform: uppercase;" placeholder="Nomor NPWP" class="form-control" type="input">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Tanggal NPWP</label>
                                    <div class="col-sm-6">
                                        <input id="tglnpwp" name="tglnpwp" style="text-transform: uppercase;" value="<?= trim($dtl['tglnpwp']) == "" ? null : date("d-m-Y", strtotime(trim($dtl['tglnpwp']))) ?>" data-date-format="dd-mm-yyyy" placeholder="Tanggal NPWP" class="form-control" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_6">
                        <div class="col-sm-12 ">
                            <div class="col-sm-12">
                                <h3> Step 6</h3>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Departemen</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="dept" name="dept" placeholder="--- DEPARTEMEN ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_dept as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kddept'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#dept').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kddept',
                                                    labelField: 'nmdept',
                                                    searchField: ['nmdept'],
                                                    sortField: 'nmdept',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#subbag_dept')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_subdept",
                                                        type: "post",
                                                        data: {
                                                            kddept: $('#dept').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#subbag_dept')[0].selectize.addOption({
                                                                    kdsubdept: data[i].kdsubdept.trim(),
                                                                    nmsubdept: data[i].nmsubdept.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#subbag_dept')[0].selectize.setValue("<?= trim($dtl['subbag_dept']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#dept").addClass("selectize-hidden-accessible");
                                                $('#dept')[0].selectize.setValue("<?= trim($dtl['bag_dept']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Sub Departemen</label>
                                    <div class="col-sm-6" >
                                        <select class="form-control" id="subbag_dept" name="subbag_dept" placeholder="--- SUB DEPARTEMEN ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#subbag_dept').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdsubdept',
                                                    labelField: 'nmsubdept',
                                                    searchField: ['nmsubdept'],
                                                    sortField: 'nmsubdept',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#jabatan')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_jabatan",
                                                        type: "post",
                                                        data: {
                                                            kddept: $('#dept').val(),
                                                            kdsubdept: $('#subbag_dept').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#jabatan')[0].selectize.addOption({
                                                                    kdjabatan: data[i].kdjabatan.trim(),
                                                                    nmjabatan: data[i].nmjabatan.trim()
                                                                });
                                                            }
                                                            if (firstLoad) {
                                                                $('#jabatan')[0].selectize.setValue("<?= trim($dtl['jabatan']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#subbag_dept").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Jabatan</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="jabatan" name="jabatan" placeholder="--- JABATAN ---" required>
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#jabatan').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdjabatan',
                                                    labelField: 'nmjabatan',
                                                    searchField: ['nmjabatan'],
                                                    sortField: 'nmjabatan',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#jabatan").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Job Grade</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="lvl_jabatan" name="lvl_jabatan" placeholder="--- JOB GRADE ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_lvljabt as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kdlvl'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#lvl_jabatan').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdlvl',
                                                    labelField: 'nmlvljabatan',
                                                    searchField: ['nmlvljabatan'],
                                                    sortField: 'nmlvljabatan',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                }).on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#grade_golongan')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_grade_golongan",
                                                        type: "post",
                                                        data: {
                                                            kdlvl: $('#lvl_jabatan').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#grade_golongan')[0].selectize.addOption({
                                                                    kdgrade: data[i].kdgrade.trim(),
                                                                    nmgrade: data[i].nmgrade.trim()
                                                                });
                                                            }
                                                            $("#grade_golongan").prop('required', false);
                                                            if(data.length > 0) {
                                                                $("#grade_golongan").prop('required', true);
                                                            }
                                                            if (firstLoad) {
                                                                $('#grade_golongan')[0].selectize.setValue("<?= trim($dtl['grade_golongan']) ?>");
                                                            }
                                                        }
                                                    });
                                                });
                                                $("#lvl_jabatan").addClass("selectize-hidden-accessible");
                                                $('#lvl_jabatan')[0].selectize.setValue("<?= trim($dtl['lvl_jabatan']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Level Grade</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="grade_golongan" name="grade_golongan" placeholder="--- LEVEL GRADE ---">
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#grade_golongan').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdgrade',
                                                    labelField: 'nmgrade',
                                                    searchField: ['nmgrade'],
                                                    sortField: 'nmgrade',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                })/*.on('change', function () {
                                                    if (!firstLoad) {
                                                        $('#kdlvlgp')[0].selectize.clearOptions();
                                                    }
                                                    $.ajax({
                                                        url: HOST_URL + "trans/karyawan/get_kdlvlgp",
                                                        type: "post",
                                                        data: {
                                                            kdgrade: $('#grade_golongan').val()
                                                        },
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            for (var i = 0; i < data.length; i++) {
                                                                $('#kdlvlgp')[0].selectize.addOption({
                                                                    kdlvlgp: data[i].kdlvlgp.trim()
                                                                });
                                                            }
                                                            $("#kdlvlgp").prop('required', false);
                                                            if(data.length > 0) {
                                                                $("#kdlvlgp").prop('required', true);
                                                            }
                                                            if (firstLoad) {
                                                                $('#kdlvlgp')[0].selectize.setValue("<?= trim($dtl['kdlvlgp']) ?>");
                                                            }
                                                        }
                                                    });
                                                });*/
                                                $("#grade_golongan").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div>
                            <!--   <div class="form-group">
                                    <label class="control-label col-sm-3">Golongan</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="kdlvlgp" name="kdlvlgp" placeholder="--- GOLONGAN ---">
                                            <option value="" class=""></option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kdlvlgp').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdlvlgp',
                                                    labelField: 'kdlvlgp',
                                                    searchField: ['kdlvlgp'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#kdlvlgp").addClass("selectize-hidden-accessible");
                                            });
                                        </script>
                                    </div>
                                </div> -->
                                <div class="form-group">
                                        <label class="control-label col-sm-3">idbu</label>
                                        <div class="col-sm-6">
                                            <select name="idbu" class="form-control input-sm" placeholder="---KETIK IDBU---" id="idbu">
                                                <!-- Tambahkan option default langsung dari PHP -->
                                                <option value="<?= $dtl['idbu'] ?>" selected><?=$dtl['idbuname'] ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <script>
                                    var idbuSelect = $('#idbu').selectize({
                                        plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                        valueField: 'idbu',
                                        labelField: 'idbuname',
                                        searchField: ['idbuname'],
                                        options: [], // Selectize akan tetap bisa menambah opsi dari AJAX
                                        create: false,
                                        render: {
                                            option: function(item, escape) {
                                                return `
                                                    <div class="row">
                                                        <div class="col-xs-3">${escape(item.idbu)}</div>
                                                        <div class="col-xs-9">${escape(item.idbuname)}</div>
                                                    </div>
                                                `;
                                            },
                                            item: function(item, escape) {
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
                                            }).done(function(res) {
                                                callback(res.group);
                                            }).fail(function() {
                                                callback();
                                            });
                                        }
                                    })[0].selectize;
                                    </script>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Atasan</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="nik_atasan" name="nik_atasan" placeholder="--- ATASAN ---">
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_atasan as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['nik'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#nik_atasan').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'nik',
                                                    searchField: ['nik', 'nmlengkap'],
                                                    sortField: 'nmlengkap',
                                                    options: [],
                                                    create: false,
                                                    initData: true,
                                                    render: {
                                                        option: function(item, escape) {
                                                            return '' +
                                                                '<div class=\'row\'>' +
                                                                    '<div class=\'col-md-2 text-nowrap\'>' + escape(item.nik) + '</div>' +
                                                                    '<div class=\'col-md-10 text-nowrap\'>' + escape(item.nmlengkap) + '</div>' +
                                                                '</div>' +
                                                            '';
                                                        },
                                                        item: function(item, escape) {
                                                            return '' +
                                                                '<div>' +
                                                                    escape(item.nik) + ' - ' +
                                                                    escape(item.nmlengkap) +
                                                                '</div>'
                                                            ;
                                                        }
                                                    }
                                                });
                                                $("#nik_atasan").addClass("selectize-hidden-accessible");
                                                $('#nik_atasan')[0].selectize.setValue("<?= trim($dtl['nik_atasan']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Atasan Ke-2</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="nik_atasan2" name="nik_atasan2" placeholder="--- ATASAN KE-2 ---">
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_atasan as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['nik'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#nik_atasan2').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'nik',
                                                    searchField: ['nik', 'nmlengkap'],
                                                    sortField: 'nmlengkap',
                                                    options: [],
                                                    create: false,
                                                    initData: true,
                                                    render: {
                                                        option: function(item, escape) {
                                                            return '' +
                                                                '<div class=\'row\'>' +
                                                                    '<div class=\'col-md-2 text-nowrap\'>' + escape(item.nik) + '</div>' +
                                                                    '<div class=\'col-md-10 text-nowrap\'>' + escape(item.nmlengkap) + '</div>' +
                                                                '</div>' +
                                                            '';
                                                        },
                                                        item: function(item, escape) {
                                                            return '' +
                                                                '<div>' +
                                                                    escape(item.nik) + ' - ' +
                                                                    escape(item.nmlengkap) +
                                                                '</div>'
                                                            ;
                                                        }
                                                    }
                                                });
                                                $("#nik_atasan2").addClass("selectize-hidden-accessible");
                                                $('#nik_atasan2')[0].selectize.setValue("<?= trim($dtl['nik_atasan2']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Kantor Wilayah</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="kdcabang" name="kdcabang" placeholder="--- KANTOR WILAYAH ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_kanwil as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kdcabang'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kdcabang').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdcabang',
                                                    searchField: ['kdcabang', 'desc_cabang'],
                                                    sortField: 'desc_cabang',
                                                    options: [],
                                                    create: false,
                                                    initData: true,
                                                    render: {
                                                        option: function(item, escape) {
                                                            return '' +
                                                                '<div class=\'row\'>' +
                                                                    '<div class=\'col-md-2 text-nowrap\'>' + escape(item.kdcabang) + '</div>' +
                                                                    '<div class=\'col-md-10 text-nowrap\'>' + escape(item.desc_cabang) + '</div>' +
                                                                '</div>' +
                                                            '';
                                                        },
                                                        item: function(item, escape) {
                                                            return '' +
                                                                '<div>' +
                                                                    escape(item.kdcabang) + ' - ' +
                                                                    escape(item.desc_cabang) +
                                                                '</div>'
                                                            ;
                                                        }
                                                    }
                                                });
                                                $("#kdcabang").addClass("selectize-hidden-accessible");
                                                $('#kdcabang')[0].selectize.setValue("<?= trim($dtl['kdcabang']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Tanggal Masuk</label>
                                    <div class="col-sm-6">
                                        <input id="tglmasukkerja" name="tglmasukkerja" value="<?= date("d-m-Y", strtotime(trim($dtl['tglmasukkerja']))) ?>" style="text-transform: uppercase;" placeholder="Tanggal Masuk Karyawan" data-date-format="dd-mm-yyyy" class="form-control tgl" type="text" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Branch ID</label>
                                    <div class="col-sm-6">
                                        <input name="branch" value="<?= trim($dtl['branch']) ?>" class="form-control" type="input" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_7">
                        <div class="col-sm-12 ">
                            <div class="col-sm-12">
                                <h3> Step 7</h3>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Group Penggajian</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="grouppenggajian" name="grouppenggajian" placeholder="--- GROUP PENGGAJIAN ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_grp_gaji as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kdgroup_pg'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#grouppenggajian').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdgroup_pg',
                                                    searchField: ['kdgroup_pg', 'nmgroup_pg'],
                                                    sortField: 'nmgroup_pg',
                                                    options: [],
                                                    create: false,
                                                    initData: true,
                                                    render: {
                                                        option: function(item, escape) {
                                                            return '' +
                                                                '<div class=\'row\'>' +
                                                                    '<div class=\'col-md-2 text-nowrap\'>' + escape(item.kdgroup_pg) + '</div>' +
                                                                    '<div class=\'col-md-10 text-nowrap\'>' + escape(item.nmgroup_pg) + '</div>' +
                                                                '</div>' +
                                                            '';
                                                        },
                                                        item: function(item, escape) {
                                                            return '' +
                                                                '<div>' +
                                                                    escape(item.kdgroup_pg) + ' - ' +
                                                                    escape(item.nmgroup_pg) +
                                                                '</div>'
                                                            ;
                                                        }
                                                    }
                                                });
                                                $("#grouppenggajian").addClass("selectize-hidden-accessible");
                                                $('#grouppenggajian')[0].selectize.setValue("<?= trim($dtl['grouppenggajian']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">PTKP</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="status_ptkp" name="status_ptkp" placeholder="--- PTKP ---">
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_ptkp as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <?php $row['besaranpertahunrp'] = "Rp " . number_format($row['besaranpertahun'], 2, ',', '.'); ?>
                                                <option value="<?= $row['kodeptkp'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#status_ptkp').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kodeptkp',
                                                    searchField: ['kodeptkp', 'besaranpertahun'],
                                                    sortField: 'kodeptkp',
                                                    options: [],
                                                    create: false,
                                                    initData: true,
                                                    render: {
                                                        option: function(item, escape) {
                                                            return '' +
                                                                '<div class=\'row\'>' +
                                                                    '<div class=\'col-md-2 text-nowrap\'>' + escape(item.kodeptkp) + '</div>' +
                                                                    '<div class=\'col-md-10 text-nowrap\'>' + escape(item.besaranpertahunrp) + '</div>' +
                                                                '</div>' +
                                                            '';
                                                        },
                                                        item: function(item, escape) {
                                                            return '' +
                                                                '<div>' +
                                                                    escape(item.kodeptkp) + ' - ' +
                                                                    escape(item.besaranpertahunrp) +
                                                                '</div>'
                                                            ;
                                                        }
                                                    }
                                                });
                                                $("#status_ptkp").addClass("selectize-hidden-accessible");
                                                $('#status_ptkp')[0].selectize.setValue("<?= trim($dtl['status_ptkp']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Nama Bank</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="namabank" name="namabank" placeholder="--- NAMA BANK ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_opt_bank as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kdbank'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#namabank').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdbank',
                                                    labelField: 'nmbank',
                                                    searchField: ['nmbank'],
                                                    sortField: 'nmbank',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#namabank").addClass("selectize-hidden-accessible");
                                                $('#namabank')[0].selectize.setValue("<?= trim($dtl['namabank']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Basis Gaji Wilayah</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="kdwilayahnominal" name="kdwilayahnominal" placeholder="--- BASIS GAJI WILAYAH ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_wilnom as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['kdwilayahnominal'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#kdwilayahnominal').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'kdwilayahnominal',
                                                    labelField: 'nmwilayahnominal',
                                                    searchField: ['nmwilayahnominal'],
                                                    sortField: 'nmwilayahnominal',
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#kdwilayahnominal").addClass("selectize-hidden-accessible");
                                                $('#kdwilayahnominal')[0].selectize.setValue("<?= trim($dtl['kdwilayahnominal']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Nama Pemilik Rekening</label>
                                    <div class="col-sm-6">
                                        <input name="namapemilikrekening" value="<?= trim($dtl['namapemilikrekening']) ?>" style="text-transform: uppercase;" placeholder="Nama Pemilik Rekening" class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Nomor Rekening</label>
                                    <div class="col-sm-6">
                                        <input name="norek" value="<?= trim($dtl['norek']) ?>" style="text-transform: uppercase;" placeholder="Nomor Rekening" class="form-control" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_8">
                        <div class="col-sm-12 ">
                            <div class="col-sm-12">
                                <h3> Step 8</h3>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">ID Absensi</label>
                                    <div class="col-sm-6">
                                        <input name="idabsen" style="text-transform: uppercase;" value="<?= trim($dtl['idabsen']) ?>" placeholder="ID Absensi" class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">ID Mesin</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="idmesin" name="idmesin" placeholder="--- ID MESIN ---" required>
                                            <option value="" class=""></option>
                                            <?php foreach($list_finger as $v): ?>
                                                <?php $row = array_map('trim', (array)$v); ?>
                                                <option value="<?= $row['fingerid'] ?>" data-data='<?= json_encode($row, JSON_HEX_APOS) ?>'></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#idmesin').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    valueField: 'fingerid',
                                                    searchField: ['wilayah', 'ipaddress'],
                                                    options: [],
                                                    create: false,
                                                    initData: true,
                                                    render: {
                                                        option: function(item, escape) {
                                                            return '' +
                                                                '<div class=\'row\'>' +
                                                                    '<div class=\'col-md-2 text-nowrap\'>' + escape(item.wilayah) + '</div>' +
                                                                    '<div class=\'col-md-10 text-nowrap\'>' + escape(item.ipaddress) + '</div>' +
                                                                '</div>' +
                                                            '';
                                                        },
                                                        item: function(item, escape) {
                                                            return '' +
                                                                '<div>' +
                                                                    escape(item.wilayah) + ' - ' +
                                                                    escape(item.ipaddress) +
                                                                '</div>'
                                                            ;
                                                        }
                                                    }
                                                });
                                                $("#idmesin").addClass("selectize-hidden-accessible");
                                                $('#idmesin')[0].selectize.setValue("<?= trim($dtl['idmesin']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Borong</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="borong" name="borong" placeholder="--- BORONG ---" required>
                                            <option value="" class=""></option>
                                            <option value="t">YA</option>
                                            <option value="f">TIDAK</option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#borong').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#borong").addClass("selectize-hidden-accessible");
                                                $('#borong')[0].selectize.setValue("<?= trim($dtl['tjborong']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Shift</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="shift" name="shift" placeholder="--- SHIFT ---" required>
                                            <option value="" class=""></option>
                                            <option value="t">YA</option>
                                            <option value="f">TIDAK</option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#shift').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#shift").addClass("selectize-hidden-accessible");
                                                $('#shift')[0].selectize.setValue("<?= trim($dtl['tjshift']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Lembur</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="lembur" name="lembur" placeholder="--- LEMBUR ---" required>
                                            <option value="" class=""></option>
                                            <option value="t">YA</option>
                                            <option value="f">TIDAK</option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#lembur').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#lembur").addClass("selectize-hidden-accessible");
                                                $('#lembur')[0].selectize.setValue("<?= trim($dtl['tjlembur']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Callplan</label>
                                    <div class="col-sm-6">
                                        <select class="form-control input-sm" id="callplan" name="callplan" placeholder="--- CALLPLAN ---" required>
                                            <option value="" class=""></option>
                                            <option value="t">YA</option>
                                            <option value="f">TIDAK</option>
                                        </select>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#callplan').selectize({
                                                    plugins: ['hide-arrow', 'selectable-placeholder'],
                                                    options: [],
                                                    create: false,
                                                    initData: true
                                                });
                                                $("#callplan").addClass("selectize-hidden-accessible");
                                                $('#callplan')[0].selectize.setValue("<?= trim($dtl['callplan']) ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Mobile Device ID</label>
                                    <div class="col-sm-6">
                                        <input name="deviceid" style="text-transform: uppercase;" value="<?= trim($dtl['deviceid']) ?>" placeholder="Mobile Device ID" class="form-control" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success pull-right" onclick="return confirm('Anda Yakin Ubah Data ini?')" style="margin: 5px;">Simpan Data</button>
            <a href="<?= site_url('trans/karyawan') ?>" class="btn btn-default pull-right" style="margin: 5px;">Kembali</a>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(function() {
            firstLoad = !firstLoad;
        }, 5000);
    });
</script>
