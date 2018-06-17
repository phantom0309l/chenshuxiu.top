<?php

class DrugItemMgrAction extends AuditBaseAction
{

    // form新增drugitem
    public function doAddPost () {
        $drugsheetid = XRequest::getValue("drugsheetid", 0);
        $medicineid = XRequest::getValue("medicineid", 0);
        $value = XRequest::getValue("value", 0);

        $drugsheet = DrugSheet::getById($drugsheetid);
        if (false == $drugsheet instanceof DrugSheet) {
            return self::SUCCESS;
        }

        $medicine = Medicine::getById($medicineid);
        if ($medicine instanceof Medicine) {
            $thedate = $drugsheet->thedate;
            $patient = $drugsheet->patient;
            $ids = $patient->get5id();

            // 生成patientmedicineref如果没有
            // 对应修改patientmedicineref
            // 生成一条drugitem

            $patientmedicineref = $patient->getRefWithMedicine($medicine, true);
            $patientmedicineref->updateStatusByValueThedate($value, $thedate);

            $auditor = $this->myauditor;
            $row = array();
            $row['drugsheetid'] = $drugsheet->id;
            $row['medicineid'] = $medicine->id;
            $row['type'] = 1;
            $row['value'] = $value;
            $row['record_date'] = $thedate;
            $row['auditorid'] = $auditor->id;
            $row += $ids;
            $drugitem = DrugItem::createByBiz($row);
            $drugsheet->is_nodrug = 0;
        }
        XContext::setJumpPath("/drugsheetmgr/updatedrugitems?drugsheetid={$drugsheetid}");
        return self::SUCCESS;
    }

    public function doModifyNewJson () {
        $drugitemid = XRequest::getValue("drugitemid", 0);
        $value = XRequest::getValue("value", 0);
        $drug_frequency = XRequest::getValue("drug_frequency", "");
        $content = XRequest::getValue("content", "");

        $drugitem = DrugItem::getById($drugitemid);

        $auditor = $this->myauditor;

        $drugitem->value = $value;
        $drugitem->drug_frequency = $drug_frequency;
        $drugitem->content = $content;
        $drugitem->auditorid = $auditor->id;

        $medicine = $drugitem->medicine;
        $patient = $drugitem->patient;
        if ($medicine instanceof Medicine && $patient instanceof Patient) {
            $thedate = $drugitem->drugsheet->thedate;
            $patientmedicineref = $patient->getRefWithMedicine($medicine, true);
            $patientmedicineref->updateStatusByValueThedate($value, $thedate);
        }
        echo "ok";
        return self::BLANK;
    }

    public function doDeleteJson () {
        $drugitemid = XRequest::getValue("drugitemid", 0);
        $drugitem = DrugItem::getById($drugitemid);
        if ($drugitem instanceof DrugItem) {
            // 判断有没有drugsheet，有的话判断drugsheet对应的drugitem是否是1条，如果是则也删除drugsheet
            $drugsheet = $drugitem->drugsheet;
            if ($drugsheet instanceof DrugSheet) {
                $drugitems_by_drugsheet = $drugsheet->getDrugItems();
                if (count($drugitems_by_drugsheet) == 1) {
                    $drugsheet->remove();

                    // 同时删除drugsheet对应的流
                    $pipe = PipeDao::getByEntity($drugsheet);
                    if ($pipe instanceof Pipe) {
                        $pipe->remove();
                    }

                    // 任务删除: 同时删除drugsheet对应的optask
                    OpTaskService::removeAllOpTasksByObj($drugsheet);
                }
            }
            // 如果总共只有一条drugitem 则也要删除patientmedicineref
            $patientid = $drugitem->patientid;
            $medicineid = $drugitem->medicineid;
            $drugitems = DrugItemDao::getListByPatientidMedicineid($patientid, $medicineid);
            $patientmedicineref = PatientMedicineRefDao::getByPatientidMedicineid($patientid, $medicineid);
            if (count($drugitems) == 1) {
                $patientmedicineref->remove();
            } elseif ($this->mydisease->id > 1) {
                $drugitems = array_reverse($drugitems);
                if ($drugitems[0]->id == $drugitemid) {
                    $patientmedicineref->first_start_date = $drugitems[1]->record_date;
                } else {
                    $patientmedicineref->first_start_date = $drugitems[0]->record_date;
                }
            }
            $drugitem->remove();
        }

        echo "ok";
        return self::BLANK;
    }
}
