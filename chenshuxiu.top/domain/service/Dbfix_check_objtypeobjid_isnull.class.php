<?php

// 线上数据库, objtype,objid 数据完整性检查
class Dbfix_check_objtypeobjid_isnull extends DbFixBase
{

    private $arrForNullTable = array();

    private $arrForNullObjtype = array();

    public function doWork () {

        // 删除测试用户的null patient的流
        $deletePipesOfNullPaitentOfAuditors = $this->deletePipesOfNullPaitentOfAuditors();

        $jumpTables = array(
            'doctordboplogs',  // 不需要处理
            'xanswersheets',  // 暂不处理
            'wxqrcodes',  // 暂不处理,
            'pushmsgs',  // 暂不处理
            'pictures',  // 不需要处理
            'picturerefs'); // 可以删除,暂不处理

        // 不需要处理
        $jumpTables_nocheck = [
            'comments',
            'doctordboplogs',
            'pictures'];

        // 可以暂不处理
        $jumpTables = [];

        // 获取有字段:objtype,objid的表
        $tables = $this->getObjtypeObjidTables();

        // 先过一遍
        foreach ($tables as $a) {

            // 这些表不检查了
            if (in_array($a, $jumpTables_nocheck)) {
                Debug::log("== {$a} == continue == ");
                continue;
            }

            // 这些表一会再查
            if (in_array($a, $jumpTables)) {
                Debug::log("== {$a} == continue == ");
                continue;
            }

            $cnt = $this->checkObjtypeObjid_checkOneTable($a);

            // 一个一个表处理
            if ($cnt > 0) {
//                 Debug::log("== {$a} == break == ");
//                 break;
            }
        }

        // 二次处理
        foreach ($jumpTables as $a) {
            $cnt = $this->checkObjtypeObjid_checkOneTable($a);
        }

        // 返回结果
        $result = array();
        $result['deletePipesOfNullPaitentOfAuditors'] = $deletePipesOfNullPaitentOfAuditors;
        $result['jumpTables_nocheck'] = $jumpTables_nocheck;
        $result['jumpTables'] = $jumpTables;
        $result['arrForNullTable'] = $this->arrForNullTable;
        $result['arrForNullObjtype'] = $this->arrForNullObjtype;

        return $result;
    }

    // 删除,测试用户,null patient 的流
    private function deletePipesOfNullPaitentOfAuditors () {
        $sql = "select a.*
            from pipes a
            left join patients b on b.id=a.objid
            where a.objtype='Patient' and a.objid > 0 and b.id is null and a.userid > 10000 and a.userid < 20000";

        $pipes = Dao::loadEntityList('Pipe', $sql, []);

        $cnt = count($pipes);

        XContext::setValue('deletePipeCntOfNullPaitentOfAuditors', $cnt);

        // echo "\npipe->remove() cnt={$cnt}\n";

        $unitofwork = BeanFinder::get("UnitOfWork");

        $i = 0;
        foreach ($pipes as $a) {
            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
                // echo "{$i}/{$cnt}";
            }
            // 删除
            $a->remove();
        }

        $unitofwork->commitAndInit();

        return array(
            'sql' => $sql,
            'cnt' => $cnt);
    }

    // 检查 one table
    private function checkObjtypeObjid_checkOneTable ($tablea) {
        $sql = "select objtype from {$tablea} group by objtype";
        $objtypes = Dao::queryValues($sql, []);

        $cnt = 0;
        foreach ($objtypes as $objtype) {

            if (in_array($objtype, array())) {
                continue;
            }

            $cnt += $this->fixObjidIfObjIdIsNull($tablea, $objtype);
        }

        return $cnt;
    }

    private function fixObjidIfObjIdIsNull ($tablea, $objtype, $isExe = false) {
        $tableb = strtolower($objtype);
        $tableb .= 's';

        if (false == in_array($tableb, $this->getAllTables())) {
            $this->arrForNullTable[] = array(
                'table' => $tablea,
                'objtype' => $objtype);
            // echo "\n\n====== nulltable : {$tablea} objtype='{$objtype}'
            // =======\n\n";
            return 0;
        }

        // if b.id is null then delete a.*
        $sql = "select count(*) as cnt
from {$tablea} a
left join {$tableb} b on b.id=a.objid
where a.objtype='{$objtype}' and a.objid > 0 and b.id is null";

        $cnt = Dao::queryValue($sql, []);
        if ($cnt > 0) {
            $this->arrForNullObjtype[] = array(
                'table' => $tablea,
                'objtype' => $objtype,
                'cnt' => $cnt,
                'sql' => $sql);
            // echo "\n\n====== need update =======\n\n";
        }

        return $cnt;
    }
}
