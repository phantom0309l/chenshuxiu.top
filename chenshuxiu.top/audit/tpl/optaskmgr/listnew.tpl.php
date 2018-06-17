<?php
$pagetitle = '运营任务';
$cssFiles = [
    $img_uri . '/v5/page/audit/optaskmgr/list/list.css?v=201805211016',
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20170820',
    $img_uri . '/v5/plugin/speech/speech-input.css?v=2018022201',
    $img_uri . '/static/css/jquery-ui.autocomplete.min.css?v=20180208',
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v3/js/amr/amrnb.js',
    $img_uri . '/v3/js/amr/amritem.js',
    $img_uri . '/v5/plugin/speech/speech-input.js?v=2018022202',
    $img_uri . '/v5/page/audit/optaskmgr/list/pipe.js?v=2018050901',
    $img_uri . '/v5/common/wxvoicemsg_content_modify.js?v=20171208',
    $img_uri . '/v5/common/dealwithtpl.js?v=2018050401',
    $img_uri . "/v5/common/pipelevelfix.js?v=20171222",
    $img_uri . "/v5/common/setMedicineBreakDate.js?v=20171019",
    $img_uri . '/v5/page/audit/optaskmgr/list/listnew.js?v=2018060701',
    $img_uri . '/v5/page/audit/optaskmgr/list/changelevel.js?v=20171226',
    $img_uri . '/v5/page/audit/optaskmgr/list/pgroup.js?v=20171206',
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170820',
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
]; //填写完整地址
$sideBarMini = true;
$pageStyle = <<<STYLE

    .content-left label.control-label {
        padding-right: 0;
        font-weight: 500;
    }
    #main-container {
     background: #f5f5f5 !important;
    }
    .task-block {
        border: 1px solid #eee;
        border-radius: 2px;
    }
    .divOnSelected {
        border: 1px solid #43a3e5;
    }
    .block-title-user {
        margin-left: -10px;
        margin-right: 10px;
    }

    .block-content .optask:last-child .optask-t {
        border-bottom: 0;
    }
    .onepatient-tab li.active > a{
        color: #70b9eb !important;
    }
    .showRemarkBox {
        background: #fff;
        position:absolute;
        padding: 8px 10px;
        border: 1px solid blue;
        z-index: 100;
        width: 100px;
        border-radius: 3px;
        left: 0px;
        top: 20px;
    }

    @media screen and (min-width: 320px) {
        .xs-nav > li > a {
            padding-left: 8px;
            padding-right: 8px;
        }
    }

    @media screen and (min-width: 375px) {
        .xs-nav > li > a {
            padding-left: 14px;
            padding-right: 14px;
        }
    }

    .xs-nav.nav-pills > li > a {
        border-radius: 0;
    }

    .anchor_solid {
        height: 100px;
    }

    .patientMsg-cnt-Box{
        display:inline-block;
        background: #f5f5f5;
        margin: 2px;
        padding: 0px 5px;
        height: 28px;
        line-height: 28px;
        border-radius: 2px;
    }

    .week-line{
        color: #ccc;
    }
    
    .green_channel th {
        text-align: right;
    }

    .green_channel th, .green_channel td {
        padding: 5px 10px;
    }
    .msg-block a:visited,.msg-block a:active,.msg-block a:link{
        text-decoration:none; 
        color:black;
    }
    .optask-level_1 {
        color:#666;
    }
    .optask-level_2 {
        color:#666;
    }
    .optask-level_3 {
        color:#f3b760;
    }
    .optask-level_4 {
        color:#ff6a4e;
    }
    .optask-level_5 {
        color:#d26a5c;
    }
    .optask-level_9 {
        color:#d26a5c;
    }
    .patient-item-name {
        font-weight: 900;
    }
    #succCopy {
        position: fixed;
        z-index: 9;
        display: none;
        width: 300px;
        text-align: center;
    }
  

