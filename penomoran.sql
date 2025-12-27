CREATE OR REPLACE VIEW lv_m_karyawan AS
 SELECT x.branch,
    x.nik,
    x.nmlengkap,
    x.callname,
    x.jk,
    x.neglahir,
    x.provlahir,
    x.kotalahir,
    x.tgllahir,
    x.kd_agama,
    x.stswn,
    x.stsfisik,
    x.ketfisik,
    x.noktp,
    x.ktp_seumurhdp,
    x.ktpdikeluarkan,
    x.tgldikeluarkan,
    x.status_pernikahan,
    x.gol_darah,
    x.negktp,
    x.provktp,
    x.kotaktp,
    x.kecktp,
    x.kelktp,
    x.alamatktp,
    x.negtinggal,
    x.provtinggal,
    x.kotatinggal,
    x.kectinggal,
    x.keltinggal,
    x.alamattinggal,
    x.nohp1,
    x.nohp2,
    x.npwp,
    x.tglnpwp,
    x.bag_dept,
    x.subbag_dept,
    x.jabatan,
    x.lvl_jabatan,
    x.grade_golongan,
    x.nik_atasan,
    x.nik_atasan2,
    x.status_ptkp,
    x.besaranptkp,
    x.tglmasukkerja,
    x.tglkeluarkerja,
    x.masakerja,
    x.statuskepegawaian,
    x.kdcabang,
    x.branchaktif,
    x.grouppenggajian,
    x.gajipokok,
    x.gajibpjs,
    x.namabank,
    x.namapemilikrekening,
    x.norek,
    x.tjshift,
    x.idabsen,
    x.email,
    x.email2,
    x.bolehcuti,
    x.sisacuti,
    x.inputdate,
    x.inputby,
    x.updatedate,
    x.updateby,
    x.image,
    x.idmesin,
    x.cardnumber,
    x.status,
    x.tgl_ktp,
    x.costcenter,
    x.tj_tetap,
    x.gajitetap,
    x.gajinaker,
    x.tjlembur,
    x.tjborong,
    x.nokk,
    x.kdwilayahnominal,
    x.nmjabatan,
    x.nmdept,
    x.nmsubdept,
    x.nmlvljabatan,
    x.nmgrade,
    x.nmagama,
    x.namanegara,
    x.nmprovlahir,
    x.nmkotalahir,
    x.nmprovktp,
    x.nmkotaktp,
    x.nmkecktp,
    x.nmdesaktp,
    x.nmprovtinggal,
    x.nmkotatinggal,
    x.nmkectinggal,
    x.nmdesatinggal,
    x.nmatasan1,
    x.nmatasan2,
    x.nmwilayahnominal,
    x.kdwilayah_gp,
    x.kdlvlgp,
    x.tglmasukkerja1,
    x.tglkeluarkerja1,
    x.tgllahir1,
    x.tglptkp,
    x.tglktp1,
    x.tjborong1,
    x.tjshift1,
    x.tjlembur1,
    x.nmcabang,
    x.masakerja1,
    x.tahunmasakerja,
    x.pinjaman,
    x.nmstatus_pernikahan,
    x.nmstatus_ptkp,
    x.nmbank,
    x.kdgradejabatan,
    x.nmgradejabatan,
    x.deviceid,
    x.callplan,
    x.idbu,
    x.idbuname
   FROM ( SELECT a.branch,
            a.nik,
            a.nmlengkap,
            a.callname,
            a.jk,
            a.neglahir,
            a.provlahir,
            a.kotalahir,
            a.tgllahir,
            a.kd_agama,
            a.stswn,
            a.stsfisik,
            a.ketfisik,
            a.noktp,
            a.ktp_seumurhdp,
            a.ktpdikeluarkan,
            a.tgldikeluarkan,
            a.status_pernikahan,
            a.gol_darah,
            a.negktp,
            a.provktp,
            a.kotaktp,
            a.kecktp,
            a.kelktp,
            a.alamatktp,
            a.negtinggal,
            a.provtinggal,
            a.kotatinggal,
            a.kectinggal,
            a.keltinggal,
            a.alamattinggal,
            a.nohp1,
            a.nohp2,
            a.npwp,
            a.tglnpwp,
            a.bag_dept,
            a.subbag_dept,
            a.jabatan,
            a.lvl_jabatan,
            a.grade_golongan,
            a.nik_atasan,
            a.nik_atasan2,
            a.status_ptkp,
            a.besaranptkp,
            a.tglmasukkerja,
            a.tglkeluarkerja,
            a.masakerja,
            a.statuskepegawaian,
            a.kdcabang,
            a.branchaktif,
            a.grouppenggajian,
            a.gajipokok,
            a.gajibpjs,
            a.namabank,
            a.namapemilikrekening,
            a.norek,
            a.tjshift,
            a.idabsen,
            a.email,
            a.email2,
            a.bolehcuti,
            a.sisacuti,
            a.inputdate,
            a.inputby,
            a.updatedate,
            a.updateby,
            a.image,
            a.idmesin,
            a.cardnumber,
            a.status,
            a.tgl_ktp,
            a.costcenter,
            a.tj_tetap,
            a.gajitetap,
            a.gajinaker,
            a.tjlembur,
            a.tjborong,
            a.nokk,
            a.kdwilayahnominal,
            a.kdlvlgp,
            a.kdgradejabatan,
            a.idbu,
            i5.idbuname,
            (COALESCE(a.pinjaman, (0)::numeric))::numeric(18,2) AS pinjaman,
            b.nmjabatan,
            c.nmdept,
            d.nmsubdept,
            e.nmlvljabatan,
            f.nmgrade,
            g1.nmagama,
            g2.namanegara,
            g3.namaprov AS nmprovlahir,
            g4.namakotakab AS nmkotalahir,
            g6.nmnikah AS nmstatus_pernikahan,
            g7.nmnikah AS nmstatus_ptkp,
            g8.nmbank,
            h1.namaprov AS nmprovktp,
            h2.namakotakab AS nmkotaktp,
            h3.namakec AS nmkecktp,
            h4.namakeldesa AS nmdesaktp,
            i1.namaprov AS nmprovtinggal,
            i2.namakotakab AS nmkotatinggal,
            i3.namakec AS nmkectinggal,
            i4.namakeldesa AS nmdesatinggal,
            f1.nmlengkap AS nmatasan1,
            f2.nmlengkap AS nmatasan2,
            k1.nmwilayahnominal,
            k1.kdwilayah AS kdwilayah_gp,
            k3.nmgradejabatan,
            to_char((a.tglmasukkerja)::timestamp with time zone, 'dd-mm-yyyy'::text) AS tglmasukkerja1,
            to_char((a.tglkeluarkerja)::timestamp with time zone, 'dd-mm-yyyy'::text) AS tglkeluarkerja1,
            to_char((a.tgllahir)::timestamp with time zone, 'dd-mm-yyyy'::text) AS tgllahir1,
            to_char((a.tgldikeluarkan)::timestamp with time zone, 'dd-mm-yyyy'::text) AS tglptkp,
            to_char((a.tgl_ktp)::timestamp with time zone, 'dd-mm-yyyy'::text) AS tglktp1,
                CASE
                    WHEN (a.tjborong = 't'::bpchar) THEN 'YA'::text
                    WHEN ((a.tjborong = 'f'::bpchar) OR (a.tjborong IS NULL)) THEN 'TIDAK'::text
                    ELSE NULL::text
                END AS tjborong1,
                CASE
                    WHEN (a.tjshift = 't'::bpchar) THEN 'YA'::text
                    WHEN ((a.tjshift = 'f'::bpchar) OR (a.tjshift IS NULL)) THEN 'TIDAK'::text
                    ELSE NULL::text
                END AS tjshift1,
                CASE
                    WHEN (a.tjlembur = 't'::bpchar) THEN 'YA'::text
                    WHEN ((a.tjlembur = 'f'::bpchar) OR (a.tjlembur IS NULL)) THEN 'TIDAK'::text
                    ELSE NULL::text
                END AS tjlembur1,
            k2.desc_cabang AS nmcabang,
            age((a.tglmasukkerja)::timestamp with time zone) AS masakerja1,
            floor((date_part('day'::text, (now() - ((a.tglmasukkerja)::timestamp without time zone)::timestamp with time zone)) / (365)::double precision)) AS tahunmasakerja,
            a.deviceid,
            a.callplan
           FROM ((((((((((((((((((((((((((karyawan a
             LEFT JOIN departmen c ON ((a.bag_dept = c.kddept)))
             LEFT JOIN subdepartmen d ON (((a.subbag_dept = d.kdsubdept) AND (a.bag_dept = d.kddept))))
             LEFT JOIN jabatan b ON (((a.subbag_dept = b.kdsubdept) AND (a.bag_dept = b.kddept) AND (a.jabatan = b.kdjabatan))))
             LEFT JOIN lvljabatan e ON ((a.lvl_jabatan = e.kdlvl)))
             LEFT JOIN jobgrade f ON ((a.grade_golongan = f.kdgrade)))
             LEFT JOIN karyawan f1 ON ((a.nik_atasan = f1.nik)))
             LEFT JOIN karyawan f2 ON ((a.nik_atasan2 = f2.nik)))
             LEFT JOIN agama g1 ON ((a.kd_agama = g1.kdagama)))
             LEFT JOIN negara g2 ON ((a.neglahir = g2.kodenegara)))
             LEFT JOIN provinsi g3 ON ((a.provlahir = g3.kodeprov)))
             LEFT JOIN kotakab g4 ON (((a.kotalahir = g4.kodekotakab) AND (g4.kodeprov = g3.kodeprov))))
             LEFT JOIN negara g5 ON ((a.negktp = g5.kodenegara)))
             LEFT JOIN status_nikah g6 ON ((a.status_pernikahan = g6.kdnikah)))
             LEFT JOIN status_nikah g7 ON ((a.status_ptkp = g7.kdnikah)))
             LEFT JOIN bank g8 ON ((a.namabank = g8.kdbank)))
             LEFT JOIN provinsi h1 ON ((a.provktp = h1.kodeprov)))
             LEFT JOIN kotakab h2 ON (((a.kotaktp = h2.kodekotakab) AND (h2.kodeprov = h1.kodeprov))))
             LEFT JOIN kec h3 ON ((a.kecktp = h3.kodekec)))
             LEFT JOIN keldesa h4 ON ((a.kelktp = h4.kodekeldesa)))
             LEFT JOIN provinsi i1 ON ((a.provtinggal = i1.kodeprov)))
             LEFT JOIN kotakab i2 ON (((a.kotatinggal = i2.kodekotakab) AND (i2.kodeprov = i1.kodeprov))))
             LEFT JOIN kec i3 ON ((a.kectinggal = i3.kodekec)))
             LEFT JOIN keldesa i4 ON ((a.keltinggal = i4.kodekeldesa)))
             LEFT JOIN m_wilayah_nominal k1 ON ((a.kdwilayahnominal = k1.kdwilayahnominal)))
             LEFT JOIN kantorwilayah k2 ON ((k2.kdcabang = a.kdcabang)))
             LEFT JOIN m_grade_jabatan k3 ON ((k3.kdgradejabatan = a.kdgradejabatan))
             LEFT JOIN idbu i5 on ((a.idbu=i5.idbu))
             )) x;

