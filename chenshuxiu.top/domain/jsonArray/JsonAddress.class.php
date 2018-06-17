<?php

class JsonAddress
{
    public static function getArrayXprovince () {
        $xprovinces = XprovinceDao::getAll();

        $list = JsonAddress::addressEntityToArray($xprovinces);

        return $list;
    }

    public static function getArrayXcity ($xprovinceid) {
        $xcitys = XcityDao::getListByXprovinceid($xprovinceid);

        $list = JsonAddress::addressEntityToArray($xcitys);

        return $list;
    }

    public static function getArrayXcityForAdmin ($xprovinceid) {
        $four = [110000, 120000, 310000, 500000];
        if (in_array($xprovinceid, $four)) {
            $entitys = XcountyDao::getListByXcityid($xprovinceid + 100);
        } else {
            $entitys = XcityDao::getListByXprovinceid($xprovinceid);
        }

        $list = JsonAddress::addressEntityToArray($entitys);

        return $list;
    }

    public static function getArrayXcounty ($xcityid) {
        $xcountys = XcountyDao::getListByXcityid($xcityid);

        $list = JsonAddress::addressEntityToArray($xcountys);

        return $list;
    }

    public static function addressEntityToArray($entitys) {
        $list = [];
        $list[] = [
            'id' => 0,
            'name' => '请选择'
        ];

        foreach ($entitys as $entity) {
            $list[] = [
                'id' => $entity->id,
                'name' => $entity->name,
            ];
        }

        return $list;
    }

    public static function jsonArrayForAdmin (PatientAddress $patientaddress) {
        $four = [110000, 120000, 310000, 500000];
        if (in_array($patientaddress->xprovinceid, $four)) {
            $arr =  [
                'id' => $patientaddress->id,
                'provinceid' => $patientaddress->xprovinceid,
                'provincestr' => $patientaddress->xprovince->name ?? '',
                'cityid' => $patientaddress->xcountyid,
                'citystr' => $patientaddress->xcounty->name ?? '',
                'content' => $patientaddress->content
            ];
        } else {
            $arr =  [
                'id' => $patientaddress->id,
                'provinceid' => $patientaddress->xprovinceid,
                'provincestr' => $patientaddress->xprovince->name ?? '',
                'cityid' => $patientaddress->xcityid,
                'citystr' => $patientaddress->xcity->name ?? '',
                'content' => $patientaddress->content
            ];
        }

        $arr['citys'] = [];
        if ($patientaddress->xprovince instanceof Xprovince) {
            $arr['citys'] = JsonAddress::getArrayXcityForAdmin($patientaddress->xprovinceid);
        }
        
        $arr['provinces'] = JsonAddress::getArrayXprovince();

        return $arr;
    }

    public static function jsonArrayCitysForAdmin ($citys) {
        $list = [];
        foreach ($citys as $a) {
            $tmp = [];

            $tmp['id'] = $a->code;
            $tmp['name'] = $a->name;

            $list[] = $tmp;
        }

        return $list;
    }
}
