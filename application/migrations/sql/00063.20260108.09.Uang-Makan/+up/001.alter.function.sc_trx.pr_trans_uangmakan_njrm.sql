create OR REPLACE function sc_trx.pr_trans_uangmakan_njrm(vr_tglawal character, vr_tglakhir character,
                                                          vr_kdcabang character, vr_userid character) returns text
    language plpgsql
as
$$
DECLARE
/*
	AUTHOR FIKY ASHARIZA:
	CREATE DATE: 24/05/2017
	UPDATE DATE: 07/07/2017
	TITLE: PROSEDUR TARIKAN UANG MAKAN DARI TRANSREADY + KOMPONEN
	UPDATE DATE: 15/08/2019
	TITLE: PENAMBAHAN FORMAT 1 & FORMAT 2 PERUBAHAN PADA UANG KEHADIRAN
	UPDATE DATE: 17/03/2023
	TITLE: REVISI DINAS ADA CHECKIN TIDAK BOLEH DAPAT UM
	UPDATE BY: RKM
	UPDATE DATE: 21/06/2024
	TITLE: REVISI PENYAMAAN METODE DENGAN HRMS.NUSA (sc_trx.uangmakan tidak boleh dihapus)
*/
    vr_nik              char(12);
    vr_nikb             char(12);
    vr_branch           char(12);
    vr_badgenumber      char(12);
    vr_tgl_min_masuk    timestamp without time zone;
    vr_tgl_max_pulang   timestamp without time zone;
    vr_jam_masuk_absen  time without time zone;
    vr_jam_pulang_absen time without time zone;
    vr_kdjamkerja       char(12);
    vr_jam_masuk        time without time zone;
    vr_jam_pulang       time without time zone;
    vr_shiftke          char(12);
    vr_tgl              date;
    vr_um_max01         time without time zone;
    vr_um_max02         time without time zone;
    vr_um_min01         time without time zone;
    vr_um_min02         time without time zone;
    vr_lbrmin01         time without time zone;
    vr_lbrmin02         time without time zone;
    vr_lbrmin03         time without time zone;
    vr_ptg              numeric;
    vr_date_now         date;
    vr_dok_lembur       character(25);
    vr_jabatan          character(25);
    vr_komplembur       numeric(18);
    vr_intv_ist         numeric;
    vr_if_nol_potist    numeric;
    vr_if_nol_potmas    numeric;
    vr_if_nol_potpul    numeric;
    vr_valkomplembur    numeric;
    vr_valpotist        numeric;
    vr_valpotmas        numeric;
    vr_valpotpul        numeric;
    vr_ZLBRKMPDF        character varying(25);
    vr_jbshif           char(12); vr_jblembur char(12); vr_jbum char(12); vr_jbpot_um char(12); vr_jbpot_um_ist char(12); vr_jbpot_um_pul char(12);
    vr_flag varchar;

BEGIN

    vr_branch := coalesce(branch, '') from sc_mst.branch where coalesce(cdefault, '') = 'YES';
    vr_ZLBRKMPDF := coalesce(value1, '') from sc_mst.option where kdoption = 'ZLBRKMPDF';
    --SETTING UANG MAKAN DISINI GAN
    --vr_ZLBRKMPDF:=select coalesce(value1,'') from sc_mst.option where kdoption='ZLBRKMPDF'; --SETTING UANG MAKAN DISINI GAN
