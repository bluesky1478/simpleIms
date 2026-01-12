SELECT count(1) as cnt
 FROM (
SELECT DISTINCT a.*
 FROM sl_imsTodoRequest a
 JOIN sl_imsTodoResponse b
   ON a.sno = b.reqSno
WHERE approvalStatus='proc'
  AND a.todoType='approval'
  AND
    (
        b.managerSno={mSno}
        OR a.regManagerSno={mSno}
    )
  AND a.delFl = 'n'
) a
