<?php

class OcrTextMgrAction extends AuditBaseAction
{

    public function doList() {
        $pagesize = XRequest::getValue('pagesize', 10);
        $pagenum = XRequest::getValue('pagenum', 1);
        $type = XRequest::getValue('type', 'all');

        $url = "/ocrtextmgr/list?";

        $sql = '';
        $bind = [];

        if ($type != 'all') {
            $sql .= " and type = :type ";
            $bind[':type'] = $type;
            $url .= "type=" . $type;
        }

        $sql .= 'order by createtime desc';
        $patientPictures = CheckupPictureDao::getEntityListByCond4Page('PatientPicture', $pagesize, $pagenum, $sql, $bind);
        XContext::setValue('patientPictures', $patientPictures);

        $cnt = Dao::queryValue("select count(*) from patientpictures where 1=1 " . $sql, $bind);
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('type', $type);

        return self::SUCCESS;
    }

    // 通过数据表获取数据
    public function doTableDataForOneHtml () {
        $patientPicId = XRequest::getValue('picId', 0);

        $patientPic = PatientPicture::getById($patientPicId);
        DBC::requireTrue($patientPic instanceof PatientPicture, '图片不存在');

        $contentJson = $patientPic->ocrjson;
        $contentArr = json_decode($contentJson, true);

        XContext::setValue("ocrArrformdata", $contentArr);
        XContext::setSuccessTemplate('ocrtextmgr/onehtml.tpl.php');
        return self::SUCCESS;
    }




    // 获取ocr接口调用结果
    public function doOcrDataForOneHtml() {
        $url = XRequest::getValue('url');
        $type = XRequest::getValue('type', 1);
        $patientPicId = XRequest::getValue('picId', 0);
        $isText = XRequest::getValue('isTest', 0);

        $patientPic = PatientPicture::getById($patientPicId);
        DBC::requireTrue($patientPic instanceof PatientPicture, '图片不存在');

        $picture = $patientPic->obj->picture;
        if ($picture->getSizeMB() > 2) {
            $arr = $picture->toJsonArrayThumb('1500', '1500', false);
            $url = $arr['thumb_url'];
            $url = str_replace('fangcunhulian.cn', 'fangcunyisheng.com', $url);
        }

        $ocrJson = $this->curlToOcr($url, $type);
        $patientPic->ocrjson_back = $ocrJson;
        $ocrArr = json_decode($ocrJson, true);
        $ocrArr['status'] = 1;

        if (!empty($ocrJson) && $ocrArr['errorcode'] == 0) {
            // 如果ocr 正常返回且返回的错误码为0
            $ocrArrformdata = OcrService::formdateJson($ocrArr,$type);
            $ocrArrformdata['type'] = $type;
            $ocrArrformdata['lastChangeTime'] = date('Y-m-d H:i:s');
            $ocrArrformdata['lastChangeAuditId'] = $this->myauditor->id;
            $ocrArrformdata['lastChangeAuditName'] = $this->myauditor->name;
            $ocrArr = $this->htmlentitiesArr($ocrArrformdata);
        }else {
            XContext::setValue("ocrArr", $ocrArr);
        }

        $ocrJsonText = json_encode($ocrArr, JSON_UNESCAPED_UNICODE);

        XContext::setValue("isText", $isText);
        if (1 == $isText) {
            $ocrArr['errorcode'] = 0;
            $ocrArr['type'] = $type;
            $ocrArr['text'] = nl2br($ocrArr['text']);
            $ocrArr['json'] = "<pre>" . print_r($ocrArr, true) . "</pre>";
            XContext::setValue("ocrArrformdata", $ocrArr);
        } else {
            $patientPic->ocrjson = $ocrJsonText;
            XContext::setValue("ocrArrformdata", $ocrArrformdata);
        }

        XContext::setSuccessTemplate('ocrtextmgr/onehtml.tpl.php');
        return self::SUCCESS;
    }



    private function curlToOcr($url, $type) {
        $arr = array(
            'url' => $url,
            'type' => $type
        );
        // 若是加密方式，一下两个方法调用第二个参数为 true
        $body = OcrService::getDefaultPostFields($arr, false);
        $headers = OcrService::getDefautlHttpHeader($body, false);
        $ocrJson = OcrService::sendPostRequest($headers, $body);

        return $ocrJson;
    }

    private function htmlentitiesArr(Array $arr) {
        foreach ($arr as $key => $item) {
            if (is_array($item)) {
                $resultArr = $this->htmlentitiesArr($item);
                $ocrArr[$key] = $resultArr;
            } else {
                $ocrArr[$key] = htmlentities(trim($item));
            }
        }
        return $arr;
    }
}