select idbuname,* from sc_mst.lv_m_karyawan
--=======-=============-======

CREATE OR REPLACE FUNCTION sc_tmp.pr_nipnbi_after()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
DECLARE
    vr_nik_final    VARCHAR(30);
    vr_prefix       VARCHAR(20);
    vr_partid       VARCHAR(10);
    vr_dokumen      VARCHAR(20);
    vr_nomor        TEXT;
    v_exists        INTEGER;
	v_error_msg     TEXT;
BEGIN
    DELETE FROM sc_mst.trxerror WHERE userid = NEW.nik;

    BEGIN
        IF (NEW.tjborong = 't') THEN
            -- BORONG: tetap global
            vr_prefix  := TO_CHAR(NEW.tglmasukkerja, 'YY');
            vr_dokumen := 'NIP-BORONG';
            vr_partid  := '';
        ELSE
            -- PEGAWAI BIASA: per cabang
            vr_prefix  := TRIM(NEW.kdcabang) || '.' || TO_CHAR(NEW.tglmasukkerja, 'MMYY') || '.';
            vr_dokumen := 'NIP-PEGAWAI';
            vr_partid  := TRIM(NEW.idbu);
        END IF;

        -- KHUSUS PEGAWAI BIASA: Auto create setting di sc_mst.nomor jika belum ada
        IF (NEW.tjborong = 'f') THEN
            SELECT COUNT(*) INTO v_exists
            FROM sc_mst.nomor
            WHERE dokumen = vr_dokumen
              AND part = vr_partid;

            IF v_exists = 0 THEN
                INSERT INTO sc_mst.nomor (dokumen, part, docno, prefix, sufix, count3)
                VALUES (vr_dokumen, vr_partid, 0, '', '', 3);  -- mulai dari 0 → jadi 001
            END IF;
        END IF;

        DELETE FROM sc_mst.penomoran WHERE userid = NEW.nik;

        -- Insert ke penomoran → trigger pr_beri_nomor_after akan generate
        INSERT INTO sc_mst.penomoran (
            userid, dokumen, nomor, errorid, partid, counterid, xno
        ) VALUES (
            NEW.nik, vr_dokumen, ' ', 0, vr_partid, 1, 0
        );

        SELECT TRIM(nomor)
        INTO vr_nomor
        FROM sc_mst.penomoran
        WHERE userid = NEW.nik;

        IF vr_nomor IS NULL OR TRIM(vr_nomor) = '' THEN
            RAISE EXCEPTION 'Gagal generate nomor untuk dokumen=% partid=%', vr_dokumen, vr_partid;
        END IF;

        -- Format NIK akhir
        IF (NEW.tjborong = 't') THEN
            vr_nik_final := vr_prefix || vr_nomor;
        ELSE
            vr_nik_final := vr_prefix || vr_nomor;  -- sudah 3 digit dari trigger (001, 002, dst.)
        END IF;

        -- Insert ke master karyawan
        INSERT INTO sc_mst.karyawan (
            branch, nik, nmlengkap, callname, jk, neglahir, provlahir, kotalahir, tgllahir, kd_agama,
            stswn, stsfisik, ketfisik, noktp, ktp_seumurhdp, ktpdikeluarkan, tgldikeluarkan,
            status_pernikahan, gol_darah, negktp, provktp, kotaktp, kecktp, kelktp, alamatktp,
            negtinggal, provtinggal, kotatinggal, kectinggal, keltinggal, alamattinggal,
            nohp1, nohp2, npwp, tglnpwp, bag_dept, subbag_dept, jabatan, lvl_jabatan,
            grade_golongan, nik_atasan, nik_atasan2, status_ptkp, besaranptkp,
            tglmasukkerja, tglkeluarkerja, masakerja, statuskepegawaian, kdcabang,
            branchaktif, grouppenggajian, gajipokok, gajibpjs, namabank, namapemilikrekening,
            norek, tjshift, idabsen, email, bolehcuti, sisacuti, inputdate, inputby,
            updatedate, updateby, image, idmesin, cardnumber, status, tgl_ktp, costcenter,
            tj_tetap, gajitetap, gajinaker, tjlembur, tjborong, nokk, kdwilayahnominal,
            kdlvlgp, pinjaman, kdgradejabatan, deviceid, callplan, idbu
        )
        SELECT
            branch, vr_nik_final, nmlengkap, callname, jk, neglahir, provlahir, kotalahir, tgllahir, kd_agama,
            stswn, stsfisik, ketfisik, noktp, ktp_seumurhdp, ktpdikeluarkan, tgldikeluarkan,
            status_pernikahan, gol_darah, negktp, provktp, kotaktp, kecktp, kelktp, alamatktp,
            negtinggal, provtinggal, kotatinggal, kectinggal, keltinggal, alamattinggal,
            nohp1, nohp2, npwp, tglnpwp, bag_dept, subbag_dept, jabatan, lvl_jabatan,
            grade_golongan, nik_atasan, nik_atasan2, status_ptkp, besaranptkp,
            tglmasukkerja, tglkeluarkerja, masakerja, statuskepegawaian, kdcabang,
            branchaktif, grouppenggajian, gajipokok, gajibpjs, namabank, namapemilikrekening,
            norek, tjshift, idabsen, email, bolehcuti, sisacuti, inputdate, inputby,
            updatedate, updateby, image, idmesin, cardnumber, status, tgl_ktp, costcenter,
            tj_tetap, gajitetap, gajinaker, tjlembur, tjborong, nokk, kdwilayahnominal,
            kdlvlgp, pinjaman, kdgradejabatan, deviceid, callplan, idbu
        FROM sc_tmp.karyawan
        WHERE nik = NEW.nik;

        DELETE FROM sc_tmp.karyawan WHERE nik = NEW.nik;

        -- Log sukses
        INSERT INTO sc_mst.trxerror (
            userid, errorcode, nomorakhir1, nomorakhir2, modul, error_detail
        ) VALUES (
            NEW.nik, '0', vr_nomor, vr_nik_final, 'KARYAWAN',
            jsonb_build_object(
                'timestamp', CURRENT_TIMESTAMP,
                'status', 'success',
                'message', 'NIK berhasil digenerate (auto create counter per cabang)',
                'nik', vr_nik_final,
                'nomor_urut', vr_nomor,
                'kdcabang', NEW.kdcabang
            )
        );

    EXCEPTION WHEN OTHERS THEN
        GET STACKED DIAGNOSTICS v_error_msg = MESSAGE_TEXT;

        INSERT INTO sc_mst.trxerror (
            userid, errorcode, nomorakhir1, nomorakhir2, modul, error_detail
        ) VALUES (
            NEW.nik, '1', 'ERROR', '',
            'KARYAWAN',
            jsonb_build_object(
                'timestamp', CURRENT_TIMESTAMP,
                'status', 'error',
                'message', COALESCE(v_error_msg, 'Unknown error'),
                'dokumen', vr_dokumen,
                'partid', vr_partid
            )
        );

        RAISE;
    END;

    RETURN NEW;
