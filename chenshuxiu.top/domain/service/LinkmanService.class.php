<?php

/**
 * Created by PhpStorm.
 * User: fanghanwen
 * Date: 2018/1/19
 * Time: 14:00
 */
class LinkmanService
{

    public static function getMasterLinkman(Patient $patient) {
        $master_linkman = LinkmanDao::getMasterByPatientid($patient->id);

        // 没有主联系人就创建一个，或者把一个备用的设置为主联系人
        if (false == $master_linkman instanceof Linkman) {
            $row = [];
            $row["userid"] = $patient->getOneUserId();
            $row["patientid"] = $patient->id;
            $row["name"] = '';
            $row["shipstr"] = '';
            $row["mobile"] = '';
            $row["is_master"] = 1;
            $master_linkman = Linkman::createByBiz($row);
        }

        return $master_linkman;
    }

    public static function getPhoneLinkman(Patient $patient) {
        $phone_linkman = LinkmanDao::getOtherByPatientid($patient->id);

        if (false == $phone_linkman instanceof Linkman) {
            $row = [];
            $row["userid"] = $patient->getOneUserId();
            $row["patientid"] = $patient->id;
            $row["mobile"] = '';
            $row["is_master"] = 0;
            $phone_linkman = Linkman::createByBiz($row);

        }

        return $phone_linkman;
    }

    public static function updateForAdmin_doctor(Patient $patient, $linkman_rows, $mobile) {
        $mobile = $mobile ?? '';

        // 主联系人
        LinkmanService::updateMasterMobile($patient, $mobile);

        // 备用联系人
        if (empty($linkman_rows)) {
            // 如果数组为空，说明要删除备用联系人
            $linkmans = LinkmanDao::getOtherListByPatientid($patient->id);

            foreach ($linkmans as $linkman) {
                XPatientIndex::deleteXpatientIndexMobile($patient, $linkman->mobile);
                $linkman->remove();
            }
        } else {
            $sql = "select id from linkmans where patientid = :patientid and is_master = 0 ";
            $bind = [
                ':patientid' => $patient->id
            ];
            $old_ids = Dao::queryValues($sql, $bind);

            $new_ids = [];
            foreach ($linkman_rows as $row) {
                if ($row['id']) {
                    $new_ids[] = $row['id'];

                    $linkman = Linkman::getById($row['id']);

                    $linkman->name = $row['name'];
                    $linkman->shipstr = $row['shipstr'];

                    if ($linkman->mobile != $row['mobile'] && $row['mobile'] != $mobile) {
                        XPatientIndex::deleteXpatientIndexMobile($patient, $linkman->mobile);

                        $linkman->mobile = $row['mobile'];
                        XPatientIndex::addXPatientIndexMobile($patient, $linkman->mobile);
                    }
                } else {
                    if ($row['mobile'] != $mobile) {
                        $linkman_row = [];
                        $linkman_row['userid'] = $patient->getOneUserId();
                        $linkman_row['patientid'] = $patient->id;
                        $linkman_row['name'] = $row['name'];
                        $linkman_row['shipstr'] = $row['shipstr'];
                        $linkman_row['mobile'] = $row['mobile'];
                        $linkman_row['is_master'] = 0;
                        $linkman = Linkman::createByBiz($linkman_row);

                        XPatientIndex::addXPatientIndexMobile($patient, $linkman->mobile);
                    }
                }
            }

            // 删除
            foreach ($old_ids as $old_id) {
                if (false == in_array($old_id, $new_ids)) {
                    $linkman = Linkman::getById($old_id);
                    if ($linkman instanceof Linkman) {
                        XPatientIndex::deleteXpatientIndexMobile($patient, $linkman->mobile);
                        $linkman->remove();
                    }
                }
            }
        }
    }

    // wx端
    public static function updateByUserMobile(User $user, $mobile) {
        if (empty($mobile)) {
            return;
        }

        $linkman = LinkmanDao::getByUseridMobile($user->id, $mobile);

        if (false == $linkman instanceof Linkman) {
            $linkman = LinkmanDao::getByPatientidMobile($user->patientid, $mobile);

            if ($linkman instanceof Linkman && $linkman->userid == 0) {
                $linkman->userid = $user->id;
            } else {
                $master_linkman = LinkmanDao::getMasterByPatientid($user->patientid);

                $row = [];
                $row["userid"] = $user->id;
                $row["patientid"] = $user->patientid;
                $row["name"] = $user->name;
                $row["shipstr"] = $user->shipstr;
                $row["mobile"] = $mobile;
                $row["is_master"] = $master_linkman instanceof Linkman ? 0 : 1;
                $linkman = Linkman::createByBiz($row);

                XPatientIndex::addXPatientIndexMobile($user->patient, $mobile);
            }
        }
    }

    public static function updateMasterMobile(Patient $patient, $mobile, $userid = 0, $name = '', $shipstr = '') {
        $master_linkman = LinkmanService::getMasterLinkman($patient);

        if ($master_linkman->mobile != $mobile) {
            XPatientIndex::deleteXpatientIndexMobile($patient, $master_linkman->mobile);

            $master_linkman->mobile = $mobile;
            XPatientIndex::addXPatientIndexMobile($patient, $master_linkman->mobile);
        }

        if ($userid) {
            $master_linkman->userid = $userid;
        }

        if ($name) {
            $master_linkman->name = $name;
        }

        if ($shipstr) {
            $master_linkman->shipstr = $shipstr;
        }
    }

    // 患者wx端修改基本信息：修改备用联系人
    public static function updateOtherPhone(Patient $patient, $mobile, $userid = 0) {
        $phone_linkman = LinkmanService::getPhoneLinkman($patient);

        if ($phone_linkman->mobile != $mobile) {
            XPatientIndex::deleteXpatientIndexMobile($patient, $phone_linkman->mobile);

            $phone_linkman->mobile = $mobile;
            XPatientIndex::addXPatientIndexMobile($patient, $phone_linkman->mobile);
        }

        if ($userid) {
            $phone_linkman->userid = $userid;
        }
    }

    public static function getOneUseridByPatientidMobile($patientid, $mobile) {
        $sql = "select id from users where patientid = :patientid and mobile = :mobile ";
        $bind = [
            ':patientid' => $patientid,
            ':mobile' => $mobile
        ];
        $userid = Dao::queryValue($sql, $bind);

        return $userid ? $userid : 0;
    }

    public static function updateXprovinceidAndXcityid(Linkman $linkman) {
        $data = FetchMobileService::fetchByMobile($linkman->mobile);

        if(is_null($data)){
            return;
        }

        $provinceName = $data['data'][0]['prov'] ?? '';
        $cityName = $data['data'][0]['city'] ?? '';

        if (false == empty($provinceName)) {
            $sql = "SELECT id FROM xprovinces WHERE name LIKE '%{$provinceName}%'";
            $xprovinceid = Dao::queryValue($sql);
            if ($xprovinceid > 0 && $linkman->xprovinceid != $xprovinceid) {
                $linkman->xprovinceid = $xprovinceid;

            }
        }
        if (false == empty($cityName)) {
            $sql = "SELECT id FROM xcitys WHERE name LIKE '%{$cityName}%'";
            $xcityid = Dao::queryValue($sql);
            if ($xcityid > 0 && $linkman->xcityid != $xcityid) {
                $linkman->xcityid = $xcityid;
            }
        }
    }
}