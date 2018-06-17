<div><?php
    if (!empty($ocrArrformdata)) {
        ?>
        <div class="audit-info">
            <div>
                <span>最后修改人:<?=$ocrArrformdata['lastChangeAuditName']?></span>
                <span>最后修改时间:<?=$ocrArrformdata['lastChangeTime']?></span>
            </div>
            <?php
            if ($ocrArrformdata['status'] == 0) {
                echo "<div><span> ocr识别失败，请手动录入</span></div>";
            }
            ?>

        </div>
        <?php
        if (!empty($ocrArrformdata['patientInfo'])) {
            $arrHeader = OcrService::getTableHeader(1);
            echo "<form name='patientInfo_form'>" . HtmlCtr::getTableCtrImp('patient_info', 'patient_info', $arrHeader, $ocrArrformdata['patientInfo']) . "</form>";
        }

        if (!empty($ocrArrformdata['items'])) {
            $arrHeader = OcrService::getTableHeader(2);
            echo "<form name='items_form'>" . HtmlCtr::getTableCtrImp('report_items', 'report_items', $arrHeader, $ocrArrformdata['items']) . "</form>";
            echo "<button class='btn btn-info' id='add-tr' data-type='report_items'><i class='fa fa-plus'></i> 新增行</button>";
        }

        if (!empty($ocrArrformdata['drugName'])) {
            $arrHeader = OcrService::getTableHeader(3);
            $bodyArr = array($ocrArrformdata['drugName'], $ocrArrformdata['drugFactory']);
            echo "<form name='drugName_form'>" . HtmlCtr::getTableCtrImp('drugItem', 'drugItem', $arrHeader, $bodyArr) . "</form>";
        }

        if (!empty($ocrArrformdata['drugList'])) {
            $arrHeader = OcrService::getTableHeader(4);
            echo "<form name='drugList_form'>" . HtmlCtr::getTableCtrImp('drugList', 'drugList', $arrHeader, $ocrArrformdata['drugList']) . "</form>";
            echo "<button class='btn btn-info' id='add-tr' data-type='drugList'><i class='fa fa-plus'></i> 新增行</button>";
        }
        if (1 == $isText) {
            echo $ocrArrformdata['text'];
            echo $ocrArrformdata['json'];
        } else {
            ?>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-checkuppicture"><i
                            class="fa fa-check"></i>提交
                </button>
            </div>
            <?php
        }
    } elseif (!empty($ocrArr)) {
        $arrHeader = OcrService::getTableHeader(0);
        echo HtmlCtr::getTableCtrImp('patient_info', 'patient_info', $arrHeader, $ocrArr);
    }
    ?></div>
<style>
    .patient_info tr, .report_items tr, .drugItem tr, .drugList tr {
        width: 100%;
    }

    .patient_info tr td, .report_items tr td, .drugItem tr td, .drugList tr td {
        text-align: center;
        height: 36px;
        line-height: 36px;
    }

    thead tr td {
        border: 1px solid #f0f0f0;
    }

    .patient_info tr td {
        width: 25%;
    }

    .report_items tr td {
        width: 20%;
    }

    .drugItem tr td, .drugList tr td {
        width: 50%
    }

    #table-box input {
        border: 1px solid #f0f0f0;
        width: 100%;
        text-align: center;
    }

    #table-box input:focus {
        border: 1px solid #5c90d2;
    }

    #table-box .btn-info {
        position: absolute;
        margin-top: 12px;
    }

    #table-box form {
        margin-bottom: 15px;
    }

    .audit-info {
        margin: 15px 0;
    }

    .audit-info span {
        margin-right: 15px;
    }
</style>