END;
$function$;

alter table sc_mst.kantorwilayah 
add column idbu varchar(2)

alter table sc_tmp.karyawan
add column idbu varchar(2)


CREATE OR REPLACE FUNCTION sc_tmp.pr_lembur_after()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
declare
     
     vr_nomor   char(30);
     vr_partid  VARCHAR(10);
     vr_prefix       VARCHAR(20);
     vr_dokumen VARCHAR(20);
     v_exists   INTEGER;
     
begin

select idbu from sc_mst.karyawan where nik=new.nik into vr_partid;
vr_dokumen := 'LEMBUR';
vr_prefix  := 'LBR' || TRIM(vr_partid);

SELECT COUNT(*) INTO v_exists
FROM sc_mst.nomor
WHERE dokumen = vr_dokumen
    AND part = vr_partid;

        IF v_exists = 0 THEN
            INSERT INTO sc_mst.nomor (dokumen, part, docno, prefix, sufix, count3)
            VALUES (vr_dokumen, vr_partid, 0, vr_prefix, '', 8); 
        END IF;

delete from sc_mst.penomoran where userid=new.nodok; 
insert into sc_mst.penomoran 
        (userid,dokumen,nomor,errorid,partid,counterid,xno)
        values(new.nodok,vr_dokumen,' ',0,vr_partid,1,0);

