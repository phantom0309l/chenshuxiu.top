<?php
class PatientAddressService
{
    public static function getPatientAddressByTypePatientid ($type, $patientid) {
        $patientaddress = PatientAddressDao::getByTypePatientid($type, $patientid);
        if ($patientaddress instanceof PatientAddress) {
            return $patientaddress;
        } else {
            $row = [];
            $row["type"] = $type;
            $row["patientid"] =  $patientid;
            $row["xprovinceid"] = 0;
            $row["xcityid"] = 0;
            $row["xcountyid"] = 0;
            $row["content"] = 0;
            $patientaddress = PatientAddress::createByBiz($row);

            return $patientaddress;
        }
    }

    public static function createPatientAddress (array $place, $type, $patientid) {
        $row = [];
        $row["type"] = $type;
        $row["patientid"] = $patientid;
        $row["xprovinceid"] = $place['xprovinceid'] ?? 0;
        $row["xcityid"] = $place['xcityid'] ?? 0;
        $row["xcountyid"] = $place['xcountyid'] ?? 0;
        $row["content"] = $place['content'] ?? '';
        $patientaddress = PatientAddress::createByBiz($row);

        return $patientaddress;
    }

    public static function updatePatientAddress (array $place, $type, $patientid, $needcontent = true) {
        $patientaddress = self::getPatientAddressByTypePatientid($type, $patientid);

        $patientaddressSnap = clone $patientaddress;

        $patientaddress->xprovinceid = $place['xprovinceid'];
        $patientaddress->xcityid = $place['xcityid'];
        $patientaddress->xcountyid = $place['xcountyid'];
        if ($needcontent) {
            $patientaddress->content = $place['content'] ?? '';
        }

        $logContent = self::getPatientAddressModifyLogContent($patientaddressSnap, $patientaddress);

        return $logContent;
    }

    public static function getPatientAddressModifyLogContent ($patientaddressSnap, $patientaddress) {
        $place_str = [
                'birth_place' => '出生地',
                'communicate_place' => '通讯地址',
                'hospital_place' => '医院地址',
                'long_live_place' => '长期居住地',
                'mobile_place' => '手机地址',
                'native_place' => '籍贯',
                'now_place' => '现居住地',
                'once_place' => '曾居住地',
                'schedule_place' => '门诊地址'
        ];

        $xprovinceSnapstr = $patientaddressSnap->xprovince->name;
        $xcitySnapstr = $patientaddressSnap->xcity->name;
        $xcountySnapstr = $patientaddressSnap->xcounty->name;

        $xprovincestr = $patientaddress->xprovince->name;
        $xcitystr = $patientaddress->xcity->name;
        $xcountystr = $patientaddress->xcounty->name;

        $addressSnapstr = $xprovinceSnapstr . "/" . $xcitySnapstr . "/" . $xcountySnapstr . "/" . $patientaddressSnap->content;
        $addressstr = $xprovincestr . "/" . $xcitystr . "/" . $xcountystr . "/" . $patientaddress->content;

        $logcontent = '';
        if ($addressSnapstr != $addressstr) {
            $logcontent = "<{$place_str["{$patientaddress->type}"]}>: 从[{$addressSnapstr}]修改为[{$addressstr}]";
        }

        return $logcontent;
    }

    public static function fixNull ($place) {
        $place['xprovinceid'] = $place['xprovinceid'] ?? 0;
        $place['xcityid'] = $place['xcityid'] ?? 0;
        $place['xcountyid'] = $place['xcountyid'] ?? 0;

        return $place;
    }

    public static function fixForAdmin ($place) {
        $place['xprovinceid'] = trim($place['provinceid']) ? trim($place['provinceid']) : 0;
        $place['xcityid'] = trim($place['cityid']) ? trim($place['cityid']) : 0;
        $place['xcountyid'] = trim($place['quid']) ? trim($place['quid']) : 0;
        $place['content'] = $place['content'] ?? '';

        $four = [110000, 120000, 310000, 500000];
        if (in_array($place['xprovinceid'], $four)) {
            $place['xcountyid'] = $place['xcityid'];
            $place['xcityid'] = $place['xprovinceid'] + 100;
        }

        return $place;
    }
}
