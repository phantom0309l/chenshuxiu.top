<?php
/*
 * PatientStageDao
 */
class PatientStageDao extends Dao
{
    public static function getByTitle($title) {
        $cond = " and title = :title ";
        $bind = [
            ':title' => $title
        ];

        return Dao::getEntityByCond('PatientStage', $cond, $bind);
    }
}
