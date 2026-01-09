create OR REPLACE function sc_trx.tr_transready_uangmkn() returns trigger
    language plpgsql
as
$$
DECLARE
    -- Update By ARBI : 27/10/2021
    -- Penyesuaian Uang Makan Lembur & Level Jabatan B
    -- Update By Andika: 23/02/2023
    -- Penyesuaian uang makan untuk ijin pulang awal, ijin keluar, ijin datang terlambat
    -- Update By RKM: 16/08/2023
    -- Penyesuaian untuk multi ijin dalam sehari
    -- Update By RKM: 23/08/2023
    vr_um_max01   TIME WITHOUT TIME ZONE;
    vr_um_max02   TIME WITHOUT TIME ZONE;
    vr_um_min01   TIME WITHOUT TIME ZONE;
    vr_um_min02   TIME WITHOUT TIME ZONE;
    vr_date_now   DATE;
    vr_dok_lembur CHARACTER(25);
BEGIN
    DELETE FROM sc_trx.uangmakan WHERE nik = new.nik AND tgl = new.tgl;
    vr_date_now := CAST(TO_CHAR(NOW(), 'YYYY-MM-DD') AS DATE);

    IF (TRIM(new.kdregu) = 'NC') THEN
        vr_um_max01 := value2 FROM sc_mst.option WHERE kdoption = 'HRUMMAX01' AND group_option = 'NC'; --'13:00:00'
        vr_um_max02 := value2 FROM sc_mst.option WHERE kdoption = 'HRUMMAX02' AND group_option = 'NC'; --'11:00:00'
        vr_um_min01 := value2 FROM sc_mst.option WHERE kdoption = 'HRUMMIN01' AND group_option = 'NC'; --'07:55:00'
        vr_um_min02 := value2 FROM sc_mst.option WHERE kdoption = 'HRUMMIN02' AND group_option = 'NC'; --'07:55:00'
    ELSE
        vr_um_max01 := value2 FROM sc_mst.option WHERE kdoption = 'HRUMMAX01' AND group_option = 'DEFAULT'; --'13:00:00'
        vr_um_max02 := value2 FROM sc_mst.option WHERE kdoption = 'HRUMMAX02' AND group_option = 'DEFAULT'; --'11:00:00'
        vr_um_min01 := value2 FROM sc_mst.option WHERE kdoption = 'HRUMMIN01' AND group_option = 'DEFAULT'; --'07:55:00'
        vr_um_min02 := value2 FROM sc_mst.option WHERE kdoption = 'HRUMMIN02' AND group_option = 'DEFAULT'; --'07:55:00'
    END IF;

    INSERT INTO sc_trx.uangmakan (
        SELECT
            ta.branch,
            ta.nik,
            ta.tgl,
            ta.checkin,
            ta.checkout,
            CASE
                WHEN kdcabang <> 'SMGDMK' AND checkin IS NULL AND checkout IS NULL AND
                     td.nodok IS NULL AND tc.nodok IS NULL AND tf.tgl_libur IS NULL AND
                     th.nodok IS NULL THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin IS NULL AND checkout IS NULL AND
                     td.nodok IS NULL AND tf.tgl_libur IS NOT NULL AND th.nodok IS NULL
                    THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND
                     (checkin IS NOT NULL OR checkout IS NOT NULL) AND
                     lvl_jabatan = 'A' AND tf.tgl_libur IS NULL AND td.nodok IS NULL AND
                     th.nodok IS NULL AND (tc.nodok IS NULL OR
                                           (tc.nodok IS NOT NULL AND tc.kdijin_absensi IN ('DT', 'PA', 'IK')))
                    THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND td.nodok IS NOT NULL THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND th.nodok IS NOT NULL THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout > jam_pulang AND te.nodok IS NULL AND tf.tgl_libur IS NULL
                    THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout > jam_pulang AND te.nodok IS NULL AND
                     tf.tgl_libur IS NOT NULL THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout > jam_pulang AND te.nodok IS NOT NULL THEN besaran + besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout < jam_pulang AND
                     (tk.kdijin_absensi IS NULL AND ti.kdijin_absensi IS NULL) THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND
                     (tk.kdijin_absensi IS NULL AND tc.kdijin_absensi IS NULL) AND
                     te.nodok IS NULL THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND
                     (checkin < jam_masuk OR checkin >= jam_masuk) AND
                     (checkout > jam_pulang OR checkout <= jam_pulang) AND
                     tc.kdijin_absensi IS NOT NULL AND
                     (tc.tgl_jam_mulai = jam_masuk AND tc.tgl_jam_selesai >= jam_pulang)
                    THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND tg.kdijin_absensi IS NOT NULL AND
                     te.nodok IS NULL THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND tk.kdijin_absensi IS NOT NULL AND
                     te.nodok IS NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND
                     (tc.kdijin_absensi IS NULL OR tk.kdijin_absensi IS NULL) AND
                     te.nodok IS NOT NULL THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND tc.kdijin_absensi IS NOT NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin IS NULL AND checkout IS NULL AND
                     tc.kdijin_absensi IS NOT NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin IS NULL AND checkout > jam_pulang AND
                     (tk.kdijin_absensi IS NULL) THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin IS NULL AND checkout > jam_pulang AND
                     (tk.kdijin_absensi IS NOT NULL) THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND checkout IS NULL AND
                     tc.kdijin_absensi IS NULL THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND tc.tgl_jam_selesai < jam_pulang AND
                     (tc.kdijin_absensi IS NULL OR ti.kdijin_absensi IS NULL) THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND tc.tgl_jam_selesai >= jam_pulang AND
                     tc.kdijin_absensi IS NOT NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND
                     (tg.kdijin_absensi IS NOT NULL OR ti.kdijin_absensi IS NOT NULL)
                    THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND
                     (tg.kdijin_absensi IS NOT NULL AND ti.kdijin_absensi IS NOT NULL)
                    THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND
                     (tj.kdijin_absensi IS NOT NULL AND ti.kdijin_absensi IS NOT NULL)
                    THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND ti.kdijin_absensi IS NOT NULL AND
                     tg.kdijin_absensi IS NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout > jam_pulang AND tc.kdijin_absensi IS NOT NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND tj.kdijin_absensi IS NOT NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND ti.kdijin_absensi IS NOT NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND ti.kdijin_absensi IS NULL THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND tg.kdijin_absensi IS NOT NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     (checkout IS NULL OR checkout < jam_pulang) AND
                     tc.kdijin_absensi = 'IK' THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin >= jam_masuk AND
                     (checkout IS NULL OR checkout < jam_pulang) AND
                     tc.kdijin_absensi = 'IK' THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < jam_masuk AND
                     (checkout IS NOT NULL AND checkout < jam_pulang) AND
                     ti.kdijin_absensi = 'PA' THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND
                     (checkin IS NOT NULL OR checkout IS NOT NULL) AND lvl_jabatan = 'A'
                    THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < vr_um_min01 AND
                     checkout >= vr_um_max02 AND TO_CHAR(tgl, 'DY') = 'SAT' THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < vr_um_min01 AND
                     checkout >= vr_um_max01 AND TO_CHAR(tgl, 'DY') <> 'SAT' AND
                     td.nodok IS NULL AND tc.kdijin_absensi IS NULL THEN besaran
                WHEN kdcabang <> 'SMGDMK' AND checkin < vr_um_min01 AND
                     checkout >= vr_um_max01 AND TO_CHAR(tgl, 'DY') <> 'SAT' AND
                     LEFT(td.nodok, 2) = 'DL' THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin < vr_um_min01 AND
                     checkout >= vr_um_max02 AND TO_CHAR(tgl, 'DY') = 'SAT' AND
                     LEFT(td.nodok, 2) = 'DL' THEN NULL
                WHEN kdcabang <> 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout < jam_pulang AND tk.kdijin_absensi IS NOT NULL AND
                     ti.kdijin_absensi IS NOT NULL THEN besaran
                --tambahan
                WHEN kdcabang <> 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout >= jam_pulang AND tk.kdijin_absensi IS NOT NULL AND
                     ti.kdijin_absensi IS NOT NULL THEN besaran
                /*SEMARANG DEMAK*/
                WHEN kdcabang = 'SMGDMK' AND checkin IS NULL AND checkout IS NULL AND
                     td.nodok IS NULL AND tc.nodok IS NULL AND tf.tgl_libur IS NULL AND
                     th.nodok IS NULL THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin IS NULL AND checkout IS NULL AND
                     td.nodok IS NULL AND tf.tgl_libur IS NOT NULL AND th.nodok IS NULL
                    THEN NULL
                WHEN kdcabang = 'SMGDMK' AND
                     (checkin IS NOT NULL OR checkout IS NOT NULL) AND
                     lvl_jabatan = 'A' AND tf.tgl_libur IS NULL AND td.nodok IS NULL AND
                     th.nodok IS NULL AND (tc.nodok IS NULL OR
                                           (tc.nodok IS NOT NULL AND tc.kdijin_absensi IN ('DT', 'PA', 'IK')))
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND td.nodok IS NOT NULL THEN NULL
                WHEN kdcabang = 'SMGDMK' AND th.nodok IS NOT NULL THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout > jam_pulang AND te.nodok IS NULL AND tf.tgl_libur IS NULL
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout > jam_pulang AND te.nodok IS NULL AND
                     tf.tgl_libur IS NOT NULL THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout > jam_pulang AND te.nodok IS NOT NULL
                    THEN (besaran - kantin) + besaran
                WHEN kdcabang = 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout < jam_pulang AND
                     (tk.kdijin_absensi IS NULL AND ti.kdijin_absensi IS NULL) THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND
                     (tk.kdijin_absensi IS NULL AND tc.kdijin_absensi IS NULL) AND
                     te.nodok IS NULL THEN NULL
                WHEN kdcabang = 'SMGDMK' AND
                     (checkin < jam_masuk OR checkin >= jam_masuk) AND
                     (checkout > jam_pulang OR checkout <= jam_pulang) AND
                     tc.kdijin_absensi IS NOT NULL AND
                     (tc.tgl_jam_mulai = jam_masuk AND tc.tgl_jam_selesai >= jam_pulang)
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND tg.kdijin_absensi IS NOT NULL AND
                     te.nodok IS NULL THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND tk.kdijin_absensi IS NOT NULL AND
                     te.nodok IS NULL THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND
                     (tc.kdijin_absensi IS NULL OR tk.kdijin_absensi IS NULL) AND
                     te.nodok IS NOT NULL THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout > jam_pulang AND tc.kdijin_absensi IS NOT NULL
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin IS NULL AND checkout IS NULL AND
                     tc.kdijin_absensi IS NOT NULL THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin IS NULL AND checkout > jam_pulang AND
                     (tk.kdijin_absensi IS NULL) THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin IS NULL AND checkout > jam_pulang AND
                     (tk.kdijin_absensi IS NOT NULL) THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND checkout IS NULL AND
                     tc.kdijin_absensi IS NULL THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND tc.tgl_jam_selesai < jam_pulang AND
                     (tc.kdijin_absensi IS NULL OR ti.kdijin_absensi IS NULL) THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND tc.tgl_jam_selesai >= jam_pulang AND
                     tc.kdijin_absensi IS NOT NULL THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND
                     (tg.kdijin_absensi IS NOT NULL OR ti.kdijin_absensi IS NOT NULL)
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND
                     (tg.kdijin_absensi IS NOT NULL AND ti.kdijin_absensi IS NOT NULL)
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND
                     (tj.kdijin_absensi IS NOT NULL AND ti.kdijin_absensi IS NOT NULL)
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND ti.kdijin_absensi IS NOT NULL AND
                     tg.kdijin_absensi IS NULL THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout > jam_pulang AND tc.kdijin_absensi IS NOT NULL
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND tj.kdijin_absensi IS NOT NULL
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND ti.kdijin_absensi IS NOT NULL
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND ti.kdijin_absensi IS NULL THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     checkout < jam_pulang AND tg.kdijin_absensi IS NOT NULL
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     (checkout IS NULL OR checkout < jam_pulang) AND
                     tc.kdijin_absensi = 'IK' THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin >= jam_masuk AND
                     (checkout IS NULL OR checkout < jam_pulang) AND
                     tc.kdijin_absensi = 'IK' THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < jam_masuk AND
                     (checkout IS NOT NULL AND checkout < jam_pulang) AND
                     ti.kdijin_absensi = 'PA' THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND
                     (checkin IS NOT NULL OR checkout IS NOT NULL) AND lvl_jabatan = 'A'
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < vr_um_min01 AND
                     checkout >= vr_um_max02 AND TO_CHAR(tgl, 'DY') = 'SAT'
                    THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < vr_um_min01 AND
                     checkout >= vr_um_max01 AND TO_CHAR(tgl, 'DY') <> 'SAT' AND
                     td.nodok IS NULL AND tc.kdijin_absensi IS NULL THEN besaran - kantin
                WHEN kdcabang = 'SMGDMK' AND checkin < vr_um_min01 AND
                     checkout >= vr_um_max01 AND TO_CHAR(tgl, 'DY') <> 'SAT' AND
                     LEFT(td.nodok, 2) = 'DL' THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin < vr_um_min01 AND
                     checkout >= vr_um_max02 AND TO_CHAR(tgl, 'DY') = 'SAT' AND
                     LEFT(td.nodok, 2) = 'DL' THEN NULL
                WHEN kdcabang = 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout < jam_pulang AND tk.kdijin_absensi IS NOT NULL AND
                     ti.kdijin_absensi IS NOT NULL THEN besaran - kantin
                --tambahan
                WHEN kdcabang = 'SMGDMK' AND checkin >= jam_masuk AND
                     checkout >= jam_pulang AND tk.kdijin_absensi IS NOT NULL AND
                     ti.kdijin_absensi IS NOT NULL THEN besaran - kantin
                END     AS nominal,
            CASE
                WHEN checkin >= jam_masuk AND checkout > jam_pulang AND
                     tg.nodok is NOT null AND tg.kdijin_absensi = 'DT' AND
                     tg.type_ijin = 'PB' THEN 'IZIN DT :' || tg.nodok || '|| APP TGL: ' ||
                                              TO_CHAR(tc.approval_date, 'YYYY-MM-DD')
                WHEN checkin < jam_masuk AND checkout > jam_pulang AND
                     tg.nodok is NOT null AND tg.kdijin_absensi = 'PA' AND
                     tg.type_ijin = 'PB' THEN 'IZIN PA :' || tg.nodok || '|| APP TGL: ' ||
                                              TO_CHAR(tg.approval_date, 'YYYY-MM-DD')
                WHEN checkin IS NULL AND checkout IS NULL AND td.nodok IS NULL AND
                     tc.nodok IS NULL AND tf.tgl_libur IS NULL AND th.nodok IS NULL AND tg.nodok IS NOT NULL
                    THEN 'TIDAK MASUK KANTOR + IZIN '||tg.kdijin_absensi||' : '|| tg.nodok ||' || APP TGL: '||TO_CHAR(tg.approval_date, 'YYYY-MM-DD')
                WHEN checkin IS NULL AND checkout IS NULL AND td.nodok IS NULL AND
                     tc.nodok IS NULL AND tf.tgl_libur IS NULL AND th.nodok IS NULL
                    THEN 'TIDAK MASUK KANTOR'
                WHEN checkin IS NULL AND checkout IS NULL AND td.nodok IS NULL AND
                     tf.tgl_libur IS NOT NULL AND th.nodok IS NULL THEN tf.ket_libur
                WHEN td.nodok IS NOT NULL THEN
                    'DINAS DENGAN NO DINAS :' || td.nodok || '|| APP TGL: ' ||
                    TO_CHAR(td.approval_date, 'YYYY-MM-DD')
                WHEN th.nodok IS NOT NULL THEN
                    'CUTI DENGAN NO CUTI :' || th.nodok || '|| APP TGL: ' ||
                    TO_CHAR(th.approval_date, 'YYYY-MM-DD')
                --EDIT PENYESUAIAN IJIN
                WHEN checkin < jam_masuk AND checkout > jam_pulang AND tg.nodok is null AND
                     tc.kdijin_absensi = 'IK'AND tc.type_ijin ='DN' THEN
                    'IJIN KELUAR DINAS NO :' || tc.nodok || '|| APP TGL: ' ||
                    TO_CHAR(tc.approval_date, 'YYYY-MM-DD')
                WHEN checkin < jam_masuk AND checkout > jam_pulang AND te.nodok IS NULL AND
                     tf.tgl_libur IS NULL THEN 'TEPAT WAKTU'
                WHEN checkin < jam_masuk AND checkout > jam_pulang AND te.nodok IS NULL AND
                     tf.tgl_libur IS NOT NULL THEN tf.ket_libur
                WHEN checkin < jam_masuk AND checkout > jam_pulang AND te.nodok IS NOT NULL
                    THEN 'TEPAT WAKTU + Lembur :' || te.nodok
                WHEN checkin >= jam_masuk AND checkout < jam_pulang AND
                     (tk.kdijin_absensi IS NULL AND ti.kdijin_absensi IS NULL)
                    THEN 'TELAT MASUK/PULANG AWAL'
                WHEN checkin >= jam_masuk AND checkout > jam_pulang AND
                     (tk.kdijin_absensi IS NULL AND tc.kdijin_absensi IS NULL) AND
                     te.nodok IS NULL THEN 'TELAT MASUK'
                WHEN (checkin < jam_masuk OR checkin >= jam_masuk) AND
                     (checkout > jam_pulang OR checkout <= jam_pulang) AND
                     tc.kdijin_absensi IS NOT NULL AND
                     (tc.tgl_jam_mulai = jam_masuk AND tc.tgl_jam_selesai >= jam_pulang)
                    THEN 'IJIN KELUAR DINAS 1 NO :' || tc.nodok || '|| APP TGL: ' ||
                         TO_CHAR(tc.approval_date, 'YYYY-MM-DD')
                WHEN checkin >= jam_masuk AND checkout > jam_pulang AND
                     tk.kdijin_absensi IS NOT NULL AND te.nodok IS NULL THEN
                    'IJIN DT DINAS NO :' || tk.nodok || '|| APP TGL: ' ||
                    TO_CHAR(tk.approval_date, 'YYYY-MM-DD')
                WHEN checkin >= jam_masuk AND checkout < jam_pulang AND
                     tk.kdijin_absensi IS NOT NULL AND ti.kdijin_absensi IS NOT NULL
                    THEN 'IJIN DT DINAS NO :' || tk.nodok || ' DAN IJIN PA NO :' || ti.nodok || ''
                WHEN checkin >= jam_masuk AND checkout > jam_pulang AND
                     (tc.kdijin_absensi IS NULL OR tk.kdijin_absensi IS NULL) AND
                     te.nodok IS NOT NULL THEN
                    'TELAT MASUK + Lembur :' || te.nodok || '|| APP TGL: ' ||
                    TO_CHAR(te.approval_date, 'YYYY-MM-DD')
                WHEN checkin >= jam_masuk AND checkout > jam_pulang AND
                     (tj.nodok is null OR tg.nodok is null) AND
                     tc.kdijin_absensi IS NOT NULL THEN
                    'IJIN KELUAR DINAS NO 2 :' || tc.nodok || '|| APP TGL: ' ||
                    TO_CHAR(tc.approval_date, 'YYYY-MM-DD')
                WHEN checkin >= jam_masuk AND checkout > jam_pulang AND tj.nodok is NOT null
                    THEN '!IJIN KELUAR DINAS NO :' || tj.nodok || '|| APP TGL: ' ||
                         TO_CHAR(tc.approval_date, 'YYYY-MM-DD')
                WHEN checkin IS NULL AND checkout IS NULL AND tc.kdijin_absensi IS NOT NULL
                    THEN 'IJIN KELUAR DINAS NO :' || tc.nodok || '|| APP TGL: ' ||
                         TO_CHAR(tc.approval_date, 'YYYY-MM-DD')
                WHEN checkin IS NULL AND checkout > jam_pulang AND (tk.kdijin_absensi IS NULL)
                    THEN 'TIDAK CEKLOG MASUK'
                WHEN checkin IS NULL AND checkout > jam_pulang AND
                     (tk.kdijin_absensi IS NOT NULL) THEN
                    'IJIN DT DINAS DGN NO :' || tk.nodok || '|| APP TGL: ' ||
                    TO_CHAR(tk.approval_date, 'YYYY-MM-DD')
                WHEN checkin < jam_masuk AND checkout IS NULL AND tc.kdijin_absensi IS NULL
                    THEN 'TIDAK CEKLOG PULANG'
                WHEN checkin < jam_masuk AND checkout < jam_pulang AND
                     (ti.kdijin_absensi IS NULL AND
                      (tj.kdijin_absensi IS NULL AND tg.kdijin_absensi IS NULL))
                    THEN 'PULANG AWAL'
                WHEN checkin < jam_masuk AND checkout < jam_pulang AND
                     tc.tgl_jam_selesai < jam_pulang AND tc.kdijin_absensi IS NULL
                    THEN 'PULANG AWAL'
                WHEN checkin < jam_masuk AND checkout < jam_pulang AND
                     tc.tgl_jam_selesai < jam_pulang AND
                     (tc.kdijin_absensi IS NULL OR ti.kdijin_absensi IS NULL)
                    THEN 'PULANG AWAL'
                WHEN checkin < jam_masuk AND checkout < jam_pulang AND
                     tc.tgl_jam_selesai >= jam_pulang AND tc.kdijin_absensi IS NOT NULL
                    THEN 'IJIN KELUAR DINAS NO :' || tc.nodok || '|| APP TGL: ' ||
                         TO_CHAR(tc.approval_date, 'YYYY-MM-DD')
                WHEN checkin < jam_masuk AND checkout < jam_pulang AND
                     tc.tgl_jam_selesai >= jam_pulang AND ti.kdijin_absensi IS NOT NULL
                    THEN 'IJIN PA NO :' || ti.nodok || '|| APP TGL: ' ||
                         TO_CHAR(ti.approval_date, 'YYYY-MM-DD')
                WHEN checkin < jam_masuk AND checkout < jam_pulang AND
                     (tg.kdijin_absensi IS NOT NULL AND ti.kdijin_absensi IS NOT NULL) THEN
                    'IJIN PA PRIBADI NO :' || tg.nodok || '|| APP TGL: ' ||
                    TO_CHAR(tg.approval_date, 'YYYY-MM-DD')
                WHEN checkin < jam_masuk AND checkout < jam_pulang AND
                     (ti.kdijin_absensi IS NOT NULL AND tj.kdijin_absensi IS NOT NULL) THEN
                    'IJIN PA DINAS NO :' || tj.nodok || '|| APP TGL: ' ||
                    TO_CHAR(tj.approval_date, 'YYYY-MM-DD')
                WHEN checkin < jam_masuk AND checkout > jam_pulang AND tg.nodok is null AND
                     tc.kdijin_absensi = 'IK' THEN
                    'IJIN KELUAR DINAS NO :' || tc.nodok || '|| APP TGL: ' ||
                    TO_CHAR(tc.approval_date, 'YYYY-MM-DD')
                WHEN checkin >= jam_masuk AND
                     (checkout IS NULL OR checkout < jam_pulang) AND
                     tc.kdijin_absensi = 'IK' THEN
                    'IJIN KELUAR DINAS NO :' || tc.nodok || '|| APP TGL: ' ||
                    TO_CHAR(tc.approval_date, 'YYYY-MM-DD')
                WHEN checkin < jam_masuk AND
                     (checkout IS NOT NULL AND checkout < jam_pulang) AND
                     ti.kdijin_absensi = 'PA' THEN
                    'IJIN PA NO :' || ti.nodok || '|| APP TGL: ' ||
                    TO_CHAR(ti.approval_date, 'YYYY-MM-DD')
                END     AS keterangan,
            vr_date_now AS tgl_dok,
            CASE
                WHEN tc.nodok IS NOT NULL AND tk.nodok IS NULL AND tg.nodok IS NULL
                    THEN tc.nodok
                WHEN tg.nodok IS NOT NULL AND tk.nodok IS NULL THEN tg.nodok
                WHEN tk.nodok IS NOT NULL THEN tk.nodok
                ELSE td.nodok
                END     AS nodok
        FROM (
                 SELECT
                     b.branch                        AS branch,
                     a.nik,
                     b.nmlengkap,
                     c.kddept,
                     c.nmdept,
                     e.kdjabatan,
                     e.nmjabatan,
                     a.tgl,
                     CASE
                         WHEN a.jam_masuk_absen = a.jam_pulang_absen AND
                              a.jam_masuk_absen > vr_um_max01 THEN NULL
                         ELSE a.jam_masuk_absen END  AS checkin,
                     CASE
                         WHEN a.jam_masuk_absen = a.jam_pulang_absen AND
                              a.jam_pulang_absen <= vr_um_max01 THEN NULL
                         ELSE a.jam_pulang_absen END AS checkout,
                     NULL                            AS nominal,
                     'TRIAL'                              AS keterangan,
                     b.kdcabang,
                     b.lvl_jabatan,
                     a.jam_masuk,
                     a.jam_pulang,
                     CASE
                         WHEN bb.kdregu = 'SL' THEN 0
                         ELSE f.besaran
                         END AS kantin,
                     b.idbu
                 FROM sc_trx.transready a
                          LEFT OUTER JOIN sc_mst.karyawan b ON a.nik = b.nik
                          LEFT OUTER JOIN sc_mst.regu_opr bb ON a.nik = bb.nik
                          LEFT OUTER JOIN sc_mst.departmen c ON b.bag_dept = c.kddept
                          LEFT OUTER JOIN sc_mst.subdepartmen d ON b.subbag_dept = d.kdsubdept AND b.bag_dept = d.kddept
                          LEFT OUTER JOIN sc_mst.jabatan e ON b.jabatan = e.kdjabatan AND b.subbag_dept = e.kdsubdept AND b.bag_dept = e.kddept
                          LEFT OUTER JOIN sc_mst.kantin f ON b.kdcabang = f.kdcabang
             ) AS ta
                 LEFT OUTER JOIN sc_mst.uangmakan tb ON tb.kdlvl = ta.lvl_jabatan AND tb.branch = case when ta.idbu NOT IN ('A','B') THEN 'SBYNSA' ELSE 'NJRBJM' END
                 LEFT OUTER JOIN (
            select a.*
            from sc_trx.ijin_karyawan a
                     left outer join sc_trx.dtljadwalkerja b on a.nik = b.nik and a.tgl_kerja = b.tgl
                     left outer join sc_mst.jam_kerja c on b.kdjamkerja = c.kdjam_kerja
            where TRUE
              AND a.kdijin_absensi = 'IK'
              and a.type_ijin = 'DN'
              and status = 'P'
        ) as tc ON tc.nik = ta.nik AND tc.tgl_kerja = ta.tgl
                 LEFT OUTER JOIN (
            select a.*
            from sc_trx.ijin_karyawan a
                     left outer join sc_trx.dtljadwalkerja b on a.nik = b.nik and a.tgl_kerja = b.tgl
                     left outer join sc_mst.jam_kerja c on b.kdjamkerja = c.kdjam_kerja
            where a.kdijin_absensi = 'PA'
              and status = 'P'
              and a.tgl_jam_selesai < c.jam_pulang
        ) as ti ON ti.nik = ta.nik AND ti.tgl_kerja = ta.tgl
                 LEFT OUTER JOIN (
            select a.*
            from sc_trx.ijin_karyawan a
                     left outer join sc_trx.dtljadwalkerja b on a.nik = b.nik and a.tgl_kerja = b.tgl
                     left outer join sc_mst.jam_kerja c on b.kdjamkerja = c.kdjam_kerja
            where a.kdijin_absensi = 'DT'
              and a.type_ijin = 'DN'
              and status = 'P'
              and a.tgl_jam_mulai >= c.jam_masuk
        ) as tk ON tk.nik = ta.nik AND tk.tgl_kerja = ta.tgl
                 LEFT OUTER JOIN sc_trx.dinas td ON td.nik = ta.nik AND (ta.tgl BETWEEN td.tgl_mulai AND td.tgl_selesai) AND td.status = 'P'
                 LEFT OUTER JOIN sc_trx.lembur te
                                 ON te.nik = ta.nik AND te.tgl_kerja = ta.tgl AND
                                    TO_CHAR(ta.checkout, 'HH24:MI:SS') >= '18:00:00' AND
                                    TO_CHAR(te.tgl_jam_selesai, 'HH24:MI:SS') >=
                                    '18:00:00' AND (te.status = 'P' OR te.status = 'F') AND
                                    te.kdlembur = 'BIASA' AND
                                    TO_CHAR(te.tgl_jam_mulai, 'HH24:MI:SS') >=
                                    '13:00:00' /*LEMBUR BIASA*/
                 LEFT OUTER JOIN sc_mst.libur tf ON tf.tgl_libur = ta.tgl
                 LEFT OUTER JOIN sc_trx.ijin_karyawan tg
                                 ON tg.nik = ta.nik AND tg.tgl_kerja = ta.tgl AND
                                    tg.status = 'P' AND tg.type_ijin = 'PB' AND
                                    tg.kdijin_absensi = (select kode
                                                         FROM (SELECT tg1.kdijin_absensi AS kode,
                                                                      CASE
                                                                          WHEN tg1.kdijin_absensi = 'DT'
                                                                              THEN 1
                                                                          WHEN tg1.kdijin_absensi = 'PA'
                                                                              THEN 1
                                                                          ELSE 2
                                                                          END            AS sort
                                                               FROM sc_trx.ijin_karyawan tg1
                                                               WHERE tg1.nik = new.nik
                                                                 AND tg1.tgl_kerja = new.tgl
                                                                 AND tg1.status = 'P'
                                                                 AND tg1.type_ijin = 'PB') a
                                                         ORDER BY a.sort ASC
                                                         LIMIT 1)
                 LEFT OUTER JOIN sc_trx.ijin_karyawan tj
                                 ON tj.nik = ta.nik AND tj.tgl_kerja = ta.tgl AND
                                    tj.status = 'P' AND tj.type_ijin = 'DN' AND
                                    tj.kdijin_absensi = (select kode
                                                         FROM (SELECT tc1.kdijin_absensi AS kode,
                                                                      CASE
                                                                          WHEN tc1.kdijin_absensi = 'DT'
                                                                              THEN 1
                                                                          WHEN tc1.kdijin_absensi = 'PA'
                                                                              THEN 1
                                                                          ELSE 2
                                                                          END            AS sort
                                                               FROM sc_trx.ijin_karyawan tc1
                                                               WHERE tc1.nik = new.nik
                                                                 AND tc1.tgl_kerja = new.tgl
                                                                 AND tc1.status = 'P'
                                                                 AND tc1.type_ijin = 'DN') a
                                                         ORDER BY a.sort ASC
                                                         LIMIT 1)
                 LEFT OUTER JOIN sc_trx.cuti_karyawan th ON th.nik = ta.nik AND
                                                            (ta.tgl BETWEEN th.tgl_mulai AND th.tgl_selesai) AND
                                                            th.status = 'P'
        WHERE ta.lvl_jabatan <> 'A'
          AND ta.nik = new.nik
          AND ta.tgl = new.tgl
        GROUP BY ta.nik, ta.nmlengkap, ta.tgl, ta.checkin, ta.checkout, ta.kdcabang,
                 ta.jam_masuk, ta.jam_pulang, tb.besaran, ta.lvl_jabatan, ta.kantin,
                 tc.kdijin_absensi, tc.type_ijin,
                 tg.kdijin_absensi, tg.nodok, tg.tgl_jam_selesai, tg.type_ijin, tc.tgl_kerja,
                 td.nodok, td.approval_date, tc.nodok, tc.approval_date, te.nodok,
                 tf.tgl_libur,
                 tg.kdijin_absensi, tg.nodok, tg.tgl_jam_selesai, tg.type_ijin, tc.tgl_kerja,
                 td.nodok, td.approval_date, tc.nodok, tc.approval_date, te.nodok,
                 tf.tgl_libur,
                 tf.ket_libur, th.nodok, th.approval_date, ti.kdijin_absensi, ti.nodok,
                 ti.tgl_jam_selesai, ti.approval_date, tk.kdijin_absensi, tk.nodok,
                 tk.approval_date,
                 tg.approval_date, te.approval_date, tc.tgl_jam_selesai, tj.kdijin_absensi,
                 tj.nodok, tj.approval_date, tc.tgl_jam_mulai,ta.branch
        ORDER BY nmlengkap
    ) ON CONFLICT (nik,tgl) DO UPDATE
        SET
            branch = excluded.branch,
            checkin = excluded.checkin,
            checkout = excluded.checkout,
            nominal = excluded.nominal,
            keterangan = excluded.keterangan,
            tgl_dok = excluded.tgl_dok,
            nodok = excluded.nodok;

    RETURN new;
END;
$$;
