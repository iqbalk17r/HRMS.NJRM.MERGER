create OR REPLACE function sc_tmp.pr_hitung_rekap_um_njrm(vr_kdcabang character, vr_tglawal date, vr_tglakhir date) returns SETOF void
    language plpgsql
as
$$
    --author by : Fiky Ashariza 02-02-2017
--update by : RKM 21-06-2024 :: penyamaan dengan metode nusa dan penambahan kolom(bbm,sewakendaran,rencanacallplan,realisaicallplan)
--update by : RKM 04-01-2025 :: perubahan ketetntuan uang bbm => realisasi callplan > 0
DECLARE vr_tglapproval date;
    DECLARE vr_tgl_act date;
    DECLARE vr_tgl_dok date;
    DECLARE vr_nodok_ref character(20);
    DECLARE vr_nominal_um numeric;
    DECLARE vr_cek_nominal_um numeric;
    DECLARE vr_nik character(15);

--DECLARE vr_out integer;


BEGIN
    --select * from sc_mst.kantin
    --select * from sc_mst.uangmakan_njrm
    --select * from sc_mst.karyawan where nmlengkap like '%TINO%'
    FOR vr_nik in select trim(nik) from sc_mst.karyawan where tglkeluarkerja is null and kdcabang=vr_kdcabang
        LOOP
            vr_nominal_um:=case
                               when a.kdcabang='SMGDMK' then b.besaran-c.besaran
                               else b.besaran
                               end as nominal from sc_mst.karyawan a left outer join
                                                   sc_mst.uangmakan_njrm b on a.lvl_jabatan=b.kdlvl left outer join
                                                   sc_mst.kantin c on a.kdcabang=c.kdcabang
                           where a.nik=vr_nik;

            for vr_nodok_ref in select trim(dok_ref) from sc_trx.uangmakan
                                where nik=vr_nik and dok_ref is not null and (left(dok_ref,2)='IK' or left(dok_ref,2)='PA' or left(dok_ref,2)='DT') and
                                    (to_char(tgl,'yyyy-mm-dd') between to_char(vr_tglawal - interval'1 week','yyyy-mm-dd') and to_char(vr_tglakhir,'yyyy-mm-dd'))
                loop
                    select cast(to_char(approval_date,'yyyy-mm-dd')as date) into vr_tglapproval from sc_trx.ijin_karyawan where nodok=vr_nodok_ref;
                    select nominal into vr_cek_nominal_um from sc_trx.uangmakan where dok_ref=vr_nodok_ref and nik=vr_nik;

                    IF (vr_cek_nominal_um=0 or vr_cek_nominal_um is null) THEN
                        update sc_trx.uangmakan set nominal=vr_nominal_um,keterangan='+ PERSETUJUAN NO IJIN : '||vr_nodok_ref where tgl=vr_tglapproval and nik=vr_nik;
                    END IF;

                    return next vr_nodok_ref;
                end loop;

            return next vr_nik;
        end loop;

    UPDATE sc_trx.uangmakan AS a
    SET
        rencanacallplan = x.rencanacallplan,
        realisasicallplan = x.realisasicallplan,
        nominal = x.nominal,
        keterangan = x.keterangan,
        bbm = CASE
                  WHEN x.tbbm = 'T' THEN
                      CASE
                          WHEN x.checkin IS NOT NULL THEN
                              CASE
                                  WHEN callplan = 't' THEN
                                      CASE
                                          WHEN x.rencanacallplan > 0 THEN
                                              CASE
                                                  WHEN x.realisasicallplan > 0 THEN
                                                      CASE
                                                          WHEN nodok IS NOT NULL AND type_ijin = 'DN' AND kendaraan = 'PRIBADI' THEN y.nominal
                                                          WHEN x.keterangan NOT SIMILAR TO '(DINAS|CUTI)%' THEN y.nominal
                                                          ELSE 0
                                                          END
                                                  ELSE 0
                                                  END
                                          ELSE
                                              0
                                          END
                                  ELSE
                                      CASE
                                          WHEN nodok IS NOT NULL AND type_ijin = 'DN' AND kendaraan = 'PRIBADI' THEN y.nominal
                                          WHEN x.keterangan NOT SIMILAR TO '(DINAS|CUTI)%' THEN y.nominal
                                          ELSE 0
                                          END
                                  END
                          ELSE 0
                          END
                  ELSE 0
            END,
        sewa_kendaraan = CASE
                             WHEN x.tsewa = 'T' THEN
                                 case
                                     when x.checkin is not null THEN
                                         CASE
                                             WHEN nodok is not null AND type_ijin = 'DN' AND kendaraan = 'PRIBADI' THEN z.nominal
                                             WHEN x.checkin is not null AND x.keterangan NOT SIMILAR TO '(DINAS|CUTI)%' THEN z.nominal
                                             ELSE 0
                                             END
                                     else 0
                                     END
                             ELSE 0
            END
    FROM (
             SELECT
                 a.nik,
                 a.tgl,
                 d.jumlah AS rencanacallplan,
                 e.jumlah AS realisasicallplan,
                 a.checkin,
                 a.checkout,
                 EXTRACT(HOUR FROM (a.checkout - a.checkin)) AS worktime,
                 j.total AS nominal,
                 CASE
                     WHEN b.callplan = 't' THEN
                         CASE
                             WHEN (a.dok_ref IS NOT NULL AND h.kdijin_absensi = 'IK' AND h.type_ijin = 'DN' AND h.status = 'P' AND a.keterangan SIMILAR TO 'TEPAT WAKTU' AND e.jumlah < d.jumlah AND d.jumlah > 0 ) THEN j.keterangan || ' + IJIN DENGAN NO. '||a.dok_ref || ' + CALLPLAN TIDAK TERPENUHI'
                             WHEN (a.dok_ref IS NOT NULL AND h.kdijin_absensi = 'IK' AND h.type_ijin = 'DN' AND h.status = 'P' AND h.tgl_jam_mulai <= f.jam_masuk AND h.tgl_jam_selesai >= jam_pulang)
                                 OR (a.dok_ref IS NOT NULL AND h.kdijin_absensi NOT IN ('IK', 'DT', 'PA'))
                                 OR (a.checkin IS NULL AND a.checkout IS NULL) OR a.keterangan SIMILAR TO '(DINAS|CUTI)%' THEN SPLIT_PART(j.keterangan, ' + ', 1)
                             WHEN d.jumlah = 0 AND b.callplan = 't' THEN SPLIT_PART(j.keterangan, ' + ', 1) || ' + TIDAK ADA RENCANA CALLPLAN'
                             WHEN d.jumlah = 0 THEN SPLIT_PART(j.keterangan, ' + ', 1)
                             WHEN e.jumlah >= d.jumlah THEN SPLIT_PART(j.keterangan, ' + ', 1) || ' + CALLPLAN TERPENUHI'
                             ELSE SPLIT_PART(j.keterangan, ' + ', 1) || ' + CALLPLAN TIDAK TERPENUHI'
                             END
                     ELSE
                         CASE
                             WHEN (a.dok_ref IS NOT NULL AND h.kdijin_absensi = 'IK' AND h.type_ijin = 'DN' AND h.status = 'P' AND a.keterangan SIMILAR TO 'TEPAT WAKTU' AND e.jumlah < d.jumlah AND d.jumlah > 0 ) THEN j.keterangan || ' + IJIN DENGAN NO. '||a.dok_ref
                             WHEN (a.dok_ref IS NOT NULL AND h.kdijin_absensi = 'IK' AND h.type_ijin = 'DN' AND h.status = 'P' AND h.tgl_jam_mulai <= f.jam_masuk AND h.tgl_jam_selesai >= jam_pulang)
                                 OR (a.dok_ref IS NOT NULL AND h.kdijin_absensi NOT IN ('IK', 'DT', 'PA'))
                                 OR (a.checkin IS NULL AND a.checkout IS NULL) OR a.keterangan SIMILAR TO '(DINAS|CUTI)%' THEN SPLIT_PART(j.keterangan, ' + ', 1)
                             WHEN d.jumlah = 0 AND b.callplan = 't' THEN SPLIT_PART(j.keterangan, ' + ', 1)
                             WHEN d.jumlah = 0 THEN SPLIT_PART(j.keterangan, ' + ', 1)
                             WHEN e.jumlah >= d.jumlah THEN SPLIT_PART(j.keterangan, ' + ', 1)
                             ELSE SPLIT_PART(j.keterangan, ' + ', 1)
                             END
                     END AS keterangan,
                 h.nodok,
                 h.type_ijin,
                 h.kendaraan,
                 COALESCE(TRIM(i.sewakendaraan),'') AS tsewa,
                 COALESCE(TRIM(i.bbm),'') AS tbbm,
                 jam_masuk,
                 jam_pulang,
                 jam_pulang_min,
                 jam_masuk_max,
                 c.kdregu,
                 b.callplan
             FROM sc_trx.uangmakan a
                      LEFT OUTER JOIN sc_mst.karyawan b ON b.nik = a.nik AND b.tglkeluarkerja IS NULL AND b.kdcabang = vr_kdcabang
                      left OUTER JOIN sc_mst.jabatan i ON b.bag_dept = i.kddept AND b.subbag_dept = i.kdsubdept AND b.jabatan = i.kdjabatan
                      LEFT OUTER JOIN sc_mst.regu_opr c ON c.nik = b.nik
                      LEFT JOIN LATERAL (
                 SELECT COUNT(x.custcode) AS jumlah
                 FROM (
                          SELECT COALESCE(NULLIF(TRIM(xa.locationid), ''), NULLIF(TRIM(xa.locationidlocal), '')) AS custcode
                          FROM sc_tmp.scheduletolocation xa
                          WHERE xa.nik = a.nik AND xa.scheduledate = a.tgl
                          GROUP BY 1
                      ) x
                 ) d ON TRUE
                      LEFT JOIN LATERAL (
                 SELECT COUNT(x.custcode) AS jumlah
                 FROM (
                          SELECT COALESCE(NULLIF(TRIM(xa.customeroutletcode), ''), NULLIF(TRIM(xa.customercodelocal), '')) AS custcode
                          FROM sc_tmp.checkinout xa
                          WHERE xa.checktime::DATE = a.tgl
                            AND xa.nik = a.nik
                            --AND xa.customertype = 'C'
                            AND COALESCE(NULLIF(TRIM(xa.customeroutletcode), ''), NULLIF(TRIM(xa.customercodelocal), '')) IN (
                              SELECT COALESCE(NULLIF(TRIM(xa.locationid), ''), NULLIF(TRIM(xa.locationidlocal), '')) AS custcode
                              FROM sc_tmp.scheduletolocation xa
                              WHERE xa.nik = a.nik AND xa.scheduledate = a.tgl
                              GROUP BY 1
                          )
                          GROUP BY 1
                      ) x
                 ) e ON TRUE
                      LEFT JOIN LATERAL (
                 SELECT jam_masuk, jam_pulang, jam_masuk_max, jam_pulang_min
                 FROM sc_mst.jam_kerja
                 WHERE kdjam_kerja = CASE
                                         WHEN EXTRACT(DOW FROM a.tgl) IN (1, 2, 3, 4) AND c.kdregu = 'SL' THEN 'SL1'
                                         WHEN EXTRACT(DOW FROM a.tgl) IN (5) AND c.kdregu = 'SL' THEN 'SL2'
                                         WHEN EXTRACT(DOW FROM a.tgl) IN (6) AND c.kdregu = 'SL' THEN 'SL3'
                                         WHEN EXTRACT(DOW FROM a.tgl) IN (1, 2, 3, 4) AND c.kdregu = 'NC' THEN 'NC1'
                                         WHEN EXTRACT(DOW FROM a.tgl) IN (5) AND c.kdregu = 'NC' THEN 'NC2'
                                         WHEN EXTRACT(DOW FROM a.tgl) IN (6) AND c.kdregu = 'NC' THEN 'NC3'
                                         WHEN EXTRACT(DOW FROM a.tgl) IN (1, 2, 3, 4) THEN 'NSA'
                                         WHEN EXTRACT(DOW FROM a.tgl) IN (5) THEN 'NSB'
                                         WHEN EXTRACT(DOW FROM a.tgl) IN (6) THEN 'NSC'
                     END
                 ) f ON TRUE
                      LEFT JOIN LATERAL (
                 SELECT
                     CASE
                         WHEN b.kdcabang = 'SMGDMK' THEN
                             CASE
                                 WHEN (c.kdregu = 'SL' OR b.callplan = 't') THEN xa.besaran
                                 ELSE xa.besaran - xb.besaran
                                 END
                         ELSE xa.besaran
                         END AS nominal
                 FROM sc_mst.uangmakan_njrm xa
                          LEFT JOIN sc_mst.kantin xb ON xb.kdcabang = b.kdcabang
                 WHERE xa.kdlvl = b.lvl_jabatan
                 ) g ON TRUE
                      LEFT JOIN sc_trx.ijin_karyawan h ON h.nodok = a.dok_ref AND h.status = 'P'
                      LEFT JOIN sc_trx.master_um j ON a.nik = j.nik AND a.tgl = j.tgl
             WHERE a.tgl::DATE BETWEEN vr_tglawal AND vr_tglakhir
             ORDER BY b.kdcabang, b.nmlengkap, a.tgl
         ) AS x
             LEFT JOIN LATERAL (
        SELECT value3 as nominal from sc_mst.option where kdoption = 'UB'
        ) y ON TRUE
             LEFT JOIN LATERAL (
        SELECT value3 as nominal from sc_mst.option where kdoption = 'USK'
        ) z ON TRUE
    WHERE a.nik = x.nik AND a.tgl = x.tgl;


END
$$;