/*
'ZLBRKMP01','SETUP UANG MAKAN FORMAT 1',
'ZLBRKMP02','SETUP UANG MAKAN FORMAT 2',
*/
    IF (vr_ZLBRKMPDF = 'ZLBRKMP01' or vr_ZLBRKMPDF = '') THEN
        FOR vr_nik,vr_tgl,vr_flag IN

            select trim(a.nik)::text as nik, tgl, case when default_option is not null THEN 'F' END AS default_option
            from sc_trx.transready a
                     LEFT OUTER JOIN sc_mst.karyawan b on a.nik = b.nik
                     LEFT OUTER JOIN LATERAL (
                select
                    CASE WHEN b.idbu = any (string_to_array(value1,',')) THEN 'T' ELSE null END AS default_option
                FROM sc_mst.option opt where kdoption = 'B:U:E:D'
                ) o ON TRUE
            where (to_char(tgl, 'YYYY-MM-DD') between vr_tglawal and vr_tglakhir)
              and a.nik in (select nik from sc_mst.karyawan where kdcabang = vr_kdcabang)
            order by tgl asc

            LOOP
                raise notice 'nik : % start 0',vr_nik;
                raise notice 'tgl : %',vr_tgl;
                select
                    coalesce(shift,'F') as vr_jbshif,
                    coalesce(lembur,'F') as vr_jblembur,
                    coalesce(um,'F') as vr_jbum,
                    CASE WHEN vr_flag = 'F' THEN 'F' ELSE coalesce(pot_um,'F') END as vr_jbpot_um,
                    CASE WHEN vr_flag = 'F' THEN 'F' ELSE coalesce(pot_um_ist,'F') END as vr_jbpot_um_ist,
                    CASE WHEN vr_flag = 'F' THEN 'F' ELSE coalesce(pot_um_pul,'F') END as vr_jbpot_um_pul
                into vr_jbshif,vr_jblembur,vr_jbum,vr_jbpot_um,vr_jbpot_um_ist,vr_jbpot_um_pul
                from sc_mst.jabatan a
                         left outer join sc_mst.karyawan b on a.kdjabatan = b.jabatan and a.kddept = b.bag_dept and
                                                              a.kdsubdept = b.subbag_dept

                    where b.nik = vr_nik;
                raise notice 'uangmakan : %',vr_jbum;
                --delete from sc_trx.uangmakan where nik=vr_nik and tgl=vr_tgl;
                vr_date_now := cast(to_char(now(), 'yyyy-mm-dd') as date);
                vr_um_max01 := value2 from sc_mst.option where kdoption = 'HRUMMAX01';
                vr_um_max02 := value2 from sc_mst.option where kdoption = 'HRUMMAX02';
                vr_um_min01 := value2 from sc_mst.option where kdoption = 'HRUMMIN01';
                vr_um_min02 := value2 from sc_mst.option where kdoption = 'HRUMMIN02';
                vr_valkomplembur := value3 from sc_mst.option where kdoption = 'LBRPKT01';
                vr_valpotist := value3 from sc_mst.option where kdoption = 'POTIST';
                vr_valpotmas := value3 from sc_mst.option where kdoption = 'POTMAS';
                vr_valpotpul := value3 from sc_mst.option where kdoption = 'POTPUL';

                vr_ptg := value3 from sc_mst.option where kdoption = 'PTG1';
                vr_intv_ist := (extract(epoch from cast(jam_istirahatselesai - jam_istirahat as time)::interval) / 60)::numeric
                               from sc_mst.jam_kerja
                               where kdjam_kerja in (select trim(kdjamkerja)
                                                     from sc_trx.transready
                                                     where nik = vr_nik and tgl = vr_tgl);

                /* INSERT INTO KOMPENSASI LEMBUR TEMPORARY  */
                delete from sc_tmp.komplembur_um where nik = vr_nik and nodok = vr_userid;
                if (vr_jblembur = 'T') then
                    raise notice 'nik : % start1',vr_nik;
                    select case when count(*) <= 5 then 0 else vr_valkomplembur end as nominal
                    into vr_komplembur
                    from sc_trx.transready
                    where jam_masuk_absen is not null
                      and jam_pulang_absen is not null
                      and nik = vr_nik
                      and to_char(tgl, 'yyyy-mm-dd') between vr_tglawal and vr_tglakhir;
                    if (vr_komplembur > 0) then
                        insert into sc_tmp.komplembur_um (branch, nik, kdcabang, nodok, dokref, tglawal, tglakhir,
                                                          status, flag, nominal, keterangan)
                        values (vr_branch, vr_nik, vr_kdcabang, vr_userid, '', vr_tglawal::DATE, vr_tglakhir::DATE, 'I',
                                'YES', vr_komplembur, 'LEMBUR KOMPENSASI ' || vr_tglawal || ' s/d ' || vr_tglakhir);
                    end if;
                end if;
                /* INSERT KE POTONGAN UANG MAKAN DR JAM ISTIRAHAT*/
                /* 1 POTONGAN DARI JAM ISTIRAHAT */
                delete from sc_tmp.potongan_um where nodok = vr_userid and tgl = vr_tgl and nik = vr_nik;
                if (vr_jbpot_um_ist = 'T') then
                    raise notice 'nik : % start2',vr_nik;

                    select case
                               when ik.nodok is not null then
                                   case
                                       when
                                           ((ik.tgl_jam_mulai <= jk.jam_istirahat and
                                             ik.tgl_jam_selesai > jk.jam_istirahat) or
                                            (ik.tgl_jam_mulai >= jk.jam_istirahat and
                                             ik.tgl_jam_selesai > jk.jam_istirahat)) then 0
                                       end
                               when jam_istirahat_in is null or jam_istirahat_out is null then vr_valpotist
                               when (extract(epoch from cast(jam_istirahat_out - jam_istirahat_in as time)::interval) /
                                     60)::numeric > vr_intv_ist then vr_valpotist
                               when (extract(epoch from cast(jam_istirahat_out - jam_istirahat_in as time)::interval) /
                                     60)::numeric < 15 then vr_valpotist
                               else 0 end
                    into vr_if_nol_potist
                    from sc_trx.transready t
                             left outer join sc_trx.ijin_karyawan ik
                                             on t.nik = ik.nik and t.tgl = ik.tgl_kerja and ik.kdijin_absensi = 'IK' and
                                                ik.type_ijin = 'DN' and ik.status = 'P'
                             left outer join sc_mst.jam_kerja jk on t.kdjamkerja = jk.kdjam_kerja
                    where t.nik = vr_nik
                      and t.tgl = vr_tgl
                      and jam_masuk_absen is not null
                      and jam_pulang_absen is not null;
                    if (vr_if_nol_potist > 0) then
                        raise notice 'nik : % start3',vr_nik;
                        insert into sc_tmp.potongan_um (branch, nik, kdcabang, nodok, dokref, doktype, tgl, status,
                                                        flag, nominal, jam_istirahat_in, jam_istirahat_out, keterangan,
                                                        durasi_ist)
                            (select vr_branch,
                                    nik,
                                    vr_kdcabang,
                                    vr_userid,
                                    '',
                                    'POTIST',
                                    tgl,
                                    'I',
                                    'YES',
                                    vr_if_nol_potist,
                                    jam_istirahat_in,
                                    jam_istirahat_out,
                                    'POTONGAN ISTIRAHAT',
                                    coalesce(floor(extract(epoch from
                                                           cast(to_char(jam_istirahat_out - jam_istirahat_in, 'HH24:MI:SS') as time)::interval) /
                                                   60)::numeric, 0)
                             from sc_trx.transready
                             where nik = vr_nik
                               and tgl = vr_tgl
                               and jam_masuk_absen is not null
                               and jam_pulang_absen is not null);
                    end if;
                end if;
                if (vr_jbpot_um = 'T') then
                    raise notice 'nik : % start4',vr_nik;

                    /* 2 POTONGAN DARI JAM MASUK KERJA */
                    select case
                               when jam_masuk_absen is null or jam_masuk_absen > jam_masuk then vr_valpotmas
                               else 0 end
                    into vr_if_nol_potmas
                    from sc_trx.transready a
                             left outer join sc_trx.listlinkjadwalcuti b on a.nik = b.nik and a.tgl = b.tgl
                    where a.nik = vr_nik
                      and a.tgl = vr_tgl
                      and b.kdpokok <> 'AL'
                      and b.nodok is null;

                    if (vr_if_nol_potmas > 0) then
                        raise notice 'nik : % start5',vr_nik;

                        insert into sc_tmp.potongan_um (branch, nik, kdcabang, nodok, dokref, doktype, tgl, status,
                                                        flag, nominal, jam_istirahat_in, jam_istirahat_out, keterangan)
                            (select vr_branch,
                                    a.nik,
                                    vr_kdcabang,
                                    vr_userid,
                                    '',
                                    'POTMAS',
                                    a.tgl,
                                    'I',
                                    case when ijin is not null or ijin != '' then 'NO' else 'YES' end,
                                    case
                                        when jam_masuk_absen is null or jam_masuk_absen >= jam_masuk then vr_valpotmas
                                        else 0 end,
                                    a.jam_istirahat_in,
                                    a.jam_istirahat_out,
                                    'POTONGAN TERLAMBAT'
                             from (select *
                                   from (select ta.*,
                                                case
                                                    when ta.jam_masuk_absen = ta.jam_pulang_absen and
                                                         ta.jam_masuk_absen > vr_um_max01 then null
                                                    else ta.jam_masuk_absen end  as checkin,
                                                case
                                                    when ta.jam_masuk_absen = ta.jam_pulang_absen and
                                                         ta.jam_pulang_absen <= vr_um_max01 then null
                                                    else ta.jam_pulang_absen end as checkout,
                                                tc.nodok                         as ijin,
                                                td.nodok                         as dinas,
                                                te.nodok                         as lembur
                                         from sc_trx.transready ta
                                                  left outer join sc_trx.ijin_karyawan tc
                                                                  on tc.nik = ta.nik and tc.tgl_kerja = ta.tgl and
                                                                     tc.status = 'P' and tc.type_ijin = 'DN' AND
                                                                     kdijin_absensi = (select tcc.kdijin_absensi
                                                                                       from (select kdijin_absensi,
                                                                                                    CASE
                                                                                                        when kdijin_absensi IN ('DT')
                                                                                                            THEN 1
                                                                                                        when kdijin_absensi IN ('PA')
                                                                                                            THEN 2
                                                                                                        else 3
                                                                                                        END as sort
                                                                                             from sc_trx.ijin_karyawan
                                                                                             where nik = ta.nik
                                                                                               and tgl_kerja = ta.tgl
                                                                                               and type_ijin = 'DN'
                                                                                             order by sort asc
                                                                                             limit 1) tcc)
                                                  left outer join sc_trx.dinas td on td.nik = ta.nik and
                                                                                     (ta.tgl between td.tgl_mulai and td.tgl_selesai) and
                                                                                     td.status = 'P'
                                                  left outer join sc_trx.lembur te
                                                                  on te.nik = ta.nik and te.tgl_kerja = ta.tgl and te.status = 'P') as x1) a
                                      left outer join sc_trx.listlinkjadwalcuti b on a.nik = b.nik and a.tgl = b.tgl
                             where a.nik = vr_nik
                               and a.tgl = vr_tgl
                               and b.kdpokok <> 'AL'
                               and b.nodok is null);
                    end if;
                end if;
                if (vr_jbpot_um_pul = 'T') then
                    raise notice 'nik : % start6',vr_nik;

                    /* 3 POTONGAN TIDAK CEKLOK PULANG */
                    select case
                               when jam_pulang_absen is null or jam_pulang_absen < jam_pulang then vr_valpotpul
                               else 0 end
                    into vr_if_nol_potpul
                    from sc_trx.transready a
                             left outer join sc_trx.listlinkjadwalcuti b on a.nik = b.nik and a.tgl = b.tgl
                    where a.nik = vr_nik
                      and a.tgl = vr_tgl
                      and b.kdpokok <> 'AL'
                      and b.nodok is null;

                    if (vr_if_nol_potpul > 0) then
                        raise notice 'nik : % start7',vr_nik;

                        insert into sc_tmp.potongan_um (branch, nik, kdcabang, nodok, dokref, doktype, tgl, status,
                                                        flag, nominal, jam_istirahat_in, jam_istirahat_out, keterangan)
                            (select vr_branch,
                                    a.nik,
                                    vr_kdcabang,
                                    vr_userid,
                                    '',
                                    'POTPUL',
                                    a.tgl,
                                    'I',
                                    case when ijin is not null or ijin != '' then 'NO' else 'YES' end,
                                    case
                                        when jam_pulang_absen is null or jam_pulang_absen < jam_pulang then vr_valpotpul
                                        else 0 end,
                                    a.jam_istirahat_in,
                                    a.jam_istirahat_out,
                                    'POTONGAN TIDAK CEKLOK PULANG'
                             from (select *
                                   from (select ta.*,
                                                case
                                                    when ta.jam_masuk_absen = ta.jam_pulang_absen and
                                                         ta.jam_masuk_absen > vr_um_max01 then null
                                                    else ta.jam_masuk_absen end  as checkin,
                                                case
                                                    when ta.jam_masuk_absen = ta.jam_pulang_absen and
                                                         ta.jam_pulang_absen <= vr_um_max01 then null
                                                    else ta.jam_pulang_absen end as checkout,
                                                tc.nodok                         as ijin,
                                                td.nodok                         as dinas,
                                                te.nodok                         as lembur
                                         from sc_trx.transready ta
                                                  left outer join sc_trx.ijin_karyawan tc
                                                                  on tc.nik = ta.nik and tc.tgl_kerja = ta.tgl and
                                                                     tc.status = 'P' and tc.nodok = (select nodok
                                                                                                     from (select nodok,
                                                                                                                  case
                                                                                                                      when type_ijin = 'DN'
                                                                                                                          THEN 0
                                                                                                                      else 1
                                                                                                                      END AS type_sort,
                                                                                                                  CASE
                                                                                                                      WHEN kdijin_absensi IN ('DT', 'PA')
                                                                                                                          THEN 0
                                                                                                                      ELSE 1
                                                                                                                      END as kdijin_sort
                                                                                                           from sc_trx.ijin_karyawan
                                                                                                           where nik = tc.nik
                                                                                                             AND tgl_kerja = tc.tgl_kerja
                                                                                                           order by type_sort
                                                                                                               ASC, kdijin_sort
                                                                                                               ASC
                                                                                                           limit 1) aa)
                                                  left outer join sc_trx.dinas td on td.nik = ta.nik and
                                                                                     (ta.tgl between td.tgl_mulai and td.tgl_selesai) and
                                                                                     td.status = 'P'
                                                  left outer join sc_trx.lembur te
                                                                  on te.nik = ta.nik and te.tgl_kerja = ta.tgl and te.status = 'P') as x1) a
                                      left outer join sc_trx.listlinkjadwalcuti b on a.nik = b.nik and a.tgl = b.tgl
                             where a.nik = vr_nik
                               and a.tgl = vr_tgl
                               and b.kdpokok <> 'AL'
                               and b.nodok is null);
                    end if;
                end if;
                /* UANG SEWA KENDARAAN YG SUDAH FINAL */
                if not exists(select *
                              from sc_tmp.master_um
                              where nik = vr_nik and nodok = vr_userid and branch = vr_branch and tgl = vr_tgl) then
                    insert into sc_tmp.master_um (branch, nik, kdcabang, nodok, dokref, tgl, status, total, uangmkn,
                                                  potongan, sewa, lembur_um, keterangan)
                    values (vr_branch, vr_nik, vr_kdcabang, vr_userid, '', vr_tgl, 'I', 0, 0, 0, 0, 0, '');
                end if;
                update sc_tmp.master_um
                set sewa=(select sum(total_besaran)
                          from sc_trx.mtrmst a
                                   left outer join sc_trx.transready b on a.nik = b.nik and
                                                                          to_char(a.tanggal_akhir, 'yyyy-mm-dd')::date =
                                                                          b.tgl
                              and a.status = 'P'
                          where a.nik = vr_nik
                            and b.tgl = vr_tgl)
                where nik = vr_nik
                  and nodok = vr_userid
                  and branch = vr_branch
                  and tgl = vr_tgl;

                update sc_tmp.master_um
                set total=(select coalesce(sum(coalesce(sewa, 0.0) + coalesce(lembur_um, 0.0) + (
                    case
                        when coalesce(uangmkn, 0.0) - coalesce(potongan, 0.0) < 0 then 0
                        else coalesce(coalesce(uangmkn, 0.0) - coalesce(potongan, 0.0), 0.0) end)), 0.0)
                           from sc_tmp.master_um
                           where nik = vr_nik
                             and nodok = vr_userid
                             and branch = vr_branch
                             and tgl = vr_tgl)
                where nik = vr_nik
                  and nodok = vr_userid
                  and branch = vr_branch
                  and tgl = vr_tgl;


                /* UANG MAKAN DTL KARYAWAN */
                delete from sc_tmp.uangmakan where nodok = vr_userid and tgl = vr_tgl and nik = vr_nik;
                if (vr_jbum = 'T') then
                    raise notice 'nik : % start8',vr_nik;

                    insert into sc_tmp.uangmakan
                    (branch, nodok, nik, kdcabang, dokref, tgl, checkin, checkout, nominal, keterangan, status)
                        (select vr_branch,
                                vr_userid,
                                ta.nik,
                                vr_kdcabang,
                                case when tc.nodok is not null then tc.nodok else td.nodok end as nodok,
                                ta.tgl,
                                ta.checkin,
                                ta.checkout,
                                case
                                    when checkin is null and checkout is null then 0
                                    when checkin is not null and checkout is not null and td.nodok is not null then 0
                                    when checkin is null and checkout is null and td.nodok is not null then 0
                                    /*when  checkin<=vr_um_min01 and checkout>jam_pulang and te.nodok is null then besaran
                                    ---when  checkin<=vr_um_min01 and checkout>jam_pulang and te.nodok is not null then besaran+besaran
                                    when  checkin>vr_um_min01 and vr_um_max01<checkout and kdijin_absensi is null then besaran
                                    when  checkin>vr_um_min01  and checkout>jam_pulang and kdijin_absensi is null then 0
                                    when  checkin is null and checkout>jam_pulang and kdijin_absensi is null  then 0
                                    when  checkin<vr_um_min01 and checkout is null  and kdijin_absensi is null  then 0
                                    when  checkin<vr_um_min01 and checkout<vr_um_max01 then 0
                                    when  checkin<vr_um_min01 and checkout>=vr_um_max01 and to_char(tgl,'Dy')<>'Sat' then besaran
                                    when  checkin<vr_um_min01 and checkout>=vr_um_max02 and to_char(tgl,'Dy')='Sat' then besaran
                                    when  checkin<vr_um_min01 and checkout is null and kdijin_absensi='IK' and cast(to_char(tc.approval_date,'yyyy-mm-dd')as date)<=vr_date_now  then besaran
                                    when  cast(to_char(tc.approval_date,'yyyy-mm-dd')as date)=ta.tgl  then besaran */

                                    else besaran
                                    end                                                        as nominal,
                                case
                                    when checkin is null and checkout is null and td.nodok is null
                                        then 'TIDAK MASUK KANTOR'
                                    when checkin is null and checkout is null and td.nodok is not null then
                                        'DINAS DENGAN NO DINAS :' || td.nodok || '|| APP TGL: ' ||
                                        to_char(td.approval_date, 'yyyy-mm-dd')
                                    when checkin is not null and checkout is not null and td.nodok is not null then
                                        'DINAS DENGAN NO DINAS :' || td.nodok || '|| APP TGL: ' ||
                                        to_char(td.approval_date, 'yyyy-mm-dd')
                                    when checkin < jam_masuk and checkout > jam_pulang and te.nodok is null
                                        then 'TEPAT WAKTU'
                                    when checkin < jam_masuk and checkout > jam_pulang and te.nodok is not null
                                        then 'TEPAT WAKTU + Lembur :' || te.nodok
                                    when checkin > jam_masuk and checkout < jam_pulang AND tc.nodok is not null then
                                        'IJIN DGN NO :' || tc.nodok || '|| APP TGL: ' ||
                                        to_char(tc.approval_date, 'yyyy-mm-dd')
                                    when checkin > jam_masuk and checkout < jam_pulang then 'TELAT MASUK/PULANG AWAL'
                                    when checkin >= jam_masuk and checkout > jam_pulang and tc.kdijin_absensi is null
                                        then 'TELAT MASUK'
                                    when checkin >= jam_masuk and checkout > jam_pulang and
                                         tc.kdijin_absensi is not null then
                                        'IJIN DGN NO :' || tc.nodok || '|| APP TGL: ' ||
                                        to_char(tc.approval_date, 'yyyy-mm-dd')
                                    when checkin isnull and checkout > jam_pulang then 'TIDAK CEKLOG MASUK'
                                    when checkin < jam_masuk and checkout is null and tc.kdijin_absensi is null
                                        then 'TIDAK CEKLOG PULANG'
                                    when checkin < jam_masuk and checkout < jam_pulang then 'PULANG AWAL'
                                    when checkin < vr_um_min01 and checkout is null and kdijin_absensi = 'IK' then
                                        'IJIN DGN NO :' || tc.nodok || '|| APP TGL: ' ||
                                        to_char(tc.approval_date, 'yyyy-mm-dd')
                                    end                                                        as keterangan,
                                'I'                                                            as status
                         from (select vr_branch                       as branch,
                                      a.nik,
                                      b.nmlengkap,
                                      c.kddept,
                                      c.nmdept,
                                      e.kdjabatan,
                                      e.nmjabatan,
                                      a.tgl,
                                      case
                                          when a.jam_masuk_absen = a.jam_pulang_absen and
                                               a.jam_masuk_absen > vr_um_max01 then null
                                          else a.jam_masuk_absen end  as checkin,
                                      case
                                          when a.jam_masuk_absen = a.jam_pulang_absen and
                                               a.jam_pulang_absen <= vr_um_max01 then null
                                          else a.jam_pulang_absen end as checkout,
                                      null                            as nominal,
                                      ''                              as keterangan,
                                      b.kdcabang,
                                      b.lvl_jabatan,
                                      a.jam_masuk,
                                      a.jam_pulang,
                                      f.besaran                       as kantin,
                                      b.idbu
                               from sc_trx.transready a
                                        left outer join sc_mst.karyawan b on a.nik = b.nik
                                        left outer join sc_mst.departmen c on b.bag_dept = c.kddept
                                        left outer join sc_mst.subdepartmen d
                                                        on b.subbag_dept = d.kdsubdept and b.bag_dept = d.kddept
                                        left outer join sc_mst.jabatan e
                                                        on b.jabatan = e.kdjabatan and b.subbag_dept = e.kdsubdept and
                                                           b.bag_dept = e.kddept
                                        left outer join sc_mst.kantin f on b.kdcabang = f.kdcabang) as ta
                                  left outer join sc_mst.uangmakan tb on tb.kdlvl = ta.lvl_jabatan AND tb.branch = case when ta.idbu NOT IN ('A','B') THEN 'SBYNSA' ELSE 'NJRBJM' END
                                  left outer join sc_trx.ijin_karyawan tc
                                                  on tc.nik = ta.nik and tc.tgl_kerja = ta.tgl and tc.status = 'P' and
                                                     tc.type_ijin = 'DN' AND kdijin_absensi = (select tcc.kdijin_absensi
                                                                                               from (select kdijin_absensi,
                                                                                                            CASE
                                                                                                                when kdijin_absensi IN ('DT')
                                                                                                                    THEN 1
                                                                                                                when kdijin_absensi IN ('PA')
                                                                                                                    THEN 2
                                                                                                                else 3
                                                                                                                END as sort
                                                                                                     from sc_trx.ijin_karyawan
                                                                                                     where nik = ta.nik
                                                                                                       and tgl_kerja = ta.tgl
                                                                                                       and type_ijin = 'DN'
                                                                                                     order by sort asc
                                                                                                     limit 1) tcc) --and to_char(tc.approval_date,'yyyy-mm-dd')<=to_char(now(),'yyyy-mm-dd')
                                  left outer join sc_trx.dinas td on td.nik = ta.nik and
                                                                     (ta.tgl between td.tgl_mulai and td.tgl_selesai) and
                                                                     td.status = 'P'
                                  left outer join sc_trx.lembur te on te.nik = ta.nik and te.tgl_kerja = ta.tgl and
                                                                      to_char(ta.checkout, 'HH23:MI:SS') >=
                                                                      '18:00:00' and te.status = 'P'/*LEMBUR */
                         where ta.lvl_jabatan <> 'A'
                           and ta.nik = vr_nik
                           and ta.tgl = vr_tgl
                         group by ta.nik, ta.nmlengkap, ta.tgl, ta.checkin, ta.checkout, ta.kdcabang, ta.jam_masuk,
                                  ta.jam_pulang, tb.besaran, ta.lvl_jabatan, ta.kantin, tc.kdijin_absensi, tc.tgl_kerja,
                                  td.nodok, td.approval_date, tc.nodok, tc.approval_date, te.nodok
                         ORDER BY NMLENGKAP);
                end if;
            END LOOP;


