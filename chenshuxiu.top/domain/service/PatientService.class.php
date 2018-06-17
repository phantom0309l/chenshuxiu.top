<?php

class PatientService {
    public static function getCheckItemsByPatient (Patient $patient) {
        $items = [];

        $medicines = PADRMonitor_AutoService::getMedicines($patient);

        $itemtpls = ADRMonitorRuleItem::getItemTpls();
        foreach ($medicines as $medicine) {
            $adrmonitorruleitems = ADRMonitorRuleItemDao::getGroupListByMedicineidAndDiseaseid($medicine->id, $patient->diseaseid);
            foreach ($adrmonitorruleitems as $item) {

                $firstDrugDate = PADRMonitor_AutoService::getFirstDrugDate($patient->id, $patient->diseaseid, $medicine->id);
                if ($firstDrugDate == false) { // 用药记录不存在
                    continue;
                }

                // 首次用药时间
                $firstDrugDate = substr($firstDrugDate, 0, 10);
                Debug::trace("首次用药日期：{$firstDrugDate}");

                $week = PADRMonitor_AutoService::getWeek($firstDrugDate);
                Debug::trace("当前处于第 {$week} 周");

                $msmritem = ADRMonitorRuleItemDao::getByMedicineidAndDiseaseidAndWeekAndEname($medicine->id, $patient->diseaseid, $week, $item->ename);
                if (false == $msmritem instanceof ADRMonitorRuleItem) {    // 不存在符合当前时间的监测规则
                    Debug::trace("不存在符合当前时间的监测规则");
                    if ($week < 1) {
                        Debug::trace("因为周数小于1，尝试改为1再次获取");
                        $msmritem = ADRMonitorRuleItemDao::getByMedicineidAndDiseaseidAndWeekAndEname($medicine->id, $patient->diseaseid, 1, $item->ename);
                        if (false == $msmritem instanceof ADRMonitorRuleItem) {    // 不存在符合当前时间的监测规则
                            continue;
                        }
                    } else {
                        continue;
                    }
                }

                $items["{$msmritem->ename}"] = $itemtpls["{$msmritem->ename}"];
            }
        }

        return $items;
    }