vr_nomor:=trim(coalesce(nomor,'')) from sc_mst.penomoran where userid=new.nodok;
 if (trim(vr_nomor)!='') or (not vr_nomor is null) then
	INSERT INTO sc_trx.lembur(
		nodok,nik,kddept,kdsubdept,kdlvljabatan,kdjabatan,tgl_dok,tgl_kerja,kdlembur,tgl_jam_mulai,tgl_jam_selesai,
		durasi,keterangan,input_by,update_by,nmatasan,kdtrx,nmatasan2,
		input_date,update_date,status,approval_date,approval_by,delete_by,delete_date,cancel_by,cancel_date,durasi_istirahat,jenis_lembur
	    )
	SELECT vr_nomor,nik,kddept,kdsubdept,kdlvljabatan,kdjabatan,tgl_dok,tgl_kerja,kdlembur,tgl_jam_mulai,tgl_jam_selesai,
		durasi,keterangan,input_by,update_by,nmatasan,kdtrx,nmatasan2,
		input_date,update_date,'I' as status,approval_date,approval_by,delete_by,delete_date,cancel_by,cancel_date,durasi_istirahat,jenis_lembur

	from sc_tmp.lembur where nodok=new.nodok and nik=new.nik;
       
delete from sc_tmp.lembur where nodok=new.nodok and nik=new.nik;

