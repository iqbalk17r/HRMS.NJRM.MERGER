/*REKAP UM*/
-- DROP TABLE IF EXISTS sc_tmp.rekap_um;
create table sc_tmp.rekap_um
(
    branch     char(6)  not null,
    nodok      char(20) not null,
    dokref     char(20),
    kdcabang   char(20),
    tgldok     date,
    tglawal    date,
    tglakhir   date,
    status     char(4),
    nominal    numeric(18, 2),
    keterangan text,
    primary key (branch, nodok)
);

alter table sc_tmp.rekap_um
    owner to postgres;

create trigger tr_rekap_um
    after update
    on sc_tmp.rekap_um
    for each row
execute procedure sc_tmp.tr_rekap_um();

create OR REPLACE function sc_tmp.tr_rekap_um() returns trigger
    language plpgsql
as
$$
declare

    vr_nomor char(30);
    vr_nodok char(30);
    vr_dokref char(30);
    vr_kdcabang char(30);

begin


    /*select * from sc_mst.nomor
        AUTHOR FIKY ASHARIZA:
        CREATE DATE: 24/05/2017
        UPDATE DATE: 15/08/2019
        TITLE: PENAMBAHAN FORMAT 1 & FORMAT 2 PERUBAHAN PADA UANG KEHADIRAN
    */

    if (old.status='I' and new.status='P') then
        delete from sc_mst.penomoran where userid=new.nodok;
        insert into sc_mst.penomoran
        (userid,dokumen,nomor,errorid,partid,counterid,xno)
        values(new.nodok,'UANGMAKAN',' ',0,' ',1,0);

        vr_nomor:=trim(coalesce(nomor,'')) from sc_mst.penomoran where userid=new.nodok;
        update sc_tmp.master_um a set keterangan=b.keterangan||''||case
                                                                       when coalesce(b.potongan,0)>0 then ' -POTONGAN UM '
                                                                       else ''  end from sc_tmp.master_um b
        where a.branch=b.branch and a.nodok=new.nodok and a.nik=b.nik and a.tgl=b.tgl ;

        update sc_tmp.master_um a set keterangan=b.keterangan||''||case
                                                                       when coalesce(b.lembur_um,0)>0 then ' +UANG KEHADIRAN'
                                                                       else ''  end from sc_tmp.master_um b
        where a.branch=b.branch and a.nodok=new.nodok and a.nik=b.nik and a.tgl=b.tgl ;

        update sc_tmp.master_um a set keterangan=b.keterangan||''||case
                                                                       when coalesce(b.sewa,0)>0 then ' +SEWA KENDARAAN'
                                                                       else ''  end from sc_tmp.master_um b
        where a.branch=b.branch and a.nodok=new.nodok and a.nik=b.nik and a.tgl=b.tgl ;
        --select * from sc_tmp.master_um
        --select * from sc_tmp.uang_makan
        insert into sc_trx.detail_um
        (branch,nodok,nik,kdcabang,dokref,tgl,checkin,checkout,nominal,keterangan)
            (select branch,vr_nomor,nik,kdcabang,dokref,tgl,checkin,checkout,nominal,keterangan from sc_tmp.uangmakan where nodok=new.nodok and branch=new.branch);

        insert into sc_trx.komplembur_um
        (branch,nik,kdcabang,nodok,dokref,tglawal,tglakhir,status,flag,nominal,keterangan,jamawal,jamakhir)
            (select branch,nik,kdcabang,vr_nomor,dokref,tglawal,tglakhir,'P' as status,flag,nominal,keterangan,jamawal,jamakhir from sc_tmp.komplembur_um where nodok=new.nodok and branch=new.branch);

        insert into sc_trx.potongan_um
        (branch,nik,kdcabang,nodok,dokref,doktype,tgl,status,flag,nominal,jam_istirahat_in,jam_istirahat_out,keterangan,durasi_ist)
            (select branch,nik,kdcabang,vr_nomor,dokref,doktype,tgl,'P' as status,flag,nominal,jam_istirahat_in,jam_istirahat_out,keterangan,durasi_ist from sc_tmp.potongan_um where nodok=new.nodok and branch=new.branch);

        insert into sc_trx.master_um
        (branch,nik,kdcabang,nodok,dokref,tgl,status,total,uangmkn,potongan,sewa,lembur_um,keterangan)
            (select branch,nik,kdcabang,vr_nomor,dokref,tgl,'P' as status,total,uangmkn,potongan,sewa,lembur_um,keterangan from sc_tmp.master_um where nodok=new.nodok and branch=new.branch);

        insert into sc_trx.rekap_um
        (branch,nodok,dokref,kdcabang,tgldok,tglawal,tglakhir,status,nominal,keterangan)
            (select branch,vr_nomor,dokref,kdcabang,tgldok,tglawal,tglakhir,'P' as status,nominal,keterangan from sc_tmp.rekap_um where nodok=new.nodok and branch=new.branch);

        delete from sc_tmp.rekap_um where nodok=new.nodok and branch=new.branch;
        delete from sc_tmp.master_um where nodok=new.nodok and branch=new.branch;
        delete from sc_tmp.potongan_um where nodok=new.nodok and branch=new.branch;
        delete from sc_tmp.komplembur_um where nodok=new.nodok and branch=new.branch;
        delete from sc_tmp.uangmakan where nodok=new.nodok and branch=new.branch;
    elseif(old.status='E' and new.status='P') then
        select kdcabang,nodok,dokref from sc_tmp.rekap_um into vr_kdcabang,vr_nodok,vr_dokref where nodok=new.nodok and status=new.status;
        --select * from sc_tmp.rekap_um
        --select * from sc_tmp.master_um
        update sc_tmp.master_um a set keterangan=b.keterangan||''||case
                                                                       when coalesce(b.potongan,0)>0 then ' -POTONGAN UM '
                                                                       else ''  end from sc_tmp.master_um b
        where a.branch=b.branch and a.nodok=new.nodok and a.nik=b.nik and a.tgl=b.tgl ;

        update sc_tmp.master_um a set keterangan=b.keterangan||''||case
                                                                       when coalesce(b.lembur_um,0)>0 then ' +UANG KEHADIRAN'
                                                                       else ''  end from sc_tmp.master_um b
        where a.branch=b.branch and a.nodok=new.nodok and a.nik=b.nik and a.tgl=b.tgl ;

        update sc_tmp.master_um a set keterangan=b.keterangan||''||case
                                                                       when coalesce(b.sewa,0)>0 then ' +SEWA KENDARAAN'
                                                                       else ''  end from sc_tmp.master_um b
        where a.branch=b.branch and a.nodok=new.nodok and a.nik=b.nik and a.tgl=b.tgl ;
        --select * from sc_tmp.master_um
        --select * from sc_tmp.uang_makan
        insert into sc_trx.detail_um
        (branch,nodok,nik,kdcabang,dokref,tgl,checkin,checkout,nominal,keterangan)
            (select branch,vr_dokref,nik,kdcabang,vr_nodok,tgl,checkin,checkout,nominal,keterangan from sc_tmp.uangmakan where nodok=new.nodok and branch=new.branch);

        insert into sc_trx.komplembur_um
        (branch,nik,kdcabang,nodok,dokref,tglawal,tglakhir,status,flag,nominal,keterangan,jamawal,jamakhir)
            (select branch,nik,kdcabang,vr_dokref,vr_nodok,tglawal,tglakhir,'P' as status,flag,nominal,keterangan,jamawal,jamakhir from sc_tmp.komplembur_um where nodok=new.nodok and branch=new.branch);

        insert into sc_trx.potongan_um
        (branch,nik,kdcabang,nodok,dokref,doktype,tgl,status,flag,nominal,jam_istirahat_in,jam_istirahat_out,keterangan,durasi_ist)
            (select branch,nik,kdcabang,vr_dokref,vr_nodok,doktype,tgl,'P' as status,flag,nominal,jam_istirahat_in,jam_istirahat_out,keterangan,durasi_ist from sc_tmp.potongan_um where nodok=new.nodok and branch=new.branch);

        insert into sc_trx.master_um
        (branch,nik,kdcabang,nodok,dokref,tgl,status,total,uangmkn,potongan,sewa,lembur_um,keterangan)
            (select branch,nik,kdcabang,vr_dokref,vr_nodok,tgl,'P' as status,total,uangmkn,potongan,sewa,lembur_um,keterangan from sc_tmp.master_um where nodok=new.nodok and branch=new.branch);

        insert into sc_trx.rekap_um
        (branch,nodok,dokref,kdcabang,tgldok,tglawal,tglakhir,status,nominal,keterangan)
            (select branch,vr_dokref,vr_nodok,kdcabang,tgldok,tglawal,tglakhir,'P' as status,nominal,keterangan from sc_tmp.rekap_um where nodok=new.nodok and branch=new.branch);

        delete from sc_tmp.rekap_um where nodok=new.nodok and branch=new.branch;
        delete from sc_tmp.master_um where nodok=new.nodok and branch=new.branch;
        delete from sc_tmp.potongan_um where nodok=new.nodok and branch=new.branch;
        delete from sc_tmp.komplembur_um where nodok=new.nodok and branch=new.branch;
        delete from sc_tmp.uangmakan where nodok=new.nodok and branch=new.branch;

    end if;

    return new;

