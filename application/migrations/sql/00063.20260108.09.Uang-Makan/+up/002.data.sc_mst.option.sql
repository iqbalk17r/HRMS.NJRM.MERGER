DELETE FROM sc_mst.option where kdoption IN ('B:U:E:D');
INSERT INTO sc_mst.option (kdoption, nmoption, value1, value2, value3, status, keterangan, input_by, input_date, group_option)
    VALUES
        ('B:U:E:D', 'Branch Unit Exclude Deduction', 'E,F,G,H,I,J,K', null, null, 'T', '', 'SYSTEM', '2026-01-08 00:00:06', 'TRANSREADY')
ON CONFLICT (kdoption,group_option) DO UPDATE
SET
    nmoption = excluded.nmoption,
    value1 = excluded.value1,
    value2 = excluded.value2,
    value3 = excluded.value3,
    status = excluded.status,
    keterangan = excluded.keterangan,
    input_by = excluded.input_by,
    input_date = excluded.input_date,
    group_option = excluded.group_option;
