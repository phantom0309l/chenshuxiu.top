<?php
// Rpt_date_tableMgrAction
class Rpt_date_tableMgrAction extends AuditBaseAction
{

    public function doList () {
        $thedate = XRequest::getValue('thedate', '');
        $tablename = XRequest::getValue('tablename', '');

        $cond = '';
        $bind = [];

        if ($thedate) {
            $cond = " and thedate=:thedate order by rowcnt desc , tablename asc ";
            $bind[':thedate'] = $thedate;
        }

        if ($tablename) {
            $cond = " and tablename=:tablename order by id desc ";
            $bind[':tablename'] = $tablename;
        }

        $rpt_date_tables = Dao::getEntityListByCond('Rpt_date_table', $cond, $bind, 'statdb');

        XContext::setValue('thedate', $thedate);
        XContext::setValue('tablename', $tablename);
        XContext::setValue('rpt_date_tables', $rpt_date_tables);
        return self::SUCCESS;
    }

    public function doSumOfTables () {
        $orderby = XRequest::getValue('orderby', 'max_thedate');

        $orderbyCond = '';
        switch ($orderby) {
            case 'tablename':
                $orderbyCond = 'order by tablename';
                break;
            case 'min_thedate':
                $orderbyCond = 'order by min_thedate asc, tablename';
                break;
            case 'max_thedate':
                $orderbyCond = 'order by max_thedate desc, tablename';
                break;
            default:
                $orderbyCond = 'order by max_thedate desc, tablename';
                break;
        }

        XContext::setValue('orderby', $orderby);

        $sql = "select tablename, min(thedate) as min_thedate, max(thedate) as max_thedate , max(total_rowcnt) as total_rowcnt
            from rpt_date_tables
            where rowcnt>0
            group by tablename
            $orderbyCond";

        $rows = Dao::queryRows($sql, [], 'statdb');
        XContext::setValue('rows', $rows);

        $sql = "select tablename
                from rpt_date_tables
                group by tablename
                order by tablename";
        $rpt_date_tables = Dao::queryValues($sql, [], 'statdb');

        $sql = "show tables";
        $all_tables = Dao::queryValues($sql, []);

        // 表已经不存在了
        $diff1 = array_diff($rpt_date_tables, $all_tables);

        // 没有统计的表
        $diff2 = array_diff($all_tables, $rpt_date_tables);

        XContext::setValue('diff1', $diff1);
        XContext::setValue('diff2', $diff2);

        return self::SUCCESS;
    }
}
