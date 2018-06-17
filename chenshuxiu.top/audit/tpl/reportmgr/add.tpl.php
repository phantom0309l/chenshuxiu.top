<?php
$pagetitle = '患者汇报';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
textarea {
    border: 0;
    resize:none;
}

.table-header-bg > thead > tr > th, .table-header-bg > thead > tr > td {
    background-color: #f9f9f9;
    color: #333;
}

.table > tbody > tr:first-child > td, .table > tbody > tr:first-child > th {
    border-top: 0;
}

.table {
    white-space: nowrap;
}

.block-content {
    overflow-x: auto;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <div style="display: inline-block">
                模板：
                <select autocomplete="off" name="reportTplid" id="reportTplid">
                    <?php
                    foreach ($reportTpls as $a) {
                        ?>
                        <option value="<?= $a->id ?>" <?= $a->id == $reportTpl->id ? 'selected' : '' ?> >
                            <?= $a->title ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div style="margin-left: 15px;display: inline-block;">
                医生：
                <select autocomplete="off" name="doctorid" id="doctorid" class="">
                    <?php
                    foreach ($pcards as $a) {
                        ?>
                        <option value="<?= $a->doctor->id ?>" <?= $doctor->id == $a->doctor->id ? 'selected' : '' ?> >
                            <?= $a->doctor->id ?> <?= $a->doctor->name ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <span style="margin: 0 15px;">汇报日期：<?= date('Y-m-d') ?></span>
            <a class="btn btn-primary"
               href="/reportmgr/listbypatient?patientid=<?= $patient->id ?>&doctorid=<?= $doctor->id ?>">汇报历史</a>
        </div>
        <div class="col-md-12" style="padding: 10px 0px;">
            <form id="reportForm">
                <input type="hidden" name="patientid" value="<?= $patient->id ?>"/>
                <input type="hidden" name="doctorid" value="<?= $doctor->id ?>"/>
                <input type="hidden" name="diseaseid" value="<?= $pcard->diseaseid ?>"/>
                <input type="hidden" name="revisitrecordid" value="<?= $revisitrecord->id ?>"/>
                <input type="hidden" name="reporttplid" value="<?= $reportTpl->id ?>"/>

                <?php
                $config = json_decode($reportTpl->content, true);
                foreach ($config as $item) {
                    switch ($item) {
                        case 'baseInfo':    // 基本信息
                            ?>
                            <div class="block block-bordered">
                                <div class="block-header bg-gray-lighter">
                                    <ul class="block-options">
                                        <li>
                                            <button type="button" data-toggle="block-option"
                                                    data-action="content_toggle"><i class="si si-arrow-up"></i>
                                            </button>
                                        </li>
                                    </ul>
                                    <h3 class="block-title">基本信息</h3>
                                </div>
                                <div class="block-content">
                                    <div class="table-responsive">
                                        <table class="table">
                                        <thead>
                                        <tr class="text-info">
                                            <th style="width: 100px;"><?= $patient->name ?></th>
                                            <th class="text-left"><?= $patient->getAttrStr() ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if ($pcard instanceof Pcard && $pcard->complication != '') { ?>
                                            <tr>
                                                <td style="width: 100px;">诊断</td>
                                                <td><?= $pcard->complication ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($revisitrecord instanceof RevisitRecord) { ?>
                                            <tr>
                                                <td style="width: 100px;">上次就诊</td>
                                                <td><?= $revisitrecord->thedate ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <?php break;
                        case 'appeal': ?>
                            <div class="block block-bordered">
                                <div class="block-header bg-gray-lighter">
                                    <ul class="block-options">
                                        <li>
                                            <button type="button" data-toggle="block-option"
                                                    data-action="content_toggle"><i class="si si-arrow-up"></i>
                                            </button>
                                        </li>
                                    </ul>
                                    <h3 class="block-title">患者诉求</h3>
                                </div>
                                <div class="block-content">
                                    <textarea id="appeal" name="appeal"
                                              style="border-bottom: 1px solid #e9e9e9;"
                                              class="col-md-12 col-xs-12 mb20" rows="4"
                                              placeholder="请输入患者诉求..."></textarea>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <?php break;
                        case 'remark': ?>
                            <div class="block block-bordered">
                                <div class="block-header bg-gray-lighter">
                                    <ul class="block-options">
                                        <li>
                                            <button type="button" data-toggle="block-option"
                                                    data-action="content_toggle"><i class="si si-arrow-up"></i>
                                            </button>
                                        </li>
                                    </ul>
                                    <h3 class="block-title">运营备注</h3>
                                </div>
                                <div class="block-content">
                                    <textarea id="remark" name="remark"
                                              style="border-bottom: 1px solid #e9e9e9;"
                                              class="col-md-12 col-xs-12" rows="4"
                                              placeholder="请输入运营备注..."></textarea>
                                    <div class="clear"></div>
                                    <div>
                                        <div class="clear"></div>
                                        <div>
                                            <?php
                                            $picWidth = 140;
                                            $picHeight = 140;
                                            $maxImgLen = 0;
                                            $pictureInputName = "pictureids";
                                            $isCut = false;
                                            $objtype = "Auditor";
                                            $objid = $myauditor->id;
                                            require_once("$dtpl/mult_picture.ctr.php");
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php break;
                        case 'patientRemark':   // 症状体征及不良反应
                            if (!empty($patientRemarks)) { ?>
                                <div class="block block-bordered">
                                    <div class="block-header bg-gray-lighter">
                                        <ul class="block-options">
                                            <li>
                                                <button type="button" data-toggle="block-option"
                                                        data-action="content_toggle"><i class="si si-arrow-up"></i>
                                                </button>
                                            </li>
                                        </ul>
                                        <h3 class="block-title">症状体征及不良反应</h3>
                                    </div>
                                    <div class="block-content">
                                        <div style="overflow: auto">
                                            <div class="table-responsive">
                                                <table class="table">
                                                <tbody>
                                                <?php
                                                foreach ($patientRemarks as $patientRemark) {
                                                    if ($patientRemark->content == '') {
                                                        continue;
                                                    } ?>
                                                    <tr>
                                                        <td class="fb"><?= $patientRemark->name ?></td>
                                                        <td><?= $patientRemark->thedate ?></td>
                                                        <td><?= $patientRemark->content ?></td>
                                                    </tr>
                                                    <?php
                                                } ?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            <?php }
                            break;
                        case 'checkuptpls': // 检查
                            if (!empty($checkup_arr)) { ?>
                                <div class="block block-bordered">
                                    <div class="block-header bg-gray-lighter">
                                        <ul class="block-options">
                                            <li>
                                                <button type="button" data-toggle="block-option"
                                                        data-action="content_toggle"><i class="si si-arrow-up"></i>
                                                </button>
                                            </li>
                                        </ul>
                                        <h3 class="block-title">检查</h3>
                                    </div>
                                    <div class="block-content">
                                        <div class="text-center">
                                            <?php foreach ($checkup_arr as $item) {
                                                $checkuptpl = $item['checkuptpl'];
                                                $checkups = $item['checkups'];
                                                ?>
                                                <div class="text-primary mt20 mb20 f16 fb">
                                                    <?= $checkuptpl->title ?>
                                                </div>
                                                <div style="overflow: auto;">
                                                    <div class="table-responsive">
                                                        <table class="table table-header-bg table-bordered checkups">
                                                        <thead>
                                                        <tr role="row">
                                                            <th class="text-center">日期</th>
                                                            <?php
                                                            $questions = $checkuptpl->xquestionsheet->getQuestions();
                                                            foreach ($questions as $i => $q) {
                                                                echo "<th class=\"text-center\">";
                                                                if ($q->isMultText()) {
                                                                    foreach ($q->getMultTitles() as $t) {
                                                                        echo "$q->content}-{$t}";
                                                                    }
                                                                } else {
                                                                    echo $q->content;
                                                                }
                                                                echo "</th>";
                                                            }
                                                            ?>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach ($checkups as $checkup) {
                                                            ?>
                                                            <tr>
                                                                <td><?= $checkup->check_date ?></td>
                                                                <?php
                                                                foreach ($questions as $i => $q) {
                                                                    $xanswer = $checkup->xanswersheet->getAnswer($q->id);
                                                                    // 有答案
                                                                    if ($xanswer instanceof XAnswer) {
                                                                        foreach ($xanswer->getQuestionCtr()->getAnswerContents() as $t) {
                                                                            echo "<td>{$t}</td>";
                                                                        }
                                                                    } else {
                                                                        if ($q->isMultText()) {
                                                                            foreach ($q->getMultTitles() as $t) {
                                                                                echo "<td></td>";
                                                                            }
                                                                        } else {
                                                                            echo "<td></td>";
                                                                        }
                                                                    }
                                                                } ?>

                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                            break;
                        case 'patientmedicinepkg':    // 用药
                            if (!empty($pmtargets)) {
                                ?>
                                <div class="block block-bordered">
                                    <div class="block-header bg-gray-lighter">
                                        <ul class="block-options">
                                            <li>
                                                <button type="button" data-toggle="block-option"
                                                        data-action="content_toggle"><i class="si si-arrow-up"></i>
                                                </button>
                                            </li>
                                        </ul>
                                        <h3 class="block-title">患者现用药情况</h3>
                                    </div>
                                    <div class="block-content">
                                        <div class="table-responsive">
                                            <table class="table">
                                            <thead>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($pmtargets as $pmtarget) {
                                                ?>
                                                <tr>
                                                    <td class="fb"><?= $pmtarget->medicine->name ?></td>
                                                    <td>
                                                        <div>
                                                            <?= $pmtarget->drug_dose ?>
                                                            <?= $pmtarget->drug_frequency ?>
                                                            <?= $pmtarget->drug_change ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                            break;
                        case 'diagnose': 
                            ?>
                                <div class="block block-bordered">
                                    <div class="block-header bg-gray-lighter">
                                        <ul class="block-options">
                                            <li>
                                                <button type="button" data-toggle="block-option"
                                                        data-action="content_toggle"><i class="si si-arrow-up"></i>
                                                </button>
                                            </li>
                                        </ul>
                                        <h3 class="block-title">诊断和分期</h3>
                                    </div>
                                    <div class="block-content">
                                    <p>最新诊断</p>
                                        <?php if ($diagnosePatientRecord) { ?>
                                        <div class="table-responsive">
                                            <table class="table">
                                            <thead>
                                            <tr>
                                                <th>日期</th>
                                                <th>部位</th>
                                                <th>起源</th>
                                                <th>特殊</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $content = $diagnosePatientRecord->loadJsonContent();?>
                                                <tr>
                                                    <td><?=$content['thedate']?></td>
                                                    <td><?=$content['position']?></td>
                                                    <td><?=$content['diagnose_start']?></td>
                                                    <td><?=$content['special']?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                        <?php } ?>
                                    <p>最新分期</p>
                                        <?php if ($stagingPatientRecord) { ?>
                                        <div class="table-responsive">
                                            <table class="table">
                                            <thead>
                                            <tr>
                                                <th>日期</th>
                                                <th>分期类型</th>
                                                <th>T</th>
                                                <th>N</th>
                                                <th>M</th>
                                                <th>分期</th>
                                                <th>备注</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $content = $stagingPatientRecord->loadJsonContent();?>
                                                <tr>
                                                    <td><?=$content['thedate']?></td>
                                                    <td><?=$content['type']?></td>
                                                    <td><?=$content['T']?></td>
                                                    <td><?=$content['N']?></td>
                                                    <td><?=$content['M']?></td>
                                                    <td><?=$content['stage']?></td>
                                                    <td><?=$stagingPatientRecord->content?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php 
                            break;
                            case "chemo":
                            ?>
                            <?php if ($chemoPatientRecord) { ?>
                                <div class="block block-bordered">
                                    <div class="block-header bg-gray-lighter">
                                        <ul class="block-options">
                                            <li>
                                                <button type="button" data-toggle="block-option"
                                                        data-action="content_toggle"><i class="si si-arrow-up"></i>
                                                </button>
                                            </li>
                                        </ul>
                                        <h3 class="block-title">最新化疗方案</h3>
                                    </div>
                                    <div class="block-content">
                                        <div class="table-responsive">
                                            <table class="table">
                                            <thead>
                                            <tr>
                                                <th>开始日期</th>
                                                <th>方案名称</th>
                                                <th>化疗周期</th>
                                                <th>性质</th>
                                                <th>疗程</th>
                                                <th>备注</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $content = $chemoPatientRecord->loadJsonContent();?>
                                                <tr>
                                                    <td><?=$chemoPatientRecord->thedate?></td>
                                                    <td><?=$content['protocol']?></td>
                                                    <td><?=$content['cycle']?></td>
                                                    <td><?=$content['property']?></td>
                                                    <td><?=$content['period']?></td>
                                                    <td><?=$chemoPatientRecord->content?></td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php 
                            break;
                            case "nexthualiaodate":
                            ?>
                            <?php if ($nextHualiaoDate) { ?>
                                <div class="block block-bordered">
                                    <div class="block-header bg-gray-lighter">
                                        <ul class="block-options">
                                            <li>
                                                <button type="button" data-toggle="block-option"
                                                        data-action="content_toggle"><i class="si si-arrow-up"></i>
                                                </button>
                                            </li>
                                        </ul>
                                        <h3 class="block-title">预计下次化疗日期</h3>
                                    </div>
                                    <div class="block-content">
                                        <div class="table-responsive">
                                            <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td><?=$nextHualiaoDate?></td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php
                            break;
                            case "wbc_checkup":
                            ?>
                                <?php if ($wbcCheckupPatientRecord) { ?>
                                <div class="block block-bordered">
                                    <div class="block-header bg-gray-lighter">
                                        <ul class="block-options">
                                            <li>
                                                <button type="button" data-toggle="block-option"
                                                        data-action="content_toggle"><i class="si si-arrow-up"></i>
                                                </button>
                                            </li>
                                        </ul>
                                        <h3 class="block-title">最近血常规</h3>
                                    </div>
                                    <div class="block-content">
                                        <div class="table-responsive">
                                            <table class="table">
                                            <thead>
                                            <tr>
                                                <th>日期</th>
                                                <th>白细胞</th>
                                                <th>血红蛋白</th>
                                                <th>血小板</th>
                                                <th>中性粒细胞</th>
                                                <th>备注</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $content = $wbcCheckupPatientRecord->loadJsonContent();?>
                                                <tr>
                                                    <td><?=$wbcCheckupPatientRecord->thedate?></td>
                                                    <td><?=$content['baixibao']?></td>
                                                    <td><?=$content['xuehongdanbai']?></td>
                                                    <td><?=$content['xuexiaoban']?></td>
                                                    <td><?=$content['zhongxingli']?></td>
                                                    <td><?=$wbcCheckupPatientRecord->content?></td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php break;?>
                <?php 
                    }
                }
                ?>
                <div class="text-right mb10">
                    <button id="send" class="btn btn-primary push-5-r push-10" type="button">
                        <i class="fa fa-send"></i>
                        <t>发送</t>
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
<div class="modal fade" id="modal-patientPictures" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">患者图片</h3>
                </div>
                <div class="block-content" style="max-height: 500px; overflow: auto;">

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary J_modal_submit" type="button">
                    <i class="fa fa-check"></i> 确定
                </button>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    var patientid = {$patient->id};
    var doctorid = {$doctor->id};
    var reportTplid = {$reportTpl->id};
    var patientPictures_loaded = false;

    $(function() {
        $('#addLeftImg').after(`<li class="li_last" id="select_patientPictures">
                                          <div class="last_img uploadifyImg">
                                              <div class="uploadify" style="height: 44px; width: 66px;">
                                                  <button type="button" data-backdrop="static" data-toggle="modal" data-target="#modal-patientPictures" class="J_patientPictures_button" style="margin-left: 5px; border: 0; background-image: url('{$img_uri}/m/img/add.jpg'); text-indent: -9999px; height: 44px; line-height: 44px; width: 66px;"></button>
                                              </div>
                                              <div class="addImg" style="padding-top: 17px;">选择患者图片</div>
                                          </div>
                                  </li>`);

        $(document).on('click', '.J_patientPictures_button', function(e) {
            if (patientPictures_loaded == true) {
                return false;
            }
            var data = {"patientid": patientid, "doctorid": doctorid};
            $.ajax({
                "type": "get",
                "url": "/reportmgr/ajaxpatientpictures",
                dataType: "html",
                data: data,
                "success": function (d) {
                    try {
                        var response = eval('('+d+')');
                        if (response.errno) {
                            $('#modal-patientPictures .block-content').html('<div class="text-center p20">'+
                                                                                '<span class="text-danger">' + response.errmsg + '</span>'+
                                                                            '</div>');
                        } else {
                            patientPictures_loaded = true;
                            $('#modal-patientPictures .block-content').html(d);
                        }
                    } catch(e) {
                        patientPictures_loaded = true;
                        $('#modal-patientPictures .block-content').html(d);
                    }
                },
                "error": function(d) {
                    $('#modal-patientPictures .block-content').html('<div class="text-center p20">'+
                                                                        '<span class="text-danger mr5">加载失败 </span>'+
                                                                        '<button id="modal-refresh" class="btn btn-sm btn-danger" type="button"><i class="fa fa-refresh"></i> 重试</button>'+
                                                                    '</div>');
                }
            });
        })

        $(document).on('click', '.J_modal_submit', function(e) {
            $('.patientPictures-Box .patientPicture-item').each(function(index, value, array) {
                var pictureid = $(value).data('pictureid');
                var patientpictureid = $(value).data('patientpictureid');

                var selected = $(value).data('selected');
                if (selected == true) {
                    if ($('#del_' + patientpictureid).length == 0) {
                        var thumburl = $(value).data('thumburl');
                        var li = createPictureLi(patientpictureid, pictureid, thumburl);
                        $('#addLeftImg').before(li);
                    }    
                } else {
                    $('#del_' + patientpictureid).remove();
                }
            });
            $('#modal-patientPictures').modal('hide');
        });

        $(document).on('click', '.patientPicture-item .img-container', function(e) {
            var parent = $(this).parent();
            var selected = parent.data('selected');
            if (selected == true) {
                parent.data('selected', false);
                parent.find('i').eq(0).hide();
            } else {
                parent.data('selected', true);
                parent.find('i').eq(0).show();
            }
        })

        function createPictureLi(patientpictureid, pictureid, thumburl) {
            return '<li id="del_' + patientpictureid + '">'+
                        '<input type="hidden" name="pictureids[]" value="' + pictureid + '">'+
                        '<p class="setting_thumbimg" style="margin-bottom: 0;">'+
                            '<img path="' + pictureid + '" src="' + thumburl + '">'+ 
                        '</p> '+
                        '<p class="setting_title" style="margin-bottom: 0;">'+
                            '<span>患者图片</span>'+ 
                            '<input type="text" name="multiImageTitle[]">'+
                        '</p>'+
                        '<a class="J_upload_delPic" data-patientpictureid="' + patientpictureid + '">'+
                            '<img src="http://img.fangcunhulian.cn/m/img/close.jpg" width="18" height="18"></a>'+
                   '</li>';
        }

        $(document).on('click', '.J_upload_delPic', function (e) {
            var patientpictureid = $(this).data('patientpictureid');
            $('#patientPicture_' + patientpictureid).data('selected', false);
            $('#patientPicture_' + patientpictureid).find('i').eq(0).hide();
            $('#del_' + patientpictureid).remove();
        })

        $('#doctorid').on('change', function () {
            window.location.href = '/reportmgr/add?patientid=' + patientid + '&doctorid=' + $(this).val() + '&reporttplid=' + reportTplid;
        })

        $('#reportTplid').on('change', function () {
            window.location.href = '/reportmgr/add?patientid=' + patientid + '&doctorid=' + doctorid + '&reporttplid=' + $(this).val();
        })

        $("#send").on('click', function () {
            if (!confirm('确定发送吗？')) {
                return;
            }
            $(this).prop('disabled', true);

            var i = $(this).find('i').eq(0);
            var t = $(this).find('t').eq(0);
            i.removeClass('fa-send');
            i.addClass('fa-refresh');
            i.addClass('fa-spin');
            t.text('正在发送...');

            var self = this;
            var data = $('#reportForm').serialize();
            $.ajax({
                "type": "post",
                "url": "/reportmgr/sendpost",
                dataType: "text",
                data: data,
                "success": function (d) {
                    $(self).prop('disabled', false);
                    i.removeClass('fa-refresh');
                    i.removeClass('fa-spin');
                    i.addClass('fa-send');
                    t.text('发送');
                    if (d === 'ok') {
                        alert('发送成功');
                        window.location.href = "/usermgr/default";
                    } else {
                        alert(d);
                    }
                }
            });
        })
    })
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