--SETUP FORMAT 2
    ELSEIF (vr_ZLBRKMPDF = 'ZLBRKMP02') THEN

        delete from sc_tmp.komplembur_um where nodok = vr_userid;
        delete from sc_tmp.potongan_um where nodok = vr_userid;
        delete from sc_tmp.uangmakan where nodok = vr_userid;
        FOR vr_nik,vr_tgl IN
            select trim(nik)::text as nik, tgl
            from sc_trx.transready
            where (to_char(tgl, 'YYYY-MM-DD') between vr_tglawal and vr_tglakhir)
              and nik in (select nik from sc_mst.karyawan where kdcabang = vr_kdcabang)
            order by tgl asc

            /*
             FOR vr_nik,vr_tgl,vr_jbshif,vr_jblembur,vr_jbum,vr_jbpot_um,vr_jbpot_um_ist,vr_jbpot_um_pul IN
                        select nik,tgl,ashift,alembur,um,pot_um,pot_um_ist,pot_um_pul from (
                        select a.nik,a.tgl,
                            coalesce(b.shift,'F') as ashift,
                            coalesce(b.lembur,'F') as alembur,
                            coalesce(b.um,'F') as um,
                            coalesce(b.pot_um,'F')as pot_um,
                            coalesce(b.pot_um_ist,'F')as pot_um_ist,
                            coalesce(b.pot_um_pul,'F')as pot_um_pul
                        from sc_trx.transready a
                        left outer join (
                                select nik ,
                                coalesce(shift,'F') as shift,
                                coalesce(lembur,'F') as lembur,
                                coalesce(um,'F') as um,
                                coalesce(pot_um,'F')as pot_um,
                                coalesce(pot_um_ist,'F')as pot_um_ist,
                                coalesce(pot_um_pul,'F')as pot_um_pul
                                from sc_mst.karyawan a join sc_mst.jabatan b on b.kdjabatan=a.jabatan and b.kddept=a.bag_dept and b.kdsubdept=a.subbag_dept
                                ) b on a.nik=b.nik
                        ) as x
                        where (to_char(tgl,'YYYY-MM-DD') between vr_tglawal and vr_tglakhir)
                        and nik in (select nik from sc_mst.karyawan where kdcabang=vr_kdcabang)
                        order by tgl asc
            */

            LOOP


                select coalesce(shift, 'F')      as shift,
                       coalesce(lembur, 'F')     as lembur,
                       coalesce(um, 'F')         as um,
                       coalesce(pot_um, 'F')     as pot_um,
                       coalesce(pot_um_ist, 'F') as pot_um_ist,
                       coalesce(pot_um_pul, 'F') as pot_um_pul
                from sc_mst.jabatan a
                         left outer join sc_mst.karyawan b on a.kdjabatan = b.jabatan and a.kddept = b.bag_dept and
                                                              a.kdsubdept = b.subbag_dept
                into vr_jbshif,vr_jblembur,vr_jbum,vr_jbpot_um,vr_jbpot_um_ist,vr_jbpot_um_pul
                    where
                nik = vr_nik;

                vr_date_now := cast(to_char(now(), 'yyyy-mm-dd') as date);
                vr_um_max01 := value2 from sc_mst.option where kdoption = 'HRUMMAX01';
                vr_um_max02 := value2 from sc_mst.option where kdoption = 'HRUMMAX02';
                vr_um_min01 := value2 from sc_mst.option where kdoption = 'HRUMMIN01';
                vr_um_min02 := value2 from sc_mst.option where kdoption = 'HRUMMIN02';
                vr_valkomplembur := value3 from sc_mst.option where kdoption = 'LBRPKT02';
                vr_valpotist := value3 from sc_mst.option where kdoption = 'POTIST';
                vr_valpotmas := value3 from sc_mst.option where kdoption = 'POTMAS';
                vr_valpotpul := value3 from sc_mst.option where kdoption = 'POTPUL';
                /* JAM MINIMAL LEMBUR */
                vr_lbrmin01 := value2 from sc_mst.option where kdoption = 'HRLBRMIN01'; --SENIN-KAMIS
                vr_lbrmin02 := value2 from sc_mst.option where kdoption = 'HRLBRMIN02'; --JUMAT
                vr_lbrmin03 := value2 from sc_mst.option where kdoption = 'HRLBRMIN03'; --SABTU

                vr_ptg := value3 from sc_mst.option where kdoption = 'PTG1';
                vr_intv_ist := (extract(epoch from cast(jam_istirahatselesai - jam_istirahat as time)::interval) / 60)::numeric
                               from sc_mst.jam_kerja
                               where kdjam_kerja in (select trim(kdjamkerja) from sc_trx.transready where nik = vr_nik
                                                                                                      and tgl = vr_tgl);

                /* INSERT INTO KOMPENSASI LEMBUR TEMPORARY  */

                if (vr_jblembur = 'T') then
                    select case
                               when to_char(tgl, 'Dy') in ('Mon', 'Tue', 'Wed', 'Thu') and
                                    jam_pulang_absen >= vr_lbrmin01 then
                                   floor(extract(epoch from cast(jam_pulang_absen - vr_lbrmin01 as time)::interval) /
                                         60::numeric / 30) * vr_valkomplembur
                               when to_char(tgl, 'Dy') in ('Fri') and jam_pulang_absen >= vr_lbrmin02 then
                                   floor(extract(epoch from cast(jam_pulang_absen - vr_lbrmin02 as time)::interval) /
                                         60::numeric / 30) * vr_valkomplembur
                               when to_char(tgl, 'Dy') in ('Sat') and jam_pulang_absen >= vr_lbrmin03 then
                                   floor(extract(epoch from cast(jam_pulang_absen - vr_lbrmin03 as time)::interval) /
                                         60::numeric / 30) * vr_valkomplembur
                               else 0 end as nominalx,
                           jam_masuk_absen,
                           jam_pulang_absen
                    into vr_komplembur,vr_jam_masuk_absen,vr_jam_pulang_absen
                    from sc_trx.transready
                    where nik = vr_nik
                      and tgl = vr_tgl
                      and jam_masuk_absen is not null
                      and jam_pulang_absen is not null;

