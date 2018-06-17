<?php

// 线上数据库, 各表外键的数据完整性检查
class Dbfix_check_xxid_isnull extends DbFixBase
{

    private $i = 0;

    private $table_arr = array();

    private $table_ids_arr = array();

    private $table_objid_arr = array();

    public function doWork () {
        echo '未实行dowork';
        exit();
    }

    // 初始化分析各个表的外键字段情况
    public function initThreeArray () {
        $tables = $this->getAllTables();

        // 预处理
        foreach ($tables as $a) {
            $this->anaOneTable($a);
        }
    }

    // doCheckXxidIsNull
    public function doCheckXxidIsNull () {
        $i = 0;

        $cnt_sum = 0;

        $jump_tables = [
            'doctordboplogs'];

        $table_arr = array();
        foreach ($this->table_arr as $tablea => $arr) {

            if (in_array($tablea, $jump_tables)) {
                continue;
            }

            $i ++;
            foreach ($arr as $field => $tableb) {
                $sql = "select count(*) as cnt
from $tablea a
left join $tableb b on b.id =  a.{$field}
where a.{$field} > 0 and b.id is null";

                // $cnt = $this->queryCntSql($sql);

                $cnt_sum += $cnt = Dao::queryValue($sql, []);
                if ($cnt > 0) {
                    $table_arr[$tablea][] = array(
                        'field' => $field,
                        'tableb' => $tableb,
                        'cnt' => $cnt,
                        'sql' => $sql);
                }
            }

            if ($cnt_sum > 100) {
                // break;
            }
        }

        return $table_arr;
    }

    // doCheckXxidsIsNull
    public function doCheckXxidsIsNull () {
        $table_ids_arr = $this->table_ids_arr;

        $result = array();
        $result['table_ids_arr'] = $table_ids_arr;
        $result['table_field_rows'] = array();

        foreach ($table_ids_arr as $tablea => $arr) {
            foreach ($arr as $a) {

                if ($a['field'] == 'see_patienttagtplids') {
                    continue;
                }

                $field = $a['field'];
                $tableb = $a['tableb'];
                $result['table_field_rows'][$tablea][$field] = $this->checkXxidsIsNullOneField($tablea, $field, $tableb);
            }
        }

        // echo "<pre>";
        // print_r($result['table_field_rows']);
        // exit();

        return $result;
    }

    // checkXxidsIsNullOneField
    protected function checkXxidsIsNullOneField ($tablea, $field, $tableb) {
        $sql = "select id, createtime, {$field} from {$tablea} where {$field}<>'' ";
        $rows = Dao::queryRows($sql, []);

        $cnt = count($rows);

        $arr = array();
        $arr['field'] = $field;
        $arr['tableb'] = $tableb;
        $arr['sql'] = $sql;
        $arr['cnt'] = $cnt;
        $arr['needfix'] = array();

        foreach ($rows as $row) {
            $ida = $row['id'];
            $str = $row[$field];

            $ids = explode(',', $str);
            foreach ($ids as $id) {
                $id = trim($id);
                if (empty($id)) {
                    continue;
                }

                $sqlCheck = "select count(*) as cnt from $tableb where id =:id ";
                $bind = array(
                    ':id' => $id);
                $entityCnt = 0 + Dao::queryValue($sqlCheck, $bind);
                // 数据不存在
                if ($entityCnt < 1) {
                    // echo "\n $tablea [$ida] [$field] [$str] => $tableb [$id]
                    // -----";

                    $tmp = array();
                    $tmp['ida'] = $ida;
                    $tmp['str'] = $str;
                    $tmp['idb'] = $id;
                    $tmp['entityCnt'] = $entityCnt;

                    $arr['needfix'][] = $tmp;
                } else {

                    // 数据存在
                    // $tmp = array();
                    // $tmp['ida'] = $ida;
                    // $tmp['str'] = $str;
                    // $tmp['idb'] = $id;
                    // $tmp['entityCnt'] = $entityCnt;

                    // $arr['needfix'][] = $tmp;
                }
            }
        }

        return $arr;
    }

    // anaOneTable
    protected function anaOneTable ($table) {
        $dbExecuter = BeanFinder::get("DbExecuter");

        $sql = "show full fields from {$table}";
        $rows = $dbExecuter->query($sql);

        $jumpFields = array(
            'nextid',
            'id',
            'patientcard_id',  // 就诊卡上的患者ID
            'xhotelid',
            'xroomid',
            'xorderid',
            'xcustomerid');

        $ignoreFields = array(
            'version',
            'createtime',
            'updatetime',  // 就诊卡上的患者ID
            'name',
            'title',
            'content',
            'sn',
            'code',
            'type',
            'typestr',
            'groupstr',
            'status',
            'auditstatus',
            'remark');

        // 白名单
        $ids_table_list = array();
        $ids_table_list['patients_referencing'] = 'doctors';

        foreach ($rows as $a) {
            $field = $a['field'];
            $type = $a['type'];

            // 忽略字段
            if (in_array($field, $jumpFields)) {
                continue;
            }

            // 忽略字段
            if (in_array($field, $ignoreFields)) {
                continue;
            }

            if (strpos($type, 'bigint') === 0) {
                // echo "\n$table $field";

                if ($field == 'objid') {
                    // 字段=objid
                    $this->table_objid_arr[$table][] = $field;
                } elseif (strpos($field, 'objid') > 0) {
                    // 字段含有objid
                    $this->table_objid_arr[$table][] = $field;
                } elseif (strpos($field, 'id') > 0) {
                    // 字段含有id
                    $tableb = $this->field2tableName($field);
                    if ($tableb) {
                        $this->table_arr[$table][$field] = $tableb;
                    }
                } else {
                    // echo "\n$table $field";
                }
            } elseif (strpos($type, 'int') === 0) {
                //
            } elseif (strpos($type, 'tinyint') === 0) {
                //
            } elseif (strpos($type, 'double') === 0) {
                //
            } elseif (strpos($type, 'float') === 0) {
                //
            } elseif (strpos($type, 'datetime') === 0) {
                //
            } elseif (strpos($type, 'date') === 0) {
                //
            } else {
                $this->i ++;
                // echo "\n{$this->i} == $table {$type} == [ $field ] ";

                // 重置
                $tableb = '';

                // 白名单
                if ($ids_table_list[$field]) {
                    $tableb = $ids_table_list[$field];
                } elseif (strpos($field, 'ids') > 0) {
                    // 字段含有ids
                    $tableb = $this->field2tableName($field);
                }

                if ($tableb) {
                    $this->table_ids_arr[$table][] = array(
                        'field' => $field,
                        'tableb' => $tableb);
                }
            }
        }
    }
}