end if;

return new;

end;
$function$


CREATE OR REPLACE FUNCTION sc_tmp.tr_dinas_after()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
declare
     
     vr_nomor char(30);
     vr_partid  VARCHAR(10);
     vr_prefix       VARCHAR(20);
     vr_dokumen VARCHAR(20);
     v_exists   INTEGER;
     
begin

select idbu from sc_mst.karyawan where nik=new.nik into vr_partid;
vr_dokumen := 'DINAS-LUAR';
vr_prefix  := 'DL' || TRIM(vr_partid);

SELECT COUNT(*) INTO v_exists
FROM sc_mst.nomor
WHERE dokumen = vr_dokumen
    AND part = vr_partid;

        IF v_exists = 0 THEN
            INSERT INTO sc_mst.nomor (dokumen, part, docno, prefix, sufix, count3)
            VALUES (vr_dokumen, vr_partid, 0, vr_prefix, '', 9); 
        END IF;

delete from sc_mst.penomoran where userid=new.nodok; 
insert into sc_mst.penomoran 
        (userid,dokumen,nomor,errorid,partid,counterid,xno)
        values(new.nodok,vr_dokumen,' ',0,vr_partid,1,0);
      
vr_nomor:=trim(coalesce(nomor,'')) from sc_mst.penomoran where userid=new.nodok;
 if (trim(vr_nomor)!='') or (not vr_nomor is null) then
	INSERT INTO sc_trx.dinas(
		nik,nodok,tgl_dok,nmatasan,tgl_mulai,tgl_selesai,status,keperluan,tujuan,input_date,input_by,approval_date,approval_by,delete_date,delete_by,update_by,update_date,cancel_by,cancel_date
	    )
	SELECT nik,vr_nomor,tgl_dok,nmatasan,tgl_mulai,tgl_selesai,'I' as status,keperluan,tujuan,input_date,input_by,approval_date,approval_by,delete_date,delete_by,update_by,update_date,cancel_by,cancel_date
	from sc_tmp.dinas where nodok=new.nodok and nik=new.nik;
       
