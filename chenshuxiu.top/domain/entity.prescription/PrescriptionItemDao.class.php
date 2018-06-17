<?php

/*
 * PrescriptionItemDao
 */
class PrescriptionItemDao extends Dao
{

    public static function getPrescriptionItemsByPrescription (Prescription $prescription) {
        $cond = 'and prescriptionid=:prescriptionid';

        $bind = [];
        $bind[':prescriptionid'] = $prescription->id;

        return Dao::getEntityListByCond('PrescriptionItem', $cond, $bind);
    }
}