    public static function getPatientListByDoctorId ($doctorid, $patienttagtplid = 0) {
        if (!$doctorid) {
            return false;
        }
        $mydoctor = Dao::getEntityById('Doctor', $doctorid);
        if (!$mydoctor) {
            return false;
        }

        $diseaseids = $mydoctor->getDiseaseIdArray();

        $list = [];
        foreach ($diseaseids as $diseaseid) {
            $disease = Disease::getById($diseaseid);
            if (false == $disease instanceof Disease) {
                continue;
            }

            $patientbaseinfo_k = FitPageService::getFitPageByCodeDiseaseidDoctorid('patientbaseinfo', $diseaseid, $mydoctor->id);
            $patientpcard_k = FitPageService::getFitPageByCodeDiseaseidDoctorid('patientpcard', $diseaseid, $mydoctor->id);
            $diseasehistory_k = FitPageService::getFitPageByCodeDiseaseidDoctorid('diseasehistory', $diseaseid, $mydoctor->id);

            $patientinfo = JsonFitPageItem::jsonArrayArrayOfFitPageForNewAdmin($patientbaseinfo_k);
            $patientpcard = JsonFitPageItem::jsonArrayArrayOfFitPageForNewAdmin($patientpcard_k);
            $diseasehistory = JsonFitPageItem::jsonArrayArrayOfFitPageForNewAdmin($diseasehistory_k);

            if ($patienttagtplid == 0) {
                $sql = "select DISTINCT a.id
                    from patients a
                    inner join pcards b on b.patientid = a.id
                    where b.doctorid = {$mydoctor->id} and b.diseaseid = {$diseaseid} and a.status=1";
            } else {
                $sql = "select DISTINCT a.id
                    from patients a
                    inner join pcards b on b.patientid = a.id
                    INNER JOIN patienttags c ON b.patientid=c.patientid
                    where b.doctorid = {$mydoctor->id} and b.diseaseid = {$diseaseid} and a.status=1 and c.patienttagtplid={$patienttagtplid}";
            }
            $ids = Dao::queryValues($sql);

            //------------------------------------ patient start ------------------------------------
            $patientbaseinfo = [];

            // 基本信息中json化的字段
            $field_json = ['blood_type', 'children', 'other_contacts'];
            $list["{$disease->name}"] = [];
            foreach ($ids as $id) {
                $patient = Patient::getById($id);

                $other_contactsinfo = [];
                $addresss = [];
                $patient_tmp = [];
                $patient_tmp['id'] = $id;
                foreach ($patientinfo as $a) {
                    $field = $a['code'];
                    if (false !== strpos($field, '_place')) {
                        $patientaddress = PatientAddressService::getPatientAddressByTypePatientid($field, $patient->id);

                        $patient_tmp["{$field}"] = $patientaddress->xprovince->name . $patientaddress->xcity->name . $patientaddress->xcounty->name . $patientaddress->content;
                    } else {
                        if ($field == 'other_contacts') {
                            $other_contacts = JsonLinkman::jsonArray($patient);
                            $other_contactStr = '';
                            if (is_array($other_contacts)) {
                                foreach ($other_contacts as $linkman) {
                                    $other_contactStr .= $linkman['name'] . " " . $linkman['shipstr'] . " " . $linkman['mobile'] . "\n";
                                }
                            }
                            $other_contactStr = rtrim($other_contactStr, "\n");
                            $other_contactStr = self::filterEmoji($other_contactStr);
                            $patient_tmp["{$field}"] = $other_contactStr;
                        } elseif (in_array($field, $field_json)) {
                            $field_jsonArr = json_decode($patient->$field, true);
                            $patient_tmp["{$field}"] = $field_jsonArr['first'] . " " . $field_jsonArr['second'];
                        } else if ($field == 'sex') {
                            $patient_tmp[$field] = $patient->getSexStr();
                        } else {
                            $patient_tmp["{$field}"] = $patient->$field;
                        }
                    }
                }
                //------------------------------------ patient end ------------------------------------

                //------------------------------------ pcard start ------------------------------------
                $pcard = $patient->getPcardByDoctorOrMasterPcard($mydoctor);

                // 非pcard上的字段，只是用来展示
                $notpcard_field = ['masterdoctor', 'hospital'];
                foreach ($patientpcard as $a) {
                    $field = $a['code'];
                    if (false == in_array($field, $notpcard_field)) {
                        $patient_tmp["{$field}"] = $pcard->$field;
                    } elseif ($field == 'masterdoctor') {
                        $patient_tmp["{$field}"] = $mydoctor->name;
                    } elseif ($field == 'hospital') {
                        $patient_tmp["{$field}"] = $mydoctor->hospital->name;
                    }
                }
                //------------------------------------ pcard end ------------------------------------

                //------------------------------------ diseasehistory start ------------------------------------
                $diseasehistory = JsonFitPageItem::jsonArrayArrayOfFitPageForNewAdmin($diseasehistory_k);
                foreach ($diseasehistory as $a) {
                    $field = $a['code'];
                    $json_tmp = json_decode($patient->$field, true);
                    switch ($field) {
                    case 'general_history':
                        $general_history = '';

                        if (! is_array($json_tmp['options'])) {
                            $json_tmp['options'] = [];
                        }
                        $general_history .= implode('、', $json_tmp['options']);
                        $general_history .= $json_tmp['other'] ? "," . $json_tmp['other'] : '';

                        $patient_tmp["{$field}"] = $general_history ? $general_history : '';
                        break;
                    case 'family_history':
                        $family_history = '';
                        $arr = [
                            '癌症' => $json_tmp['options_value']['cancer'],
                            '遗传病史' => $json_tmp['options_value']['yichuan'],
                            '其他' => $json_tmp['options_value']['other']
                        ];

                        if (! is_array($json_tmp['options'])) {
                            $json_tmp['options'] = [];
                        }

                        foreach ($json_tmp['options'] as $family) {
                            $family_history .= $family . ":" . $arr["{$family}"] . "\n";
                        }

                        $family_history = rtrim($family_history, "\n");

                        $patient_tmp["{$field}"] = $family_history ? $family_history : '';
                        break;
                    case 'menstruation_history':
                        $menstruation_history = '';
                        if ($patient->sex == 2) {
                            $arr = [
                                'theAgeOfFirstMS' => '初次月经',
                                'MSStatus' => '月经状况',
                                'MSPeriod' => '月经周期',
                                'stopReason' => '停经原因',
                                'stopAge' => '停经年龄',
                                'diseaseReason' => '病理原因'
                            ];

                            if ($json_tmp['theAgeOfFirstMS']) {
                                $menstruation_history .= $arr['theAgeOfFirstMS'] . ":" . $json_tmp['theAgeOfFirstMS'] . "\n";
                            }

                            if ($json_tmp['MSStatus']) {
                                $menstruation_history .= $arr['MSStatus'] . ":" . $json_tmp['MSStatus'] . "\n";
                            }

                            if ($json_tmp['MSStatus'] == '正常') {
                                $menstruation_history .= $arr['MSPeriod'] . ":" . $json_tmp['MSPeriod']['lastDays'] . "/" . $json_tmp['MSPeriod']['SumDays'] . "\n";
                            } elseif($json_tmp['MSStatus'] == '停经') {
                                $menstruation_history .= $arr['stopReason'] . ":" . $json_tmp['stopReason'] . "\n";

                                if ($json_tmp['stopReason'] == '生理性') {
                                    $menstruation_history .= $arr['stopAge'] . ":" . $json_tmp['stopAge'] . "\n";
                                } elseif ($json_tmp['stopReason'] == '病理性') {
                                    $menstruation_history .= $arr['diseaseReason'] . ":" . $json_tmp['diseaseReason'] . "\n";
                                }
                            }
                        }

                        $patient_tmp["{$field}"] = $menstruation_history ? $menstruation_history : '';
                        break;
                    case 'childbearing_history':
                        $childbearing_history = '';
                        if ($patient->sex == 2) {
                            $str = "";
                            if ($json_tmp['yun']) {
                                $str .= "孕{$json_tmp['yun']}\n";
                            }
                            if ($json_tmp['chan']) {
                                $str .= "产{$json_tmp['chan']}\n";
                            }
                            if ($json_tmp['yunDates']) {
                                $str .= "怀孕时间{$json_tmp['yunDates']}\n";
                            }
                            if ($json_tmp['chanDates']) {
                                $str .= "生育时间{$json_tmp['chanDates']}\n";
                            }
                            $childbearing_history .= $str;
                        }

                        $patient_tmp["{$field}"] = $childbearing_history ? $childbearing_history : '';
                        break;
                    case 'smoke_history':
                        $smoke_history = '';

                        if ($json_tmp['is'] == '是') {
                            $smoke_history .= "环境接触:" . implode('、', $json_tmp['environment']['options']) . " " . $json_tmp['environment']['other'] . "\n";
                            $smoke_history .= "吸烟指数:" . $json_tmp['referenceIndex']['day'] . "支/天  X " . $json_tmp['referenceIndex']['year'] . "年 = " . $json_tmp['referenceIndex']['result'] . "\n";
                        } else {
                            $smoke_history .= "";
                        }

                        $patient_tmp["{$field}"] = $smoke_history;
                        break;
                    case 'drink_history':
                        $drink_history = '';

                        if ($json_tmp['is'] == '是') {
                            $drink_history .= $json_tmp['content'];
                        }

                        $patient_tmp["{$field}"] = $drink_history ? $drink_history : '';
                        break;
                    case 'trauma_history':
                        $trauma_history = '';

                        if ($json_tmp['sergery']['is'] == '是') {
                            $trauma_history .= "手术:" . $json_tmp['sergery']['content'] . "\n";
                        } else {
                            $trauma_history .= "手术:" . "\n";
                        }

                        if ($json_tmp['bloodTrans']['is'] == '是') {
                            $trauma_history .= "输血:" . $json_tmp['bloodTrans']['content'] . "\n";
                        } else {
                            $trauma_history .= "输血:" . "\n";
                        }

                        if ($json_tmp['trauma']['is'] == '是') {
                            $trauma_history .= "外伤:" . $json_tmp['trauma']['content'] . "\n";
                        } else {
                            $trauma_history .= "外伤:" . "\n";
                        }

                        $patient_tmp["{$field}"] = $trauma_history;
                        break;
                    case 'infect_history':
                        $infect_history = '';

                        if (is_array($json_tmp['options'])) {
                            $infect_history .= implode(',', $json_tmp['options']);
                            $infect_history .= $json_tmp['other'] ? ',' . $json_tmp['other'] : '';
                        }

                        $patient_tmp["{$field}"] = $infect_history ? $infect_history : '';
                        break;
                    case 'special_contact_history':
                        $special_contact_history = '';

                        if ($json_tmp['epidArea']['is'] == '是') {
                            $special_contact_history .= "疫区接触史:" . $json_tmp['epidArea']['content'] . "\n";
                        } else {
                            $special_contact_history .= "疫区接触史:" . "\n";
                        }

                        if ($json_tmp['epidWater']['is'] == '是') {
                            $special_contact_history .= "疫水接触史:" . $json_tmp['epidWater']['content'] . "\n";
                        } else {
                            $special_contact_history .= "疫水接触史:" . "\n";
                        }

                        if ($json_tmp['chemical']['is'] == '是') {
                            $special_contact_history .= "化学物质接触史:" . $json_tmp['chemical']['content'] . "\n";
                        } else {
                            $special_contact_history .= "化学物质接触史:" . "\n";
                        }

                        if ($json_tmp['radioactive']['is'] == '是') {
                            $special_contact_history .= "放射物质接触史:" . $json_tmp['radioactive']['content'] . "\n";
                        } else {
                            $special_contact_history .= "放射物质接触史:";
                        }

                        $patient_tmp["{$field}"] = $special_contact_history;
                        break;
                    case 'allergy_history':
                        $allergy_history = '';

                        if ($json_tmp['foodAllergy']['is'] == '是') {
                            $allergy_history .= "食物过敏:" . $json_tmp['foodAllergy']['content'] . "\n";
                        } else {
                            $allergy_history .= "食物过敏:" . "\n";
                        }

                        if ($json_tmp['medicineAllergy']['is'] == '是') {
                            $allergy_history .= "药物过敏:" . $json_tmp['medicineAllergy']['content'] . "\n";
                        } else {
                            $allergy_history .= "药物过敏:" . "\n";
                        }

                        $patient_tmp["{$field}"] = $allergy_history;
                        break;
                    default:
                        break;
                    }
                }

                $list["{$disease->name}"][] = $patient_tmp;
            }
        }

        return $list;
    }

    private function filterEmoji($str)
    {
        $str = preg_replace_callback( '/[\xf0-\xf7].{3}/', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $str);

        return $str;
    }
}