delete from sc_tmp.dinas where nodok=new.nodok and nik=new.nik;

end if;

return new;

end;
$function$

select * from sc_mst.nomor where dokumen = 'DINAS-LUAR'



CREATE OR REPLACE FUNCTION sc_tmp.tr_dinas_tmp_after()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
declare
    vr_nomor char(30);
    vr_partid  VARCHAR(10);
    vr_prefix  VARCHAR(20);
    vr_dokumen VARCHAR(20);
begin
    if (new.status='F' and old.status='I') then
        delete from sc_mst.penomoran where userid=new.nodok;

        select idbu from sc_mst.karyawan where nik=new.nik into vr_partid;
        vr_dokumen := 'DINAS-LUAR';
        vr_prefix  := 'DL' || TRIM(vr_partid);

        insert into sc_mst.penomoran 
        (userid,dokumen,nomor,errorid,partid,counterid,xno)
        values(new.nodok,vr_dokumen,' ',0,vr_partid,1,0);

        vr_nomor:=trim(coalesce(nomor,'')) from sc_mst.penomoran where userid=new.nodok;
   

        if (trim(vr_nomor)!='') or (not vr_nomor is null) then
            INSERT INTO sc_trx.dinas (
                nik,
                nodok,
                tgl_dok,
                nmatasan,
                tgl_mulai,
                jam_mulai,
                tgl_selesai,
                jam_selesai,
                status,
                keperluan,
                callplan,
                tujuan_kota,
                input_date,
                input_by,
                approval_date,
                approval_by,
                delete_date,
                delete_by,
                update_by,
                update_date,
                cancel_by,
                cancel_date,
                kdkategori,
                transportasi,
                tipe_transportasi,
                jenis_tujuan,
                no_telp
            ) SELECT
                  nik,
                  vr_nomor,
                  tgl_dok,
                  nmatasan,
                  tgl_mulai,
                  jam_mulai,
                  tgl_selesai,
                  jam_selesai,
                  old.status as status,
                  keperluan,
                  callplan,
                  tujuan_kota,
                  input_date,
                  input_by,
                  approval_date,
                  approval_by,
                  delete_date,
                  delete_by,
                  update_by,
                  update_date,
                  cancel_by,
                  cancel_date,
                  kdkategori,
                  transportasi,
                  tipe_transportasi,
                  jenis_tujuan,
                  no_telp
            from sc_tmp.dinas where TRUE
                                AND nodok = new.nodok
                                and nik = new.nik
                                and old.status = 'I';

            delete from sc_tmp.dinas where TRUE
                                       AND nodok = new.nodok
                                       and nik = new.nik
                                       and old.status = 'I';
        end if;
    end if;


    IF ( NEW.status = 'U' and OLD.status <> 'U' ) THEN
        DELETE FROM sc_trx.dinas WHERE TRUE
                                   AND nik = NEW.nik
                                   AND nodok = NEW.nodok;

        INSERT INTO sc_trx.dinas (
            nik,
            nodok,
            tgl_dok,
            nmatasan,
            tgl_mulai,
            jam_mulai,
            tgl_selesai,
            jam_selesai,
            status,
            keperluan,
            callplan,
            tujuan_kota,
            input_date,
            input_by,
            approval_date,
            approval_by,
            delete_date,
            delete_by,
            update_by,
            update_date,
            cancel_by,
            cancel_date,
            kdkategori,
            transportasi,
            tipe_transportasi,
            jenis_tujuan,
            no_telp
        ) SELECT
              nik,
              nodok,
              tgl_dok,
              nmatasan,
              tgl_mulai,
              jam_mulai,
              tgl_selesai,
              jam_selesai,
              OLD.status AS status,
              keperluan,
              callplan,
              tujuan_kota,
              input_date,
              input_by,
              approval_date,
              approval_by,
              delete_date,
              delete_by,
              update_by,
              update_date,
              cancel_by,
              cancel_date,
              kdkategori,
              transportasi,
              tipe_transportasi,
              jenis_tujuan,
              no_telp
        FROM sc_tmp.dinas WHERE TRUE
                            AND nik = NEW.nik
                            AND nodok = NEW.nodok;

        DELETE FROM sc_tmp.dinas WHERE TRUE
                                   AND nik = NEW.nik
                                   AND nodok = NEW.nodok;
    END IF;

    /*EX: extend/ perpanjang*/
    IF ( NEW.status = 'EX' and OLD.status <> 'EX' ) THEN
        DELETE FROM sc_trx.dinas WHERE TRUE
                                   AND nik = NEW.nik
                                   AND nodok = NEW.nodok;

        INSERT INTO sc_trx.dinas (
            nik,
            nodok,
            tgl_dok,
            nmatasan,
            tgl_mulai,
            jam_mulai,
            tgl_selesai,
            jam_selesai,
            status,
            keperluan,
            callplan,
            tujuan_kota,
            input_date,
            input_by,
            approval_date,
            approval_by,
            delete_date,
            delete_by,
            update_by,
            update_date,
            cancel_by,
            cancel_date,
            kdkategori,
            transportasi,
            tipe_transportasi,
            jenis_tujuan,
            no_telp
        ) SELECT
              nik,
              nodok,
              tgl_dok,
              nmatasan,
              tgl_mulai,
              jam_mulai,
              tgl_selesai,
              jam_selesai,
              'A' AS status,
              keperluan,
              callplan,
              tujuan_kota,
              input_date,
              input_by,
              approval_date,
              approval_by,
              delete_date,
              delete_by,
              update_by,
              update_date,
              cancel_by,
              cancel_date,
              kdkategori,
              transportasi,
              tipe_transportasi,
              jenis_tujuan,
              no_telp
        FROM sc_tmp.dinas WHERE TRUE
                            AND nik = NEW.nik
                            AND nodok = NEW.nodok;

        DELETE FROM sc_tmp.dinas WHERE TRUE
                                   AND nik = NEW.nik
                                   AND nodok = NEW.nodok;
    END IF;

    IF ( NEW.status = 'A' and OLD.status <> 'A' ) THEN
        perform sc_trx.pr_capture_approvals_system();
    end if;

    RETURN NEW;
