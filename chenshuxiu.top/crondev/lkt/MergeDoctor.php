123<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/5/11
 * Time: 10:50
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class MergeDoctor
{

    public function doWork() {
        fwrite(STDOUT, "from_doctorid：");
        $from_doctorid = trim(fgets(STDOUT));
        $from_doctor = Doctor::getById($from_doctorid);

        if ($from_doctor instanceof Doctor) {
            echo "from_doctor_name：" . $from_doctor->name;
            echo "\n";
            echo "from_doctor_username：" . $from_doctor->user->username;
            echo "\n\n";
            fwrite(STDOUT, "to_doctorid：");

            $to_doctorid = trim(fgets(STDOUT));
            $to_doctor = Doctor::getById($to_doctorid);

            if ($to_doctor instanceof Doctor) {
                echo "to_doctor_name：" . $to_doctor->name;
                echo "\n";
                echo "to_doctor_username：" . $to_doctor->user->username;
                echo "\n\n";
                fwrite(STDOUT, "是否将{$from_doctorid}替换为{$to_doctorid} {y/n}：");
                $result = trim(fgets(STDOUT));
                if ($result === "y") {
                    $this->to_doctorid($from_doctor, $to_doctor);
                } else {
                    exit();
                }
            } else {
                echo "找不到该医生\n";
            }
        } else {
            echo "找不到该医生\n";
        }
    }

    // 修改各个表中doctorid
    // $from_doctor 目标 $to_doctor 替换
    private function to_doctorid(Doctor $from_doctor, Doctor $to_doctor) {
        $from_doctorid = $from_doctor->id;
        $to_doctorid = $to_doctor->id;

        // 忽略
        $ignore_table_arr = [
            ''];

        $sql = "show tables";
        $tables = Dao::queryValues($sql, []);

        $tables_doctorid = [];
        $tables_objid = [];

        foreach ($tables as $table) {
            if (in_array($table, $ignore_table_arr)) {
                continue;
            }

            $sql = "show full fields from {$table}";

            $fields = Dao::queryRows($sql, []);

            foreach ($fields as $field) {
                if ($field['field'] == 'doctorid') {
                    $tables_doctorid[] = $table;
                }
                if ($field['field'] == 'objid') {
                    $tables_objid[] = [
                        'table' => $table,
                        'type_field' => 'objtype',
                        'id_field' => 'objid'
                    ];
                }
                if ($field['field'] == 'ref_objid') {
                    $tables_objid[] = [
                        'table' => $table,
                        'type_field' => 'ref_objtype',
                        'id_field' => 'ref_objid'
                    ];
                }
                if ($field['field'] == 'send_by_objid') {
                    $tables_objid[] = [
                        'table' => $table,
                        'type_field' => 'send_by_objtype',
                        'id_field' => 'send_by_objid'
                    ];
                }
                if ($field['field'] == 'ownerid') {
                    $tables_objid[] = [
                        'table' => $table,
                        'type_field' => 'ownertype',
                        'id_field' => 'ownerid'
                    ];
                }

            }
        }

        $unitofwork = BeanFinder::get("UnitOfWork");
        $affected_records = [];
        $affected_table_cnts = [];
        $affected_table_objcnts = [];

        $i = 0;
        // 修改 from_doctor_id => to_doctor_id
        foreach ($tables_doctorid as $tablename) {
            $entityType = $this->table2entityType($tablename);
            if (null == $entityType) {
                continue;
            }

            $cond = " and doctorid=:doctorid ";
            $bind = array(
                ':doctorid' => $from_doctorid);

            $entitys = Dao::getEntityListByCond($entityType, $cond, $bind);

            $affected_table_cnts[$tablename] = count($entitys);

            $arr = [];
            foreach ($entitys as $entity) {
                // 避免违反约束
                if ($tablename == 'pcards') {
                    $pcard = PcardDao::getByPatientidDoctorid($entity->patientid, $to_doctorid);
                    if ($pcard instanceof Pcard) {
                        continue;
                    }
                } elseif ($tablename == 'doctormedicinerefs') {
                    $doctorMedicineRef = DoctorMedicineRefDao::getByDoctoridMedicineid($to_doctorid, $entity->medicineid);
                    if ($doctorMedicineRef instanceof DoctorMedicineRef) {
                        echo "\n跳过 {$tablename} id = {$entity->id}";
                        continue;
                    }
                } elseif ($tablename == 'doctordiseaserefs') {
                    $doctorDiseaseRef = DoctorDiseaseRefDao::getByDoctoridDiseaseid($to_doctorid, $entity->diseaseid);
                    if ($doctorDiseaseRef instanceof DoctorDiseaseRef) {
                        echo "\n跳过 {$tablename} id = {$entity->id}";
                        continue;
                    }
                } elseif ($tablename == 'doctorwxshoprefs') {
                    $cond = ' and doctorid = :doctorid and wxshopid = :wxshopid ';
                    $bind = [];
                    $bind[':doctorid'] = $to_doctorid;
                    $bind[':wxshopid'] = $entity->wxshopid;

                    $doctorwxshoprefs = Dao::getEntityByCond('DoctorWxShopRef', $cond, $bind);
                    if ($doctorwxshoprefs instanceof DoctorWxShopRef) {
                        echo "\n跳过 {$tablename} id = {$entity->id}";
                        continue;
                    }
                }

                $entity->set4lock('doctorid', $to_doctorid);
                $arr[] = $entity->id;
                $i++;
            }
            if (!empty($arr)) {
                $affected_records[$tablename] = $arr;
            }
        }

        // objtype = Doctor , 修正 objid
        foreach ($tables_objid as $item) {
            $tablename = $item['table'];
            $type_field = $item['type_field'];
            $id_field = $item['id_field'];

            $entityType = $this->table2entityType($tablename);
            if (null == $entityType) {
                continue;
            }

            $cond = " and {$type_field}=:objtype and {$id_field}=:objid ";
            $bind = [
                ':objtype' => 'Doctor',
                ':objid' => $from_doctorid
            ];

            $entitys = Dao::getEntityListByCond($entityType, $cond, $bind);

            if (count($entitys) > 0) {
                $affected_table_objcnts[$tablename] = count($entitys);
            }

            $arr = [];
            foreach ($entitys as $entity) {
                $entity->set4lock($id_field, $to_doctorid);
                $arr[] = $entity->id;
                $i++;
            }
            if (!empty($arr)) {
                $affected_records[$tablename] = $arr;
            }
        }

        $unitofwork->commitAndInit();
        $result = json_encode($affected_records);
        $error_file = fopen("mergeDoctor_result.txt", "w+");

        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        fwrite($error_file, $json);
        fclose($error_file);

        echo "\n\n影响行数：{$i}";

        echo "\n\nDone\n\n";

        asort($affected_table_cnts);

        print_r($affected_table_cnts);
        print_r($affected_table_objcnts);
    }

    private function table2entityType($table) {
        global $lowerclasspath;

        $tabl = substr($table, 0, strlen($table) - 1);
        return $lowerclasspath[$tabl];
    }
}

$mergeDoctor = new MergeDoctor();
$mergeDoctor->doWork();