/*				select * from sc_trx.transready limit 20
				select case
					when kdjamkerja='NSA' and jam_pulang_absen>= (select value2 from sc_mst.option where kdoption='HRLBRMIN01') then floor(extract(epoch from cast(jam_pulang_absen-(select value2 from sc_mst.option where kdoption='HRLBRMIN01') as time)::interval)/60::numeric/30) * (select value3 from sc_mst.option where kdoption='LBRPKT02')
					when kdjamkerja='NSB' and jam_pulang_absen>= (select value2 from sc_mst.option where kdoption='HRLBRMIN02') then floor(extract(epoch from cast(jam_pulang_absen-(select value2 from sc_mst.option where kdoption='HRLBRMIN02') as time)::interval)/60::numeric/30) * (select value3 from sc_mst.option where kdoption='LBRPKT02')
					when kdjamkerja='NSC' and jam_pulang_absen>= (select value2 from sc_mst.option where kdoption='HRLBRMIN03') then floor(extract(epoch from cast(jam_pulang_absen-(select value2 from sc_mst.option where kdoption='HRLBRMIN03') as time)::interval)/60::numeric/30) * (select value3 from sc_mst.option where kdoption='LBRPKT02')
				else 0 end as nominal_kompensasi  ,
				case
					when kdjamkerja='NSA' and jam_pulang_absen>= (select value2 from sc_mst.option where kdoption='HRLBRMIN01')then floor(extract(epoch from cast(jam_pulang_absen-(select value2 from sc_mst.option where kdoption='HRLBRMIN01') as time)::interval)/60::numeric/30)
					when kdjamkerja='NSB' and jam_pulang_absen>= (select value2 from sc_mst.option where kdoption='HRLBRMIN02')then floor(extract(epoch from cast(jam_pulang_absen-(select value2 from sc_mst.option where kdoption='HRLBRMIN02') as time)::interval)/60::numeric/30)
					when kdjamkerja='NSC' and jam_pulang_absen>= (select value2 from sc_mst.option where kdoption='HRLBRMIN03')then floor(extract(epoch from cast(jam_pulang_absen-(select value2 from sc_mst.option where kdoption='HRLBRMIN03') as time)::interval)/60::numeric/30)
				else 0 end as x_kompensasi ,jam_pulang_absen
				from sc_trx.transready where nik='01.0040' and tgl='2019-04-27' and jam_masuk_absen is not null and jam_pulang_absen is not null;
*/
                    if (vr_komplembur > 0) then
                        insert into sc_tmp.komplembur_um (branch, nik, kdcabang, nodok, dokref, tglawal, tglakhir,
                                                          status, flag, nominal, keterangan, jamawal, jamakhir)
                            (select vr_branch,
                                    vr_nik,
                                    vr_kdcabang,
                                    vr_userid,
                                    '',
                                    vr_tgl::DATE,
                                    vr_tgl::DATE,
                                    'I',
                                    'YES',
                                    vr_komplembur,
                                    'UANG KEHADIRAN',
                                    vr_jam_masuk_absen,
                                    vr_jam_pulang_absen
                             from sc_trx.transready
                             where nik = vr_nik
                               and tgl = vr_tgl);
                    end if;
                    --RAISE NOTICE 'Calling cs_create_job(%)', vr_nik||'-'||vr_tgl;
                end if;
                /* INSERT KE POTONGAN UANG MAKAN DR JAM ISTIRAHAT*/
                /* 1 POTONGAN DARI JAM ISTIRAHAT */
                delete from sc_tmp.potongan_um where nodok = vr_userid and tgl = vr_tgl and nik = vr_nik;
                if (vr_jbpot_um_ist = 'T') then
                    select case
                               when ik.nodok is not null then
                                   case
                                       when
                                           ((ik.tgl_jam_mulai <= jk.jam_istirahat and
                                             ik.tgl_jam_selesai > jk.jam_istirahat) or
                                            (ik.tgl_jam_mulai >= jk.jam_istirahat and
                                             ik.tgl_jam_selesai > jk.jam_istirahat)) then 0
                                       end
                               when jam_istirahat_in is null or jam_istirahat_out is null then vr_valpotist
                               when (extract(epoch from cast(jam_istirahat_out - jam_istirahat_in as time)::interval) /
                                     60)::numeric > vr_intv_ist then vr_valpotist
                               when (extract(epoch from cast(jam_istirahat_out - jam_istirahat_in as time)::interval) /
                                     60)::numeric < 15 then vr_valpotist
                               else 0 end
                    into vr_if_nol_potist
                    from sc_trx.transready t
                             left outer join sc_trx.ijin_karyawan ik
                                             on t.nik = ik.nik and t.tgl = ik.tgl_kerja and ik.kdijin_absensi = 'IK' and
                                                ik.type_ijin = 'DN' and ik.status = 'P'
                             left outer join sc_mst.jam_kerja jk on t.kdjamkerja = jk.kdjam_kerja
                    where t.nik = vr_nik
                      and t.tgl = vr_tgl
                      and jam_masuk_absen is not null
                      and jam_pulang_absen is not null;
                    if (vr_if_nol_potist > 0) then
                        insert into sc_tmp.potongan_um (branch, nik, kdcabang, nodok, dokref, doktype, tgl, status,
                                                        flag, nominal, jam_istirahat_in, jam_istirahat_out, keterangan,
                                                        durasi_ist)
                            (select vr_branch,
                                    nik,
                                    vr_kdcabang,
                                    vr_userid,
                                    '',
                                    'POTIST',
                                    tgl,
                                    'I',
                                    'YES',
                                    vr_if_nol_potist,
                                    jam_istirahat_in,
                                    jam_istirahat_out,
                                    'POTONGAN ISTIRAHAT',
                                    coalesce(floor(extract(epoch from
                                                           cast(to_char(jam_istirahat_out - jam_istirahat_in, 'HH24:MI:SS') as time)::interval) /
                                                   60)::numeric, 0)
                             from sc_trx.transready
                             where nik = vr_nik
                               and tgl = vr_tgl
                               and jam_masuk_absen is not null
                               and jam_pulang_absen is not null);
                    end if;
                end if;
                if (vr_jbpot_um = 'T') then
                    /* 2 POTONGAN DARI JAM MASUK KERJA */
                    select case
                               when jam_masuk_absen is null or jam_masuk_absen > jam_masuk then vr_valpotmas
                               else 0 end
                    into vr_if_nol_potmas
                    from sc_trx.transready a
                             left outer join sc_trx.listlinkjadwalcuti b on a.nik = b.nik and a.tgl = b.tgl
                    where a.nik = vr_nik
                      and a.tgl = vr_tgl
                      and b.kdpokok <> 'AL'
                      and b.nodok is null;

                    if (vr_if_nol_potmas > 0) then
                        insert into sc_tmp.potongan_um (branch, nik, kdcabang, nodok, dokref, doktype, tgl, status,
                                                        flag, nominal, jam_istirahat_in, jam_istirahat_out, keterangan)
                            (select vr_branch,
                                    a.nik,
                                    vr_kdcabang,
                                    vr_userid,
                                    '',
                                    'POTMAS',
                                    a.tgl,
                                    'I',
                                    case when ijin is not null or ijin != '' then 'NO' else 'YES' end,
                                    case
                                        when jam_masuk_absen is null or jam_masuk_absen >= jam_masuk then vr_valpotmas
                                        else 0 end,
                                    a.jam_istirahat_in,
                                    a.jam_istirahat_out,
                                    'POTONGAN TERLAMBAT'
                             from (select *
                                   from (select ta.*,
                                                case
                                                    when ta.jam_masuk_absen = ta.jam_pulang_absen and
                                                         ta.jam_masuk_absen > vr_um_max01 then null
                                                    else ta.jam_masuk_absen end  as checkin,
                                                case
                                                    when ta.jam_masuk_absen = ta.jam_pulang_absen and
                                                         ta.jam_pulang_absen <= vr_um_max01 then null
                                                    else ta.jam_pulang_absen end as checkout,
                                                tc.nodok                         as ijin,
                                                td.nodok                         as dinas,
                                                te.nodok                         as lembur
                                         from sc_trx.transready ta
                                                  left outer join sc_trx.ijin_karyawan tc
                                                                  on tc.nik = ta.nik and tc.tgl_kerja = ta.tgl and
                                                                     tc.status = 'P' and tc.type_ijin = 'DN' AND
                                                                     kdijin_absensi = (select tcc.kdijin_absensi
                                                                                       from (select kdijin_absensi,
                                                                                                    CASE
                                                                                                        when kdijin_absensi IN ('DT')
                                                                                                            THEN 1
                                                                                                        when kdijin_absensi IN ('PA')
                                                                                                            THEN 2
                                                                                                        else 3
                                                                                                        END as sort
                                                                                             from sc_trx.ijin_karyawan
                                                                                             where nik = ta.nik
                                                                                               and tgl_kerja = ta.tgl
                                                                                               and type_ijin = 'DN'
                                                                                             order by sort asc
                                                                                             limit 1) tcc)
                                                  left outer join sc_trx.dinas td on td.nik = ta.nik and
                                                                                     (ta.tgl between td.tgl_mulai and td.tgl_selesai) and
                                                                                     td.status = 'P'
                                                  left outer join sc_trx.lembur te
                                                                  on te.nik = ta.nik and te.tgl_kerja = ta.tgl and te.status = 'P') as x1) a
                                      left outer join sc_trx.listlinkjadwalcuti b on a.nik = b.nik and a.tgl = b.tgl
                             where a.nik = vr_nik
                               and a.tgl = vr_tgl
                               and b.kdpokok <> 'AL'
                               and b.nodok is null);
                    end if;
                end if;
                if (vr_jbpot_um_pul = 'T') then
                    /* 3 POTONGAN TIDAK CEKLOK PULANG */
                    select case
                               when jam_pulang_absen is null or jam_pulang_absen < jam_pulang then vr_valpotpul
                               else 0 end
                    into vr_if_nol_potpul
                    from sc_trx.transready a
                             left outer join sc_trx.listlinkjadwalcuti b on a.nik = b.nik and a.tgl = b.tgl
                    where a.nik = vr_nik
                      and a.tgl = vr_tgl
                      and b.kdpokok <> 'AL'
                      and b.nodok is null;

                    if (vr_if_nol_potpul > 0) then
                        insert into sc_tmp.potongan_um (branch, nik, kdcabang, nodok, dokref, doktype, tgl, status,
                                                        flag, nominal, jam_istirahat_in, jam_istirahat_out, keterangan)
                            (select vr_branch,
                                    a.nik,
                                    vr_kdcabang,
                                    vr_userid,
                                    '',
                                    'POTPUL',
                                    a.tgl,
                                    'I',
                                    case when ijin is not null or ijin != '' then 'NO' else 'YES' end,
                                    case
                                        when jam_pulang_absen is null or jam_pulang_absen < jam_pulang then vr_valpotpul
                                        else 0 end,
                                    a.jam_istirahat_in,
                                    a.jam_istirahat_out,
                                    'POTONGAN TIDAK CEKLOK PULANG'
                             from (select *
                                   from (select ta.*,
                                                case
                                                    when ta.jam_masuk_absen = ta.jam_pulang_absen and
                                                         ta.jam_masuk_absen > vr_um_max01 then null
                                                    else ta.jam_masuk_absen end  as checkin,
                                                case
                                                    when ta.jam_masuk_absen = ta.jam_pulang_absen and
                                                         ta.jam_pulang_absen <= vr_um_max01 then null
                                                    else ta.jam_pulang_absen end as checkout,
                                                tc.nodok                         as ijin,
                                                td.nodok                         as dinas,
                                                te.nodok                         as lembur
                                         from sc_trx.transready ta
                                                  left outer join sc_trx.ijin_karyawan tc
                                                                  on tc.nik = ta.nik and tc.tgl_kerja = ta.tgl and
                                                                     tc.status = 'P' and tc.type_ijin = 'DN' AND
                                                                     kdijin_absensi = (select tcc.kdijin_absensi
                                                                                       from (select kdijin_absensi,
                                                                                                    CASE
                                                                                                        when kdijin_absensi IN ('DT')
                                                                                                            THEN 1
                                                                                                        when kdijin_absensi IN ('PA')
                                                                                                            THEN 2
                                                                                                        else 3
                                                                                                        END as sort
                                                                                             from sc_trx.ijin_karyawan
                                                                                             where nik = ta.nik
                                                                                               and tgl_kerja = ta.tgl
                                                                                               and type_ijin = 'DN'
                                                                                             order by sort asc
                                                                                             limit 1) tcc)
                                                  left outer join sc_trx.dinas td on td.nik = ta.nik and
                                                                                     (ta.tgl between td.tgl_mulai and td.tgl_selesai) and
                                                                                     td.status = 'P'
                                                  left outer join sc_trx.lembur te
                                                                  on te.nik = ta.nik and te.tgl_kerja = ta.tgl and te.status = 'P') as x1) a
                                      left outer join sc_trx.listlinkjadwalcuti b on a.nik = b.nik and a.tgl = b.tgl
                             where a.nik = vr_nik
                               and a.tgl = vr_tgl
                               and b.kdpokok <> 'AL'
                               and b.nodok is null);
                    end if;
                end if;
                /* UANG SEWA KENDARAAN YG SUDAH FINAL */
                if not exists(select *
                              from sc_tmp.master_um
                              where nik = vr_nik and nodok = vr_userid and branch = vr_branch and tgl = vr_tgl) then
                    insert into sc_tmp.master_um (branch, nik, kdcabang, nodok, dokref, tgl, status, total, uangmkn,
                                                  potongan, sewa, lembur_um, keterangan)
                    values (vr_branch, vr_nik, vr_kdcabang, vr_userid, '', vr_tgl, 'I', 0, 0, 0, 0, 0, '');
                end if;
                update sc_tmp.master_um
                set sewa=(select sum(total_besaran)
                          from sc_trx.mtrmst a
                                   left outer join sc_trx.transready b on a.nik = b.nik and
                                                                          to_char(a.tanggal_akhir, 'yyyy-mm-dd')::date =
                                                                          b.tgl
                              and a.status = 'P'
                          where a.nik = vr_nik
                            and b.tgl = vr_tgl)
                where nik = vr_nik
                  and nodok = vr_userid
                  and branch = vr_branch
                  and tgl = vr_tgl;

                update sc_tmp.master_um
                set total=(select coalesce(sum(coalesce(sewa, 0.0) + coalesce(lembur_um, 0.0) + (
                    case
                        when coalesce(uangmkn, 0.0) - coalesce(potongan, 0.0) < 0 then 0
                        else coalesce(coalesce(uangmkn, 0.0) - coalesce(potongan, 0.0), 0.0) end)), 0.0)
                           from sc_tmp.master_um
                           where nik = vr_nik
                             and nodok = vr_userid
                             and branch = vr_branch
                             and tgl = vr_tgl)
                where nik = vr_nik
                  and nodok = vr_userid
                  and branch = vr_branch
                  and tgl = vr_tgl;


                /* UANG MAKAN DTL KARYAWAN */

                if (vr_jbum = 'T') then
                    insert into sc_tmp.uangmakan
                    (branch, nodok, nik, kdcabang, dokref, tgl, checkin, checkout, nominal, keterangan, status)
                        (select vr_branch,
                                vr_userid,
                                ta.nik,
                                vr_kdcabang,
                                case when tc.nodok is not null then tc.nodok else td.nodok end as nodok,
                                ta.tgl,
                                ta.checkin,
                                ta.checkout,
                                case
                                    when checkin is null and checkout is null then 0
                                    /*when  checkin<=vr_um_min01 and checkout>jam_pulang and te.nodok is null then besaran
                                    ---when  checkin<=vr_um_min01 and checkout>jam_pulang and te.nodok is not null then besaran+besaran
                                    when  checkin>vr_um_min01 and vr_um_max01<checkout and kdijin_absensi is null then besaran
                                    when  checkin>vr_um_min01  and checkout>jam_pulang and kdijin_absensi is null then 0
                                    when  checkin is null and checkout>jam_pulang and kdijin_absensi is null  then 0
                                    when  checkin<vr_um_min01 and checkout is null  and kdijin_absensi is null  then 0
                                    when  checkin<vr_um_min01 and checkout<vr_um_max01 then 0
                                    when  checkin<vr_um_min01 and checkout>=vr_um_max01 and to_char(tgl,'Dy')<>'Sat' then besaran
                                    when  checkin<vr_um_min01 and checkout>=vr_um_max02 and to_char(tgl,'Dy')='Sat' then besaran
                                    when  checkin<vr_um_min01 and checkout is null and kdijin_absensi='IK' and cast(to_char(tc.approval_date,'yyyy-mm-dd')as date)<=vr_date_now  then besaran
                                    when  cast(to_char(tc.approval_date,'yyyy-mm-dd')as date)=ta.tgl  then besaran */

                                    else besaran
                                    end                                                        as nominal,
                                case
                                    when checkin is null and checkout is null and td.nodok is null
                                        then 'TIDAK MASUK KANTOR'
                                    when checkin is null and checkout is null and td.nodok is not null then
                                        'DINAS DENGAN NO DINAS :' || td.nodok || '|| APP TGL: ' ||
                                        to_char(td.approval_date, 'yyyy-mm-dd')
                                    when checkin < jam_masuk and checkout > jam_pulang and te.nodok is null
                                        then 'TEPAT WAKTU'
                                    when checkin < jam_masuk and checkout > jam_pulang and te.nodok is not null
                                        then 'TEPAT WAKTU + Lembur :' || te.nodok
                                    when checkin > jam_masuk and checkout < jam_pulang AND tc.nodok is not null then
                                        'IJIN DGN NO :' || tc.nodok || '|| APP TGL: ' ||
                                        to_char(tc.approval_date, 'yyyy-mm-dd')
                                    when checkin > jam_masuk and checkout < jam_pulang then 'TELAT MASUK/PULANG AWAL'
                                    when checkin >= jam_masuk and checkout > jam_pulang and tc.kdijin_absensi is null
                                        then 'TELAT MASUK'
                                    when checkin >= jam_masuk and checkout > jam_pulang and
                                         tc.kdijin_absensi is not null then
                                        'IJIN DGN NO :' || tc.nodok || '|| APP TGL: ' ||
                                        to_char(tc.approval_date, 'yyyy-mm-dd')
                                    when checkin isnull and checkout > jam_pulang then 'TIDAK CEKLOG MASUK'
                                    when checkin < jam_masuk and checkout is null and tc.kdijin_absensi is null
                                        then 'TIDAK CEKLOG PULANG'
                                    when checkin < jam_masuk and checkout < jam_pulang then 'PULANG AWAL'
                                    when checkin < vr_um_min01 and checkout is null and kdijin_absensi = 'IK' then
                                        'IJIN DGN NO :' || tc.nodok || '|| APP TGL: ' ||
                                        to_char(tc.approval_date, 'yyyy-mm-dd')
                                    end                                                        as keterangan,
                                'I'                                                            as status
                         from (select vr_branch                       as branch,
                                      a.nik,
                                      b.nmlengkap,
                                      c.kddept,
                                      c.nmdept,
                                      e.kdjabatan,
                                      e.nmjabatan,
                                      a.tgl,
                                      case
                                          when a.jam_masuk_absen = a.jam_pulang_absen and
                                               a.jam_masuk_absen > vr_um_max01 then null
                                          else a.jam_masuk_absen end  as checkin,
                                      case
                                          when a.jam_masuk_absen = a.jam_pulang_absen and
                                               a.jam_pulang_absen <= vr_um_max01 then null
                                          else a.jam_pulang_absen end as checkout,
                                      null                            as nominal,
                                      ''                              as keterangan,
                                      b.kdcabang,
                                      b.lvl_jabatan,
                                      a.jam_masuk,
                                      a.jam_pulang,
                                      f.besaran                       as kantin,
                                      b.idbu
                               from sc_trx.transready a
                                        left outer join sc_mst.karyawan b on a.nik = b.nik
                                        left outer join sc_mst.departmen c on b.bag_dept = c.kddept
                                        left outer join sc_mst.subdepartmen d
                                                        on b.subbag_dept = d.kdsubdept and b.bag_dept = d.kddept
                                        left outer join sc_mst.jabatan e
                                                        on b.jabatan = e.kdjabatan and b.subbag_dept = e.kdsubdept and
                                                           b.bag_dept = e.kddept
                                        left outer join sc_mst.kantin f on b.kdcabang = f.kdcabang) as ta
                                  left outer join sc_mst.uangmakan tb on tb.kdlvl = ta.lvl_jabatan AND tb.branch = case when ta.idbu NOT IN ('A','B') THEN 'SBYNSA' ELSE 'NJRBJM' END
                                  left outer join sc_trx.ijin_karyawan tc
                                                  on tc.nik = ta.nik and tc.tgl_kerja = ta.tgl and tc.status = 'P' and
                                                     tc.type_ijin = 'DN' AND kdijin_absensi = (select tcc.kdijin_absensi
                                                                                               from (select kdijin_absensi,
                                                                                                            CASE
                                                                                                                when kdijin_absensi IN ('DT')
                                                                                                                    THEN 1
                                                                                                                when kdijin_absensi IN ('PA')
                                                                                                                    THEN 2
                                                                                                                else 3
                                                                                                                END as sort
                                                                                                     from sc_trx.ijin_karyawan
                                                                                                     where nik = ta.nik
                                                                                                       and tgl_kerja = ta.tgl
                                                                                                       and type_ijin = 'DN'
                                                                                                     order by sort asc
                                                                                                     limit 1) tcc) --and to_char(tc.approval_date,'yyyy-mm-dd')<=to_char(now(),'yyyy-mm-dd')
                                  left outer join sc_trx.dinas td on td.nik = ta.nik and
                                                                     (ta.tgl between td.tgl_mulai and td.tgl_selesai) and
                                                                     td.status = 'P'
                                  left outer join sc_trx.lembur te on te.nik = ta.nik and te.tgl_kerja = ta.tgl and
                                                                      to_char(ta.checkout, 'HH23:MI:SS') >=
                                                                      '18:00:00' and te.status = 'P'/*LEMBUR */
                         where ta.lvl_jabatan <> 'A'
                           and ta.nik = vr_nik
                           and ta.tgl = vr_tgl
                         group by ta.nik, ta.nmlengkap, ta.tgl, ta.checkin, ta.checkout, ta.kdcabang, ta.jam_masuk,
                                  ta.jam_pulang, tb.besaran, ta.lvl_jabatan, ta.kantin, tc.kdijin_absensi, tc.tgl_kerja,
                                  td.nodok, td.approval_date, tc.nodok, tc.approval_date, te.nodok
                         ORDER BY NMLENGKAP);
                end if;
            END LOOP;

    END IF;
    RETURN 'MMMUACH :* ';
END;
$$;