--drop table if exists sc_tmp.uangmakan;
create table if not exists sc_tmp.uangmakan
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

alter table sc_tmp.uangmakan
    owner to postgres;

DROP trigger if exists tr_detail_um ON sc_tmp.uangmakan;
create trigger tr_detail_um
    after insert or update or delete
    on sc_tmp.uangmakan
    for each row
execute procedure sc_tmp.tr_detail_um();

create OR REPLACE function sc_tmp.tr_detail_um() returns trigger
    language plpgsql
as
$$
DECLARE
    --author by fiky: 23/05/2017
    --update by fiky: 23/05/2017
    --title : DETAIL UANG MAKAN uang makan

BEGIN
    IF tg_op = 'INSERT' THEN
        --select * from sc_tmp.master_um
        --select * from sc_tmp.uangmakan
        IF NEW.STATUS='I' THEN
            if not exists(select * from sc_tmp.master_um where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl) then
                insert into sc_tmp.master_um (branch,nik,kdcabang,nodok,dokref,tgl,status,total,uangmkn,potongan,sewa,lembur_um,keterangan)
                values
                    (new.branch,new.nik,new.kdcabang,new.nodok,'',new.tgl,'I',0,0,0,0,0,new.keterangan);
            end if;
            /* update uangmakan ke tabe master*/
            update sc_tmp.master_um set uangmkn=(select sum(nominal) from sc_tmp.uangmakan where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl),keterangan=new.keterangan
            where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl;
            /* update total dari keseluruhan potongan */
            update sc_tmp.master_um set total=(select coalesce(sum(coalesce(sewa,0.0)+coalesce(lembur_um,0.0)+(
                case when coalesce(uangmkn,0.0)-coalesce(potongan,0.0)<0 then 0
                     else coalesce(coalesce(uangmkn,0.0)-coalesce(potongan,0.0),0.0) end)),0.0) from sc_tmp.master_um where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl)
            where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl;
        ELSEIF NEW.STATUS='E' THEN
            /* update uangmakan ke tabe master*/
            update sc_tmp.master_um set uangmkn=(select sum(nominal) from sc_tmp.uangmakan where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl),keterangan=new.keterangan
            where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl;
            /* update total dari keseluruhan potongan */
            update sc_tmp.master_um set total=(select coalesce(sum(coalesce(sewa,0.0)+coalesce(lembur_um,0.0)+(
                case when coalesce(uangmkn,0.0)-coalesce(potongan,0.0)<0 then 0
                     else coalesce(coalesce(uangmkn,0.0)-coalesce(potongan,0.0),0.0) end)),0.0) from sc_tmp.master_um where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl)
            where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl;
        END IF;
        RETURN new;
    ELSEIF tg_op = 'UPDATE'	 THEN
        IF NEW.STATUS='I' THEN
            if not exists(select * from sc_tmp.master_um where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl) then
                insert into sc_tmp.master_um (branch,nik,kdcabang,nodok,dokref,tgl,status,total,uangmkn,potongan,sewa,lembur_um,keterangan)
                values
                    (new.branch,new.nik,new.kdcabang,new.nodok,'',new.tgl,'I',0,0,0,0,0,new.keterangan);
            end if;
            /* update uangmakan ke tabe master*/
            update sc_tmp.master_um set uangmkn=(select sum(nominal) from sc_tmp.uangmakan where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl),keterangan=new.keterangan
            where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl;
            /* update total dari keseluruhan potongan */
            update sc_tmp.master_um set total=(select coalesce(sum(coalesce(sewa,0.0)+coalesce(lembur_um,0.0)+(
                case when coalesce(uangmkn,0.0)-coalesce(potongan,0.0)<0 then 0
                     else coalesce(coalesce(uangmkn,0.0)-coalesce(potongan,0.0),0.0) end)),0.0) from sc_tmp.master_um where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl)
            where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl;
        ELSEIF NEW.STATUS='E' THEN
            /* update uangmakan ke tabe master*/
            update sc_tmp.master_um set uangmkn=(select sum(nominal) from sc_tmp.uangmakan where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl),keterangan=new.keterangan
            where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl;
            /* update total dari keseluruhan potongan */
            update sc_tmp.master_um set total=(select coalesce(sum(coalesce(sewa,0.0)+coalesce(lembur_um,0.0)+(
                case when coalesce(uangmkn,0.0)-coalesce(potongan,0.0)<0 then 0
                     else coalesce(coalesce(uangmkn,0.0)-coalesce(potongan,0.0),0.0) end)),0.0) from sc_tmp.master_um where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl)
            where nik=new.nik and nodok=new.nodok and branch=new.branch and tgl=new.tgl;
        END IF;

        RETURN new;
    ELSEIF tg_op = 'DELETE' THEN
        /*if exists(select * from sc_tmp.master_um where nik=old.nik and nodok=old.nodok and branch=old.branch  and  tgl=old.tgl) then
            update sc_tmp.master_um set potongan=0
            where nik=old.nik and nodok=old.nodok and branch=old.branch and tgl=old.tgl;
        end if; */
        RETURN old;
    END IF;

END;
$$;

-- drop table if exists sc_trx.uangmakan;
create table if not exists sc_trx.uangmakan
(
    branch            char(6),
    nik               char(12) not null,
    tgl               date     not null,
    checkin           time,
    checkout          time,
    nominal           numeric,
    keterangan        text,
    tgl_dok           date,
    dok_ref           char(25),
    rencanacallplan   integer,
    realisasicallplan integer,
    bbm               numeric,
    sewa_kendaraan    numeric,
    primary key (nik, tgl)
);

alter table sc_trx.uangmakan
    owner to postgres;

drop trigger if exists tr_meal_allowance ON sc_trx.uangmakan;
create trigger tr_meal_allowance
    after insert or update
    on sc_trx.uangmakan
    for each row
execute procedure sc_trx.pr_meal_allowance();

CREATE OR REPLACE FUNCTION sc_trx.pr_meal_allowance()
    RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    INSERT INTO sc_trx.meal_allowance
    (
        branch,
        nik,
        tgl,
        checkin,
        checkout,
        nominal,
        keterangan,
        tgl_dok,
        dok_ref,
        rencanacallplan,
        realisasicallplan,
        bbm,
        sewa_kendaraan
    )
    VALUES
        (
            NEW.branch,
            NEW.nik,
            NEW.tgl,
            NEW.checkin,
            NEW.checkout,
            NEW.nominal,
            NEW.keterangan,
            NEW.tgl_dok,
            NEW.dok_ref,
            NEW.rencanacallplan,
            NEW.realisasicallplan,
            NEW.bbm,
            NEW.sewa_kendaraan
        )
    ON CONFLICT (nik, tgl)
        DO UPDATE SET
                      checkin            = EXCLUDED.checkin,
                      checkout           = EXCLUDED.checkout,
                      nominal            = EXCLUDED.nominal,
                      keterangan         = EXCLUDED.keterangan,
                      tgl_dok            = EXCLUDED.tgl_dok,
                      dok_ref            = EXCLUDED.dok_ref,
                      rencanacallplan    = EXCLUDED.rencanacallplan,
                      realisasicallplan  = EXCLUDED.realisasicallplan,
                      bbm                = EXCLUDED.bbm,
                      sewa_kendaraan     = EXCLUDED.sewa_kendaraan;

    RETURN NEW;
END;
$$;