end;
$$;

/*mASTER UM*/
-- DROP TABLE IF EXISTS sc_tmp.master_um;
create table IF NOT EXISTS sc_tmp.master_um
(
    branch     char(6)  not null,
    nik        char(20) not null,
    kdcabang   char(20),
    nodok      char(20) not null,
    dokref     char(20),
    tgl        date     not null,
    status     char(4),
    total      numeric(18, 2),
    uangmkn    numeric(18, 2),
    potongan   numeric(18, 2),
    sewa       numeric(18, 2),
    lembur_um  numeric(18, 2),
    keterangan text,
    primary key (branch, nodok, nik, tgl)
);

alter table sc_tmp.master_um
    owner to postgres;

create trigger tr_master_um
    after insert or update or delete
    on sc_tmp.master_um
    for each row
execute procedure sc_tmp.tr_master_um();

create OR REPLACE function sc_tmp.tr_master_um() returns trigger
    language plpgsql
as
$$
DECLARE
    --title: triger master um ke rekap um
    --author by fiky: 23/05/2017
    --update by fiky: 23/05/2017
    vr_tglawal date;
    vr_tglakhir date;

BEGIN
    IF tg_op = 'INSERT' THEN
        vr_tglawal:=min(tgl) from sc_tmp.master_um where nodok=new.nodok and branch=new.branch;
        vr_tglakhir:=max(tgl) from sc_tmp.master_um where nodok=new.nodok and branch=new.branch;
        IF NEW.STATUS='I' THEN
            if not exists(select * from sc_tmp.rekap_um where nodok=new.nodok and branch=new.branch) then
                insert into sc_tmp.rekap_um (branch,nodok,dokref,kdcabang,tgldok,tglawal,tglakhir,status,nominal,keterangan)
                values
                    (new.branch,new.nodok,'',(select trim(kdcabang) from sc_mst.karyawan where nik=new.nik),to_char(now(),'yyyy-mm-dd')::date,vr_tglawal,vr_tglakhir,'I',0,'');
            end if;
            update sc_tmp.rekap_um set nominal=(select sum(total) from sc_tmp.master_um
                                                where nodok=new.nodok and branch=new.branch ),tglawal=vr_tglawal,tglakhir=vr_tglakhir
            where nodok=new.nodok and branch=new.branch;
        ELSEIF NEW.STATUS='E' THEN
            update sc_tmp.rekap_um set nominal=(select sum(total) from sc_tmp.master_um
                                                where nodok=new.nodok and branch=new.branch ),tglawal=vr_tglawal,tglakhir=vr_tglakhir
            where nodok=new.nodok and branch=new.branch;
        END IF;
        RETURN new;
    ELSEIF tg_op = 'UPDATE'	 THEN
        vr_tglawal:=min(tgl) from sc_tmp.master_um where nodok=new.nodok and branch=new.branch;
        vr_tglakhir:=max(tgl) from sc_tmp.master_um where nodok=new.nodok and branch=new.branch;
        IF NEW.STATUS='I' THEN
            if not exists(select * from sc_tmp.rekap_um where nodok=new.nodok and branch=new.branch) then
                insert into sc_tmp.rekap_um (branch,nodok,dokref,kdcabang,tgldok,tglawal,tglakhir,status,nominal,keterangan)
                values
                    (new.branch,new.nodok,'',(select trim(kdcabang) from sc_mst.karyawan where nik=new.nik),to_char(now(),'yyyy-mm-dd')::date,vr_tglawal,vr_tglakhir,'I',0,'');
            end if;
            update sc_tmp.rekap_um set nominal=(select sum(total) from sc_tmp.master_um
                                                where nodok=new.nodok and branch=new.branch ),tglawal=vr_tglawal,tglakhir=vr_tglakhir
            where nodok=new.nodok and branch=new.branch;
        ELSEIF NEW.STATUS='E' THEN
            update sc_tmp.rekap_um set nominal=(select sum(total) from sc_tmp.master_um
                                                where nodok=new.nodok and branch=new.branch ),tglawal=vr_tglawal,tglakhir=vr_tglakhir
            where nodok=new.nodok and branch=new.branch;
        END IF;
        RETURN new;
    ELSEIF tg_op = 'DELETE' THEN
        if exists(select * from sc_tmp.rekap_um where nodok=old.nodok and branch=old.branch ) then
            update sc_tmp.rekap_um set nominal=(select sum(total) from sc_tmp.master_um
                                                where nodok=old.nodok and branch=old.branch )
            where nodok=old.nodok and branch=old.branch;
        end if;
        RETURN old;
    END IF;