end;
$function$


alter table sc_mst.departmen
add column hold varchar(2) default 'N'

alter table sc_mst.subdepartmen
add column hold varchar(2) default 'N'

alter table sc_mst.jabatan
add column hold varchar(2) default 'N'

alter table sc_mst.lvljabatan
add column hold varchar(2) default 'N'


CREATE OR REPLACE FUNCTION sc_tmp.pr_ijin_after()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
declare

    vr_nomor char(30);
    vr_partid  VARCHAR(10);
    vr_prefix       VARCHAR(20);
    vr_dokumen VARCHAR(20);
    v_exists   INTEGER;

begin
select idbu from sc_mst.karyawan where nik=new.nik into vr_partid;
vr_dokumen := 'IJIN-KARYAWAN';
vr_prefix  := 'IK' || TRIM(vr_partid);

SELECT COUNT(*) INTO v_exists
FROM sc_mst.nomor
WHERE dokumen = vr_dokumen
    AND part = vr_partid;

        IF v_exists = 0 THEN
            INSERT INTO sc_mst.nomor (dokumen, part, docno, prefix, sufix, count3)
            VALUES (vr_dokumen, vr_partid, 0, vr_prefix, '', 9); 
        END IF;

    delete from sc_mst.penomoran where userid=new.nodok; 
    insert into sc_mst.penomoran 
    (userid,dokumen,nomor,errorid,partid,counterid,xno)
    values(new.nodok,vr_dokumen,' ',0,vr_partid,1,0);

    vr_nomor:=trim(coalesce(nomor,'')) from sc_mst.penomoran where userid=new.nodok;
    if (trim(vr_nomor)!='') or (not vr_nomor is null) then
        INSERT INTO sc_trx.ijin_karyawan(
            nodok,nik,kddept,kdsubdept,kdlvljabatan,kdjabatan,tgl_dok,tgl_kerja,kdijin_absensi,tgl_jam_mulai,tgl_jam_selesai,
            durasi,keterangan,input_by,update_by,nmatasan,
            input_date,update_date,status,approval_date,approval_by,delete_by,delete_date,cancel_by,cancel_date,type_ijin,kendaraan,nopol,nikpengikut,tgl_mulai, tgl_selesai
        )
        SELECT vr_nomor,nik,kddept,kdsubdept,kdlvljabatan,kdjabatan,tgl_dok,tgl_kerja,kdijin_absensi,tgl_jam_mulai,tgl_jam_selesai,
               durasi,keterangan,input_by,update_by,nmatasan,
               input_date,update_date,'I' as status,approval_date,approval_by,delete_by,delete_date,cancel_by,cancel_date,type_ijin,kendaraan,nopol,nikpengikut,tgl_mulai, tgl_selesai

        from sc_tmp.ijin_karyawan where nodok=new.nodok and nik=new.nik;

        delete from sc_tmp.ijin_karyawan where nodok=new.nodok and nik=new.nik;

    end if;

    return new;

end;
$function$


select * from sc_mst.nomor where dokumen='IJIN-KARYAWAN'