STYLE;
$pageScript = <<<SCRIPT
    $(function(){
        $(".level-remark").hover(function() {
            $(".showRemarkBox").show();
            $(this).css("color", "#f66");
        }, function() {
            $(".showRemarkBox").hide();
            $(this).css("color", "#333");
        });

        $('.xs-nav').find('a').on('click', function() {
            $('.xs-nav').find('li.active').removeClass('active');
            $(this).parent().addClass('active');

            if ($(this).attr('href') === '#') {
                document.getElementById('J_content_left').scrollTop=0;
            }
            window.scrollTo(0, 0);
        })
    });
    
SCRIPT;
?>
<?php include_once dirname(__FILE__) . '/../_header.new.tpl.php'; ?>
<div class="col-md-12 contentShell" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <section class="col-xs-12 visible-xs remove-padding" style="position:fixed; top: 47px; z-index: 10;">
        <ul class="xs-nav nav nav-pills push bg-white">
            <li class="active">
                <a href="#">过滤器</a>
            </li>
            <li class="">
                <a href="#anchor_patientlist">患者列表</a>
            </li>
            <li>
                <a href="#anchor_pipe">患者流</a>
            </li>
            <li>
                <a href="#anchor_patientone">患者信息</a>
            </li>
            <li>
                <a href="#anchor_optask">任务</a>
            </li>
        </ul>
    </section>
    <section class="col-md-3 content-left sectionItem" id="J_content_left">
        <div class="alert alert-info alert-dismissable" id="succCopy">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h3 class="font-w300 push-15">Success</h3>
            <p>复制成功</p>
        </div>
        <div id="anchor_filter" class="visible-xs anchor_solid" style="height: 0;"></div>
        <div class="form-horizontal filter-box" style="margin-bottom:10px;">
            <?php include_once dirname(__FILE__) . '/_filter/_filter.tpl.php' ?>
        </div>
        <div id="anchor_patientlist" class="visible-xs anchor_solid" style="height: 85px;"></div>
        <div>
            <h2 class="content-heading">共 <?= $patient_cnt ?> 个患者的 <?= $optask_cnt ?> 个任务</h2>
            <div>
                <?php
                foreach ($optasks as $ii => $a) {
                    $patient = $a->patient;
                    $tomorrow = date('Y-m-d', time() + 3600 * 24);
                    $patient_optasks = OpTaskDao::getListByPatient($a->patient, " and status = 0 and plantime<'{$tomorrow}' order by plantime asc, id asc ");
                    $max_level_optask = $patient->getOneMaxLevelOpTask();
                    ?>
                    <div class="block task-block msg-block">
                        <a href="#goPatientBase" data-patientname="<?= $patient->name ?>" data-patientid="<?= $patient->id ?>"
                           data-diseaseid="<?= $patient->diseaseid ?>" data-doctorid="<?= $patient->doctorid ?>"
                           data-optaskid="<?= $patient_first_optask->id ?>"
                           class="showOptask showPatientOneHtml patientid-<?= $patient->id ?>" id="showOptask">
                            <?php
                            $backgroundstr = '';
                            if ($max_level_optask instanceof OpTask) {
                                if ($max_level_optask->level > 3) {
                                    $color = $max_level_optask->getLevelColor();
                                    $backgroundstr = "background:{$color}";
                                }
                            }
                            ?>
                            <div class="block-header" style="<?= $backgroundstr ?>">
<!--                                VIP和重大患者图标显示-->
                                <ul class="block-options">
                                        <?php if ($patient->has_valid_quickpass_service()) {
                                            echo '<li><i data-toggle="popover" data-placement="bottom"
                                                          data-content="此用户为VIP" class="vip_quickpass block-title-user" style=""></i></li>';
                                        } ?>
                                    <?php if (DiseaseGroup::isCancer($patient->disease->diseasegroupid)){ ?>
                                    <li>
                                        <i class='todaymark block-title-user <?= $patient->isTodayMark() ? 'todaymark_primary' : '' ?>'
                                           style="<?= $patient->isTodayMark() ? '' : 'display: none;' ?>" data-toggle='popover' data-placement='bottom'
                                           data-content='<?= $patient->getTodayMarksStr() ?>'></i>
                                    </li>
                                    <?php } ?>

                                    <li>#<?= ($ii + 1) ?></li>
                                </ul>
<!--                                主体标题显示-->
                                <h3 class="block-title ">
                                    <?php
                                    if ($patient instanceof Patient) {
                                        $female = $patient->sex == 2 ? '-female' : '';
                                        $color = '';
                                        echo '<i class="si si-user' . $female . ' block-title-user ' . $color . '"></i> ';

                                        echo "<span class='patient-item-name' id='text'>".$patient->getMaskName().'</span>';
                                        $showtitle = "{$patient->patientgroup->title} {$patient->doctor->doctorgroup->title} <span class='doctor_name' style='color:#666;'> {$patient->doctor->name} </span>";
                                        ?>
                                        <span class="gray f12"><?= $showtitle ?></span>
                                        <span class="blue f12 lock_auditor_name_<?= $patient->id ?>"><?= $patient->auditor->name; ?></span>
                                        <?php if ($patient->is_alk == 1) { ?>
                                            <span class="red f12">[ALK]</span>
                                        <?php } ?>
                                        <?php
                                        $baseinfo_collection_optask = OpTaskDao::getOneByPatientUnicode($patient, 'BaseInfo:collection');
                                        if ($baseinfo_collection_optask instanceof OpTask && (false == $baseinfo_collection_optask->obj instanceof Paper)) {
                                            ?>
                                            <span class="red f12">[入组信息]未完成</span>
                                            <?php
                                        } else
                                            ?>
                                        <?php if ($patient->is_live == 0) {
                                            ?>
                                            <span class="red f12">已死亡</span>
                                            <?php
                                        } ?>
                                        <?php
                                        if ($patient->wxuser_cnt < 1 && $patient->subscribe_cnt < 1) {
                                            ?> <span class="red f12">未关注</span> <?php
                                        } elseif ($patient->wxuser_cnt > 0 && $patient->subscribe_cnt < 1) {
                                            ?> <span class="red f12">已取关</span> <?php
                                        } elseif ($patient->wxuser_cnt != $patient->subscribe_cnt) {
                                            ?> <span class="red f12"><?= $patient->subscribe_cnt ?>/<?= $patient->wxuser_cnt ?></span> <?php
                                        }
                                        ?>
                                        <?php
                                    } else {
                                        echo "患者不存在";
                                    }
                                    ?>
                                </h3>
                            </div>
<!--                            主体内容区域-->
                            <div class="block-content">
                                <ul>
                                    <?php foreach ($patient_optasks as $patient_optask) { ?>
                                        <li>
                                            <div style="border-top: 1px solid #ddd; padding:10px 0px;">
                                                <?= $patient_optask->getFixPlantime() ?>
                                                <?= $patient_optask->optasktpl->title ?>
                                                <?php if ($patient_optask->opnode instanceof OpNode) { ?>
                                                    <span class="text-warning"
                                                          style="color: green">[<?= $patient_optask->opnode->title ?>]</span>
                                                <?php } ?>


                                                <?php if (false && $patient_optask->getOwnerNames()) { ?>
                                                    <span>[<?= $patient_optask->getOwnerNames() ?>]</span>
                                                <?php } ?>
<!--                                                任务等级-->
                                                <?php if ($patient_optask->level > 2) { ?>
                                                    <span class="red" data-toggle="popover" data-placement="bottom"
                                                          data-content="<?= $patient_optask->level_remark; ?>">
                                                            <span class="optask-leve_<?= $patient_optask->level ?>">[<?= $patient_optask->getLevelStr() ?>]</span>
                                                    </span>
                                                <?php } ?>
                                            </div>

                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </a>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="tc bgFFF mb10 p10">
                <?php include $dtpl . "/pagelink.ctr.php"; ?>
            </div>
        </div>
    </section>
    <section class="col-md-5 content-right sectionItem">
        <div id="anchor_pipe" class="visible-xs anchor_solid"></div>
        <?php include_once $tpl . "/_pipelayout_optask.php"; ?>
    </section>
    <section class="col-md-4 sectionItem" id="oneHtml">
        <div id="anchor_patientone" class="visible-xs anchor_solid"></div>
        <div class="onePatientHtml"></div>
        <div id="anchor_optask" class="visible-xs anchor_solid"></div>
        <div class="optaskshell"></div>
    </section>
    <div style="position: fixed;top:50px;right:10px;padding:5px;" class="hidden-xs">
        <a href="javascript:" data-open="0" class="a-optask-full-screen" title="放大工作区"><i class="si si-size-fullscreen"></i></a>
    </div>
</div>

<div class="clear"></div>
<?php include_once($tpl . "/_thankbox.php"); ?>
<?php include_once($tpl . "/_pipelevelfixbox.php"); ?>
<?php include_once($tpl . "/optaskmgr/_pipe_bind_optask.php"); ?>
<?php
$footerScript = <<<SCRIPT
$(function(){
    App.initHelper('select2');
    $('.a-optask-full-screen').on('click', function() {
        if ($(this).data('open') == 0) {
            $('section:eq(0)').attr('class', 'col-md-1 content-left sectionItem').css('transition', 'width .2s');
            $('section:eq(1)').attr('class', 'col-md-1 content-right sectionItem').css('transition', 'width .2s');
            $('section:eq(2)').attr('class', 'col-md-10 sectionItem').css('transition', 'width .2s');
            $(this).data('open', 1);
            $(this).attr('title', '恢复工作区');
        } else {
            $('section:eq(0)').attr('class', 'col-md-3 content-left sectionItem').css('transition', 'width .2s');
            $('section:eq(1)').attr('class', 'col-md-5 content-right sectionItem').css('transition', 'width .2s');
            $('section:eq(2)').attr('class', 'col-md-4 sectionItem').css('transition', 'width .2s');
            $(this).data('open', 0);
            $(this).attr('title', '放大工作区');
        }
    });
    function getPatientMsgCnt() {
        $.ajax({
            url: '/optaskmgr/getpatientmsgfirstandcntjson',
            type: 'get',
            dataType: 'json'
        })
        .done(function(result) {
            // console.log("done");
            if (result.errno == -1) {
                console.log(result.errmsg);
            } else {
                // console.log(result);
                var data = result.data;
                var html_str = "";
                $.each(data, function(key,term) {
                    html_str += '<div class="patientMsg-cnt-Box">';
                    html_str += '<span class="fa fa-comment" style="color: ' + term.levelcolor + '" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="' + term.levelstr + '"></span><span class="week-line">|</span>';
                    html_str += '<span><span style="color: #333;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="' + term.date + '">' + term.time + '</span><span class="week-line">|</span></span>';
                    if(term.needdealtime){
                        html_str += '<span><span style="color: #d26a5c;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="最晚处理时间">' + term.needdealtime + '</span><span class="week-line">|</span></span>';
                    }
                    html_str += '<span>共 ' + term.cnt + ' 条</span>';
                    html_str += '</div>';
                });
                $(".optask-patientmsg-cnt").html(html_str);
                // Initialize tooltip
                $('.patientMsg-cnt-Box [data-toggle="tooltip"]').tooltip();
            }
        })
        .fail(function() {
            // console.log("fail");
        })
    };
    // 进入页面第一次拉取一次消息任务数量
    getPatientMsgCnt();
    // 每隔30s轮询一次消息任务数量
    setInterval(function(){
        getPatientMsgCnt();
    }, 30000);
});

$(function () {

    function showPopover(screenX, screenY) {
        $("#succCopy").attr('style','top:'+screenY+'px;left:'+screenX+'px;display:block;');
      //2秒后消失提示框
      var id = setTimeout(
        function () {
            $("#succCopy").hide();
        }, 1500
      );
    }
    //点击文本框复制其内容到剪贴板上方法
    $(document).on('click', '.patient-item-name, .doctor_name', function (event) {
        event.stopPropagation(); 
        var txt = $(this).text();
        var input = document.createElement('input');
        
        document.body.appendChild(input);
        input.setAttribute('value', txt);
        input.select();
        if (document.execCommand('copy')) {
            document.execCommand('copy');
            var screenX =event.screenX-1350;
            var screenY = event.screenY-200;
            showPopover(screenX, screenY);
        }
        
        document.body.removeChild(input);   
    });
   
})
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
