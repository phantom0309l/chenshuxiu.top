<?php

/*
 * ActivityDao
 */

class ClinicalTestDao extends Dao
{
    public static function getAll($condEx = "") {
        return Dao::getEntityListByCond('ClinicalTest', $condEx);
    }
}