END;
$$;

create table IF NOT EXISTS sc_trx.rekap_um
(
    branch     char(6)  not null,
    nodok      char(20) not null,
    dokref     char(20),
    kdcabang   char(20),
    tgldok     date,
    tglawal    date,
    tglakhir   date,
    status     char(4),
    nominal    numeric(18, 2),
    keterangan text,
    primary key (branch, nodok)
);

alter table sc_trx.rekap_um
    owner to postgres;

DROP TRIGGER IF EXISTS tr_editfinalrekapum ON sc_trx.rekap_um;
create trigger tr_editfinalrekapum
    after update
    on sc_trx.rekap_um
    for each row
execute procedure sc_trx.tr_editfinalrekapum();

create OR REPLACE function sc_trx.tr_editfinalrekapum() returns trigger
    language plpgsql
as
$$
declare

    vr_kdcabang char(30);
    vr_nodok char(30);
    vr_dokref char(30);

begin

    if (old.status='P')and(new.status='E') then
        select kdcabang,nodok,dokref from sc_trx.rekap_um into vr_kdcabang,vr_nodok,vr_dokref where nodok=new.nodok and status='E';

        insert into sc_tmp.rekap_um (branch,nodok,dokref,kdcabang,tgldok,tglawal,tglakhir,status,nominal,keterangan)
            (select branch,vr_nodok,vr_dokref,kdcabang,tgldok,tglawal,tglakhir,'E' as status,nominal,keterangan from sc_trx.rekap_um where nodok=new.nodok and status='E');

        insert into sc_tmp.master_um (branch,nik,kdcabang,nodok,dokref,tgl,status,total,uangmkn,potongan,sewa,lembur_um,keterangan)
            (select branch,nik,kdcabang,vr_nodok,vr_dokref,tgl,'E' as status,total,uangmkn,potongan,sewa,lembur_um,keterangan from sc_trx.master_um where nodok=vr_dokref);

        insert into sc_tmp.uangmakan (branch,nodok,nik,kdcabang,dokref,tgl,checkin,checkout,nominal,keterangan,status)
            (select branch,vr_nodok,nik,kdcabang,vr_dokref,tgl,checkin,checkout,nominal,keterangan,'E' as status from sc_trx.detail_um where nodok=vr_dokref);

        insert into sc_tmp.komplembur_um (branch,nik,kdcabang,nodok,dokref,tglawal,tglakhir,status,flag,nominal,keterangan,jamawal,jamakhir)
            (select branch,nik,kdcabang,vr_nodok,vr_dokref,tglawal,tglakhir,'E' as status,flag,nominal,keterangan,jamawal,jamakhir from sc_trx.komplembur_um where nodok=vr_dokref);

        insert into sc_tmp.potongan_um (branch,nik,kdcabang,nodok,dokref,doktype,tgl,status,flag,nominal,jam_istirahat_in,jam_istirahat_out,keterangan,durasi_ist)
            (select branch,nik,kdcabang,vr_nodok,vr_dokref,doktype,tgl,'E' as status,flag,nominal,jam_istirahat_in,jam_istirahat_out,keterangan,durasi_ist from sc_trx.potongan_um where nodok=vr_dokref);

        delete from sc_trx.rekap_um where dokref=vr_dokref and branch=new.branch;
        delete from sc_trx.master_um where nodok=vr_dokref and branch=new.branch;
        delete from sc_trx.potongan_um where nodok=vr_dokref and branch=new.branch;
        delete from sc_trx.komplembur_um where nodok=vr_dokref and branch=new.branch;
        delete from sc_trx.detail_um where nodok=vr_dokref and branch=new.branch;

    end if;
    return new;

end;
$$;

--DROP TABLE IF EXISTS sc_trx.master_um;
create table sc_trx.master_um
(
    branch     char(6)  not null,
    nik        char(20) not null,
    kdcabang   char(20),
    nodok      char(20) not null,
    dokref     char(20),
    tgl        date     not null,
    status     char(4),
    total      numeric(18, 2),
    uangmkn    numeric(18, 2),
    potongan   numeric(18, 2),
    sewa       numeric(18, 2),
    lembur_um  numeric(18, 2),
    keterangan text,
    primary key (branch, nodok, nik, tgl)
);

alter table sc_trx.master_um
    owner to postgres;

--drop table if exists sc_trx.detail_um;
create table sc_trx.detail_um
(
    branch     char(6)  not null,
    nodok      char(23) not null,
    nik        char(12) not null,
    kdcabang   char(20),
    dokref     char(25),
    tgl        date     not null,
    checkin    time,
    checkout   time,
    nominal    numeric,
    keterangan text,
    status     char(4),
    primary key (branch, nodok, nik, tgl)
);

alter table sc_trx.detail_um
    owner to postgres;









