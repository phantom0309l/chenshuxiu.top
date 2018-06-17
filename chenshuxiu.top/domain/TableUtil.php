<?php

/**
 * 主要用来获取字段存在的表
 * 字段之间的关系，and用下划线_,or则什么都不用,not表示不存在
 * @author fhw
 *
 */
class TableUtil
{
    // 表中有patientid,doctorid,diseaseid
    public static $patientid_doctorid_diseaseid = [];

    // 表中有patientid,diseaseid
    public static $patient_diseaseid = [];

    // 表中有patientid
    public static $patientid = [];

    public static function getEntityClassNameArray_with_patientid () {
        self::init();
        return self::$patientid;
    }

    public static function getEntityClassNameArray_with_patientid_diseaseid () {
        self::init();
        return self::$patient_diseaseid;
    }

    public static function init () {
        $sql = "show tables ";
        $tables = Dao::queryValues($sql);

        $patientid_doctorid_diseaseid = [];
        $patientid_diseaseid = [];
        $patientid = [];
        foreach ($tables as $table) {
            $sql = "show full fields from {$table} ";
            $fields = Dao::queryRows($sql);

            $is_diseaseid = false;
            $is_doctorid = false;
            $is_patientid = false;
            foreach ($fields as $field) {
                if ($field['field'] == 'diseaseid') {
                    $is_diseaseid = true;
                }

                if ($field['field'] == 'doctorid') {
                    $is_doctorid = true;
                }

                if ($field['field'] == 'patientid') {
                    $is_patientid = true;
                }
            }

            $entityType = self::table2entityType($table);
            if ($is_patientid && $is_diseaseid && $is_doctorid) {
                $patientid_doctorid_diseaseid[] = $entityType;
            }

            if ($is_patientid && $is_diseaseid) {
                $patientid_diseaseid[] = $entityType;
            }

            if ($is_patientid) {
                $patientid[] = $entityType;
            }
        }

        self::$patientid_doctorid_diseaseid = $patientid_doctorid_diseaseid;
        self::$patient_diseaseid = $patientid_diseaseid;
        self::$patientid = $patientid;
    }

    private static function table2entityType ($table) {
        global $lowerclasspath;

        $tabl = substr($table, 0, strlen($table) - 1);
        return $lowerclasspath[$tabl];
    }
}
