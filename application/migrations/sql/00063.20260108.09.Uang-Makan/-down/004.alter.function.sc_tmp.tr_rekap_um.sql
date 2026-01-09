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
